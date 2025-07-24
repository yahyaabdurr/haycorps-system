<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('sk_product')->primary();
            $table->string('product_code')->unique();
            $table->string('product_name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('stock')->default(0);
            $table->string('category')->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('active')->default(true);
            $table->decimal('weight', 8, 2)->nullable()->comment('Weight in grams');
            $table->decimal('volume', 8, 2)->nullable()->comment('Volume in cubic cm');
            $table->decimal('cost', 10, 2)->nullable()->comment('Cost price');
            $table->string('created_by');
            $table->string('last_modified_by');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
