<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\DailyWorkReport;
use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the HRMS Superadmin Dashboard.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $isEmployee = ! $user->isAdmin();
        $employee = $user->employee;

        $today = Carbon::today()->toDateString();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        if ($isEmployee) {
            // Employee specific dashboard
            $todayAttendance = $employee ? Attendance::where('employee_id', $employee->id)->where('date', $today)->first() : null;

            // Month attendance for this employee
            $monthAttendances = $employee ? Attendance::where('employee_id', $employee->id)
                ->whereYear('date', $currentYear)
                ->whereMonth('date', $currentMonth)
                ->get() : collect();

            $myPresentDays = $monthAttendances->where('status', 'Present')->count();
            $myLeavesDays = $monthAttendances->whereIn('status', ['Leave', 'Half Day'])->count();
            $myTotalHours = round($monthAttendances->sum('total_hours'), 1);

            // My leaves requests
            $myLeavesCount = $employee ? LeaveRequest::where('employee_id', $employee->id)->count() : 0;
            $myPendingLeavesCount = $employee ? LeaveRequest::where('employee_id', $employee->id)->where('status', 'Pending')->count() : 0;

            // Latest payslip
            $latestPayroll = $employee ? Payroll::where('employee_id', $employee->id)->latest()->first() : null;

            // Recent activity or work reports
            $recentWorkReports = $employee ? DailyWorkReport::where('employee_id', $employee->id)->latest('report_date')->take(5)->get() : collect();

            $employeeStats = [
                'present_days' => $myPresentDays,
                'leave_days' => $myLeavesDays,
                'total_hours' => $myTotalHours,
                'pending_leaves' => $myPendingLeavesCount,
                'total_leaves' => $myLeavesCount,
                'latest_net_salary' => $latestPayroll ? '₹'.number_format($latestPayroll->net_salary, 2) : 'Pending',
                'latest_payroll' => $latestPayroll,
            ];

            $attendanceSettings = [
                'office_latitude' => (float) Setting::get('office_latitude', 23.0225),
                'office_longitude' => (float) Setting::get('office_longitude', 72.5714),
                'geofence_radius_meters' => (int) Setting::get('geofence_radius_meters', 250),
                'require_geofence' => (bool) Setting::get('require_geofence', true),
                'require_camera_punch' => (bool) Setting::get('require_camera_punch', true),
            ];

            return view('dashboard', compact('isEmployee', 'employee', 'todayAttendance', 'employeeStats', 'recentWorkReports', 'attendanceSettings'));
        }

        // Superadmin / HR Admin metrics
        $totalEmployees = Employee::count();
        $activeEmployees = Employee::where('status', 'Active')->count();
        $inactiveEmployees = Employee::where('status', '!=', 'Active')->count();
        $totalUsers = User::count();

        // Today's Live Attendance metrics
        $todayAttendances = Attendance::with('employee')->where('date', $today)->get();
        $presentToday = $todayAttendances->where('status', 'Present')->count();
        $absentToday = $todayAttendances->where('status', 'Absent')->count();
        $onLeaveToday = $todayAttendances->whereIn('status', ['Leave', 'Half Day'])->count();
        $lateArrivalsToday = $todayAttendances->where('is_late', true)->count();
        $overtimeCountToday = $todayAttendances->where('is_overtime', true)->count();
        $currentlyWorking = $todayAttendances->filter(fn ($att) => $att->clock_in && ! $att->clock_out && ! ($att->break_in && ! $att->break_out))->count();
        $currentlyOnBreak = $todayAttendances->filter(fn ($att) => $att->break_in && ! $att->break_out)->count();

        // Attendance Percentage Today
        $attendanceRate = $activeEmployees > 0 ? round(($presentToday / $activeEmployees) * 100, 1) : 0;

        // Leave Requests
        $pendingLeaves = LeaveRequest::with('employee')->where('status', 'Pending')->latest()->take(5)->get();
        $pendingLeavesCount = LeaveRequest::where('status', 'Pending')->count();
        $approvedLeavesThisMonth = LeaveRequest::where('status', 'Approved')
            ->whereMonth('start_date', $currentMonth)
            ->whereYear('start_date', $currentYear)
            ->count();

        // Monthly Payroll stats
        $monthPayroll = Payroll::where('month', $currentMonth)->where('year', $currentYear)->get();
        $monthPayrollTotal = $monthPayroll->sum('net_salary');
        $payrollGeneratedCount = $monthPayroll->count();
        $payrollPaidCount = $monthPayroll->where('status', 'Paid')->count();
        $payrollCostFormatted = $monthPayrollTotal > 0 ? '₹'.number_format($monthPayrollTotal, 2) : '₹0.00';

        // Department Breakdown
        $departmentStats = Employee::selectRaw('department, count(*) as count')
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->groupBy('department')
            ->orderByDesc('count')
            ->take(5)
            ->get();

        // Today's Daily Work Reports & Activities
        $allTodayWorkReports = DailyWorkReport::with('employee')->where('report_date', $today)->get();
        $todayWorkReports = $allTodayWorkReports->take(5);
        $todayReportsCount = $allTodayWorkReports->count();
        $todayCallsCount = $allTodayWorkReports->sum('calls');
        $todaySocialCount = $allTodayWorkReports->sum(fn ($report) => $report->social_outreach_activities);

        // Recent Employees
        $recentEmployees = Employee::with('user')->latest()->take(6)->get();

        // Recent Live Clock-Ins
        $recentPunches = Attendance::with('employee')
            ->where('date', $today)
            ->whereNotNull('clock_in')
            ->orderByDesc('clock_in')
            ->take(5)
            ->get();

        $stats = [
            'total_staff' => $totalEmployees,
            'active_staff' => $activeEmployees,
            'inactive_staff' => $inactiveEmployees,
            'total_users' => $totalUsers,
            'departments' => Department::count(),
            'present_today' => $presentToday,
            'absent_today' => $absentToday,
            'on_leave' => $onLeaveToday,
            'late_today' => $lateArrivalsToday,
            'overtime_today' => $overtimeCountToday,
            'currently_working' => $currentlyWorking,
            'currently_on_break' => $currentlyOnBreak,
            'attendance_rate' => $attendanceRate,
            'pending_leaves_count' => $pendingLeavesCount,
            'approved_leaves_month' => $approvedLeavesThisMonth,
            'payroll_cost' => $payrollCostFormatted,
            'payroll_generated_count' => $payrollGeneratedCount,
            'payroll_paid_count' => $payrollPaidCount,
            'today_reports_count' => $todayReportsCount,
            'today_calls' => $todayCallsCount,
            'today_social' => $todaySocialCount,
        ];

        return view('dashboard', compact(
            'isEmployee',
            'stats',
            'recentEmployees',
            'pendingLeaves',
            'departmentStats',
            'todayWorkReports',
            'recentPunches'
        ));
    }
}
