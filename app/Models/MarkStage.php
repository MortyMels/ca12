<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarkStage extends Model
{
    protected $fillable = [
        'mark_id',
        'status',
        'fixation_date',
        'regulation_date',
        'state'
    ];

    protected $casts = [
        'fixation_date' => 'datetime',
        'regulation_date' => 'date'
    ];

    public const STATUS_CORRESPONDS = 'corresponds';
    public const STATUS_PARTIALLY = 'partially';
    public const STATUS_NOT_CORRESPONDS = 'not_corresponds';
    public const STATUS_NEEDS_CLARIFICATION = 'needs_clarification';
    public const STATUS_NOT_APPLICABLE = 'not_applicable';

    public function mark(): BelongsTo
    {
        return $this->belongsTo(Mark::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(MarkStagePhoto::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_CORRESPONDS => 'Соответствует',
            self::STATUS_PARTIALLY => 'Частично соответствует',
            self::STATUS_NOT_CORRESPONDS => 'Не соответствует',
            self::STATUS_NEEDS_CLARIFICATION => 'Нужно уточнение',
            self::STATUS_NOT_APPLICABLE => 'Неприменимо',
            default => 'Неизвестный статус'
        };
    }
} 