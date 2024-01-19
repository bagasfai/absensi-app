<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Absen extends Model
{
	use HasFactory;

	protected $fillable = [
		'nama',
		'email',
		'status',
		'keterangan',
		'posisi_absen',
		'absen_masuk',
		'absen_keluar',
	];

	public function getData()
	{
		$absen = User::all();
	}
}
