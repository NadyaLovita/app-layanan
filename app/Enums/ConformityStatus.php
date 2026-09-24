<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ConformityStatus: string implements HasColor, HasLabel
{
    case AsPlanned = 'as_planned';
    case Changed = 'changed';
    case NotExecuted = 'not_executed';
    case Unplanned = 'unplanned';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::AsPlanned => 'Sesuai Rencana',
            self::Changed => 'Berubah dari Rencana',
            self::NotExecuted => 'Tidak Terlaksana',
            self::Unplanned => 'Insidental / Tanpa Rencana',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::AsPlanned => 'success',
            self::Changed => 'warning',
            self::NotExecuted => 'danger',
            self::Unplanned => 'info',
        };
    }
}
