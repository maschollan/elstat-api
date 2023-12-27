<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAnswerEssay extends Model
{
    use HasFactory;

    protected $fillable = [
        'pass_quiz_id', 'quiz_id', 'answers'
    ];

    public function studentPassMaterialQuiz()
    {
        return $this->belongsTo(StudentPassMaterialQuiz::class, 'pass_quiz_id', 'pass_quiz_id');
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id', 'quiz_id');
    }
}
