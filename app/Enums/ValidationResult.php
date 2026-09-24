<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ValidationResult: string implements HasColor, HasLabel
{
    case Valid = 'valid';
    case NeedsRevision = 'needs_revision';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Valid => 'Valid',
            self::NeedsRevision => 'Perlu Revisi',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Valid => 'success',
            self::NeedsRevision => 'danger',
        };
    }
}
