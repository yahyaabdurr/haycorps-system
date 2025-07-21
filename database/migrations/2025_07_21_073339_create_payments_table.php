<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('sk_transaction')->primary();
            $table->uuid('order_id');
            $table->string('transaction_id');
            $table->string('type');
            $table->decimal('amount', 15, 2);
            $table->string('method');
            $table->text('description')->nullable();
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
        Schema::dropIfExists('payments');
    }
};
