<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_set_id')->constrained()->onDelete('cascade');
            $table->text('question');
            $table->json('options'); // Store options as JSON array
            $table->string('correct_answer');
            $table->integer('points')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('quizzes');
    }
};