@props(['active'])

@php
$classes = ($active ?? false)
    ? 'flex items-center gap-x-3.5 py-2 px-2.5 bg-white text-sm text-slate-700 rounded-lg hover:bg-white dark:bg-slate-900 dark:text-white dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-slate-700'
    : 'flex items-center gap-x-3.5 py-2 px-2.5 text-sm text-slate-700 rounded-lg hover:bg-white hover:text-slate-700 focus:outline-none focus:ring-1 focus:ring-slate-700 dark:text-slate-400 dark:hover:text-slate-300 dark:hover:bg-slate-800 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-slate-700';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if (isset($icon))
        <div class="flex-shrink-0">
            {{ $icon }}
        </div>
    @endif
    {{ $slot }}
</a> 