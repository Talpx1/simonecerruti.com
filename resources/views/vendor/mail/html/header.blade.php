@props(['url'])
<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            <img src="{{ 'data:image/svg+xml;base64,' . base64_encode(\Illuminate\Support\Facades\Blade::render('<x-app-logo color="#000" />')) }}"
                style="max-width: 270px;">
            {!! $slot !!}
        </a>
    </td>
</tr>
