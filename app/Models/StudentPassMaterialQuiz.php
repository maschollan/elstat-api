<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentPassMaterialQuiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'material_id', 'student_id', 'pass_quiz_id', 'score'
    ];

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id', 'material_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id', 'user_id');
    }

    public function studentAnswerChoice()
    {
        return $this->hasMany(StudentAnswerChoice::class, 'pass_quiz_id', 'pass_quiz_id')->with('quiz.quizChoice','quizChoice');
    }

    public function studentAnswerEssay()
    {
        return $this->hasMany(StudentAnswerEssay::class, 'pass_quiz_id', 'pass_quiz_id')->with('quiz');
    }
}
