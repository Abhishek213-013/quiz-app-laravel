<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('quizzes')) {
            Schema::create('quizzes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('quiz_set_id')->constrained()->onDelete('cascade');
                $table->enum('question_type', ['multiple_choice', 'brief_answer', 'true_false'])->default('multiple_choice')
                  ->after('question');
                $table->text('question');
                $table->json('options')->nullable()->change();
                $table->string('correct_answer');
                $table->integer('points')->default(1);
                $table->timestamps();
                
                // Add index for better performance
                $table->index(['quiz_set_id', 'question_type']);
            });
            
            echo "Quizzes table created successfully!\n";
        } else {
            echo "Quizzes table already exists!\n";
        }
    }

    public function down()
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropColumn('question_type');
            $table->json('options')->nullable(false)->change();
        });
    }
};