Task: {{ $data->prompt }}

Content subject: {{ $data->field->getSubject() }}.
Write the result in {{ $language }}.
@if ($data->field->isRichText())
Output format: HTML.
@else
Output format: plain text, no HTML and no markdown.
@endif
@if ($data->field->getMaxCharacters() !== null)
Maximum length: {{ $data->field->getMaxCharacters() }} characters.
@endif
@if ($data->context !== [])

Context from the same record:
@foreach ($data->context as $label => $value)
{{ $label }}: {{ $value }}
@endforeach
@endif
@if (filled($data->current_value))

Current value of the field:
{{ $data->current_value }}
@endif
@if (filled($data->notes))

Notes and raw material from the author:
{{ $data->notes }}
@endif
