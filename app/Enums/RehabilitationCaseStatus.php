<?php

namespace App\Enums;

enum RehabilitationCaseStatus: string
{
    case RECEIVED = 'received';
    case ASSESSMENT = 'assessment';
    case SERVICE_PLANNING = 'service_planning';
    case IN_SERVICE = 'in_service';
    case MONITORING = 'monitoring';
    case CLOSED = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::RECEIVED => 'Kasus Diterima',
            self::ASSESSMENT => 'Dalam Assessment',
            self::SERVICE_PLANNING => 'Rencana Pelayanan',
            self::IN_SERVICE => 'Dalam Pelayanan',
            self::MONITORING => 'Dalam Monitoring',
            self::CLOSED => 'Kasus Ditutup',
        };
    }
}
