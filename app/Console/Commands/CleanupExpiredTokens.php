<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Sanctum\PersonalAccessToken;

class CleanupExpiredTokens extends Command
{
    protected $signature = 'auth:cleanup-tokens';
    protected $description = 'Clean up expired authentication tokens';

    public function handle()
    {
        $this->info('Cleaning up expired tokens...');

        // Delete tokens older than 30 days
        $expiredTokens = PersonalAccessToken::where('created_at', '<', now()->subDays(30))->count();
        PersonalAccessToken::where('created_at', '<', now()->subDays(30))->delete();

        $this->info("Deleted {$expiredTokens} expired tokens.");

        return 0;
    }
}
