<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Criterion extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'criteria_group_id'
    ];

    public function criteriaGroup(): BelongsTo
    {
        return $this->belongsTo(CriteriaGroup::class);
    }
} 