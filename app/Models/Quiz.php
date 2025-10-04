<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_set_id',
        'question',
        'options',
        'correct_answer',
        'points'
    ];

    protected $casts = [
        'options' => 'array'
    ];

    public function quizSet()
    {
        return $this->belongsTo(QuizSet::class);
    }
}