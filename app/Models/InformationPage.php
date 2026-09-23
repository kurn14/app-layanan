<?php

namespace App\Models;

use App\Enums\InformationCategory;
use App\Enums\InformationPublishStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'title',
    'slug',
    'category',
    'service_type_id',
    'description',
    'requirements',
    'procedure',
    'service_hours',
    'location',
    'contact',
    'publish_status',
    'published_at',
    'manager_id',
])]
class InformationPage extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'category' => InformationCategory::class,
            'publish_status' => InformationPublishStatus::class,
            'published_at' => 'datetime',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('publish_status', InformationPublishStatus::PUBLISHED);
    }

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function downloadableForms(): HasMany
    {
        return $this->hasMany(DownloadableForm::class);
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(Faq::class)->orderBy('sort_order');
    }

    public function pageVisits(): HasMany
    {
        return $this->hasMany(PageVisit::class);
    }
}
