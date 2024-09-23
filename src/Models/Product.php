<?php

namespace Modules\Warehouse\Models;

use App\Models\Tenants\Product as TenantsProduct;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends TenantsProduct
{
    use HasFactory;

    public function productionRecipes(): HasMany
    {
        return $this->hasMany(ProductionRecipe::class);
    }

    public function rawMaterials(): HasMany
    {
        return $this->hasMany(RawMaterial::class);
    }
}
