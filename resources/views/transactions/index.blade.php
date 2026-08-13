<x-app-layout>
    <x-slot name="eyebrow">Money Log</x-slot>
    <x-slot name="header">Transactions</x-slot>

    <div class="flex justify-between items-center mb-6">
        <span class="label-tactical">{{ $transactions->total() }} record(s)</span>
        <a href="{{ route('transactions.create') }}" class="btn-tactical">
            Log Transaction
        </a>
    </div>

    <div class="card-academic p-4 mb-6">
        <form method="GET" action="{{ route('transactions.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
            <div class="lg:col-span-2">
                <label class="label-tactical block mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Description or note..."
                    class="w-full rounded-sm border-ink/20 text-sm focus:border-ration-green focus:ring-ration-green">
            </div>

            <div>
                <label class="label-tactical block mb-1">Type</label>
                <select name="type" class="w-full rounded-sm border-ink/20 text-sm focus:border-ration-green focus:ring-ration-green">
                    <option value="">All</option>
                    <option value="income" @selected(request('type') === 'income')>Income</option>
                    <option value="expense" @selected(request('type') === 'expense')>Expense</option>
                </select>
            </div>

            <div>
                <label class="label-tactical block mb-1">Category</label>
                <select name="category_id" class="w-full rounded-sm border-ink/20 text-sm focus:border-ration-green focus:ring-ration-green">
                    <option value="">All</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="label-tactical block mb-1">From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                    class="w-full rounded-sm border-ink/20 text-sm focus:border-ration-green focus:ring-ration-green">
            </div>

            <div>
                <label class="label-tactical block mb-1">To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                    class="w-full rounded-sm border-ink/20 text-sm focus:border-ration-green focus:ring-ration-green">
            </div>

            <div class="lg:col-span-6 flex flex-wrap items-end gap-3">
                <div>
                    <label class="label-tactical block mb-1">Sort</label>
                    <select name="sort" class="rounded-sm border-ink/20 text-sm focus:border-ration-green focus:ring-ration-green">
                        <option value="newest" @selected(request('sort', 'newest') === 'newest')>Newest</option>
                        <option value="oldest" @selected(request('sort') === 'oldest')>Oldest</option>
                        <option value="highest" @selected(request('sort') === 'highest')>Highest Amount</option>
                        <option value="lowest" @selected(request('sort') === 'lowest')>Lowest Amount</option>
                    </select>
                </div>

                <button type="submit" class="btn-tactical-outline">Apply Filters</button>

                @if (request()->anyFilled(['search', 'type', 'category_id', 'date_from', 'date_to', 'sort']))
                    <a href="{{ route('transactions.index') }}" class="label-tactical hover:text-ration-red">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="card-academic overflow-hidden">
        @if ($transactions->isEmpty())
            <x-empty-state
                title="No transactions match these filters yet."
                message="Try adjusting your filters, or log your first transaction."
                actionLabel="Log Transaction"
                :actionUrl="route('transactions.create')" />
        @else
            {{-- Desktop table: hidden below md breakpoint --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-ink/10 bg-paper-dark/40">
                            <th class="text-left px-4 py-3 label-tactical">Date</th>
                            <th class="text-left px-4 py-3 label-tactical">Description</th>
                            <th class="text-left px-4 py-3 label-tactical">Category</th>
                            <th class="text-right px-4 py-3 label-tactical">Amount</th>
                            <th class="text-right px-4 py-3 label-tactical">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $transaction)
                            <tr class="border-b border-ink/5 hover:bg-paper-dark/20">
                                <td class="px-4 py-3 whitespace-nowrap text-ink-light">
                                    {{ $transaction->transaction_date->format('d M Y') }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-ink">{{ $transaction->description }}</div>
                                    @if ($transaction->note)
                                        <div class="text-xs text-ink-muted mt-0.5">{{ $transaction->note }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-ink-light">
                                    {{ $transaction->category->name }}
                                </td>
                                <td class="px-4 py-3 text-right font-mono
                                    {{ $transaction->type === 'income' ? 'text-ration-green' : 'text-ration-red' }}">
                                    {{ $transaction->type === 'income' ? '+' : '-' }}R {{ number_format($transaction->amount, 2) }}
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <a href="{{ route('transactions.edit', $transaction) }}"
                                        class="text-xs font-mono uppercase text-ration-blue hover:underline">Edit</a>

                                    <form action="{{ route('transactions.destroy', $transaction) }}" method="POST"
                                        class="inline"
                                        onsubmit="return confirm('Delete this transaction? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-xs font-mono uppercase text-ration-red hover:underline ml-3">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile cards: shown only below md breakpoint --}}
            <div class="md:hidden divide-y divide-ink/5">
                @foreach ($transactions as $transaction)
                    <div class="p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="text-sm text-ink font-medium">{{ $transaction->description }}</div>
                                <div class="text-xs text-ink-muted mt-0.5">
                                    {{ $transaction->category->name }} &middot; {{ $transaction->transaction_date->format('d M Y') }}
                                </div>
                            </div>
                            <div class="font-mono text-sm whitespace-nowrap
                                {{ $transaction->type === 'income' ? 'text-ration-green' : 'text-ration-red' }}">
                                {{ $transaction->type === 'income' ? '+' : '-' }}R {{ number_format($transaction->amount, 2) }}
                            </div>
                        </div>

                        @if ($transaction->note)
                            <div class="text-xs text-ink-muted mt-2">{{ $transaction->note }}</div>
                        @endif

                        <div class="flex gap-4 mt-3">
                            <a href="{{ route('transactions.edit', $transaction) }}"
                                class="text-xs font-mono uppercase text-ration-blue hover:underline">Edit</a>

                            <form action="{{ route('transactions.destroy', $transaction) }}" method="POST"
                                onsubmit="return confirm('Delete this transaction? This cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-mono uppercase text-ration-red hover:underline">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="px-4 py-3 border-t border-ink/10">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</x-app-layout>