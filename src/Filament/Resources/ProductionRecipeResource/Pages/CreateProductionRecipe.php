<?php

namespace Modules\Warehouse\Filament\Resources\ProductionRecipeResource\Pages;

use App\Filament\Tenant\Resources\Traits\RedirectToIndex;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Modules\Warehouse\Filament\Resources\ProductionRecipeResource;
use Modules\Warehouse\Filament\Resources\ProductionRecipeResource\RelationManagers\ProductionRecipeItemsRelationManager;
use Modules\Warehouse\Models\Product;
use Modules\Warehouse\Models\ProductionRecipe;
use Modules\Warehouse\Services\ProductionRecipeService;

class CreateProductionRecipe extends CreateRecord
{
    use RedirectToIndex;

    protected static string $resource = ProductionRecipeResource::class;

    protected static bool $canCreateAnother = false;

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label(__('Warehouse::app.production_recipe.name'))
                ->placeholder(__('Warehouse::app.production_recipe.name_placeholder'))
                ->rule(fn (Get $get) => function (string $attribute, $value, \Closure $fail) use ($get) {
                    $productId = $get('product_id');

                    if ($productId && ProductionRecipe::nameIsTaken((int) $productId, $value)) {
                        $fail(__('Warehouse::app.production_recipe.name_taken'));
                    }
                })
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),
            Select::make('product_id')
                ->label(__('Product'))
                ->options(fn () => Product::query()->orderBy('name')->pluck('name', 'id'))
                ->searchable()
                ->required()
                ->columnSpanFull(),
            Repeater::make('raw_materials')
                ->label(__('Warehouse::app.form.recipes'))
                ->default([])
                ->addActionLabel(__('Add to recipes'))
                ->minItems(1)
                ->required()
                ->distinct()
                ->schema(ProductionRecipeItemsRelationManager::getDefaultForm())
                ->columnSpanFull(),
        ]);
    }

    protected function handleRecordCreation(array $data): Model
    {
        $rows = array_values(array_filter(
            $data['raw_materials'] ?? [],
            fn ($row) => ! empty($row['raw_material_id']),
        ));

        return (new ProductionRecipeService)->create(
            (int) $data['product_id'],
            $data['name'],
            $rows,
        );
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('Recipe saved'));
    }
}
