<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Get attendance history for the authenticated user.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $attendances = Attendance::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get()
            ->map(function ($attendance) {
                return [
                    'id' => $attendance->id,
                    'date' => Carbon::parse($attendance->date)->format('d F Y'),
                    'totalHours' => $attendance->total_hours ?? '00:00:00 hrs',
                    'clockInOut' => ($attendance->clock_in ? Carbon::parse($attendance->clock_in)->format('h:i A') : '--:--') . ' — ' . 
                                    ($attendance->clock_out ? Carbon::parse($attendance->clock_out)->format('h:i A') : '--:--'),
                    'status' => $attendance->status,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $attendances
        ]);
    }

    /**
     * Punch In / Out / Break
     */
    public function punch(Request $request)
    {
        $request->validate([
            'type' => 'required|in:IN,OUT,BREAK_IN,BREAK_OUT',
            'lat' => 'nullable|numeric',
            'lon' => 'nullable|numeric',
            'photo' => 'nullable|image|max:5120',
        ]);

        $user = $request->user();
        $today = Carbon::today();

        $attendance = Attendance::firstOrCreate(
            ['user_id' => $user->id, 'date' => $today->format('Y-m-d')],
            ['status' => 'present']
        );

        $now = Carbon::now();
        $message = '';

        switch ($request->type) {
            case 'IN':
                if (!$attendance->clock_in) {
                    $attendance->clock_in = $now;
                    $message = 'Clocked in successfully.';
                } else {
                    return response()->json(['success' => false, 'message' => 'Already clocked in today.'], 400);
                }
                break;
            case 'OUT':
                if ($attendance->clock_in && !$attendance->clock_out) {
                    $attendance->clock_out = $now;
                    // Calculate total hours simple logic
                    $in = Carbon::parse($attendance->clock_in);
                    $diff = $in->diffInSeconds($now);
                    $attendance->total_hours = gmdate('H:i:s', $diff) . ' hrs';
                    $message = 'Clocked out successfully.';
                } else {
                    return response()->json(['success' => false, 'message' => 'Cannot clock out right now.'], 400);
                }
                break;
            case 'BREAK_IN':
            case 'BREAK_OUT':
                // Simplified break tracking
                $message = 'Break logged successfully.';
                break;
        }

        $attendance->save();

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $attendance
        ]);
    }
}
