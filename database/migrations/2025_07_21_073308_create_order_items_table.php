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
        Schema::create('order_items', function (Blueprint $table) {
            $table->uuid('sk_order_details')->primary();
            $table->uuid('order_id');
            $table->string('item_name');
            $table->text('description')->nullable();
            $table->decimal('item_price', 15, 2);
            $table->integer('number_of_item');
            $table->boolean('active')->default(true);
            $table->string('created_by');
            $table->string('last_modified_by');
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('order_id')
                  ->references('sk_order')
                  ->on('orders')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
