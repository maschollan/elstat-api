<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'material_id', 'quiz_id', 'question', 'question_image', 'correct_answer_order', 'type'
    ];

    protected $hidden = [
        'status', 'correct_answer_order'
    ];

    public function quizChoice()
    {
        return $this->hasMany(QuizChoice::class, 'quiz_id', 'quiz_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id', 'material_id');
    }
}
