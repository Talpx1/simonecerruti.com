<x-mail::message>
# {{ __('Contact request from :full_name', ['full_name' => $lead->full_name]) }}

**{{ __('First name') }}**: {{ $lead->first_name }}<br>
**{{ __('Last name') }}**: {{ $lead->last_name }}<br>
**{{ __('Company name') }}**: {{ $lead->company_name }}<br>
**{{ __('Email') }}**: {{ $lead->email }}<br>
**{{ __('Phone') }}**: {{ $lead->phone ?? '--' }}<br>

**{{ __('Message') }}**:<br>
{{ $lead->message }}

**{{ __('Data processing accepted') }}**: {{ $lead->acceptance ? __('Yes') : __('No') }}<br>
**{{ __('IP address') }}**: {{ $lead->ip }}<br>
**{{ __('Received at') }}**: {{ $lead->created_at->timezone(config()->string('app.actual_timezone'))->format('d/m/Y H:i:s') }}<br>
</x-mail::message>
