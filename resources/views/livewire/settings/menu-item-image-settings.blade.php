<div>
    <div class="mx-4 p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
        <h3 class="mb-4 text-xl font-semibold dark:text-white inline-flex gap-4 items-center">
            @lang('modules.menu.menuItemImageSettings')
        </h3>

        <form wire:submit="submitForm" class="space-y-6">
            <div class="flex items-center justify-between py-2">
                <div class="flex flex-col flex-grow">
                    <div class="text-base font-semibold text-gray-900 dark:text-white">
                        @lang('modules.menu.disableDefaultMenuItemImage')
                    </div>
                    <div class="text-base font-normal text-gray-500 dark:text-gray-400">
                        @lang('modules.menu.disableDefaultMenuItemImageDescription')
                    </div>
                </div>

                <label for="disableDefaultImage" class="relative flex items-center cursor-pointer">
                    <input type="checkbox" id="disableDefaultImage" wire:model.live="disableDefaultImage" class="sr-only">
                    <span class="h-6 bg-gray-200 rounded-full w-11 toggle-bg dark:bg-gray-700 dark:border-gray-600"></span>
                </label>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <div class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        @lang('modules.menu.currentDefaultImage')
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="h-16 w-16 rounded-md overflow-hidden bg-gray-50 border border-gray-200 flex items-center justify-center">
                            @if($this->currentDefaultImageUrl)
                                <img src="{{ $this->currentDefaultImageUrl }}" alt="Default image" class="h-full w-full object-cover">
                            @else
                                <div class="text-xs text-gray-400">-</div>
                            @endif
                        </div>

                        @if(!empty($settings->menu_item_default_image_path))
                            <x-danger-button type="button" wire:click="removeDefaultImage" wire:loading.attr="disabled">
                                @lang('app.remove')
                            </x-danger-button>
                        @endif
                    </div>
                </div>

                <div class="space-y-2">
                    <x-label value="{{ __('modules.menu.uploadDefaultMenuItemImage') }}" />
                    <input type="file"
                           class="mt-1 block w-full text-sm text-gray-700 dark:text-gray-300"
                           wire:model="defaultImage"
                           accept="image/png,image/jpeg,image/jpg,image/webp,image/svg+xml" />
                    <x-input-error for="defaultImage" class="mt-2" />
                    <div wire:loading wire:target="defaultImage" class="text-xs text-gray-500 mt-1">
                        {{ __('app.uploading') }}...
                    </div>

                    @if($defaultImage)
                        <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            @lang('modules.menu.preview')
                        </div>
                        <div class="h-16 w-16 rounded-md overflow-hidden bg-gray-50 border border-gray-200">
                            <img src="{{ $defaultImage->temporaryUrl() }}" class="h-full w-full object-cover" alt="Preview">
                        </div>
                    @endif
                </div>
            </div>

            <div>
                <x-button wire:loading.attr="disabled">@lang('app.save')</x-button>
            </div>
        </form>
    </div>
</div>

