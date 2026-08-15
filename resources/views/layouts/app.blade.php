<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Campus Survival Kit') }} - {{ $header ?? 'Dashboard' }}</title>

   <link rel="stylesheet" href="{{ asset('build/assets/app-D8oP8UHe.css') }}">
    <script type="module" src="{{ asset('build/assets/app-3J7N80GV.js') }}"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen">
    <div class="min-h-screen flex flex-col">

        @include('layouts.navigation')

        @isset($header)
            <header class="bg-white border-b border-ink/10">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                    <span class="label-tactical block mb-1">{{ $eyebrow ?? '' }}</span>
                    <h1 class="text-xl font-semibold text-ink">{{ $header }}</h1>
                </div>
            </header>
        @endisset

        <main class="flex-1">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <x-flash-messages />
                {{ $slot }}
            </div>
        </main>

        <footer class="border-t border-ink/10 py-4">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <span class="label-tactical">Campus Survival Kit &middot; Student Finance Tracker</span>
            </div>
        </footer>

    </div>
</body>
</html>