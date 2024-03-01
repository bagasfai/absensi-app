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


class AbsensiController extends Controller
{
  public function index()
  {
    $absen = Absen::all();
    $jumlahIzin = Pengajuan_Izin::where('status_approved', 0)->count();
    return view('absensi.index', compact('absen', 'jumlahIzin'));
  }

  public function masuk()
  {
    $email = auth()->user()->email; // Assuming 'email' is the column in the users table
    $absen = User::where('email', $email)->get();

    return view('absensi.create', ['absen' => $absen]);
  }

  public function keluar(Absen $id, Request $request)
  {

    $email = auth()->user()->email; // Assuming 'email' is the column in the users table
    $absen = User::where('email', $email)->get();

    return view('absensi.keluar', ['absen' => $absen]);
  }

  public function create()
  {
    $hariini = date("Y-m-d");
    $email = auth()->user()->email;
    $cek = Absen::where('email', $email)->whereNull('jam_keluar')->orderBy('id', 'desc')->first();
    $currentDateTime = now();
    $latestEntry = Absen::select(DB::raw('CONCAT(tanggal, " ", jam_masuk) as datetime'))
      ->where('email', $email)
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

    $cek = Absen::where('tanggal', $tanggal)->where('email', $email)->count();
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

  public function absenMasuk(Request $request)
  {
    $data = $request->validate([
      'email' => 'required|string',
      'nama' => 'required|string',
      'status' => 'required|in:HADIR,TIDAK HADIR,IZIN,SAKIT',
      'keterangan' => 'nullable',
      'posisi_absen' => 'nullable',
    ]);

    $data['absen_masuk'] = Carbon::now();
    $data['posisi_absen'] = $request->input('posisi_absen');

    $absen = Absen::create($data);

    return redirect(route('absen.index'));
  }

  public function absenKeluar(Request $request)
  {
    $data = $request->validate([
      'email' => 'required|string',
      'nama' => 'required|string',
      'laporan' => 'required',
      'posisi_absen' => 'nullable',
    ]);

    $data['absen_keluar'] = Carbon::now();
    $data['posisi_absen'] = $request->input('posisi_absen');

    $absen = Absen::create($data);

    return redirect(route('absen.index'));
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

    $cek = Absen::where('email', $email)->orderBy('id', 'desc')->first();

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

    $cek = Absen::where('email', $email)->orderBy('id', 'desc')->first();

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

    $cek = Absen::where('email', $email)->orderBy('id', 'desc')->first();

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

    $cek = Absen::where('email', $email)->orderBy('id', 'desc')->first();

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
    $jumlahIzin = Pengajuan_Izin::where('status_approved', 0)->count();
    return view('absensi.monitor', compact('jumlahIzin'));
  }

  public function getpresensi(Request $request)
  {
    $tanggal = $request->tanggal;
    $absen = Absen::where('tanggal', $tanggal)->get();
    $jumlahIzin = Pengajuan_Izin::where('status_approved', 0)->count();

    return view('absensi.getpresensi', compact('absen', 'jumlahIzin'));
  }

  public function showmap(Request $request)
  {
    $id = $request->id;
    $absen = Absen::where('id', $id)->first();

    return view('absen.showmap', compact('absen'));
  }

  public function laporan(Request $request)
  {
    $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    $user = User::orderBy('nama')->get();
    $jumlahIzin = Pengajuan_Izin::where('status_approved', 0)->count();

    $email = $request->email;
    $bulan = $request->bulan;
    $tahun = $request->tahun;

    $absen = Absen::where('email', $email)
      ->whereRaw('MONTH(tanggal) = ?', [$bulan])
      ->whereRaw('YEAR(tanggal) = ?', [$tahun])
      ->orderBy('tanggal')
      ->get();

    return view('absensi.laporan.laporan', compact('namabulan', 'user', 'jumlahIzin', 'absen'));
  }

  public function previewDataLaporan(Request $request)
  {
    $email = $request->email;
    $bulan = $request->bulan;
    $tahun = $request->tahun;

    // Fetch the preview data based on the selected employee's email, month, and year
    $previewData = Absen::where('email', $email)
      ->whereRaw('MONTH(tanggal) = ?', [$bulan])
      ->whereRaw('YEAR(tanggal) = ?', [$tahun])
      ->get();

    return response()->json($previewData);
  }

  public function previewDataRekap(Request $request)
  {
    $bulan = $request->bulan;
    $tahun = $request->tahun;

    $totalDays = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

    $selectClause = 'email, nama';

    for ($day = 1; $day <= $totalDays; $day++) {
      $selectClause .= ", MAX(
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
      ) as tgl_$day";
    }

    $previewData = Absen::selectRaw($selectClause)
      ->whereRaw('MONTH(tanggal) = ?', [$bulan])
      ->whereRaw('YEAR(tanggal) = ?', [$tahun])
      ->groupByRaw('email, nama')
      ->get();

    return response()->json($previewData);
  }

  public function cetaklaporan(Request $request)
  {
    $email = $request->email;
    $bulan = $request->bulan;
    $tahun = $request->tahun;
    $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    $user = User::where('email', $email)->first();
    $absen = Absen::where('email', $email)
      ->whereRaw('MONTH(tanggal) = ?', [$bulan])
      ->whereRaw('YEAR(tanggal) = ?', [$tahun])
      ->orderBy('tanggal')
      ->get();

    if (isset($_POST['exportExcel'])) {
      $time = date("d-m-Y H:i:s");
      // fungsi header dengan mengirimkan raw data excel
      header("Content-type: application/vnd-ms-excel");
      // mendefinisikan nama file export "hasil-export.xls"
      header("Content-Disposition: attachment; filename=Laporan Absensi $time.xls");
      return view('absensi.laporan.cetaklaporanexcel', compact('bulan', 'tahun', 'namabulan', 'user', 'absen'));
    }

    return view('absensi.laporan.cetaklaporan', compact('bulan', 'tahun', 'namabulan', 'user', 'absen'));
  }

  public function rekap()
  {
    $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    $jumlahIzin = Pengajuan_Izin::where('status_approved', 0)->count();

    return view('absensi.laporan.rekap', compact('namabulan', 'jumlahIzin'));
  }

  public function cetakrekap(Request $request)
  {
    $bulan = str_pad($request->bulan, 2, "0", STR_PAD_LEFT);
    $bulans = $request->bulan;
    $tahun = $request->tahun;
    $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    $totalDays =  cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

    $rekap = Absen::select([
      "email",
      "nama",
      "status",
      "tanggal",
      "jam_masuk",
      "jam_keluar",
      DB::raw("DAYNAME(tanggal) AS hari"),
      DB::raw("DAY(tanggal) AS date"),
    ])
      ->whereRaw('MONTH(tanggal) = ?', [$bulan])
      ->whereRaw('YEAR(tanggal) = ?', [$tahun])
      // ->groupByRaw('email, nama')
      ->get();

    $result = [];
    foreach ($rekap as $item) {

      if (!array_key_exists($item->email, $result)) {
        $result[$item->email] = [
          "nama" => $item->nama,
          "email" => $item->email,
        ];

        for ($day = 1; $day <= $totalDays; $day++) {
          $today = Carbon::createFromFormat("Ymj", "{$tahun}{$bulan}{$day}");

          if ($today->englishDayOfWeek === "Sunday") {
            $result[$item->email][$day] = "LIBUR";
          } else {
            $result[$item->email][$day] = null;
          }
        }
      }

      $result[$item->email][$item->date] = match ($item->status) {
        "H", "0" => "{$item->jam_masuk}-{$item->jam_keluar}",
        'I' => 'I',
        'S' => 'S',
        null => "A"
      };
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
    $query = Pengajuan_Izin::query();
    $query->select('pengajuan_izin.id', 'tanggal_izin', 'pengajuan_izin.email', 'nama', 'jabatan', 'status', 'status_approved', 'keterangan', 'evident');
    $query->join('users', 'pengajuan_izin.email', '=', 'users.email');
    if (!empty($request->dari) && !empty($request->sampai)) {
      $query->whereBetween('tanggal_izin', [$request->dari, $request->sampai]);
    }
    $query->orderBy('tanggal_izin', 'desc');
    $izinsakit = $query->get();

    $jumlahIzin = Pengajuan_Izin::where('status_approved', 0)->count();

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
