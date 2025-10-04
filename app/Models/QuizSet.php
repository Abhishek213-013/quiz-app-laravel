<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizSet extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'description',
        'time_limit',
        'question_count',
        'is_active'
    ];

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function results()
    {
        return $this->hasMany(QuizResult::class);
    }
}