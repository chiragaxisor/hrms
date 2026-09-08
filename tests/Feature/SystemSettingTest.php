<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SystemSettingTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'superadmin']);
    }

    public function test_authenticated_user_can_view_system_settings(): void
    {
        $response = $this->actingAs($this->user)->get(route('settings.index'));

        $response->assertOk();
        $response->assertSee('System Settings & Configurations', false);
        $response->assertSee('Company Profile', false);
        $response->assertSee('Attendance & Shift Policies', false);
    }

    public function test_user_can_update_company_settings(): void
    {
        $response = $this->actingAs($this->user)->post(route('settings.update'), [
            'tab' => 'company',
            'company_name' => 'Acme HRMS Tech Ltd',
            'company_email' => 'admin@acme-tech.com',
            'company_phone' => '+91 99999 88888',
            'company_website' => 'https://acme-tech.com',
            'company_tax_id' => 'GSTIN24AAACA0000A1Z5',
            'company_cin' => 'U12345GJ2026PTC000000',
            'company_address' => 'Floor 5, Tech Park, Surat, Gujarat',
            'timezone' => 'Asia/Kolkata',
            'currency_symbol' => '₹',
            'date_format' => 'd-M-Y',
        ]);

        $response->assertRedirect(route('settings.index', ['tab' => 'company']));
        $this->assertEquals('Acme HRMS Tech Ltd', Setting::get('company_name'));
        $this->assertEquals('admin@acme-tech.com', Setting::get('company_email'));
    }

    public function test_user_can_update_attendance_settings(): void
    {
        $response = $this->actingAs($this->user)->post(route('settings.update'), [
            'tab' => 'attendance',
            'office_start_time' => '10:00',
            'office_end_time' => '19:00',
            'grace_period_minutes' => 20,
            'standard_break_minutes' => 45,
            'full_day_hours' => 8.5,
            'half_day_hours' => 4.0,
            'auto_deduct_breaks' => '1',
        ]);

        $response->assertRedirect(route('settings.index', ['tab' => 'attendance']));
        $this->assertEquals('10:00', Setting::get('office_start_time'));
        $this->assertEquals(20, Setting::get('grace_period_minutes'));
        $this->assertTrue(Setting::get('auto_deduct_breaks'));
    }

    public function test_user_can_update_payroll_settings(): void
    {
        $response = $this->actingAs($this->user)->post(route('settings.update'), [
            'tab' => 'payroll',
            'basic_salary_percentage' => 50,
            'hra_salary_percentage' => 25,
            'allowance_salary_percentage' => 25,
            'default_pt_amount' => 200,
            'pf_employee_percent' => 12,
            'pf_employer_percent' => 12,
            'salary_disbursement_day' => 10,
        ]);

        $response->assertRedirect(route('settings.index', ['tab' => 'payroll']));
        $this->assertEquals('200', Setting::get('default_pt_amount'));
        $this->assertEquals('10', Setting::get('salary_disbursement_day'));
    }

    public function test_user_can_view_security_tab_and_change_password(): void
    {
        $viewResponse = $this->actingAs($this->user)->get(route('settings.index', ['tab' => 'security']));
        $viewResponse->assertOk();
        $viewResponse->assertSee('Change Account Password');
        $viewResponse->assertSee('Current Password');
        $viewResponse->assertSee('New Password');

        $updateResponse = $this->actingAs($this->user)->put(route('password.update'), [
            'current_password' => 'password',
            'password' => 'NewSecurePassword123!',
            'password_confirmation' => 'NewSecurePassword123!',
        ]);

        $updateResponse->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('NewSecurePassword123!', $this->user->fresh()->password));
    }
}
