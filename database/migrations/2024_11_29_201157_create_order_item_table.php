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
        Schema::create('order_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->references("id")->on("orders")->onDelete('cascade'); // المرجعية إلى الطلب
            $table->foreignId('item_id')->references("id")->on("items")->onDelete('cascade'); // المرجعية إلى العنصر
            $table->integer('amount'); // الكمية
            $table->string("name");
            $table->double('price'); // السعر
            $table->tinyInteger("status")->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_item');
    }
};
