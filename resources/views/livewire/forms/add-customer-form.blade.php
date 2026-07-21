<div @close-add-customer-modal.window="$wire.resetModalState()" class="flex flex-col flex-1 min-h-0">
    <form wire:submit="submitForm" class="flex flex-col flex-1 min-h-0">
        @csrf
        <div class="flex-1 min-h-0 overflow-y-auto overflow-x-hidden space-y-4 pb-4">

            <div>
                <x-label for="customerName" value="{{ __('modules.customer.name') }}" />
                <x-input id="customerName" class="block mt-1 w-full" type="text" autofocus wire:model='customerName' placeholder="{{ __('placeholders.customerName') }}" />
                <x-input-error for="customerName" class="mt-2" />
            </div>

            <div>
                <x-label for="customerEmail" value="{{ __('modules.customer.email') }}" />
                <x-input id="customerEmail" class="block mt-1 w-full" type="email" autofocus wire:model='customerEmail' placeholder="{{ __('placeholders.customerEmail') }}" />
                <x-input-error for="customerEmail" class="mt-2" />
            </div>

            <div>
                <x-label for="customerPhone" value="{{ __('modules.customer.phone') }}" />
                <div class="flex gap-2 mt-2">
                    <!-- Phone Code Dropdown -->
                    <div class="relative w-32" wire:key="add-customer-phone-code-dropdown" @click.away="$wire.set('phoneCodeIsOpen', false)">
                        <button
                            type="button"
                            wire:click="$toggle('phoneCodeIsOpen')"
                            class="w-full p-2 bg-gray-100 border rounded cursor-pointer dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-gray-600 dark:focus:ring-gray-600">
                            <div class="flex items-center justify-between">
                                <span class="text-sm">
                                    @if($customerPhoneCode)
                                        +{{ $customerPhoneCode }}
                                    @else
                                        {{ __('modules.settings.select') }}
                                    @endif
                                </span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </button>

                        <!-- Search Input and Options -->
                        @if ($phoneCodeIsOpen)
                        <ul
                            x-transition
                            class="absolute z-10 w-full mt-1 overflow-auto bg-white rounded-lg shadow-lg max-h-60 ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-gray-600 dark:focus:ring-gray-600">
                            <li class="sticky top-0 px-3 py-2 bg-white dark:bg-gray-900 z-10">
                                <x-input wire:model.live.debounce.300ms="phoneCodeSearch" class="block w-full" type="text" placeholder="{{ __('placeholders.search') }}" />
                            </li>
                            @forelse ($phonecodes ?? [] as $phonecode)
                                <li @click="$wire.selectPhoneCode('{{ $phonecode }}')"
                                    wire:key="phone-code-{{ $phonecode }}"
                                    class="relative py-2 pl-3 text-gray-900 transition-colors duration-150 cursor-pointer select-none pr-9 hover:bg-gray-100 dark:border-gray-700 dark:hover:bg-gray-800 dark:text-gray-300 dark:focus:border-gray-600 dark:focus:ring-gray-600"
                                    :class="{ 'bg-gray-100 dark:bg-gray-800': '{{ $phonecode }}' === '{{ $customerPhoneCode }}' }" role="option">
                                    <div class="flex items-center">
                                        <span class="block ml-3 text-sm whitespace-nowrap">+{{ $phonecode }}</span>
                                        <span x-show="'{{ $phonecode }}' === '{{ $customerPhoneCode }}'" class="absolute inset-y-0 right-0 flex items-center pr-4 text-black dark:text-gray-300" x-cloak>
                                            <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    </div>
                                </li>
                            @empty
                                <li class="relative py-2 pl-3 text-gray-500 cursor-default select-none pr-9 dark:text-gray-400">
                                    {{ __('modules.settings.noPhoneCodesFound') }}
                                </li>
                            @endforelse
                        </ul>
                        @endif
                    </div>

                    <!-- Phone Number Input -->
                    <x-input id="customerPhone" class="block w-full" type="tel" name="customerPhone" wire:model='customerPhone' required />
                </div>
                <x-input-error for="customerPhoneCode" class="mt-2" />
                <x-input-error for="customerPhone" class="mt-2" />
            </div>

            <div>
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <x-label value="{{ __('modules.customer.address') }}" class="!mb-0" />
                    <div class="flex flex-wrap items-center gap-2 shrink-0">
                        <button type="button" wire:click="addAddressTab"
                            class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                            @lang('modules.delivery.addNewAddress')
                        </button>
                    </div>
                </div>

                <div class="mt-2 flex gap-2 overflow-x-auto pb-1 border-b border-gray-200 dark:border-gray-600 scrollbar-thin" role="tablist">
                    @foreach ($addresses as $idx => $row)
                        <div
                            wire:key="add-addr-tab-{{ $idx }}-{{ $row['id'] ?? 'new' }}"
                            role="tab"
                            aria-selected="{{ $activeAddressTab === $idx ? 'true' : 'false' }}"
                            @class([
                                'flex items-stretch shrink-0 min-w-0 max-w-[14rem] rounded-t-lg border transition-colors overflow-hidden',
                                'bg-white dark:bg-gray-800 text-skin-base border-gray-200 dark:border-gray-600 border-b-white dark:border-b-gray-800 -mb-px z-[1] shadow-sm' => $activeAddressTab === $idx,
                                'bg-gray-50 dark:bg-gray-900/80 text-gray-600 dark:text-gray-400 border-gray-200/80 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800' => $activeAddressTab !== $idx,
                            ])>
                            <button type="button"
                                wire:click="selectAddressTab({{ $idx }})"
                                class="flex-1 min-w-0 px-3 py-2 text-xs font-medium text-left whitespace-nowrap truncate">
                                @if (filled($row['label'] ?? null))
                                    {{ \Illuminate\Support\Str::limit($row['label'], 18) }}
                                @else
                                    @lang('modules.delivery.addressLabel') #{{ $idx + 1 }}
                                @endif
                            </button>
                            @if (count($addresses) > 1)
                                <button type="button"
                                    wire:click.stop="requestRemoveAddressTab({{ $idx }})"
                                    class="shrink-0 flex items-center justify-center px-2 border-l border-gray-200 dark:border-gray-600 text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/25 dark:hover:text-red-400 transition-colors"
                                    title="@lang('app.delete')">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="mt-3 space-y-3 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50/50 dark:bg-gray-900/20 p-3">
                    <div>
                        <x-label for="addAddressLabel" value="{{ __('modules.delivery.addressLabel') }}" class="!mb-0.5" />
                        <x-input id="addAddressLabel" type="text" class="block mt-1 w-full" wire:model.live="addressLabel" placeholder="{{ __('placeholders.addressLabelPlaceholder') }}" />
                        <x-input-error for="addresses.{{ $activeAddressTab }}.label" class="mt-1" />
                    </div>

                    <div>
                        <x-label value="{{ __('modules.delivery.fullAddress') }}" class="!mb-0.5" />
                        <div id="customer-add-place-autocomplete-card" class="mb-2 border dark:border-gray-500 rounded-lg p-1 relative z-[1200] bg-white dark:bg-gray-900" wire:ignore>
                            <div class="relative">
                                <input id="customer-add-location-search-input" type="text"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                                    placeholder="{{ __('placeholders.search') }}"
                                    autocomplete="off" />
                                <div id="customer-add-location-search-results" class="absolute z-[1300] mt-1 hidden w-full max-h-60 overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-600 dark:bg-gray-800"></div>
                            </div>
                        </div>
                        <section id="customer-add-address-map" class="relative z-0 h-40 sm:h-44 min-h-[10rem] w-full rounded-lg shadow-md border border-gray-200 dark:border-gray-600 mb-2 bg-white dark:bg-gray-900" wire:ignore></section>
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mb-2 text-sm text-gray-600 dark:text-gray-400">
                            <span>
                                <span class="font-medium text-gray-700 dark:text-gray-300">{{ __('modules.delivery.latitude') }}:</span>
                                {{ filled($customerLat) || $customerLat === 0 || $customerLat === '0' ? $customerLat : '—' }}
                            </span>
                            <span>
                                <span class="font-medium text-gray-700 dark:text-gray-300">{{ __('modules.delivery.longitude') }}:</span>
                                {{ filled($customerLng) || $customerLng === 0 || $customerLng === '0' ? $customerLng : '—' }}
                            </span>
                        </div>
                        <x-textarea id="customerAddress" wire:model.live="customerAddress" rows="3" class="block mt-1 w-full min-h-[4.5rem] max-h-36 resize-y dark:bg-gray-900" data-gramm="false" placeholder="{{ __('placeholders.customerAddress') }}" />
                        <x-input-error for="addresses.{{ $activeAddressTab }}.address" class="mt-2" />
                        <x-input-error for="addresses.{{ $activeAddressTab }}.lat" class="mt-2" />
                        <x-input-error for="addresses.{{ $activeAddressTab }}.lng" class="mt-2" />
                    </div>
                </div>
            </div>

        </div>

        <div class="shrink-0 flex flex-wrap items-center gap-3 w-full pt-3 pb-4 sm:pb-5 mt-auto border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 -mx-4 px-4 sm:-mx-6 sm:px-6 z-10">
            <x-button type="submit">@lang('app.save')</x-button>
            <x-button-cancel type="button" @click="window.dispatchEvent(new CustomEvent('close-add-customer-modal'))">@lang('app.cancel')</x-button-cancel>
        </div>
    </form>

    @script
    <script>
        const ADD_MAP_API_KEY = atob('{{ base64_encode($mapApiKey ?? '') }}');
        const ADD_MAP_PROVIDER = '{{ $mapProvider ?? 'google' }}';
        const ADD_BRANCH_LAT = {{ is_numeric($branchLat ?? null) ? (float) $branchLat : 26.9124336 }};
        const ADD_BRANCH_LNG = {{ is_numeric($branchLng ?? null) ? (float) $branchLng : 75.7872719 }};

        const ADD_STRINGS = {
            deliveryLocation: "@lang('modules.delivery.deliveryLocation')",
            useCurrentLocation: "@lang('modules.delivery.useCurrentLocation')",
        };

        let addAddressMap = null, addAddressMarker = null;
        let addLeafletMap = null, addLeafletMarker = null;
        let addSearchDebounce = null;
        let addMapAssetsLoaded = false;
        let addMapUseOsm = ADD_MAP_PROVIDER === 'osm' || !ADD_MAP_API_KEY;

        function destroyCustomerAddMap() {
            if (addLeafletMap) {
                addLeafletMap.remove();
                addLeafletMap = null;
                addLeafletMarker = null;
            }
            const el = document.getElementById('customer-add-address-map');
            if (el) {
                el.innerHTML = '';
            }
            addAddressMap = null;
            addAddressMarker = null;
        }

        window.destroyCustomerAddMap = destroyCustomerAddMap;

        function whenMapElReady(callback, attempts = 0) {
            const el = document.getElementById('customer-add-address-map');
            if (el && el.offsetWidth > 0 && el.offsetHeight > 0) {
                callback();
                return;
            }
            if (attempts > 60) {
                callback();
                return;
            }
            requestAnimationFrame(() => whenMapElReady(callback, attempts + 1));
        }

        function resolveAddMapCenter() {
            const rawLat = @this.get('customerLat');
            const rawLng = @this.get('customerLng');
            const lat = parseFloat(rawLat);
            const lng = parseFloat(rawLng);
            if (Number.isFinite(lat) && Number.isFinite(lng)) {
                return { lat, lng };
            }
            return { lat: ADD_BRANCH_LAT, lng: ADD_BRANCH_LNG };
        }

        function initCustomerAddAddressMap() {
            const el = document.getElementById('customer-add-address-map');
            if (!el) return;
            const { lat, lng } = resolveAddMapCenter();
            if (!Number.isFinite(parseFloat(@this.get('customerLat')))) {
                @this.set('customerLat', lat);
                @this.set('customerLng', lng);
            }
            destroyCustomerAddMap();
            setupCustomerAddMap([{ lat, lng }]);
        }

        async function bootstrapCustomerAddMapAssets() {
            if (addMapAssetsLoaded) {
                return;
            }
            if (ADD_MAP_PROVIDER === 'osm') {
                addMapUseOsm = true;
                await loadAddLeafletAssets();
            } else {
                try {
                    await loadAddGoogleMaps();
                    addMapUseOsm = false;
                } catch (e) {
                    addMapUseOsm = true;
                    await loadAddLeafletAssets();
                }
            }
            addMapAssetsLoaded = true;
        }

        async function refreshCustomerAddMapOnOpen() {
            const el = document.getElementById('customer-add-address-map');
            if (!el) return;

            await bootstrapCustomerAddMapAssets();
            initCustomerAddAddressMap();
            ensureCustomerAddSearchWired();

            setTimeout(() => {
                const { lat, lng } = resolveAddMapCenter();
                if (addLeafletMap) {
                    addLeafletMap.invalidateSize(true);
                    addLeafletMap.setView([lat, lng], addLeafletMap.getZoom());
                    if (addLeafletMarker) {
                        addLeafletMarker.setLatLng([lat, lng]);
                    }
                }
                if (addAddressMap && window.google?.maps) {
                    google.maps.event.trigger(addAddressMap, 'resize');
                    addAddressMap.setCenter({ lat, lng });
                    if (addAddressMarker) {
                        addAddressMarker.position = { lat, lng };
                    }
                }
            }, 350);
        }

        window.refreshCustomerAddMapOnOpen = () => whenMapElReady(() => refreshCustomerAddMapOnOpen());

        function loadAddGoogleMaps() {
            return new Promise((resolve, reject) => {
                if (window.google && window.google.maps) {
                    resolve();
                    return;
                }
                window.initCustomerAddAddressMapCallback = () => resolve();
                const script = document.createElement('script');
                script.src = ADD_MAP_API_KEY
                    ? `https://maps.googleapis.com/maps/api/js?key=${ADD_MAP_API_KEY}&loading=async&libraries=places,geocoding,marker&callback=initCustomerAddAddressMapCallback`
                    : `https://maps.googleapis.com/maps/api/js?libraries=places,geocoding,marker&callback=initCustomerAddAddressMapCallback`;
                script.async = true;
                script.onerror = reject;
                document.head.appendChild(script);
            });
        }

        function loadAddLeafletAssets() {
            return new Promise((resolve) => {
                if (window.L) {
                    resolve();
                    return;
                }
                if (!document.querySelector('link[data-map-provider="leaflet"]')) {
                    const leafletCss = document.createElement('link');
                    leafletCss.rel = 'stylesheet';
                    leafletCss.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                    leafletCss.setAttribute('data-map-provider', 'leaflet');
                    document.head.appendChild(leafletCss);
                }
                const script = document.createElement('script');
                script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                script.onload = () => resolve();
                document.head.appendChild(script);
            });
        }

        function setupCustomerAddMap(params = []) {
            const el = document.getElementById('customer-add-address-map');
            if (!el) return;

            let { lat = ADD_BRANCH_LAT, lng = ADD_BRANCH_LNG } = (params[0] && Object.keys(params[0]).length) ? params[0] : {};
            lat = parseFloat(lat);
            lng = parseFloat(lng);
            if (!Number.isFinite(lat)) lat = ADD_BRANCH_LAT;
            if (!Number.isFinite(lng)) lng = ADD_BRANCH_LNG;

            if (addMapUseOsm) {
                setupCustomerAddLeaflet(lat, lng);
                return;
            }

            try {
                if (!window.google?.maps) {
                    throw new Error('Google Maps unavailable');
                }

                addAddressMap = new google.maps.Map(el, {
                    center: { lat, lng },
                    zoom: 15,
                    gestureHandling: 'greedy',
                    zoomControl: false,
                    streetViewControl: false,
                    mapId: 'DEMO_MAP_ID',
                });

                addAddressMarker = new google.maps.marker.AdvancedMarkerElement({
                    position: { lat, lng },
                    map: addAddressMap,
                    gmpDraggable: true,
                    title: ADD_STRINGS.deliveryLocation
                });

                google.maps.event.addListener(addAddressMarker, 'dragend', (e) => {
                    updateCustomerAddLatLng(e.latLng.lat(), e.latLng.lng());
                    reverseGeocodeCustomerAdd(e.latLng);
                });

                google.maps.event.addListener(addAddressMap, 'click', (e) => {
                    addAddressMarker.position = { lat: e.latLng.lat(), lng: e.latLng.lng() };
                    updateCustomerAddLatLng(e.latLng.lat(), e.latLng.lng());
                    reverseGeocodeCustomerAdd(e.latLng);
                });

                setTimeout(() => {
                    if (addAddressMap) {
                        google.maps.event.trigger(addAddressMap, 'resize');
                        addAddressMap.setCenter({ lat, lng });
                    }
                }, 100);

                ensureCustomerAddSearchWired();
                addCustomerAddCurrentLocationButton();
            } catch (e) {
                addMapUseOsm = true;
                loadAddLeafletAssets().then(() => setupCustomerAddLeaflet(lat, lng));
            }
        }

        window.addEventListener('customer-add-map-rebuild', () => {
            window.refreshCustomerAddMapOnOpen?.();
        });

        function setupCustomerAddLeaflet(lat, lng) {
            if (!window.L) return;

            const el = document.getElementById('customer-add-address-map');
            if (!el) return;

            addLeafletMap = L.map(el, { preferCanvas: true }).setView([lat, lng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
                maxZoom: 19,
            }).addTo(addLeafletMap);

            addLeafletMarker = L.marker([lat, lng], {
                draggable: true,
                title: ADD_STRINGS.deliveryLocation
            }).addTo(addLeafletMap);

            addLeafletMarker.on('dragend', (event) => {
                const position = event.target.getLatLng();
                updateCustomerAddLatLng(position.lat, position.lng, true);
            });

            addLeafletMap.on('click', (event) => {
                addLeafletMarker.setLatLng([event.latlng.lat, event.latlng.lng]);
                updateCustomerAddLatLng(event.latlng.lat, event.latlng.lng, true);
            });

            setTimeout(() => addLeafletMap?.invalidateSize(true), 100);
            setTimeout(() => addLeafletMap?.invalidateSize(true), 400);

            ensureCustomerAddSearchWired();
            addCustomerAddCurrentLocationButtonLeaflet();
        }

        whenMapElReady(() => refreshCustomerAddMapOnOpen());

        function addCustomerAddCurrentLocationButton() {
            if (!addAddressMap) return;
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'bg-white p-2 rounded-lg shadow-md m-3';
            button.title = ADD_STRINGS.useCurrentLocation;
            button.innerHTML = `<svg class="w-5 h-5 text-current" width="20" height="20" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M13 4.069V2h-2v2.069A8.01 8.01 0 0 0 4.069 11H2v2h2.069A8.01 8.01 0 0 0 11 19.931V22h2v-2.069A8.01 8.01 0 0 0 19.931 13H22v-2h-2.069A8.01 8.01 0 0 0 13 4.069M12 18c-3.309 0-6-2.691-6-6s2.691-6 6-6 6 2.691 6 6-2.691 6-6 6"/></svg>`;
            button.addEventListener('click', () => {
                if (!navigator.geolocation) return;
                navigator.geolocation.getCurrentPosition(
                    ({ coords: { latitude, longitude } }) => {
                        if (addAddressMarker) addAddressMarker.position = { lat: latitude, lng: longitude };
                        addAddressMap.setCenter({ lat: latitude, lng: longitude });
                        updateCustomerAddLatLng(latitude, longitude);
                        reverseGeocodeCustomerAdd({ lat: latitude, lng: longitude });
                    },
                    () => {},
                    { timeout: 10000, enableHighAccuracy: true }
                );
            });
            addAddressMap.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(button);
        }

        function addCustomerAddCurrentLocationButtonLeaflet() {
            if (!addLeafletMap) return;
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'bg-white p-2 rounded-lg shadow-md';
            button.title = ADD_STRINGS.useCurrentLocation;
            button.innerHTML = `<svg class="w-5 h-5 text-current" width="20" height="20" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M13 4.069V2h-2v2.069A8.01 8.01 0 0 0 4.069 11H2v2h2.069A8.01 8.01 0 0 0 11 19.931V22h2v-2.069A8.01 8.01 0 0 0 19.931 13H22v-2h-2.069A8.01 8.01 0 0 0 13 4.069M12 18c-3.309 0-6-2.691-6-6s2.691-6 6-6 6 2.691 6 6-2.691 6-6 6"/></svg>`;
            button.addEventListener('click', () => {
                if (!navigator.geolocation) return;
                navigator.geolocation.getCurrentPosition(
                    ({ coords: { latitude, longitude } }) => {
                        addLeafletMarker.setLatLng([latitude, longitude]);
                        addLeafletMap.setView([latitude, longitude], addLeafletMap.getZoom());
                        updateCustomerAddLatLng(latitude, longitude, true);
                    },
                    () => {},
                    { timeout: 10000, enableHighAccuracy: true }
                );
            });
            const c = L.control({ position: 'bottomright' });
            c.onAdd = () => button;
            c.addTo(addLeafletMap);
        }

        async function updateCustomerAddLatLng(lat, lng, shouldReverseGeocode = false) {
            if (lat == null || lng == null) return;
            if (addMapUseOsm && addLeafletMap && addLeafletMarker) {
                addLeafletMarker.setLatLng([lat, lng]);
                addLeafletMap.setView([lat, lng], addLeafletMap.getZoom());
            } else if (addAddressMarker && addAddressMap) {
                addAddressMarker.position = { lat, lng };
                addAddressMap.setCenter({ lat, lng });
            }
            @this.set('customerLat', lat);
            @this.set('customerLng', lng);

            if (addMapUseOsm && shouldReverseGeocode) {
                const formatted = await reverseGeocodeOsmCustomerAdd(lat, lng);
                if (formatted) @this.set('customerAddress', formatted);
            }
        }

        function ensureCustomerAddSearchWired() {
            const input = document.getElementById('customer-add-location-search-input');
            if (!input || input.dataset.addSearchWired === '1') {
                return;
            }
            if (addMapUseOsm) {
                input.dataset.addSearchWired = '1';
                addCustomerAddOsmAutocomplete(input);
                return;
            }
            if (window.google && window.google.maps && google.maps.places) {
                input.dataset.addSearchWired = '1';
                addCustomerAddGoogleAutocomplete(input);
            }
        }

        function addCustomerAddGoogleAutocomplete(inputElement) {
            if (!inputElement || !window.google || !google.maps) return;
            const ac = new google.maps.places.Autocomplete(inputElement, {
                fields: ['geometry', 'formatted_address']
            });
            ac.addListener('place_changed', () => {
                const place = ac.getPlace();
                const location = place?.geometry?.location;
                const formattedAddress = place?.formatted_address;
                if (!location) return;
                inputElement.value = formattedAddress || '';
                @this.set('customerAddress', formattedAddress || '');
                const la = location.lat();
                const ln = location.lng();
                if (addAddressMarker) addAddressMarker.position = { lat: la, lng: ln };
                if (addAddressMap) addAddressMap.setCenter({ lat: la, lng: ln });
                updateCustomerAddLatLng(la, ln);
            });
        }

        function addCustomerAddOsmAutocomplete(inputElement) {
            if (!inputElement) return;
            const resultBox = document.getElementById('customer-add-location-search-results');
            if (!resultBox) return;
            inputElement.addEventListener('input', (event) => {
                const query = event.target.value?.trim();
                clearTimeout(addSearchDebounce);
                if (!query || query.length < 3) {
                    resultBox.classList.add('hidden');
                    resultBox.innerHTML = '';
                    return;
                }
                addSearchDebounce = setTimeout(async () => {
                    resultBox.classList.remove('hidden');
                    resultBox.innerHTML = `<div class="px-3 py-2 text-sm text-gray-500 dark:text-gray-300">Searching...</div>`;
                    const results = await searchOsmCustomerAdd(query);
                    renderOsmResultsCustomerAdd(results, resultBox, inputElement);
                }, 400);
            });
        }

        function renderOsmResultsCustomerAdd(results, resultBox, inputElement) {
            if (!Array.isArray(results) || results.length === 0) {
                resultBox.classList.remove('hidden');
                resultBox.innerHTML = `<div class="px-3 py-2 text-sm text-gray-500 dark:text-gray-300">No locations found.</div>`;
                return;
            }
            resultBox.innerHTML = results.map((item) => `
                <button type="button" class="block w-full border-b border-gray-100 px-3 py-2 text-left text-sm hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700" data-lat="${item.lat}" data-lng="${item.lon}" data-label="${encodeURIComponent(item.display_name)}">
                    ${item.display_name.replace(/</g, '&lt;')}
                </button>
            `).join('');
            resultBox.classList.remove('hidden');
            resultBox.querySelectorAll('button').forEach((button) => {
                button.addEventListener('click', () => {
                    const la = parseFloat(button.dataset.lat);
                    const ln = parseFloat(button.dataset.lng);
                    const label = decodeURIComponent(button.dataset.label || '');
                    inputElement.value = label;
                    @this.set('customerAddress', label);
                    if (addLeafletMarker && addLeafletMap) {
                        addLeafletMarker.setLatLng([la, ln]);
                        addLeafletMap.setView([la, ln], addLeafletMap.getZoom());
                    }
                    updateCustomerAddLatLng(la, ln);
                    resultBox.classList.add('hidden');
                    resultBox.innerHTML = '';
                });
            });
        }

        async function searchOsmCustomerAdd(query) {
            const normalizedQuery = query.replace(/\s+/g, ' ').trim();
            const encodedQuery = encodeURIComponent(normalizedQuery);
            let centerLat = NaN, centerLng = NaN;
            if (addLeafletMap) {
                const c = addLeafletMap.getCenter();
                centerLat = c.lat;
                centerLng = c.lng;
            }
            const latParam = Number.isFinite(centerLat) ? `&lat=${centerLat}` : '';
            const lonParam = Number.isFinite(centerLng) ? `&lon=${centerLng}` : '';
            const nominatimUrl = `https://nominatim.openstreetmap.org/search?q=${encodedQuery}&format=json&addressdetails=1&limit=8&accept-language=en${latParam}${lonParam}`;
            try {
                const res = await fetch(nominatimUrl);
                if (res.ok) {
                    const data = await res.json();
                    if (Array.isArray(data) && data.length > 0) {
                        return data.map((item) => ({
                            lat: item.lat,
                            lon: item.lon,
                            display_name: item.display_name
                        }));
                    }
                }
            } catch (e) {
                console.error(e);
            }
            return [];
        }

        function reverseGeocodeCustomerAdd(latLng) {
            const geocoder = new google.maps.Geocoder();
            geocoder.geocode({ location: latLng }, (results, status) => {
                if (status === google.maps.GeocoderStatus.OK && results[0]) {
                    @this.set('customerAddress', results[0].formatted_address);
                }
            });
        }

        async function reverseGeocodeOsmCustomerAdd(lat, lng) {
            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`);
                const data = await response.json();
                return data?.display_name || null;
            } catch (e) {
                return null;
            }
        }
    </script>
    @endscript
</div>
