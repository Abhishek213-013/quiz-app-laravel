<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('quiz_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_set_id')->constrained()->onDelete('cascade');
            $table->string('participant_name');
            $table->integer('score');
            $table->integer('total_questions');
            $table->json('answers')->nullable(); // Store user answers
            $table->decimal('percentage', 5, 2);
            $table->integer('time_taken'); // in seconds
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('quiz_results');
    }
};