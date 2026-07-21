@props([
    'iosModel',
    'androidModel',
    'iosLabel',
    'androidLabel',
])

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div class="p-4 border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700/40">
        <div class="flex items-center mb-3">
            <svg class="w-6 h-6 shrink-0 text-gray-900 dark:text-gray-100" fill="currentColor" viewBox="0 0 24 24"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/></svg>
            <h5 class="ml-1 text-base font-semibold text-gray-900 dark:text-white">{{ $iosLabel }}</h5>
        </div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">@lang('superadmin.downloadUrl')</label>
        <div class="relative">
            <input type="url" wire:model.defer="{{ $iosModel }}"
                   class="block w-full px-3 py-2 pr-10 border border-gray-300 rounded text-sm text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-500 dark:text-white dark:placeholder-gray-400"
                   placeholder="https://apps.apple.com/...">
            @if(!empty($this->{$iosModel}))
                <button type="button" wire:click="$set('{{ $iosModel }}', '')"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            @endif
        </div>
        @error($iosModel)
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
        @if(!empty($this->{$iosModel}))
            <a href="{{ $this->{$iosModel} }}" target="_blank" class="inline-flex items-center mt-3 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                @lang('superadmin.downloadNow')
            </a>
        @endif
    </div>

    <div class="p-4 border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700/40">
        <div class="flex items-center mb-3">
            <svg class="w-6 h-6 shrink-0 text-gray-900 dark:text-gray-100" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M3.609 1.814L13.792 12 3.61 22.186a.996.996 0 01-.61-.92V2.734a1 1 0 01.609-.92zm10.89 10.893l2.302 2.302-10.937 6.333 8.635-8.635zm3.199-3.198l2.807 1.626a1 1 0 010 1.73l-2.808 1.626L12.001 12l5.697-5.695zM5.864 2.658L16.802 8.99l-2.302 2.302-8.636-8.634z"/>
            </svg>
            <h5 class="ml-1 text-base font-semibold text-gray-900 dark:text-white">{{ $androidLabel }}</h5>
        </div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">@lang('superadmin.downloadUrl')</label>
        <div class="relative">
            <input type="url" wire:model.defer="{{ $androidModel }}"
                   class="block w-full px-3 py-2 pr-10 border border-gray-300 rounded text-sm text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-500 dark:text-white dark:placeholder-gray-400"
                   placeholder="https://play.google.com/store/...">
            @if(!empty($this->{$androidModel}))
                <button type="button" wire:click="$set('{{ $androidModel }}', '')"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            @endif
        </div>
        @error($androidModel)
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
        @if(!empty($this->{$androidModel}))
            <a href="{{ $this->{$androidModel} }}" target="_blank" class="inline-flex items-center mt-3 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                @lang('superadmin.downloadNow')
            </a>
        @endif
    </div>
</div>
