<?php

namespace Modules\Warehouse\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductionRecipeItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function productionRecipe(): BelongsTo
    {
        return $this->belongsTo(ProductionRecipe::class);
    }

    public function rawMaterials(): HasMany
    {
        return $this->hasMany(RawMaterial::class);
    }
}
