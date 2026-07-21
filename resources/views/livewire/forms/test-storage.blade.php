<div>
    <form wire:submit="submitForm">
        @csrf
        <div class="space-y-4">

            @if ($fileUrl)
                <x-alert type="success">
                    <div class="space-y-3">
                        <p class="font-medium text-gray-900 dark:text-white">
                            @lang('modules.settings.testStorageUploadSuccess')
                        </p>

                        <dl class="grid grid-cols-1 gap-2 text-sm text-gray-700 dark:text-gray-300">
                            <div class="flex flex-wrap gap-x-2 gap-y-1">
                                <dt class="font-medium text-gray-500 dark:text-gray-400 shrink-0">
                                    @lang('modules.settings.testStorageUploadedTo'):
                                </dt>
                                <dd class="font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $this->storageProviderLabel() }}
                                    @if ($isCloudStorage)
                                        <span class="font-normal text-gray-500 dark:text-gray-400">
                                            (@lang('modules.settings.testStorageCloudDisk'))
                                        </span>
                                    @else
                                        <span class="font-normal text-gray-500 dark:text-gray-400">
                                            (@lang('modules.settings.testStorageLocalDisk'))
                                        </span>
                                    @endif
                                </dd>
                            </div>

                            @if ($uploadedFilename)
                                <div class="flex flex-wrap gap-x-2 gap-y-1">
                                    <dt class="font-medium text-gray-500 dark:text-gray-400 shrink-0">
                                        @lang('modules.settings.testStorageOriginalFile'):
                                    </dt>
                                    <dd>{{ $uploadedFilename }}</dd>
                                </div>
                            @endif

                            @if ($uploadedObjectKey)
                                <div class="flex flex-wrap gap-x-2 gap-y-1">
                                    <dt class="font-medium text-gray-500 dark:text-gray-400 shrink-0">
                                        @lang('modules.settings.testStorageObjectKey'):
                                    </dt>
                                    <dd class="break-all font-mono text-xs">{{ $uploadedObjectKey }}</dd>
                                </div>
                            @endif

                            @if ($isCloudStorage && $storageBucket)
                                <div class="flex flex-wrap gap-x-2 gap-y-1">
                                    <dt class="font-medium text-gray-500 dark:text-gray-400 shrink-0">
                                        @lang('modules.settings.testStorageBucket'):
                                    </dt>
                                    <dd class="break-all">{{ $storageBucket }}</dd>
                                </div>
                            @endif

                            @if ($isCloudStorage && $storageRegion)
                                <div class="flex flex-wrap gap-x-2 gap-y-1">
                                    <dt class="font-medium text-gray-500 dark:text-gray-400 shrink-0">
                                        @lang('modules.settings.testStorageRegion'):
                                    </dt>
                                    <dd>{{ $storageRegion }}</dd>
                                </div>
                            @endif

                            @if ($isCloudStorage && $storageEndpoint)
                                <div class="flex flex-wrap gap-x-2 gap-y-1">
                                    <dt class="font-medium text-gray-500 dark:text-gray-400 shrink-0">
                                        @lang('modules.settings.testStorageEndpoint'):
                                    </dt>
                                    <dd class="break-all text-xs">{{ $storageEndpoint }}</dd>
                                </div>
                            @endif

                        </dl>

                        <div class="pt-1">
                            <a href="{{ $fileUrl }}" target="_blank" rel="noopener noreferrer"
                                class="inline-flex items-center text-sm font-medium text-skin-base hover:underline">
                                @lang('app.view') @lang('app.file')
                                <svg class="w-3.5 h-3.5 ltr:ml-1 rtl:mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </x-alert>
            @endif

            @error('file_error')
                <x-alert type="danger">
                    <div>
                        {!! $message !!}
                    </div>
                </x-alert>
            @enderror

            <div>
                <x-label for="test-storage-file" value="{{ __('modules.settings.testStorageFile') }}" />

                <input
                    id="test-storage-file"
                    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 mt-1 file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-gray-800 file:text-white hover:file:bg-gray-700 dark:file:bg-gray-600 dark:hover:file:bg-gray-500"
                    type="file"
                    wire:model="file">

                <x-input-error for="file" class="mt-2" />

                @if ($file && is_object($file))
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        {{ $file->getClientOriginalName() }}
                    </p>
                @endif

                <div wire:loading wire:target="file" class="mt-2">
                    <div class="flex items-center space-x-2 text-sm text-blue-600 dark:text-blue-400">
                        <svg class="animate-spin h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>@lang('app.uploading')...</span>
                    </div>
                </div>
            </div>

        </div>

        <div class="flex w-full pb-4 space-x-4 mt-6 rtl:space-x-reverse">
            <x-button target="file,submitForm">
                <span wire:loading.remove wire:target="file,submitForm">@lang('app.save')</span>
                <span wire:loading wire:target="file">@lang('app.uploading')...</span>
                <span wire:loading wire:target="submitForm">@lang('app.saving')...</span>
            </x-button>
            <x-button-cancel wire:click="$dispatch('hideTestStorageModal')" wire:loading.attr="disabled" wire:target="file,submitForm">
                @lang('app.cancel')
            </x-button-cancel>
        </div>
    </form>
</div>
