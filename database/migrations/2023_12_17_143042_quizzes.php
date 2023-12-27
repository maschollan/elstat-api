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
        Schema::create('quizzes', function (Blueprint $table): void {
            $table->id();
            $table->string('quiz_id')->unique();
            $table->string('material_id');
            $table->string('question')->default('');
            $table->string('question_image')->default('');
            $table->string('correct_answer_order')->default('');
            $table->enum('type', ['choice', 'essay']);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->foreign('material_id')->references('material_id')->on('materials')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
