<x-app-layout>
    <x-slot name="eyebrow">Recon Reports</x-slot>
    <x-slot name="header">Reports</x-slot>

    <div class="flex justify-end mb-4">
        <div class="inline-flex rounded-sm border border-ink/20 overflow-hidden">
            @foreach (['daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly'] as $value => $label)
                <a href="{{ route('reports.index', ['period' => $value]) }}"
                    class="px-4 py-2 text-xs font-mono uppercase tracking-widest
                        {{ $period === $value ? 'bg-ration-green text-paper' : 'bg-white text-ink-light hover:bg-paper-dark/40' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    <span class="label-tactical block mb-4">
        {{ ucfirst($period) }} Report &middot; {{ $start->format('d M Y') }}
        @if (! $start->isSameDay($end))
            &ndash; {{ $end->format('d M Y') }}
        @endif
    </span>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="card-academic p-5">
            <span class="label-tactical">Income</span>
            <div class="text-2xl font-mono font-semibold text-ration-green mt-1">R {{ number_format($income, 2) }}</div>
        </div>
        <div class="card-academic p-5">
            <span class="label-tactical">Expenses</span>
            <div class="text-2xl font-mono font-semibold text-ration-red mt-1">R {{ number_format($expense, 2) }}</div>
        </div>
        <div class="card-academic p-5">
            <span class="label-tactical">Net Balance</span>
            <div class="text-2xl font-mono font-semibold mt-1 {{ $netBalance >= 0 ? 'text-ration-green' : 'text-ration-red' }}">
                R {{ number_format($netBalance, 2) }}
            </div>
        </div>
        <div class="card-academic p-5">
            <span class="label-tactical">Transactions</span>
            <div class="text-2xl font-mono font-semibold text-ink mt-1">{{ $transactionCount }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 card-academic p-6">
            <span class="label-tactical block mb-4">Spending by Category</span>

            @if ($categoryBreakdown->isEmpty())
                <p class="text-sm text-ink-muted">No expenses recorded for this period.</p>
            @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-ink/10">
                            <th class="text-left py-2 label-tactical">Category</th>
                            <th class="text-right py-2 label-tactical">Entries</th>
                            <th class="text-right py-2 label-tactical">Total</th>
                            <th class="text-right py-2 label-tactical">Share</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categoryBreakdown as $categoryName => $data)
                            <tr class="border-b border-ink/5">
                                <td class="py-2 text-ink">{{ $categoryName }}</td>
                                <td class="py-2 text-right text-ink-muted">{{ $data['count'] }}</td>
                                <td class="py-2 text-right font-mono text-ink">R {{ number_format($data['total'], 2) }}</td>
                                <td class="py-2 text-right font-mono text-ink-muted">
                                    {{ $expense > 0 ? number_format(($data['total'] / $expense) * 100, 0) : 0 }}%
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="space-y-4">
            <div class="card-academic p-5">
                <span class="label-tactical">Vs Previous {{ ucfirst($period) }}</span>
                @if (! is_null($spendingChange))
                    <div class="text-xl font-mono font-semibold mt-1 {{ $spendingChange > 0 ? 'text-ration-red' : 'text-ration-green' }}">
                        {{ $spendingChange > 0 ? '+' : '' }}{{ number_format($spendingChange, 1) }}%
                    </div>
                    <div class="text-xs text-ink-muted mt-1">Previous: R {{ number_format($previousExpense, 2) }}</div>
                @else
                    <div class="text-sm text-ink-muted mt-1">No prior data to compare.</div>
                @endif
            </div>

            <div class="card-academic p-5">
                <span class="label-tactical">Average Daily Spend</span>
                <div class="text-xl font-mono font-semibold text-ink mt-1">R {{ number_format($averageDailySpend, 2) }}</div>
            </div>

            @if ($highestSpendingDay && $highestSpendingDay->isNotEmpty())
                <div class="card-academic p-5">
                    <span class="label-tactical">Highest Spending Day</span>
                    <div class="text-lg font-mono font-semibold text-ration-red mt-1">
                        {{ \Carbon\Carbon::parse($highestSpendingDay->keys()->first())->format('d M Y') }}
                    </div>
                    <div class="text-xs text-ink-muted mt-1">R {{ number_format($highestSpendingDay->first(), 2) }} spent</div>
                </div>
            @endif
        </div>

    </div>
</x-app-layout>