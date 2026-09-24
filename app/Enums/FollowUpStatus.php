<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum FollowUpStatus: string implements HasColor, HasLabel
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case Done = 'done';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Open => 'Terbuka / Belum Ditangani',
            self::InProgress => 'Sedang Ditangani',
            self::Done => 'Selesai Ditangani',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Open => 'danger',
            self::InProgress => 'warning',
            self::Done => 'success',
        };
    }
}
