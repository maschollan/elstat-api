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
        Schema::create('quiz_choices', function (Blueprint $table): void {
            $table->id();
            $table->string('quiz_id');
            $table->string('choice_id')->unique();
            $table->string('answer_order');
            $table->string('answer')->default('');
            $table->string('answer_image')->default('');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->foreign('quiz_id')->references('quiz_id')->on('quizzes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_choices');
    }
};
