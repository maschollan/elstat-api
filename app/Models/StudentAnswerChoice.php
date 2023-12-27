<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAnswerChoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'pass_quiz_id', 'quiz_id', 'choice_id'
    ];

    public function quizChoice()
    {
        return $this->belongsTo(QuizChoice::class, 'choice_id', 'choice_id');
    }

    public function studentPassMaterialQuiz()
    {
        return $this->belongsTo(StudentPassMaterialQuiz::class, 'pass_quiz_id', 'pass_quiz_id');
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id', 'quiz_id');
    }
}
