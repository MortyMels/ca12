<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AuditVisit extends Model
{
    protected $fillable = [
        'audit_id',
        'visit_date',
        'type',
        'notes'
    ];

    protected $casts = [
        'visit_date' => 'datetime',
        'type' => 'string'
    ];

    public const TYPE_PRIMARY = 'primary';
    public const TYPE_REPEAT = 'repeat';

    public function audit(): BelongsTo
    {
        return $this->belongsTo(Audit::class);
    }

    public function responsibleUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'audit_visit_responsible_users')
            ->withTimestamps();
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            self::TYPE_PRIMARY => 'Первичный',
            self::TYPE_REPEAT => 'Повторный',
            default => 'Неизвестный тип'
        };
    }
} 