<?php

namespace Database\Seeders;

use App\Enums\BillingInterval;
use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Notification;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Notification::fake();

        $testUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $alice = User::factory()->create(['name' => 'Alice Johnson', 'email' => 'alice@example.com']);
        $bob = User::factory()->create(['name' => 'Bob Smith', 'email' => 'bob@example.com']);
        $carol = User::factory()->create(['name' => 'Carol White', 'email' => 'carol@example.com']);
        $dave = User::factory()->create(['name' => 'Dave Brown', 'email' => 'dave@example.com']);
        $eve = User::factory()->create(['name' => 'Eve Davis', 'email' => 'eve@example.com']);
        $frank = User::factory()->create(['name' => 'Frank Miller', 'email' => 'frank@example.com']);

        $acme = Team::factory()
            ->proPlan(BillingInterval::Yearly)
            ->create(['name' => 'Acme Inc', 'user_id' => $testUser->id]);
        $acme->members()->attach($alice, ['role' => TeamRole::Administrator]);
        $acme->members()->attach($bob, ['role' => TeamRole::Member]);
        $acme->members()->attach($carol, ['role' => TeamRole::Member]);
        TeamInvitation::factory()->for($acme)->administrator()->create([
            'email' => 'newadmin@example.com',
            'user_id' => $testUser->id,
        ]);
        TeamInvitation::factory()->for($acme)->member()->create([
            'email' => 'newhire@example.com',
            'user_id' => $alice->id,
        ]);

        $sideProject = Team::factory()->create([
            'name' => 'Side Project',
            'user_id' => $testUser->id,
        ]);
        $sideProject->members()->attach($dave, ['role' => TeamRole::Member]);

        $soloStudio = Team::factory()
            ->studioPlan(BillingInterval::Monthly)
            ->create(['name' => 'Solo Studio', 'user_id' => $testUser->id]);
        TeamInvitation::factory()->for($soloStudio)->member()->create([
            'email' => 'pending@example.com',
            'user_id' => $testUser->id,
        ]);

        $studioCo = Team::factory()
            ->studioPlan(BillingInterval::Yearly)
            ->create(['name' => 'Studio Co', 'user_id' => $eve->id]);
        $studioCo->members()->attach($frank, ['role' => TeamRole::Administrator]);
        $studioCo->members()->attach($testUser, ['role' => TeamRole::Member]);
        $studioCo->members()->attach($alice, ['role' => TeamRole::Member]);
        TeamInvitation::factory()->for($studioCo)->member()->create([
            'email' => 'contractor@example.com',
            'user_id' => $eve->id,
        ]);

        $clientWork = Team::factory()
            ->proPlan(BillingInterval::Monthly)
            ->create(['name' => 'Client Work', 'user_id' => $bob->id]);
        $clientWork->members()->attach($testUser, ['role' => TeamRole::Member]);
        $clientWork->members()->attach($carol, ['role' => TeamRole::Member]);

        TeamInvitation::factory()->for($clientWork)->administrator()->create([
            'email' => 'pending-admin@example.com',
            'user_id' => $bob->id,
        ]);

        $testUser->update(['current_team_id' => $acme->id]);
    }
}
