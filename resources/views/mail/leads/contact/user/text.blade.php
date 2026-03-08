{{ __('You contacted :company_name!', ['company_name' => config()->string('company.friendly_name')]) }}
{{ __('Here\'s a summary of your message:') }}
{{ __('First name') }}: {{ $lead->first_name }}
{{ __('Last name') }}: {{ $lead->last_name }}
{{ __('Company Name') }}: {{ $lead->company_name }}
{{ __('Email') }}: {{ $lead->email }}
{{ __('Phone') }}: {{ $lead->phone ?? '--' }}
{{ __('Message') }}: {{ $lead->message }}
{{ __('Sent at') }}:
{{ $lead->created_at->timezone(config()->string('app.actual_timezone'))->format('d/m/Y H:i:s') }}
