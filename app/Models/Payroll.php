<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payroll extends Model
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
        'month' => 'integer',
        'year' => 'integer',
        'total_month_days' => 'integer',
        'working_days' => 'integer',
        'present_days' => 'decimal:1',
        'paid_leaves' => 'decimal:1',
        'holidays_weekoffs' => 'integer',
        'absent_days' => 'decimal:1',
        'payable_days' => 'decimal:1',
        'gross_salary' => 'decimal:2',
        'earned_basic' => 'decimal:2',
        'earned_hra' => 'decimal:2',
        'earned_allowances' => 'decimal:2',
        'overtime_bonus' => 'decimal:2',
        'total_earnings' => 'decimal:2',
        'pf_amount' => 'decimal:2',
        'esic_amount' => 'decimal:2',
        'prof_tax' => 'decimal:2',
        'tds_amount' => 'decimal:2',
        'unpaid_leave_deduction' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    /**
     * Get the employee that this payroll belongs to.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the user who generated this payroll.
     */
    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    /**
     * Helper to return tailwind badge color classes for payment status.
     *
     * @return array{bg: string, text: string, border: string, dot: string}
     */
    public function getPaymentStatusBadgeAttribute(): array
    {
        return match ($this->payment_status) {
            'Paid' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'dot' => 'bg-emerald-500'],
            'Processing' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'dot' => 'bg-blue-500'],
            default => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'dot' => 'bg-amber-500'], // Pending
        };
    }
}
