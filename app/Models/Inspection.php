<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inspection extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'outlet_id',
        'reference_no',
        'inspection_date',
        'audit_time',
        'auditor_id',
        'status',
        'notes',
        'reporter_name',
        'parent_inspection_id',
    ];

    protected $casts = [
        'inspection_date' => 'date',
    ];

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function auditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auditor_id');
    }

    /**
     * All auditors on this inspection (many-to-many via pivot).
     */
    public function auditors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'inspection_auditors');
    }

    /**
     * Returns a comma-separated list of all auditor names.
     */
    public function getAuditorNamesAttribute(): string
    {
        return $this->auditors->pluck('name')->implode(', ');
    }

    public function findings(): HasMany
    {
        return $this->hasMany(Finding::class);
    }

    public function openFindings(): HasMany
    {
        return $this->hasMany(Finding::class)->where('status', 'open');
    }

    public function closedFindings(): HasMany
    {
        return $this->hasMany(Finding::class)->where('status', 'closed');
    }

    public function categoryStatuses(): HasMany
    {
        return $this->hasMany(InspectionCategoryStatus::class);
    }

    public function parentInspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class, 'parent_inspection_id');
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(Inspection::class, 'parent_inspection_id');
    }

    public function notCompliedFindings(): HasMany
    {
        return $this->hasMany(Finding::class)->where('verification_status', 'not_complied');
    }

    public static function generateReferenceNo(): string
    {
        $year  = now()->year;
        $count = static::whereYear('created_at', $year)->count() + 1;
        return 'FC-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
