<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A product can carry several named recipes (e.g. "Potong 12", "Potong 10").
     * Existing items become one recipe per product, named after the product.
     *
     * Items lose their own product_id: the recipe owns the product, and a
     * duplicate column could only drift from it.
     */
    public function up(): void
    {
        if (! Schema::hasTable('production_recipes')) {
            Schema::create('production_recipes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->string('name');
                $table->timestamps();
                $table->unique(['product_id', 'name']);
            });
        }

        if (! Schema::hasColumn('production_recipe_items', 'production_recipe_id')) {
            Schema::table('production_recipe_items', function (Blueprint $table) {
                $table->foreignId('production_recipe_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            });
        }

        if (! Schema::hasColumn('production_batches', 'production_recipe_id')) {
            Schema::table('production_batches', function (Blueprint $table) {
                $table->foreignId('production_recipe_id')->nullable()->after('product_id')->constrained()->nullOnDelete();
            });
        }

        $productIds = DB::table('production_recipe_items')
            ->whereNull('production_recipe_id')
            ->select('product_id')
            ->distinct()
            ->pluck('product_id');

        foreach ($productIds as $productId) {
            $recipeId = DB::table('production_recipes')->insertGetId([
                'product_id' => $productId,
                'name' => DB::table('products')->where('id', $productId)->value('name') ?? 'Recipe',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('production_recipe_items')
                ->where('product_id', $productId)
                ->update(['production_recipe_id' => $recipeId]);

            DB::table('production_batches')
                ->where('product_id', $productId)
                ->whereNull('production_recipe_id')
                ->update(['production_recipe_id' => $recipeId]);
        }

        if (Schema::hasColumn('production_recipe_items', 'product_id')) {
            Schema::table('production_recipe_items', function (Blueprint $table) {
                $table->dropForeign(['product_id']);
                $table->dropUnique('production_recipe_items_product_material_unique');
                $table->unique(['production_recipe_id', 'raw_material_id'], 'production_recipe_items_recipe_material_unique');
                $table->dropColumn('product_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('production_recipe_items', function (Blueprint $table) {
            $table->dropUnique('production_recipe_items_recipe_material_unique');
            $table->foreignId('product_id')->nullable()->after('id');
        });

        DB::statement('update production_recipe_items i join production_recipes r on r.id = i.production_recipe_id set i.product_id = r.product_id');

        Schema::table('production_recipe_items', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->unique(['product_id', 'raw_material_id'], 'production_recipe_items_product_material_unique');
            $table->dropConstrainedForeignId('production_recipe_id');
        });

        Schema::table('production_batches', function (Blueprint $table) {
            $table->dropConstrainedForeignId('production_recipe_id');
        });

        Schema::dropIfExists('production_recipes');
    }
};
