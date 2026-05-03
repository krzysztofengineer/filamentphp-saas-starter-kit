<?php

namespace App\Console\Commands;

use App\Actions\PruneDeletedUser;
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
        $threshold = now()->subDays((int) $this->option('days'));

        $toPrune = User::query()
            ->whereNotNull('deleted_at')
            ->where('deleted_at', '<=', $threshold)
            ->get();

        foreach ($toPrune as $user) {
            (new PruneDeletedUser)->handle($user);
        }

        $this->info("Pruned {$toPrune->count()} user(s) scheduled before {$threshold->toDateTimeString()}.");

        return self::SUCCESS;
    }
}
