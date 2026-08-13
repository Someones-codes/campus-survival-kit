<x-app-layout>
    <x-slot name="eyebrow">Rationing</x-slot>
    <x-slot name="header">Edit Ration</x-slot>

    <div class="card-academic p-6 max-w-xl">
        <form method="POST" action="{{ route('budgets.update', $budget) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="label-tactical block mb-1">Name</label>
                <input type="text" name="name" value="{{ old('name', $budget->name) }}"
                    class="w-full rounded-sm border-ink/20 text-sm focus:border-ration-green focus:ring-ration-green">
            </div>

            <div>
                <label class="label-tactical block mb-1">Category (optional)</label>
                <select name="category_id" class="w-full rounded-sm border-ink/20 text-sm focus:border-ration-green focus:ring-ration-green">
                    <option value="">All Expenses</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}"
                            @selected((string) old('category_id', $budget->category_id) === (string) $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="label-tactical block mb-1">Amount (R)</label>
                <input type="number" step="0.01" min="0.01" name="amount"
                    value="{{ old('amount', $budget->amount) }}"
                    class="w-full rounded-sm border-ink/20 text-sm focus:border-ration-green focus:ring-ration-green">
            </div>

            <div>
                <label class="label-tactical block mb-1">Period</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 text-sm">
                        <input type="radio" name="period" value="weekly" @checked(old('period', $budget->period) === 'weekly')>
                        Weekly
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="radio" name="period" value="monthly" @checked(old('period', $budget->period) === 'monthly')>
                        Monthly
                    </label>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-tactical">Update Ration</button>
                <a href="{{ route('budgets.index') }}" class="btn-tactical-outline">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>