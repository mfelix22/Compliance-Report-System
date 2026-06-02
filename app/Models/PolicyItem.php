<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PolicyItem extends Model
{
    protected $fillable = ['inspection_policy_id', 'text', 'sort_order'];

    public function policy(): BelongsTo
    {
        return $this->belongsTo(InspectionPolicy::class, 'inspection_policy_id');
    }
}
