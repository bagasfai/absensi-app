<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JabatanSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		DB::table('jabatan')->insert([
			[
				'id_jabatan' => '1',
				'jabatan' => 'SUPERADMIN',
			],
			[
				'id_jabatan' => '2',
				'jabatan' => 'OFFICE',
			],
			[
				'id_jabatan' => '3',
				'jabatan' => 'MANAJER AREA',
			],
			[
				'id_jabatan' => '4',
				'jabatan' => 'DIREKSI',
			],
			[
				'id_jabatan' => '5',
				'jabatan' => 'KORLAP',
			]
		]);
	}
}
