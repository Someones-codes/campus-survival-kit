<x-app-layout>
    <x-slot name="eyebrow">Mission Control</x-slot>
    <x-slot name="header">Dashboard</x-slot>

    <div class="flex justify-end mb-4">
        <div class="inline-flex rounded-sm border border-ink/20 overflow-hidden">
            @foreach (['day' => 'Day', 'week' => 'Week', 'month' => 'Month'] as $value => $label)
                <a href="{{ route('dashboard', ['period' => $value]) }}"
                    class="px-4 py-2 text-xs font-mono uppercase tracking-widest
                        {{ $period === $value ? 'bg-ration-green text-paper' : 'bg-white text-ink-light hover:bg-paper-dark/40' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">

        <div class="card-academic p-6 lg:col-span-2">
            <span class="label-tactical">
                Spent this {{ $period }} &middot; {{ $currentStart->format('d M') }} &ndash; {{ $currentEnd->format('d M Y') }}
            </span>
            <div class="text-4xl font-semibold text-ration-red mt-2 font-mono">
                R {{ number_format($expense, 2) }}
            </div>

            @if (! is_null($spendingChange))
                <div class="mt-2 text-sm">
                    <span class="{{ $spendingChange > 0 ? 'text-ration-red' : 'text-ration-green' }}">
                        {{ $spendingChange > 0 ? '+' : '' }}{{ number_format($spendingChange, 1) }}%
                    </span>
                    <span class="text-ink-muted"> vs previous {{ $period }} (R {{ number_format($previousExpense, 2) }})</span>
                </div>
            @else
                <div class="mt-2 text-sm text-ink-muted">No spending in the previous {{ $period }} to compare against.</div>
            @endif
        </div>

        <div class="card-academic p-6">
            <span class="label-tactical">Remaining</span>
            <div class="text-3xl font-semibold mt-2 font-mono {{ $remaining >= 0 ? 'text-ration-green' : 'text-ration-red' }}">
                R {{ number_format($remaining, 2) }}
            </div>
            <div class="mt-3 text-xs text-ink-muted space-y-1">
                <div class="flex justify-between">
                    <span>Income</span>
                    <span class="font-mono text-ration-green">R {{ number_format($income, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Expenses</span>
                    <span class="font-mono text-ration-red">R {{ number_format($expense, 2) }}</span>
                </div>
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        <div class="card-academic p-6 lg:col-span-2">
            <div class="flex justify-between items-center mb-4">
                <span class="label-tactical">Recent Entries</span>
                <a href="{{ route('transactions.index') }}" class="text-xs font-mono uppercase text-ration-blue hover:underline">
                    View Full Log
                </a>
            </div>

            @if ($recentTransactions->isEmpty())
                <p class="text-sm text-ink-muted">No transactions logged yet.</p>
                <a href="{{ route('transactions.create') }}" class="btn-tactical inline-flex mt-4">
                    Log Your First Transaction
                </a>
            @else
                <div class="divide-y divide-ink/5">
                    @foreach ($recentTransactions as $transaction)
                        <div class="flex justify-between items-center py-3">
                            <div>
                                <div class="text-sm text-ink">{{ $transaction->description }}</div>
                                <div class="text-xs text-ink-muted">
                                    {{ $transaction->category->name }} &middot; {{ $transaction->transaction_date->format('d M Y') }}
                                </div>
                            </div>
                            <div class="font-mono text-sm {{ $transaction->type === 'income' ? 'text-ration-green' : 'text-ration-red' }}">
                                {{ $transaction->type === 'income' ? '+' : '-' }}R {{ number_format($transaction->amount, 2) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="card-academic p-6">
            <span class="label-tactical block mb-4">Spending Breakdown &middot; This {{ ucfirst($period) }}</span>

            @if ($categoryBreakdown->isEmpty())
                <p class="text-sm text-ink-muted">No expenses recorded for this period.</p>
            @else
                <div class="space-y-3">
                    @foreach ($categoryBreakdown as $categoryName => $total)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-ink">{{ $categoryName }}</span>
                                <span class="font-mono text-ink-light">R {{ number_format($total, 2) }}</span>
                            </div>
                            <div class="h-1.5 bg-paper-dark rounded-sm overflow-hidden">
                                <div class="h-full bg-ration-olive"
                                    style="width: {{ $expense > 0 ? min(100, ($total / $expense) * 100) : 0 }}%">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</x-app-layout>