<?php

namespace App\Filament\Resources\MyProjectResource\Pages;

use App\Filament\Resources\MyProjectResource;
use App\Models\Project;
use App\Models\ProjectLogs;
use Filament\Actions;
use Filament\Forms\Components\Actions as ComponentsActions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString as BaseHtmlString;
use App\Enums\StatusEnum;



class EditMyProject extends EditRecord
{
    protected static string $resource = MyProjectResource::class;

    public function getHeading(): string
    {
        return 'Project Logs: ' . $this->record->title;
    }

    public function getSubHeading(): string
    {
        return "Total Time Duration: " . ProjectLogs::getDuration($this->record->id);
    }

    protected function getHeaderActions(): array
    {
        $isTracking = ProjectLogs::isTracking($this->record->id);

        return [
            Actions\Action::make('startTracking')
                ->label(fn() => $isTracking
                    ? 'Tracking (' . ProjectLogs::getLiveDuration($this->record->id) . ')'
                    : 'Start Tracking')
                ->action(fn() => !$isTracking
                    ? ProjectLogs::startTracking($this->record->id)
                    : $this->record->refresh())
                ->color(fn() => $isTracking ? 'warning' : 'success')
                ->icon(fn() => $isTracking ? 'heroicon-o-clock' : 'heroicon-o-play-circle')
                ->requiresConfirmation(!$isTracking)
                ->hidden($this->record->status === StatusEnum::COMPLETED->value),
            Actions\Action::make('stopTracking')
                ->label('Stop Tracking')
                ->action(function () {
                    ProjectLogs::stopTracking($this->record->id);
                })
                ->requiresConfirmation()
                ->icon('heroicon-o-stop-circle')
                ->color('danger')
                ->hidden(!ProjectLogs::shouldHideTracker($this->record->id)),
            Actions\ViewAction::make()
                ->label('View Details')
                ->icon('heroicon-o-eye'),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('completeProject')
                ->label('Complete This Project')
                ->action(function () {
                    $this->record->update(['status' => StatusEnum::COMPLETED->value]);
                })
                ->requiresConfirmation()
                ->color(color: 'primary')
                ->icon('heroicon-o-check-circle')
                ->tooltip('Warning: This action will mark the project as completed and cannot be undone.')
                ->hidden($this->record->status === StatusEnum::COMPLETED->value),
        ];
    }
}
