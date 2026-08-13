@props(['label' => 'Save', 'loadingLabel' => 'Saving...'])

<button
    type="submit"
    x-data="{ submitting: false }"
    x-on:click="submitting = true"
    :disabled="submitting"
    :class="submitting ? 'opacity-60 cursor-not-allowed' : ''"
    {{ $attributes->merge(['class' => 'btn-tactical']) }}
>
    <span x-show="!submitting">{{ $label }}</span>
    <span x-show="submitting" x-cloak>{{ $loadingLabel }}</span>
</button>