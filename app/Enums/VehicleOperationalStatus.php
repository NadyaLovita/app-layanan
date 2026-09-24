<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum VehicleOperationalStatus: string implements HasColor, HasLabel
{
    case Active = 'active';
    case Damaged = 'damaged';
    case Inactive = 'inactive';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Active => 'Aktif / Siap Operasi',
            self::Damaged => 'Rusak / Perbaikan',
            self::Inactive => 'Tidak Aktif',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Active => 'success',
            self::Damaged => 'danger',
            self::Inactive => 'gray',
        };
    }
}
