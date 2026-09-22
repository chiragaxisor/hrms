<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Superadmin
        User::updateOrCreate(
            ['email' => 'superadmin@hrms.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
                'role' => 'superadmin',
                'email_verified_at' => now(),
            ]
        );

        // Create HR Admin
        User::updateOrCreate(
            ['email' => 'hr@hrms.com'],
            [
                'name' => 'HR Manager',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Seed Master Departments & Designations
        $this->call(DepartmentSeeder::class);

        // Seed HRMS Company & System Settings
        $settings = [
            // Company
            ['key' => 'company_name', 'value' => 'HRMS', 'group' => 'company', 'type' => 'string'],
            ['key' => 'company_email', 'value' => 'contact@hrms.com', 'group' => 'company', 'type' => 'string'],
            ['key' => 'company_phone', 'value' => '+91 98765 43210', 'group' => 'company', 'type' => 'string'],
            ['key' => 'company_website', 'value' => 'https://hrms.com', 'group' => 'company', 'type' => 'string'],
            ['key' => 'company_tax_id', 'value' => 'GSTIN24ABCDE1234F1Z5', 'group' => 'company', 'type' => 'string'],
            ['key' => 'company_cin', 'value' => 'U72900GJ2026PTC123456', 'group' => 'company', 'type' => 'string'],
            ['key' => 'company_address', 'value' => 'Ahmedabad, Gujarat, India', 'group' => 'company', 'type' => 'string'],
            ['key' => 'timezone', 'value' => 'Asia/Kolkata', 'group' => 'company', 'type' => 'string'],
            ['key' => 'currency_symbol', 'value' => '₹', 'group' => 'company', 'type' => 'string'],
            ['key' => 'date_format', 'value' => 'd-M-Y', 'group' => 'company', 'type' => 'string'],

            // Attendance & Geofence
            ['key' => 'office_start_time', 'value' => '09:30', 'group' => 'attendance', 'type' => 'string'],
            ['key' => 'office_end_time', 'value' => '18:30', 'group' => 'attendance', 'type' => 'string'],
            ['key' => 'half_day_hours', 'value' => '4.5', 'group' => 'attendance', 'type' => 'string'],
            ['key' => 'full_day_hours', 'value' => '8.0', 'group' => 'attendance', 'type' => 'string'],
            ['key' => 'grace_period_minutes', 'value' => '15', 'group' => 'attendance', 'type' => 'integer'],
            ['key' => 'standard_break_minutes', 'value' => '60', 'group' => 'attendance', 'type' => 'integer'],
            ['key' => 'auto_deduct_breaks', 'value' => '1', 'group' => 'attendance', 'type' => 'boolean'],
            ['key' => 'office_latitude', 'value' => '23.0225', 'group' => 'attendance', 'type' => 'string'],
            ['key' => 'office_longitude', 'value' => '72.5714', 'group' => 'attendance', 'type' => 'string'],
            ['key' => 'geofence_radius_meters', 'value' => '250', 'group' => 'attendance', 'type' => 'integer'],
            ['key' => 'require_geofence', 'value' => '1', 'group' => 'attendance', 'type' => 'boolean'],
            ['key' => 'require_camera_punch', 'value' => '1', 'group' => 'attendance', 'type' => 'boolean'],

            // Payroll
            ['key' => 'default_pt_amount', 'value' => '200', 'group' => 'payroll', 'type' => 'string'],
            ['key' => 'pf_employee_percent', 'value' => '12', 'group' => 'payroll', 'type' => 'string'],
            ['key' => 'pf_employer_percent', 'value' => '12', 'group' => 'payroll', 'type' => 'string'],
            ['key' => 'salary_disbursement_day', 'value' => '7', 'group' => 'payroll', 'type' => 'integer'],
            ['key' => 'basic_salary_percentage', 'value' => '50', 'group' => 'payroll', 'type' => 'string'],
            ['key' => 'hra_salary_percentage', 'value' => '25', 'group' => 'payroll', 'type' => 'string'],
            ['key' => 'allowance_salary_percentage', 'value' => '25', 'group' => 'payroll', 'type' => 'string'],
        ];

        foreach ($settings as $s) {
            Setting::updateOrCreate(
                ['key' => $s['key']],
                [
                    'value' => $s['value'],
                    'group' => $s['group'],
                    'type' => $s['type'],
                ]
            );
        }
    }
}
