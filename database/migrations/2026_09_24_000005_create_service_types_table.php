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
        Schema::create('service_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // DTSEN, PBI, REHSOS, etc.
            $table->string('name');
            $table->string('category');
            $table->text('description')->nullable();
            $table->string('handler')->default('generic'); // generic|dtsen|pbi
            $table->boolean('needs_assessment')->default(false);
            $table->integer('sla_days')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_types');
    }
};
