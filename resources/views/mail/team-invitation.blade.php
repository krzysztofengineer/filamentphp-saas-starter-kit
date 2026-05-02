<x-mail::message>
# You've been invited to join {{ $invitation->team->name }}

{{ $invitation->user->name }} has invited you to join the **{{ $invitation->team->name }}** team.

<x-mail::button :url="route('team-invitations.show')">
View invitation
</x-mail::button>

If you did not expect this invitation, you can safely ignore this email.

Thanks,<br>
the SAAS team
</x-mail::message>
