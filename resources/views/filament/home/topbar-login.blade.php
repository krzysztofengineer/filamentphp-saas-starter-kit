@php
    $loginUrl = \Filament\Facades\Filament::getPanel('app')->getLoginUrl();
    $registerUrl = \Filament\Facades\Filament::getPanel('app')->getRegistrationUrl();
    $appUrl = \Filament\Facades\Filament::getPanel('app')->getUrl();
@endphp

<div
    x-data="{
        theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
        toggle() {
            this.theme = this.theme === 'dark' ? 'light' : 'dark';
            document.documentElement.classList.toggle('dark', this.theme === 'dark');
            localStorage.setItem('theme', this.theme);
        },
    }"
    class="flex items-center gap-2"
>
    <x-filament::icon-button
        icon="heroicon-o-moon"
        color="gray"
        label="Switch to light mode"
        x-show="theme === 'dark'"
        x-cloak
        @click="toggle()"
    />
    <x-filament::icon-button
        icon="heroicon-o-sun"
        color="gray"
        label="Switch to dark mode"
        x-show="theme === 'light'"
        x-cloak
        @click="toggle()"
    />

    @auth
        <x-filament::button tag="a" :href="$appUrl" color="gray" outlined>
            Dashboard
        </x-filament::button>
    @else
        <x-filament::button tag="a" :href="$loginUrl" color="gray" outlined data-testid="topbar-login">
            Log in
        </x-filament::button>
        <x-filament::button tag="a" :href="$registerUrl" color="primary" data-testid="topbar-register">
            Get started for free
        </x-filament::button>
    @endauth
</div>
