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

class DashboardController extends Controller
{
	public function dashboard()
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

		$cek = Absen::where('email', $email)->orderBy('id', 'desc')->first();

		$absenBulan = Absen::where('email', $email)
			->whereRaw('MONTH(tanggal) = ?', [$bulan])
			->whereRaw('YEAR(tanggal) = ?', [$tahun])
			->orderBy('tanggal')->get();

		$user = User::where('email', $email)->first();

		$rekapAbsen = Absen::selectRaw('COUNT(email) AS jumlah_hadir')
			->where('email', $email)
			->whereRaw('MONTH(tanggal) = ?', [$bulan])
			->whereRaw('YEAR(tanggal) = ?', [$tahun])
			->whereNotNull('jam_masuk')
			->whereNotNull('jam_keluar')
			->first();

		$daftarHadir = Absen::join('users', 'absens.email', '=', 'users.email')
			->where('tanggal', $hariini)
			->orderBy('jam_masuk')
			->get();

		$namaBulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

		$rekapIzin = Pengajuan_Izin::selectRaw('SUM(IF(status="SAKIT",1,0)) AS jumlah_sakit, SUM(IF(status="IZIN",1,0)) AS jumlah_izin')
			->where('email', $email)
			->where('status_approved', 1)
			->whereRaw('MONTH(tanggal_izin) = ?', [$bulan])
			->whereRaw('YEAR(tanggal_izin) = ?', [$tahun])
			->first();


		return view('dashboard', compact('absen', 'cek', 'user', 'absenBulan', 'namaBulan', 'bulan', 'tahun', 'rekapAbsen', 'daftarHadir', 'rekapIzin', 'latestEntry', 'selisihWaktu', 'selisihWaktuOut'));
	}

	public function dashboardadmin()
	{
		$tahun = date('Y');
		$bulan = date('m') * 1;
		$hariini =  date("Y-m-d");
		$user = User::count();
		$jumlahIzin = Pengajuan_Izin::select('*')->where('status_approved', 0)->count();
		$rekapAbsen = Absen::selectRaw('COUNT(email) AS jumlah_hadir')
			->where('tanggal', $hariini)
			->whereNotNull('jam_masuk')
			->whereNotNull('jam_keluar')
			->first();

		$rekapIzin = Pengajuan_Izin::selectRaw('SUM(IF(status="SAKIT",1,0)) AS jumlah_sakit, SUM(IF(status="IZIN",1,0)) AS jumlah_izin')
			->where('tanggal_izin', $hariini)
			->where('status_approved', 1)
			->whereRaw('MONTH(tanggal_izin) = ?', [$bulan])
			->whereRaw('YEAR(tanggal_izin) = ?', [$tahun])
			->first();

		return view('dashboardadmin', compact('rekapAbsen', 'rekapIzin', 'user', 'jumlahIzin'));
	}
}
