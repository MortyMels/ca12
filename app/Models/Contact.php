<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Contact extends Model
{
    protected $fillable = [
        'last_name',
        'first_name',
        'middle_name',
        'position',
        'phone',
        'email',
        'comment',
        'contactable_id',
        'contactable_type'
    ];

    public function contactable(): MorphTo
    {
        return $this->morphTo();
    }
} 