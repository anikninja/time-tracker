<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use App\Enums\StatusEnum;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use App\Enums\RolesEnum;
use App\Models\User;
use App\Models\ProjectLogs;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Projects';

    protected static ?string $slug = 'projects';

    static ?int $navigationSort = 1;


    public static function canViewAny(): bool
    {
        return Auth::check() && Auth::user()->hasRole(RolesEnum::Client);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Select::make('status')
                    ->options(StatusEnum::class)
                    ->default(StatusEnum::PENDING->value)
                    ->required(),
                Forms\Components\DateTimePicker::make('deadline')
                    ->required()
                    ->minDate(now()->addDay())
                    ->default(fn() => now()->addDays(7)),
                Forms\Components\Hidden::make('client_id')
                    ->required()
                    ->default(fn($state) => $state ?? Auth::user()->id),
            ]);
    }

    // Define the query for the table
    // This will filter projects to only those belonging to the authenticated client
    protected static function getTableQuery()
    {
        $user = Auth::user();

        if ($user) {
            return Project::query()->where('client_id', $user->id);
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
                    ->icon(fn($state) => StatusEnum::getIcon($state))
                    ->searchable(),
                Tables\Columns\TextColumn::make('deadline')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('freelancer_id')
                    ->label('Freelancer')
                    ->getStateUsing(function (Project $record) {
                        return User::find($record->freelancer_id)?->name ?? 'Not Claimed';
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration')
                    ->label('Total Duration')
                    ->badge()
                    ->color(function (Project $record) {
                        return ProjectLogs::isTracking($record->id) ? 'warning' : 'success';
                    })
                    ->getStateUsing(function (Project $record) {
                        $duration = ProjectLogs::getDuration($record->id);
                        if (ProjectLogs::isTracking($record->id)) {
                            $liveDuration = ProjectLogs::getLiveDuration($record->id, true);
                            return $duration . " + Tracking ({$liveDuration})";
                        }
                        return $duration;
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->poll('5s')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
