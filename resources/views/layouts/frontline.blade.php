<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head', ['title' => $title ?? config('app.name')])
    </head>
    <body class="min-h-screen bg-gradient-to-b from-neutral-950 via-zinc-900 to-black text-zinc-100">
        @include('partials.public-nav')

        {{ $slot }}

        @fluxScripts
    </body>
</html>
