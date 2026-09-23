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
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->string('referral_number')->unique(); // RJK-YYYYMM-NNNNN
            $table->foreignId('rehabilitation_case_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assessment_id')->constrained();
            $table->foreignId('referral_institution_id')->constrained();
            $table->foreignId('officer_id')->constrained('users');
            $table->date('referral_date');
            $table->string('status')->default('draft'); // draft|sent|accepted|in_service|completed|declined|cancelled
            $table->text('service_result')->nullable();
            $table->timestampTz('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('referral_date');
            $table->index('referral_institution_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
