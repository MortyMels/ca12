<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarkStagePhoto extends Model
{
    protected $fillable = [
        'mark_stage_id',
        'path'
    ];

    public function markStage(): BelongsTo
    {
        return $this->belongsTo(MarkStage::class);
    }
} 