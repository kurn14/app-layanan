<?php

namespace App\Enums;

enum ServiceDocumentVerificationStatus: string
{
    case PENDING = 'pending';
    case VALID = 'valid';
    case REVISION_NEEDED = 'revision_needed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu Verifikasi',
            self::VALID => 'Valid / Sesuai',
            self::REVISION_NEEDED => 'Perlu Perbaikan',
        };
    }
}
