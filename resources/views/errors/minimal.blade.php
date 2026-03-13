<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title')</title>

    <style>
        body {
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        }
    </style>
</head>

<body style="-webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; margin: 0; border: 0; padding: 0;">
    <div style="display: grid; place-content: center; min-height: 100dvh; height: 100dvh; max-height: 100dvh; background-color: black; text-align: center; overflow: hidden; padding: 12px;"
        role="main">

        <a href="{{ route('home') }}" wire:navigate style="margin-bottom: 42px;">
            <x-app-logo style="max-width: 100%;" />
        </a>

        <h1 style="color: white; font-size: 128px; font-weight: bolder; margin: 0;">
            @yield('code')
        </h1>

        <div style="color: white; font-size: 42px;">
            @yield('message')
        </div>
    </div>
</body>

</html>
