<?php

namespace Database\Seeders;

use App\Models\Classes;
use App\Models\ClassStudent;
use App\Models\Material;
use App\Models\Quiz;
use App\Models\QuizChoice;
use App\Models\StudentAnswerChoice;
use App\Models\StudentAnswerEssay;
use App\Models\StudentPassMaterialQuiz;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Guru 1',
            'user_id' => 'GR1',
            'email' => 'guru1@mail.com',
            'username' => 'guru1',
            'password' => app('hash')->driver('bcrypt')->make('guru1'),
            'role' => 'teacher',
        ]);
        User::create([
            'name' => 'Guru 2',
            'user_id' => 'GR2',
            'email' => 'guru2@mail.com',
            'username' => 'guru2',
            'password' => app('hash')->driver('bcrypt')->make('guru2'),
            'role' => 'teacher',
        ]);
        User::create([
            'name' => 'Siswa 1',
            'user_id' => 'SW1',
            'email' => 'siswa1@mail.com',
            'username' => 'siswa1',
            'password' => app('hash')->driver('bcrypt')->make('siswa1'),
            'role' => 'student',
        ]);
        User::create([
            'name' => 'Siswa 2',
            'user_id' => 'SW2',
            'email' => 'siswa2@mail.com',
            'username' => 'siswa2',
            'password' => app('hash')->driver('bcrypt')->make('siswa2'),
            'role' => 'student',
        ]);

        Classes::create([
            'class_id' => 'CLS1',
            'name' => 'Kelas 1',
            'description' => 'Kelas 1',
            'teacher_id' => 'GR1',
        ]);

        ClassStudent::create([
            'class_id' => 'CLS1',
            'student_id' => 'SW1',
        ]);

        ClassStudent::create([
            'class_id' => 'CLS1',
            'student_id' => 'SW2',
        ]);

        Material::create([
            'class_id' => 'CLS1',
            'material_id' => 'MTR1',
            'title' => 'Materi 1',
            'description' => 'Materi 1',
            'file' => 'materi1.pdf',
        ]);

        Quiz::create([
            'material_id' => 'MTR1',
            'quiz_id' => 'QZ1',
            'question' => 'Berapa hasil 1 + 1?',
            'correct_answer_order' => '3',
            'type' => 'choice',
        ]);

        Quiz::create([
            'material_id' => 'MTR1',
            'quiz_id' => 'QZ2',
            'question' => 'Berapa hasil 2 + 2?',
            'correct_answer_order' => '0',
            'type' => 'choice',
        ]);

        Quiz::create([
            'material_id' => 'MTR1',
            'quiz_id' => 'QZ3',
            'question' => 'Berapa nilai phi?',
            'correct_answer_order' => '0',
            'type' => 'essay',
        ]);

        QuizChoice::create([
            'quiz_id' => 'QZ1',
            'choice_id' => 'QZ1CH1',
            'answer_order' => '0',
            'answer' => '-1',
        ]);

        QuizChoice::create([
            'quiz_id' => 'QZ1',
            'choice_id' => 'QZ1CH2',
            'answer_order' => '1',
            'answer' => '0',
        ]);

        QuizChoice::create([
            'quiz_id' => 'QZ1',
            'choice_id' => 'QZ1CH3',
            'answer_order' => '2',
            'answer' => '1',
        ]);

        QuizChoice::create([
            'quiz_id' => 'QZ1',
            'choice_id' => 'QZ1CH4',
            'answer_order' => '3',
            'answer' => '2',
        ]);

        QuizChoice::create([
            'quiz_id' => 'QZ2',
            'choice_id' => 'QZ2CH1',
            'answer_order' => '0',
            'answer' => '4',
        ]);

        QuizChoice::create([
            'quiz_id' => 'QZ2',
            'choice_id' => 'QZ2CH2',
            'answer_order' => '1',
            'answer' => '3',
        ]);

        QuizChoice::create([
            'quiz_id' => 'QZ2',
            'choice_id' => 'QZ2CH3',
            'answer_order' => '2',
            'answer' => '2',
        ]);

        QuizChoice::create([
            'quiz_id' => 'QZ2',
            'choice_id' => 'QZ2CH4',
            'answer_order' => '3',
            'answer' => '1',
        ]);

        StudentPassMaterialQuiz::create([
            'student_id' => 'SW1',
            'material_id' => 'MTR1',
            'pass_quiz_id' => 'PQ1',
        ]);

        StudentAnswerChoice::create([
            'pass_quiz_id' => 'PQ1',
            'quiz_id' => 'QZ1',
            'choice_id' => 'QZ1CH3',
        ]);

        StudentAnswerChoice::create([
            'pass_quiz_id' => 'PQ1',
            'quiz_id' => 'QZ2',
            'choice_id' => 'QZ2CH4',
        ]);

        StudentAnswerEssay::create([
            'pass_quiz_id' => 'PQ1',
            'quiz_id' => 'QZ3',
            'answers' => '3.14',
        ]);

        StudentPassMaterialQuiz::create([
            'student_id' => 'SW2',
            'material_id' => 'MTR1',
            'pass_quiz_id' => 'PQ2',
        ]);

        StudentAnswerChoice::create([
            'pass_quiz_id' => 'PQ2',
            'quiz_id' => 'QZ1',
            'choice_id' => 'QZ1CH2',
        ]);

        StudentAnswerChoice::create([
            'pass_quiz_id' => 'PQ2',
            'quiz_id' => 'QZ2',
            'choice_id' => 'QZ2CH3',
        ]);

        StudentAnswerEssay::create([
            'pass_quiz_id' => 'PQ2',
            'quiz_id' => 'QZ3',
            'answers' => '3.15',
        ]);
    }
}
