<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  
     public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->uuid('sk_customer')->primary();
            $table->string('customer_id')->unique();
            $table->string('customer_name');
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->text('address1')->nullable();
            $table->text('address2')->nullable();
            $table->string('institution')->nullable();
            $table->boolean('active')->default(true);
            $table->string('created_by');
            $table->string('last_modified_by');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
