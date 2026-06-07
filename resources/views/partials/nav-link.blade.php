@props(['href', 'label', 'active' => false, 'tid' => ''])
<a href="{{ $href }}"
   data-testid="{{ $tid }}"
   class="px-3 py-2 rounded-md text-sm font-medium transition {{ $active ? 'bg-amber-400 text-stone-900' : 'text-stone-300 hover:bg-stone-800' }}">
    {{ $label }}
</a>
