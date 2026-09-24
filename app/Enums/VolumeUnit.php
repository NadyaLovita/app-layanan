<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum VolumeUnit: string implements HasLabel
{
    case Ton = 'ton';
    case Kg = 'kg';
    case M3 = 'm3';
    case Trip = 'trip';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Ton => 'Ton',
            self::Kg => 'Kilogram (kg)',
            self::M3 => 'Meter Kubik (m³)',
            self::Trip => 'Rit / Trip',
        };
    }
}
