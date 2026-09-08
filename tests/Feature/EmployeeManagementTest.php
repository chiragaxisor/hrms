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
}
