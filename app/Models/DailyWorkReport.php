<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyWorkReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'report_date',
        'calls',
        'instagram_dms',
        'facebook_dms',
        'linkedin_dms',
        'whatsapp_dms',
        'linkedin_posts',
        'instagram_posts',
        'facebook_group_actions',
        'facebook_group_dms',
        'linkedin_connections',
        'remarks',
        'custom_fields',
    ];

    protected $casts = [
        'report_date' => 'date:Y-m-d',
        'calls' => 'integer',
        'instagram_dms' => 'integer',
        'facebook_dms' => 'integer',
        'linkedin_dms' => 'integer',
        'whatsapp_dms' => 'integer',
        'linkedin_posts' => 'integer',
        'instagram_posts' => 'integer',
        'facebook_group_actions' => 'integer',
        'facebook_group_dms' => 'integer',
        'linkedin_connections' => 'integer',
        'custom_fields' => 'array',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Compute Total Social / Outreach Activities matching spreadsheet column M + dynamic social fields
     */
    public function getSocialOutreachActivitiesAttribute(): int
    {
        $total = (int) (
            $this->instagram_dms +
            $this->facebook_dms +
            $this->linkedin_dms +
            $this->whatsapp_dms +
            $this->linkedin_posts +
            $this->instagram_posts +
            $this->facebook_group_actions +
            $this->facebook_group_dms +
            $this->linkedin_connections
        );

        if (is_array($this->custom_fields)) {
            $customDefs = Setting::get('dwr_custom_fields', []);
            foreach ($customDefs as $field) {
                if (! empty($field['is_social']) && isset($this->custom_fields[$field['key']])) {
                    $total += (int) $this->custom_fields[$field['key']];
                }
            }
        }

        return $total;
    }
}
