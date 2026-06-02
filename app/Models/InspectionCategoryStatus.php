<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InspectionCategoryStatus extends Model
{
    protected $fillable = ['inspection_id', 'inspection_policy_id', 'status'];

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class);
    }

    public function policy(): BelongsTo
    {
        return $this->belongsTo(InspectionPolicy::class, 'inspection_policy_id');
    }

    public function getLabelAttribute(): string
    {
        return match ($this->status) {
            'C'  => 'Compliant',
            'NC' => 'Non-Compliant',
            'NA' => 'N/A',
            default => $this->status,
        };
    }
}
