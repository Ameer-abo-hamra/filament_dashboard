<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cart_item', function (Blueprint $table) {
            $table->id(); // مفتاح أساسي
            $table->unsignedBigInteger('cart_id'); // مفتاح أجنبي يشير إلى جدول carts
            $table->unsignedBigInteger('item_id'); // مفتاح أجنبي يشير إلى جدول items
            $table->boolean('check_out')->default(false); // حالة الشراء
            $table->double('price'); // سعر العنصر
            $table->integer("amount") ;
            $table->tinyInteger("Minimum_sale");
            $table->timestamps(); // التواريخ
            $table->foreign('cart_id')->references('id')->on('carts')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_item');
    }
};
