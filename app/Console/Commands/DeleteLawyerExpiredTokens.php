<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DeleteLawyerExpiredTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-lawyer-expired-tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes lawyer verification tokens that are older than 30 minutes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $threshold = Carbon::now()->subMinutes(30);

        $deleted = DB::table('lawyer_verification_tokens')
            ->where('created_at', '<=', $threshold)
            ->delete();
            
        $this->info("Deleted {$deleted} expired verification tokens.");
        
        $deleted = DB::table('lawyer_password_reset_tokens')
            ->where('created_at', '<=', $threshold)
            ->delete();

        $this->info("Deleted {$deleted} expired reset tokens.");
    }
}
