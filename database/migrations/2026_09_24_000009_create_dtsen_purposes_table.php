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
        Schema::create('dtsen_purposes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // spmb, pip, kip_kuliah, bansos, kesehatan, dll
            $table->string('name');
            $table->unsignedTinyInteger('max_decile');
            $table->integer('validity_days')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dtsen_purposes');
    }
};
