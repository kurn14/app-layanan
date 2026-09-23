<?php

namespace App\Enums;

enum ServiceHandler: string
{
    case GENERIC = 'generic';
    case DTSEN = 'dtsen';
    case PBI = 'pbi';

    public function label(): string
    {
        return match ($this) {
            self::GENERIC => 'Umum',
            self::DTSEN => 'SK DTSEN',
            self::PBI => 'Reaktivasi PBI-JK',
        };
    }
}
