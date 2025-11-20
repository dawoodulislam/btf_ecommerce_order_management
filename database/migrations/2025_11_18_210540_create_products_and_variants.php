<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function(Blueprint $table){
            $table->id();
            $table->string('sku')->unique();
            $table->string('title')->index();
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->foreignId('vendor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('active')->default(true);
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->fullText(['title', 'description']); // fulltext index for search
        });

        Schema::create('product_variants', function(Blueprint $table){
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('sku')->unique();
            $table->string('name'); // e.g., Size L, Color Red
            $table->decimal('price', 12, 2)->nullable();
            $table->integer('position')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('inventories', function(Blueprint $table){
            $table->id();
            $table->foreignId('product_variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->bigInteger('quantity')->default(0)->index();
            $table->bigInteger('reserved')->default(0);
            $table->timestamps();
        });

        // Add index for searching SKU
        Schema::table('product_variants', function(Blueprint $table){
            $table->index('sku');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('products');
    }
};
