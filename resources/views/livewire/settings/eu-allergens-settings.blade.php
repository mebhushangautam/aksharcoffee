<div>
    <div class="mx-4 mb-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
        <h3 class="mb-2 inline-flex items-center gap-2 text-xl font-semibold dark:text-white">
            @lang('modules.settings.euAllergensFicTitle')
        </h3>
        <p class="mb-6 text-sm text-gray-500 dark:text-gray-400">
            @lang('modules.settings.euAllergensFicDescription')
        </p>

        <form wire:submit="submitForm" class="space-y-6">
            <div class="flex items-center justify-between py-2">
                <div class="flex flex-grow flex-col">
                    <div class="text-base font-semibold text-gray-900 dark:text-white">
                        @lang('modules.settings.euAllergensEnable')
                    </div>
                    <div class="text-base font-normal text-gray-500 dark:text-gray-400">
                        @lang('modules.settings.euAllergensEnableDescription')
                    </div>
                </div>

                <label for="euAllergensEnabled" class="relative flex cursor-pointer items-center">
                    <input type="checkbox" id="euAllergensEnabled" wire:model.live="euAllergensEnabled" class="sr-only">
                    <span class="h-6 w-11 rounded-full bg-gray-200 toggle-bg dark:border-gray-600 dark:bg-gray-700"></span>
                </label>
            </div>

            <div class="space-y-3 rounded-lg border border-gray-100 bg-gray-50/80 p-4 dark:border-gray-700 dark:bg-gray-900/40">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="text-sm font-semibold text-gray-900 dark:text-white">
                            @lang('modules.settings.euAllergensDefaultAnnexHeading')
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            @lang('modules.settings.euAllergensDefaultAnnexHelp')
                        </div>
                    </div>
                    <x-secondary-button type="button" wire:click="resetToAnnexIiDefaults" wire:loading.attr="disabled">
                        @lang('modules.settings.euAllergensResetToDefaultsButton')
                    </x-secondary-button>
                </div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($annexKeys as $key)
                        <label class="flex cursor-pointer items-center gap-3 rounded-md border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800">
                            <input type="checkbox" class="rounded border-gray-300 text-skin-base focus:ring-skin-base dark:border-gray-600 dark:bg-gray-700"
                                wire:model.live="selectedKeys"
                                value="{{ $key }}"
                                @disabled(!$euAllergensEnabled)>
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-md border border-gray-100 bg-gray-50 dark:border-gray-600 dark:bg-gray-900/60">
                                <img src="{{ $annexIconUrls[$key] ?? '' }}"
                                    alt="{{ $annexLabels[$key] ?? $key }}"
                                    class="h-8 w-8 object-contain"
                                    width="32"
                                    height="32"
                                    loading="lazy"
                                    decoding="async">
                            </span>
                            <span class="min-w-0 flex-1 font-medium text-gray-900 dark:text-white">{{ $annexLabels[$key] ?? $key }}</span>
                        </label>
                    @endforeach
                </div>

                <x-input-error for="selectedKeys" class="mt-1" />
            </div>

            <div>
                <x-button wire:loading.attr="disabled">@lang('app.save')</x-button>
            </div>
        </form>
    </div>
</div>
