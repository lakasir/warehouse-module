<?php

namespace Modules\Warehouse\Extends\Product;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Lakasir\LakasirModule\Forms\ModuleForm;
use Modules\Warehouse\Filament\Resources\RawMaterialResource;
use Modules\Warehouse\Models\ProductionRecipeItem;
use Modules\Warehouse\Models\RawMaterial;

class Form extends ModuleForm
{
    public static function getTitle(): string
    {
        return __('Warehouse::app.form.title');
    }

    public static function fill(array $data): array
    {
        $productionRecipeItems = ProductionRecipeItem::whereProductId(self::$record->id)->get();

        $data = array_merge($data, [
            getPackageModuleName('Warehouse') => [
                'recipes' => array_map(fn ($recipeItem) => [
                    'raw_material_id' => $recipeItem['raw_material_id'],
                    'quantity_required' => $recipeItem['quantity_required'],
                ], $productionRecipeItems->toArray()),
            ],
        ]);

        return $data;
    }

    public static function make(): array
    {
        return [
            Repeater::make('recipes')
                ->label(__('Warehouse::app.form.recipes'))
                ->default([
                    ['raw_material_id' => 1, 'quantity_required' => 0.2],
                ])
                ->schema([
                    Split::make([
                        Select::make('raw_material_id')
                            ->live(debounce: 500)
                            ->searchable()
                            ->options(RawMaterial::pluck('name', 'id'))
                            ->createOptionForm(RawMaterialResource::getForm())
                            ->createOptionUsing(function (array $data) {
                                return RawMaterial::create($data)->id;
                            }),
                        TextInput::make('quantity_required')
                            ->suffix(function (Get $get) {
                                $unit = RawMaterial::query()
                                    ->find($get('raw_material_id'));

                                return str($unit?->unit)?->upper();
                            })
                            ->numeric()
                            ->required(),
                    ]),
                ]),
        ];
    }
}
