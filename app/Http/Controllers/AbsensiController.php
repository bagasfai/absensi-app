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

class AbsensiController extends Controller
{
  public function index()
  {
    $absen = Absen::all();
    return view('absensi.index', ['absen' => $absen]);
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
    $cek = Absen::where('tanggal', $hariini)->where('email', $email)->count();
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
      $selisihWaktu = "";
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
        'status' => 0,
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
      ];
    } else {
      $data = [
        'nama' => $nama,
        'email' => $email,
        'password' => $password,
        'foto' => $foto,
      ];
    }

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

    return view('absensi.izin', compact('dataizin', 'selisihWaktuOut'));
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

    return view('absensi.buatizin', compact('selisihWaktuOut'));
  }

  public function storeizin(Request $request)
  {
    $email = auth()->user()->email;
    $tanggal = $request->tanggal_izin;
    $status = $request->status;
    $keterangan = $request->keterangan;
    $karyawan = User::where('email', $email)->first();

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
    return view('absensi.monitor');
  }

  public function getpresensi(Request $request)
  {
    $tanggal = $request->tanggal;
    $absen = Absen::where('tanggal', $tanggal)->get();

    return view('absensi.getpresensi', compact('absen'));
  }

  public function showmap(Request $request)
  {
    $id = $request->id;
    $absen = Absen::where('id', $id)->first();

    return view('absen.showmap', compact('absen'));
  }

  public function laporan()
  {
    $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    $user = User::orderBy('nama')->get();

    return view('absensi.laporan', compact('namabulan', 'user'));
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
      return view('absensi.cetaklaporanexcel', compact('bulan', 'tahun', 'namabulan', 'user', 'absen'));
    }

    return view('absensi.cetaklaporan', compact('bulan', 'tahun', 'namabulan', 'user', 'absen'));
  }

  public function rekap()
  {
    $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

    return view('absensi.rekap', compact('namabulan'));
  }

  public function cetakrekap(Request $request)
  {
    $bulan = $request->bulan;
    $tahun = $request->tahun;
    $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

    $rekap = Absen::selectRaw('email, nama, 
    MAX(IF(DAY(tanggal) = 1, IFNULL(CONCAT(jam_masuk, "-", jam_keluar), ""), "")) as tgl_1,
    MAX(IF(DAY(tanggal) = 2, IFNULL(CONCAT(jam_masuk, "-", jam_keluar), ""), "")) as tgl_2,
    MAX(IF(DAY(tanggal) = 3, IFNULL(CONCAT(jam_masuk, "-", jam_keluar), ""), "")) as tgl_3,
    MAX(IF(DAY(tanggal) = 4, IFNULL(CONCAT(jam_masuk, "-", jam_keluar), ""), "")) as tgl_4,
    MAX(IF(DAY(tanggal) = 5, IFNULL(CONCAT(jam_masuk, "-", jam_keluar), ""), "")) as tgl_5,
    MAX(IF(DAY(tanggal) = 6, IFNULL(CONCAT(jam_masuk, "-", jam_keluar), ""), "")) as tgl_6,
    MAX(IF(DAY(tanggal) = 7, IFNULL(CONCAT(jam_masuk, "-", jam_keluar), ""), "")) as tgl_7,
    MAX(IF(DAY(tanggal) = 8, IFNULL(CONCAT(jam_masuk, "-", jam_keluar), ""), "")) as tgl_8,
    MAX(IF(DAY(tanggal) = 9, IFNULL(CONCAT(jam_masuk, "-", jam_keluar), ""), "")) as tgl_9,
    MAX(IF(DAY(tanggal) = 10, IFNULL(CONCAT(jam_masuk, "-", jam_keluar), ""), "")) as tgl_10,
    MAX(IF(DAY(tanggal) = 11, IFNULL(CONCAT(jam_masuk, "-", jam_keluar), ""), "")) as tgl_11,
    MAX(IF(DAY(tanggal) = 12, IFNULL(CONCAT(jam_masuk, "-", jam_keluar), ""), "")) as tgl_12,
    MAX(IF(DAY(tanggal) = 13, IFNULL(CONCAT(jam_masuk, "-", jam_keluar), ""), "")) as tgl_13,
    MAX(IF(DAY(tanggal) = 14, IFNULL(CONCAT(jam_masuk, "-", jam_keluar), ""), "")) as tgl_14,
    MAX(IF(DAY(tanggal) = 15, IFNULL(CONCAT(jam_masuk, "-", jam_keluar), ""), "")) as tgl_15,
    MAX(IF(DAY(tanggal) = 16, IFNULL(CONCAT(jam_masuk, "-", jam_keluar), ""), "")) as tgl_16,
    MAX(IF(DAY(tanggal) = 17, IFNULL(CONCAT(jam_masuk, "-", jam_keluar), ""), "")) as tgl_17,
    MAX(IF(DAY(tanggal) = 18, IFNULL(CONCAT(jam_masuk, "-", jam_keluar), ""), "")) as tgl_18,
    MAX(IF(DAY(tanggal) = 19, IFNULL(CONCAT(jam_masuk, "-", jam_keluar), ""), "")) as tgl_19,
    MAX(IF(DAY(tanggal) = 20, IFNULL(CONCAT(jam_masuk, "-", jam_keluar), ""), "")) as tgl_20,
    MAX(IF(DAY(tanggal) = 21, IFNULL(CONCAT(jam_masuk, "-", jam_keluar), ""), "")) as tgl_21,
    MAX(IF(DAY(tanggal) = 22, IFNULL(CONCAT(jam_masuk, "-", jam_keluar), ""), "")) as tgl_22,
    MAX(IF(DAY(tanggal) = 23, IFNULL(CONCAT(jam_masuk, "-", jam_keluar), ""), "")) as tgl_23,
    MAX(IF(DAY(tanggal) = 24, IFNULL(CONCAT(jam_masuk, "-", jam_keluar), ""), "")) as tgl_24,
    MAX(IF(DAY(tanggal) = 25, IFNULL(CONCAT(jam_masuk, "-", jam_keluar), ""), "")) as tgl_25,
    MAX(IF(DAY(tanggal) = 26, IFNULL(CONCAT(jam_masuk, "-", jam_keluar), ""), "")) as tgl_26,
    MAX(IF(DAY(tanggal) = 27, IFNULL(CONCAT(jam_masuk, "-", jam_keluar), ""), "")) as tgl_27,
    MAX(IF(DAY(tanggal) = 28, IFNULL(CONCAT(jam_masuk, "-", jam_keluar), ""), "")) as tgl_28,
    MAX(IF(DAY(tanggal) = 29, IFNULL(CONCAT(jam_masuk, "-", jam_keluar), ""), "")) as tgl_29,
    MAX(IF(DAY(tanggal) = 30, IFNULL(CONCAT(jam_masuk, "-", jam_keluar), ""), "")) as tgl_30,
    MAX(IF(DAY(tanggal) = 31, IFNULL(CONCAT(jam_masuk, "-", jam_keluar), ""), "")) as tgl_31')
      ->whereRaw('MONTH(tanggal) = ?', [$bulan])
      ->whereRaw('YEAR(tanggal) = ?', [$tahun])
      ->groupByRaw('email,nama')
      ->get();

    if (isset($_POST['exportExcel'])) {
      $time = date("d-m-Y H:i:s");
      // fungsi header dengan mengirimkan raw data excel
      header("Content-type: application/vnd-ms-excel");
      // mendefinisikan nama file export "hasil-export.xls"
      header("Content-Disposition: attachment; filename=Rekap Absensi $time.xls");
    }


    return view('absensi.cetakrekap', compact('bulan', 'tahun', 'rekap', 'namabulan'));
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
    // $izinsakit->appends($request->all());
    return view('absensi.izinsakit', compact('izinsakit'));
  }

  public function action(Request $request)
  {
    $status_approved = $request->status_approved;
    $id_izin_form = $request->id_izin_form;
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
