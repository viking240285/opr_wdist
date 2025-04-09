<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RiskAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'risk_map_id',
        'hazard_id',
        'probability',
        'severity',
        'exposure',
        'calculated_risk',
        'risk_category',
    ];

    protected $casts = [
        'probability' => 'float',
        'severity' => 'float',
        'exposure' => 'float',
        'calculated_risk' => 'float',
    ];

    public function riskMap(): BelongsTo
    {
        return $this->belongsTo(RiskMap::class);
    }

    public function hazard(): BelongsTo
    {
        return $this->belongsTo(Hazard::class);
    }

    // Measures associated with this specific assessment
    public function measures(): BelongsToMany
    {
        return $this->belongsToMany(Measure::class, 'assessment_measure')
            ->withPivot('measure_type'); // Include the type (existing/planned)
        // ->withTimestamps(); // Add if pivot table has timestamps
    }

    // Helper methods to get existing/planned measures directly
    public function existingMeasures(): BelongsToMany
    {
        return $this->measures()->wherePivot('measure_type', 'existing');
    }

    public function plannedMeasures(): BelongsToMany
    {
        return $this->measures()->wherePivot('measure_type', 'planned');
    }
}
