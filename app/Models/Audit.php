<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Audit extends Model
{
    protected $fillable = [
        'type',
        'template_id',
        'organization_id',
        'branch_id',
        'status',
        'notes'
    ];

    protected $casts = [
        'type' => 'string',
        'status' => 'string'
    ];

    public const TYPE_PLANNED = 'planned';
    public const TYPE_UNPLANNED = 'unplanned';

    public const STATUS_PLANNED = 'planned';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function visits(): HasMany
    {
        return $this->hasMany(AuditVisit::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            self::TYPE_PLANNED => 'Плановый',
            self::TYPE_UNPLANNED => 'Внеплановый',
            default => 'Неизвестный тип'
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PLANNED => 'Запланирован',
            self::STATUS_IN_PROGRESS => 'Проводится',
            self::STATUS_COMPLETED => 'Завершен',
            default => 'Неизвестный статус'
        };
    }
} 