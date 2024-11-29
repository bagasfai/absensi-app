<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'assign_to' => 'object',
            'pilihan' => 'object',
        ];
    }

    protected $fillable = ['jenis_quiz', 'pertanyaan', 'jawaban', 'pilihan', 'jadwal', 'durasi_edit', 'edit_unit', 'assign_to', 'is_upload'];

    /**
     * Get all of the quizAnswer for the Quiz
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function quizAnswer(): HasMany
    {
        return $this->hasMany(QuizAnswer::class, 'quiz_id', 'id');
    }
}
