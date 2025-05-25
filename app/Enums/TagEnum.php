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

    public static function getLabel($state): ?string
    {
        return match ($state) {
            self::BILLABLE->value => 'Billable',
            self::NON_BILLABLE->value => 'Non Billable',
        };
    }

    public static function getIcon($state): ?string
    {
        return match ($state) {
            self::BILLABLE->value => 'heroicon-o-currency-dollar',
            self::NON_BILLABLE->value => 'heroicon-o-no-symbol',
        };
    }

    public static function getColor($state): ?string
    {
        return match ($state) {
            self::BILLABLE->value => 'info',
            self::NON_BILLABLE->value => 'warning',
        };
    }
}
