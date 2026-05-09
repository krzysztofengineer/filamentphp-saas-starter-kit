<?php

namespace App\Console\Commands;

use App\Actions\Accounts\PruneDeletedUser;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('users:prune {--days= : Override the configured grace period}')]
#[Description('Permanently delete users scheduled for deletion past the grace period')]
class PruneDeletedUsers extends Command
{
    public function handle(): int
    {
        $days = (int) ($this->option('days') ?? config('account.deletion_grace_days'));
        $threshold = now()->subDays($days);

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
