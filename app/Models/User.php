<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory;

    protected $fillable = [
        'name', 'email', 'username', 'password', 'role', 'user_id'
    ];

    protected $hidden = [
        'password', 'status'
    ];

    public function classes()
    {
        if ($this->role == 'teacher') {
            return $this->hasMany(Classes::class, 'teacher_id', 'user_id')->with('materials');
        } else {
            return $this->belongsToMany(Classes::class, 'class_students', 'student_id', 'class_id', 'user_id', 'class_id')->with('materials');
        }
    }

    public function studentPassMaterialQuiz()
    {
        return $this->hasMany(StudentPassMaterialQuiz::class, 'student_id', 'user_id')->with('material.class');
    }
}
