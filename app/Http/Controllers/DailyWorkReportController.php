<?php

namespace App\Http\Controllers;

use App\Models\DailyWorkReport;
use App\Models\Employee;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DailyWorkReportController extends Controller
{
    /**
     * Base default system fields configuration.
     *
     * @return array<string, array<string, mixed>>
     */
    protected function getDefaultFieldDefinitions(): array
    {
        return [
            'calls' => [
                'key' => 'calls',
                'label' => 'Calls',
                'type' => 'number',
                'is_system' => true,
                'is_social' => false,
                'bg_class' => 'bg-blue-50/60 text-blue-900',
                'min_width' => 'min-w-[60px]',
            ],
            'instagram_dms' => [
                'key' => 'instagram_dms',
                'label' => 'Instagram DMs',
                'type' => 'number',
                'is_system' => true,
                'is_social' => true,
                'bg_class' => '',
                'min_width' => 'min-w-[80px]',
            ],
            'facebook_dms' => [
                'key' => 'facebook_dms',
                'label' => 'Facebook DMs',
                'type' => 'number',
                'is_system' => true,
                'is_social' => true,
                'bg_class' => '',
                'min_width' => 'min-w-[80px]',
            ],
            'linkedin_dms' => [
                'key' => 'linkedin_dms',
                'label' => 'LinkedIn DMs',
                'type' => 'number',
                'is_system' => true,
                'is_social' => true,
                'bg_class' => '',
                'min_width' => 'min-w-[80px]',
            ],
            'whatsapp_dms' => [
                'key' => 'whatsapp_dms',
                'label' => 'WhatsApp DMs',
                'type' => 'number',
                'is_system' => true,
                'is_social' => true,
                'bg_class' => '',
                'min_width' => 'min-w-[80px]',
            ],
            'linkedin_posts' => [
                'key' => 'linkedin_posts',
                'label' => 'LinkedIn Posts',
                'type' => 'number',
                'is_system' => true,
                'is_social' => true,
                'bg_class' => 'bg-sky-50/60 text-sky-900',
                'min_width' => 'min-w-[80px]',
            ],
            'instagram_posts' => [
                'key' => 'instagram_posts',
                'label' => 'Instagram Posts',
                'type' => 'number',
                'is_system' => true,
                'is_social' => true,
                'bg_class' => 'bg-purple-50/60 text-purple-900',
                'min_width' => 'min-w-[80px]',
            ],
            'facebook_group_actions' => [
                'key' => 'facebook_group_actions',
                'label' => 'Facebook Group Actions',
                'type' => 'number',
                'is_system' => true,
                'is_social' => true,
                'bg_class' => '',
                'min_width' => 'min-w-[90px]',
            ],
            'facebook_group_dms' => [
                'key' => 'facebook_group_dms',
                'label' => 'Facebook Group DMs',
                'type' => 'number',
                'is_system' => true,
                'is_social' => true,
                'bg_class' => '',
                'min_width' => 'min-w-[90px]',
            ],
            'linkedin_connections' => [
                'key' => 'linkedin_connections',
                'label' => 'LinkedIn Connections',
                'type' => 'number',
                'is_system' => true,
                'is_social' => true,
                'bg_class' => 'bg-indigo-50/60 text-indigo-900',
                'min_width' => 'min-w-[85px]',
            ],
        ];
    }

    /**
     * Get system fields with any user custom overrides (title, disabled state, etc.).
     *
     * @return array<string, array<string, mixed>>
     */
    protected function getEffectiveSystemFields(): array
    {
        $defaults = $this->getDefaultFieldDefinitions();
        $overrides = Setting::get('dwr_system_field_overrides', []);

        if (! is_array($overrides)) {
            $overrides = [];
        }

        foreach ($defaults as $key => &$field) {
            $field['enabled'] = true;
            if (isset($overrides[$key])) {
                if (isset($overrides[$key]['label']) && filled($overrides[$key]['label'])) {
                    $field['label'] = trim($overrides[$key]['label']);
                }
                if (isset($overrides[$key]['enabled'])) {
                    $field['enabled'] = (bool) $overrides[$key]['enabled'];
                }
            }
        }

        return $defaults;
    }

    /**
     * Get active custom field definitions for Daily Work Reports.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getCustomFieldDefinitions(): array
    {
        $fields = Setting::get('dwr_custom_fields', []);

        if (! is_array($fields)) {
            return [];
        }

        foreach ($fields as &$f) {
            if (! isset($f['enabled'])) {
                $f['enabled'] = true;
            }
            $f['is_system'] = false;
        }

        return $fields;
    }

    /**
     * Display the Daily Work Report sheet view and KPI summary tab.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $isEmployee = ! $user->isAdmin();
        $myEmployee = $user->employee;

        if ($isEmployee) {
            $employees = $myEmployee ? collect([$myEmployee]) : collect();
            $selectedEmployeeId = $myEmployee?->id;
            $selectedEmployee = $myEmployee;
        } else {
            $employees = Employee::orderBy('employee_name')->get();
            // Default to selected employee or first employee
            $selectedEmployeeId = $request->input('employee_id');
            if (! $selectedEmployeeId && $employees->isNotEmpty()) {
                $selectedEmployeeId = $employees->first()->id;
            }
            $selectedEmployee = $employees->firstWhere('id', $selectedEmployeeId);
        }

        // Selected month (format Y-m, default current month or e.g. August/September 2026)
        $month = $request->input('month', Carbon::now()->format('Y-m'));
        $activeTab = $request->input('tab', 'sheet'); // 'sheet' or 'kpi'

        $startOfMonth = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endOfMonth = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        // Retrieve system fields (with enabled state) & dynamic custom fields
        $systemFields = $this->getEffectiveSystemFields();
        $customFields = $this->getCustomFieldDefinitions();

        // Fetch existing daily reports for this employee & month
        $reports = collect();
        if ($selectedEmployee) {
            $reports = DailyWorkReport::where('employee_id', $selectedEmployee->id)
                ->whereBetween('report_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
                ->orderBy('report_date', 'asc')
                ->get()
                ->keyBy(fn ($item) => $item->report_date->format('Y-m-d'));
        }

        // Generate full daily rows for the month
        $daysInMonth = [];
        $totals = [
            'calls' => 0,
            'instagram_dms' => 0,
            'facebook_dms' => 0,
            'linkedin_dms' => 0,
            'whatsapp_dms' => 0,
            'linkedin_posts' => 0,
            'instagram_posts' => 0,
            'facebook_group_actions' => 0,
            'facebook_group_dms' => 0,
            'linkedin_connections' => 0,
            'social_activities' => 0,
            'logged_days_count' => 0,
            'custom_fields' => [],
        ];

        foreach ($customFields as $field) {
            $totals['custom_fields'][$field['key']] = 0;
        }

        $currentDate = $startOfMonth->copy();
        while ($currentDate->lte($endOfMonth)) {
            $dateString = $currentDate->format('Y-m-d');
            $report = $reports->get($dateString);

            $hasReport = ! is_null($report);
            $calls = $report?->calls ?? 0;
            $instagramDms = $report?->instagram_dms ?? 0;
            $facebookDms = $report?->facebook_dms ?? 0;
            $linkedinDms = $report?->linkedin_dms ?? 0;
            $whatsappDms = $report?->whatsapp_dms ?? 0;
            $linkedinPosts = $report?->linkedin_posts ?? 0;
            $instagramPosts = $report?->instagram_posts ?? 0;
            $facebookGroupActions = $report?->facebook_group_actions ?? 0;
            $facebookGroupDms = $report?->facebook_group_dms ?? 0;
            $linkedinConnections = $report?->linkedin_connections ?? 0;
            $remarks = $report?->remarks ?? ($currentDate->isSunday() ? 'Sunday' : '');

            // Calculate daily social outreach taking into account only enabled fields
            $dailySocial = 0;
            if ($systemFields['instagram_dms']['enabled']) {
                $dailySocial += $instagramDms;
            }
            if ($systemFields['facebook_dms']['enabled']) {
                $dailySocial += $facebookDms;
            }
            if ($systemFields['linkedin_dms']['enabled']) {
                $dailySocial += $linkedinDms;
            }
            if ($systemFields['whatsapp_dms']['enabled']) {
                $dailySocial += $whatsappDms;
            }
            if ($systemFields['linkedin_posts']['enabled']) {
                $dailySocial += $linkedinPosts;
            }
            if ($systemFields['instagram_posts']['enabled']) {
                $dailySocial += $instagramPosts;
            }
            if ($systemFields['facebook_group_actions']['enabled']) {
                $dailySocial += $facebookGroupActions;
            }
            if ($systemFields['facebook_group_dms']['enabled']) {
                $dailySocial += $facebookGroupDms;
            }
            if ($systemFields['linkedin_connections']['enabled']) {
                $dailySocial += $linkedinConnections;
            }

            $dayCustomFields = [];
            $savedCustomValues = is_array($report?->custom_fields) ? $report->custom_fields : [];
            foreach ($customFields as $field) {
                $val = $savedCustomValues[$field['key']] ?? ($field['type'] === 'number' ? 0 : '');
                $dayCustomFields[$field['key']] = $val;
                if ($field['type'] === 'number') {
                    $totals['custom_fields'][$field['key']] += (int) $val;
                    if (! empty($field['is_social']) && ! empty($field['enabled'])) {
                        $dailySocial += (int) $val;
                    }
                }
            }

            if ($hasReport) {
                $totals['logged_days_count']++;
            }

            $totals['calls'] += $calls;
            $totals['instagram_dms'] += $instagramDms;
            $totals['facebook_dms'] += $facebookDms;
            $totals['linkedin_dms'] += $linkedinDms;
            $totals['whatsapp_dms'] += $whatsappDms;
            $totals['linkedin_posts'] += $linkedinPosts;
            $totals['instagram_posts'] += $instagramPosts;
            $totals['facebook_group_actions'] += $facebookGroupActions;
            $totals['facebook_group_dms'] += $facebookGroupDms;
            $totals['linkedin_connections'] += $linkedinConnections;
            $totals['social_activities'] += $dailySocial;

            $daysInMonth[] = [
                'date' => $currentDate->copy(),
                'date_string' => $dateString,
                'formatted_date' => $currentDate->format('j-M-Y'),
                'is_sunday' => $currentDate->isSunday(),
                'has_report' => $hasReport,
                'report' => $report,
                'calls' => $calls,
                'instagram_dms' => $instagramDms,
                'facebook_dms' => $facebookDms,
                'linkedin_dms' => $linkedinDms,
                'whatsapp_dms' => $whatsappDms,
                'linkedin_posts' => $linkedinPosts,
                'instagram_posts' => $instagramPosts,
                'facebook_group_actions' => $facebookGroupActions,
                'facebook_group_dms' => $facebookGroupDms,
                'linkedin_connections' => $linkedinConnections,
                'remarks' => $remarks,
                'social_activities' => $dailySocial,
                'custom_fields' => $dayCustomFields,
            ];

            $currentDate->addDay();
        }

        // Calculate KPI Summary Metrics
        $totalWorkingDays = collect($daysInMonth)->filter(fn ($day) => ! $day['is_sunday'])->count();
        $activeReportedDays = $totals['logged_days_count'] > 0 ? $totals['logged_days_count'] : 1;

        $avgCallsPerDay = round($totals['calls'] / $activeReportedDays, 1);
        $avgSocialPerDay = round($totals['social_activities'] / $activeReportedDays, 1);
        $totalPosts = ($systemFields['linkedin_posts']['enabled'] ? $totals['linkedin_posts'] : 0) + ($systemFields['instagram_posts']['enabled'] ? $totals['instagram_posts'] : 0);
        $totalDMs = ($systemFields['instagram_dms']['enabled'] ? $totals['instagram_dms'] : 0)
            + ($systemFields['facebook_dms']['enabled'] ? $totals['facebook_dms'] : 0)
            + ($systemFields['linkedin_dms']['enabled'] ? $totals['linkedin_dms'] : 0)
            + ($systemFields['whatsapp_dms']['enabled'] ? $totals['whatsapp_dms'] : 0)
            + ($systemFields['facebook_group_dms']['enabled'] ? $totals['facebook_group_dms'] : 0);

        // Targets (e.g. 50 calls/day benchmark)
        $targetCallsPerDay = 50;
        $monthlyTargetCalls = $totalWorkingDays * $targetCallsPerDay;
        $callAchievementRate = $monthlyTargetCalls > 0 ? min(100, round(($totals['calls'] / $monthlyTargetCalls) * 100, 1)) : 0;

        $kpiData = [
            'total_working_days' => $totalWorkingDays,
            'logged_days_count' => $totals['logged_days_count'],
            'avg_calls_per_day' => $avgCallsPerDay,
            'avg_social_per_day' => $avgSocialPerDay,
            'total_posts' => $totalPosts,
            'total_dms' => $totalDMs,
            'target_calls_per_day' => $targetCallsPerDay,
            'monthly_target_calls' => $monthlyTargetCalls,
            'call_achievement_rate' => $callAchievementRate,
            'consistency_rate' => $totalWorkingDays > 0 ? min(100, round(($totals['logged_days_count'] / $totalWorkingDays) * 100, 1)) : 0,
        ];

        return view('work_reports.index', compact(
            'employees',
            'selectedEmployee',
            'month',
            'activeTab',
            'daysInMonth',
            'totals',
            'kpiData',
            'customFields',
            'systemFields'
        ));
    }

    /**
     * Store or update a daily work report entry.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'report_date' => 'required|date',
            'calls' => 'nullable|integer|min:0',
            'instagram_dms' => 'nullable|integer|min:0',
            'facebook_dms' => 'nullable|integer|min:0',
            'linkedin_dms' => 'nullable|integer|min:0',
            'whatsapp_dms' => 'nullable|integer|min:0',
            'linkedin_posts' => 'nullable|integer|min:0',
            'instagram_posts' => 'nullable|integer|min:0',
            'facebook_group_actions' => 'nullable|integer|min:0',
            'facebook_group_dms' => 'nullable|integer|min:0',
            'linkedin_connections' => 'nullable|integer|min:0',
            'remarks' => 'nullable|string|max:1000',
            'custom_fields' => 'nullable|array',
        ]);

        $user = $request->user();
        $targetEmployeeId = (int) $validated['employee_id'];

        if (! $user->isAdmin()) {
            if (! $user->employee || $user->employee->id !== $targetEmployeeId) {
                abort(403, 'You can only log daily work reports for your own employee account.');
            }
        }

        $reportDate = Carbon::parse($validated['report_date'])->format('Y-m-d');

        $existingReport = DailyWorkReport::where('employee_id', $targetEmployeeId)
            ->where('report_date', $reportDate)
            ->first();

        // If report exists and employee is attempting to edit it, restrict to Superadmin
        if ($existingReport && ! $user->isSuperAdmin()) {
            abort(403, 'Unauthorized. Existing daily work reports can only be edited by Superadmin.');
        }

        // Sanitize dynamic custom field inputs based on definition types
        $customFieldDefs = $this->getCustomFieldDefinitions();
        $processedCustomFields = [];
        $rawCustomFields = $request->input('custom_fields', []);

        foreach ($customFieldDefs as $field) {
            $key = $field['key'];
            if (isset($rawCustomFields[$key])) {
                if ($field['type'] === 'number') {
                    $processedCustomFields[$key] = is_numeric($rawCustomFields[$key]) ? (int) $rawCustomFields[$key] : 0;
                } else {
                    $processedCustomFields[$key] = (string) $rawCustomFields[$key];
                }
            } else {
                $processedCustomFields[$key] = $field['type'] === 'number' ? 0 : '';
            }
        }

        DailyWorkReport::updateOrCreate(
            [
                'employee_id' => $targetEmployeeId,
                'report_date' => $reportDate,
            ],
            [
                'calls' => $validated['calls'] ?? 0,
                'instagram_dms' => $validated['instagram_dms'] ?? 0,
                'facebook_dms' => $validated['facebook_dms'] ?? 0,
                'linkedin_dms' => $validated['linkedin_dms'] ?? 0,
                'whatsapp_dms' => $validated['whatsapp_dms'] ?? 0,
                'linkedin_posts' => $validated['linkedin_posts'] ?? 0,
                'instagram_posts' => $validated['instagram_posts'] ?? 0,
                'facebook_group_actions' => $validated['facebook_group_actions'] ?? 0,
                'facebook_group_dms' => $validated['facebook_group_dms'] ?? 0,
                'linkedin_connections' => $validated['linkedin_connections'] ?? 0,
                'remarks' => $validated['remarks'] ?? null,
                'custom_fields' => $processedCustomFields,
            ]
        );

        $month = Carbon::parse($validated['report_date'])->format('Y-m');

        return redirect()->route('work-reports.index', [
            'employee_id' => $targetEmployeeId,
            'month' => $month,
            'tab' => $request->input('tab', 'sheet'),
        ])->with('success', 'Daily Work Report entry saved successfully!');
    }

    /**
     * Store a new custom field definition (Superadmin only).
     */
    public function storeCustomField(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! $user->isSuperAdmin()) {
            abort(403, 'Unauthorized. Only Superadmin can manage custom fields.');
        }

        $validated = $request->validate([
            'label' => 'required|string|max:50',
            'type' => 'required|in:number,text',
            'is_social' => 'nullable|boolean',
        ]);

        $key = Str::slug($validated['label'], '_');
        if (empty($key)) {
            return back()->withErrors(['label' => 'Invalid field label.']);
        }

        // Prevent collisions with fixed database columns
        $reservedKeys = [
            'calls', 'instagram_dms', 'facebook_dms', 'linkedin_dms', 'whatsapp_dms',
            'linkedin_posts', 'instagram_posts', 'facebook_group_actions', 'facebook_group_dms',
            'linkedin_connections', 'remarks', 'date', 'id', 'employee_id', 'report_date',
        ];

        if (in_array($key, $reservedKeys, true)) {
            return back()->withErrors(['label' => 'This field name is reserved. Please choose another label.']);
        }

        $existingFields = $this->getCustomFieldDefinitions();

        // Check if key already exists
        foreach ($existingFields as $f) {
            if ($f['key'] === $key) {
                return back()->withErrors(['label' => 'A field with this name already exists.']);
            }
        }

        $newField = [
            'key' => $key,
            'label' => trim($validated['label']),
            'type' => $validated['type'],
            'is_social' => $request->boolean('is_social'),
            'enabled' => true,
        ];

        $existingFields[] = $newField;

        Setting::set('dwr_custom_fields', $existingFields, 'work_reports', 'json');

        return back()->with('success', "Custom field '{$validated['label']}' added successfully!");
    }

    /**
     * Update an existing field definition (default system field or custom field) (Superadmin only).
     */
    public function updateCustomField(Request $request, string $key): RedirectResponse
    {
        $user = $request->user();
        if (! $user->isSuperAdmin()) {
            abort(403, 'Unauthorized. Only Superadmin can manage custom fields.');
        }

        $validated = $request->validate([
            'label' => 'required|string|max:50',
            'type' => 'nullable|in:number,text',
            'is_social' => 'nullable|boolean',
        ]);

        $defaults = $this->getDefaultFieldDefinitions();

        // If it's a default system field
        if (isset($defaults[$key])) {
            $overrides = Setting::get('dwr_system_field_overrides', []);
            if (! is_array($overrides)) {
                $overrides = [];
            }
            if (! isset($overrides[$key])) {
                $overrides[$key] = [];
            }
            $overrides[$key]['label'] = trim($validated['label']);

            Setting::set('dwr_system_field_overrides', $overrides, 'work_reports', 'json');

            return back()->with('success', "Field '{$validated['label']}' updated successfully!");
        }

        // Otherwise custom field
        $existingFields = $this->getCustomFieldDefinitions();
        $fieldFound = false;

        foreach ($existingFields as &$field) {
            if ($field['key'] === $key) {
                $field['label'] = trim($validated['label']);
                if (isset($validated['type'])) {
                    $field['type'] = $validated['type'];
                }
                $field['is_social'] = $request->boolean('is_social');
                $fieldFound = true;
                break;
            }
        }

        if (! $fieldFound) {
            return back()->withErrors(['label' => 'Field not found.']);
        }

        Setting::set('dwr_custom_fields', $existingFields, 'work_reports', 'json');

        return back()->with('success', "Custom field '{$validated['label']}' updated successfully!");
    }

    /**
     * Toggle visibility (Active/Hidden) of a system or custom field (Superadmin only).
     */
    public function toggleField(Request $request, string $key): RedirectResponse
    {
        $user = $request->user();
        if (! $user->isSuperAdmin()) {
            abort(403, 'Unauthorized. Only Superadmin can manage custom fields.');
        }

        $defaults = $this->getDefaultFieldDefinitions();

        // If it's a default system field
        if (isset($defaults[$key])) {
            $overrides = Setting::get('dwr_system_field_overrides', []);
            if (! is_array($overrides)) {
                $overrides = [];
            }
            $currentEnabled = isset($overrides[$key]['enabled']) ? (bool) $overrides[$key]['enabled'] : true;
            $overrides[$key]['enabled'] = ! $currentEnabled;

            Setting::set('dwr_system_field_overrides', $overrides, 'work_reports', 'json');

            $statusText = ! $currentEnabled ? 'activated' : 'hidden';

            return back()->with('success', "Field '{$defaults[$key]['label']}' {$statusText} successfully!");
        }

        // Custom field toggle
        $existingFields = $this->getCustomFieldDefinitions();
        $fieldFound = false;
        $label = $key;

        foreach ($existingFields as &$field) {
            if ($field['key'] === $key) {
                $currentEnabled = isset($field['enabled']) ? (bool) $field['enabled'] : true;
                $field['enabled'] = ! $currentEnabled;
                $label = $field['label'];
                $fieldFound = true;
                break;
            }
        }

        if ($fieldFound) {
            Setting::set('dwr_custom_fields', $existingFields, 'work_reports', 'json');
            $statusText = ! $currentEnabled ? 'activated' : 'hidden';

            return back()->with('success', "Custom field '{$label}' {$statusText} successfully!");
        }

        return back()->withErrors(['label' => 'Field not found.']);
    }

    /**
     * Delete an existing custom field definition (Superadmin only).
     */
    public function destroyCustomField(Request $request, string $key): RedirectResponse
    {
        $user = $request->user();
        if (! $user->isSuperAdmin()) {
            abort(403, 'Unauthorized. Only Superadmin can manage custom fields.');
        }

        $existingFields = $this->getCustomFieldDefinitions();
        $updatedFields = array_values(array_filter($existingFields, fn ($f) => $f['key'] !== $key));

        Setting::set('dwr_custom_fields', $updatedFields, 'work_reports', 'json');

        return back()->with('success', 'Custom field removed successfully!');
    }

    /**
     * Remove the specified report.
     */
    public function destroy(Request $request, DailyWorkReport $workReport): RedirectResponse
    {
        $user = $request->user();
        if (! $user->isSuperAdmin()) {
            abort(403, 'Unauthorized. Daily work reports can only be deleted by Superadmin.');
        }

        $employeeId = $workReport->employee_id;
        $month = $workReport->report_date->format('Y-m');

        $workReport->delete();

        return redirect()->route('work-reports.index', [
            'employee_id' => $employeeId,
            'month' => $month,
        ])->with('success', 'Daily Work Report entry removed.');
    }
}
