<?php

namespace App\Exports;

use App\Models\QuizAnswer;
use Illuminate\Support\Facades\URL;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

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
        $date = Carbon::parse($this->tanggal);
        $startOfMonth = $date->startOfMonth()->toDateString();
        $endOfMonth = $date->endOfMonth()->toDateString();

        $dataCollection = QuizAnswer::with(['user', 'quiz'])
            ->whereDate('created_at', '>=', $startOfMonth)
            ->whereDate('created_at', '<=', $endOfMonth)
            ->get()
            ->map(function ($quizAnswer, $index) {
                return [
                    'No' => $index + 1,
                    'Tanggal' => Carbon::parse($quizAnswer->created_at)->format('d-M-Y'),
                    'Pertanyaan' => $quizAnswer->quiz->pertanyaan ?? '-',
                    'Nama' => $quizAnswer->user->nama,
                    'Email' => $quizAnswer->user->email,
                    'Jawaban' => $quizAnswer->jawaban,
                    'Gambar' => $quizAnswer->file ? URL::to('/storage/uploads/quiz/' . $quizAnswer->file) : '-',
                ];
            });

        return $dataCollection;
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Pertanyaan',
            'Nama',
            'Email',
            'Jawaban',
            'Gambar',
        ];
    }
}
