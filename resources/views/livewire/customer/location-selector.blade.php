<div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 rounded-xl p-2">
    <div class="mb-2">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">@lang('modules.delivery.selectDeliveryLocation')</h2>
    </div>


    @if($inRange && $isDeliveryTimeAvailable)
        <x-alert type="success" class="mt-4">
            <div class="flex items-center">
                @if($isFreeDelivery)
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 13 4 4L19 7"/></svg>
                    @lang('modules.delivery.freeDelivery')
                    @if(isset($freeDeliveryOverAmount) && $orderGrandTotal >= $freeDeliveryOverAmount)
                        (@lang('modules.delivery.orderQualifiesForFreeDelivery'))
                    @endif
                @else
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-14v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32 1.41 1.41M2 12h2m16 0h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
                    @lang('modules.delivery.deliveryFee'): <span class="font-semibold ml-1">{{ currency_format($deliveryFee, $currencyId) }}</span>
                    @if($distance)
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">
                            ({{ number_format($branch->deliverySetting->unit === 'miles' ? $distance / 1.60934 : $distance, 2) }} {{ $branch->deliverySetting->unit === 'miles' ? 'miles' : 'km' }})
                        </span>
                    @endif
                @endif
            </div>
        </x-alert>
    @elseif($deliveryMessage)
        <x-alert type="danger" class="mt-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3"/></svg>
                {{ $deliveryMessage ?? __('modules.delivery.locationOutOfRange') }}
            </div>
        </x-alert>
    @endif

    <!-- Saved/Manual Toggle -->
    <div class="flex justify-end mb-4">
        @if($customerAllAddresses?->isNotEmpty())
            <x-button
                wire:click="toggleManualLocation"
                @class([
                    'transition-colors duration-200',
                    'bg-blue-600' => !$showManualLocation,
                    'text-gray-700' => $showManualLocation,
                    'opacity-50' => !$isDeliveryTimeAvailable
                ]) :disabled="!$isDeliveryTimeAvailable">
                <svg class="w-5 h-5 mr-1 inline-flex" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    @if($showManualLocation)
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    @endif
                </svg>
                {{ $showManualLocation ? __('modules.delivery.useSavedAddress') : __('modules.delivery.useDifferentLocation') }}
            </x-button>
        @endif
    </div>

    <div class="space-y-6">
        <!-- Saved Addresses Section -->
        @if($customerAllAddresses?->isNotEmpty() && !$showManualLocation)
            <div class="grid gap-6 md:grid-cols-2">
                @foreach($customerAllAddresses as $address)
                    <div class="relative h-[120px]">
                        <div
                            wire:click="selectAddressFromSaved({{ $address->id }})"
                            class="h-full p-4 bg-white dark:bg-gray-700 rounded-xl border-2 cursor-pointer
                                hover:shadow-lg hover:border-blue-500 dark:hover:border-blue-400
                                transition-all duration-200 ease-in-out
                                {{ $selectedAddressId == $address->id ? 'border-blue-500 dark:border-blue-400 ring-4 ring-blue-500/20' : 'border-gray-200 dark:border-gray-600' }}"
                        >
                            <div class="flex h-full gap-3">
                                <div class="flex-shrink-0">
                                    <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center
                                        {{ $selectedAddressId == $address->id ? 'border-blue-500' : 'border-gray-300 dark:border-gray-500' }}">
                                        @if($selectedAddressId == $address->id)
                                            <div class="w-4 h-4 rounded-full bg-blue-500"></div>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex-1 flex flex-col space-y-3 min-w-0">
                                    <p class="font-semibold text-gray-900 dark:text-white truncate">{{ $address->label }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-3">
                                        {{ $address->address }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Manual Location Section -->
        @if(!$customerAllAddresses?->isNotEmpty() || $showManualLocation)
            <div class="space-y-6" wire:key='checkout-map-form'>
                <!-- Search Box -->
                <div id="place-autocomplete-card" wire:ignore>
                    <p id="location-search"> </p>
                </div>

                <!-- Map -->
                <div id="delivery-map" class="w-full h-[500px] rounded-xl shadow-md border border-gray-200 dark:border-gray-600" wire:ignore></div>

                <!-- Address Input -->
                <div class="space-y-1">
                    <x-textarea
                        id="delivery-address"
                        wire:model.defer="selectedAddress"
                        rows="3"
                        class="w-full resize-none"
                        placeholder="{{__('modules.delivery.fullAddressPlaceholder')}}">
                    </x-textarea>
                    <x-input-error for="selectedAddress" />
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-4 pt-2">
                    @if($customerAllAddresses?->isNotEmpty())
                        <x-secondary-button wire:click="$set('showManualLocation', false)">
                            @lang('app.cancel')
                        </x-secondary-button>
                    @endif
                    <x-button
                        wire:click="selectDeliveryAddress"
                        wire:loading.attr="disabled"
                        :disabled="!$inRange || !$isDeliveryTimeAvailable"
                        @class([
                            'px-6',
                            'opacity-50' => !$inRange || !$isDeliveryTimeAvailable
                        ])>
                        @lang('modules.delivery.confirmLocation')
                    </x-button>
                </div>
            </div>
        @endif
        <!-- Action Buttons for saved address -->
        @if($selectedAddressId)
        <div class="flex justify-end gap-4 pt-4">
            <x-button wire:click="confirmSelectedAddress" wire:loading.attr="disabled"
                :disabled="!$inRange || !$isDeliveryTimeAvailable"
                class="px-6 bg-green-600 hover:bg-green-700"
                @class(['bg-green-600 hover:bg-green-700',
                        'opacity-50' => !$inRange || !$isDeliveryTimeAvailable
                ])>
                @lang('modules.delivery.confirmLocation')
            </x-button>
        </div>
        @endif
    </div>

    @push('scripts')
    @script
    <script>
        const MAP_API_KEY = atob('{{ base64_encode($mapApiKey) }}');
        const MAP_PROVIDER = '{{ $mapProvider ?? 'google' }}';

        const STRINGS = {
            deliveryLocation: "@lang('modules.delivery.deliveryLocation')",
            shopLocation: "@lang('modules.delivery.shopLocation')",
            dragToAdjust: "@lang('modules.delivery.dragMarkerToAdjust')",
            showRange: "@lang('modules.delivery.showDeliveryRange')",
            hideRange: "@lang('modules.delivery.hideDeliveryRange')",
            useCurrentLocation: "@lang('modules.delivery.useCurrentLocation')",
            locationPermissionDenied: "@lang('modules.delivery.locationPermissionDenied')",
        };

        let deliveryMap, deliveryMarker, deliveryCircle;
        let leafletMap = null, leafletMarker = null, leafletBranchMarker = null, leafletCircle = null;
        let googleAutocompleteInstance = null;
        let searchDebounce = null;
        let mapInitialized = false;

        bootstrapMap();

        function bootstrapMap() {
            if (MAP_PROVIDER === 'osm') {
                loadLeafletAssets().then(() => initDeliveryMap());
                return;
            }

            loadGoogleMaps().then(() => initDeliveryMap()).catch(() => {
                loadLeafletAssets().then(() => initDeliveryMap());
            });
        }

        function loadGoogleMaps() {
            return new Promise((resolve, reject) => {
                if (window.google && google.maps) {
                    resolve();
                    return;
                }

                window.initDeliveryMap = () => resolve();
                const script = document.createElement('script');
                script.src = MAP_API_KEY
                    ? `https://maps.googleapis.com/maps/api/js?key=${MAP_API_KEY}&loading=async&libraries=places,geocoding,marker&callback=initDeliveryMap`
                    : `https://maps.googleapis.com/maps/api/js?&loading=async&libraries=places,geocoding,marker&callback=initDeliveryMap`;
                script.async = true;
                script.onerror = reject;
                document.head.appendChild(script);
            });
        }

        function loadLeafletAssets() {
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

        function initDeliveryMap() {
            Livewire.on('initDeliveryMap', (params) => {
                setTimeout(() => setupMap(params), 200);
            });
        }

        function setupMap(params = {}) {
            const el = document.getElementById('delivery-map');
            if (!el || (mapInitialized && !params)) return;
            mapInitialized = true;

            const {
                branchLat = @js($branchLat),
                branchLng = @js($branchLng),
                maxKm = @js($maxKm),
                defaultLat = @js($customerLat),
                defaultLng = @js($customerLng)
            } = params?.[0] || {};

            if (MAP_PROVIDER === 'osm') {
                setupLeafletMap(branchLat, branchLng, maxKm, defaultLat, defaultLng);
                return;
            }

            deliveryMap = new google.maps.Map(el, {
                center: { lat: defaultLat || branchLat, lng: defaultLng || branchLng },
                zoom: 15,
                gestureHandling: 'greedy',
                zoomControl: false,
                streetViewControl: false,
                mapId: 'DEMO_MAP_ID',
            });

            // Customer marker
            deliveryMarker = new google.maps.marker.AdvancedMarkerElement({
                position: { lat: defaultLat || branchLat, lng: defaultLng || branchLng },
                map: deliveryMap,
                gmpDraggable: true,
                title: STRINGS.deliveryLocation
            });

            // Branch marker
            const branchImg = document.createElement("div");
            branchImg.style.position = "relative";
            branchImg.style.width = "40px";
            branchImg.style.height = "50px";
            const markerSvg = `<svg viewBox="0 0 512 512" style="position:absolute;left:0;bottom:0">
                <path d="M256 0C150 0 64 86 64 192c0 133.1 174.9 307.7 181.6 314.4a16 16 0 0022.8 0C273.1 499.7 448 325.1 448 192 448 86 362 0 256 0z" fill="#f44336"/>
                <circle cx="256" cy="192" r="140" fill="#ffffff"/>
                <image href="{{ $restaurantLogo }}" x="136" y="72" width="240" height="240" clip-path="circle(120px at center)"/>
            </svg>`;

            branchImg.innerHTML = markerSvg;

            new google.maps.marker.AdvancedMarkerElement({
                position: { lat: branchLat, lng: branchLng },
                map: deliveryMap,
                content: branchImg,
                title: STRINGS.shopLocation
            });

            addGoogleAutocomplete(mountSearchInput());
            addMapEvents();
            drawDeliveryRange(branchLat, branchLng, maxKm);
            addCurrentLocationButton();
        }

        function setupLeafletMap(branchLat, branchLng, maxKm, defaultLat, defaultLng) {
            const centerLat = defaultLat || branchLat;
            const centerLng = defaultLng || branchLng;

            if (leafletMap) {
                leafletMap.remove();
                leafletMap = null;
            }

            leafletMap = L.map('delivery-map').setView([centerLat, centerLng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(leafletMap);

            leafletMarker = L.marker([centerLat, centerLng], { draggable: true, title: STRINGS.deliveryLocation }).addTo(leafletMap);
            leafletBranchMarker = L.marker([branchLat, branchLng], { title: STRINGS.shopLocation }).addTo(leafletMap);

            drawDeliveryRange(branchLat, branchLng, maxKm);
            addOsmAutocomplete(mountSearchInput());
            addCurrentLocationButton();

            leafletMarker.on('dragend', async (e) => {
                const p = e.target.getLatLng();
                await updateLocation({ lat: p.lat, lng: p.lng }, true);
            });

            leafletMap.on('click', async (e) => {
                await updateLocation({ lat: e.latlng.lat, lng: e.latlng.lng }, true);
            });
        }

        function drawDeliveryRange(lat, lng, maxKm) {
            const container = document.createElement('div');
            container.className = 'bg-white rounded-lg shadow-md p-1 m-2';

            const toggle = document.createElement('button');
            toggle.type = 'button';
            toggle.className = 'text-sm px-2 py-1 font-medium transition text-gray-700 hover:text-blue-600';
            toggle.innerText = STRINGS.showRange;

            let visible = false;

            toggle.addEventListener('click', () => {
                visible = !visible;
                if (MAP_PROVIDER === 'osm') {
                    if (!leafletCircle) {
                        leafletCircle = L.circle([lat, lng], {
                            radius: maxKm * 1000,
                            color: '#4CAF50',
                            weight: 2,
                            fillColor: '#4CAF50',
                            fillOpacity: 0.1
                        });
                    }
                    if (visible) {
                        leafletCircle.addTo(leafletMap);
                    } else {
                        leafletMap.removeLayer(leafletCircle);
                    }
                } else {
                    if (!deliveryCircle) {
                        deliveryCircle = new google.maps.Circle({
                            map: null,
                            center: { lat, lng },
                            radius: maxKm * 1000,
                            strokeColor: '#4CAF50',
                            strokeOpacity: 0.8,
                            strokeWeight: 2,
                            fillColor: '#4CAF50',
                            fillOpacity: 0.1,
                            clickable: false
                        });
                    }
                    deliveryCircle.setMap(visible ? deliveryMap : null);
                }
                toggle.innerText = visible ? STRINGS.hideRange : STRINGS.showRange;
            });

            container.appendChild(toggle);
            if (MAP_PROVIDER === 'osm' && leafletMap) {
                const customControl = L.control({ position: 'topright' });
                customControl.onAdd = () => container;
                customControl.addTo(leafletMap);
            } else if (deliveryMap) {
                deliveryMap.controls[google.maps.ControlPosition.TOP_RIGHT].push(container);
            }
        }

        function addCurrentLocationButton() {
            const button = document.createElement('button');
            button.className = 'bg-white p-2 rounded-lg shadow-md m-3';
            button.title = STRINGS.useCurrentLocation;

            const defaultSvg = `<svg class="w-5 h-5 text-current" width="20" height="20" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="3"/><path d="M13 4.069V2h-2v2.069A8.01 8.01 0 0 0 4.069 11H2v2h2.069A8.01 8.01 0 0 0 11 19.931V22h2v-2.069A8.01 8.01 0 0 0 19.931 13H22v-2h-2.069A8.01 8.01 0 0 0 13 4.069M12 18c-3.309 0-6-2.691-6-6s2.691-6 6-6 6 2.691 6 6-2.691 6-6 6"/></svg>`;
            const loadingSvg = `<svg class="animate-spin w-5 h-5 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4z"/></svg>`;

            button.innerHTML = defaultSvg;

            button.addEventListener('click', () => {
            if (!navigator.geolocation) return;

            button.innerHTML = loadingSvg;

            navigator.geolocation.getCurrentPosition(
                ({ coords: { latitude: lat, longitude: lng } }) => {
                const coords = { lat, lng };
                updateLocation(coords, true);
                button.innerHTML = defaultSvg;
                },
                (error) => {
                console.error('Geolocation error:', error);
                button.innerHTML = defaultSvg;
                },
                { timeout: 10000, enableHighAccuracy: true }
            );
            });

            if (MAP_PROVIDER === 'osm' && leafletMap) {
                const customControl = L.control({ position: 'bottomright' });
                customControl.onAdd = () => button;
                customControl.addTo(leafletMap);
            } else if (deliveryMap) {
                deliveryMap.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(button);
            }
        }

        function mountSearchInput() {
            const card = document.getElementById('place-autocomplete-card');
            if (!card) return null;

            card.innerHTML = `
                <div class="relative">
                    <input id="location-search-input" type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white" placeholder="Search address..." autocomplete="off" />
                    <div id="location-search-results" class="absolute z-[1300] mt-1 hidden w-full rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-600 dark:bg-gray-800"></div>
                </div>`;
            return document.getElementById('location-search-input');
        }

        function addGoogleAutocomplete(inputElement) {
            if (!inputElement || !window.google || !google.maps) return;
            googleAutocompleteInstance = new google.maps.places.Autocomplete(inputElement, {
                fields: ['geometry', 'formatted_address']
            });
            googleAutocompleteInstance.addListener('place_changed', () => {
                const place = googleAutocompleteInstance.getPlace();
                const location = place?.geometry?.location;
                const formattedAddress = place?.formatted_address || '';
                @this.set('selectedAddress', formattedAddress);
                inputElement.value = formattedAddress;
                if (location) updateLocation({ lat: location.lat(), lng: location.lng() });
            });
        }

        function addOsmAutocomplete(inputElement) {
            if (!inputElement) return;
            const resultBox = document.getElementById('location-search-results');
            if (!resultBox) return;

            inputElement.addEventListener('input', (event) => {
                const query = event.target.value?.trim();
                clearTimeout(searchDebounce);
                if (!query || query.length < 3) {
                    resultBox.classList.add('hidden');
                    resultBox.innerHTML = '';
                    return;
                }
                searchDebounce = setTimeout(async () => {
                    const results = await searchOsmAddress(query);
                    renderOsmResults(results, resultBox, inputElement);
                }, 400);
            });
        }

        async function searchOsmAddress(query) {
            const encoded = encodeURIComponent(query.replace(/\s+/g, ' ').trim());
            const center = leafletMap ? leafletMap.getCenter() : null;
            const latParam = center ? `&lat=${center.lat}` : '';
            const lonParam = center ? `&lon=${center.lng}` : '';
            const url = `https://nominatim.openstreetmap.org/search?q=${encoded}&format=json&addressdetails=1&limit=8&accept-language=en${latParam}${lonParam}`;
            try {
                const response = await fetch(url);
                if (!response.ok) return [];
                return await response.json();
            } catch (e) {
                return [];
            }
        }

        function renderOsmResults(results, resultBox, inputElement) {
            if (!Array.isArray(results) || results.length === 0) {
                resultBox.classList.remove('hidden');
                resultBox.innerHTML = `<div class="px-3 py-2 text-sm text-gray-500 dark:text-gray-300">No locations found.</div>`;
                return;
            }
            resultBox.innerHTML = results.map((item) => `
                <button type="button" class="block w-full border-b border-gray-100 px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-100 dark:hover:bg-gray-700 dark:hover:text-white" data-lat="${item.lat}" data-lng="${item.lon}" data-label="${item.display_name}">
                    ${item.display_name}
                </button>
            `).join('');
            resultBox.classList.remove('hidden');
            resultBox.querySelectorAll('button').forEach((button) => {
                button.addEventListener('click', () => {
                    const lat = parseFloat(button.dataset.lat);
                    const lng = parseFloat(button.dataset.lng);
                    const label = button.dataset.label || '';
                    inputElement.value = label;
                    @this.set('selectedAddress', label);
                    updateLocation({ lat, lng });
                    resultBox.classList.add('hidden');
                    resultBox.innerHTML = '';
                });
            });
        }

        function addMapEvents() {
            google.maps.event.addListener(deliveryMarker, 'dragend', (e) => {
                updateLocation(e.latLng);
                reverseGeocode(e.latLng);
            });

            google.maps.event.addListener(deliveryMap, 'click', (e) => {
                deliveryMarker.position = e.latLng;
                updateLocation(e.latLng);
                reverseGeocode(e.latLng);
            });
        }

        async function updateLocation(latLng, shouldReverseGeocode = false) {
            const lat = typeof latLng.lat === 'function' ? latLng.lat() : latLng.lat;
            const lng = typeof latLng.lng === 'function' ? latLng.lng() : latLng.lng;

            if (MAP_PROVIDER === 'osm' && leafletMap && leafletMarker) {
                leafletMarker.setLatLng([lat, lng]);
                leafletMap.setView([lat, lng], leafletMap.getZoom());
            } else {
                deliveryMarker.position = { lat, lng };
                deliveryMap.setCenter({ lat, lng });
            }

            if (MAP_PROVIDER === 'osm' && shouldReverseGeocode) {
                const address = await reverseGeocodeOsm(lat, lng);
                if (address) {
                    @this.set('selectedAddress', address);
                }
            }

            Livewire.dispatch('locationSelected', {
                lat,
                lng,
                address: @this.get('selectedAddress')
            });
        }

        function reverseGeocode(latLng) {
            if (MAP_PROVIDER === 'osm') return;
            const geocoder = new google.maps.Geocoder();
            geocoder.geocode({ location: latLng }, (results, status) => {
                if (status === 'OK' && results[0]) {
                    @this.set('selectedAddress', results[0].formatted_address);
                }
            });
        }

        async function reverseGeocodeOsm(lat, lng) {
            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`);
                const data = await response.json();
                return data?.display_name || null;
            } catch (error) {
                return null;
            }
        }
    </script>
    @endscript
    @endpush

</div>
