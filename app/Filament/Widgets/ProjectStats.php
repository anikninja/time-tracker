<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use App\Enums\RolesEnum;
use App\Models\Project;

class ProjectStats extends BaseWidget
{

    protected static ?int $sort = 1;
    protected ?string $heading = "My Project Status";
    protected int | string | array $columnSpan = 'full';
    protected static ?string $pollingInterval = '10s';
    public static function canView(): bool
    {
        return Auth::check() && Auth::user()->hasRole(RolesEnum::Client);
    }

    protected function getStats(): array
    {
        return [
            Stat::make(
                'Project Log Time',
                Project::getProjectDurationByUser(Auth::user()->id, false)
            )->label('Project Log Time')
                ->icon('heroicon-o-clock')
                ->color('primary'),

            Stat::make(
                'Active Projects',
                Project::query()
                    ->where('status', 'active')
                    ->where('client_id', Auth::user()->id)
                    ->count()
            )->label('Active Projects')
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make(
                'Completed Projects',
                Project::query()
                    ->where('status', 'completed')
                    ->where('client_id', Auth::user()->id)
                    ->count()
            )->label('Completed Projects')
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make(
                'Pending Projects',
                Project::query()
                    ->where('client_id', Auth::user()->id)
                    ->where('status', 'pending')
                    ->count()
            )->label('Pending Projects')
                ->icon('heroicon-o-briefcase')
                ->color('primary'),

        ];
    }
}
