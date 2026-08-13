<x-app-layout>
    <x-slot name="eyebrow">Rationing</x-slot>
    <x-slot name="header">Rations</x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 space-y-4">
            @if ($budgets->isEmpty())
                <div class="card-academic p-8 text-center">
                    <p class="text-sm text-ink-muted">No Rations set yet. Create one to start tracking a limit.</p>
                </div>
            @else
                @foreach ($budgets as $budget)
                    <div class="card-academic p-5">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <div class="text-sm font-semibold text-ink">{{ $budget->name }}</div>
                                <div class="text-xs text-ink-muted mt-0.5">
                                    {{ $budget->category->name ?? 'All Expenses' }} &middot;
                                    {{ ucfirst($budget->period) }} &middot;
                                    {{ $budget->period_start->format('d M') }}&ndash;{{ $budget->period_end->format('d M') }}
                                </div>
                            </div>

                            <span class="badge-status-{{ $budget->status === 'over' ? 'over' : ($budget->status === 'warning' ? 'warn' : 'ok') }}">
                                {{ $budget->status === 'over' ? 'Over Ration' : ($budget->status === 'warning' ? 'Warning' : 'On Track') }}
                            </span>
                        </div>

                        <div class="flex justify-between text-sm mb-1.5">
                            <span class="text-ink-muted">
                                R {{ number_format($budget->spent, 2) }} of R {{ number_format($budget->amount, 2) }}
                            </span>
                            <span class="font-mono {{ $budget->remaining >= 0 ? 'text-ration-green' : 'text-ration-red' }}">
                                R {{ number_format($budget->remaining, 2) }} remaining
                            </span>
                        </div>

                        <div class="h-2 bg-paper-dark rounded-sm overflow-hidden">
                            <div class="h-full {{ $budget->status === 'over' ? 'bg-ration-red' : ($budget->status === 'warning' ? 'bg-ration-highlight' : 'bg-ration-green') }}"
                                style="width: {{ min(100, $budget->percent_used) }}%">
                            </div>
                        </div>

                        <div class="flex justify-between items-center mt-3">
                            <span class="label-tactical">{{ number_format(min(999, $budget->percent_used), 0) }}% used</span>
                            <div class="flex gap-3">
                                <a href="{{ route('budgets.edit', $budget) }}" class="text-xs font-mono uppercase text-ration-blue hover:underline">Edit</a>
                                <form action="{{ route('budgets.destroy', $budget) }}" method="POST"
                                    onsubmit="return confirm('Remove this Ration?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-mono uppercase text-ration-red hover:underline">Remove</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <div>
            <span class="label-tactical block mb-3">New Ration</span>
            <div class="card-academic p-6">
                <form method="POST" action="{{ route('budgets.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="label-tactical block mb-1">Name</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            placeholder="e.g. Transport Ration"
                            class="w-full rounded-sm border-ink/20 text-sm focus:border-ration-green focus:ring-ration-green">
                    </div>

                    <div>
                        <label class="label-tactical block mb-1">Category (optional)</label>
                        <select name="category_id" class="w-full rounded-sm border-ink/20 text-sm focus:border-ration-green focus:ring-ration-green">
                            <option value="">All Expenses</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected((string) old('category_id') === (string) $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="label-tactical block mb-1">Amount (R)</label>
                        <input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount') }}"
                            placeholder="0.00"
                            class="w-full rounded-sm border-ink/20 text-sm focus:border-ration-green focus:ring-ration-green">
                    </div>

                    <div>
                        <label class="label-tactical block mb-1">Period</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 text-sm">
                                <input type="radio" name="period" value="weekly" @checked(old('period', 'weekly') === 'weekly')>
                                Weekly
                            </label>
                            <label class="flex items-center gap-2 text-sm">
                                <input type="radio" name="period" value="monthly" @checked(old('period') === 'monthly')>
                                Monthly
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn-tactical w-full">Create Ration</button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>