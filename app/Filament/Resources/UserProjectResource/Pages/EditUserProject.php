<?php

namespace App\Filament\Resources\UserProjectResource\Pages;

use App\Filament\Resources\UserProjectResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use App\Enums\RolesEnum;

class EditUserProject extends EditRecord
{
    protected static string $resource = UserProjectResource::class;

    protected function authorizeAccess(): void
    {
        $user = Auth::user();
        if ($user->hasRole(RolesEnum::Freelancer)) {
            abort(403, 'You do not have permission to edit this project.');
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
