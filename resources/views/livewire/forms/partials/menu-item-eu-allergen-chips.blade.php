@php
    $euKeys = restaurant()->selectableEuAllergenKeys();
    $domPrefix = $euAllergenDomPrefix ?? 'eu-mi';
@endphp
@if (count($euKeys) > 0)
    <div>
        <x-label :value="__('modules.menu.euAllergensSectionTitle')" class="mb-3" />
        <p class="mb-3 text-sm text-gray-500 dark:text-gray-400">
            @lang('modules.menu.euAllergensSectionHelp')
        </p>
        <ul class="grid w-full grid-cols-2 gap-2 md:grid-cols-3">
            @foreach ($euKeys as $key)
                <li wire:key="{{ $domPrefix }}-{{ $key }}">
                    <input type="checkbox" id="{{ $domPrefix }}-allergen-{{ $key }}" class="peer hidden"
                        wire:model.live="selectedEuAllergens" value="{{ $key }}">
                    <label for="{{ $domPrefix }}-allergen-{{ $key }}"
                        class="inline-flex w-full cursor-pointer select-none items-center gap-2 rounded-lg border-2 border-gray-200 bg-white p-2 text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-600 peer-checked:border-skin-base peer-checked:text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-300 dark:peer-checked:text-skin-base">
                        <img src="{{ \App\Support\EuAnnexIiAllergens::defaultIconUrl($key) }}" alt=""
                            class="h-5 w-5 shrink-0 object-contain" width="20" height="20" loading="lazy">
                        <span class="min-w-0 flex-1 text-left leading-snug">@lang(\App\Support\EuAnnexIiAllergens::langKey($key))</span>
                    </label>
                </li>
            @endforeach
        </ul>
        <x-input-error for="selectedEuAllergens" class="mt-2" />
    </div>
@endif
