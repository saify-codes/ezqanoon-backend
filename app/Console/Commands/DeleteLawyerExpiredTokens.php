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
    protected $signature = 'app:delete-expired-tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete lawyers & firms verification and reset tokens';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $deleted = DB::table('email_verification_tokens')
            ->where('created_at', '<=', Carbon::now()->subMinutes(15))
            ->delete();
            
        $this->info("Deleted {$deleted} expired verification tokens.");
        
        $deleted = DB::table('password_reset_tokens')
            ->where('created_at', '<=', Carbon::now()->subMinutes(60))
            ->delete();

        $this->info("Deleted {$deleted} expired reset tokens.");
    }
}
