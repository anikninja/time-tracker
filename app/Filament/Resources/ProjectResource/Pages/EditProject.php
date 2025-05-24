<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Enums\RolesEnum;
use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function authorizeAccess(): void
    {
        $user = Auth::user();
        if ($user->hasRole(RolesEnum::Freelancer)) {
            abort(403, 'You do not have permission to edit this project.');
        }
        // Check if the user is the client of the project
        if ($this->record->client_id !== $user->id) {
            abort(403, 'You do not have permission to edit this project.');
        }
    }
}
