<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Absen;
use App\Models\User;
use App\Models\Pengajuan_Izin;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Telegram\Bot\Laravel\Facades\Telegram;
use App\Http\Controllers\TelegramController;
use Symfony\Component\HttpFoundation\Session\Session;


class AbsensiController extends Controller
{

  public function setPeriode(Request $request)
  {
    $tanggal = $request->tanggal;
    session(['periode' => $tanggal]);

    return Redirect::back();
  }

  public function index(Request $request)
  {
    $tanggal = date('Y-m');
    $periode = session('periode');
    $query = Absen::query();

    if ($periode) {
      $query = Absen::whereRaw('DATE_FORMAT(tanggal, "%Y-%m") = ?', [$periode]);
    }

    if (auth()->user()->jabatan == 'TEAM WAGNER') {
      $absen = $query->whereIn('email', ['kucingjuna400@gmail.com', 'handhalah@sds.co.id', 'furganalathas@gmail.com'])
        ->get();
    } else if (auth()->user()->jabatan == 'ADMIN') {
      $absen = $query->leftJoin('users', 'absens.user_id', '=', 'users.id')
        ->where('users.jabatan', 'KORLAP')
        ->get();
    } else {
      $absen = Absen::all();
    }

    if (auth()->user()->jabatan == 'TEAM WAGNER') {
      $jumlahIzin = Pengajuan_Izin::where('status_approved', 0)->whereIn('email', ['kucingjuna400@gmail.com', 'handhalah@sds.co.id', 'furganalathas@gmail.com'])->count();
    } else if (auth()->user()->jabatan == 'ADMIN') {
      $jumlahIzin = Pengajuan_Izin::leftJoin('users', 'pengajuan_izin.email', '=', 'users.email')->select('*')->where('status_approved', 0)->where('users.jabatan', 'KORLAP')->count();
    } else {
      $jumlahIzin = Pengajuan_Izin::where('status_approved', 0)->count();
    }

    return view('absensi.index', compact('absen', 'jumlahIzin'));
  }

  public function create()
  {
    $hariini = date("Y-m-d");
    $email = auth()->user()->email;
    $cek = Absen::where('email', $email)->where('status', 'H')->whereNull('jam_keluar')->orderBy('id', 'desc')->first();
    $currentDateTime = now();
    $latestEntry = Absen::select(DB::raw('CONCAT(tanggal, " ", jam_masuk) as datetime'))
      ->where('email', $email)
      ->where('status', 'H')
      ->whereNotNull('jam_masuk')
      ->orderBy('id', 'desc')
      ->first();

    if ($latestEntry) {
      $lastEntryDateTime = Carbon::parse($latestEntry->datetime);
      $selisihWaktu = $currentDateTime->diffInHours($lastEntryDateTime);
    } else {
      $lastEntryDateTime = "";
      $selisihWaktu = 24;
    }

    return view('absensi.create', compact('cek', 'email', 'hariini', 'selisihWaktu'));
  }

  public function store(Request $request)
  {
    $email = auth()->user()->email;
    $nama = auth()->user()->nama;
    $laporan = $request->laporan;
    $tanggal = date("Y-m-d");
    $lokasi = $request->lokasi;
    $jam = date("H:i:s");
    $jenisAbsen = $request->jenis_absen;

    if ($jenisAbsen == 'masuk') {
      $ket = 'masuk';
    } else if ($jenisAbsen == 'keluar') {
      $ket = 'keluar';
    }

    $cek = Absen::where('tanggal', $tanggal)->where('email', $email)->where('status', 'H')->count();
    $image = $request->image;

    $folderPath = "public/uploads/absensi/";
    $formatName = $email . "-" . $tanggal . "-" . $ket;
    $image_parts = explode(";base64", $image);
    $image_base64 = base64_decode($image_parts[1]);
    $fileName = $formatName . ".jpeg";
    $file = $folderPath . $fileName;


    if ($jenisAbsen == 'masuk') {
      // insert absen
      $data = [
        'email' => $email,
        'nama' => $nama,
        'status' => 'H',
        'tanggal' => $tanggal,
        'jam_masuk' => $jam,
        'foto_masuk' => $fileName,
        'lokasi_masuk' => $lokasi,
        'laporan_masuk' => $laporan,
        'user_id' => auth()->user()->id,
      ];

      $simpan = Absen::insert($data);

      if ($simpan) {
        echo "success|Terimakasih, Selamat bekerja!|in";
        Storage::put($file, $image_base64);
      } else {
        echo  "error|Maaf, absen tidak berhasil.|in";
      }
    } else if ($jenisAbsen == 'keluar') {
      // update absen jika sudah absen masuk
      $data_pulang = [
        'tanggal_keluar' => $tanggal,
        'jam_keluar' => $jam,
        'foto_keluar' => $fileName,
        'lokasi_keluar' => $lokasi,
        'laporan_keluar'   => $laporan,
      ];

      $update = Absen::where('email', $email)
        ->whereNotNull('jam_masuk')
        ->orderBy('id', 'desc')
        ->first();

      $updateKeluar = Absen::where('id', $update->id)
        ->update($data_pulang);

      if ($updateKeluar) {
        echo "success|Terimakasih, Selamat beristirahat.|out";
        Storage::put($file, $image_base64);
      } else {
        echo "error|Maaf, absen tidak berhasil.|out";
      }
    }
  }

  public function edit(Absen $absen)
  {
    return view('absensi.edit', ['absen' => $absen]);
  }

  public function update(Absen $absen, Request $request)
  {
    $data = $request->validate([
      'email' => 'required|email',
      'nama' => 'required|string',
      'status' => 'required|in:HADIR,TIDAK HADIR,IZIN,SAKIT',
      'keterangan' => 'nullable',
      'posisi_absen' => 'nullable',
      'absen_masuk' => 'nullable',
      'absen_keluar' => 'nullable',
    ]);

    $absen->update($data);

    return redirect(route('absen.index'))->with('success', 'Absen Updated Successfully');
  }

  public function delete(Absen $absen)
  {
    $absen->delete();

    return redirect(route('absen.index'))->with('success', 'Absen Deleted Successfully');
  }

  public function editProfile()
  {
    $email = auth()->user()->email;
    $karyawan = User::where('email', $email)->first();

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

    return view('absensi.editprofile', compact('karyawan', 'selisihWaktuOut'));
  }

  public function updateprofile(Request $request)
  {
    $nama = $request->nama_lengkap;
    $email = auth()->user()->email;
    $password = Hash::make($request->password);
    $karyawan = User::where('email', $email)->first();

    if ($request->hasFile('foto')) {
      $foto = $email . "." . $request->file('foto')->getClientOriginalExtension();
    } else {
      $foto = $karyawan->foto;
    }


    if (empty($request->password)) {
      $data = [
        'nama' => $nama,
        'email' => $email,
        'foto' => $foto,
        'id_telegram' => $request->id_telegram,
      ];
    } else {
      $data = [
        'nama' => $nama,
        'email' => $email,
        'password' => $password,
        'foto' => $foto,
        'id_telegram' => $request->id_telegram,
      ];
    }

    $chatId = '649920017';
    $message = 'testing lol';

    // Send the message using TelegramController
    $telegramController = app(TelegramController::class);
    $telegramController->sendMessage($chatId, $message);

    $update = User::where('email', $email)->update($data);
    if ($update) {
      if ($request->hasFile('foto')) {
        $folderPath = "public/uploads/karyawan/";
        $uploaded = $request->file('foto')->storeAs($folderPath, $foto);
      }
      return Redirect::back()->with(['success' => 'Data berhasil di update!']);
    } else {
      return Redirect::back()->with(['error' => 'Data gagal di update!']);
    }
  }

  public function histori()
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

    $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    return view('absensi.histori', compact('namabulan', 'selisihWaktuOut'));
  }

  public function gethistori(Request $request)
  {
    $bulan = $request->bulan;
    $tahun = $request->tahun;
    $email = auth()->user()->email;

    $histori = Absen::whereRaw('MONTH(tanggal)="' . $bulan . '"')
      ->whereRaw(('YEAR(tanggal)="' . $tahun . '"'))
      ->where('email', $email)
      ->orderBy('tanggal')
      ->get();

    return view('absensi.gethistori', compact('histori'));
  }

  public function izin()
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

    $dataizin = Pengajuan_Izin::where('email', $email)->get();

    return view('absensi.izin.izin', compact('dataizin', 'selisihWaktuOut'));
  }

  public function buatizin()
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

    return view('absensi.izin.buatizin', compact('selisihWaktuOut'));
  }

  public function storeizin(Request $request)
  {
    $email = auth()->user()->email;
    $tanggal = $request->tanggal_izin;
    $status = $request->status;
    $keterangan = $request->keterangan;
    $nama = User::where('email', $email)->pluck('nama')->first();
    $idTelegram = User::where('jabatan', 'SUPERADMIN')->pluck('id_telegram')->toArray();

    if ($request->hasFile('foto')) {
      $foto = $status . "-" . $tanggal . "-" . $email . "." . $request->file('foto')->getClientOriginalExtension();
    }

    if (empty($foto)) {
      $data = [
        'email' => $email,
        'tanggal_izin' => $tanggal,
        'status' => $status,
        'keterangan' => $keterangan,
      ];
    } else {
      $data = [
        'email' => $email,
        'tanggal_izin' => $tanggal,
        'status' => $status,
        'keterangan' => $keterangan,
        'evident' => $foto,
      ];
    }

    $message = "PENGAJUAN IZIN \n\n$nama mengajukan pengajuan $status \nuntuk tanggal $tanggal \n\nDengan keterangan: \n$keterangan";

    // Send the message using TelegramController
    $telegramController = app(TelegramController::class);
    foreach ($idTelegram as $chatId) {
      $telegramController->sendMessage($chatId, $message);
    }

    $simpan = Pengajuan_Izin::insert($data);

    if ($simpan) {
      if ($request->hasFile('foto')) {
        $folderPath = "public/uploads/izin/";
        $request->file('foto')->storeAs($folderPath, $foto);
      }
      return redirect(route('absen.izin'))->with(['success' => 'Form berhasil dibuat.']);
    } else {
      return redirect(route('absen.izin'))->with(['error' => 'Form gagal dibuat.']);
    }
  }

  public function monitor()
  {
    if (auth()->user()->jabatan == 'TEAM WAGNER') {
      $jumlahIzin = Pengajuan_Izin::where('status_approved', 0)->whereIn('email', ['kucingjuna400@gmail.com', 'handhalah@sds.co.id', 'furganalathas@gmail.com'])->count();
    } else if (auth()->user()->jabatan == 'ADMIN') {
      $jumlahIzin = Pengajuan_Izin::leftJoin('users', 'pengajuan_izin.email', '=', 'users.email')->select('*')->where('status_approved', 0)->where('users.jabatan', 'KORLAP')->count();
    } else {
      $jumlahIzin = Pengajuan_Izin::where('status_approved', 0)->count();
    }

    return view('absensi.monitor', compact('jumlahIzin'));
  }

  public function getpresensi(Request $request)
  {
    $tanggal = $request->tanggal;

    if (auth()->user()->jabatan == 'TEAM WAGNER') {
      $jumlahIzin = Pengajuan_Izin::where('status_approved', 0)->whereIn('email', ['kucingjuna400@gmail.com', 'handhalah@sds.co.id', 'furganalathas@gmail.com'])->count();
    } else if (auth()->user()->jabatan == 'ADMIN') {
      $jumlahIzin = Pengajuan_Izin::leftJoin('users', 'pengajuan_izin.email', '=', 'users.email')->select('*')->where('status_approved', 0)->where('users.jabatan', 'KORLAP')->count();
    } else {
      $jumlahIzin = Pengajuan_Izin::where('status_approved', 0)->count();
    }

    if (auth()->user()->jabatan == 'TEAM WAGNER') {
      $absen = Absen::where('tanggal', $tanggal)
        ->whereIn('email', ['kucingjuna400@gmail.com', 'handhalah@sds.co.id', 'furganalathas@gmail.com'])
        ->get();
    } else if (auth()->user()->jabatan == 'ADMIN') {
      $absen = Absen::leftJoin('users', 'absens.user_id', '=', 'users.id')
        ->where('tanggal', $tanggal)
        ->where('users.jabatan', 'KORLAP')
        ->get();
    } else {
      $absen = Absen::where('tanggal', $tanggal)->get();
    }

    return view('absensi.getpresensi', compact('absen', 'jumlahIzin'));
  }

  public function records(Request $request)
  {
    // Get the value of 'tanggal' from the request
    $tanggal = $request->tanggal;

    // Check if the request is an AJAX request
    if ($request->ajax()) {

      // Check if 'tanggal' is set in the request
      if (request('tanggal')) {
        // Fetch records where 'tanggal' matches the given date
        $absens = Absen::where('tanggal', '=', $tanggal)->get();
      } else {
        // Fetch records with a conditional 'where' clause based on 'tanggal'
        $absens = Absen::when(request('std'), function ($query) use ($tanggal) {
          $query->where('tanggal', '=', $tanggal);
        })->get();
      }

      // Return the fetched records as a JSON response
      return response()->json([
        'absens' => $absens
      ]);
    } else {
      // If the request is not AJAX, abort with status code 403 (Forbidden)
      abort(403);
    }
  }

  public function getRekapPresensi(Request $request)
  {
    $tanggal = $request->tanggal;

    if (auth()->user()->jabatan == 'TEAM WAGNER') {
      $jumlahIzin = Pengajuan_Izin::where('status_approved', 0)->whereIn('email', ['kucingjuna400@gmail.com', 'handhalah@sds.co.id', 'furganalathas@gmail.com'])->count();
    } else if (auth()->user()->jabatan == 'ADMIN') {
      $jumlahIzin = Pengajuan_Izin::leftJoin('users', 'pengajuan_izin.email', '=', 'users.email')->select('*')->where('status_approved', 0)->where('users.jabatan', 'KORLAP')->count();
    } else {
      $jumlahIzin = Pengajuan_Izin::where('status_approved', 0)->count();
    }

    if (auth()->user()->jabatan == 'TEAM WAGNER') {
      $absen = Absen::whereRaw('DATE_FORMAT(tanggal, "%Y-%m") = ?', [$tanggal])
        ->whereIn('email', ['kucingjuna400@gmail.com', 'handhalah@sds.co.id', 'furganalathas@gmail.com'])
        ->get();
    } else if (auth()->user()->jabatan == 'ADMIN') {
      $absen = Absen::leftJoin('users', 'absens.user_id', '=', 'users.id')
        ->whereRaw('DATE_FORMAT(tanggal, "%Y-%m") = ?', [$tanggal])
        ->where('users.jabatan', 'KORLAP')
        ->get();
    } else {
      $absen = Absen::where('tanggal', $tanggal)->get();
    }

    return view('absensi.getrekappresensi', compact('absen', 'jumlahIzin'));
  }

  public function showmap(Request $request)
  {
    $id = $request->id;
    $absen = Absen::where('id', $id)->first();

    return view('absen.showmap', compact('absen'));
  }

  public function previewDataLaporan(Request $request)
  {
    $email = $request->email;
    $bulan = $request->bulan;
    $tahun = $request->tahun;

    if (auth()->user()->jabatan == 'TEAM WAGNER') {
      // Fetch the preview data based on the selected employee's email, month, and year
      $previewData = Absen::whereIn('email', ['kucingjuna400@gmail.com', 'handhalah@sds.co.id', 'furganalathas@gmail.com'])
        ->select(
          'absens.*',
          DB::raw('
            FLOOR(TIMESTAMPDIFF(SECOND, jam_masuk, jam_keluar) / 3600) as total_hours,
            FLOOR((TIMESTAMPDIFF(SECOND, jam_masuk, jam_keluar) % 3600) / 60) as total_minutes
        ')
        )
        ->whereRaw('MONTH(tanggal) = ?', [$bulan])
        ->whereRaw('YEAR(tanggal) = ?', [$tahun])
        ->get();
    } else if (auth()->user()->jabatan == 'ADMIN') {
      $previewData = Absen::leftJoin('users', 'absens.user_id', '=', 'users.id')
        ->select(
          'absens.*',
          DB::raw('
            FLOOR(TIMESTAMPDIFF(SECOND, jam_masuk, jam_keluar) / 3600) as total_hours,
            FLOOR((TIMESTAMPDIFF(SECOND, jam_masuk, jam_keluar) % 3600) / 60) as total_minutes
        ')
        )
        ->where('users.jabatan', 'KORLAP')
        ->whereRaw('MONTH(tanggal) = ?', [$bulan])
        ->whereRaw('YEAR(tanggal) = ?', [$tahun])
        ->get();
    } else {
      // Fetch the preview data based on the selected employee's email, month, and year
      $previewData = Absen::where('email', $email)
        ->select(
          'absens.*',
          DB::raw('
            FLOOR(TIMESTAMPDIFF(SECOND, jam_masuk, jam_keluar) / 3600) as total_hours,
            FLOOR((TIMESTAMPDIFF(SECOND, jam_masuk, jam_keluar) % 3600) / 60) as total_minutes
        ')
        )
        ->whereRaw('MONTH(tanggal) = ?', [$bulan])
        ->whereRaw('YEAR(tanggal) = ?', [$tahun])
        ->get();
    }

    foreach ($previewData as $data) {
      if ($data->total_hours !== null && $data->total_hours > 0) {
        $data->total_time = $data->total_hours . ' jam ' . $data->total_minutes . ' menit';
      } else {
        $data->total_time = '-';
      }
    }

    return response()->json($previewData);
  }

  public function previewDataRekap(Request $request)
  {
    $bulan = $request->bulan;
    $tahun = $request->tahun;

    $totalDays = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

    $selectClause = 'email, nama';

    $totalHoursClause = "0";
    $totalMinutesClause = "0";

    for ($day = 1; $day <= $totalDays; $day++) {
      $dayClause = "
            CASE 
                WHEN DAY(tanggal) = $day THEN 
                    CASE 
                        WHEN status = 'H' THEN CONCAT_WS('-', COALESCE(jam_masuk, ''), COALESCE(jam_keluar, '')) 
                        WHEN status = 'I' THEN 'I' 
                        WHEN status = 'S' THEN 'S'
                        ELSE ''
                    END 
                ELSE 
                    CASE 
                        WHEN DAYNAME(CONCAT(YEAR(tanggal), '-', MONTH(tanggal), '-', $day)) = 'Sunday' THEN 'LIBUR'
                        ELSE '' 
                    END
            END
        ";

      $hoursClause = "
            CASE 
                WHEN DAY(tanggal) = $day AND status = 'H' THEN FLOOR(TIMESTAMPDIFF(SECOND, jam_masuk, jam_keluar) / 3600)
                ELSE 0
            END
        ";

      $minutesClause = "
            CASE 
                WHEN DAY(tanggal) = $day AND status = 'H' THEN FLOOR((TIMESTAMPDIFF(SECOND, jam_masuk, jam_keluar) % 3600) / 60)
                ELSE 0
            END
        ";

      $selectClause .= ", MAX($dayClause) as tgl_$day";
      $selectClause .= ", MAX($hoursClause) as total_hours_$day";
      $selectClause .= ", MAX($minutesClause) as total_minutes_$day";

      $totalHoursClause .= " + COALESCE(MAX($hoursClause), 0)";
      $totalMinutesClause .= " + COALESCE(MAX($minutesClause), 0)";
    }

    // This part will calculate the total_hours_month and total_minutes_month separately
    $selectClause .= ", ($totalHoursClause) as total_hours_month_raw";
    $selectClause .= ", ($totalMinutesClause) as total_minutes_month_raw";

    if (auth()->user()->jabatan == 'TEAM WAGNER') {
      $previewData = Absen::selectRaw($selectClause)
        ->whereRaw('MONTH(tanggal) = ?', [$bulan])
        ->whereRaw('YEAR(tanggal) = ?', [$tahun])
        ->whereIn('email', ['kucingjuna400@gmail.com', 'handhalah@sds.co.id', 'furganalathas@gmail.com'])
        ->groupByRaw('email, nama')
        ->get();
    } else if (auth()->user()->jabatan == 'ADMIN') {
      $previewData = Absen::leftJoin('users', 'absens.jabatan', '=', 'users.id')
        ->where('users.jabatan', 'KORLAP')
        ->selectRaw($selectClause)
        ->whereRaw('MONTH(tanggal) = ?', [$bulan])
        ->whereRaw('YEAR(tanggal) = ?', [$tahun])
        ->groupByRaw('email, nama')
        ->get();
    } else {
      $previewData = Absen::selectRaw($selectClause)
        ->whereRaw('MONTH(tanggal) = ?', [$bulan])
        ->whereRaw('YEAR(tanggal) = ?', [$tahun])
        ->groupByRaw('email, nama')
        ->get();
    }

    // Post-process to correct the total hours and minutes for the month
    foreach ($previewData as $data) {
      $total_hours = $data->total_hours_month_raw;
      $total_minutes = $data->total_minutes_month_raw;

      // Convert excess minutes to hours
      $additional_hours = floor($total_minutes / 60);
      $remaining_minutes = $total_minutes % 60;

      // Add the additional hours to total hours
      $data->total_hours_month = $total_hours + $additional_hours;
      $data->total_minutes_month = $remaining_minutes;
    }

    return response()->json($previewData);
  }

  public function laporan(Request $request)
  {
    $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    $user = User::orderBy('nama')->get();
    if (auth()->user()->jabatan == 'TEAM WAGNER') {
      $jumlahIzin = Pengajuan_Izin::where('status_approved', 0)->whereIn('email', ['kucingjuna400@gmail.com', 'handhalah@sds.co.id', 'furganalathas@gmail.com'])->count();
    } else if (auth()->user()->jabatan == 'ADMIN') {
      $jumlahIzin = Pengajuan_Izin::leftJoin('users', 'pengajuan_izin.email', '=', 'users.email')->select('*')->where('status_approved', 0)->where('users.jabatan', 'KORLAP')->count();
    } else {
      $jumlahIzin = Pengajuan_Izin::where('status_approved', 0)->count();
    }

    $email = $request->email;
    $bulan = $request->bulan;
    $tahun = $request->tahun;

    if (auth()->user()->jabatan == 'TEAM WAGNER') {
      $user = User::whereIn('email', ['kucingjuna400@gmail.com', 'handhalah@sds.co.id', 'furganalathas@gmail.com'])
        ->orderBy('nama')
        ->get();
    } else if (auth()->user()->jabatan == 'ADMIN') {
      $user = User::where('jabatan', 'KORLAP')
        ->orderBy('nama')
        ->get();
    } else {
      $user = User::orderBy('nama')->get();
    }

    $absen = Absen::where('email', $email)
      ->whereRaw('MONTH(tanggal) = ?', [$bulan])
      ->whereRaw('YEAR(tanggal) = ?', [$tahun])
      ->orderBy('tanggal')
      ->get();

    return view('absensi.laporan.laporan', compact('namabulan', 'user', 'jumlahIzin', 'absen'));
  }

  public function cetaklaporan(Request $request)
  {
    $email = $request->email;
    $bulan = $request->bulan;
    $tahun = $request->tahun;
    $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    $user = User::where('email', $email)->first();
    $absen = Absen::where('email', $email)
      ->select(
        'absens.*',
        DB::raw('
        FLOOR(TIMESTAMPDIFF(SECOND, jam_masuk, jam_keluar) / 3600) as total_hours,
        FLOOR((TIMESTAMPDIFF(SECOND, jam_masuk, jam_keluar) % 3600) / 60) as total_minutes
    ')
      )
      ->whereRaw('MONTH(tanggal) = ?', [$bulan])
      ->whereRaw('YEAR(tanggal) = ?', [$tahun])
      ->orderBy('tanggal')
      ->get();

    foreach ($absen as $data) {
      if ($data->total_hours !== null && $data->total_hours > 0) {
        $data->total_time = $data->total_hours . ' jam ' . $data->total_minutes . ' menit';
      } else {
        $data->total_time = '-';
      }
    }

    if (isset($_POST['exportExcel'])) {
      $time = date("d-m-Y H:i:s");
      // fungsi header dengan mengirimkan raw data excel
      header("Content-type: application/vnd-ms-excel");
      // mendefinisikan nama file export "hasil-export.xls"
      header("Content-Disposition: attachment; filename=Laporan Absensi $time.xls");
    }

    return view('absensi.laporan.cetaklaporan', compact('bulan', 'tahun', 'namabulan', 'user', 'absen'));
  }

  public function rekap()
  {
    $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    if (auth()->user()->jabatan == 'TEAM WAGNER') {
      $jumlahIzin = Pengajuan_Izin::where('status_approved', 0)->whereIn('email', ['kucingjuna400@gmail.com', 'handhalah@sds.co.id', 'furganalathas@gmail.com'])->count();
    } else if (auth()->user()->jabatan == 'ADMIN') {
      $jumlahIzin = Pengajuan_Izin::leftJoin('users', 'pengajuan_izin.email', '=', 'users.email')->select('*')->where('status_approved', 0)->where('users.jabatan', 'KORLAP')->count();
    } else {
      $jumlahIzin = Pengajuan_Izin::where('status_approved', 0)->count();
    }

    return view('absensi.laporan.rekap', compact('namabulan', 'jumlahIzin'));
  }

  public function cetakrekap(Request $request)
  {
    $bulan = str_pad($request->bulan, 2, "0", STR_PAD_LEFT);
    $bulans = $request->bulan;
    $tahun = $request->tahun;
    $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    $totalDays =  cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

    if (auth()->user()->jabatan == 'TEAM WAGNER') {
      $rekap = Absen::select([
        "users.perner",
        "users.jabatan",
        "absens.email",
        "absens.nama",
        "absens.status",
        "absens.tanggal",
        "absens.jam_masuk",
        "absens.jam_keluar",
        DB::raw("DAYNAME(absens.tanggal) AS hari"),
        DB::raw("DAY(absens.tanggal) AS date"),
        DB::raw('
            FLOOR(TIMESTAMPDIFF(SECOND, absens.jam_masuk, absens.jam_keluar) / 3600) as total_hours,
            FLOOR((TIMESTAMPDIFF(SECOND, absens.jam_masuk, absens.jam_keluar) % 3600) / 60) as total_minutes
        ')
      ])
        ->leftJoin('users', 'absens.email', '=', 'users.email')
        ->whereRaw('MONTH(absens.tanggal) = ?', [$bulan])
        ->whereRaw('YEAR(absens.tanggal) = ?', [$tahun])
        ->whereIn('absens.email', ['kucingjuna400@gmail.com', 'handhalah@sds.co.id', 'furganalathas@gmail.com'])
        // ->groupByRaw('email, nama')
        ->get();
    } else if (auth()->user()->jabatan == 'ADMIN') {
      $rekap = Absen::select([
        "users.perner",
        "users.jabatan",
        "absens.email",
        "absens.nama",
        "absens.status",
        "absens.tanggal",
        "absens.jam_masuk",
        "absens.jam_keluar",
        DB::raw("DAYNAME(absens.tanggal) AS hari"),
        DB::raw("DAY(absens.tanggal) AS date"),
        DB::raw('
            FLOOR(TIMESTAMPDIFF(SECOND, absens.jam_masuk, absens.jam_keluar) / 3600) as total_hours,
            FLOOR((TIMESTAMPDIFF(SECOND, absens.jam_masuk, absens.jam_keluar) % 3600) / 60) as total_minutes
        ')
      ])
        ->leftJoin('users', 'absens.email', '=', 'users.email')
        ->whereRaw('MONTH(absens.tanggal) = ?', [$bulan])
        ->whereRaw('YEAR(absens.tanggal) = ?', [$tahun])
        ->where('users.jabatan', 'KORLAP')
        ->whereIn('absens.email', ['kucingjuna400@gmail.com', 'handhalah@sds.co.id', 'furganalathas@gmail.com'])
        // ->groupByRaw('email, nama')
        ->get();
    } else {
      $rekap = Absen::select([
        "users.perner",
        "users.jabatan",
        "absens.email",
        "absens.nama",
        "absens.status",
        "absens.tanggal",
        "absens.jam_masuk",
        "absens.jam_keluar",
        DB::raw("DAYNAME(absens.tanggal) AS hari"),
        DB::raw("DAY(absens.tanggal) AS date"),
        DB::raw('
            FLOOR(TIMESTAMPDIFF(SECOND, absens.jam_masuk, absens.jam_keluar) / 3600) as total_hours,
            FLOOR((TIMESTAMPDIFF(SECOND, absens.jam_masuk, absens.jam_keluar) % 3600) / 60) as total_minutes
        ')
      ])
        ->leftJoin('users', 'absens.email', '=', 'users.email')
        ->whereRaw('MONTH(absens.tanggal) = ?', [$bulan])
        ->whereRaw('YEAR(absens.tanggal) = ?', [$tahun])
        // ->groupByRaw('email, nama')
        ->get();
    }

    $total_hours_month_raw = 0;
    $total_minutes_month_raw = 0;

    $result = [];
    foreach ($rekap as $item) {

      if (!array_key_exists($item->email, $result)) {
        $result[$item->email] = [
          "perner" => $item->perner,
          "jabatan" => $item->jabatan,
          "nama" => $item->nama,
          "email" => $item->email,
          "total_hours_month" => 0,
          "total_minutes_month" => 0,
        ];

        for ($day = 1; $day <= $totalDays; $day++) {
          $today = Carbon::createFromFormat("Ymj", "{$tahun}{$bulan}{$day}");

          if ($today->englishDayOfWeek === "Sunday") {
            $result[$item->email]['tgl_' . $day] = "LIBUR";
            $result[$item->email]['total_hours_' . $day] = 0;
            $result[$item->email]['total_minutes_' . $day] = 0;
            // $total_hours_month_raw += $result[$item->email]['total_hours_' . $day];
          } else {
            $result[$item->email]['tgl_' . $day] = null;
            $result[$item->email]['total_hours_' . $day] = null;
            $result[$item->email]['total_minutes_' . $day] = null;
          }
        }
      }

      $total_hours_month_raw += $item['total_hours'];

      $result[$item->email]['tgl_' . $item->date] = match ($item->status) {
        "H", "0" => "{$item->jam_masuk}-{$item->jam_keluar}",
        'I' => 'I',
        'S' => 'S',
        null => "A"
      };

      if ($item->status == 'H' && $item->total_hours !== null && $item->total_minutes !== null) {
        $result[$item->email]['total_hours_' . $item->date] = $item->total_hours;
        $result[$item->email]['total_minutes_' . $item->date] = $item->total_minutes;

        $result[$item->email]['total_hours_month'] += $item->total_hours;
        $result[$item->email]['total_minutes_month'] += $item->total_minutes;
      }
    }

    foreach ($result as $email => $data) {
      $total_minutes = $data['total_minutes_month'];
      $additional_hours = floor($total_minutes / 60);
      $remaining_minutes = $total_minutes % 60;

      $result[$email]['total_hours_month'] += $additional_hours;
      $result[$email]['total_minutes_month'] = $remaining_minutes;
    }

    if (isset($_POST['exportExcel'])) {
      $time = date("d-m-Y H:i:s");
      // fungsi header dengan mengirimkan raw data excel
      header("Content-type: application/vnd-ms-excel");
      // mendefinisikan nama file export "hasil-export.xls"
      header("Content-Disposition: attachment; filename=Rekap Absensi $time.xls");
    }

    return view('absensi.laporan.cetakrekap', compact('bulan', 'tahun', 'rekap', 'namabulan', 'bulans', 'result', 'totalDays'));
  }

  public function izinsakit(Request $request)
  {
    if (auth()->user()->jabatan == 'TEAM WAGNER') {
      $query = Pengajuan_Izin::query();
      $query->select('pengajuan_izin.id', 'tanggal_izin', 'pengajuan_izin.email', 'nama', 'jabatan', 'status', 'status_approved', 'keterangan', 'evident');
      $query->whereIn('pengajuan_izin.email', ['kucingjuna400@gmail.com', 'handhalah@sds.co.id', 'furganalathas@gmail.com']);
      $query->join('users', 'pengajuan_izin.email', '=', 'users.email');
      if (!empty($request->dari) && !empty($request->sampai)) {
        $query->whereBetween('tanggal_izin', [$request->dari, $request->sampai]);
      }
      $query->orderBy('tanggal_izin', 'desc');
      $izinsakit = $query->get();
    } else if (auth()->user()->jabatan == 'ADMIN') {
      $query = Pengajuan_Izin::query();
      $query->select('pengajuan_izin.id', 'tanggal_izin', 'pengajuan_izin.email', 'nama', 'jabatan', 'status', 'status_approved', 'keterangan', 'evident');
      $query->whereIn('pengajuan_izin.email', ['kucingjuna400@gmail.com', 'handhalah@sds.co.id', 'furganalathas@gmail.com']);
      $query->where('users.jabatan', 'KORLAP');
      $query->join('users', 'pengajuan_izin.email', '=', 'users.email');
      if (!empty($request->dari) && !empty($request->sampai)) {
        $query->whereBetween('tanggal_izin', [$request->dari, $request->sampai]);
      }
      $query->orderBy('tanggal_izin', 'desc');
      $izinsakit = $query->get();
    } else {
      $query = Pengajuan_Izin::query();
      $query->select('pengajuan_izin.id', 'tanggal_izin', 'pengajuan_izin.email', 'nama', 'jabatan', 'status', 'status_approved', 'keterangan', 'evident');
      $query->join('users', 'pengajuan_izin.email', '=', 'users.email');
      if (!empty($request->dari) && !empty($request->sampai)) {
        $query->whereBetween('tanggal_izin', [$request->dari, $request->sampai]);
      }
      $query->orderBy('tanggal_izin', 'desc');
      $izinsakit = $query->get();
    }

    if (auth()->user()->jabatan == 'TEAM WAGNER') {
      $jumlahIzin = Pengajuan_Izin::where('status_approved', 0)->whereIn('email', ['kucingjuna400@gmail.com', 'handhalah@sds.co.id', 'furganalathas@gmail.com'])->count();
    } else if (auth()->user()->jabatan == 'ADMIN') {
      $jumlahIzin = Pengajuan_Izin::leftJoin('users', 'pengajuan_izin.email', '=', 'users.email')->select('*')->where('status_approved', 0)->where('users.jabatan', 'KORLAP')->count();
    } else {
      $jumlahIzin = Pengajuan_Izin::where('status_approved', 0)->count();
    }

    // $izinsakit->appends($request->all());
    return view('absensi.izin.izinsakit', compact('izinsakit', 'jumlahIzin'));
  }

  public function action(Request $request)
  {
    $status_approved = $request->status_approved;
    $id_izin_form = $request->id_izin_form;
    $status_izin_form = $request->status_izin_form;
    $tanggal = $request->tanggal_izin_form;
    $evident = $request->evident_izin_form;
    $nama = $request->nama_izin_form;
    $email = $request->email_izin_form;
    $user_id = User::where('email', $email)->first()->id;

    if ($status_approved == 1) {
      if ($status_izin_form == "SAKIT") {
        $status = "S";
      } else {
        $status = "I";
      }

      $data = [
        'email' => $email,
        'nama' => $nama,
        'status' => $status,
        'tanggal' => $tanggal,
        'jam_masuk' => "00:00:00",
        'jam_keluar' => "00:00:00",
        'foto_masuk' => $evident ?? '',
        'foto_keluar' => $evident ?? '',
        'lokasi_masuk' => "",
        'lokasi_keluar' => "",
        'laporan_masuk' => $status_izin_form,
        'laporan_keluar' => $status_izin_form,
        'status_validasi' => 1,
        'user_id' => $user_id,
      ];

      $simpan = Absen::insert($data);
    }

    $update = Pengajuan_Izin::where('id', $id_izin_form)->update([
      'status_approved' => $status_approved,
    ]);

    if ($update) {
      return Redirect::back()->with(['success' => 'Data berhasil di Update']);
    } else {
      return Redirect::back()->with(['warning' => 'Data gagal di Update']);
    }
  }

  public function batalapprove($id)
  {
    $update = Pengajuan_Izin::where('id', $id)->update([
      'status_approved' => 0,
    ]);
    if ($update) {
      return Redirect::back()->with(['success' => 'Data berhasil di Update']);
    } else {
      return Redirect::back()->with(['warning' => 'Data gagal di Update']);
    }
  }

  public function cekizin(Request $request)
  {
    $tanggal = $request->tanggal_izin;
    $email = auth()->user()->email;

    $cek = Pengajuan_Izin::where('email', $email)->where('tanggal_izin', $tanggal)->count();

    return $cek;
  }
}
