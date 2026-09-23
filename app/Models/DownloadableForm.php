<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'information_page_id',
    'name',
    'file_path',
    'version',
    'is_current',
])]
class DownloadableForm extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_current' => 'boolean',
        ];
    }

    public function scopeCurrent(Builder $query): Builder
    {
        return $query->where('is_current', true);
    }

    public function informationPage(): BelongsTo
    {
        return $this->belongsTo(InformationPage::class);
    }
}
