@auth
@else
<div class="flex items-center gap-4">
    <x-filament::button
        tag="a"
        :href="route('filament.app.auth.register')"
        color="secondary"
        data-testid="topbar-register"
    >
        Sign up
    </x-filament::button>
    <x-filament::button
        tag="a"
        :href="route('filament.app.auth.login')"
        data-testid="topbar-login"
    >
        Log in
    </x-filament::button>
</div>
@endauth
