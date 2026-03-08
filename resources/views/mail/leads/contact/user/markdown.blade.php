<x-mail::message>
# {{ __('You contacted :company_name!', ['company_name' => config()->string('company.friendly_name')]) }}

{{ __('Here\'s a summary of your message:') }}

**{{ __('First name') }}**: {{ $lead->first_name }}<br>
**{{ __('Last name') }}**: {{ $lead->last_name }}<br>
**{{ __('Company Name') }}**: {{ $lead->company_name }}<br>
**{{ __('Email') }}**: {{ $lead->email }}<br>
**{{ __('Phone') }}**: {{ $lead->phone ?? '--' }}<br>

**{{ __('Message') }}**:<br>
{{ $lead->message }}

**{{ __('Sent at') }}**:
{{ $lead->created_at->timezone(config()->string('app.actual_timezone'))->format('d/m/Y H:i:s') }}
</x-mail::message>
