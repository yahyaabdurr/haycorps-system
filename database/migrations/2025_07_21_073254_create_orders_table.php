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
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('sk_order')->primary();
            $table->string('order_id')->unique();
            $table->string('file_url')->nullable();
            $table->string('pic_employee')->nullable();
            $table->dateTime('order_date');
            $table->dateTime('completion_date')->nullable();
            $table->string('order_status')->default('Pending');
            $table->decimal('total_price', 15, 2)->default(0);
            $table->boolean('active')->default(true);
            $table->uuid('customer_id');
            $table->string('created_by');
            $table->string('last_modified_by');
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('customer_id')
                  ->references('sk_customer')
                  ->on('customers')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
