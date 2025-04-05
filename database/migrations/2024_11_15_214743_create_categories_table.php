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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
                $table->string('name');
                $table->string("image")->nullable() ;
                $table->text("description")->nullable();
                $table->string("color")->nullable();
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
        Schema::dropIfExists('categories');
    }
};
