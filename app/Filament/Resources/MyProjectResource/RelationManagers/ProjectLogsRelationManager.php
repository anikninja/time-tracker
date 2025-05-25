<?php

namespace App\Filament\Resources\MyProjectResource\RelationManagers;

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

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DateTimePicker::make('start_time'),
                Forms\Components\DateTimePicker::make('end_time'),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\Select::make('tag')
                    ->label('Tag')
                    ->options(TagEnum::class)
                    ->default('non_billable'),

            ]);
    }

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
            ->poll('5s')
            ->defaultSort('start_time', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }
}
