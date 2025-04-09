<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'inn',
        'kpp',
        'logo',
        'address',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    // Workplaces belonging to this organization (potentially through departments/positions)
    // This provides a direct way to get all workplaces for an organization
    public function workplaces(): HasMany
    {
        // Direct relationship if workplaces table had organization_id
        // return $this->hasMany(Workplace::class);

        // Indirect relationship through departments
        return $this->hasManyThrough(Workplace::class, Department::class);
    }
}
