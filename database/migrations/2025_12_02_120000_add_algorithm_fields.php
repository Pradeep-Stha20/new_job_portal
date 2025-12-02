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
        // Add fields to users table for skill matching
        Schema::table('users', function (Blueprint $table) {
            $table->text('skills')->nullable()->comment('Comma-separated skills of user');
            $table->integer('years_experience')->default(0)->comment('Total years of professional experience');
        });

        // Add fields to job_applications for algorithm results
        Schema::table('job_applications', function (Blueprint $table) {
            $table->float('fit_score')->default(0)->comment('Job match score (0-100)');
            $table->float('fraud_score')->default(0)->comment('Fraud detection score (0-100)');
            $table->enum('status', ['pending', 'approved', 'rejected', 'flagged'])->default('pending')->change();
        });

        // Add fields to jobs table for salary tracking
        Schema::table('jobs', function (Blueprint $table) {
            $table->decimal('salary_min', 10, 2)->nullable();
            $table->decimal('salary_max', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['skills', 'years_experience']);
        });

        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropColumn(['fit_score', 'fraud_score']);
        });

        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn(['salary_min', 'salary_max']);
        });
    }
};
