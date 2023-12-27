<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_answer_essays', function (Blueprint $table): void {
            $table->id();
            $table->string('pass_quiz_id');
            $table->string('quiz_id');
            $table->string('answers');
            $table->timestamps();

            $table->foreign('pass_quiz_id')->references('pass_quiz_id')->on('student_pass_material_quizzes')->onDelete('cascade');
            $table->foreign('quiz_id')->references('quiz_id')->on('quizzes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_answer_essays');
    }
};
