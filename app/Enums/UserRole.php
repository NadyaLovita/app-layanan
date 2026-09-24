<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum UserRole: string implements HasColor, HasLabel
{
    case Admin = 'admin';
    case Officer = 'officer';
    case Driver = 'driver';
    case Coordinator = 'coordinator';
    case Head = 'head';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Admin => 'Admin Sistem',
            self::Officer => 'Petugas / Operator Persampahan',
            self::Driver => 'Pengemudi / Operator Armada',
            self::Coordinator => 'Koordinator / Pengawas',
            self::Head => 'Pimpinan',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Admin => 'danger',
            self::Officer => 'info',
            self::Driver => 'warning',
            self::Coordinator => 'primary',
            self::Head => 'success',
        };
    }
}
