<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_set_id',
        'participant_name',
        'score',
        'total_questions',
        'answers',
        'percentage',
        'time_taken',
        'browser_id',
        'answered_count',
        'skipped_count',
        'correct_count'
    ];

    protected $casts = [
        'answers' => 'array',
        'score' => 'integer',
        'total_questions' => 'integer',
        'percentage' => 'decimal:2',
        'time_taken' => 'integer',
        'answered_count' => 'integer',
        'skipped_count' => 'integer',
        'correct_count' => 'integer'
    ];

    public function quizSet()
    {
        return $this->belongsTo(QuizSet::class);
    }
}