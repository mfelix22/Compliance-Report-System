<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InspectionPolicy extends Model
{
    protected $fillable = ['code', 'name', 'due_date_offset_days', 'score', 'sort_order'];

    public function items(): HasMany
    {
        return $this->hasMany(PolicyItem::class)->orderBy('sort_order');
    }

    public function categoryStatuses(): HasMany
    {
        return $this->hasMany(InspectionCategoryStatus::class);
    }

    public function findings(): HasMany
    {
        return $this->hasMany(Finding::class);
    }

    /**
     * Human-readable due-date label, e.g. "D+1"
     */
    public function getDueLabelAttribute(): string
    {
        return 'D+' . $this->due_date_offset_days;
    }
}
