<x-app-layout>
    <x-slot name="eyebrow">Money Log</x-slot>
    <x-slot name="header">Log Transaction</x-slot>

    <div class="card-academic p-6 max-w-2xl">
        <form method="POST" action="{{ route('transactions.store') }}" class="space-y-5">
            @csrf

            <div>
               <label class="flex items-center gap-2 text-sm">
                    <input type="radio" name="type" value="income" @checked(old('type', request('type')) === 'income')>
                    Income
                </label>
                <label class="flex items-center gap-2 text-sm">
                    <input type="radio" name="type" value="expense" @checked(old('type', request('type', 'expense')) === 'expense')>
                    Expense
                </label>
                </div>
            </div>

            <div>
                <label class="label-tactical block mb-1">Category</label>
                <select name="category_id" class="w-full rounded-sm border-ink/20 text-sm focus:border-ration-green focus:ring-ration-green">
                    <option value="">Select a category...</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) old('category_id') === (string) $category->id)>
                            {{ $category->name }} ({{ ucfirst($category->type) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="label-tactical block mb-1">Amount (R)</label>
                    <input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount') }}"
                        placeholder="0.00"
                        class="w-full rounded-sm border-ink/20 text-sm focus:border-ration-green focus:ring-ration-green">
                </div>

                <div>
                    <label class="label-tactical block mb-1">Date</label>
                    <input type="date" name="transaction_date" value="{{ old('transaction_date', now()->format('Y-m-d')) }}"
                        class="w-full rounded-sm border-ink/20 text-sm focus:border-ration-green focus:ring-ration-green">
                </div>
            </div>

            <div>
                <label class="label-tactical block mb-1">Description</label>
                <input type="text" name="description" value="{{ old('description') }}"
                    placeholder="e.g. Programming textbook"
                    class="w-full rounded-sm border-ink/20 text-sm focus:border-ration-green focus:ring-ration-green">
            </div>

            <div>
                <label class="label-tactical block mb-1">Note (optional)</label>
                <textarea name="note" rows="3"
                    placeholder="Any extra detail..."
                    class="w-full rounded-sm border-ink/20 text-sm focus:border-ration-green focus:ring-ration-green">{{ old('note') }}</textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <x-submit-button label="Save Transaction" loading-label="Saving..." />
                <a href="{{ route('transactions.index') }}" class="btn-tactical-outline">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>