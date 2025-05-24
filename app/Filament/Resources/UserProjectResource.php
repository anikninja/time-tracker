<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserProjectResource\Pages;
use App\Filament\Resources\UserProjectResource\RelationManagers;
use App\Models\Project;
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

class UserProjectResource extends Resource
{
    protected static ?string $model = Project::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Projects';
    protected static ?string $slug = 'all-projects';

    public static function canViewAny(): bool
    {
        return Auth::check() && Auth::user()->hasRole(RolesEnum::Freelancer);
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Project::query()->where('freelancer_id', null)
            ->where('status', 'pending')
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
            return Project::query()->where('freelancer_id', null)->where('status', 'pending');
        }

        return Project::query()->whereRaw('1 = 0');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(self::getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->disabled(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->searchable()
                    ->disabled(),
                Tables\Columns\TextColumn::make('deadline')
                    ->dateTime()
                    ->sortable()
                    ->disabled(),
                Tables\Columns\TextColumn::make('freelancer_id')
                    ->label(label: 'Client')
                    ->getStateUsing(function (Project $record) {
                        return User::find($record->client_id)?->name;
                    })
                    ->sortable()
                    ->disabled(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('claim')
                    ->action(function (Project $record) {
                        $user = Auth::user();
                        if ($record->freelancer_id === null) {
                            $record->update([
                                'status' => StatusEnum::ACTIVE,
                                'freelancer_id' => $user->id
                            ]);
                        }
                    })
                    ->requiresConfirmation()
                    ->label('Claim Project')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('warning')
                    ->visible(fn(Project $record) => $record->freelancer_id === null),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('claim')
                        ->action(function (array $records) {
                            $user = Auth::user();
                            foreach ($records as $record) {
                                if ($record->freelancer_id === null) {
                                    $record->update([
                                        'status' => StatusEnum::ACTIVE,
                                        'freelancer_id' => $user->id
                                    ]);
                                }
                            }
                        })
                        ->requiresConfirmation()
                        ->label('Claim Project'),
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
                                ->formatStateUsing(fn($state) => ucfirst($state))
                                ->badge()
                                ->color(fn(string $state): string => match ($state) {
                                    StatusEnum::ACTIVE->value => 'success',
                                    StatusEnum::PENDING->value => 'warning',
                                    StatusEnum::COMPLETED->value => 'info',
                                }),
                            Infolists\Components\TextEntry::make(name: 'deadline')
                                ->label('Deadline')
                                ->dateTime(),
                            Infolists\Components\TextEntry::make(name: 'client_id')
                                ->label('Client')
                                ->getStateUsing(function (Project $record) {
                                    return User::find($record->client_id)?->name;
                                }),
                        ]),
                    ]),
                    Infolists\Components\Section::make([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->dateTime(),
                    ])->grow(false),
                ])->columnSpan(2),
            ]);
    }
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserProjects::route('/'),
            'create' => Pages\CreateUserProject::route('/create'),
            'edit' => Pages\EditUserProject::route('/{record}/edit'),
        ];
    }
}
