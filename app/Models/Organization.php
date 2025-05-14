<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Organization extends Model
{
    protected $fillable = [
        'name',
        'inn'
    ];

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    public function contacts(): MorphMany
    {
        return $this->morphMany(Contact::class, 'contactable');
    }
} 