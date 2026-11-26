<?php

namespace Modules\Warehouse\Filament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Lakasir\LakasirModule\Abstracts\ModuleResource;
use Modules\Warehouse\Filament\Actions\ProductionAction;
use Modules\Warehouse\Filament\Resources\ProductionRecipeResource\Pages;
use Modules\Warehouse\Filament\Resources\ProductionRecipeResource\RelationManagers\ProductionBathcesRelationManager;
use Modules\Warehouse\Filament\Resources\ProductionRecipeResource\RelationManagers\ProductionRecipeItemsRelationManager;
use Modules\Warehouse\Models\ProductionRecipe;
use Modules\Warehouse\Services\ProductionRecipeService;

class ProductionRecipeResource extends ModuleResource
{
    protected static ?string $model = ProductionRecipe::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getLabel(): ?string
    {
        return __('Warehouse::app.production_recipe.title');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->translateLabel(),
                TextColumn::make('product.name')
                    ->searchable()
                    ->translateLabel(),
                TextColumn::make('items_count')
                    ->counts('items')
                    ->label(__('Warehouse::app.production_recipe.items_count')),
                TextColumn::make('cost_per_unit')
                    ->getStateUsing(fn (ProductionRecipe $record) => price_format($record->costPerUnit()))
                    ->label(__('Warehouse::app.production_recipe.cost_per_unit')),
            ])
            ->actions([
                ViewAction::make(),
                Action::make('duplicate')
                    ->label(__('Duplicate'))
                    ->icon('heroicon-o-document-duplicate')
                    ->form(fn (ProductionRecipe $record) => [
                        TextInput::make('name')
                            ->label(__('Warehouse::app.production_recipe.name'))
                            ->default($record->name.' (copy)')
                            ->rule(fn () => function (string $attribute, $value, \Closure $fail) use ($record) {
                                if (ProductionRecipe::nameIsTaken((int) $record->product_id, $value)) {
                                    $fail(__('Warehouse::app.production_recipe.name_taken'));
                                }
                            })
                            ->required()
                            ->maxLength(255),
                    ])
                    ->action(function (ProductionRecipe $record, array $data) {
                        $copy = (new ProductionRecipeService)->duplicate($record, $data['name']);

                        Notification::make()->success()->title(__('Warehouse::app.production_recipe.duplicated'))->send();

                        return redirect(ProductionRecipeResource::getUrl('view', ['record' => $copy]));
                    }),
                ProductionAction::make(Action::class),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([]);
    }

    public static function getRelations(): array
    {
        return [
            ProductionRecipeItemsRelationManager::class,
            ProductionBathcesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductionRecipes::route('/'),
            'create' => Pages\CreateProductionRecipe::route('/create'),
            'view' => Pages\ViewProductionRecipe::route('/{record}'),
        ];
    }
}
