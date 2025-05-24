<?php

namespace App\Filament\Resources\MyProjectResource\Pages;

use App\Filament\Resources\MyProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use App\Enums\RolesEnum;

class CreateMyProject extends CreateRecord
{
    protected static string $resource = MyProjectResource::class;

    protected function authorizeAccess(): void
    {
        $user = Auth::user();
        if ($user->hasRole(RolesEnum::Freelancer)) {
            abort(403, 'You do not have permission to create project.');
        }
    }
}
