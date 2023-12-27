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
        Schema::create('student_pass_material_quizzes', function (Blueprint $table): void {
            $table->id();
            $table->string('student_id');
            $table->string('material_id');
            $table->string('pass_quiz_id')->unique();
            $table->string('score')->default(0);
            $table->timestamps();

            $table->foreign('student_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('material_id')->references('material_id')->on('materials')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_pass_material_quizzes');
    }
};
