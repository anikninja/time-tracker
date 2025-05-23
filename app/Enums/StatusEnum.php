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

    public function getLabel(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::PENDING => 'Pending',
            self::COMPLETED => 'Completed',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::ACTIVE => 'heroicon-o-check-circle',
            self::PENDING => 'heroicon-o-clock',
            self::COMPLETED => 'heroicon-o-check-circle',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::ACTIVE => 'info',
            self::PENDING => 'warning',
            self::COMPLETED => 'success',
        };
    }
}
