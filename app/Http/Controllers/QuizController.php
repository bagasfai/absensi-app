<?php

namespace App\Http\Controllers;

use App\Models\Absen;
use App\Models\Pengajuan_Izin;
use App\Models\PengajuanCuti;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use JsonException;

class QuizController extends Controller
{
    public function index()
    {
        if (auth()->user()->jabatan == 'TEAM WAGNER') {
            $jumlahIzin = Pengajuan_Izin::where('status_approved', 0)->whereIn('email', ['kucingjuna400@gmail.com', 'handhalah@sds.co.id', 'furganalathas@gmail.com'])->count();
        } else if (auth()->user()->jabatan == 'ADMIN') {
            $jumlahIzin = Pengajuan_Izin::leftJoin('users', 'pengajuan_izin.email', '=', 'users.email')->select('*')->where('status_approved', 0)->where('users.jabatan', 'KORLAP')->count();
        } else {
            $jumlahIzin = Pengajuan_Izin::where('status_approved', 0)->count();
        }

        $jumlahCuti = PengajuanCuti::where('status', 0)->count();
        $quiz = Quiz::get();

        return view('quiz.index', compact('jumlahIzin', 'quiz', 'jumlahCuti'));
    }

    function laporan()
    {
        if (auth()->user()->jabatan == 'TEAM WAGNER') {
            $jumlahIzin = Pengajuan_Izin::where('status_approved', 0)->whereIn('email', ['kucingjuna400@gmail.com', 'handhalah@sds.co.id', 'furganalathas@gmail.com'])->count();
        } else if (auth()->user()->jabatan == 'ADMIN') {
            $jumlahIzin = Pengajuan_Izin::leftJoin('users', 'pengajuan_izin.email', '=', 'users.email')->select('*')->where('status_approved', 0)->where('users.jabatan', 'KORLAP')->count();
        } else {
            $jumlahIzin = Pengajuan_Izin::where('status_approved', 0)->count();
        }

        $quiz = QuizAnswer::get();
        $jumlahCuti = PengajuanCuti::where('status', 0)->count();

        return view('quiz.laporan', compact('jumlahIzin', 'jumlahCuti', 'quiz'));
    }

    public function create()
    {
        $users = User::all();
        $roles = User::groupBy('jabatan')->pluck('jabatan');

        if (auth()->user()->jabatan == 'TEAM WAGNER') {
            $jumlahIzin = Pengajuan_Izin::where('status_approved', 0)->whereIn('email', ['kucingjuna400@gmail.com', 'handhalah@sds.co.id', 'furganalathas@gmail.com'])->count();
        } else if (auth()->user()->jabatan == 'ADMIN') {
            $jumlahIzin = Pengajuan_Izin::leftJoin('users', 'pengajuan_izin.email', '=', 'users.email')->select('*')->where('status_approved', 0)->where('users.jabatan', 'KORLAP')->count();
        } else {
            $jumlahIzin = Pengajuan_Izin::where('status_approved', 0)->count();
        }
        $jumlahCuti = PengajuanCuti::where('status', 0)->count();

        return view('quiz.create', compact('jumlahIzin', 'jumlahCuti', 'users', 'roles'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'pertanyaan' => ['required', 'string'],
            'jadwal' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) {
                    if ($value && Quiz::whereDate('jadwal', $value)->exists()) {
                        return $fail('Sudah ada quiz untuk hari ini.');
                    }
                }
            ],
            'durasi_edit' => ['required', 'integer', 'min:1'],
            'edit_unit' => ['required', 'in:minutes,hours'],
            'assignTo' => ['nullable', 'array'],
        ]);

        // If the unit is 'hours', convert it to minutes
        if ($validatedData['edit_unit'] == 'hours') {
            $validatedData['durasi_edit'] *= 60;
        }

        try {
            DB::transaction(function () use ($validatedData) {
                $assignTo = $validatedData['assignTo'] ?? [];

                $jabatanMapping = [
                    'office' => 'OFFICE',
                    'security' => 'SECURITY',
                    'pmr' => 'PMR',
                    'superadmin' => 'SUPERADMIN',
                    'wh' => 'WH',
                ];

                foreach ($jabatanMapping as $key => $jabatan) {
                    if (in_array($key, $assignTo)) {
                        $users = User::where('jabatan', $jabatan)->pluck('id')->toArray();
                        $assignTo = array_merge($assignTo, $users);
                    }
                }

                $assignTo = array_diff($assignTo, array_keys($jabatanMapping));
                $assignTo = array_values(array_unique($assignTo));

                unset($validatedData['assignTo']);
                $data = array_merge($validatedData, [
                    'assign_to' => json_encode($assignTo),
                ]);

                Quiz::create($data);
            });

            return redirect()->route('quiz.index')->with('success', 'Berhasil membuat quiz!');
        } catch (\Exception $error) {
            Log::error('Quiz creation failed: ' . $error->getMessage());
            return back()->withErrors(['error' => 'Gagal membuat quiz. Mohon coba lagi.']);
        }
    }

    public function edit($id, Request $request)
    {
        $quiz = Quiz::where('id', $id)->first();
        $users = User::whereIn('jabatan', ['PMR', 'WH'])->get();
        $assignTo = json_decode($quiz->assign_to, true);

        if (auth()->user()->jabatan == 'TEAM WAGNER') {
            $jumlahIzin = Pengajuan_Izin::where('status_approved', 0)->whereIn('email', ['kucingjuna400@gmail.com', 'handhalah@sds.co.id', 'furganalathas@gmail.com'])->count();
        } else if (auth()->user()->jabatan == 'ADMIN') {
            $jumlahIzin = Pengajuan_Izin::leftJoin('users', 'pengajuan_izin.email', '=', 'users.email')->select('*')->where('status_approved', 0)->where('users.jabatan', 'KORLAP')->count();
        } else {
            $jumlahIzin = Pengajuan_Izin::where('status_approved', 0)->count();
        }
        $jumlahCuti = PengajuanCuti::where('status', 0)->count();

        return view('quiz.edit', compact('jumlahIzin', 'jumlahCuti', 'quiz', 'users', 'assignTo'));
    }

    public function update($id, Request $request)
    {
        $quiz = Quiz::findOrFail($id);

        $validatedData = $request->validate([
            'pertanyaan' => ['required', 'string'],
            'jadwal' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) use ($id) {
                    if ($value && Quiz::whereDate('jadwal', $value)->where('id', '!=', $id)->exists()) {
                        return $fail('Sudah ada quiz untuk hari ini.');
                    }
                }
            ],
            'durasi_edit' => ['required', 'integer', 'min:1'],
            'edit_unit' => ['required', 'in:minutes,hours'],
            'assignTo' => ['nullable', 'array'],
        ]);

        if ($validatedData['edit_unit'] == 'hours') {
            $validatedData['durasi_edit'] *= 60;
        }

        $assignTo = $validatedData['assignTo'] ?? [];

        if (in_array('pmr', $assignTo)) {
            $pmrUsers = User::whereIn('jabatan', ['PMR', 'WH'])->pluck('id')->toArray();
            $assignTo = array_merge($assignTo, $pmrUsers);
        }

        if (in_array('wh', $assignTo)) {
            $whUsers = User::where('roles', 'wh')->pluck('id')->toArray();
            $assignTo = array_merge($assignTo, $whUsers);
        }

        $assignTo = array_diff($assignTo, ['PMR', 'WH']);
        // reindex the array so its stored as an array not object when using json_encode
        $assignTo = array_values($assignTo);

        $assignTo = array_unique($assignTo);

        unset($validatedData['assignTo']);
        $data = array_merge($validatedData, [
            'assign_to' => json_encode($assignTo),
        ]);

        $data = $validatedData;

        try {
            DB::transaction(function () use ($quiz, $data) {
                $quiz->update($data);
            });

            return redirect()->route('quiz.index')->with('success', 'Quiz updated successfully!');
        } catch (\Exception $error) {
            Log::error('Quiz update failed: ' . $error->getMessage());
            return back()->withErrors(['error' => 'Gagal memperbarui quiz. Mohon coba lagi.']);
        }
    }

    public function editJawaban($id, Request $request)
    {
        $email = auth()->user()->email;
        $hariini = date("Y-m-d");
        $currentDateTime = now();
        $quizAnswer = QuizAnswer::where('id', $id)->first();
        $quiz = $quizAnswer->quiz;

        $latestEntry = Absen::select('*', DB::raw('CONCAT(tanggal, " ", jam_masuk) as datetime'))
            ->where('email', $email)
            ->orderBy('id', 'desc')
            ->first();

        $latestEntryOut = Absen::select('*', DB::raw('CONCAT(tanggal, " ", jam_keluar) as datetime'))
            ->where('email', $email)
            ->orderBy('id', 'desc')
            ->first();

        if ($latestEntry) {
            $lastEntryDateTime = Carbon::parse($latestEntry->datetime);
            $selisihWaktu = $currentDateTime->diffInHours($lastEntryDateTime);
        } else {
            $lastEntryDateTime = "";
            $selisihWaktu = "";
        }
        if ($latestEntryOut) {
            $lastEntryDateTimeOut = Carbon::parse($latestEntryOut->datetime);
            $selisihWaktuOut = $currentDateTime->diffInHours($lastEntryDateTimeOut);
        } else {
            $lastEntryDateTimeOut = "";
            $selisihWaktuOut = 24;
        }

        return view('quiz.edit-jawaban', compact('latestEntry', 'selisihWaktu', 'selisihWaktuOut', 'quiz', 'quizAnswer'));
    }

    public function updateJawaban($id, Request $request)
    {
        $quizAnswer = QuizAnswer::findOrFail($id);

        $validatedData = $request->validate([
            'jawaban' => ['required', 'string'],
            'file' => ['nullable', 'file', 'mimes:jpg,jpeg,png'],
        ]);

        $data = [
            'jawaban' => $validatedData['jawaban'],
            'is_edit' => 1,
        ];

        if ($request->hasFile('file')) {
            // Delete the existing file if it exists
            if ($quizAnswer->file) {
                Storage::delete("public/uploads/quiz/" . $quizAnswer->file);
            }

            $fileQuiz = $request->file('file');
            $folderPathQuiz = "public/uploads/quiz/";
            $fileNameQuiz = auth()->user()->email . "-" . date("Y-m-d") . "-" . auth()->user()->nama . "." . $fileQuiz->getClientOriginalExtension();
            $fileQuiz->storeAs($folderPathQuiz, $fileNameQuiz);
            $data['file'] = $fileNameQuiz;
        }

        try {
            DB::transaction(function () use ($quizAnswer, $data) {
                $quizAnswer->update($data);
            });

            return redirect()->route('absen.dashboard')->with('success', 'Jawaban updated successfully!');
        } catch (\Exception $error) {
            Log::error('Jawaban update failed: ' . $error->getMessage());
            return back()->withErrors(['error' => 'Gagal memperbarui jawaban. Mohon coba lagi.']);
        }
    }

    public function getQuiz(Request $request)
    {
        $tanggal = $request->tanggal;

        if ($request->ajax()) {
            $quiz = QuizAnswer::when($tanggal, function ($query) use ($tanggal) {
                $query->whereDate('created_at', '=', $tanggal);
            })
                ->with('user:id,nama,email')
                ->with('quiz:id,pertanyaan')
                ->get();

            $pertanyaan = Quiz::where('jadwal', '=', $tanggal)->first();

            return response()->json([
                'quiz' => $quiz,
                'pertanyaan' => $pertanyaan,
            ]);
        } else {
            abort(403);
        }
    }

    public function getQuizDates(Request $request)
    {
        $quizId = $request->quiz_id;

        $dates = Quiz::selectRaw('DATE(jadwal) as jadwal')
            ->whereNotNull('jadwal')
            ->when($quizId, function ($query) use ($quizId) {
                return $query->where('id', '!=', $quizId);
            })
            ->distinct()
            ->pluck('jadwal');

        return response()->json($dates);
    }

    public function checkQuizForToday(Request $request)
    {
        $user = $request->user();

        if ($user->jabatan != 'PMR' || $user->jabatan != 'WH') {
            return response()->json(['message' => 'No quizzes for this user.'], 404);
        }

        // Get today's date
        $today = now()->toDateString();

        // Find quizzes assigned to this user for today
        $quiz = Quiz::whereDate('jadwal', $today)
            ->whereJsonContains('assign_to', $user->id)
            ->first();

        $quizAnswer = QuizAnswer::where('quiz_id', $quiz->id)
            ->where('user_id', $user->id)
            ->first();

        if ($quiz && !$quizAnswer) {
            return response()->json(['message' => 'Quiz available', 'quiz' => $quiz]);
        }

        return response()->json(['message' => 'No quiz available for today.'], 404);
    }
}
