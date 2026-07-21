@php
    $detectedPhoneCode = (new \App\Models\User())->getPhoneCodeFromIp();
    $defaultPhoneCode = $detectedPhoneCode
        ?? (restaurant()->country->phonecode ?? null)
        ?? (restaurant()->phone_code ?? null);
@endphp

<x-js-dialog-modal id="add-customer-modal-root" maxWidth="2xl">
    <x-slot name="title">
        <div class="flex items-center space-x-3">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-skin-base rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">@lang('modules.customer.addCustomer')</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">@lang('modules.customer.searchOrCreate')</p>
            </div>
        </div>
    </x-slot>

    <x-slot name="content">
        <form id="ajax-add-customer-form" class="flex max-h-[78vh] flex-col" onsubmit="event.preventDefault()">
            @csrf
            <div class="space-y-3 overflow-y-auto pr-1">
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 border border-gray-200 dark:border-gray-600">
                    <div class="flex items-center space-x-2 mb-2">
                        <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <h3 class="text-base font-medium text-gray-900 dark:text-white">@lang('modules.customer.searchCustomer')</h3>
                    </div>

                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input
                            type="text"
                            id="ajax-customer-search-input"
                            class="block w-full h-10 pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                            placeholder="@lang('modules.customer.searchPlaceholder')"
                            autocomplete="off"
                        />
                    </div>

                    <div id="ajax-customer-search-results" class="relative mt-2 hidden">
                        <div class="absolute z-50 w-full bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-600 overflow-hidden">
                            <div class="max-h-60 overflow-y-auto" id="ajax-customer-search-results-list"></div>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <h3 class="text-base font-medium text-gray-900 dark:text-white">@lang('modules.customer.customerDetails')</h3>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <x-label for="ajaxCustomerName" value="{{ __('modules.customer.name') }}" class="text-sm font-medium text-gray-700 dark:text-gray-300" />
                            <input id="ajaxCustomerName" type="text" class="w-full h-10 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" placeholder="@lang('modules.customer.enterCustomerName')" required />
                        </div>

                        <div class="space-y-1">
                            <x-label for="ajaxCustomerPhone" value="{{ __('modules.customer.phone') }}" class="text-sm font-medium text-gray-700 dark:text-gray-300" />
                            <div class="flex gap-2">
                                <select id="ajaxCustomerPhoneCode" class="w-32 h-10 px-2 pr-8 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 dark:text-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">@lang('modules.settings.select')</option>
                                </select>
                                <input id="ajaxCustomerPhone" type="tel" class="flex-1 h-10 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" placeholder="@lang('modules.customer.enterPhoneNumber')" required />
                            </div>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <x-label for="ajaxCustomerEmail" value="{{ __('modules.customer.email') }}" class="text-sm font-medium text-gray-700 dark:text-gray-300" />
                        <input id="ajaxCustomerEmail" type="email" class="w-full h-10 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" placeholder="@lang('modules.customer.enterEmailAddress')" />
                    </div>

                    <div class="space-y-1">
                        <x-label for="ajaxCustomerAddress" value="{{ __('modules.customer.address') }}" class="text-sm font-medium text-gray-700 dark:text-gray-300" />
                        <textarea id="ajaxCustomerAddress" rows="3" data-gramm="false" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 resize-none" placeholder="@lang('modules.customer.enterDeliveryAddress')"></textarea>
                    </div>
                </div>
            </div>

            <div class="sticky bottom-0 mt-3 flex items-center justify-between border-t border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 pt-3">
                <div class="flex items-center space-x-2 text-xs text-gray-500 dark:text-gray-400">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>@lang('modules.customer.searchOrCreate')</span>
                </div>
                <div class="flex space-x-2 rtl:space-x-reverse">
                    {{-- Plain button so id stays on the clickable element (Jetstream cancel + Livewire can interfere with POS modal). --}}
                    <button type="button" id="ajax-add-customer-cancel-btn"
                        class="button-cancel inline-flex justify-center items-center text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-xs font-medium px-4 py-2 hover:text-gray-900 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                        @lang('app.cancel')
                    </button>
                    {{-- Plain submit button (no Livewire wire:*): POS modal sits outside Livewire roots but Jetstream x-button adds wire:loading and can break AJAX submit / cause full page reload. --}}
                    <button type="submit" id="ajax-add-customer-save-btn"
                        class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-semibold text-white bg-skin-base hover:bg-skin-base/[.88] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-skin-base disabled:opacity-60 disabled:cursor-not-allowed dark:focus:ring-offset-gray-800">
                        <svg class="w-5 h-5 ltr:mr-1 rtl:ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 13 4 4L19 7"/></svg>
                        @lang('app.save')
                    </button>
                </div>
            </div>
        </form>
    </x-slot>
</x-js-dialog-modal>

<script>
    (function () {
        if (window.__posAjaxAddCustomerBootstrapped) {
            return;
        }
        window.__posAjaxAddCustomerBootstrapped = true;

        const config = {
            ajaxBase: '/ajax/pos',
            csrfToken: @json(csrf_token()),
            defaultPhoneCode: @json($defaultPhoneCode),
            messages: {
                noCustomersFound: @json(__('modules.customer.noCustomersFound')),
                createNewCustomer: @json(__('modules.customer.createNewCustomer')),
                searchingCustomers: @json(__('modules.customer.searchingCustomers')),
                addNewCustomer: @json(__('modules.customer.addNewCustomer')),
            },
            selectLabel: @json(__('modules.settings.select')),
        };

        const state = {
            selectedCustomerId: null,
            orderId: null,
            fromPos: false,
            searchTimer: null,
            phoneCodes: [],
        };

        const dom = {};

        function refreshDomRefs() {
            dom.form = document.getElementById('ajax-add-customer-form');
            dom.searchInput = document.getElementById('ajax-customer-search-input');
            dom.searchResults = document.getElementById('ajax-customer-search-results');
            dom.searchResultsList = document.getElementById('ajax-customer-search-results-list');
            dom.name = document.getElementById('ajaxCustomerName');
            dom.phoneCode = document.getElementById('ajaxCustomerPhoneCode');
            dom.phone = document.getElementById('ajaxCustomerPhone');
            dom.email = document.getElementById('ajaxCustomerEmail');
            dom.address = document.getElementById('ajaxCustomerAddress');
            dom.cancelBtn = document.getElementById('ajax-add-customer-cancel-btn');
            dom.saveBtn = document.getElementById('ajax-add-customer-save-btn');
        }

        function setBusy(isBusy) {
            refreshDomRefs();
            if (!dom.saveBtn) {
                return;
            }
            dom.saveBtn.disabled = isBusy;
            dom.saveBtn.classList.toggle('opacity-60', isBusy);
            dom.saveBtn.classList.toggle('cursor-not-allowed', isBusy);
        }

        function resolvePosOrderIdFromClient() {
            if (typeof window.getCurrentPosOrderId === 'function') {
                const id = window.getCurrentPosOrderId();
                const n = parseInt(String(id || ''), 10);
                if (!Number.isNaN(n) && n > 0) {
                    return n;
                }
            }
            if (typeof window.posState !== 'undefined' && window.posState) {
                const raw = window.posState.orderID || (window.posState.orderDetail && window.posState.orderDetail.id);
                const n = parseInt(String(raw || ''), 10);
                if (!Number.isNaN(n) && n > 0) {
                    return n;
                }
            }
            return null;
        }

        function dispatchCustomerUpdated(customer) {
            window.dispatchEvent(new CustomEvent('pos-customer-updated', {
                detail: { customer: customer || null },
            }));
        }

        async function request(url, options = {}) {
            const response = await fetch(url, {
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': config.csrfToken,
                    ...(options.headers || {}),
                },
                ...options,
            });

            const data = await response.json().catch(() => ({}));
            if (!response.ok) {
                const message = data?.message || 'Request failed';
                throw new Error(message);
            }
            return data;
        }

        function renderSearchLoading() {
            refreshDomRefs();
            if (!dom.searchResults || !dom.searchResultsList) {
                return;
            }
            dom.searchResultsList.innerHTML = `
                <div class="flex flex-col items-center justify-center gap-2 px-4 py-8">
                    <svg class="h-8 w-8 animate-spin text-skin-base" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="text-xs font-medium text-gray-600 dark:text-gray-400">${config.messages.searchingCustomers}</p>
                </div>
            `;
            dom.searchResults.classList.remove('hidden');
        }

        function renderSearchResults(customers) {
            refreshDomRefs();
            if (!dom.searchResults || !dom.searchResultsList) {
                return;
            }

            if (!customers || customers.length === 0) {
                dom.searchResultsList.innerHTML = `
                    <div class="p-4 text-center space-y-3">
                        <p class="text-sm text-gray-600 dark:text-gray-300">${config.messages.noCustomersFound}</p>
                        <button type="button" data-add-new-customer="1"
                            class="inline-flex items-center justify-center px-4 py-2.5 w-full sm:w-auto mx-auto bg-skin-base hover:bg-skin-base/[.88] text-white text-sm font-medium rounded-lg transition-colors">
                            <svg class="w-4 h-4 ltr:mr-1.5 rtl:ml-1.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            ${config.messages.addNewCustomer}
                        </button>
                    </div>
                `;
                dom.searchResults.classList.remove('hidden');
                return;
            }

            dom.searchResultsList.innerHTML = customers.map((customer) => {
                const name = customer.name || '';
                const phone = customer.phone || '';
                const email = customer.email || '';
                return `
                    <button type="button" class="w-full text-left px-3 py-2 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors" data-customer-id="${customer.id}">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">${name}</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">${phone}${email ? ` | ${email}` : ''}</p>
                    </button>
                `;
            }).join('');

            dom.searchResults.classList.remove('hidden');
        }

        function hideSearchResults() {
            refreshDomRefs();
            if (dom.searchResults) {
                dom.searchResults.classList.add('hidden');
            }
            if (dom.searchResultsList) {
                dom.searchResultsList.innerHTML = '';
            }
        }

        function setForm(customer) {
            refreshDomRefs();
            if (!dom.name || !dom.phone || !dom.email || !dom.address || !dom.phoneCode) {
                return;
            }
            const data = customer || {};
            dom.name.value = data.name || '';
            dom.phone.value = data.phone || '';
            dom.email.value = data.email || '';
            dom.address.value = data.delivery_address || data.address || '';
            if (data.phone_code) {
                dom.phoneCode.value = String(data.phone_code);
            }
        }

        function getPreferredPhoneCode() {
            const candidateFromState = window.posState?.customer?.phone_code;
            if (candidateFromState) {
                return String(candidateFromState);
            }
            if (config.defaultPhoneCode) {
                return String(config.defaultPhoneCode);
            }
            return state.phoneCodes.length > 0 ? String(state.phoneCodes[0]) : '';
        }

        function clearForm(opts) {
            opts = opts || {};
            refreshDomRefs();
            if (!dom.form) {
                return;
            }
            if (opts.clearSelection !== false) {
                state.selectedCustomerId = null;
            }
            dom.form.reset();
            hideSearchResults();
            if (dom.searchInput) {
                dom.searchInput.value = '';
            }
            const preferredPhoneCode = getPreferredPhoneCode();
            if (preferredPhoneCode && dom.phoneCode) {
                dom.phoneCode.value = preferredPhoneCode;
            }
        }

        /**
         * Always repaints the visible <select> after Livewire/native navigations (DOM may be replaced).
         * Fetch phone codes once per session; re-use cached list for new selects.
         */
        async function ensurePhoneCodesPopulated() {
            refreshDomRefs();
            if (!dom.phoneCode) {
                return;
            }

            if (!state.phoneCodes.length) {
                try {
                    const list = await request(`${config.ajaxBase}/phone-codes`);
                    state.phoneCodes = Array.isArray(list) ? list : [];
                } catch (e) {
                    console.error('POS add customer: phone codes fetch failed', e);
                    return;
                }
            }

            dom.phoneCode.innerHTML = '<option value="">' + config.selectLabel + '</option>';
            state.phoneCodes.forEach((code) => {
                const option = document.createElement('option');
                option.value = String(code);
                option.textContent = `+${code}`;
                dom.phoneCode.appendChild(option);
            });
            const preferredPhoneCode = getPreferredPhoneCode();
            if (preferredPhoneCode) {
                dom.phoneCode.value = preferredPhoneCode;
            }
        }

        async function loadCustomer(customerId) {
            if (!customerId) {
                return;
            }
            const payload = await request(`${config.ajaxBase}/customers/${customerId}`);
            if (payload?.customer) {
                state.selectedCustomerId = payload.customer.id;
                setForm(payload.customer);
            }
        }

        async function searchCustomers(query) {
            if (!query || query.length < 2) {
                hideSearchResults();
                return;
            }
            renderSearchLoading();
            try {
                const customers = await request(`${config.ajaxBase}/customers?search=${encodeURIComponent(query)}`);
                renderSearchResults(customers);
            } catch (e) {
                hideSearchResults();
                throw e;
            }
        }

        async function saveCustomer(event) {
            event.preventDefault();
            refreshDomRefs();
            if (!dom.form || !dom.name || !dom.phoneCode || !dom.phone) {
                console.warn('POS add customer: form fields missing; modal DOM may not be ready.');
                return;
            }

            setBusy(true);

            try {
                if (!state.orderId) {
                    state.orderId = resolvePosOrderIdFromClient();
                }

                const payload = {
                    name: dom.name.value.trim(),
                    phone_code: dom.phoneCode.value.trim(),
                    phone: dom.phone.value.trim(),
                    email: (dom.email && dom.email.value.trim()) || null,
                    address: (dom.address && dom.address.value.trim()) || null,
                };

                const saveResponse = await request(`${config.ajaxBase}/customers`, {
                    method: 'POST',
                    body: JSON.stringify(payload),
                });

                const customer = saveResponse?.customer || null;
                if (!customer || !customer.id) {
                    throw new Error('Customer save failed');
                }

                if (state.orderId) {
                    await request(`${config.ajaxBase}/orders/${state.orderId}/set-customer`, {
                        method: 'POST',
                        body: JSON.stringify({
                            customer_id: customer.id,
                            delivery_address: payload.address,
                        }),
                    });
                }

                if (window.posState) {
                    window.posState.customerId = customer.id;
                    window.posState.customer = customer;
                }

                dispatchCustomerUpdated(customer);
                window.dispatchEvent(new CustomEvent('add-customer-modal-close'));
            } catch (error) {
                if (typeof showToast === 'function') {
                    showToast('error', error.message || 'Unable to save customer');
                } else {
                    alert(error.message || 'Unable to save customer');
                }
            } finally {
                setBusy(false);
            }
        }

        /**
         * Delegated listeners survive Livewire navigations / DOM swaps (direct bindings pointed at detached nodes).
         */
        function bindDelegatedEventsOnce() {
            if (window.__posAjaxAddCustomerDelegated) {
                return;
            }
            window.__posAjaxAddCustomerDelegated = true;

            document.addEventListener('submit', function (event) {
                const form = event.target;
                if (!form || form.id !== 'ajax-add-customer-form') {
                    return;
                }
                event.preventDefault();
                saveCustomer(event);
            }, true);

            // Cancel + search row + outside-close use capture: modal panel has x-on:click.stop, so bubble never reaches document.
            document.addEventListener('click', function (event) {
                if (event.target.closest('#ajax-add-customer-cancel-btn')) {
                    event.preventDefault();
                    window.dispatchEvent(new CustomEvent('add-customer-modal-close'));
                }
            }, true);

            document.addEventListener('input', function (event) {
                if (event.target.id !== 'ajax-customer-search-input') {
                    return;
                }
                const value = event.target.value.trim();
                if (state.searchTimer) {
                    clearTimeout(state.searchTimer);
                }
                state.searchTimer = setTimeout(function () {
                    searchCustomers(value).catch(function (error) {
                        console.error('Customer search error:', error);
                        hideSearchResults();
                    });
                }, 250);
            });

            document.addEventListener('click', function (event) {
                const button = event.target.closest('[data-customer-id]');
                if (!button || !button.closest('#ajax-customer-search-results-list')) {
                    return;
                }
                const rawId = button.getAttribute('data-customer-id');
                const customerId = Number.parseInt(String(rawId), 10);
                if (!Number.isNaN(customerId) && customerId > 0) {
                    loadCustomer(customerId).catch(function (error) {
                        console.error('Load customer error:', error);
                    });
                    hideSearchResults();
                }
            }, true);

            document.addEventListener('click', function (event) {
                const btn = event.target.closest('[data-add-new-customer]');
                if (!btn || !btn.closest('#ajax-customer-search-results-list')) {
                    return;
                }
                event.preventDefault();
                refreshDomRefs();
                const searchTerm = (dom.searchInput && dom.searchInput.value.trim()) || '';
                hideSearchResults();
                if (dom.searchInput) {
                    dom.searchInput.value = '';
                }
                state.selectedCustomerId = null;
                if (dom.name) {
                    if (searchTerm && !/\d/.test(searchTerm)) {
                        dom.name.value = searchTerm;
                    }
                    dom.name.focus();
                }
            }, true);

            document.addEventListener('click', function (event) {
                refreshDomRefs();
                if (!dom.searchResults || !dom.searchInput || dom.searchResults.classList.contains('hidden')) {
                    return;
                }
                if (dom.searchResults.contains(event.target) || event.target === dom.searchInput) {
                    return;
                }
                hideSearchResults();
            }, true);
        }

        bindDelegatedEventsOnce();

        window.PosAddCustomerModal = {
            async open(options = {}) {
                refreshDomRefs();
                if (!dom.form) {
                    console.warn('POS add customer: #ajax-add-customer-form not in DOM yet.');
                    return;
                }

                const rawOid = options.orderId;
                const parsedOid = rawOid !== undefined && rawOid !== null && rawOid !== ''
                    ? parseInt(String(rawOid), 10)
                    : null;
                state.orderId = (!Number.isNaN(parsedOid) && parsedOid > 0)
                    ? parsedOid
                    : resolvePosOrderIdFromClient();
                state.fromPos = !!options.fromPos;
                state.selectedCustomerId = options.customerId || null;

                await ensurePhoneCodesPopulated();
                clearForm({ clearSelection: false });
                // Some navigations / resets restore the select to its initial markup — repaint options from cache.
                await ensurePhoneCodesPopulated();

                const posCustomer = window.posState?.customer || null;
                if (posCustomer && (!state.selectedCustomerId || Number(posCustomer.id) === Number(state.selectedCustomerId))) {
                    setForm(posCustomer);
                    state.selectedCustomerId = posCustomer.id || state.selectedCustomerId;
                }

                if (state.selectedCustomerId) {
                    try {
                        await loadCustomer(state.selectedCustomerId);
                    } catch (e) {
                        console.error('POS add customer: loadCustomer failed', e);
                    }
                }

                window.dispatchEvent(new CustomEvent('add-customer-modal-open'));
            },
        };
    })();
</script>
