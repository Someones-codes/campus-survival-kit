@if (session('success'))
    <div class="card-academic border-l-4 border-l-ration-green px-4 py-3 mb-4 flex items-start gap-3">
        <span class="label-tactical text-ration-green">Confirmed</span>
        <span class="text-sm text-ink">{{ session('success') }}</span>
    </div>
@endif

@if (session('error'))
    <div class="card-academic border-l-4 border-l-ration-red px-4 py-3 mb-4 flex items-start gap-3">
        <span class="label-tactical text-ration-red">Error</span>
        <span class="text-sm text-ink">{{ session('error') }}</span>
    </div>
@endif

@if ($errors->any())
    <div class="card-academic border-l-4 border-l-ration-red px-4 py-3 mb-4">
        <span class="label-tactical text-ration-red block mb-2">Validation Issues</span>
        <ul class="text-sm text-ink list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif