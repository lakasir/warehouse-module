<?php

namespace Modules\Warehouse\Services;

use Illuminate\Support\Facades\DB;
use Modules\Warehouse\Models\ProductionRecipe;
use Modules\Warehouse\Models\ProductionRecipeItem;

class ProductionRecipeService
{
    /**
     * Create a named recipe for a product with the given ingredient rows.
     *
     * @param  array<int, array{raw_material_id: int|string, quantity_required: int|float|string, quantity_unit?: string|null}>  $rows
     */
    public function create(int $productId, string $name, array $rows): ProductionRecipe
    {
        return DB::transaction(function () use ($productId, $name, $rows) {
            $recipe = ProductionRecipe::query()->create([
                'product_id' => $productId,
                'name' => $name,
            ]);

            $this->syncItems($recipe, $rows);

            return $recipe;
        });
    }

    /**
     * Copy a recipe, ingredients included, under a new name.
     */
    public function duplicate(ProductionRecipe $recipe, string $name): ProductionRecipe
    {
        return DB::transaction(function () use ($recipe, $name) {
            $copy = ProductionRecipe::query()->create([
                'product_id' => $recipe->product_id,
                'name' => $name,
            ]);

            foreach ($recipe->items as $item) {
                ProductionRecipeItem::query()->create([
                    'production_recipe_id' => $copy->id,
                    'raw_material_id' => $item->raw_material_id,
                    'quantity_required' => $item->quantity_required,
                    'quantity_unit' => $item->quantity_unit,
                ]);
            }

            return $copy;
        });
    }

    /**
     * Replace a recipe's ingredients with the given rows.
     *
     * @param  array<int, array{raw_material_id: int|string, quantity_required: int|float|string, quantity_unit?: string|null}>  $rows
     */
    public function syncItems(ProductionRecipe $recipe, array $rows): void
    {
        $recipe->items()->delete();

        foreach ($rows as $row) {
            if (empty($row['raw_material_id'])) {
                continue;
            }

            ProductionRecipeItem::query()->create([
                'production_recipe_id' => $recipe->id,
                'raw_material_id' => $row['raw_material_id'],
                'quantity_required' => (float) ($row['quantity_required'] ?? 0),
                'quantity_unit' => $row['quantity_unit'] ?? null,
            ]);
        }
    }
}
