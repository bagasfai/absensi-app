<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\TelegramController;
use App\Models\Absen;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AbsenReminders extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'absen:reminders';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Send reminders for absen';

	/**
	 * Execute the console command.
	 */

	public function handle()
	{

		// Get current date and times
		$today = Carbon::now()->toDateString();
		$currentTime = Carbon::now();
		$todayCarbon = Carbon::now();

		// Skip sending reminders on Sundays
		if ($todayCarbon->dayOfWeek === Carbon::SUNDAY) {
			$this->info('Today is Sunday. Skipping reminders.');
			return;
		}

		// Retrieve all users in the system
		$allUsers = User::pluck('email');

		// Retrieve users who have done attendance on the current day
		$usersWithAttendance = Absen::where('tanggal', $today)
			->whereNotNull('jam_masuk')
			->whereNotNull('jam_keluar')
			->pluck('email');

		// Identify users who haven't done attendance
		$missingAttendanceUsers = $allUsers->diff($usersWithAttendance);

		// Customize the reminder messages based on your requirements
		$clockInReminderMessage = function ($email) {
			return "Reminder: Anda belum melakukan absen masuk. harap segera melakukan absen masuk.";
		};
		$clockOutReminderMessage = function ($email) {
			return "Reminder: Anda belum melakukan absen keluar. harap segera melakukan absen keluar.";
		};

		// Retrieve all users in the system with their id_telegram
		$usersWithTelegram = User::whereNotNull('id_telegram')->get(['email', 'id_telegram']);

		// Send reminders for clock in to users who missed attendance
		foreach ($missingAttendanceUsers as $email) {
			// Find the user's id_telegram
			$userTelegram = $usersWithTelegram->firstWhere('email', $email);

			if ($userTelegram) {
				$telegramController = app(TelegramController::class);

				// Check if the user has clocked in but not clocked out
				$userHasClockedInButNotOut = Absen::where('email', $email)
					->where('tanggal', $today)
					->whereNotNull('jam_masuk')
					->whereNull('jam_keluar')
					->exists();

				// Send clock in reminder at 9 AM
				if ($currentTime->hour == 9) {
					$telegramController->sendReminderMessage($userTelegram->id_telegram, $clockInReminderMessage($email));
				}

				// Send clock out reminder at 5 PM only if the user has clocked in but not out
				if ($currentTime->hour == 17 && $userHasClockedInButNotOut) {
					$telegramController->sendReminderMessage($userTelegram->id_telegram, $clockOutReminderMessage($email));
				}
			}

			$this->info('Reminders sent successfully.');
		}
	}
}
