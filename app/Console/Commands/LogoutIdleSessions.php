<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SessionLogin;
use Carbon\Carbon;

class LogoutIdleSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sessions:logout-idle';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Logout semua sesi login yang tidak aktif selama lebih dari 30 menit';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $cutoff = Carbon::now()->subMinutes(30);

        $expiredSessions = SessionLogin::where('is_active', true)
            ->whereNotNull('last_activity_at')
            ->where('last_activity_at', '<', $cutoff)
            ->get();

        foreach ($expiredSessions as $session) {
            $session->update([
                'is_active'     => false,
                'logged_out_at' => now(),
            ]);

            $this->line("✔️  Sesi logout paksa (user_id: {$session->user_id}, session_id: {$session->session_id})");
        }

        $this->info("Total sesi yang di-logout otomatis: " . $expiredSessions->count());
    }
}
