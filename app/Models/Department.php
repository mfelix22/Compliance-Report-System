<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'description'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'department_user');
    }

    public function findings(): HasMany
    {
        return $this->hasMany(Finding::class);
    }
}
