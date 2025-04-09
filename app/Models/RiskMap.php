<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RiskMap extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'workplace_id',
        'assessment_date',
        'commission_members',
        'status',
        'conducted_by_user_id',
        'participants',
    ];

    protected $casts = [
        'assessment_date' => 'date',
        'commission_members' => 'array',
        'participants' => 'array',
    ];

    public function workplace(): BelongsTo
    {
        return $this->belongsTo(Workplace::class);
    }

    public function conductedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'conducted_by_user_id');
    }

    public function riskAssessments(): HasMany
    {
        return $this->hasMany(RiskAssessment::class);
    }
}
