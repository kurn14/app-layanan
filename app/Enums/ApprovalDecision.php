<?php

namespace App\Enums;

enum ApprovalDecision: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case RETURNED = 'returned';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu Keputusan',
            self::APPROVED => 'Disetujui',
            self::RETURNED => 'Dikembalikan / Perlu Perbaikan',
        };
    }
}
