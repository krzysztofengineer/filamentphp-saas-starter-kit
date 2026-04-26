<x-filament-panels::page>
    @php
        $tenant = \Filament\Facades\Filament::getTenant();
        $user = auth()->user();
    @endphp

    <div class="space-y-6" data-testid="dashboard">
        <div class="rounded-2xl border border-gray-200 bg-gradient-to-br from-blue-50 to-white p-6 dark:border-gray-800 dark:from-blue-950/30 dark:to-gray-900">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white">
                        Welcome to {{ $tenant?->name ?? 'your team' }}
                    </h2>
                    <p class="mt-1 text-sm leading-relaxed text-gray-600 dark:text-gray-400">
                        Hi {{ $user->name }} — switch teams from the topbar, manage your account or upgrade your plan.
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <x-filament::button
                        tag="a"
                        :href="\App\Filament\Clusters\TeamSettings\Pages\TeamMembers::getUrl()"
                        color="primary"
                        icon="heroicon-o-user-plus"
                    >
                        Invite a member
                    </x-filament::button>
                    <x-filament::button
                        tag="a"
                        :href="\App\Filament\Clusters\AccountSettings\Pages\AccountBilling::getUrl()"
                        color="gray"
                        outlined
                        icon="heroicon-o-credit-card"
                    >
                        Subscription
                    </x-filament::button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            @foreach ([
                ['icon' => 'heroicon-o-users', 'title' => 'Members', 'desc' => 'Manage who can access this team.', 'href' => \App\Filament\Clusters\TeamSettings\Pages\TeamMembers::getUrl()],
                ['icon' => 'heroicon-o-cog-6-tooth', 'title' => 'Team settings', 'desc' => 'Update the team name and details.', 'href' => \App\Filament\Clusters\TeamSettings\Pages\TeamProfile::getUrl()],
                ['icon' => 'heroicon-o-user-circle', 'title' => 'Account', 'desc' => 'Profile, password and billing.', 'href' => \App\Filament\Clusters\AccountSettings\Pages\AccountSettings::getUrl()],
            ] as $card)
                <a href="{{ $card['href'] }}" class="group rounded-2xl border border-gray-200 bg-white p-6 transition hover:border-blue-300 hover:shadow-sm dark:border-gray-800 dark:bg-gray-900 dark:hover:border-blue-700">
                    <div class="mb-3 inline-flex size-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600 transition group-hover:bg-blue-100 dark:bg-blue-500/15 dark:text-blue-400">
                        @svg($card['icon'], 'size-5')
                    </div>
                    <h3 class="font-semibold text-gray-950 dark:text-white">{{ $card['title'] }}</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $card['desc'] }}</p>
                </a>
            @endforeach
        </div>

        @if (auth()->user()?->isScheduledForDeletion())
            @livewire(\App\Filament\Widgets\PendingDeletionBanner::class)
        @endif

        @livewire(\App\Filament\Widgets\PendingInvitationsTable::class)
    </div>
</x-filament-panels::page>
