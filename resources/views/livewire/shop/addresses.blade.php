<div class="py-8 px-4 mx-auto lg:px-6">
    <div class="mx-auto">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                @lang('menu.myAddresses')
            </h2>
            @if(!$showAddressForm && $addresses->isNotEmpty() && $addresses->count() < \App\Livewire\Shop\Addresses::MAX_ADDRESSES)
            <x-button wire:click="createNewAddress" type="button" class="w-full justify-center sm:w-auto">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 inline-flex" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                @lang('app.addNew')
            </x-button>
            @endif
        </div>

        @if($showAddressForm)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                    {{ $editMode ? __('modules.delivery.editAddress') : __('modules.delivery.addNewAddress') }}
                </h3>

                <form wire:submit="saveAddress">
                    <div class="mb-4">
                        <x-label for="label" value="{{ __('modules.delivery.addressLabel') }}" />
                        <x-input id="label" wire:model.defer="label" placeholder="{{ __('placeholders.addressLabelPlaceholder') }}" class="w-full" />
                        <x-input-error for="label" />
                    </div>

                    <!-- Search Box -->
                    <div id="place-autocomplete-card" class="mb-2 border dark:border-gray-500 rounded-lg p-1 relative z-[1200]" wire:ignore>
                        <p id="location-search"> </p>
                    </div>

                    <div class="mb-4">
                        <section id="address-map" class="relative z-0 h-96 rounded-lg shadow-md border border-gray-200 mb-2" wire:ignore></section>
                        <x-input-error for="lat" custom-message="{{ __('modules.delivery.pleaseSelectLocation') }}" />
                        <div class="flex items-center gap-4 mt-2 text-sm text-gray-600 dark:text-gray-400">
                            <span>
                                <span class="font-medium text-gray-700 dark:text-gray-300">{{ __('modules.delivery.latitude') }}:</span>
                                {{ $lat ?? 'N/A' }}
                            </span>
                            <span>
                                <span class="font-medium text-gray-700 dark:text-gray-300">{{ __('modules.delivery.longitude') }}:</span>
                                {{ $lng ?? 'N/A' }}
                            </span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <x-label for="address" value="{{ __('modules.delivery.fullAddress') }}" />
                        <x-textarea id="address" wire:model.defer="address" rows="3" class="w-full" data-gramm="false" placeholder="{{ __('placeholders.addressPlaceholder') }}"></x-textarea>
                        <x-input-error for="address" />
                    </div>

                    <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                        <x-secondary-button wire:click="cancelForm" type="button" class="w-full justify-center sm:w-auto">
                            @lang('app.cancel')
                        </x-secondary-button>
                        <x-button type="submit" class="w-full justify-center sm:w-auto">
                            {{ $editMode ? __('modules.delivery.updateAddress') : __('modules.delivery.saveAddress') }}
                        </x-button>
                    </div>
                </form>
            </div>
        @endif

        @if($addresses->isEmpty() && !$showAddressForm)
            <div class="flex flex-col items-center justify-center p-8 text-center bg-white rounded-lg border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700">
                <div class="w-16 h-16 mb-4 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                    </svg>
                </div>
                <h3 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">@lang('modules.delivery.noAddressesFound')</h3>
                <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">@lang('modules.delivery.addAddressDescription')</p>
                <x-button wire:click="createNewAddress" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 inline-flex" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    @lang('app.addNew')
                </x-button>
            </div>
        @elseif(!$showAddressForm)
            <div class="grid gap-4">
                @foreach($addresses as $address)
                    <div class="p-4 bg-white rounded-lg border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700">
                        <div class="flex justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $address->label }}
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $address->address }}
                                </p>
                                <div class="mt-1 text-xs text-gray-400">
                                    Lat: {{ $address->lat }}, Lng: {{ $address->lng }}
                                </div>
                            </div>
                        <div class="ml-3 flex flex-shrink-0 space-x-2">
                                <button wire:click="editAddress({{ $address->id }})" class="text-blue-600 hover:text-blue-800 dark:text-blue-500 dark:hover:text-blue-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button wire:click="confirmDeleteAddress({{ $address->id }})" class="text-red-600 hover:text-red-800 dark:text-red-500 dark:hover:text-red-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <x-confirmation-modal wire:model.defer="confirmDeleteAddressModal">
        <x-slot name="title">
            @lang('modules.delivery.confirmDeleteAddress')
        </x-slot>

        <x-slot name="content">
            @lang('modules.delivery.confirmDeleteAddressDescription')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmDeleteAddressModal')" wire:loading.attr="disabled">
                {{ __('app.cancel') }}
            </x-secondary-button>

            @if ($confirmDeleteAddressId)
            <x-danger-button class="ml-3" wire:click='deleteAddress' wire:loading.attr="disabled">
                {{ __('app.delete') }}
            </x-danger-button>
            @endif
        </x-slot>
    </x-confirmation-modal>

    @include('livewire.shop.partials.address-map-picker-script', [
        'prefix' => 'address',
        'apiKey' => $mapApiKey,
        'provider' => $mapProvider ?? 'google',
        'event' => 'initAddressMap',
        'mapElementId' => 'address-map',
        'searchCardId' => 'place-autocomplete-card',
        'latField' => 'lat',
        'lngField' => 'lng',
        'addressField' => 'address',
        'defaultLat' => 26.9125,
        'defaultLng' => 75.7875,
    ])
</div>
