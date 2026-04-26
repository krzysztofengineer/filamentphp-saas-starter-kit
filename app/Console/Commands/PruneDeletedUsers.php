<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('users:prune {--days=30 : Number of days after scheduling before pruning}')]
#[Description('Permanently delete users scheduled for deletion over N days ago')]
class PruneDeletedUsers extends Command
{
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $threshold = now()->subDays($days);

        $toPrune = User::query()
            ->whereNotNull('scheduled_for_deletion_at')
            ->where('scheduled_for_deletion_at', '<=', $threshold)
            ->get();

        foreach ($toPrune as $user) {
            $user->teams()->detach();
            $user->delete();
        }

        $this->info("Pruned {$toPrune->count()} user(s) scheduled before {$threshold->toDateTimeString()}.");

        return self::SUCCESS;
    }
}
