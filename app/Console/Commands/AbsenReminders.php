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

		// Skip sending reminders on Sundays
		if (Carbon::now()->isSunday()) {
			$this->info('Today is Sunday. Skipping reminders.');
			return;
		}

		// Retrieve all users in the system
		$allUsers = User::pluck('email');

		// Retrieve users who have done attendance on the current day
		$usersWithAttendance = $this->getUsersWithAttendance($today);

		// Identify users who haven't done attendance
		$missingAttendanceUsers = $allUsers->diff($usersWithAttendance);

		// Retrieve all users with their id_telegram
		$usersWithTelegram = User::whereNotNull('id_telegram')->get(['email', 'id_telegram']);

		// Send reminders
		foreach ($missingAttendanceUsers as $email) {
			$this->sendReminders($email, $usersWithTelegram, $today, $currentTime);
		}

		$this->info('Reminders sent successfully.');
	}

	/**
	 * Get users with attendance for the current day.
	 *
	 * @param string $today
	 * @return \Illuminate\Support\Collection
	 */
	protected function getUsersWithAttendance(string $today)
	{
		return Absen::where('tanggal', $today)
			->whereNotNull('jam_masuk')
			->whereNotNull('jam_keluar')
			->pluck('email');
	}

	/**
	 * Send reminders to users who missed attendance.
	 *
	 * @param string $email
	 * @param \Illuminate\Support\Collection $usersWithTelegram
	 * @param string $today
	 * @param \Carbon\Carbon $currentTime
	 */
	protected function sendReminders(string $email, $usersWithTelegram, string $today, Carbon $currentTime)
	{
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
				$telegramController->sendReminderMessage($userTelegram->id_telegram, $this->clockInReminderMessage());
			}

			// Send clock out reminder at 5 PM only if the user has clocked in but not out
			if ($currentTime->hour == 11 && $userHasClockedInButNotOut) {
				$telegramController->sendReminderMessage($userTelegram->id_telegram, $this->clockOutReminderMessage());
			}
		}
	}

	/**
	 * Get clock in reminder message.
	 *
	 * @return string
	 */
	protected function clockInReminderMessage()
	{
		return "Reminder: Anda belum melakukan absen masuk. Harap segera melakukan absen masuk.";
	}

	/**
	 * Get clock out reminder message.
	 *
	 * @return string
	 */
	protected function clockOutReminderMessage()
	{
		return "Reminder: Anda belum melakukan absen keluar. Harap segera melakukan absen keluar.";
	}
}
