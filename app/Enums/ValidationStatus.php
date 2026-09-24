<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ValidationStatus: string implements HasColor, HasLabel
{
    case Pending = 'pending';
    case Valid = 'valid';
    case NeedsRevision = 'needs_revision';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Pending => 'Menunggu Validasi',
            self::Valid => 'Valid',
            self::NeedsRevision => 'Perlu Revisi',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Valid => 'success',
            self::NeedsRevision => 'danger',
        };
    }
}
