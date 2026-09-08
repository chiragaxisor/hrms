<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LeaveRequestController extends Controller
{
    /**
     * Display a listing of leave requests with filters and stats.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $isEmployee = ! $user->isAdmin();
        $myEmployee = $user->employee;

        $statusFilter = $request->input('status');
        $departmentFilter = $request->input('department');
        $search = $request->input('search');

        $query = LeaveRequest::with(['employee.user', 'actionBy'])->latest();

        if ($isEmployee) {
            $myEmployeeId = $myEmployee ? $myEmployee->id : 0;
            $query->where('employee_id', $myEmployeeId);
        }

        if ($statusFilter && in_array($statusFilter, ['Pending', 'Approved', 'Rejected', 'Cancelled'])) {
            $query->where('status', $statusFilter);
        }

        if ($departmentFilter && ! $isEmployee) {
            $query->whereHas('employee', function ($q) use ($departmentFilter) {
                $q->where('department', $departmentFilter);
            });
        }

        if ($search && ! $isEmployee) {
            $query->where(function ($q) use ($search) {
                $q->where('leave_type', 'like', "%{$search}%")
                    ->orWhere('reason', 'like', "%{$search}%")
                    ->orWhereHas('employee', function ($eq) use ($search) {
                        $eq->where('employee_name', 'like', "%{$search}%")
                            ->orWhere('employee_code', 'like', "%{$search}%");
                    });
            });
        }

        $leaveRequests = $query->paginate(15)->withQueryString();

        // Summary metrics
        $allLeavesQuery = LeaveRequest::query();
        if ($isEmployee) {
            $allLeavesQuery->where('employee_id', $myEmployee?->id ?? 0);
        }
        $allLeaves = $allLeavesQuery->get();

        $today = Carbon::today()->toDateString();
        $onLeaveTodayQuery = Attendance::where('date', $today)->whereIn('status', ['Leave', 'Half Day']);
        if ($isEmployee) {
            $onLeaveTodayQuery->where('employee_id', $myEmployee?->id ?? 0);
        }
        $onLeaveTodayCount = $onLeaveTodayQuery->count();

        $stats = [
            'total' => $allLeaves->count(),
            'pending' => $allLeaves->where('status', 'Pending')->count(),
            'approved' => $allLeaves->where('status', 'Approved')->count(),
            'rejected' => $allLeaves->where('status', 'Rejected')->count(),
            'on_leave_today' => $onLeaveTodayCount,
        ];

        if ($isEmployee) {
            $employees = $myEmployee ? collect([$myEmployee]) : collect();
        } else {
            $employees = Employee::where('status', 'Active')->orderBy('employee_name')->get();
        }
        $departments = Department::orderBy('name')->get();

        return view('leaves.index', compact(
            'leaveRequests',
            'stats',
            'employees',
            'departments',
            'statusFilter',
            'departmentFilter',
            'search',
            'myEmployee'
        ));
    }

    /**
     * Store a newly created leave request.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $isEmployee = ! $user->isAdmin();
        $myEmployee = $user->employee;

        if ($isEmployee) {
            if (! $myEmployee) {
                abort(403, 'No employee record linked with your account.');
            }
            $request->merge(['employee_id' => $myEmployee->id]);
        }

        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'leave_type' => ['required', 'string', 'max:50'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'is_half_day' => ['nullable', 'boolean'],
            'half_day_type' => ['nullable', 'string', 'in:first_half,second_half'],
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $isHalfDay = (bool) ($request->input('is_half_day', false));
        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);

        if ($isHalfDay) {
            // Half day applies to single date
            $endDate = $startDate->copy();
            $totalDays = 0.5;
        } else {
            $totalDays = $startDate->diffInDays($endDate) + 1;
        }

        LeaveRequest::create([
            'employee_id' => $validated['employee_id'],
            'leave_type' => $validated['leave_type'],
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'total_days' => $totalDays,
            'is_half_day' => $isHalfDay,
            'half_day_type' => $isHalfDay ? ($validated['half_day_type'] ?? 'first_half') : null,
            'reason' => $validated['reason'],
            'status' => 'Pending',
        ]);

        return back()->with('success', 'Leave request submitted successfully. Waiting for approval.');
    }

    /**
     * Approve a leave request and synchronize Attendance records as 'Leave' or 'Half Day'.
     */
    public function approve(Request $request, LeaveRequest $leave): RedirectResponse
    {
        $validated = $request->validate([
            'admin_remarks' => ['nullable', 'string', 'max:500'],
        ]);

        $leave->update([
            'status' => 'Approved',
            'action_by' => Auth::id(),
            'action_at' => Carbon::now(),
            'admin_remarks' => $validated['admin_remarks'] ?? 'Approved by Admin',
        ]);

        // Synchronize with Attendance records for each day in range
        $period = CarbonPeriod::create($leave->start_date, $leave->end_date);
        $status = $leave->is_half_day ? 'Half Day' : 'Leave';
        $totalHours = $leave->is_half_day ? 4.50 : 0.00;
        $halfDayNote = $leave->is_half_day ? " ({$leave->half_day_type})" : '';
        $remarks = "Approved Leave: {$leave->leave_type}{$halfDayNote} - {$leave->reason}";

        foreach ($period as $date) {
            $dateString = $date->toDateString();

            Attendance::updateOrCreate(
                [
                    'employee_id' => $leave->employee_id,
                    'date' => $dateString,
                ],
                [
                    'status' => $status,
                    'clock_in' => null,
                    'clock_out' => null,
                    'break_in' => null,
                    'break_out' => null,
                    'break_minutes' => 0,
                    'total_hours' => $totalHours,
                    'shift' => 'General',
                    'late_minutes' => 0,
                    'location_type' => 'Office',
                    'remarks' => $remarks,
                    'created_by' => Auth::id(),
                ]
            );
        }

        return back()->with('success', "Leave request approved! Attendance records updated to '{$status}' for {$leave->total_days} day(s).");
    }

    /**
     * Reject a leave request and restore any previously created attendance if was approved.
     */
    public function reject(Request $request, LeaveRequest $leave): RedirectResponse
    {
        $validated = $request->validate([
            'admin_remarks' => ['required', 'string', 'max:500'],
        ]);

        $wasApproved = ($leave->status === 'Approved');

        $leave->update([
            'status' => 'Rejected',
            'action_by' => Auth::id(),
            'action_at' => Carbon::now(),
            'admin_remarks' => $validated['admin_remarks'],
        ]);

        // If it was previously approved, remove/reset attendance records created by this leave
        if ($wasApproved) {
            $period = CarbonPeriod::create($leave->start_date, $leave->end_date);
            foreach ($period as $date) {
                Attendance::where('employee_id', $leave->employee_id)
                    ->where('date', $date->toDateString())
                    ->whereIn('status', ['Leave', 'Half Day'])
                    ->where('remarks', 'like', 'Approved Leave%')
                    ->delete();
            }
        }

        return back()->with('success', 'Leave request has been rejected.');
    }

    /**
     * Cancel/Delete a leave request.
     */
    public function destroy(LeaveRequest $leave): RedirectResponse
    {
        // If approved, cleanup attendance
        if ($leave->status === 'Approved') {
            $period = CarbonPeriod::create($leave->start_date, $leave->end_date);
            foreach ($period as $date) {
                Attendance::where('employee_id', $leave->employee_id)
                    ->where('date', $date->toDateString())
                    ->whereIn('status', ['Leave', 'Half Day'])
                    ->where('remarks', 'like', 'Approved Leave%')
                    ->delete();
            }
        }

        $leave->delete();

        return back()->with('success', 'Leave request deleted successfully.');
    }
}
