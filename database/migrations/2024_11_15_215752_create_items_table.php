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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId("group_id")->references("id")->on("groups");
            $table->foreignId("category_id")->references("id")->on("categories");
            $table->foreignId("brand_id")->references("id")->on("brands");
            $table->foreignId("coin_id")->references("id")->on("coins");
            $table->string("name");
            $table->text("description")->nullable();
            $table->tinyInteger("is_new")->default(0);
            $table->float('price');
            $table->float(column: 'commission')->default(0);
            $table->unsignedInteger("amount")->nullable();
            $table->float('total')->default(0);
            $table->integer("status")->default(0);
            $table->tinyInteger("Minimum_sale")->default(1);
            $table->text("Reason_rejection")->nullable();
            $table->tinyInteger('star')->default(0);
            $table->tinyInteger("sold")->default(0);
            $table->tinyInteger("editable")->default(0);
            $table->tinyInteger("locked")->default(0);
            $table->integer("visitors")->default(1);
            $table->string("image")->nullable();
            $table->foreignId("inserted_by")->nullable()->references('id')->on("admins")->nullable();
            $table->foreignId("updated_by")->nullable()->references('id')->on("admins")->nullable();
            $table->foreignId("deleted_by")->nullable()->references('id')->on("admins")->nullable();
            $table->softDeletes();
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
