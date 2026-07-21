<li>
    <a
        href="{{ $link }}"
        @if($navigate) wire:navigate @endif
        @class([
            // Base styles
            'flex items-center gap-2.5 px-2 py-2 rounded-lg text-sm font-medium cursor-pointer w-full transition-colors duration-150 text-gray-500 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700',
            // Active state
            'text-skin-base font-bold bg-skin-base/[.20]' => $active,
            // Hover state should always apply if not active
            'hover:text-gray-800 hover:bg-skin-base/[.10]' => !$active,
        ])
    >
        <span
            @class([
                'w-4 h-4 rounded bg-brand-600 flex items-center justify-center text-[9px]',
                'text-skin-base' => $active
            ])
        >
            {!! $customIcon ?? $icon !!}
        </span>
        {{ $name }}
    </a>
</li>
