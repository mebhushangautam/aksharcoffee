<div>
    @php
        $languageSettings = collect(App\Models\LanguageSetting::LANGUAGES)
            ->keyBy('language_code')
            ->map(function ($lang) {
                return [
                    'flag_url' => asset('flags/1x1/' . strtolower($lang['flag_code']) . '.svg'),
                    'name' => App\Models\LanguageSetting::LANGUAGES_TRANS[$lang['language_code']] ?? $lang['language_name'],
                ];
            });
    @endphp
    <form wire:submit="submitForm">
        @csrf
        <div class="space-y-4">

            @if(count($languages) > 1)
            <div class="mb-4">
                <x-label for="language" :value="__('modules.menu.selectLanguage')" />
                <div class="flex flex-wrap gap-2 mt-2">
                    @foreach($languages as $code => $name)
                        <button
                            type="button"
                            wire:click="$set('currentLanguage', '{{ $code }}')"
                            @class([
                                'px-3 py-1.5 text-xs rounded-md border transition-all duration-200 focus:ring-2 focus:ring-offset-2 focus:outline-none flex items-center gap-2',
                                'bg-skin-base text-white border-skin-base shadow-sm font-medium focus:ring-skin-base' => $currentLanguage === $code,
                                'bg-white text-gray-700 border-gray-200 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-700' => $currentLanguage !== $code,
                            ])
                        >
                            <img src="{{ $languageSettings->get($code)['flag_url'] ?? asset('flags/1x1/' . strtolower($code) . '.svg') }}" alt="{{ $code }}" class="w-4 h-4 rounded-sm object-cover" />
                            <span>{{ $languageSettings->get($code)['name'] ?? $name }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
            @endif

            <div>
                <x-label for="reason" :value="__('modules.settings.reason') . ' (' . ($languages[$currentLanguage] ?? $currentLanguage) . ')'" />
                <x-input id="reason" class="block w-full mt-1" type="text" wire:model="reason" wire:change="updateTranslation" />
                <x-input-error for="translationReasons.{{ $globalLocale }}" class="mt-2" />
            </div>

            @if(count($languages) > 1 && array_filter($translationReasons))
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-2.5">
                <x-label :value="__('modules.menu.translations')" class="text-sm mb-2" />
                <div class="divide-y divide-gray-200 dark:divide-gray-600">
                    @foreach($languages as $lang => $langName)
                        @if(!empty($translationReasons[$lang]))
                        <div class="flex items-center gap-3 py-2" wire:key="add-cancel-reason-translation-{{ $lang }}">
                            <span class="min-w-[80px] text-xs font-medium text-gray-600 dark:text-gray-300">
                                {{ $languageSettings->get($lang)['name'] ?? strtoupper($lang) }}
                            </span>
                            <span class="text-xs text-gray-700 dark:text-gray-200">{{ $translationReasons[$lang] }}</span>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            <div>
                <x-label value="{{ __('modules.settings.cancellationTypes') }}" class="mb-2" />
                <div class="space-y-3">
                    <label class="flex items-center">
                        <input type="checkbox" wire:model.defer="cancel_order" class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-skin-base focus:ring-skin-base dark:focus:ring-skin-base dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        <span class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">@lang('modules.order.cancelOrder')</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" wire:model.defer="cancel_kot" class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-skin-base focus:ring-skin-base dark:focus:ring-skin-base dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        <span class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">@lang('modules.order.cancelKot')</span>
                    </label>
                </div>
                <x-input-error for="cancel_order" class="mt-2" />
            </div>

        </div>

        <div class="flex w-full pb-4 mt-6 space-x-4 rtl:space-x-reverse">
            <x-button>@lang('app.save')</x-button>
            <x-button-cancel wire:click="$dispatch('hideAddKotReason')" wire:loading.attr="disabled">@lang('app.cancel')</x-button-cancel>
        </div>
    </form>
</div>
