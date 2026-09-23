<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'keyword',
    'result_count',
    'searched_at',
])]
class SearchLog extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'result_count' => 'integer',
            'searched_at' => 'datetime',
        ];
    }
}
