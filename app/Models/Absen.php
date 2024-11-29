<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Absen extends Model
{
	use HasFactory;

	protected $fillable = [
		'user_id',
		'email',
		'nama',
		'status',
		'keterangan',
		'tanggal',
		'tanggal_keluar',
		'jam_masuk',
		'jam_keluar',
		'foto_masuk',
		'foto_keluar',
		'lokasi_masuk',
		'lokasi_keluar',
		'laporan_masuk',
		'laporan_keluar',
	];

	public function getData()
	{
		$absen = User::all();
	}
}
