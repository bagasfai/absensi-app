<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Absen;
use App\Models\User;
use App\Models\Jabatan;
use App\Models\Pengajuan_Izin;
use Symfony\Component\VarDumper\Caster\RedisCaster;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
	public function index()
	{
		$jabatan = Jabatan::all();

		if (auth()->user()->jabatan == 'TEAM WAGNER') {
			$user = User::whereIn('email', ['kucingjuna400@gmail.com', 'handhalah@sds.co.id', 'furganalathas@gmail.com'])->get();
		} else if (auth()->user()->jabatan == 'ADMIN') {
			$user = User::where('jabatan', 'KORLAP')->orderBy('nama')->get();
		} else {
			$user = User::orderBy('nama')->get();
		}

		if (auth()->user()->jabatan == 'ADMIN') {
			$jumlahIzin = Pengajuan_Izin::leftJoin('users', 'pengajuan_izin.email', '=', 'users.email')->select('*')->where('status_approved', 0)->where('users.jabatan', 'KORLAP')->count();
		} else {
			$jumlahIzin = Pengajuan_Izin::select('*')->where('status_approved', 0)->count();
		}

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
		return view('user.index', compact('user', 'jabatan', 'selisihWaktuOut', 'jumlahIzin'));
	}

	public function store(Request $request)
	{
		$nama = $request->nama;
		$jabatan = $request->jabatan;
		$email = $request->email;
		$password = Hash::make('123456');

		if ($request->hasFile('foto')) {
			$foto = $email . "." . $request->file('foto')->getClientOriginalExtension();
		}

		try {
			$data = [
				'nama' => $nama,
				'jabatan' => $jabatan,
				'email' => $email,
				'password' => $password,
				'foto' => $foto,
			];
			$simpan = User::insert($data);
			if ($simpan) {
				if ($request->hasFile('foto')) {
					$folderPath = "public/uploads/karyawan/";
					$request->file('foto')->storeAs($folderPath, $foto);
				}
				return Redirect::back()->with(['success' => 'Data berhasil ditambah.']);
			}
		} catch (\Exception $e) {
			if ($e->getCode() == 23000) {
				$message = "Data dengan Email " . $email . " sudah ada.";
			}
			return Redirect::back()->with(['warning' => 'Data gagal ditambah. ' . $message]);
		}
	}

	public function edit(Request $request)
	{
		$email = $request->email;
		$jabatan = Jabatan::all();
		$user = User::where('email', $email)->first();
		return view('user.edit', compact('user', 'jabatan'));
	}

	public function update($email, Request $request)
	{
		$nama = $request->nama;
		$jabatan = $request->jabatan;
		$email = $request->email;
		$password = $request->password;
		$old_foto = $request->old_foto;

		if ($request->hasFile('foto')) {
			$foto = $email . "." . $request->file('foto')->getClientOriginalExtension();
		} else {
			$foto = $old_foto;
		}

		try {
			$data = [
				'nama' => $nama,
				'jabatan' => $jabatan,
				'email' => $email,
				'foto' => $foto,
			];

			// Hash the password if it's not empty
			if (!empty($password)) {
				$data['password'] = Hash::make($password);
			}

			$update = User::where('email', $email)->update($data);
			if ($update) {
				if ($request->hasFile('foto')) {
					$folderPath = "public/uploads/karyawan/";
					$folderPathOld = "public/uploads/karyawan/" . $old_foto;
					Storage::delete($folderPathOld);
					$request->file('foto')->storeAs($folderPath, $foto);
				}
				return Redirect::back()->with(['success' => 'Data berhasil diupdate.']);
			}
		} catch (\Exception $e) {
			return redirect::back()->with(['warning', 'Data gagal diupdate.']);
		}
	}

	public function delete($email)
	{
		$delete = User::where('email', $email)->delete();
		if ($delete) {
			return Redirect::back()->with(['success' => 'Data berhasil dihapus.']);
		} else {
			return Redirect::back()->with(['error' => 'Data gagal dihapus.']);
		}
	}
}
