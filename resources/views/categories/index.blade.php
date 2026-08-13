<x-app-layout>
    <x-slot name="eyebrow">Spending Units</x-slot>
    <x-slot name="header">Categories</x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 space-y-6">

            <div>
                <span class="label-tactical block mb-3">Income Units</span>
                <div class="card-academic divide-y divide-ink/5">
                    @foreach ($categories->where('type', 'income') as $category)
                        <div class="flex justify-between items-center px-4 py-3">
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-ink">{{ $category->name }}</span>
                                @if ($category->is_default)
                                    <span class="badge-status bg-ink/5 text-ink-muted border-ink/15">Default</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="font-mono text-sm text-ration-green">
                                    R {{ number_format($totals[$category->id] ?? 0, 2) }}
                                </span>
                                @if (! $category->is_default)
                                    <form action="{{ route('categories.destroy', $category) }}" method="POST"
                                        onsubmit="return confirm('Delete this spending unit?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-mono uppercase text-ration-red hover:underline">
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div>
                <span class="label-tactical block mb-3">Expense Units</span>
                <div class="card-academic divide-y divide-ink/5">
                    @foreach ($categories->where('type', 'expense') as $category)
                        <div class="flex justify-between items-center px-4 py-3">
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-ink">{{ $category->name }}</span>
                                @if ($category->is_default)
                                    <span class="badge-status bg-ink/5 text-ink-muted border-ink/15">Default</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="font-mono text-sm text-ration-red">
                                    R {{ number_format($totals[$category->id] ?? 0, 2) }}
                                </span>
                                @if (! $category->is_default)
                                    <form action="{{ route('categories.destroy', $category) }}" method="POST"
                                        onsubmit="return confirm('Delete this spending unit?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-mono uppercase text-ration-red hover:underline">
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

        <div>
            <span class="label-tactical block mb-3">Add Spending Unit</span>
            <div class="card-academic p-6">
                <form method="POST" action="{{ route('categories.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="label-tactical block mb-1">Name</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            placeholder="e.g. Gym Membership"
                            class="w-full rounded-sm border-ink/20 text-sm focus:border-ration-green focus:ring-ration-green">
                    </div>

                    <div>
                        <label class="label-tactical block mb-1">Type</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 text-sm">
                                <input type="radio" name="type" value="income" @checked(old('type') === 'income')>
                                Income
                            </label>
                            <label class="flex items-center gap-2 text-sm">
                                <input type="radio" name="type" value="expense" @checked(old('type', 'expense') === 'expense')>
                                Expense
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn-tactical w-full">Create Unit</button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>