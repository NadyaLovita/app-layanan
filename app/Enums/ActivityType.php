<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ActivityType: string implements HasColor, HasLabel
{
    case Planned = 'planned';
    case Incidental = 'incidental';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Planned => 'Terencana',
            self::Incidental => 'Insidental / Aktual',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Planned => 'info',
            self::Incidental => 'warning',
        };
    }
}
