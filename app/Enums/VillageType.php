<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum VillageType: string implements HasLabel
{
    case Village = 'village';
    case UrbanVillage = 'urban_village';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Village => 'Desa',
            self::UrbanVillage => 'Kelurahan',
        };
    }
}
