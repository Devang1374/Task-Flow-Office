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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('customer_id');
            $table->string('invoice_number');
            $table->string('order_number');
            $table->date('due_date');
            $table->string('company_name');
            $table->string('company_email');
            $table->string('company_number');
            $table->string('company_address');
            $table->string('terms');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
