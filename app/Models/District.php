<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'code',
    'name',
])]
class District extends Model
{
    use HasFactory;

    public function villages(): HasMany
    {
        return $this->hasMany(Village::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
