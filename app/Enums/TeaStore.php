<?php

namespace App\Enums;

enum TeaStore: string {
    case TE_CENTRALEN = 'te-centralen';
    case TEKUNGEN = 'tekungen';

    public function label()
    {
        return match($this) {
            self::TE_CENTRALEN => 'tecentralen.se',
            self::TEKUNGEN => 'tekungen.se',
        };
    }

    public function labelColor()
    {
        return match($this) {
            self::TE_CENTRALEN => 'text-red-700',
            self::TEKUNGEN => 'text-green-500',
        };
    }
}
