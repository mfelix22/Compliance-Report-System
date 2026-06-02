<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Finding extends Model
{
    use HasFactory;

    protected $fillable = [
        'inspection_id',
        'inspection_policy_id',
        'policy_item_id',
        'selected_policy_item_ids',
        'custom_finding_items',
        'number',
        'finding',
        'root_cause',
        'department_id',
        'due_date',
        'photo',
        'keterangan',
        'corrective_action',
        'preventive_action',
        'status',
        'date_closed',
        'verification_status',
        'verification_date',
        'verification_notes',
        'parent_finding_id',
    ];

    protected $casts = [
        'due_date'                  => 'date',
        'date_closed'               => 'date',
        'verification_date'         => 'date',
        'selected_policy_item_ids'  => 'array',
        'custom_finding_items'      => 'array',
    ];

    public function policy(): BelongsTo
    {
        return $this->belongsTo(InspectionPolicy::class, 'inspection_policy_id');
    }

    public function policyItem(): BelongsTo
    {
        return $this->belongsTo(PolicyItem::class);
    }

    public function isOverdue(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->status === 'open'
                && $this->due_date !== null
                && $this->due_date->isPast(),
        );
    }

    public function daysUntilDue(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->due_date === null) {
                    return null;
                }
                return (int) now()->startOfDay()->diffInDays($this->due_date->startOfDay(), false);
            },
        );
    }

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function parentFinding(): BelongsTo
    {
        return $this->belongsTo(Finding::class, 'parent_finding_id');
    }

    public function followUpFinding(): HasOne
    {
        return $this->hasOne(Finding::class, 'parent_finding_id');
    }

    public function getRootCauseLabelAttribute(): string
    {
        return match ($this->root_cause) {
            'people'     => 'People',
            'facilities' => 'Facilities',
            'training'   => 'Training',
            'others'     => 'Others',
            default      => ucfirst($this->root_cause),
        };
    }
}
