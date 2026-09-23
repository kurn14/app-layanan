<?php

namespace App\Models;

use App\Enums\ComplaintAttachmentType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'complaint_id',
    'file_path',
    'type',
])]
class ComplaintAttachment extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'type' => ComplaintAttachmentType::class,
        ];
    }

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }
}
