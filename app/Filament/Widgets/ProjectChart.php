<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use App\Enums\RolesEnum;
use App\Models\Project;
use App\Models\ProjectLogs;


class ProjectChart extends ChartWidget
{
    protected static ?string $heading = 'Project Progress';
    protected int | string | array $columnSpan = 'full';
    protected static ?string $pollingInterval = '10s';
    protected static ?string $maxWidth = '100%';
    protected static ?string $maxHeight = '400px';

    public static function canView(): bool
    {
        return false;
    }
    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Project Duration',
                    'data' => Project::getMonthlyDurations(),
                ],
            ],
            'labels' => array_keys(Project::getMonthlyDurations()),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
