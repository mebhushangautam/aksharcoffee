<div>
    @php
        $languageDropdownId = 'delivery-language-dropdown-' . $this->getId();
    @endphp

    <button type="button" data-dropdown-toggle="{{ $languageDropdownId }}"
        class="inline-flex items-center justify-center gap-1.5 rounded-lg p-2.5 text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
            <path d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m7.5-6.923c-.67.204-1.335.82-1.887 1.855A8 8 0 0 0 5.145 4H7.5zM4.09 4a9.3 9.3 0 0 1 .64-1.539 7 7 0 0 1 .597-.933A7.03 7.03 0 0 0 2.255 4zm-.582 3.5c.03-.877.138-1.718.312-2.5H1.674a7 7 0 0 0-.656 2.5zM4.847 5a12.5 12.5 0 0 0-.338 2.5H7.5V5zM8.5 5v2.5h2.99a12.5 12.5 0 0 0-.337-2.5zM4.51 8.5a12.5 12.5 0 0 0 .337 2.5H7.5V8.5zm3.99 0V11h2.653c.187-.765.306-1.608.338-2.5zM5.145 12q.208.58.468 1.068c.552 1.035 1.218 1.65 1.887 1.855V12zm.182 2.472a7 7 0 0 1-.597-.933A9.3 9.3 0 0 1 4.09 12H2.255a7 7 0 0 0 3.072 2.472M3.82 11a13.7 13.7 0 0 1-.312-2.5h-2.49c.062.89.291 1.733.656 2.5zm6.853 3.472A7 7 0 0 0 13.745 12H11.91a9.3 9.3 0 0 1-.64 1.539 7 7 0 0 1-.597.933M8.5 12v2.923c.67-.204 1.335-.82 1.887-1.855q.26-.487.468-1.068zm3.68-1h2.146c.365-.767.594-1.61.656-2.5h-2.49a13.7 13.7 0 0 1-.312 2.5m2.802-3.5a7 7 0 0 0-.656-2.5H12.18c.174.782.282 1.623.312 2.5zM11.27 2.461c.247.464.462.98.64 1.539h1.835a7 7 0 0 0-3.072-2.472c.218.284.418.598.597.933M10.855 4a8 8 0 0 0-.468-1.068C9.835 1.897 9.17 1.282 8.5 1.077V4z"/>
        </svg>
        @if ($activeLanguage)
            <span class="hidden xl:inline">
                {{ \App\Models\LanguageSetting::LANGUAGES_TRANS[$activeLanguage->language_code] ?? $activeLanguage->language_name }}
            </span>
        @endif
    </button>

    <div class="z-50 hidden my-4 min-w-44 list-none divide-y divide-gray-100 rounded bg-white text-base shadow dark:bg-gray-700"
        id="{{ $languageDropdownId }}">
        <ul class="py-1" role="none">
            @foreach (languages() as $item)
                <li wire:key="delivery-language-{{ $item->id }}">
                    <button type="button" wire:click="setLanguage('{{ $item->language_code }}')"
                        class="flex w-full items-center gap-2 px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white"
                        role="menuitem">
                        <img class="h-3.5 w-3.5 rounded-full object-cover" src="{{ $item->flagUrl }}" alt="">
                        <span class="min-w-0 flex-1 truncate">
                            {{ \App\Models\LanguageSetting::LANGUAGES_TRANS[$item->language_code] ?? $item->language_name }}
                        </span>
                        @if ($activeLanguage?->id === $item->id)
                            <svg class="h-4 w-4 shrink-0 text-skin-base" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                                <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z"/>
                            </svg>
                        @endif
                    </button>
                </li>
            @endforeach
        </ul>
    </div>
</div>
