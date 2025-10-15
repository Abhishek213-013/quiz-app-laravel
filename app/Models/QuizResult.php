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
        'browser_id' // ← ADD THIS LINE
    ];

    protected $casts = [
        'answers' => 'array'
    ];

    public function quizSet()
    {
        return $this->belongsTo(QuizSet::class);
    }
}