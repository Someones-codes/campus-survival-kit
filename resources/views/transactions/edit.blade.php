<x-app-layout>
    <x-slot name="eyebrow">Money Log</x-slot>
    <x-slot name="header">Edit Transaction</x-slot>

    <div class="card-academic p-6 max-w-2xl">
        <form method="POST" action="{{ route('transactions.update', $transaction) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
    <label class="label-tactical block mb-1">Type</label>
    <div class="flex gap-4">
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
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}"
                            @selected((string) old('category_id', $transaction->category_id) === (string) $category->id)>
                            {{ $category->name }} ({{ ucfirst($category->type) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="label-tactical block mb-1">Amount (R)</label>
                    <input type="number" step="0.01" min="0.01" name="amount"
                        value="{{ old('amount', $transaction->amount) }}"
                        class="w-full rounded-sm border-ink/20 text-sm focus:border-ration-green focus:ring-ration-green">
                </div>

                <div>
                    <label class="label-tactical block mb-1">Date</label>
                    <input type="date" name="transaction_date"
                        value="{{ old('transaction_date', $transaction->transaction_date->format('Y-m-d')) }}"
                        class="w-full rounded-sm border-ink/20 text-sm focus:border-ration-green focus:ring-ration-green">
                </div>
            </div>

            <div>
                <label class="label-tactical block mb-1">Description</label>
                <input type="text" name="description" value="{{ old('description', $transaction->description) }}"
                    class="w-full rounded-sm border-ink/20 text-sm focus:border-ration-green focus:ring-ration-green">
            </div>

            <div>
                <label class="label-tactical block mb-1">Note (optional)</label>
                <textarea name="note" rows="3"
                    class="w-full rounded-sm border-ink/20 text-sm focus:border-ration-green focus:ring-ration-green">{{ old('note', $transaction->note) }}</textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-tactical">Update Transaction</button>
                <a href="{{ route('transactions.index') }}" class="btn-tactical-outline">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>