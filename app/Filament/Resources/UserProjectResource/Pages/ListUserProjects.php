<?php

namespace App\Filament\Resources\UserProjectResource\Pages;

use App\Filament\Resources\UserProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserProjects extends ListRecords
{
    protected static string $resource = UserProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
