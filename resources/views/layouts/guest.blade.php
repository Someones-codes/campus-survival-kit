<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Campus Survival Kit') }}</title>

        <link rel="icon" type="image/png" href="{{ asset('icon.png') }}">

        @php
            $cssFile = glob(public_path('build/assets/app-*.css'))[0] ?? null;
        @endphp

        <style>
            {!! $cssFile ? file_get_contents($cssFile) : '/* CSS file not found */' !!}
        </style>
    </head>
    <body class="min-h-screen">
        <div class="min-h-screen flex flex-col items-center justify-center px-4 py-10">

            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 mb-6">
                <span class="font-mono text-xs px-2 py-1 border border-ink/30 rounded-sm text-ink-muted">CSK</span>
                <span class="font-sans font-semibold text-ink">Campus Survival Kit</span>
            </a>

            <div class="w-full sm:max-w-md card-academic p-6 sm:p-8">
                {{ $slot }}
            </div>

            <p class="label-tactical mt-6 text-center">
                Student Finance Tracker
            </p>

        </div>
    </body>
</html>