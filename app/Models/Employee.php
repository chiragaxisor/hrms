<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Employee extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'joining_date' => 'date',
        'date_of_birth' => 'date',
        'anniversary_date' => 'date',
        'statutory_details' => 'array',
        'bank_details' => 'array',
        'address_details' => 'array',
        'emergency_contact' => 'array',
        'reference_contact' => 'array',
        'nominee_details' => 'array',
        'family_members' => 'array',
        'document_checklist' => 'array',
        'asset_details' => 'array',
    ];

    /**
     * Get the user that owns the employee profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the attendance records for the employee.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get the leave requests for the employee.
     */
    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    /**
     * Get the salary structure configured for this employee.
     */
    public function salaryStructure(): HasOne
    {
        return $this->hasOne(SalaryStructure::class);
    }

    /**
     * Get monthly payroll records for this employee.
     */
    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    /**
     * Get daily work reports for this employee.
     */
    public function dailyWorkReports(): HasMany
    {
        return $this->hasMany(DailyWorkReport::class);
    }
}
