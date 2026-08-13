<x-app-layout>
    <x-slot name="eyebrow">Tactical Command Center</x-slot>
    <x-slot name="header">Mission Control</x-slot>

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

        <!-- Card 1: Balance & Survival Grade -->
        <div class="card-academic p-6">
            <span class="label-tactical">Surplus / Deficit</span>
            <div class="text-3xl font-semibold mt-2 font-mono {{ $remaining >= 0 ? 'text-ration-green' : 'text-ration-red' }}">
                R {{ number_format($remaining, 2) }}
            </div>
            <div class="mt-3 text-xs text-ink-muted space-y-1">
                <div class="flex justify-between">
                    <span>Inflow (Allowance / Pay)</span>
                    <span class="font-mono text-ration-green">R {{ number_format($income, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Outflow (Damage Done)</span>
                    <span class="font-mono text-ration-red">R {{ number_format($expense, 2) }}</span>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-ink/10 flex items-center justify-between">
                <span class="label-tactical">Survival Assessment</span>
                @if ($grade['available'])
                    <span class="badge-status
                        {{ in_array($grade['letter'], ['A+', 'A']) ? 'bg-ration-green/10 text-ration-green border-ration-green/30' :
                        (in_array($grade['letter'], ['B', 'C']) ? 'bg-ration-highlight/20 text-ink border-ration-highlight/50' :
                        'bg-ration-red/10 text-ration-red border-ration-red/30') }}">
                        Grade {{ $grade['letter'] }} &middot; {{ $grade['score'] }}/100
                    </span>
                @else
                    <span class="badge-status bg-ink/5 text-ink-muted border-ink/15">
                        Awaiting Intel
                    </span>
                @endif
            </div>
        </div>

        <!-- Card 2: Burn Rate vs Previous Period -->
        <div class="card-academic p-6">
            <span class="label-tactical">Burn Rate Analysis</span>
            <div class="text-3xl font-semibold mt-2 font-mono text-ink">
                @if (is_null($spendingChange))
                    --
                @else
                    {{ $spendingChange > 0 ? '+' : '' }}{{ number_format($spendingChange, 1) }}%
                @endif
            </div>
            <p class="text-xs text-ink-muted mt-3 leading-relaxed">
                @if (is_null($spendingChange))
                    No previous data available to calculate your spend variance.
                @elseif ($spendingChange > 0)
                    You are burning cash <strong class="text-ration-red">{{ number_format($spendingChange, 1) }}% faster</strong> than last {{ $period }}. Fall back and review your rations!
                @else
                    You spent <strong class="text-ration-green">{{ number_format(abs($spendingChange), 1) }}% less</strong> than last {{ $period }}. Discipline holds the line!
                @endif
            </p>
        </div>

        <!-- Card 3: Quick Action / Threat Level -->
        <div class="card-academic p-6 flex flex-col justify-between">
            <div>
                <span class="label-tactical">Current Condition</span>
                <div class="text-lg font-medium text-ink mt-2">
                    @if ($remaining < 0)
                        Critical Deficit: Living on Borrowed Time
                    @elseif ($income > 0 && ($expense / $income) > 0.85)
                        Code Red: Approaching Broke Status
                    @elseif ($income > 0 && ($expense / $income) > 0.5)
                        Yellow Zone: Exercise Caution
                    @else
                        Stable Operations: Keep It Up
                    @endif
                </div>
                <p class="text-xs text-ink-muted mt-2">
                    @if ($remaining < 0)
                        You are officially in the red. Avoid all unvetted takeaway purchases until reinforcements arrive.
                    @else
                        Log every minor expenditure to prevent unbudgeted cash leaks before mid-terms hit.
                    @endif
                </p>
            </div>
            <a href="{{ route('transactions.create') }}" class="btn-tactical text-center mt-4 text-xs">
                + Report New Expense / Income
            </a>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        <!-- Recent Transactions Log -->
        <div class="card-academic p-6 lg:col-span-2">
            <div class="flex justify-between items-center mb-4">
                <span class="label-tactical">Recent Paper Trail</span>
                <a href="{{ route('transactions.index') }}" class="text-xs font-mono uppercase text-ration-blue hover:underline">
                    Access Full Black Box
                </a>
            </div>

            @if ($recentTransactions->isEmpty())
                <div class="py-8 text-center">
                    <p class="text-sm text-ink-muted">No tactical movements logged for this cycle.</p>
                    <a href="{{ route('transactions.create') }}" class="btn-tactical inline-flex mt-4">
                        Log First Incident
                    </a>
                </div>
            @else
                <div class="divide-y divide-ink/5">
                    @foreach ($recentTransactions as $transaction)
                        <div class="flex justify-between items-center py-3">
                            <div>
                                <div class="text-sm text-ink font-medium">{{ $transaction->description }}</div>
                                <div class="text-xs text-ink-muted">
                                    {{ $transaction->category->name }} &middot; {{ $transaction->transaction_date->format('d M Y') }}
                                </div>
                            </div>
                            <div class="font-mono text-sm font-semibold {{ $transaction->type === 'income' ? 'text-ration-green' : 'text-ration-red' }}">
                                {{ $transaction->type === 'income' ? '+' : '-' }}R {{ number_format($transaction->amount, 2) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Category Breakdown -->
        <div class="card-academic p-6">
            <span class="label-tactical block mb-4">Damage by Sector &middot; This {{ ucfirst($period) }}</span>

            @if ($categoryBreakdown->isEmpty())
                <p class="text-sm text-ink-muted py-4 text-center">No casualty data recorded in this timeframe.</p>
            @else
                <div class="space-y-4">
                    @foreach ($categoryBreakdown as $categoryName => $total)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-ink font-medium">{{ $categoryName }}</span>
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