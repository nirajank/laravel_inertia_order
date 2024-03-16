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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('variant_id')->nullable();
            $table->string('name')->nullable();
            $table->bigInteger('product_id')->nullable();
            $table->bigInteger('customer_id')->nullable();
            $table->integer('quantity')->nullable();
            $table->bigInteger('taxed_price')->nullable();
            $table->json('shipping')->nullable();
            $table->bigInteger('item_id')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};