<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAnswer extends Model
{
    use HasFactory;

    protected $fillable = ['quiz_id', 'user_id', 'absen_id', 'jawaban', 'file', 'is_edit'];

    /**
     * Get the quiz that owns the QuizAnswer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class, 'quiz_id', 'id');
    }

    /**
     * Get the absen that owns the QuizAnswer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function absen(): BelongsTo
    {
        return $this->belongsTo(Absen::class, 'absen_id', 'id');
    }

    /**
     * Get the user that owns the QuizAnswer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
