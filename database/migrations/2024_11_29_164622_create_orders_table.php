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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->onDelete('cascade'); // المرجعية إلى السلة
            $table->foreignId('sub_id')->references("id")->on("subscribers")->onDelete('cascade'); // المرجعية إلى المستخدم
            $table->double('total'); // إجمالي المبلغ
            $table->tinyInteger('status')->default(0);
            $table->boolean("deletable")->default(1) ;
            $table->boolean("editable")->default(1) ;
            $table->foreignId("updated_by")->nullable()->references("id")->on("admins");
            $table->timestamp('order_date')->default(now()); // تاريخ الطلب
            $table->softDeletes();
            $table->timestamps();
        });
        ;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
