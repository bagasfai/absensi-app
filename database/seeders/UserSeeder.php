<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */

	public function run(): void
	{
		for ($i = 1; $i <= 10; $i++) {
			DB::table('users')->insert([
				'nama' => 'Super Admin ' . $i,
				'email' => 'superadmin' . $i . '@gmail.com',
				'password' => Hash::make('password'),
				'jabatan' => 'SUPERADMIN',
			]);
		}
	}
}
