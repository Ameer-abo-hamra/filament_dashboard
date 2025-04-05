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
        Schema::create('subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email');
            $table->string('password');
            $table->string("username");
            $table->string('nationality')->nullable();
            $table->date("birthdate")->nullable();
            $table->tinyInteger("gender")->nullable();
            $table->string("mobile")->nullable();
            $table->string("verification_code")->nullable();
            $table->boolean('is_active')->default(false);
            $table->boolean("is_verified")->default(false);
            $table->string("address")->nullable();
            $table->string('reset_token')->nullable();
            $table->foreignId("country_code_id")->references("id")->on("country_codes");
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
        Schema::dropIfExists('subscribers');
    }
};
