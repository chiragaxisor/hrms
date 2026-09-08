<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SystemSettingController extends Controller
{
    /**
     * Display all system settings organized by tabs.
     */
    public function index(Request $request): View
    {
        $activeTab = $request->input('tab', 'company'); // company, attendance, payroll, notifications

        // Default system configurations if not yet seeded
        $defaults = [
            // Company Profile
            'company_name' => 'HRMS Enterprise Inc.',
            'company_email' => 'contact@hrms-enterprise.com',
            'company_phone' => '+91 98765 43210',
            'company_website' => 'https://hrms-enterprise.com',
            'company_tax_id' => 'GSTIN24ABCDE1234F1Z5',
            'company_cin' => 'U72900GJ2026PTC123456',
            'company_address' => '401, Sapphire Corporate Park, S.G. Highway, Ahmedabad, Gujarat - 380015',
            'timezone' => 'Asia/Kolkata',
            'currency_symbol' => '₹',
            'date_format' => 'd-M-Y',

            // Attendance & Shift Configuration
            'office_start_time' => '09:30',
            'office_end_time' => '18:30',
            'half_day_hours' => '4.5',
            'full_day_hours' => '8.0',
            'grace_period_minutes' => '15',
            'auto_deduct_breaks' => '1',
            'standard_break_minutes' => '60',
            'office_latitude' => '23.0225',
            'office_longitude' => '72.5714',
            'geofence_radius_meters' => '250',
            'require_geofence' => '1',
            'require_camera_punch' => '1',

            // Payroll & Statutory Configuration
            'default_pt_amount' => '200',
            'pf_employee_percent' => '12',
            'pf_employer_percent' => '12',
            'salary_disbursement_day' => '7',
            'basic_salary_percentage' => '50',
            'hra_salary_percentage' => '25',
            'allowance_salary_percentage' => '25',

            // Notification / Alert Preferences
            'notify_leave_applied' => '1',
            'notify_attendance_absent' => '1',
            'notify_payroll_generated' => '1',
            'daily_work_report_target_calls' => '50',
        ];

        $settings = [];
        foreach ($defaults as $key => $defaultVal) {
            $settings[$key] = Setting::get($key, $defaultVal);
        }

        return view('settings.index', compact('settings', 'activeTab'));
    }

    /**
     * Update settings based on tab.
     */
    public function update(Request $request): RedirectResponse
    {
        $tab = $request->input('tab', 'company');

        if ($tab === 'company') {
            $validated = $request->validate([
                'company_name' => 'required|string|max:255',
                'company_email' => 'required|email|max:255',
                'company_phone' => 'nullable|string|max:50',
                'company_website' => 'nullable|url|max:255',
                'company_tax_id' => 'nullable|string|max:50',
                'company_cin' => 'nullable|string|max:50',
                'company_address' => 'nullable|string|max:500',
                'timezone' => 'required|string|max:50',
                'currency_symbol' => 'required|string|max:10',
                'date_format' => 'required|string|max:20',
            ]);

            foreach ($validated as $key => $value) {
                Setting::set($key, $value, 'company', 'string');
            }
        } elseif ($tab === 'attendance') {
            $validated = $request->validate([
                'office_start_time' => ['required', 'regex:/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/'],
                'office_end_time' => ['required', 'regex:/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/'],
                'half_day_hours' => 'required|numeric|min:1|max:12',
                'full_day_hours' => 'required|numeric|min:1|max:16',
                'grace_period_minutes' => 'required|integer|min:0|max:120',
                'standard_break_minutes' => 'required|integer|min:0|max:180',
                'office_latitude' => 'nullable|numeric|between:-90,90',
                'office_longitude' => 'nullable|numeric|between:-180,180',
                'geofence_radius_meters' => 'nullable|integer|min:10|max:50000',
            ]);

            foreach ($validated as $key => $value) {
                if ($value !== null) {
                    Setting::set($key, (string) $value, 'attendance', 'string');
                }
            }

            Setting::set('auto_deduct_breaks', $request->has('auto_deduct_breaks') ? '1' : '0', 'attendance', 'boolean');
            Setting::set('require_geofence', $request->has('require_geofence') ? '1' : '0', 'attendance', 'boolean');
            Setting::set('require_camera_punch', $request->has('require_camera_punch') ? '1' : '0', 'attendance', 'boolean');
        } elseif ($tab === 'payroll') {
            $validated = $request->validate([
                'default_pt_amount' => 'required|numeric|min:0',
                'pf_employee_percent' => 'required|numeric|min:0|max:50',
                'pf_employer_percent' => 'required|numeric|min:0|max:50',
                'salary_disbursement_day' => 'required|integer|min:1|max:31',
                'basic_salary_percentage' => 'required|numeric|min:10|max:100',
                'hra_salary_percentage' => 'required|numeric|min:0|max:50',
                'allowance_salary_percentage' => 'required|numeric|min:0|max:50',
            ]);

            foreach ($validated as $key => $value) {
                Setting::set($key, $value, 'payroll', 'string');
            }
        } elseif ($tab === 'notifications') {
            $validated = $request->validate([
                'daily_work_report_target_calls' => 'required|integer|min:1|max:500',
            ]);

            Setting::set('daily_work_report_target_calls', $validated['daily_work_report_target_calls'], 'notifications', 'integer');
            Setting::set('notify_leave_applied', $request->has('notify_leave_applied') ? '1' : '0', 'notifications', 'boolean');
            Setting::set('notify_attendance_absent', $request->has('notify_attendance_absent') ? '1' : '0', 'notifications', 'boolean');
            Setting::set('notify_payroll_generated', $request->has('notify_payroll_generated') ? '1' : '0', 'notifications', 'boolean');
        }

        return redirect()->route('settings.index', ['tab' => $tab])
            ->with('success', 'System settings saved and updated successfully!');
    }
}
