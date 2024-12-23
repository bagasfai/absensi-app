<?php

namespace App\Http\Controllers;

use App\Models\Absen;
use App\Models\Pengajuan_Izin;
use App\Models\PengajuanCuti;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class CutiController extends Controller
{
    public function index()
    {
        $email = auth()->user()->email;
        $hariini = date("Y-m-d");
        $tahun = date('Y');
        $bulan = date('m') * 1;
        $absen = Absen::where('tanggal', $hariini)->where('email', $email)->orderBy('id', 'desc')->first();
        $currentDateTime = now();

        $latestEntry = Absen::select('*', DB::raw('CONCAT(tanggal, " ", jam_masuk) as datetime'))
            ->where('email', $email)
            ->orderBy('id', 'desc')
            ->first();


        $latestEntryOut = Absen::select('*', DB::raw('CONCAT(tanggal_keluar, " ", jam_keluar) as datetime'))
            ->where('email', $email)
            ->orderBy('id', 'desc')
            ->first();

        $startDate = Carbon::now()->subMonth()->startOfMonth()->addDays(24);
        $endDate = Carbon::now()->endOfMonth()->addDays(25);

        $attendances = Absen::whereBetween('tanggal', [$startDate, $endDate])->get();

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
            $selisihWaktuOut = "";
        }

        $cek = Absen::where('email', $email)->where('status', 'H')->orderBy('id', 'desc')->first();

        $datacuti = PengajuanCuti::where('user_id', Auth::user()->id)->get();

        return view('cuti.index', compact('datacuti', 'selisihWaktuOut'));
    }

    public function indexApproval(Request $request)
    {
        $cuti = PengajuanCuti::orderBy('tanggal_cuti', 'desc');
        if (!empty($request->dari) && !empty($request->sampai)) {
            $cuti->whereBetween('tanggal_cuti', [$request->dari, $request->sampai]);
        }
        $cuti = $cuti->get();

        $jumlahIzin = Pengajuan_Izin::where('status_approved', 0)->count();
        $jumlahCuti = PengajuanCuti::where('status', 0)->count();

        return view('cuti.approval', compact('cuti', 'jumlahIzin', 'jumlahCuti'));
    }

    public function create()
    {
        $email = auth()->user()->email;
        $hariini = date("Y-m-d");
        $tahun = date('Y');
        $bulan = date('m') * 1;
        $absen = Absen::where('tanggal', $hariini)->where('email', $email)->orderBy('id', 'desc')->first();
        $currentDateTime = now();

        $latestEntry = Absen::select('*', DB::raw('CONCAT(tanggal, " ", jam_masuk) as datetime'))
            ->where('email', $email)
            ->orderBy('id', 'desc')
            ->first();


        $latestEntryOut = Absen::select('*', DB::raw('CONCAT(tanggal_keluar, " ", jam_keluar) as datetime'))
            ->where('email', $email)
            ->orderBy('id', 'desc')
            ->first();

        $startDate = Carbon::now()->subMonth()->startOfMonth()->addDays(24);
        $endDate = Carbon::now()->endOfMonth()->addDays(25);

        $attendances = Absen::whereBetween('tanggal', [$startDate, $endDate])->get();
        // dd($attendances);

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
            $selisihWaktuOut = "";
        }

        $cek = Absen::where('email', $email)->where('status', 'H')->orderBy('id', 'desc')->first();

        return view('cuti.create', compact('selisihWaktuOut'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_cuti' => 'required|date',
            'jenis_cuti' => 'required',
            'keterangan' => 'required|string',
        ]);

        $user = Auth::user();
        $tanggalMasukKerja = Carbon::parse($user->tanggal_masuk_kerja);
        $oneYearAfterJoin = $tanggalMasukKerja->addYear();
        $existingCuti = PengajuanCuti::where('user_id', $user->id)
            ->where('tanggal_cuti', $request->tanggal_cuti)
            ->first();

        if ($existingCuti) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Anda sudah mengajukan cuti pada tanggal tersebut.']);
        }

        if (now()->lessThan($oneYearAfterJoin)) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Anda belum bekerja selama 1 tahun.']);
        }

        if ($request->jenis_cuti == 'Cuti Tahunan') {
            $currentYear = now()->year;
            $cutiCount = PengajuanCuti::where('user_id', $user->id)
                ->where('jenis_cuti', 'Cuti Tahunan')
                ->whereYear('tanggal_cuti', $currentYear)
                ->count();

            if ($cutiCount >= 12) {
                return redirect()->back()->withInput()->withErrors(['error' => 'Anda sudah mengajukan cuti tahunan sebanyak 12 kali tahun ini.']);
            }
        }

        $tanggalCuti = Carbon::parse($request->tanggal_cuti);
        $minTanggalCuti = now()->addDays(3);

        if ($tanggalCuti->lessThan($minTanggalCuti)) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Tanggal cuti harus minimal 3 hari kerja dari hari ini.']);
        }

        DB::beginTransaction();

        try {
            PengajuanCuti::create(array_merge($validated, [
                'user_id' => Auth::user()->id,
            ]));


            $idTelegram = User::where('jabatan', 'SUPERADMIN')->pluck('id_telegram')->toArray();
            $message = "PENGAJUAN CUTI \n\n$user->nama mengajukan pengajuan $request->jenis_cuti \nuntuk tanggal $request->tanggal_cuti \n\nDengan keterangan: \n$request->keterangan";
            // Send the message using TelegramController
            $telegramController = app(TelegramController::class);
            foreach ($idTelegram as $chatId) {
                if ($chatId == null) {
                    continue;
                }

                $telegramController->sendMessage($chatId, $message);
            }

            DB::commit();
            return redirect()->route('cuti.index')->with('success', 'Pengajuan cuti berhasil disimpan.');
        } catch (\Exception $error) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors(['error' => $error->getMessage()]);
        }
    }

    public function action(Request $request)
    {
        $status_approved = $request->status_approved;
        $id_cuti_form = $request->id_cuti_form;
        $cuti = PengajuanCuti::where('id', $id_cuti_form)->first();

        $data = [
            'status' => $status_approved,
        ];

        $idTelegram = User::where('id', $cuti->user_id)->pluck('id_telegram')->first();
        $keterangan = $status_approved == 1 ? 'Approved' : 'Rejected';
        $message = "APPROVAL PENGAJUAN CUTI \n\nPengajuan Cuti untuk tanggal $cuti->tanggal_cuti \n\nDengan keterangan: \n$keterangan";

        if ($idTelegram) {
            // Send the message using TelegramController
            $telegramController = app(TelegramController::class);
            $telegramController->sendMessage($idTelegram, $message);
        }

        if ($status_approved == 1) {
            $data['approved_by'] = Auth::user()->id;
            $data['approved_at'] = now();
        } else {
            $data['rejected_by'] = Auth::user()->id;
            $data['rejected_at'] = now();
        }

        $update = PengajuanCuti::where('id', $id_cuti_form)->update($data);

        if ($update) {
            return Redirect::back()->with(['success' => 'Data berhasil di Update']);
        } else {
            return Redirect::back()->with(['warning' => 'Data gagal di Update']);
        }
    }

    public function batalApprove($id)
    {
        $update = PengajuanCuti::where('id', $id)->update([
            'status' => 0,
        ]);

        if ($update) {
            return Redirect::back()->with(['success' => 'Data berhasil di Update']);
        } else {
            return Redirect::back()->with(['warning' => 'Data gagal di Update']);
        }
    }
}
