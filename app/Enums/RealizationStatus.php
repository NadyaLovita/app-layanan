<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum RealizationStatus: string implements HasColor, HasLabel
{
    case Planned = 'planned';
    case Running = 'running';
    case Completed = 'completed';
    case Obstructed = 'obstructed';
    case Delayed = 'delayed';
    case Cancelled = 'cancelled';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Planned => 'Direncanakan',
            self::Running => 'Berangkat / Berjalan',
            self::Completed => 'Selesai',
            self::Obstructed => 'Terkendala',
            self::Delayed => 'Tertunda',
            self::Cancelled => 'Dibatalkan',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Planned => 'gray',
            self::Running => 'info',
            self::Completed => 'success',
            self::Obstructed => 'danger',
            self::Delayed => 'warning',
            self::Cancelled => 'danger',
        };
    }
}
