<?php

namespace App\Filament\Resources\MyProjectResource\Pages;

use App\Filament\Resources\MyProjectResource;
use App\Models\Project;
use App\Models\ProjectLogs;
use Filament\Actions;
use Filament\Forms\Components\Actions as ComponentsActions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use App\Enums\StatusEnum;


class EditMyProject extends EditRecord
{
    protected static string $resource = MyProjectResource::class;


    public function getTitle(): string
    {
        return 'Project Logs';
    }

    public function getHeading(): string
    {
        return 'Project Logs';
    }

    public function getSubHeading(): string
    {
        // return new \Illuminate\Support\HtmlString(
        //     view('components.live-time-tracker', [
        //         'initialTime' => ProjectLogs::getDuration($this->record->id),
        //         'projectId' => $this->record->id,
        //         'isTracking' => ProjectLogs::where('project_id', $this->record->id)
        //             ->whereNull('end_time')
        //             ->exists(),
        //     ])
        // );

        return "Total Time Duration: " . ProjectLogs::getDuration($this->record->id);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('startTracking')
                ->label('Start Tracking')
                ->action(function () {
                    ProjectLogs::startTracking($this->record->id);
                })
                ->requiresConfirmation()
                ->color('success')
                ->icon('heroicon-o-play-circle')
                ->hidden(ProjectLogs::shouldHideTracker($this->record->id) || $this->record->status === StatusEnum::COMPLETED->value),
            Actions\Action::make('stopTracking')
                ->label('Stop Tracking')
                ->action(function () {
                    ProjectLogs::stopTracking($this->record->id);
                })
                ->requiresConfirmation()
                ->icon('heroicon-o-stop-circle')
                ->color('danger')
                ->hidden(!ProjectLogs::shouldHideTracker($this->record->id)),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            //
        ];
    }
}
