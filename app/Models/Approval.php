<?php

namespace App\Models;

use App\Enums\ApprovalDecision;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable([
    'approvable_type',
    'approvable_id',
    'step',
    'approver_id',
    'decision',
    'notes',
    'decided_at',
])]
class Approval extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'step' => 'integer',
            'decision' => ApprovalDecision::class,
            'decided_at' => 'datetime',
        ];
    }

    public function approvable(): MorphTo
    {
        return $this->morphTo();
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
