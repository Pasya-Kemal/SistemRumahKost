@props(['label', 'value', 'icon' => 'circle', 'highlight' => false, 'tid' => ''])
@php
    $highlight = $highlight ?? false;
    $icon      = $icon      ?? 'circle';
    $tid       = $tid       ?? '';
@endphp
<div data-testid="{{ $tid }}" class="rounded-xl border {{ $highlight ? 'border-amber-400/30' : 'border-stone-800' }} bg-ink-800 p-5 flex items-center gap-4">
    <div class="w-11 h-11 rounded-lg grid place-items-center {{ $highlight ? 'bg-amber-400/10 text-amber-300' : 'bg-stone-800 text-stone-300' }}">
        <i data-lucide="{{ $icon }}" class="w-5 h-5"></i>
    </div>
    <div>
        <div class="text-xs uppercase tracking-widest text-stone-500">{{ $label }}</div>
        <div class="text-2xl font-display tracking-tight mt-0.5">{{ $value }}</div>
    </div>
</div>
