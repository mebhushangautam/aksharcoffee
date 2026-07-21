<div>
    <form wire:submit="submitForm">
        @csrf
        <div class="space-y-4">

            <div>
                <x-label for="areaName" value="{{ __('modules.table.areaName') }}" />
                <x-input id="areaName" class="block mt-1 w-full" type="text" placeholder="{{ __('placeholders.areaNamePlaceholder') }}" autofocus wire:model='areaName' />
                <x-input-error for="areaName" class="mt-2" />
            </div>

            <div>
                <x-label for="areaImage" value="{{ __('modules.table.areaImage') }}" />

                <input
                    id="areaImage"
                    class="block my-1 w-full text-sm bg-gray-50 rounded-lg border border-gray-300 cursor-pointer focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 text-slate-500"
                    type="file"
                    wire:model="areaImageTemp"
                    accept="image/*">

                <x-input-error for="areaImageTemp" class="mt-2" />

                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    @lang('modules.table.areaImageUploadHelp')
                </p>

                @if ($areaImageTemp)
                    <div class="mt-2">
                        <div class="relative inline-block">
                            <img src="{{ $areaImageTemp->temporaryUrl() }}" alt="{{ __('modules.table.areaImagePreview') }}" class="w-32 h-32 object-cover rounded-lg border border-gray-300">
                            <button type="button" wire:click="removeSelectedImage" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="flex w-full pb-4 space-x-4 mt-6 rtl:space-x-reverse">
            <x-button>@lang('app.save')</x-button>
            <button type="button" @click="$dispatch('close-add-area-modal')"
                class="inline-flex justify-center text-gray-500 items-center bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-3 py-2 hover:text-gray-900 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                @lang('app.cancel')
            </button>
        </div>
    </form>
</div>
