<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Hazard extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'source',
        'threat',
        'type',
    ];

    // Relationship to RiskAssessments where this hazard is identified
    public function riskAssessments(): HasMany
    {
        return $this->hasMany(RiskAssessment::class);
    }
}
