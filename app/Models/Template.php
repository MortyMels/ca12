<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Template extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description'
    ];

    public function criteriaGroups(): HasMany
    {
        return $this->hasMany(CriteriaGroup::class);
    }
} 