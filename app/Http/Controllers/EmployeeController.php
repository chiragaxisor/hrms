<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\SalaryStructure;
use App\Models\User;
use App\Support\EmployeeFormData;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees.
     */
    public function index(Request $request): View
    {
        $query = Employee::with(['salaryStructure', 'user']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('employee_name', 'like', "%{$search}%")
                    ->orWhere('employee_code', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%")
                    ->orWhere('company_email', 'like', "%{$search}%")
                    ->orWhere('designation', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department')) {
            $query->where('department', $request->input('department'));
        }

        $employees = $query->latest()->paginate(10)->withQueryString();
        $departments = Department::where('status', 'Active')->orderBy('name')->pluck('name')->toArray();

        return view('employees.index', compact('employees', 'departments'));
    }

    /**
     * Show the multi-section new employee joining form.
     */
    public function create(): View
    {
        // Auto-generate employee code suggestion
        $latestId = Employee::max('id') ?? 0;
        $suggestedCode = 'EMP-'.date('Y').'-'.str_pad($latestId + 1, 4, '0', STR_PAD_LEFT);
        $departments = Department::where('status', 'Active')->orderBy('name')->get();
        $designations = Designation::where('status', 'Active')->orderBy('title')->get();
        $formData = EmployeeFormData::all();

        return view('employees.create', array_merge(
            compact('suggestedCode', 'departments', 'designations'),
            $formData
        ));
    }

    /**
     * Store a newly created employee with all 10 master sections.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            // 1. Employee & Job Details
            'employee_name' => 'required|string|max:255',
            'employee_code' => 'required|string|max:50|unique:employees,employee_code',
            'department' => 'required|string|max:100',
            'designation' => 'required|string|max:100',
            'reporting_person_name' => 'nullable|string|max:255',
            'work_location' => 'nullable|string|max:100',
            'branch' => 'nullable|string|max:100',
            'office_number' => 'nullable|string|max:50',
            'company_email' => 'nullable|email|max:255',
            'joining_date' => 'nullable|date',
            'employment_type' => 'required|string|in:Full Time,Part Time,Contract,Intern',
            'status' => 'nullable|string|in:Active,On Leave,Resigned,Terminated',

            // 2. Personal Details
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|in:Male,Female,Other',
            'marital_status' => 'nullable|string|in:Single,Married,Other',
            'anniversary_date' => 'nullable|date',
            'personal_mobile' => 'nullable|string|max:20',
            'personal_email' => 'nullable|email|max:255',
            'aadhaar_linked_mobile' => 'nullable|string|max:20',
            'blood_group' => 'nullable|string|max:10',
            'pan_number' => 'nullable|string|max:20',
            'aadhaar_number' => 'nullable|string|max:20',

            // 3. Statutory Details
            'statutory_details' => 'nullable|array',

            // 4. Bank Details
            'bank_details' => 'nullable|array',

            // 5. Address Details
            'address_details' => 'nullable|array',

            // 6. Emergency & Reference Contact
            'emergency_contact' => 'nullable|array',
            'reference_contact' => 'nullable|array',

            // 7. Nominee Details
            'nominee_details' => 'nullable|array',

            // 8. Family Members Details
            'family_members' => 'nullable|array',

            // 9. Document Checklist
            'document_checklist' => 'nullable|array',

            // 10. Asset / Device Details
            'asset_details' => 'nullable|array',
        ]);

        // Automatically create a user login if company email is given
        if (! empty($validated['company_email'])) {
            $user = User::firstOrCreate(
                ['email' => $validated['company_email']],
                [
                    'name' => $validated['employee_name'],
                    'password' => bcrypt('password'),
                    'role' => 'employee',
                    'email_verified_at' => now(),
                ]
            );
            $validated['user_id'] = $user->id;
        }

        $employee = Employee::create($validated);

        // Save Salary Structure if provided
        if ($request->filled('salary_structure.monthly_gross')) {
            $salaryData = $request->input('salary_structure');
            $gross = (float) ($salaryData['monthly_gross'] ?? 35000);
            SalaryStructure::updateOrCreate(
                ['employee_id' => $employee->id],
                [
                    'ctc_annual' => round($gross * 12, 2),
                    'monthly_gross' => $gross,
                    'basic_salary' => (float) ($salaryData['basic_salary'] ?? ($gross * 0.5)),
                    'hra' => (float) ($salaryData['hra'] ?? ($gross * 0.25)),
                    'special_allowance' => (float) ($salaryData['special_allowance'] ?? ($gross * 0.25)),
                    'pf_deduction' => (float) ($salaryData['pf_deduction'] ?? 1800),
                    'professional_tax' => (float) ($salaryData['professional_tax'] ?? 200),
                    'tds' => (float) ($salaryData['tds'] ?? 0),
                    'effective_from' => now()->toDateString(),
                ]
            );
        }

        if ($request->filled('next_tab')) {
            return redirect()->route('employees.edit', ['employee' => $employee, 'tab' => $request->input('next_tab')])
                ->with('success', 'Progress saved! Continued to the next step.');
        }

        return redirect()->route('employees.show', $employee)
            ->with('success', "Employee {$employee->employee_name} ({$employee->employee_code}) onboarding created successfully!");
    }

    /**
     * Display the specified employee master profile.
     */
    public function show(Employee $employee): View
    {
        return view('employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the employee master.
     */
    public function edit(Employee $employee): View
    {
        $departments = Department::where('status', 'Active')->orderBy('name')->get();
        $designations = Designation::where('status', 'Active')->orderBy('title')->get();
        $formData = EmployeeFormData::all($employee->id);

        return view('employees.edit', array_merge(
            compact('employee', 'departments', 'designations'),
            $formData
        ));
    }

    /**
     * Update the employee record.
     */
    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $validated = $request->validate([
            'employee_name' => 'required|string|max:255',
            'employee_code' => 'required|string|max:50|unique:employees,employee_code,'.$employee->id,
            'department' => 'required|string|max:100',
            'designation' => 'required|string|max:100',
            'reporting_person_name' => 'nullable|string|max:255',
            'work_location' => 'nullable|string|max:100',
            'branch' => 'nullable|string|max:100',
            'office_number' => 'nullable|string|max:50',
            'company_email' => 'nullable|email|max:255',
            'joining_date' => 'nullable|date',
            'employment_type' => 'required|string|in:Full Time,Part Time,Contract,Intern',
            'status' => 'required|string|in:Active,On Leave,Resigned,Terminated',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|in:Male,Female,Other',
            'marital_status' => 'nullable|string|in:Single,Married,Other',
            'anniversary_date' => 'nullable|date',
            'personal_mobile' => 'nullable|string|max:20',
            'personal_email' => 'nullable|email|max:255',
            'aadhaar_linked_mobile' => 'nullable|string|max:20',
            'blood_group' => 'nullable|string|max:10',
            'pan_number' => 'nullable|string|max:20',
            'aadhaar_number' => 'nullable|string|max:20',
            'statutory_details' => 'nullable|array',
            'bank_details' => 'nullable|array',
            'address_details' => 'nullable|array',
            'emergency_contact' => 'nullable|array',
            'reference_contact' => 'nullable|array',
            'nominee_details' => 'nullable|array',
            'family_members' => 'nullable|array',
            'document_checklist' => 'nullable|array',
            'asset_details' => 'nullable|array',
        ]);

        // Automatically create or update user login if company email is given
        if (! empty($validated['company_email'])) {
            if ($employee->user) {
                $employee->user->update([
                    'email' => $validated['company_email'],
                    'name' => $validated['employee_name'],
                ]);
            } else {
                $user = User::firstOrCreate(
                    ['email' => $validated['company_email']],
                    [
                        'name' => $validated['employee_name'],
                        'password' => bcrypt('password'),
                        'role' => 'employee',
                        'email_verified_at' => now(),
                    ]
                );
                $validated['user_id'] = $user->id;
            }
        }

        $employee->update($validated);

        // Update Salary Structure if provided
        if ($request->filled('salary_structure.monthly_gross')) {
            $salaryData = $request->input('salary_structure');
            $gross = (float) ($salaryData['monthly_gross'] ?? 35000);
            SalaryStructure::updateOrCreate(
                ['employee_id' => $employee->id],
                [
                    'ctc_annual' => round($gross * 12, 2),
                    'monthly_gross' => $gross,
                    'basic_salary' => (float) ($salaryData['basic_salary'] ?? ($gross * 0.5)),
                    'hra' => (float) ($salaryData['hra'] ?? ($gross * 0.25)),
                    'special_allowance' => (float) ($salaryData['special_allowance'] ?? ($gross * 0.25)),
                    'pf_deduction' => (float) ($salaryData['pf_deduction'] ?? 1800),
                    'professional_tax' => (float) ($salaryData['professional_tax'] ?? 200),
                    'tds' => (float) ($salaryData['tds'] ?? 0),
                    'effective_from' => now()->toDateString(),
                ]
            );
        }

        if ($request->filled('next_tab')) {
            return redirect()->route('employees.edit', ['employee' => $employee, 'tab' => $request->input('next_tab')])
                ->with('success', 'Progress saved! Continued to the next step.');
        }

        return redirect()->route('employees.show', $employee)
            ->with('success', 'Employee master data updated successfully!');
    }

    /**
     * Remove the specified employee.
     */
    public function destroy(Employee $employee): RedirectResponse
    {
        $name = $employee->employee_name;
        $employee->delete();

        return redirect()->route('employees.index')
            ->with('success', "Employee {$name} record deleted successfully.");
    }
}
