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
        $days = (int) $this->option('days');
        $threshold = now()->subDays($days);

        $toPrune = User::query()
            ->whereNotNull('deleted_at')
            ->where('deleted_at', '<=', $threshold)
            ->get();

        $action = new PruneDeletedUser;

        foreach ($toPrune as $user) {
            $action->handle($user);
        }

        $this->info("Pruned {$toPrune->count()} user(s) scheduled before {$threshold->toDateTimeString()}.");

        return self::SUCCESS;
    }
}
