<?php

namespace App\Exports;

use App\Models\QuizAnswer;
use Illuminate\Support\Facades\URL;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class QuizAnswerExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */

    protected $tanggal;

    public function __construct($tanggal)
    {
        $this->tanggal = $tanggal;
    }

    public function collection()
    {
        return QuizAnswer::with('user')
            ->whereDate('created_at', $this->tanggal)
            ->get()
            ->map(function ($quizAnswer, $index) {
                return [
                    'No' => $index + 1,
                    'Nama' => $quizAnswer->user->nama,
                    'Email' => $quizAnswer->user->email,
                    'Jawaban' => $quizAnswer->jawaban,
                    'Gambar' => $quizAnswer->file ? URL::to('/storage/uploads/quiz/' . $quizAnswer->file) : '-',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama',
            'Email',
            'Jawaban',
            'Gambar',
        ];
    }
}
