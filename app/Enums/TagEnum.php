<?php

namespace App\Enums;

enum TagEnum: string
{
    case BILLABLE = 'billable';
    case NON_BILLABLE = 'non_billable';

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::BILLABLE => 'Billable',
            self::NON_BILLABLE => 'Non Billable',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::BILLABLE => 'heroicon-o-check-circle',
            self::NON_BILLABLE => 'heroicon-o-x-circle',
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::BILLABLE => 'success',
            self::NON_BILLABLE => 'danger',
        };
    }
}
