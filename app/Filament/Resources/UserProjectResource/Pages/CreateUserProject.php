<?php

namespace App\Filament\Resources\UserProjectResource\Pages;

use App\Filament\Resources\UserProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use App\Enums\RolesEnum;

class CreateUserProject extends CreateRecord
{
    protected static string $resource = UserProjectResource::class;

    protected function authorizeAccess(): void
    {
        $user = Auth::user();
        if ($user->hasRole(RolesEnum::Freelancer)) {
            abort(403, 'You do not have permission to create project.');
        }
    }
}
