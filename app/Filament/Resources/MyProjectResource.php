<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MyProjectResource\Pages;
use App\Filament\Resources\MyProjectResource\RelationManagers;
use App\Models\Project;
use App\Models\ProjectLogs;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use App\Enums\RolesEnum;
use App\Enums\StatusEnum;
use App\Models\User;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class MyProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'My Projects';
    protected static ?string $slug = 'my-projects';
    protected static ?string $label = 'My Project';

    static ?int $navigationSort = 3;

    public static function canViewAny(): bool
    {
        return Auth::check() && Auth::user()->hasRole(RolesEnum::Freelancer);
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Project::query()->where('freelancer_id', Auth::id())
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    protected static function getTableQuery()
    {
        $user = Auth::user();

        if ($user) {
            return Project::query()->where('freelancer_id', Auth::id());
        }

        return Project::query()->whereRaw('1 = 0');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(self::getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn($state) => StatusEnum::getLabel($state))
                    ->color(fn($state) => StatusEnum::getColor($state))
                    ->icon(fn($state) => StatusEnum::getIcon($state)),
                Tables\Columns\TextColumn::make('deadline')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('client_id')
                    ->label(label: 'Client')
                    ->getStateUsing(function (Project $record) {
                        return User::find($record->client_id)?->name;
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration')
                    ->label('Total Duration')
                    ->badge()
                    ->color('success')
                    ->getStateUsing(function (Project $record) {
                        return ProjectLogs::getDuration($record->id);
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make(name: 'Start')
                    ->action(fn(Project $record) => ProjectLogs::startTracking($record->id))
                    ->requiresConfirmation()
                    ->label('Start')
                    ->button()
                    ->icon('heroicon-o-play-circle')
                    ->color('success')
                    ->hidden(fn(Project $record) => ProjectLogs::shouldHideTracker($record->id) || $record->status === StatusEnum::COMPLETED->value),
                Tables\Actions\Action::make(name: 'Stop')
                    ->action(fn(Project $record) => ProjectLogs::stopTracking($record->id))
                    ->requiresConfirmation()
                    ->label('Stop')
                    ->button()
                    ->icon('heroicon-o-stop-circle')
                    ->color('danger')
                    ->hidden(fn(Project $record) => !ProjectLogs::shouldHideTracker($record->id)),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\Action::make(name: 'complete')
                        ->action(function (Project $record) {
                            $record->update(['status' => StatusEnum::COMPLETED]);
                        })
                        ->requiresConfirmation()
                        ->label('Complete Project')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->hidden(fn(Project $record) => $record->status === StatusEnum::COMPLETED->value),
                ])->icon('heroicon-o-ellipsis-horizontal')
                    ->iconPosition('after')
                    ->color('secondary')
                    ->tooltip('More Actions'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make(name: 'complete')
                        ->action(function (array $records) {
                            foreach ($records as $record) {
                                $record->update(['status' => StatusEnum::COMPLETED]);
                            }
                        })
                        ->requiresConfirmation()
                        ->label('Complete Project'),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Split::make([
                    Infolists\Components\Section::make([
                        Infolists\Components\Grid::make(2)->schema([
                            Infolists\Components\TextEntry::make('title')
                                ->label('Project Title'),
                            Infolists\Components\TextEntry::make('description')
                                ->label('Project Description'),
                            Infolists\Components\TextEntry::make('status')
                                ->label('Status')
                                ->badge()
                                ->formatStateUsing(fn($state) => StatusEnum::getLabel($state))
                                ->color(fn($state) => StatusEnum::getColor($state))
                                ->icon(fn($state) => StatusEnum::getIcon($state)),

                            Infolists\Components\TextEntry::make('duration')
                                ->label('Total Duration')
                                ->badge()
                                ->color('success')
                                ->getStateUsing(function (Project $record) {
                                    $duration = ProjectLogs::getDuration($record->id);
                                    if (ProjectLogs::isTracking($record->id)) {
                                        $liveDuration = ProjectLogs::getLiveDuration($record->id);
                                        return $duration . " + Tracking ({$liveDuration})";
                                    }
                                    return $duration;
                                }),
                        ]),
                    ]),
                    Infolists\Components\Section::make([
                        Infolists\Components\TextEntry::make('created_at')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('deadline')
                            ->label('Deadline')
                            ->color('danger')
                            ->getStateUsing(fn(Project $record) => $record->deadline->format('F j, Y, g:i a')),
                        Infolists\Components\TextEntry::make('client_id')
                            ->label('Client')
                            ->getStateUsing(function (Project $record) {
                                return User::find($record->client_id)?->name;
                            }),

                    ])->grow(false),
                ])->columnSpan(2),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ProjectLogsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyProjects::route('/'),
            'create' => Pages\CreateMyProject::route('/create'),
            'edit' => Pages\EditMyProject::route('/{record}/edit'),
        ];
    }
}
