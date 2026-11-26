<?php

namespace Modules\Warehouse\Filament\Resources\RawMaterialResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Warehouse\Filament\Resources\RawMaterialResource;

class ListRawMaterials extends ListRecords
{
    protected static string $resource = RawMaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
