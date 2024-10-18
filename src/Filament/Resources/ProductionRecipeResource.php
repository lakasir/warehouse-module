<?php

namespace Modules\Warehouse\Filament\Resources;

use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Lakasir\LakasirModule\Abstracts\ModuleResource;
use Modules\Warehouse\Filament\Resources\ProductionRecipeResource\Pages;
use Modules\Warehouse\Models\Product;

class ProductionRecipeResource extends ModuleResource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getLabel(): ?string
    {
        return __('Warehouse::app.production_recipe.title');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function () {
                return Product::query()->whereHas('productionRecipeItems');
            })
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->translateLabel(),
            ])
            ->actions([
                Action::make('production')
                    ->icon('heroicon-s-megaphone')
                    ->label(__('Warehouse::app.production_recipe.production')),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductionRecipes::route('/'),
            'create' => Pages\CreateProductionRecipe::route('/create'),
            'view' => Pages\ViewProductionRecipe::route('/{record}'),
            'edit' => Pages\EditProductionRecipe::route('/{record}/edit'),
        ];
    }
}
