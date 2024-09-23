<?php

namespace Modules\Warehouse\Filament\Resources;

use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Lakasir\LakasirModule\Abstracts\ModuleResource;
use Modules\Warehouse\Filament\Resources\ProductionRecipeResource\Pages;
use Modules\Warehouse\Models\Product;

class ProductionRecipeResource extends ModuleResource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $label = 'Production Recipe';

    public static function getLabel(): ?string
    {
        return __(self::$label);
    }

    public static function table(Table $table): Table
    {
        return $table
            // ->modifyQueryUsing(function () {
            //     return Product::query()->whereHas('productionRecipes');
            // })
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->translateLabel(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
