<?php

namespace Modules\Warehouse\Models;

use App\Models\Tenants\Product as TenantsProduct;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Product extends TenantsProduct
{
    use HasFactory;

    public function productionRecipes(): HasMany
    {
        return $this->hasMany(ProductionRecipe::class);
    }

    public function productionRecipeItems(): HasManyThrough
    {
        return $this->hasManyThrough(ProductionRecipeItem::class, ProductionRecipe::class, 'product_id', 'production_recipe_id');
    }

    public function productionBatches(): HasMany
    {
        return $this->hasMany(ProductionBatch::class);
    }
}
