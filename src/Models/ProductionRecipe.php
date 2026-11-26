<?php

namespace Modules\Warehouse\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductionRecipe extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProductionRecipeItem::class);
    }

    public function productionBatches(): HasMany
    {
        return $this->hasMany(ProductionBatch::class);
    }

    public static function nameIsTaken(int $productId, string $name, ?int $ignoreId = null): bool
    {
        return self::query()
            ->where('product_id', $productId)
            ->where('name', $name)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists();
    }

    /**
     * Cost of the recipe for one produced unit, in the product's own cost base.
     */
    public function costPerUnit(): float
    {
        $this->loadMissing('items.rawMaterial');

        return $this->items->sum(fn (ProductionRecipeItem $item) => $item->rawMaterial?->costFor(
            (float) $item->quantity_required,
            $item->quantity_unit ?? $item->rawMaterial->base_unit,
        ) ?? 0);
    }
}
