<?php

namespace Tests\Feature;

use App\Models\DailyWorkReport;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DailyWorkReportTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Employee $employee;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'role' => 'superadmin',
        ]);
        $this->employee = Employee::create([
            'employee_name' => 'Daksh Patel',
            'employee_code' => 'EMP-0099',
            'department' => 'Marketing & Sales',
            'designation' => 'Digital Marketing Executive',
            'employment_type' => 'Permanent',
            'status' => 'Active',
            'joining_date' => '2026-01-01',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_authenticated_user_can_view_daily_work_report_sheet(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('work-reports.index', [
                'employee_id' => $this->employee->id,
                'month' => '2026-08',
                'tab' => 'sheet',
            ]));

        $response->assertOk();
        $response->assertSee('DAILY WORK REPORT');
        $response->assertSee('Daksh Patel', false);
        $response->assertSee('Calls');
        $response->assertSee('Instagram DMs');
        $response->assertSee('TOTAL');
    }

    public function test_user_can_store_and_update_daily_work_report(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('work-reports.store'), [
                'employee_id' => $this->employee->id,
                'report_date' => '2026-08-25',
                'calls' => 50,
                'instagram_dms' => 6,
                'facebook_dms' => 4,
                'linkedin_dms' => 0,
                'whatsapp_dms' => 0,
                'linkedin_posts' => 4,
                'instagram_posts' => 0,
                'facebook_group_actions' => 0,
                'facebook_group_dms' => 0,
                'linkedin_connections' => 0,
                'remarks' => 'LinkedIn activity not properly understood',
            ]);

        $response->assertRedirect();
        $report = DailyWorkReport::first();
        $this->assertNotNull($report);
        $this->assertEquals($this->employee->id, $report->employee_id);
        $this->assertEquals('2026-08-25', $report->report_date->format('Y-m-d'));
        $this->assertEquals(50, $report->calls);
        $this->assertEquals(6, $report->instagram_dms);
        $this->assertEquals(4, $report->facebook_dms);
        $this->assertEquals(4, $report->linkedin_posts);
        $this->assertEquals('LinkedIn activity not properly understood', $report->remarks);

        $report = DailyWorkReport::first();
        // Social activities = 6 + 4 + 0 + 0 + 4 + 0 + 0 + 0 + 0 = 14
        $this->assertEquals(14, $report->social_outreach_activities);

        // Update same day
        $updateResponse = $this->actingAs($this->user)
            ->post(route('work-reports.store'), [
                'employee_id' => $this->employee->id,
                'report_date' => '2026-08-25',
                'calls' => 70,
                'instagram_dms' => 10,
                'facebook_dms' => 5,
                'linkedin_dms' => 2,
                'whatsapp_dms' => 1,
                'linkedin_posts' => 5,
                'remarks' => 'Updated remarks with higher calls',
            ]);

        $updateResponse->assertRedirect();
        $this->assertEquals(1, DailyWorkReport::count());
        $this->assertDatabaseHas('daily_work_reports', [
            'employee_id' => $this->employee->id,
            'calls' => 70,
            'instagram_dms' => 10,
        ]);
    }

    public function test_user_can_view_summary_kpi_dashboard_tab(): void
    {
        // Populate sample data matching user's spreadsheet
        DailyWorkReport::create([
            'employee_id' => $this->employee->id,
            'report_date' => '2026-08-19',
            'calls' => 25,
            'instagram_dms' => 0,
            'facebook_dms' => 0,
            'linkedin_dms' => 0,
            'whatsapp_dms' => 0,
            'linkedin_posts' => 0,
            'instagram_posts' => 0,
            'facebook_group_actions' => 0,
            'facebook_group_dms' => 0,
            'linkedin_connections' => 0,
            'remarks' => null,
        ]);

        DailyWorkReport::create([
            'employee_id' => $this->employee->id,
            'report_date' => '2026-08-20',
            'calls' => 23,
            'instagram_dms' => 0,
            'facebook_dms' => 0,
            'linkedin_dms' => 0,
            'whatsapp_dms' => 0,
            'linkedin_posts' => 5,
            'instagram_posts' => 0,
            'facebook_group_actions' => 0,
            'facebook_group_dms' => 0,
            'linkedin_connections' => 0,
            'remarks' => null,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('work-reports.index', [
                'employee_id' => $this->employee->id,
                'month' => '2026-08',
                'tab' => 'kpi',
            ]));

        $response->assertOk();
        $response->assertSee('SUMMARY KPI DASHBOARD');
        $response->assertSee('Phone / Tele Calls');
        $response->assertSee('48'); // 25 + 23
        $response->assertSee('Target vs Actuals (Calls)');
    }

    public function test_superadmin_can_add_and_delete_dynamic_custom_fields(): void
    {
        // Add dynamic custom field
        $response = $this->actingAs($this->user)
            ->post(route('work-reports.custom-fields.store'), [
                'label' => 'Cold Emails',
                'type' => 'number',
                'is_social' => '1',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify custom field appears on the sheet
        $sheetResponse = $this->actingAs($this->user)
            ->get(route('work-reports.index', [
                'employee_id' => $this->employee->id,
                'month' => '2026-08',
                'tab' => 'sheet',
            ]));

        $sheetResponse->assertOk();
        $sheetResponse->assertSee('Cold Emails');
        $sheetResponse->assertSee('⚙️ Manage Fields');

        // Log daily report with dynamic custom field value
        $logResponse = $this->actingAs($this->user)
            ->post(route('work-reports.store'), [
                'employee_id' => $this->employee->id,
                'report_date' => '2026-08-25',
                'calls' => 15,
                'custom_fields' => [
                    'cold_emails' => 25,
                ],
            ]);

        $logResponse->assertRedirect();
        $report = DailyWorkReport::where('report_date', '2026-08-25')->first();
        $this->assertNotNull($report);
        $this->assertIsArray($report->custom_fields);
        $this->assertEquals(25, $report->custom_fields['cold_emails']);
        // is_social was true, so social activities includes cold_emails (25)
        $this->assertEquals(25, $report->social_outreach_activities);

        // Delete the custom field
        $deleteResponse = $this->actingAs($this->user)
            ->delete(route('work-reports.custom-fields.destroy', 'cold_emails'));

        $deleteResponse->assertRedirect();
        $deleteResponse->assertSessionHas('success');
    }

    public function test_superadmin_can_update_dynamic_custom_field(): void
    {
        // First add a field
        $this->actingAs($this->user)
            ->post(route('work-reports.custom-fields.store'), [
                'label' => 'Old Title',
                'type' => 'text',
                'is_social' => '0',
            ]);

        // Update the field
        $updateResponse = $this->actingAs($this->user)
            ->put(route('work-reports.custom-fields.update', 'old_title'), [
                'label' => 'New Awesome Title',
                'type' => 'number',
                'is_social' => '1',
            ]);

        $updateResponse->assertRedirect();
        $updateResponse->assertSessionHas('success');

        // Check that new title renders
        $sheetResponse = $this->actingAs($this->user)
            ->get(route('work-reports.index', [
                'employee_id' => $this->employee->id,
                'month' => '2026-08',
                'tab' => 'sheet',
            ]));

        $sheetResponse->assertOk();
        $sheetResponse->assertSee('New Awesome Title');
    }

    public function test_superadmin_can_toggle_and_rename_system_default_fields(): void
    {
        // 1. Rename default system field 'instagram_dms'
        $renameResponse = $this->actingAs($this->user)
            ->put(route('work-reports.custom-fields.update', 'instagram_dms'), [
                'label' => 'IG Chat Touches',
            ]);

        $renameResponse->assertRedirect();
        $renameResponse->assertSessionHas('success');

        // Verify sheet has renamed title
        $sheetResponse = $this->actingAs($this->user)
            ->get(route('work-reports.index', [
                'employee_id' => $this->employee->id,
                'month' => '2026-08',
                'tab' => 'sheet',
            ]));

        $sheetResponse->assertOk();
        $sheetResponse->assertSee('IG Chat Touches');

        // 2. Toggle 'instagram_dms' visibility to Hidden
        $toggleResponse = $this->actingAs($this->user)
            ->post(route('work-reports.custom-fields.toggle', 'instagram_dms'));

        $toggleResponse->assertRedirect();
        $toggleResponse->assertSessionHas('success');

        // Verify sheet now hides IG Chat Touches from the table header
        $hiddenSheetResponse = $this->actingAs($this->user)
            ->get(route('work-reports.index', [
                'employee_id' => $this->employee->id,
                'month' => '2026-08',
                'tab' => 'sheet',
            ]));

        $hiddenSheetResponse->assertOk();
        $this->assertFalse(str_contains($hiddenSheetResponse->getContent(), '<th class="px-2 py-3 text-center text-xs font-bold text-slate-800 uppercase tracking-wider border-r border-slate-200">IG Chat Touches</th>'));

        // 3. Toggle back to Active (Show)
        $toggleBackResponse = $this->actingAs($this->user)
            ->post(route('work-reports.custom-fields.toggle', 'instagram_dms'));

        $toggleBackResponse->assertRedirect();
        $toggleBackResponse->assertSessionHas('success');
    }
}
