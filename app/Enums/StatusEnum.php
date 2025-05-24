<?php

namespace App\Enums;

enum StatusEnum: string
{
    case ACTIVE = 'active';
    case PENDING = 'pending';
    case COMPLETED = 'completed';

    public static function getValues(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }

    public static function getLabel(string $state): string
    {
        return match ($state) {
            self::ACTIVE->value => 'Active',
            self::PENDING->value => 'Pending',
            self::COMPLETED->value => 'Completed',
        };
    }

    public static function getIcon(string $state): string
    {
        return match ($state) {
            self::ACTIVE->value => 'heroicon-o-check-circle',
            self::PENDING->value => 'heroicon-o-clock',
            self::COMPLETED->value => 'heroicon-o-check-circle',
        };
    }

    public static function getColor(string $state): string
    {
        return match ($state) {
            self::ACTIVE->value => 'info',
            self::PENDING->value => 'warning',
            self::COMPLETED->value => 'success',
        };
    }
}
