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
        Schema::create('quiz_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('absen_id')->constrained('absens');
            $table->text('jawaban')->nullable();
            $table->text('file')->nullable();
            $table->tinyInteger('is_edit')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_answers');
    }
};
