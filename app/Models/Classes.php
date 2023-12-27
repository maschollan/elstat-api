<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classes extends Model 
{
    use HasFactory;

    protected $fillable = [
        'class_id', 'name', 'description', 'teacher_id'
    ];

    protected $hidden = [
        'status'
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id', 'user_id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'class_students', 'class_id', 'student_id', 'class_id', 'user_id');
    }

    public function materials()
    {
        return $this->hasMany(Material::class, 'class_id', 'class_id');
    }
}
