<nav class="bg-white border-b border-ink/15">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">

            <div class="flex items-center gap-8">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <span class="font-mono text-xs px-2 py-1 border border-ink/30 rounded-sm text-ink-muted">CSK</span>
                    <span class="font-sans font-semibold text-ink hidden sm:inline">Campus Survival Kit</span>
                </a>

                <div class="hidden sm:flex items-center gap-1">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Mission Control
                    </x-nav-link>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <span class="label-tactical hidden sm:inline">{{ auth()->user()->name }}</span>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-tactical-outline">
                        Log Out
                    </button>
                </form>
            </div>

        </div>
    </div>
</nav>