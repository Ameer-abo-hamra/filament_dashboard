<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountryCodesTable extends Migration
{
    /**
     * تشغيل الترحيل.
     */
    public function up()
    {
        Schema::create('country_codes', function (Blueprint $table) {
            $table->id();
            $table->string('country_name')->unique(); // اسم الدولة فريد
            $table->string('country_code')->unique(); // رمز الدولة فريد
            $table->string('iso_code', 3)->nullable()->unique(); // رمز ISO فريد
            $table->string('region')->nullable();
            $table->timestamps();
        });

    }

    /**
     * عكس الترحيل.
     */
    public function down()
    {
        Schema::dropIfExists('country_codes');
    }
}

