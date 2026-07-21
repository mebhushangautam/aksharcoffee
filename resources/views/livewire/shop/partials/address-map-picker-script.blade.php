@php
    /**
     * Reusable map picker (Google Maps or OSM/Leaflet).
     *
     * Required params:
     * - prefix: unique string per instance (e.g. "address", "cartCustomer")
     * - apiKey: google map api key (string|null)
     * - provider: "google"|"osm"
     * - event: Livewire event name to (re)initialize the map with optional {lat,lng}
     * - mapElementId: DOM id for map container
     * - searchCardId: DOM id of search-card container (script mounts input+results into it)
     * - latField, lngField, addressField: Livewire property names to update
     *
     * Optional params:
     * - defaultLat, defaultLng: defaults if event params missing
     * - countryCodes: optional ISO country code list, e.g. "in,ae" (used for nominatim search)
     */
@endphp

@script
<script>
    (function () {
        const cfg = {
            prefix: @json($prefix),
            apiKey: atob(@json(base64_encode($apiKey ?? ''))),
            provider: @json($provider ?? 'google'),
            event: @json($event),
            mapElementId: @json($mapElementId),
            searchCardId: @json($searchCardId),
            latField: @json($latField),
            lngField: @json($lngField),
            addressField: @json($addressField),
            defaultLat: Number(@json($defaultLat ?? 26.9125)),
            defaultLng: Number(@json($defaultLng ?? 75.7875)),
            countryCodes: @json($countryCodes ?? ''),
        };

        const ids = {
            input: `${cfg.prefix}-location-search-input`,
            results: `${cfg.prefix}-location-search-results`,
            googleCallback: `__${cfg.prefix}InitMapPickerCallback`,
        };

        let googleMap = null, googleMarker = null;
        let leafletMap = null, leafletMarker = null;
        let googleAutocompleteInstance = null;
        let searchDebounce = null;
        let mapInitialized = false;

        Livewire.on(cfg.event, (params) => {
            setTimeout(() => setupMap(params?.[0] || {}), 250);
        });

        // In case the component wants to initialize without an event (e.g. when DOM already visible)
        if (document.getElementById(cfg.mapElementId)) {
            setTimeout(() => setupMap({}), 350);
        }

        function bootstrap() {
            if (cfg.provider === 'osm') {
                return loadLeafletAssets().then(() => true);
            }
            return loadGoogleMaps().catch(() => loadLeafletAssets());
        }

        function setupMap(params) {
            const el = document.getElementById(cfg.mapElementId);
            if (!el) return;

            // allow re-init across modal opens
            mapInitialized = false;
            googleAutocompleteInstance = null;

            const lat = Number(params.lat ?? cfg.defaultLat);
            const lng = Number(params.lng ?? cfg.defaultLng);

            bootstrap().then(() => {
                if (cfg.provider === 'osm' || !window.google || !window.google.maps) {
                    setupLeafletMap(lat, lng);
                } else {
                    setupGoogleMap(lat, lng);
                }
            });
        }

        function loadGoogleMaps() {
            return new Promise((resolve, reject) => {
                if (window.google && window.google.maps) return resolve();

                window[ids.googleCallback] = () => resolve();
                const script = document.createElement('script');
                script.src = cfg.apiKey
                    ? `https://maps.googleapis.com/maps/api/js?key=${encodeURIComponent(cfg.apiKey)}&loading=async&libraries=places,geocoding,marker&callback=${ids.googleCallback}`
                    : `https://maps.googleapis.com/maps/api/js?loading=async&libraries=places,geocoding,marker&callback=${ids.googleCallback}`;
                script.async = true;
                script.onerror = reject;
                document.head.appendChild(script);
            });
        }

        function loadLeafletAssets() {
            return new Promise((resolve) => {
                if (window.L) return resolve();

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

        function setupGoogleMap(lat, lng) {
            const el = document.getElementById(cfg.mapElementId);
            if (!el || mapInitialized) return;
            mapInitialized = true;

            googleMap = new google.maps.Map(el, {
                center: { lat, lng },
                zoom: 15,
                gestureHandling: 'greedy',
                zoomControl: false,
                streetViewControl: false,
                mapId: 'DEMO_MAP_ID',
            });

            googleMarker = new google.maps.marker.AdvancedMarkerElement({
                position: { lat, lng },
                map: googleMap,
                gmpDraggable: true,
                title: 'Location'
            });

            google.maps.event.addListener(googleMarker, 'dragend', (e) => {
                updateLatLng(e.latLng.lat(), e.latLng.lng());
                reverseGeocodeGoogle(e.latLng);
            });

            google.maps.event.addListener(googleMap, 'click', (e) => {
                updateLatLng(e.latLng.lat(), e.latLng.lng());
                reverseGeocodeGoogle(e.latLng);
            });

            setTimeout(() => {
                google.maps.event.trigger(googleMap, 'resize');
                googleMap.setCenter(new google.maps.LatLng(lat, lng));
            }, 100);

            const input = mountSearchInput();
            addGoogleAutocomplete(input);
        }

        function setupLeafletMap(lat, lng) {
            if (!window.L) return;

            if (leafletMap) {
                leafletMap.remove();
                leafletMap = null;
            }

            leafletMap = L.map(cfg.mapElementId).setView([lat, lng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(leafletMap);

            leafletMarker = L.marker([lat, lng], { draggable: true, title: 'Location' }).addTo(leafletMap);

            leafletMarker.on('dragend', async (event) => {
                const p = event.target.getLatLng();
                await updateLatLng(p.lat, p.lng, true);
            });

            leafletMap.on('click', async (event) => {
                await updateLatLng(event.latlng.lat, event.latlng.lng, true);
            });

            const input = mountSearchInput();
            addOsmAutocomplete(input);
        }

        async function updateLatLng(lat, lng, shouldReverseGeocode = false) {
            if (!lat || !lng) return;

            if (cfg.provider === 'osm' && leafletMap && leafletMarker) {
                leafletMarker.setLatLng([lat, lng]);
                leafletMap.setView([lat, lng], leafletMap.getZoom());
            } else if (googleMarker && googleMap) {
                googleMarker.position = { lat, lng };
                googleMap.setCenter({ lat, lng });
            }

            @this.set(cfg.latField, lat);
            @this.set(cfg.lngField, lng);

            if (cfg.provider === 'osm' && shouldReverseGeocode) {
                const formatted = await reverseGeocodeOsm(lat, lng);
                if (formatted) @this.set(cfg.addressField, formatted);
            }
        }

        function mountSearchInput() {
            const card = document.getElementById(cfg.searchCardId);
            if (!card) return null;

            card.innerHTML = `
                <div class="relative">
                    <input id="${ids.input}" type="text"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                        placeholder="Search address..." autocomplete="off" />
                    <div id="${ids.results}" class="absolute z-[1300] mt-1 hidden w-full rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-600 dark:bg-gray-800"></div>
                </div>
            `;

            return document.getElementById(ids.input);
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
                if (!location) return;

                inputElement.value = formattedAddress;
                @this.set(cfg.addressField, formattedAddress);
                updateLatLng(location.lat(), location.lng());
            });
        }

        function addOsmAutocomplete(inputElement) {
            if (!inputElement) return;
            const resultBox = document.getElementById(ids.results);
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
                    resultBox.classList.remove('hidden');
                    resultBox.innerHTML = `<div class="px-3 py-2 text-sm text-gray-500 dark:text-gray-300">Searching...</div>`;
                    const results = await searchOsmAddress(query);
                    renderOsmResults(results, resultBox, inputElement);
                }, 400);
            });
        }

        async function searchOsmAddress(query) {
            const normalized = query.replace(/\s+/g, ' ').trim();
            const encoded = encodeURIComponent(normalized);
            const countryFilter = (cfg.countryCodes && String(cfg.countryCodes).trim() !== '')
                ? `&countrycodes=${encodeURIComponent(cfg.countryCodes)}`
                : '';
            const url = `https://nominatim.openstreetmap.org/search?q=${encoded}&format=json&addressdetails=1&limit=8${countryFilter}&accept-language=en`;
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
                <button type="button"
                    class="block w-full border-b border-gray-100 px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-100 dark:hover:bg-gray-700 dark:hover:text-white"
                    data-lat="${item.lat}" data-lng="${item.lon}" data-label="${item.display_name}">
                    ${item.display_name}
                </button>
            `).join('');

            resultBox.classList.remove('hidden');
            resultBox.querySelectorAll('button').forEach((btn) => {
                btn.addEventListener('click', async () => {
                    const lat = parseFloat(btn.dataset.lat);
                    const lng = parseFloat(btn.dataset.lng);
                    const label = btn.dataset.label || '';
                    inputElement.value = label;
                    @this.set(cfg.addressField, label);
                    await updateLatLng(lat, lng);
                    resultBox.classList.add('hidden');
                    resultBox.innerHTML = '';
                });
            });
        }

        function reverseGeocodeGoogle(latLng) {
            if (!window.google || !google.maps) return;
            const geocoder = new google.maps.Geocoder();
            geocoder.geocode({ location: latLng }, (results, status) => {
                if (status === 'OK' && results[0]) {
                    @this.set(cfg.addressField, results[0].formatted_address);
                }
            });
        }

        async function reverseGeocodeOsm(lat, lng) {
            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`);
                const data = await response.json();
                return data?.display_name || null;
            } catch (e) {
                return null;
            }
        }
    })();
</script>
@endscript

