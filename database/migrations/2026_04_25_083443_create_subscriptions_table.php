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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('original_transaction_id');
            $table->string('product_id')->nullable();
            $table->string('entitlement')->nullable();
            $table->string('status');
            $table->string('currency')->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->decimal('price_in_purchased_currency', 8, 2)->nullable();
            $table->datetime('purchase_date');
            $table->boolean('is_active')->default(false);
            $table->datetime('expires_date');
            $table->string('environment')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
