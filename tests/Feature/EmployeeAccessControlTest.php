<?php

namespace Tests\Feature;

use App\Models\DailyWorkReport;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeAccessControlTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    private User $employeeUser;

    private Employee $adminEmployee;

    private Employee $standardEmployee;

    protected function setUp(): void
    {
        parent::setUp();

        // HR Admin User
        $this->adminUser = User::factory()->create([
            'name' => 'HR Admin',
            'email' => 'admin@company.com',
            'role' => 'admin',
        ]);
        $this->adminEmployee = Employee::create([
            'employee_name' => 'HR Admin',
            'employee_code' => 'EMP-ADM-01',
            'department' => 'Human Resources',
            'designation' => 'HR Manager',
            'employment_type' => 'Full Time',
            'status' => 'Active',
            'user_id' => $this->adminUser->id,
        ]);

        // Standard Employee User
        $this->employeeUser = User::factory()->create([
            'name' => 'Rahul Sharma',
            'email' => 'rahul@company.com',
            'role' => 'employee',
        ]);
        $this->standardEmployee = Employee::create([
            'employee_name' => 'Rahul Sharma',
            'employee_code' => 'EMP-0099',
            'department' => 'Engineering',
            'designation' => 'Developer',
            'employment_type' => 'Full Time',
            'status' => 'Active',
            'user_id' => $this->employeeUser->id,
        ]);
    }

    public function test_employee_is_forbidden_from_accessing_organization_masters(): void
    {
        // Organization Masters (Employees directory, Departments, Designations)
        $this->actingAs($this->employeeUser)->get(route('employees.index'))->assertForbidden();
        $this->actingAs($this->employeeUser)->get(route('departments.index'))->assertForbidden();
        $this->actingAs($this->employeeUser)->get(route('designations.index'))->assertForbidden();
    }

    public function test_employee_is_forbidden_from_accessing_system_settings(): void
    {
        $this->actingAs($this->employeeUser)->get(route('settings.index'))->assertForbidden();
        $this->actingAs($this->employeeUser)->post(route('settings.update'), ['app_name' => 'Hacked'])->assertForbidden();
    }

    public function test_employee_cannot_approve_or_reject_leaves(): void
    {
        $leave = LeaveRequest::create([
            'employee_id' => $this->standardEmployee->id,
            'leave_type' => 'Casual Leave',
            'start_date' => Carbon::tomorrow()->toDateString(),
            'end_date' => Carbon::tomorrow()->toDateString(),
            'total_days' => 1,
            'reason' => 'Personal work',
            'status' => 'Pending',
        ]);

        $this->actingAs($this->employeeUser)->post(route('leaves.approve', $leave->id))->assertForbidden();
        $this->actingAs($this->employeeUser)->post(route('leaves.reject', $leave->id))->assertForbidden();
    }

    public function test_employee_cannot_generate_payroll_or_payout(): void
    {
        $this->actingAs($this->employeeUser)->post(route('payrolls.generate'), [
            'month' => Carbon::now()->month,
            'year' => Carbon::now()->year,
        ])->assertForbidden();

        $payroll = Payroll::create([
            'payroll_code' => 'PAY-TEST-01',
            'employee_id' => $this->standardEmployee->id,
            'month' => Carbon::now()->month,
            'year' => Carbon::now()->year,
            'total_month_days' => 30,
            'payable_days' => 30,
            'gross_salary' => 50000,
            'basic_salary' => 25000,
            'total_earnings' => 50000,
            'total_deductions' => 0,
            'net_salary' => 50000,
            'payment_status' => 'Pending',
        ]);

        $this->actingAs($this->employeeUser)->post(route('payrolls.payout', $payroll->id), [
            'payment_status' => 'Paid',
        ])->assertForbidden();
    }

    public function test_employee_can_view_own_dashboard_with_self_service_portal(): void
    {
        $response = $this->actingAs($this->employeeUser)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Employee Self Service');
        $response->assertSee('Rahul Sharma');
        $response->assertSee('EMP-0099');
        $response->assertDontSee('Company Personnel Directory');
    }

    public function test_employee_can_only_view_own_payslip_and_cannot_view_others(): void
    {
        $ownPayroll = Payroll::create([
            'payroll_code' => 'PAY-OWN-01',
            'employee_id' => $this->standardEmployee->id,
            'month' => Carbon::now()->month,
            'year' => Carbon::now()->year,
            'total_month_days' => 30,
            'payable_days' => 30,
            'gross_salary' => 45000,
            'basic_salary' => 22500,
            'total_earnings' => 45000,
            'total_deductions' => 0,
            'net_salary' => 45000,
            'payment_status' => 'Paid',
        ]);

        $otherPayroll = Payroll::create([
            'payroll_code' => 'PAY-OTHER-01',
            'employee_id' => $this->adminEmployee->id,
            'month' => Carbon::now()->month,
            'year' => Carbon::now()->year,
            'total_month_days' => 30,
            'payable_days' => 30,
            'gross_salary' => 90000,
            'basic_salary' => 45000,
            'total_earnings' => 90000,
            'total_deductions' => 0,
            'net_salary' => 90000,
            'payment_status' => 'Paid',
        ]);

        // Own payslip is accessible
        $this->actingAs($this->employeeUser)->get(route('payrolls.payslip', $ownPayroll->id))->assertOk();

        // Other person's payslip is forbidden
        $this->actingAs($this->employeeUser)->get(route('payrolls.payslip', $otherPayroll->id))->assertForbidden();
    }

    public function test_employee_cannot_edit_or_delete_existing_daily_work_reports(): void
    {
        // 1. First-time logging for oneself succeeds
        $date = Carbon::today()->toDateString();
        $response = $this->actingAs($this->employeeUser)->post(route('work-reports.store'), [
            'employee_id' => $this->standardEmployee->id,
            'report_date' => $date,
            'calls' => 15,
            'instagram_dms' => 10,
            'remarks' => 'Completed outreach',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('daily_work_reports', [
            'employee_id' => $this->standardEmployee->id,
            'report_date' => $date,
            'calls' => 15,
        ]);

        $report = DailyWorkReport::where('employee_id', $this->standardEmployee->id)->where('report_date', $date)->first();

        // 2. Employee cannot EDIT existing report (fails with 403)
        $this->actingAs($this->employeeUser)->post(route('work-reports.store'), [
            'employee_id' => $this->standardEmployee->id,
            'report_date' => $date,
            'calls' => 99,
        ])->assertForbidden();

        // 3. Employee cannot DELETE existing report (fails with 403)
        $this->actingAs($this->employeeUser)->delete(route('work-reports.destroy', $report->id))->assertForbidden();

        // 4. Superadmin CAN edit existing report
        $superAdminUser = User::factory()->create(['role' => 'superadmin']);
        $editResponse = $this->actingAs($superAdminUser)->post(route('work-reports.store'), [
            'employee_id' => $this->standardEmployee->id,
            'report_date' => $date,
            'calls' => 99,
        ]);
        $editResponse->assertRedirect();
        $this->assertDatabaseHas('daily_work_reports', [
            'employee_id' => $this->standardEmployee->id,
            'report_date' => $date,
            'calls' => 99,
        ]);

        // 5. Superadmin CAN delete existing report
        $deleteResponse = $this->actingAs($superAdminUser)->delete(route('work-reports.destroy', $report->id));
        $deleteResponse->assertRedirect();
        $this->assertDatabaseMissing('daily_work_reports', ['id' => $report->id]);
    }

    public function test_admin_has_full_access_to_all_sections(): void
    {
        $this->actingAs($this->adminUser)->get(route('employees.index'))->assertOk();
        $this->actingAs($this->adminUser)->get(route('departments.index'))->assertOk();
        $this->actingAs($this->adminUser)->get(route('designations.index'))->assertOk();
        $this->actingAs($this->adminUser)->get(route('settings.index'))->assertOk();
        $this->actingAs($this->adminUser)->get(route('payrolls.index'))->assertOk();
        $this->actingAs($this->adminUser)->get(route('dashboard'))->assertSee('Company Personnel Directory');
    }
}
