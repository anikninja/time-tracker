<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use App\Enums\RolesEnum;
use App\Models\Project;

class MyProjectStats extends BaseWidget
{

    protected static ?int $sort = 1;
    protected ?string $heading = "My Project Status";
    protected int | string | array $columnSpan = 'full';
    protected static ?string $pollingInterval = '10s';
    public static function canView(): bool
    {
        return Auth::check() && Auth::user()->hasRole(RolesEnum::Freelancer->value);
    }

    protected function getStats(): array
    {
        return [
            Stat::make(
                'Available Projects',
                Project::query()
                    ->where('freelancer_id', null)
                    ->where('status', 'pending')
                    ->count()
            )->label('Available Projects')
                ->icon('heroicon-o-briefcase')
                ->color('primary'),

            Stat::make(
                'Active Projects',
                Project::query()
                    ->where('status', 'active')
                    ->where('freelancer_id', Auth::user()->id)
                    ->count()
            )->label('Active Projects')
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make(
                'Completed Projects',
                Project::query()
                    ->where('status', 'completed')
                    ->where('freelancer_id', Auth::user()->id)
                    ->count()
            )->label('Completed Projects')
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make(
                'Total Working Time',
                Project::getProjectDurationByUser(Auth::user()->id)
            )->label('Total Working Time')
                ->icon('heroicon-o-clock')
                ->color('warning'),
        ];
    }
}
