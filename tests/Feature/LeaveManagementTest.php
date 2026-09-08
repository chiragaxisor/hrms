<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Employee $employee;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'role' => 'superadmin',
        ]);

        $this->employee = Employee::create([
            'employee_name' => 'John Doe',
            'employee_code' => 'EMP-0001',
            'department' => 'Engineering',
            'designation' => 'Software Engineer',
            'employment_type' => 'Full Time',
            'status' => 'Active',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_leave_index_screen_can_be_rendered(): void
    {
        $response = $this->actingAs($this->user)->get(route('leaves.index'));

        $response->assertOk();
        $response->assertSee('Leave Management & Approvals');
    }

    public function test_can_submit_leave_request(): void
    {
        $startDate = Carbon::today()->addDays(2)->toDateString();
        $endDate = Carbon::today()->addDays(3)->toDateString();

        $response = $this->actingAs($this->user)->post(route('leaves.store'), [
            'employee_id' => $this->employee->id,
            'leave_type' => 'Casual Leave',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'reason' => 'Family urgent work',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('leave_requests', [
            'employee_id' => $this->employee->id,
            'leave_type' => 'Casual Leave',
            'total_days' => 2.0,
            'status' => 'Pending',
        ]);
    }

    public function test_approving_leave_creates_attendance_records_with_leave_status(): void
    {
        $startDate = Carbon::today()->addDays(1)->toDateString();
        $endDate = Carbon::today()->addDays(2)->toDateString();

        $leave = LeaveRequest::create([
            'employee_id' => $this->employee->id,
            'leave_type' => 'Sick Leave',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_days' => 2.0,
            'reason' => 'Severe fever',
            'status' => 'Pending',
        ]);

        $response = $this->actingAs($this->user)->post(route('leaves.approve', $leave->id), [
            'admin_remarks' => 'Approved, take rest',
        ]);

        $response->assertRedirect();

        // Verify leave request status is updated
        $leave->refresh();
        $this->assertEquals('Approved', $leave->status);
        $this->assertEquals('Approved, take rest', $leave->admin_remarks);

        // Verify Attendance records for both days have status 'Leave'
        $this->assertDatabaseHas('attendances', [
            'employee_id' => $this->employee->id,
            'date' => $startDate,
            'status' => 'Leave',
            'total_hours' => 0.00,
        ]);

        $this->assertDatabaseHas('attendances', [
            'employee_id' => $this->employee->id,
            'date' => $endDate,
            'status' => 'Leave',
            'total_hours' => 0.00,
        ]);
    }

    public function test_approving_half_day_leave_creates_half_day_attendance_record(): void
    {
        $leaveDate = Carbon::today()->addDays(4)->toDateString();

        $leave = LeaveRequest::create([
            'employee_id' => $this->employee->id,
            'leave_type' => 'Casual Leave',
            'start_date' => $leaveDate,
            'end_date' => $leaveDate,
            'total_days' => 0.5,
            'is_half_day' => true,
            'half_day_type' => 'first_half',
            'reason' => 'Personal work in morning',
            'status' => 'Pending',
        ]);

        $response = $this->actingAs($this->user)->post(route('leaves.approve', $leave->id));

        $response->assertRedirect();

        $this->assertDatabaseHas('attendances', [
            'employee_id' => $this->employee->id,
            'date' => $leaveDate,
            'status' => 'Half Day',
            'total_hours' => 4.50,
        ]);
    }

    public function test_rejecting_leave_request(): void
    {
        $leave = LeaveRequest::create([
            'employee_id' => $this->employee->id,
            'leave_type' => 'Paid Leave',
            'start_date' => Carbon::today()->addDays(5)->toDateString(),
            'end_date' => Carbon::today()->addDays(6)->toDateString(),
            'total_days' => 2.0,
            'reason' => 'Vacation trip',
            'status' => 'Pending',
        ]);

        $response = $this->actingAs($this->user)->post(route('leaves.reject', $leave->id), [
            'admin_remarks' => 'Staff shortage due to audit',
        ]);

        $response->assertRedirect();
        $leave->refresh();
        $this->assertEquals('Rejected', $leave->status);
        $this->assertEquals('Staff shortage due to audit', $leave->admin_remarks);

        // Attendance records should NOT be created
        $this->assertDatabaseMissing('attendances', [
            'employee_id' => $this->employee->id,
            'date' => Carbon::today()->addDays(5)->toDateString(),
        ]);
    }
}
