@props(['url'])
<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            <x-app-logo style="max-width: 270px;">
                {!! $slot !!}
        </a>
    </td>
</tr>
