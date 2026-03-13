@props(['url'])
<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            <img src="{{ asset('images/logo/logo_strokes_dark.png') }}" style="max-width: 270px;">
            {!! $slot !!}
        </a>
    </td>
</tr>
