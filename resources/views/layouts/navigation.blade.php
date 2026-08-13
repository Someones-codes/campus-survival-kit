<nav class="bg-white border-b border-ink/15" x-data="{ mobileOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">

            <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                <span class="font-mono text-xs px-2 py-1 border border-ink/30 rounded-sm text-ink-muted">CSK</span>
                <span class="font-sans font-semibold text-ink hidden sm:inline">Campus Survival Kit</span>
            </a>

            <div class="hidden sm:flex items-center gap-2">
                <a href="{{ route('transactions.create', ['type' => 'income']) }}" class="btn-tactical-outline">
                    Add Income
                </a>
                <a href="{{ route('transactions.create') }}" class="btn-tactical">
                    Add Expense
                </a>

                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open" type="button"
                        class="font-mono text-xs uppercase tracking-widest px-3 py-2 rounded-sm text-ink-light hover:bg-ink/5 flex items-center gap-1
                            {{ request()->routeIs('budgets.*') || request()->routeIs('transactions.index') || request()->routeIs('categories.*') ? 'bg-ink/5' : '' }}">
                        More
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" x-cloak
                        class="absolute right-0 mt-2 w-48 card-academic py-1 z-20">
                        <a href="{{ route('budgets.index') }}"
                            class="block px-4 py-2 text-sm text-ink hover:bg-paper-dark/40 {{ request()->routeIs('budgets.*') ? 'font-semibold' : '' }}">
                            Rationing
                        </a>
                        <a href="{{ route('transactions.index') }}"
                            class="block px-4 py-2 text-sm text-ink hover:bg-paper-dark/40 {{ request()->routeIs('transactions.index') ? 'font-semibold' : '' }}">
                            Money Log
                        </a>
                        <a href="{{ route('categories.index') }}"
                            class="block px-4 py-2 text-sm text-ink hover:bg-paper-dark/40 {{ request()->routeIs('categories.*') ? 'font-semibold' : '' }}">
                            Spending Units
                        </a>
                    </div>
                </div>

                <span class="label-tactical hidden lg:inline ml-1">{{ auth()->user()->name }}</span>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-tactical-outline">
                        Log Out
                    </button>
                </form>
            </div>

            <button @click="mobileOpen = !mobileOpen" type="button" class="sm:hidden p-2 text-ink">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    <path x-show="mobileOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

        </div>

        <div x-show="mobileOpen" x-cloak class="sm:hidden pb-4 space-y-1">
            <div class="flex gap-2 pb-2">
                <a href="{{ route('transactions.create', ['type' => 'income']) }}" class="flex-1 btn-tactical-outline text-center">Add Income</a>
                <a href="{{ route('transactions.create') }}" class="flex-1 btn-tactical text-center">Add Expense</a>
            </div>

            <a href="{{ route('budgets.index') }}" class="block px-3 py-2 rounded-sm text-sm {{ request()->routeIs('budgets.*') ? 'bg-ration-green text-paper' : 'text-ink hover:bg-ink/5' }}">Rationing</a>
            <a href="{{ route('transactions.index') }}" class="block px-3 py-2 rounded-sm text-sm {{ request()->routeIs('transactions.index') ? 'bg-ration-green text-paper' : 'text-ink hover:bg-ink/5' }}">Money Log</a>
            <a href="{{ route('categories.index') }}" class="block px-3 py-2 rounded-sm text-sm {{ request()->routeIs('categories.*') ? 'bg-ration-green text-paper' : 'text-ink hover:bg-ink/5' }}">Spending Units</a>

            <form method="POST" action="{{ route('logout') }}" class="pt-2">
                @csrf
                <button type="submit" class="w-full btn-tactical-outline">Log Out</button>
            </form>
        </div>
    </div>
</nav>