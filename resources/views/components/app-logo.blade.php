@props([
    'weight' => 'medium',
    'color' => '#fff',
])

@if ($weight === 'thin')
    <svg {{ $attributes }} preserveAspectRatio="xMidYMid meet" viewBox="0 0 96 51" fill="none"
        xmlns="http://www.w3.org/2000/svg">
        <path
            d="M1 49.4986C22 49.4986 24.0781 42.3735 34.5 27.9985C49 7.99857 72 -8.50133 95 7.99857C72 7.99857 72 26.4985 72 27.9985C72 29.4985 72 49.4985 95 49.4985"
            stroke="{{ $color }}" stroke-width="2" stroke-linecap="square" stroke-linejoin="bevel" />
    </svg>
@elseif ($weight === 'medium')
    <svg {{ $attributes }} preserveAspectRatio="xMidYMid meet" viewBox="0 0 100 54" fill="none"
        xmlns="http://www.w3.org/2000/svg">
        <path
            d="M3 51.4989C24 51.4989 26.0781 44.3738 36.5 29.9988C51 9.99881 74 -6.50109 97 9.99882C74 9.99882 74 28.4987 74 29.9987C74 31.4987 74 51.4987 97 51.4987"
            stroke="{{ $color }}" stroke-width="5" stroke-linecap="square" stroke-linejoin="bevel" />
    </svg>
@elseif ($weight === 'bold')
    <svg {{ $attributes }} preserveAspectRatio="xMidYMid meet" viewBox="0 0 104 59" fill="none"
        xmlns="http://www.w3.org/2000/svg">
        <path
            d="M5 53.4989C26 53.4989 28.0781 46.3738 38.5 31.9988C53 11.9988 76 -4.50109 99 11.9988C76 11.9988 76 30.4987 76 31.9987C76 33.4987 76 53.4987 99 53.4987"
            stroke="{{ $color }}" stroke-width="10" stroke-linecap="square" stroke-linejoin="bevel" />
    </svg>
@endif
