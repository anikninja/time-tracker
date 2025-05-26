<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Enums\TagEnum;
use App\Models\ProjectLogs;

class ProjectLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'ProjectLogs';

    protected static ?string $title = 'Project Logs';

    public function table(Table $table): Table
    {
        return $table
            ->query(ProjectLogs::query()->where('project_id', $this->ownerRecord->id))
            ->emptyStateHeading('No Project Logs Found')
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('start_time'),
                Tables\Columns\TextColumn::make('end_time'),
                Tables\Columns\TextColumn::make('duration')
                    ->label('Duration')
                    ->badge()
                    ->icon('heroicon-o-clock')
                    ->color(fn($state) => $state === '00:00:00' ? 'warning' : 'success')
                    ->formatStateUsing(fn($state) => ProjectLogs::getSingleDuration($state) ?? 'N/A'),
                Tables\Columns\TextColumn::make('tag')
                    ->label('Tag')
                    ->badge()
                    ->icon(fn($state) => TagEnum::getIcon($state))
                    ->color(fn($state) => TagEnum::getColor($state))
                    ->formatStateUsing(fn($state) => TagEnum::getLabel($state)),
            ])
            ->defaultSort('start_time', 'desc')
            ->poll('5s')
            ->filters([
                //
            ])
            ->headerActions([])
            ->actions([
                //
            ])->bulkActions([
                // Define bulk actions if needed
            ])
            ->bulkActions([
                //
            ]);
    }
}
