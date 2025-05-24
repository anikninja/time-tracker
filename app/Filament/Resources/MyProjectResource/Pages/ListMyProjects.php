<?php

namespace App\Filament\Resources\MyProjectResource\Pages;

use App\Filament\Resources\MyProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMyProjects extends ListRecords
{
    protected static string $resource = MyProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
