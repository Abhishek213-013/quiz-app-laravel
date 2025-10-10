<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_set_id',
        'question_type', // Added this
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

    // Helper methods for question types
    public function isMultipleChoice()
    {
        return $this->question_type === 'multiple_choice';
    }

    public function isBriefAnswer()
    {
        return $this->question_type === 'brief_answer';
    }

    public function isTrueFalse()
    {
        return $this->question_type === 'true_false';
    }

    // Get question type label for display
    public function getQuestionTypeLabel()
    {
        $labels = [
            'multiple_choice' => 'Multiple Choice',
            'brief_answer' => 'Brief Answer', 
            'true_false' => 'True/False'
        ];

        return $labels[$this->question_type] ?? $this->question_type;
    }

    // Scope queries for different question types
    public function scopeMultipleChoice($query)
    {
        return $query->where('question_type', 'multiple_choice');
    }

    public function scopeBriefAnswer($query)
    {
        return $query->where('question_type', 'brief_answer');
    }

    public function scopeTrueFalse($query)
    {
        return $query->where('question_type', 'true_false');
    }
}