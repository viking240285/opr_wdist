<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Measure extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'due_date',
        'responsible_user_id',
        'status',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function responsibleUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
    }

    // Measures associated with RiskAssessments (existing or planned)
    public function riskAssessments(): BelongsToMany
    {
        return $this->belongsToMany(RiskAssessment::class, 'assessment_measure')
            ->withPivot('measure_type') // Include the type (existing/planned)
            ->withTimestamps(); // If pivot table has timestamps
    }
}
