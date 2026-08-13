@props(['title', 'message' => null, 'actionLabel' => null, 'actionUrl' => null])

<div class="text-center py-12 px-4">
    <div class="inline-flex items-center justify-center w-10 h-10 rounded-sm border border-ink/20 text-ink-muted font-mono text-xs mb-3">
        &empty;
    </div>
    <p class="text-sm font-semibold text-ink">{{ $title }}</p>
    @if ($message)
        <p class="text-sm text-ink-muted mt-1 max-w-sm mx-auto">{{ $message }}</p>
    @endif
    @if ($actionLabel && $actionUrl)
        <a href="{{ $actionUrl }}" class="btn-tactical inline-flex mt-4">
            {{ $actionLabel }}
        </a>
    @endif
</div>