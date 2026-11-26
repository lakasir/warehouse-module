<?php

namespace Modules\Warehouse\Filament\Resources\ProductionRecipeResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Modules\Warehouse\Filament\Actions\ProductionAction;
use Modules\Warehouse\Filament\Resources\ProductionRecipeResource;
use Modules\Warehouse\Filament\Resources\ProductionRecipeResource\RelationManagers\ProductionBathcesRelationManager;
use Modules\Warehouse\Filament\Resources\ProductionRecipeResource\RelationManagers\ProductionRecipeItemsRelationManager;

class ViewProductionRecipe extends ViewRecord
{
    protected static string $resource = ProductionRecipeResource::class;

    public function getTitle(): string|Htmlable
    {
        return $this->record->name;
    }

    public function getSubheading(): string|Htmlable|null
    {
        return $this->record->product?->name;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('rename')
                ->label(__('Rename'))
                ->icon('heroicon-o-pencil-square')
                ->form([
                    TextInput::make('name')
                        ->label(__('Warehouse::app.production_recipe.name'))
                        ->default(fn () => $this->record->name)
                        ->rule(function () {
                            $record = $this->record;

                            return function (string $attribute, $value, \Closure $fail) use ($record) {
                                if (ProductionRecipe::nameIsTaken((int) $record->product_id, $value, (int) $record->getKey())) {
                                    $fail(__('Warehouse::app.production_recipe.name_taken'));
                                }
                            };
                        })
                        ->required()
                        ->maxLength(255),
                ])
                ->action(fn (array $data) => $this->record->update(['name' => $data['name']])),
            ProductionAction::make(Action::class),
            DeleteAction::make(),
        ];
    }

    public function getRelationManagers(): array
    {
        return [
            ProductionRecipeItemsRelationManager::class,
            ProductionBathcesRelationManager::class,
        ];
    }
}
