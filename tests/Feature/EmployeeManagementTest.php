<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create([
            'email' => 'superadmin@hrms.com',
            'role' => 'superadmin',
        ]);

        Department::create(['name' => 'Engineering', 'code' => 'ENG', 'status' => 'Active']);
        Designation::create(['department_id' => 1, 'title' => 'Software Engineer', 'code' => 'SE', 'status' => 'Active']);
    }

    public function test_creating_employee_with_email_automatically_provisions_user_login(): void
    {
        $response = $this->actingAs($this->admin)->post(route('employees.store'), [
            'employee_name' => 'Chirag Limbachiya',
            'employee_code' => 'EMP-2026-0001',
            'department' => 'Engineering',
            'designation' => 'Software Engineer',
            'company_email' => 'chirag@hrms.com',
            'employment_type' => 'Full Time',
            'status' => 'Active',
            'salary_structure' => [
                'monthly_gross' => 50000,
            ],
        ]);

        $response->assertRedirect();
        $employee = Employee::where('company_email', 'chirag@hrms.com')->first();
        $this->assertNotNull($employee);
        $this->assertNotNull($employee->user_id);

        $user = User::where('email', 'chirag@hrms.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('employee', $user->role);
        $this->assertEquals('Chirag Limbachiya', $user->name);

        // Employee can authenticate with their email and default password 'password'
        auth()->logout();

        $loginResponse = $this->post(route('login'), [
            'email' => 'chirag@hrms.com',
            'password' => 'password',
        ]);

        $loginResponse->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticatedAs($user);
    }

    public function test_employee_directory_and_profile_show_portal_login_credentials(): void
    {
        $employee = Employee::create([
            'employee_name' => 'Daksh Patel',
            'employee_code' => 'EMP-2026-0002',
            'department' => 'Engineering',
            'designation' => 'Software Engineer',
            'company_email' => 'daksh@hrms.com',
            'employment_type' => 'Full Time',
            'status' => 'Active',
        ]);

        $user = User::create([
            'name' => 'Daksh Patel',
            'email' => 'daksh@hrms.com',
            'password' => bcrypt('password'),
            'role' => 'employee',
        ]);
        $employee->update(['user_id' => $user->id]);

        $indexResponse = $this->actingAs($this->admin)->get(route('employees.index'));
        $indexResponse->assertOk();
        $indexResponse->assertSee('daksh@hrms.com');
        $indexResponse->assertSee('Login Active');
        $indexResponse->assertSee('(Pass: password)');

        $showResponse = $this->actingAs($this->admin)->get(route('employees.show', $employee));
        $showResponse->assertOk();
        $showResponse->assertSee('Employee Portal Login Credentials');
        $showResponse->assertSee('Default Pass:');
        $showResponse->assertSee('password');
    }

    public function test_create_form_renders_with_searchable_dropdowns_and_option_lists(): void
    {
        $response = $this->actingAs($this->admin)->get(route('employees.create'));

        $response->assertOk();
        $response->assertSee('New Employee Joining & Master Data Form');
        $response->assertSee('reporting_person_name');
        $response->assertSee('work_location');
        $response->assertSee('branch');
        $response->assertSee('blood_group');
        $response->assertSee('-- Select Blood Group --');
        $response->assertSee('bank_details[bank_name]', false);
        $response->assertSee('address_details[communication][state]', false);
        $response->assertSee('address_details[permanent][state]', false);
        $response->assertSee('emergency_contact[relation]', false);
        $response->assertSee('reference_contact[relation]', false);
        $response->assertSee('nominee_details[relation]', false);
        $response->assertSee('nominee_details[occupation]', false);
        $response->assertSee('family_members[1][relation]', false);
        $response->assertSee('family_members[1][occupation]', false);
        $response->assertSee('asset_details[laptop_pc]', false);
        $response->assertSee('asset_details[mobile_sim]', false);
        $response->assertSee('HDFC Bank');
        $response->assertSee('Gujarat');
    }

    public function test_can_create_employee_with_dropdown_and_searchable_dropdown_values(): void
    {
        $response = $this->actingAs($this->admin)->post(route('employees.store'), [
            'employee_name' => 'Amit Sharma',
            'employee_code' => 'EMP-2026-0003',
            'department' => 'Engineering',
            'designation' => 'Software Engineer',
            'reporting_person_name' => 'CEO / Managing Director',
            'work_location' => 'Head Office',
            'branch' => 'Ahmedabad (Head Office)',
            'company_email' => 'amit@hrms.com',
            'employment_type' => 'Full Time',
            'status' => 'Active',
            'blood_group' => 'B+',
            'statutory_details' => [
                'verified_by' => 'Super Admin',
            ],
            'bank_details' => [
                'bank_name' => 'HDFC Bank',
                'account_number' => '50100123456789',
                'ifsc_code' => 'HDFC0001234',
                'account_type' => 'Savings',
                'verified_by' => 'Super Admin',
            ],
            'address_details' => [
                'communication' => [
                    'city' => 'Ahmedabad',
                    'state' => 'Gujarat',
                    'pincode' => '380015',
                ],
                'permanent' => [
                    'city' => 'Ahmedabad',
                    'state' => 'Gujarat',
                    'pincode' => '380015',
                ],
            ],
            'emergency_contact' => [
                'name' => 'Ramesh Sharma',
                'relation' => 'Father',
                'mobile' => '9876543210',
            ],
            'reference_contact' => [
                'name' => 'Vikas Patel',
                'relation' => 'Ex-Manager / Supervisor',
                'mobile' => '9876543211',
                'checked_by' => 'Super Admin',
            ],
            'nominee_details' => [
                'name' => 'Sunita Sharma',
                'relation' => 'Mother',
                'occupation' => 'Homemaker',
                'bank_name' => 'State Bank of India (SBI)',
                'verified_by' => 'Super Admin',
            ],
            'family_members' => [
                1 => [
                    'name' => 'Ramesh Sharma',
                    'relation' => 'Father',
                    'occupation' => 'Retired / Pensioner',
                    'checked_by' => 'Super Admin',
                ],
            ],
            'asset_details' => [
                'laptop_pc' => 'Dell Latitude 5420 / 5430',
                'mobile_sim' => 'Company SIM (Jio Postpaid)',
                'handover_by' => 'Super Admin',
            ],
        ]);

        $response->assertRedirect();
        $employee = Employee::where('employee_code', 'EMP-2026-0003')->first();
        $this->assertNotNull($employee);
        $this->assertEquals('B+', $employee->blood_group);
        $this->assertEquals('Head Office', $employee->work_location);
        $this->assertEquals('Ahmedabad (Head Office)', $employee->branch);
        $this->assertEquals('CEO / Managing Director', $employee->reporting_person_name);
        $this->assertEquals('HDFC Bank', $employee->bank_details['bank_name']);
        $this->assertEquals('Gujarat', $employee->address_details['communication']['state']);
        $this->assertEquals('Father', $employee->emergency_contact['relation']);
        $this->assertEquals('Dell Latitude 5420 / 5430', $employee->asset_details['laptop_pc']);
    }

    public function test_edit_form_renders_with_dropdowns_and_prefilled_employee_data(): void
    {
        $employee = Employee::create([
            'employee_name' => 'Kavita Dave',
            'employee_code' => 'EMP-2026-0004',
            'department' => 'Engineering',
            'designation' => 'Software Engineer',
            'work_location' => 'Remote / Work From Home',
            'branch' => 'Surat Branch',
            'blood_group' => 'O+',
            'bank_details' => ['bank_name' => 'ICICI Bank'],
            'address_details' => [
                'communication' => ['state' => 'Maharashtra'],
                'permanent' => ['state' => 'Maharashtra'],
            ],
            'status' => 'Active',
            'employment_type' => 'Full Time',
        ]);

        $response = $this->actingAs($this->admin)->get(route('employees.edit', $employee));

        $response->assertOk();
        $response->assertSee('Edit Employee Master Data');
        $response->assertSee('Remote / Work From Home');
        $response->assertSee('Surat Branch');
        $response->assertSee('ICICI Bank');
        $response->assertSee('Maharashtra');
    }
}
