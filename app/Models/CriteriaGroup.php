<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CriteriaGroup extends Model
{
    protected $fillable = [
        'code',
        'name',
        'template_id'
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    public function criteria(): HasMany
    {
        return $this->hasMany(Criterion::class);
    }
} 