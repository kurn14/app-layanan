<?php

namespace App\Enums;

enum ReferralStatus: string
{
    case DRAFT = 'draft';
    case SENT = 'sent';
    case ACCEPTED = 'accepted';
    case IN_SERVICE = 'in_service';
    case COMPLETED = 'completed';
    case DECLINED = 'declined';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draf Rujukan',
            self::SENT => 'Terkirim',
            self::ACCEPTED => 'Diterima Lembaga Rujukan',
            self::IN_SERVICE => 'Dalam Pelayanan Lembaga',
            self::COMPLETED => 'Pelayanan Selesai',
            self::DECLINED => 'Ditolak',
            self::CANCELLED => 'Dibatalkan',
        };
    }
}
