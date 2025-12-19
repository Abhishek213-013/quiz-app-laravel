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
        Schema::table('quiz_results', function (Blueprint $table) {
            // Add the new columns if they don't exist
            if (!Schema::hasColumn('quiz_results', 'browser_id')) {
                $table->string('browser_id')->nullable()->after('time_taken');
            }
            
            if (!Schema::hasColumn('quiz_results', 'answered_count')) {
                $table->integer('answered_count')->default(0)->after('browser_id');
            }
            
            if (!Schema::hasColumn('quiz_results', 'skipped_count')) {
                $table->integer('skipped_count')->default(0)->after('answered_count');
            }
            
            if (!Schema::hasColumn('quiz_results', 'correct_count')) {
                $table->integer('correct_count')->default(0)->after('skipped_count');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz_results', function (Blueprint $table) {
            // Remove the columns if they exist
            $table->dropColumn(['browser_id', 'answered_count', 'skipped_count', 'correct_count']);
        });
    }
};