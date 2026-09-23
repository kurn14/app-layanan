<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

#[Fillable([
    'prefix',
    'period',
    'last_number',
])]
class NumberSequence extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'last_number' => 'integer',
        ];
    }

    /**
     * Generate next ticket number atomically using pessimistic row lock.
     */
    public static function generate(string $prefix, ?string $period = null, int $padding = 5): string
    {
        $period = $period ?? Carbon::now()->format('Ym');

        return DB::transaction(function () use ($prefix, $period, $padding) {
            $sequence = static::where('prefix', $prefix)
                ->where('period', $period)
                ->lockForUpdate()
                ->first();

            if (! $sequence) {
                $sequence = static::create([
                    'prefix' => $prefix,
                    'period' => $period,
                    'last_number' => 0,
                ]);
            }

            $sequence->increment('last_number');
            $nextNumber = str_pad((string) $sequence->last_number, $padding, '0', STR_PAD_LEFT);

            return "{$prefix}-{$period}-{$nextNumber}";
        });
    }
}
