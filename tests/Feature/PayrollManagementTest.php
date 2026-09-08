<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\SalaryStructure;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayrollManagementTest extends TestCase
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
            'employee_name' => 'Chirag Limbachiya',
            'employee_code' => 'EMP-2026-0001',
            'department' => 'Engineering',
            'designation' => 'Tech Lead',
            'employment_type' => 'Full Time',
            'status' => 'Active',
            'user_id' => $this->user->id,
            'bank_details' => [
                'bank_name' => 'HDFC Bank',
                'account_number' => '50100234567890',
                'ifsc_code' => 'HDFC0001234',
            ],
        ]);
    }

    public function test_payroll_index_screen_can_be_rendered(): void
    {
        $response = $this->actingAs($this->user)->get(route('payrolls.index'));

        $response->assertOk();
        $response->assertSee('Payroll Cycle');
    }

    public function test_can_configure_employee_salary_structure(): void
    {
        $response = $this->actingAs($this->user)->post(route('payrolls.salary-structure'), [
            'employee_id' => $this->employee->id,
            'monthly_gross' => 50000,
            'basic_salary' => 25000,
            'hra' => 12500,
            'special_allowance' => 12500,
            'pf_deduction' => 1800,
            'professional_tax' => 200,
            'tds' => 1000,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('salary_structures', [
            'employee_id' => $this->employee->id,
            'monthly_gross' => 50000.00,
            'basic_salary' => 25000.00,
            'ctc_annual' => 600000.00,
            'pf_deduction' => 1800.00,
        ]);
    }

    public function test_can_generate_monthly_payroll_with_attendance_factors(): void
    {
        // 1. Setup salary structure
        SalaryStructure::create([
            'employee_id' => $this->employee->id,
            'monthly_gross' => 60000.00,
            'basic_salary' => 30000.00,
            'hra' => 15000.00,
            'special_allowance' => 15000.00,
            'pf_deduction' => 1800.00,
            'professional_tax' => 200.00,
        ]);

        // 2. Add attendance for 2 days in September 2026
        Attendance::create([
            'employee_id' => $this->employee->id,
            'date' => '2026-09-01',
            'status' => 'Present',
            'total_hours' => 9.00,
        ]);
        Attendance::create([
            'employee_id' => $this->employee->id,
            'date' => '2026-09-02',
            'status' => 'Leave',
            'total_hours' => 0.00,
            'remarks' => 'Approved Leave',
        ]);

        // 3. Trigger payroll generation for September 2026
        $response = $this->actingAs($this->user)->post(route('payrolls.generate'), [
            'month' => 9,
            'year' => 2026,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('payrolls', [
            'employee_id' => $this->employee->id,
            'month' => 9,
            'year' => 2026,
            'gross_salary' => 60000.00,
            'payment_status' => 'Pending',
        ]);

        $payroll = Payroll::where('employee_id', $this->employee->id)->where('month', 9)->first();
        $this->assertNotNull($payroll);
        $this->assertGreaterThan(0, $payroll->net_salary);
    }

    public function test_can_record_payout_as_paid_with_transaction_details(): void
    {
        $payroll = Payroll::create([
            'employee_id' => $this->employee->id,
            'month' => 9,
            'year' => 2026,
            'payroll_code' => 'PAY-202609-EMP0001',
            'gross_salary' => 50000.00,
            'total_earnings' => 50000.00,
            'total_deductions' => 2000.00,
            'net_salary' => 48000.00,
            'payment_status' => 'Pending',
        ]);

        $response = $this->actingAs($this->user)->post(route('payrolls.payout', $payroll->id), [
            'payment_status' => 'Paid',
            'payment_mode' => 'Bank Transfer',
            'transaction_reference' => 'UTR998877665544',
            'paid_at' => Carbon::today()->toDateString(),
            'remarks' => 'HDFC batch payout successful',
        ]);

        $response->assertRedirect();
        $payroll->refresh();
        $this->assertEquals('Paid', $payroll->payment_status);
        $this->assertEquals('UTR998877665544', $payroll->transaction_reference);
        $this->assertNotNull($payroll->paid_at);
    }

    public function test_payslip_screen_can_be_rendered(): void
    {
        $payroll = Payroll::create([
            'employee_id' => $this->employee->id,
            'month' => 9,
            'year' => 2026,
            'payroll_code' => 'PAY-202609-EMP0001',
            'gross_salary' => 50000.00,
            'total_earnings' => 50000.00,
            'total_deductions' => 2000.00,
            'net_salary' => 48000.00,
            'payment_status' => 'Paid',
        ]);

        $response = $this->actingAs($this->user)->get(route('payrolls.payslip', $payroll->id));

        $response->assertOk();
        $response->assertSee('Chirag Limbachiya');
        $response->assertSee('PAY-202609-EMP0001');
        $response->assertSee('Salary Slip');
    }
}
