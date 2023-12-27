<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizChoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id', 'answer_order', 'answer', 'answer_image', 'choice_id'
    ];

    protected $hidden = [
        'status', 'answer_order'
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id', 'quiz_id');
    }
}
