<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    /**
     * Display the daily attendance sheet.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $isEmployee = ! $user->isAdmin();
        $myEmployee = $user->employee;

        $date = $request->input('date', Carbon::today()->toDateString());
        $departmentFilter = $request->input('department');
        $statusFilter = $request->input('status');
        $search = $request->input('search');

        if ($isEmployee) {
            $employees = $myEmployee ? collect([$myEmployee]) : collect();
        } else {
            // Query active employees
            $employeesQuery = Employee::query()->where('status', 'Active');

            if ($departmentFilter) {
                $employeesQuery->where('department', $departmentFilter);
            }

            if ($search) {
                $employeesQuery->where(function ($q) use ($search) {
                    $q->where('employee_name', 'like', "%{$search}%")
                        ->orWhere('employee_code', 'like', "%{$search}%")
                        ->orWhere('designation', 'like', "%{$search}%");
                });
            }

            $employees = $employeesQuery->orderBy('employee_name')->get();
        }

        // Load all attendances for the given date
        $attendances = Attendance::where('date', $date)
            ->whereIn('employee_id', $employees->pluck('id'))
            ->get()
            ->keyBy('employee_id');

        // Calculate summary metrics for today
        if ($isEmployee) {
            $totalEmployeesCount = $myEmployee ? 1 : 0;
            $todayAllAttendances = $attendances;
        } else {
            $totalEmployeesCount = Employee::where('status', 'Active')->count();
            $todayAllAttendances = Attendance::where('date', $date)->get();
        }

        $stats = [
            'total_staff' => $totalEmployeesCount,
            'present' => $todayAllAttendances->where('status', 'Present')->count(),
            'absent' => $todayAllAttendances->where('status', 'Absent')->count(),
            'half_day' => $todayAllAttendances->where('status', 'Half Day')->count(),
            'leave' => $todayAllAttendances->where('status', 'Leave')->count(),
            'holiday' => $todayAllAttendances->where('status', 'Holiday')->count(),
            'unmarked' => max(0, $totalEmployeesCount - $todayAllAttendances->count()),
        ];

        // Filter employees if status filter applied
        if ($statusFilter) {
            $employees = $employees->filter(function ($emp) use ($attendances, $statusFilter) {
                $record = $attendances->get($emp->id);
                if ($statusFilter === 'Unmarked') {
                    return ! $record;
                }

                return $record && $record->status === $statusFilter;
            });
        }

        $departments = Department::orderBy('name')->get();

        // Check if currently logged in user has an employee profile and today's punch status
        $myEmployee = Employee::where('user_id', Auth::id())->first();
        $myTodayAttendance = $myEmployee ? Attendance::where('employee_id', $myEmployee->id)->where('date', Carbon::today()->toDateString())->first() : null;

        $attendanceSettings = [
            'office_latitude' => (float) Setting::get('office_latitude', 23.0225),
            'office_longitude' => (float) Setting::get('office_longitude', 72.5714),
            'geofence_radius_meters' => (int) Setting::get('geofence_radius_meters', 250),
            'require_geofence' => (bool) Setting::get('require_geofence', true),
            'require_camera_punch' => (bool) Setting::get('require_camera_punch', true),
        ];

        return view('attendances.index', compact(
            'employees',
            'attendances',
            'date',
            'departmentFilter',
            'statusFilter',
            'search',
            'stats',
            'departments',
            'myEmployee',
            'myTodayAttendance',
            'attendanceSettings'
        ));
    }

    /**
     * Store or update attendance for a single employee.
     */
    public function store(Request $request): RedirectResponse
    {
        // Normalize time inputs to H:i if passed with seconds (e.g. 09:30:00 -> 09:30)
        foreach (['clock_in', 'clock_out', 'break_in', 'break_out'] as $timeKey) {
            if ($request->filled($timeKey)) {
                $val = trim((string) $request->input($timeKey));
                if (preg_match('/^(\d{1,2}:\d{2})(:\d{2})?$/', $val, $m)) {
                    $request->merge([$timeKey => $m[1]]);
                }
            }
        }

        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'date' => ['required', 'date'],
            'status' => ['required', 'string', 'in:Present,Absent,Half Day,Leave,Holiday,Week Off'],
            'clock_in' => ['nullable', 'date_format:H:i'],
            'clock_out' => ['nullable', 'date_format:H:i'],
            'break_in' => ['nullable', 'date_format:H:i'],
            'break_out' => ['nullable', 'date_format:H:i'],
            'break_minutes' => ['nullable', 'integer', 'min:0'],
            'shift' => ['nullable', 'string'],
            'location_type' => ['nullable', 'string', 'in:Office,Work From Home,Field'],
            'remarks' => ['nullable', 'string', 'max:500'],
        ]);

        $clockIn = $validated['clock_in'] ?? null;
        $clockOut = $validated['clock_out'] ?? null;
        $breakIn = $validated['break_in'] ?? null;
        $breakOut = $validated['break_out'] ?? null;
        $breakMinutes = (int) ($validated['break_minutes'] ?? 0);

        if ($breakIn && $breakOut) {
            $bIn = $this->parseTime($breakIn);
            $bOut = $this->parseTime($breakOut);
            if ($bIn && $bOut && $bOut->greaterThan($bIn)) {
                $breakMinutes = $bIn->diffInMinutes($bOut);
            }
        }

        $totalHours = 0;

        if ($clockIn && $clockOut) {
            $inTime = $this->parseTime($clockIn);
            $outTime = $this->parseTime($clockOut);

            if ($inTime && $outTime && $outTime->greaterThan($inTime)) {
                $grossMinutes = $inTime->diffInMinutes($outTime);
                $netMinutes = max(0, $grossMinutes - $breakMinutes);
                $totalHours = round($netMinutes / 60, 2);
            }
        }

        // Calculate late minutes based on 09:30 AM standard shift
        $lateMinutes = 0;
        if ($clockIn) {
            $inTime = $this->parseTime($clockIn);
            $standardTime = $this->parseTime('09:30');
            if ($inTime && $standardTime && $inTime->greaterThan($standardTime)) {
                $lateMinutes = $standardTime->diffInMinutes($inTime);
            }
        }

        Attendance::updateOrCreate(
            [
                'employee_id' => $validated['employee_id'],
                'date' => $validated['date'],
            ],
            [
                'status' => $validated['status'],
                'clock_in' => $clockIn,
                'clock_out' => $clockOut,
                'break_in' => $breakIn,
                'break_out' => $breakOut,
                'break_minutes' => $breakMinutes,
                'total_hours' => $totalHours,
                'shift' => $validated['shift'] ?? 'General',
                'late_minutes' => $lateMinutes,
                'location_type' => $validated['location_type'] ?? 'Office',
                'remarks' => $validated['remarks'] ?? null,
                'created_by' => Auth::id(),
            ]
        );

        return back()->with('success', 'Attendance record saved successfully.');
    }

    /**
     * Batch mark or update attendance for all or selected employees.
     */
    public function batch(Request $request): RedirectResponse
    {
        // Normalize time inputs to H:i if passed with seconds
        foreach (['clock_in', 'clock_out'] as $timeKey) {
            if ($request->filled($timeKey)) {
                $val = trim((string) $request->input($timeKey));
                if (preg_match('/^(\d{1,2}:\d{2})(:\d{2})?$/', $val, $m)) {
                    $request->merge([$timeKey => $m[1]]);
                }
            }
        }

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'status' => ['required', 'string', 'in:Present,Absent,Half Day,Leave,Holiday,Week Off'],
            'mode' => ['required', 'string', 'in:all_unmarked,all,selected'],
            'selected_ids' => ['nullable', 'array'],
            'selected_ids.*' => ['exists:employees,id'],
            'clock_in' => ['nullable', 'date_format:H:i'],
            'clock_out' => ['nullable', 'date_format:H:i'],
            'remarks' => ['nullable', 'string', 'max:255'],
        ]);

        $date = $validated['date'];
        $status = $validated['status'];
        $clockIn = $validated['clock_in'] ?? ($status === 'Present' ? '09:30' : null);
        $clockOut = $validated['clock_out'] ?? ($status === 'Present' ? '18:30' : null);

        $totalHours = ($clockIn && $clockOut) ? 9.00 : 0.00;

        $targetEmployeeIds = collect();

        if ($validated['mode'] === 'selected' && ! empty($validated['selected_ids'])) {
            $targetEmployeeIds = collect($validated['selected_ids']);
        } elseif ($validated['mode'] === 'all_unmarked') {
            $markedIds = Attendance::where('date', $date)->pluck('employee_id');
            $targetEmployeeIds = Employee::where('status', 'Active')
                ->whereNotIn('id', $markedIds)
                ->pluck('id');
        } else {
            // all
            $targetEmployeeIds = Employee::where('status', 'Active')->pluck('id');
        }

        $count = 0;
        foreach ($targetEmployeeIds as $empId) {
            Attendance::updateOrCreate(
                [
                    'employee_id' => $empId,
                    'date' => $date,
                ],
                [
                    'status' => $status,
                    'clock_in' => $clockIn,
                    'clock_out' => $clockOut,
                    'total_hours' => $totalHours,
                    'remarks' => $validated['remarks'] ?? 'Bulk marked by Admin',
                    'created_by' => Auth::id(),
                ]
            );
            $count++;
        }

        return back()->with('success', "Batch attendance marked for {$count} employee(s).");
    }

    /**
     * Self Clock-in / Clock-out punch for the current user.
     */
    public function punch(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'action_type' => ['required', 'in:clock_in,clock_out,break_in,break_out'],
            'location_type' => ['nullable', 'in:Office,Work From Home,Field'],
            'remarks' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'location_address' => ['nullable', 'string', 'max:255'],
            'face_photo' => ['nullable', 'string'], // base64 encoded data URI
        ]);

        $user = $request->user();
        $targetEmployeeId = (int) $validated['employee_id'];

        if (! $user->isAdmin()) {
            if (! $user->employee || $user->employee->id !== $targetEmployeeId) {
                abort(403, 'Unauthorized punch action. You can only record attendance for your own employee account.');
            }
        }

        $requireGeofence = (bool) Setting::get('require_geofence', true);
        $requireCamera = (bool) Setting::get('require_camera_punch', true);

        // 1. Camera Face Verification Requirement Check
        if ($requireCamera && empty($validated['face_photo'])) {
            return back()->withErrors(['face_photo' => 'Face scan camera capture is mandatory for attendance verification. Please permit camera access and scan your face.']);
        }

        // 2. Geofence Location Verification Check
        $officeLat = (float) Setting::get('office_latitude', 23.0225);
        $officeLng = (float) Setting::get('office_longitude', 72.5714);
        $radiusAllowed = (int) Setting::get('geofence_radius_meters', 250);

        $empLat = isset($validated['latitude']) ? (float) $validated['latitude'] : null;
        $empLng = isset($validated['longitude']) ? (float) $validated['longitude'] : null;
        $distanceMeters = null;

        if ($empLat !== null && $empLng !== null) {
            $distanceMeters = $this->calculateDistanceMeters($empLat, $empLng, $officeLat, $officeLng);
        }

        if ($requireGeofence) {
            if ($empLat === null || $empLng === null) {
                return back()->withErrors(['location' => 'Location permission required. Please turn on device GPS/Location to punch attendance.']);
            }

            if ($distanceMeters > $radiusAllowed) {
                $distRounded = round($distanceMeters);

                return back()->withErrors(['location' => "Outside office boundary! You are {$distRounded}m away from the office (Max allowed radius: {$radiusAllowed}m)."]);
            }
        }

        // 3. Save Captured Face Photo to Storage
        $photoPath = null;
        if (! empty($validated['face_photo'])) {
            $photoPath = $this->storeFacePhoto($validated['face_photo'], $targetEmployeeId, $validated['action_type']);
        }

        $today = Carbon::today()->toDateString();
        $currentTime = Carbon::now()->format('H:i');

        $record = Attendance::firstOrNew([
            'employee_id' => $targetEmployeeId,
            'date' => $today,
        ]);

        if ($empLat !== null) {
            $record->latitude = $empLat;
            $record->longitude = $empLng;
            $record->distance_meters = $distanceMeters;
        }
        if (! empty($validated['location_address'])) {
            $record->location_address = $validated['location_address'];
        }

        if ($validated['action_type'] === 'clock_in') {
            $record->clock_in = $currentTime;
            $record->status = $record->status ?? 'Present';
            $record->location_type = $validated['location_type'] ?? 'Office';
            $record->ip_address = $request->ip();
            if ($photoPath) {
                $record->clock_in_photo = $photoPath;
            }

            // Calculate late minutes if after 09:30 AM
            $inTime = $this->parseTime($currentTime);
            $standardTime = $this->parseTime('09:30');
            if ($inTime && $standardTime && $inTime->greaterThan($standardTime)) {
                $record->late_minutes = $standardTime->diffInMinutes($inTime);
            }

            $msg = "Clock-in verified & recorded at {$currentTime}. Face & Location verified!";
        } elseif ($validated['action_type'] === 'break_in') {
            // Started Break
            $record->break_in = $currentTime;
            if ($photoPath) {
                $record->break_in_photo = $photoPath;
            }
            $msg = "Break Started (Break-In) at {$currentTime}. Face verified, enjoy your break!";
        } elseif ($validated['action_type'] === 'break_out') {
            // Resumed work from break
            $record->break_out = $currentTime;
            if ($photoPath) {
                $record->break_out_photo = $photoPath;
            }

            // Calculate break duration
            if ($record->break_in) {
                $bIn = $this->parseTime($record->break_in);
                $bOut = $this->parseTime($currentTime);
                if ($bIn && $bOut && $bOut->greaterThan($bIn)) {
                    $addedMinutes = $bIn->diffInMinutes($bOut);
                    $record->break_minutes = ($record->break_minutes ?? 0) + $addedMinutes;
                }
            }

            // Append to history
            $history = $record->breaks_history ?? [];
            $history[] = [
                'break_in' => $record->break_in,
                'break_out' => $currentTime,
                'break_out_photo' => $photoPath,
            ];
            $record->breaks_history = $history;

            $msg = "Break Ended (Break-Out) at {$currentTime}. Welcome back!";
        } else {
            // clock_out
            $record->clock_out = $currentTime;
            if ($photoPath) {
                $record->clock_out_photo = $photoPath;
            }

            if ($record->clock_in) {
                $inTime = $this->parseTime($record->clock_in);
                $outTime = $this->parseTime($currentTime);
                if ($inTime && $outTime && $outTime->greaterThan($inTime)) {
                    $grossMinutes = $inTime->diffInMinutes($outTime);
                    $breakMinutes = $record->break_minutes ?? 0;
                    $netMinutes = max(0, $grossMinutes - $breakMinutes);
                    $record->total_hours = round($netMinutes / 60, 2);
                }
            }
            $msg = "Clock-out verified & recorded at {$currentTime}. Face verified, goodbye!";
        }

        if (! empty($validated['remarks'])) {
            $record->remarks = $validated['remarks'];
        }

        $record->created_by = Auth::id();
        $record->save();

        return back()->with('success', $msg);
    }

    /**
     * Calculate distance between two coordinates in meters (Haversine formula).
     */
    private function calculateDistanceMeters(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Store a base64 encoded face photo.
     */
    private function storeFacePhoto(string $base64Data, int $employeeId, string $actionType): ?string
    {
        try {
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
                $data = substr($base64Data, strpos($base64Data, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, etc

                $data = base64_decode($data);
                if ($data === false) {
                    return null;
                }

                $filename = "attendance_faces/{$employeeId}/".date('Ymd_His')."_{$actionType}.{$type}";
                Storage::disk('public')->put($filename, $data);

                return $filename;
            }
        } catch (\Throwable $e) {
            // Log or ignore image failure gracefully
        }

        return null;
    }

    /**
     * Parse a time string that may be in 'H:i' or 'H:i:s' format safely.
     */
    private function parseTime(?string $time): ?Carbon
    {
        if (! $time) {
            return null;
        }

        $trimmed = trim($time);
        if (preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $trimmed)) {
            $parts = explode(':', $trimmed);
            $h = (int) $parts[0];
            $m = (int) $parts[1];

            return Carbon::today()->setTime($h, $m, 0);
        }

        try {
            return Carbon::parse($trimmed);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Display Monthly Attendance Register / Sheet.
     */
    public function report(Request $request): View
    {
        $year = (int) $request->input('year', Carbon::today()->year);
        $month = (int) $request->input('month', Carbon::today()->month);
        $departmentFilter = $request->input('department');

        $user = $request->user();
        $isEmployee = ! $user->isAdmin();
        $myEmployee = $user->employee;

        $currentMonth = Carbon::createFromDate($year, $month, 1);
        $daysInMonth = $currentMonth->daysInMonth;

        if ($isEmployee) {
            $employees = $myEmployee ? collect([$myEmployee]) : collect();
        } else {
            $employeesQuery = Employee::query()->where('status', 'Active');
            if ($departmentFilter) {
                $employeesQuery->where('department', $departmentFilter);
            }
            $employees = $employeesQuery->orderBy('employee_name')->get();
        }

        // Fetch all attendances for the given month
        $attendances = Attendance::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->whereIn('employee_id', $employees->pluck('id'))
            ->get()
            ->groupBy('employee_id');

        $departments = Department::orderBy('name')->get();

        return view('attendances.report', compact(
            'employees',
            'attendances',
            'year',
            'month',
            'daysInMonth',
            'currentMonth',
            'departments',
            'departmentFilter'
        ));
    }

    /**
     * Delete an attendance record.
     */
    public function destroy(Attendance $attendance): RedirectResponse
    {
        $attendance->delete();

        return back()->with('success', 'Attendance record deleted.');
    }
}
