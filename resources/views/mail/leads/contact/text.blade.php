{{ __('Contact request from :full_name', ['full_name' => $lead->full_name]) }}

{{ __('First name') }}: {{ $lead->first_name }}
{{ __('Last name') }}: {{ $lead->last_name }}
{{ __('Company name') }}: {{ $lead->company_name }}
{{ __('Email') }}: {{ $lead->email }}
{{ __('Phone') }}: {{ $lead->phone ?? '--' }}
{{ __('Message') }}: {{ $lead->message }}
{{ __('Data processing accepted') }}: {{ $lead->acceptance ? __('Yes') : __('No') }}
{{ __('IP address') }}: {{ $lead->ip }}
{{ __('Received at') }}:
{{ $lead->created_at->timezone(config()->string('app.actual_timezone'))->format('d/m/Y H:i:s') }}
