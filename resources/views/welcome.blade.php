<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Campus Survival Kit') }} — Stop Living on 2AM Instant Noodles</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen">
    <div class="min-h-screen flex flex-col">

        <nav class="bg-white border-b border-ink/15">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-xs px-2 py-1 border border-ink/30 rounded-sm text-ink-muted">CSK</span>
                        <span class="font-sans font-semibold text-ink">Campus Survival Kit</span>
                    </div>

                    <div class="flex items-center gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn-tactical">
                                Mission Control
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn-tactical-outline">
                                Log In
                            </a>
                            <a href="{{ route('register') }}" class="btn-tactical">
                                Join The Resistance
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <main class="flex-1">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">

                <div class="max-w-2xl">
                    <span class="label-tactical"> Student Financial Battle Station</span>
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-semibold text-ink mt-3 leading-tight">
                        Stop asking where your money went.<br>
                        <span class="text-red-600">Start surviving the semester.</span>
                    </h1>
                    <p class="text-ink-light mt-5 text-base sm:text-lg leading-relaxed">
                        Look, we know student life is tough. One minute you're living like royalty on allowance day, and the next you’re calculating if 2-minute noodles count as a balanced meal. 
                        <strong>Campus Survival Kit</strong> helps you track every Rand, ration your funds, and stop your bank account from reaching "critical emergency" status.
                    </p>

                    <div class="flex flex-wrap gap-3 mt-8">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn-tactical">
                                Go to Mission Control
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="btn-tactical">
                                Secure Your Survival Kit (Free)
                            </a>
                            <a href="{{ route('login') }}" class="btn-tactical-outline">
                                Log In
                            </a>
                        @endauth
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-16">

                    <div class="card-academic p-6">
                        <span class="label-tactical"> The Money Log</span>
                        <p class="text-sm text-ink-light mt-2">
                            Log every Rand in and out. Yes, even that 3 AM post-night-out shawarma and the 6th iced coffee of the week. No secrets here.
                        </p>
                    </div>

                    <div class="card-academic p-6">
                        <span class="label-tactical"> Daily Rations</span>
                        <p class="text-sm text-ink-light mt-2">
                            Set weekly limits so you don't spend your whole monthly allowance in Week 1. Real-time status updates: <em>Safe, Code Red, or Flatline.</em>
                        </p>
                    </div>

                    <div class="card-academic p-6">
                        <span class="label-tactical"> Damage Control Reports</span>
                        <p class="text-sm text-ink-light mt-2">
                            Visual breakdowns that tell you the brutal truth: where your money actually vanished and how to survive until next pay/allowance day.
                        </p>
                    </div>

                </div>

            </div>
        </main>

        <footer class="border-t border-ink/10 py-4">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <span class="label-tactical">Campus Survival Kit &middot; Built for students living on the edge</span>
            </div>
        </footer>

    </div>
</body>
</html>