<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTest extends TestCase
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

        Setting::set('require_geofence', '0', 'attendance', 'boolean');
        Setting::set('require_camera_punch', '0', 'attendance', 'boolean');
    }

    public function test_attendance_index_screen_can_be_rendered(): void
    {
        $response = $this->actingAs($this->user)->get(route('attendances.index'));

        $response->assertOk();
        $response->assertSee('Daily Attendance Register');
        $response->assertSee('John Doe');
    }

    public function test_can_store_attendance_record(): void
    {
        $date = Carbon::today()->toDateString();

        $response = $this->actingAs($this->user)->post(route('attendances.store'), [
            'employee_id' => $this->employee->id,
            'date' => $date,
            'status' => 'Present',
            'clock_in' => '09:30',
            'clock_out' => '18:30',
            'shift' => 'General',
            'location_type' => 'Office',
            'remarks' => 'On time arrival',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('attendances', [
            'employee_id' => $this->employee->id,
            'status' => 'Present',
            'total_hours' => 9.00,
        ]);
        $record = Attendance::where('employee_id', $this->employee->id)->first();
        $this->assertNotNull($record);
        $this->assertEquals($date, $record->date->format('Y-m-d'));
    }

    public function test_can_store_attendance_with_seconds_format_and_break_deduction(): void
    {
        $date = Carbon::today()->toDateString();

        $response = $this->actingAs($this->user)->post(route('attendances.store'), [
            'employee_id' => $this->employee->id,
            'date' => $date,
            'status' => 'Present',
            'clock_in' => '09:30:00',
            'clock_out' => '18:30:00',
            'break_in' => '13:00:00',
            'break_out' => '14:00:00',
            'break_minutes' => 60,
            'shift' => 'General',
            'location_type' => 'Office',
        ]);

        $response->assertRedirect();
        $record = Attendance::where('employee_id', $this->employee->id)->first();
        $this->assertNotNull($record);
        // 9 hours gross - 1 hour (60 min) break = 8.00 hours net
        $this->assertEquals(8.00, $record->total_hours);
        $this->assertEquals(60, $record->break_minutes);
    }

    public function test_can_batch_mark_attendance(): void
    {
        $date = Carbon::today()->toDateString();

        $response = $this->actingAs($this->user)->post(route('attendances.batch'), [
            'date' => $date,
            'mode' => 'all',
            'status' => 'Present',
            'clock_in' => '09:30',
            'clock_out' => '18:30',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('attendances', [
            'employee_id' => $this->employee->id,
            'status' => 'Present',
        ]);
        $record = Attendance::where('employee_id', $this->employee->id)->first();
        $this->assertNotNull($record);
        $this->assertEquals($date, $record->date->format('Y-m-d'));
    }

    public function test_can_punch_in_and_punch_out(): void
    {
        $response = $this->actingAs($this->user)->post(route('attendances.punch'), [
            'employee_id' => $this->employee->id,
            'action_type' => 'clock_in',
            'location_type' => 'Office',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('attendances', [
            'employee_id' => $this->employee->id,
            'status' => 'Present',
        ]);
        $record = Attendance::where('employee_id', $this->employee->id)->first();
        $this->assertNotNull($record);
        $this->assertEquals(Carbon::today()->toDateString(), $record->date->format('Y-m-d'));

        $responseOut = $this->actingAs($this->user)->post(route('attendances.punch'), [
            'employee_id' => $this->employee->id,
            'action_type' => 'clock_out',
        ]);

        $responseOut->assertRedirect();
        $att = Attendance::where('employee_id', $this->employee->id)->first();
        $this->assertNotNull($att->clock_out);
    }

    public function test_can_break_in_and_break_out(): void
    {
        $this->actingAs($this->user)->post(route('attendances.punch'), [
            'employee_id' => $this->employee->id,
            'action_type' => 'clock_in',
        ]);

        $responseBreakIn = $this->actingAs($this->user)->post(route('attendances.punch'), [
            'employee_id' => $this->employee->id,
            'action_type' => 'break_in',
        ]);
        $responseBreakIn->assertRedirect();

        $att = Attendance::where('employee_id', $this->employee->id)->first();
        $this->assertNotNull($att->break_in);

        $responseBreakOut = $this->actingAs($this->user)->post(route('attendances.punch'), [
            'employee_id' => $this->employee->id,
            'action_type' => 'break_out',
        ]);
        $responseBreakOut->assertRedirect();

        $att->refresh();
        $this->assertNotNull($att->break_out);
    }

    public function test_punch_handles_database_records_with_seconds_time_format(): void
    {
        // Simulate record loaded from MySQL where TIME column produces '15:40:00'
        $att = Attendance::create([
            'employee_id' => $this->employee->id,
            'date' => Carbon::today()->toDateString(),
            'status' => 'Present',
            'clock_in' => '09:30:00',
            'break_in' => '13:00:00',
        ]);

        // Break out
        $responseBreakOut = $this->actingAs($this->user)->post(route('attendances.punch'), [
            'employee_id' => $this->employee->id,
            'action_type' => 'break_out',
        ]);
        $responseBreakOut->assertRedirect();
        $responseBreakOut->assertSessionHasNoErrors();

        // Clock out
        $responseClockOut = $this->actingAs($this->user)->post(route('attendances.punch'), [
            'employee_id' => $this->employee->id,
            'action_type' => 'clock_out',
        ]);
        $responseClockOut->assertRedirect();
        $responseClockOut->assertSessionHasNoErrors();

        $att->refresh();
        $this->assertNotNull($att->clock_out);
        $this->assertNotNull($att->break_out);
    }

    public function test_punch_verifies_face_photo_and_geofence(): void
    {
        Setting::set('require_geofence', '1', 'attendance', 'boolean');
        Setting::set('require_camera_punch', '1', 'attendance', 'boolean');
        Setting::set('office_latitude', '23.0225', 'attendance', 'string');
        Setting::set('office_longitude', '72.5714', 'attendance', 'string');
        Setting::set('geofence_radius_meters', '250', 'attendance', 'integer');

        // Attempt without face photo -> should fail validation
        $responseNoPhoto = $this->actingAs($this->user)->post(route('attendances.punch'), [
            'employee_id' => $this->employee->id,
            'action_type' => 'clock_in',
            'latitude' => 23.0225,
            'longitude' => 72.5714,
        ]);
        $responseNoPhoto->assertSessionHasErrors('face_photo');

        // Attempt outside geofence -> should fail validation
        $fakePngBase64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';
        $responseOutside = $this->actingAs($this->user)->post(route('attendances.punch'), [
            'employee_id' => $this->employee->id,
            'action_type' => 'clock_in',
            'face_photo' => $fakePngBase64,
            'latitude' => 23.1500, // ~14km away
            'longitude' => 72.6000,
        ]);
        $responseOutside->assertSessionHasErrors('location');

        // Valid inside geofence with face photo
        $responseValid = $this->actingAs($this->user)->post(route('attendances.punch'), [
            'employee_id' => $this->employee->id,
            'action_type' => 'clock_in',
            'face_photo' => $fakePngBase64,
            'latitude' => 23.02251,
            'longitude' => 72.57141,
        ]);
        $responseValid->assertRedirect();
        $responseValid->assertSessionHasNoErrors();

        $att = Attendance::where('employee_id', $this->employee->id)->first();
        $this->assertNotNull($att);
        $this->assertNotNull($att->clock_in_photo);
        $this->assertNotNull($att->latitude);
        $this->assertNotNull($att->distance_meters);
        $this->assertLessThan(10, $att->distance_meters);
    }
}
