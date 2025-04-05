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
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId("sub_id")->references('id')->on('subscribers');
            $table->string("name");
            $table->text("description")->nullable();
            $table->string("address");
            $table->boolean("deletable")->default(1);
            $table->foreignId("inserted_by")->nullable()->references('id')->on("admins")->nullable();
            $table->foreignId("updated_by")->nullable()->references('id')->on("admins")->nullable();
            $table->foreignId("deleted_by")->nullable()->references('id')->on("admins")->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.a
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
