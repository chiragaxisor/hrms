<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\SalaryStructure;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PayrollController extends Controller
{
    /**
     * Display payroll list, filters, and summary metrics for selected month/year.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $isEmployee = ! $user->isAdmin();
        $myEmployee = $user->employee;

        $month = (int) $request->input('month', Carbon::now()->month);
        $year = (int) $request->input('year', Carbon::now()->year);
        $status = $request->input('payment_status');
        $department = $request->input('department');
        $search = $request->input('search');

        $query = Payroll::with(['employee.salaryStructure', 'employee.user', 'generatedBy'])
            ->where('month', $month)
            ->where('year', $year)
            ->latest();

        if ($isEmployee) {
            $myEmployeeId = $myEmployee ? $myEmployee->id : 0;
            $query->where('employee_id', $myEmployeeId);
        }

        if ($status && in_array($status, ['Pending', 'Processing', 'Paid'])) {
            $query->where('payment_status', $status);
        }

        if ($department && ! $isEmployee) {
            $query->whereHas('employee', function ($q) use ($department) {
                $q->where('department', $department);
            });
        }

        if ($search && ! $isEmployee) {
            $query->where(function ($q) use ($search) {
                $q->where('payroll_code', 'like', "%{$search}%")
                    ->orWhere('transaction_reference', 'like', "%{$search}%")
                    ->orWhereHas('employee', function ($eq) use ($search) {
                        $eq->where('employee_name', 'like', "%{$search}%")
                            ->orWhere('employee_code', 'like', "%{$search}%");
                    });
            });
        }

        $payrolls = $query->paginate(20)->withQueryString();

        // Month metrics
        $allMonthPayrollsQuery = Payroll::where('month', $month)->where('year', $year);
        if ($isEmployee) {
            $allMonthPayrollsQuery->where('employee_id', $myEmployee?->id ?? 0);
        }
        $allMonthPayrolls = $allMonthPayrollsQuery->get();
        $totalNetSum = (float) $allMonthPayrolls->sum('net_salary');
        $paidCount = $allMonthPayrolls->where('payment_status', 'Paid')->count();
        $paidAmount = (float) $allMonthPayrolls->where('payment_status', 'Paid')->sum('net_salary');
        $pendingCount = $allMonthPayrolls->where('payment_status', 'Pending')->count();
        $pendingAmount = (float) $allMonthPayrolls->where('payment_status', 'Pending')->sum('net_salary');

        $stats = [
            'total_staff_processed' => $allMonthPayrolls->count(),
            'total_net_payout' => $totalNetSum,
            'paid_count' => $paidCount,
            'paid_amount' => $paidAmount,
            'pending_count' => $pendingCount,
            'pending_amount' => $pendingAmount,
        ];

        $employees = Employee::where('status', 'Active')->with('salaryStructure')->orderBy('employee_name')->get();
        $departments = Department::orderBy('name')->get();

        return view('payrolls.index', compact(
            'payrolls',
            'month',
            'year',
            'status',
            'department',
            'search',
            'stats',
            'employees',
            'departments'
        ));
    }

    /**
     * Bulk generate or refresh payroll for the selected month and year based on employee attendance & leaves.
     */
    public function generate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'year' => ['required', 'integer', 'min:2020', 'max:2035'],
            'employee_ids' => ['nullable', 'array'],
            'employee_ids.*' => ['exists:employees,id'],
        ]);

        $month = (int) $validated['month'];
        $year = (int) $validated['year'];

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        $totalMonthDays = $startDate->daysInMonth;

        // Employees to process
        if (! empty($validated['employee_ids'])) {
            $employees = Employee::whereIn('id', $validated['employee_ids'])->get();
        } else {
            $employees = Employee::where('status', 'Active')->get();
        }

        $processedCount = 0;

        foreach ($employees as $emp) {
            // Find or initialize salary structure
            $salaryStruct = $emp->salaryStructure;
            if (! $salaryStruct) {
                // Set reasonable default salary structure if not configured yet (e.g. CTC 4,20,000 => 35,000 gross)
                $salaryStruct = SalaryStructure::create([
                    'employee_id' => $emp->id,
                    'ctc_annual' => 420000.00,
                    'monthly_gross' => 35000.00,
                    'basic_salary' => 17500.00,
                    'hra' => 8750.00,
                    'special_allowance' => 8750.00,
                    'pf_deduction' => 1800.00,
                    'professional_tax' => 200.00,
                    'effective_from' => Carbon::now()->startOfYear()->toDateString(),
                ]);
            }

            $monthlyGross = (float) $salaryStruct->monthly_gross;
            $basicSalary = (float) $salaryStruct->basic_salary;
            $hra = (float) $salaryStruct->hra;
            $specialAllowance = (float) $salaryStruct->special_allowance;
            $conveyance = (float) $salaryStruct->conveyance_allowance;
            $medical = (float) $salaryStruct->medical_allowance;

            $pfDeduction = (float) $salaryStruct->pf_deduction;
            $esicDeduction = (float) $salaryStruct->esic_deduction;
            $profTax = (float) $salaryStruct->professional_tax;
            $tds = (float) $salaryStruct->tds;

            // Fetch all attendance records for this month
            $monthAttendances = Attendance::where('employee_id', $emp->id)
                ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
                ->get();

            $presentCount = $monthAttendances->where('status', 'Present')->count();
            $halfDayCount = $monthAttendances->where('status', 'Half Day')->count();
            $leaveCount = $monthAttendances->where('status', 'Leave')->count();
            $holidayCount = $monthAttendances->whereIn('status', ['Holiday', 'Week Off'])->count();
            $absentCount = $monthAttendances->where('status', 'Absent')->count();

            // Count Sundays / weekoffs in month if not logged in attendances
            $totalSundays = 0;
            $curr = $startDate->copy();
            while ($curr->lte($endDate)) {
                if ($curr->isSunday()) {
                    $totalSundays++;
                }
                $curr->addDay();
            }

            // Effective payable days
            // If employee has unmarked days or records:
            // If attendances logged: present + (halfDay * 0.5) + leave + (holidays/sundays)
            // If no attendance entries were recorded for this month, default full attendance to avoid zero payout
            if ($monthAttendances->isEmpty()) {
                $payableDays = (float) $totalMonthDays;
                $presentDays = (float) ($totalMonthDays - $totalSundays);
                $paidLeaves = 0.0;
                $holidaysWeekoffs = $totalSundays;
                $absentDays = 0.0;
            } else {
                $presentDays = (float) ($presentCount + ($halfDayCount * 0.5));
                $paidLeaves = (float) $leaveCount;
                $holidaysWeekoffs = max($holidayCount, $totalSundays);
                $payableDays = min((float) $totalMonthDays, $presentDays + $paidLeaves + $holidaysWeekoffs);
                $absentDays = (float) max(0, $totalMonthDays - $payableDays);
            }

            // Calculate Pro-Rata Earnings
            $ratio = $totalMonthDays > 0 ? ($payableDays / $totalMonthDays) : 1;
            $earnedBasic = round($basicSalary * $ratio, 2);
            $earnedHra = round($hra * $ratio, 2);
            $earnedAllowances = round(($specialAllowance + $conveyance + $medical) * $ratio, 2);
            $totalEarnings = round($earnedBasic + $earnedHra + $earnedAllowances, 2);

            // Deductions
            $unpaidLeaveDeduction = round(max(0, $monthlyGross - $totalEarnings), 2);
            $totalDeductions = round($pfDeduction + $esicDeduction + $profTax + $tds, 2);

            // Net Salary
            $netSalary = max(0, round($totalEarnings - $totalDeductions, 2));

            // Payroll code
            $paddedMonth = str_pad((string) $month, 2, '0', STR_PAD_LEFT);
            $payrollCode = "PAY-{$year}{$paddedMonth}-{$emp->employee_code}";

            Payroll::updateOrCreate(
                [
                    'employee_id' => $emp->id,
                    'month' => $month,
                    'year' => $year,
                ],
                [
                    'payroll_code' => $payrollCode,
                    'total_month_days' => $totalMonthDays,
                    'working_days' => max(0, $totalMonthDays - $totalSundays),
                    'present_days' => $presentDays,
                    'paid_leaves' => $paidLeaves,
                    'holidays_weekoffs' => $holidaysWeekoffs,
                    'absent_days' => $absentDays,
                    'payable_days' => $payableDays,
                    'gross_salary' => $monthlyGross,
                    'earned_basic' => $earnedBasic,
                    'earned_hra' => $earnedHra,
                    'earned_allowances' => $earnedAllowances,
                    'overtime_bonus' => 0.00,
                    'total_earnings' => $totalEarnings,
                    'pf_amount' => $pfDeduction,
                    'esic_amount' => $esicDeduction,
                    'prof_tax' => $profTax,
                    'tds_amount' => $tds,
                    'unpaid_leave_deduction' => $unpaidLeaveDeduction,
                    'other_deductions' => 0.00,
                    'total_deductions' => $totalDeductions,
                    'net_salary' => $netSalary,
                    'generated_by' => Auth::id(),
                ]
            );

            $processedCount++;
        }

        $monthName = Carbon::createFromDate($year, $month, 1)->format('F Y');

        return back()->with('success', "Payroll generated successfully for {$processedCount} employee(s) for {$monthName}.");
    }

    /**
     * Store or update employee's salary package structure.
     */
    public function storeSalaryStructure(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'monthly_gross' => ['required', 'numeric', 'min:0'],
            'basic_salary' => ['required', 'numeric', 'min:0'],
            'hra' => ['nullable', 'numeric', 'min:0'],
            'special_allowance' => ['nullable', 'numeric', 'min:0'],
            'conveyance_allowance' => ['nullable', 'numeric', 'min:0'],
            'medical_allowance' => ['nullable', 'numeric', 'min:0'],
            'pf_deduction' => ['nullable', 'numeric', 'min:0'],
            'esic_deduction' => ['nullable', 'numeric', 'min:0'],
            'professional_tax' => ['nullable', 'numeric', 'min:0'],
            'tds' => ['nullable', 'numeric', 'min:0'],
            'effective_from' => ['nullable', 'date'],
        ]);

        $monthlyGross = (float) $validated['monthly_gross'];
        $ctcAnnual = round($monthlyGross * 12, 2);

        SalaryStructure::updateOrCreate(
            ['employee_id' => $validated['employee_id']],
            [
                'ctc_annual' => $ctcAnnual,
                'monthly_gross' => $monthlyGross,
                'basic_salary' => $validated['basic_salary'],
                'hra' => $validated['hra'] ?? 0,
                'special_allowance' => $validated['special_allowance'] ?? 0,
                'conveyance_allowance' => $validated['conveyance_allowance'] ?? 0,
                'medical_allowance' => $validated['medical_allowance'] ?? 0,
                'pf_deduction' => $validated['pf_deduction'] ?? 0,
                'esic_deduction' => $validated['esic_deduction'] ?? 0,
                'professional_tax' => $validated['professional_tax'] ?? 200,
                'tds' => $validated['tds'] ?? 0,
                'effective_from' => $validated['effective_from'] ?? Carbon::now()->toDateString(),
            ]
        );

        return back()->with('success', 'Employee salary structure updated successfully.');
    }

    /**
     * Update payout details (Mark as Paid / Processing).
     */
    public function updatePayout(Request $request, Payroll $payroll): RedirectResponse
    {
        $validated = $request->validate([
            'payment_status' => ['required', 'in:Pending,Processing,Paid'],
            'payment_mode' => ['nullable', 'string', 'in:Bank Transfer,NEFT/RTGS,UPI,Cheque,Cash'],
            'transaction_reference' => ['nullable', 'string', 'max:100'],
            'paid_at' => ['nullable', 'date'],
            'remarks' => ['nullable', 'string', 'max:255'],
        ]);

        $paidAt = ($validated['payment_status'] === 'Paid')
            ? ($validated['paid_at'] ? Carbon::parse($validated['paid_at']) : Carbon::now())
            : null;

        $payroll->update([
            'payment_status' => $validated['payment_status'],
            'payment_mode' => $validated['payment_mode'] ?? 'Bank Transfer',
            'transaction_reference' => $validated['transaction_reference'] ?? null,
            'paid_at' => $paidAt,
            'remarks' => $validated['remarks'] ?? null,
        ]);

        return back()->with('success', "Payout status updated to '{$payroll->payment_status}' for {$payroll->employee->employee_name}.");
    }

    /**
     * Display printable payslip for a specific payroll record.
     */
    public function payslip(Request $request, Payroll $payroll): View
    {
        $user = $request->user();
        if (! $user->isAdmin()) {
            if (! $user->employee || $user->employee->id !== $payroll->employee_id) {
                abort(403, 'Unauthorized access. You can only view your own payslip.');
            }
        }

        $payroll->load(['employee.user', 'generatedBy']);

        return view('payrolls.payslip', compact('payroll'));
    }

    /**
     * Delete an individual payroll entry.
     */
    public function destroy(Payroll $payroll): RedirectResponse
    {
        $payroll->delete();

        return back()->with('success', 'Payroll record removed successfully.');
    }
}
