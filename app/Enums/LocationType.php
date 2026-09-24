<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum LocationType: string implements HasColor, HasLabel
{
    case PickupPoint = 'pickup_point';
    case Tps = 'tps';
    case Tpst = 'tpst';
    case Tpa = 'tpa';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PickupPoint => 'Titik Jemput (Pickup Point)',
            self::Tps => 'TPS (Tempat Penampungan Sementara)',
            self::Tpst => 'TPST (TPS Terpadu)',
            self::Tpa => 'TPA (Tempat Pemrosesan Akhir)',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PickupPoint => 'gray',
            self::Tps => 'info',
            self::Tpst => 'warning',
            self::Tpa => 'success',
        };
    }
}
