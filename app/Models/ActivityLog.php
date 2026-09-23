<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable([
    'log_name',
    'description',
    'subject_type',
    'subject_id',
    'event',
    'causer_type',
    'causer_id',
    'properties',
    'batch_uuid',
])]
class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'activity_log';

    protected function casts(): array
    {
        return [
            'properties' => 'array',
        ];
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function causer(): MorphTo
    {
        return $this->morphTo();
    }
}
