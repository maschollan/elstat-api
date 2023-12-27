<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id', 'material_id', 'title', 'description', 'file'
    ];

    protected $hidden = [
        'status'
    ];

    public function quiz()
    {
        return $this->hasMany(Quiz::class, 'material_id', 'material_id')->with('quizChoice');
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id', 'class_id');
    }

    public function studentPassMaterialQuiz()
    {
        return $this->hasMany(StudentPassMaterialQuiz::class, 'material_id', 'material_id')->with('student');
    }
}
