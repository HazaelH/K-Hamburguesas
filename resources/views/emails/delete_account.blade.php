<x-mail::message>
# {{ __('profile/messages.email_delete_greeting') }}

{{ __('profile/messages.email_delete_line1') }}

<x-mail::button :url="$url" color="error">
{{ __('profile/messages.email_delete_action') }}
</x-mail::button>

{{ __('profile/messages.email_delete_line2') }}

{{ __('profile/messages.email_thanks') }}<br>
{{ __('profile/messages.email_team', ['app' => config('app.name')]) }}
</x-mail::message>