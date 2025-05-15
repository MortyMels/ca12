<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mark extends Model
{
    protected $fillable = [
        'audit_visit_id',
        'criteria_group_code',
        'criterion_code',
        'description'
    ];

    public function auditVisit(): BelongsTo
    {
        return $this->belongsTo(AuditVisit::class);
    }

    public function stages(): HasMany
    {
        return $this->hasMany(MarkStage::class);
    }
} 