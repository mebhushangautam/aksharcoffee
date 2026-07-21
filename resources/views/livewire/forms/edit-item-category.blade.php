<form wire:submit="submitForm">
    @csrf

    <div class="space-y-4">
        <!-- Language Selection -->
        @if(count($languages) > 1)
        <div class="mb-4">
            <x-label for="language" :value="__('modules.menu.selectLanguage')" />
            <x-select class="mt-1 block w-full" wire:model.live="currentLanguage">
                @foreach($languages as $code => $name)
                <option value="{{ $code }}">{{ $name }}</option>
                @endforeach
            </x-select>
        </div>
        @endif

        <!-- Menu Name with Translation -->
        <div>
            <x-label for="categoryName"
                :value="__('modules.menu.categoryName') . ' (' . ($languages[$currentLanguage] ?? strtoupper($currentLanguage)) . ')'" />
            <x-input id="categoryName" class="block mt-1 w-full" type="text"
                placeholder="{{ __('placeholders.categoryNamePlaceholder') }}" wire:model.defer="categoryName"
                wire:change="updateTranslation" />
            <x-input-error for="translations.{{ $globalLocale }}" class="mt-2" />
        </div>

        <!-- Category Image -->
        <div>
            <x-label for="categoryImage" :value="__('modules.menu.itemImage')"/>

            <input
                id="categoryImage"
                class="block w-full text-sm border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 text-slate-500 mt-1"
                type="file"
                wire:model.defer="categoryImageTemp"
                accept="image/*"
            >

            <x-input-error for="categoryImageTemp" class="mt-2" />

            <div class="mt-2 flex items-center gap-3">
                @if ($itemCategory?->image && !$categoryImageTemp)
                    <img
                        src="{{ $itemCategory->category_image_url }}"
                        alt="Current"
                        class="w-16 h-16 object-cover rounded-lg border border-gray-300"
                    />

                    <x-danger-button type="button" wire:click="removeCurrentImage" wire:loading.attr="disabled">
                        {{ __('app.remove') }}
                    </x-danger-button>
                @endif

                @if($categoryImageTemp)
                    <div class="relative inline-block">
                        <img src="{{ $categoryImageTemp->temporaryUrl() }}" alt="Preview" class="w-16 h-16 object-cover rounded-lg border border-gray-300">
                        <button type="button" wire:click="removeSelectedImage"
                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Translation Preview with Delete Option -->
        @if(count($languages) > 1 && array_filter($translations))
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-2.5">
            <x-label :value="__('modules.menu.translations')" class="text-sm mb-2 last:mb-0" />
            <div class="divide-y divide-gray-200 dark:divide-gray-600">
                @foreach($translations as $lang => $text)
                @if(!empty($text))
                <div class="flex items-center gap-3 py-1.5">
                    <span class="min-w-[80px] text-xs font-medium text-gray-600 dark:text-gray-300">
                        {{ $languages[$lang] ?? strtoupper($lang) }}
                    </span>
                    <span class="flex-1 text-xs text-gray-700 dark:text-gray-200">{{ $text }}</span>
                    @if($lang !== $globalLocale)
                    <button type="button"
                        class="p-1 text-gray-400 hover:text-red-500 dark:hover:text-red-300 transition-colors rounded-full hover:bg-red-100 dark:hover:bg-red-800"
                        wire:click="removeTranslation('{{ $lang }}')"
                        title="Remove translation">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    @endif
                </div>
                @endif
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <div class="flex justify-end w-full space-x-4 mt-6">
        <x-button class="ml-3">@lang('app.save')</x-button>
    </div>

</form>
