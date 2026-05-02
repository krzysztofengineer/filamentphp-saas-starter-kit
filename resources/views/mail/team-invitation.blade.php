<x-mail::message>
# You've been invited to join {{ $invitation->team->name }}

{{ $invitation->user->name }} has invited you to join the **{{ $invitation->team->name }}** team.

If you already have an account, sign in to accept the invitation.

<x-mail::button :url="route('filament.app.auth.login')">
Sign in
</x-mail::button>

Don't have an account yet? Create one to accept the invitation.

<x-mail::button :url="route('filament.app.auth.register')" color="success">
Create account
</x-mail::button>

If you did not expect this invitation, you can safely ignore this email.

Thanks,<br>
the SAAS team
</x-mail::message>
