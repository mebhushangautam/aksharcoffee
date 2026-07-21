<div class="min-h-0 lg:flex lg:h-[calc(100dvh-4rem)] lg:max-h-[calc(100dvh-4rem)] lg:min-h-0 lg:flex-col lg:overflow-hidden">

    <div class="flex min-h-0 flex-1 flex-col lg:h-full lg:min-h-0 lg:flex-row lg:items-stretch lg:overflow-x-hidden lg:overflow-y-visible">

        <div class="relative z-10 flex w-full min-h-0 shrink-0 flex-col border-b border-gray-100 bg-white text-sm font-medium text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 lg:sticky lg:top-0 lg:h-full lg:max-h-full lg:min-h-0 lg:w-52 lg:min-w-[208px] lg:overflow-x-hidden lg:border-b-0 lg:border-r"
            x-data="{
                menuOpen: false,
                largeScreen: typeof window !== 'undefined' && window.matchMedia('(min-width: 1024px)').matches,
                bindMq() {
                    const mq = window.matchMedia('(min-width: 1024px)');
                    mq.addEventListener('change', () => { this.largeScreen = mq.matches; });
                }
            }"
            x-init="bindMq()"
            @settings-close-sections-menu.window="menuOpen = false"
            @keydown.escape.window="if (!largeScreen) menuOpen = false">

            <div class="shrink-0 border-b border-gray-100 p-4 dark:border-gray-700 bg-skin-base/5 dark:bg-skin-base/20">
                <button type="button"
                    class="flex w-full items-center justify-between gap-2 rounded-lg py-0.5 text-left lg:hidden "
                    @click="menuOpen = !menuOpen"
                    :aria-expanded="menuOpen"
                    aria-controls="settingsTabsScroll">
                    <span class="min-w-0 flex-1 text-sm font-semibold text-gray-900 dark:text-white">@lang('menu.settings')</span>
                    <svg class="h-5 w-5 shrink-0 text-gray-500 transition-transform dark:text-gray-400" :class="menuOpen && 'rotate-180'" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                    </svg>
                </button>
                <h1 class="hidden text-sm font-semibold text-gray-900 dark:text-white lg:block">@lang('menu.settings')</h1>
            </div>

            <div class="shrink-0 border-b border-gray-100 px-3 py-2 dark:border-gray-700"
                :class="!largeScreen && !menuOpen ? 'hidden' : 'block'">
                <label for="settingsTabSearch" class="sr-only">{{ __('app.search') }}</label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 start-0 flex items-center ps-3">
                        <svg class="h-4 w-4 text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M9 3a6 6 0 104.472 10.03l2.249 2.249a.75.75 0 101.06-1.06l-2.249-2.249A6 6 0 009 3zm-4.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input id="settingsTabSearch" type="search" autocomplete="off" placeholder="{{ __('app.search') }}"
                        class="block w-full rounded-lg border border-gray-300 bg-gray-50 py-2 ps-9 pe-9 text-sm text-gray-900 shadow-sm outline-none transition focus:border-skin-base focus:ring-2 focus:ring-skin-base/40 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" />
                    <button id="settingsTabSearchClear" type="button"
                        class="absolute inset-y-0 end-1 my-auto hidden h-7 w-7 shrink-0 items-center justify-center rounded-md text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300"
                        aria-label="{{ __('app.clear') }}">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm2.53-10.47a.75.75 0 00-1.06-1.06L10 8.94 8.53 7.47a.75.75 0 10-1.06 1.06L8.94 10l-1.47 1.47a.75.75 0 101.06 1.06L10 11.06l1.47 1.47a.75.75 0 101.06-1.06L11.06 10l1.47-1.47z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>

            <nav id="settingsTabsScroll"
                class="min-h-0 flex-1 overflow-y-auto overscroll-contain max-lg:max-h-[min(70vh,28rem)] max-lg:[-webkit-overflow-scrolling:touch] [scrollbar-width:thin] [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:rounded-full [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-200 [&::-webkit-scrollbar-thumb]:bg-gray-400 hover:[&::-webkit-scrollbar-thumb]:bg-gray-500 dark:[&::-webkit-scrollbar-track]:bg-gray-700 dark:[&::-webkit-scrollbar-thumb]:bg-gray-500 dark:hover:[&::-webkit-scrollbar-thumb]:bg-gray-400"
                :class="!largeScreen && !menuOpen ? 'hidden' : 'block'"
                aria-label="{{ __('menu.settings') }}">
            <ul id="settingsTabsList"
                class="flex w-full min-w-0 flex-col pb-3"
                @click="if (!largeScreen && $event.target.closest('a[href]')) { menuOpen = false }">
                @if (user()->hasRole('Admin_'.user()->restaurant_id))

                <li class="me-2">
                    <x-settings.sidebar-menu-item :name='__("modules.settings.restaurantSettings")' :link='route("settings.index")."?tab=restaurant"' :active='$activeSetting == "restaurant"' />
                </li>
                <li class="me-2">
                    <x-settings.sidebar-menu-item :name='__("modules.settings.appSettings")' :link='route("settings.index")."?tab=app"' :active='$activeSetting == "app"' />
                </li>
                <li class="me-2">
                    <x-settings.sidebar-menu-item :name='__("modules.settings.operationalShifts")' :link='route("settings.index")."?tab=operationalShifts"' :active='$activeSetting == "operationalShifts"' />
                </li>
                @if (user()->hasRole('Admin_'.user()->restaurant_id) || user_can('Show Restaurant Open/Close'))
                    <li class="me-2">
                        <x-settings.sidebar-menu-item :name='__("modules.settings.restaurantOpenCloseSettings")' :link='route("settings.index")."?tab=restaurantOpenClose"' :active='$activeSetting == "restaurantOpenClose"' />
                    </li>
                @endif
                <li class="me-2">
                    <x-settings.sidebar-menu-item :name='__("modules.settings.branchSettings")' :link='route("settings.index")."?tab=branch"' :active='$activeSetting == "branch"' />
                </li>
                <li class="me-2">
                    <x-settings.sidebar-menu-item :name='__("modules.settings.currencySettings")' :link='route("settings.index")."?tab=currency"' :active='$activeSetting == "currency"' />
                </li>
                <li class="me-2">
                    <x-settings.sidebar-menu-item :name='__("modules.settings.emailSettings")' :link='route("settings.index")."?tab=email"' :active='$activeSetting == "email"' />
                </li>
                <li class="me-2">
                    <x-settings.sidebar-menu-item :name='__("modules.settings.taxSettings")' :link='route("settings.index")."?tab=tax"' :active='$activeSetting == "tax"' />
                </li>
                <li class="me-2">
                    <x-settings.sidebar-menu-item :name='__("modules.settings.paymentgatewaySettings")' :link='route("settings.index")."?tab=payment"' :active='$activeSetting == "payment"' />
                </li>
                <li class="me-2">
                    <x-settings.sidebar-menu-item :name='__("modules.settings.themeSettings")' :link='route("settings.index")."?tab=theme"' :active='$activeSetting == "theme"' />
                </li>
                <li class="me-2">
                    <x-settings.sidebar-menu-item :name='__("modules.settings.roleSettings")' :link='route("settings.index")."?tab=role"' :active='$activeSetting == "role"' />
                </li>

                <li class="me-2">
                    <x-settings.sidebar-menu-item :name='__("modules.settings.billing")' :link='route("settings.index")."?tab=billing"' :active='$activeSetting == "billing"' />
                </li>

                @endif

                @if (!user()->hasRole('Admin_'.user()->restaurant_id) && user_can('Show Restaurant Open/Close'))
                    <li class="me-2">   
                        <x-settings.sidebar-menu-item :name='__("modules.settings.restaurantOpenCloseSettings")' :link='route("settings.index")."?tab=restaurantOpenClose"' :active='$activeSetting == "restaurantOpenClose"' />
                    </li>
                @endif

                <li class="me-2">
                    <x-settings.sidebar-menu-item :name='__("modules.settings.reservationSettings")' :link='route("settings.index")."?tab=reservation"' :active='$activeSetting == "reservation"' />
                </li>

                <li class="me-2">
                    <x-settings.sidebar-menu-item :name='__("modules.settings.aboutUsSettings")' :link='route("settings.index")."?tab=aboutus"' :active='$activeSetting == "aboutus"' />
                </li>

                <li class="me-2">
                    <x-settings.sidebar-menu-item :name='__("modules.settings.customerSiteSettings")' :link='route("settings.index")."?tab=customerSite"' :active='$activeSetting == "customerSite"' />
                </li>

                <li class="me-2">
                    <x-settings.sidebar-menu-item :name='__("modules.settings.receiptSetting")' :link='route("settings.index")."?tab=receipt"' :active='$activeSetting == "receipt"' />
                </li>

                <li class="me-2">
                    <x-settings.sidebar-menu-item :name='__("modules.settings.printerSetting")' :link='route("settings.index")."?tab=printer"' :active='$activeSetting == "printer"' />
                </li>

                <li class="me-2">
                    <x-settings.sidebar-menu-item :name='__("modules.settings.downloads")' :link='route("settings.index")."?tab=downloads"' :active='$activeSetting == "downloads"' />
                </li>

                <li class="me-2">
                    <x-settings.sidebar-menu-item :name='__("modules.menu.menuItemImageSettings")' :link='route("settings.index")."?tab=menuItemImageSettings"' :active='$activeSetting == "menuItemImageSettings"' />
                </li>

                <li class="me-2">
                    <x-settings.sidebar-menu-item :name='__("modules.settings.deliverySettings")' :link='route("settings.index")."?tab=deliverySettings"' :active='$activeSetting == "deliverySettings"' />
                   
                </li>

                <li class="me-2">
                    <x-settings.sidebar-menu-item :name='__("modules.settings.euAllergensFicTitle")' :link='route("settings.index")."?tab=euAllergens"' :active='$activeSetting == "euAllergens"' />
                </li>

                <li class="me-2">
                    <x-settings.sidebar-menu-item :name='__("modules.settings.kotSettings")' :link='route("settings.index")."?tab=kotSettings"' :active='$activeSetting == "kotSettings"' />
                </li>
                <li class="me-2">
                    <x-settings.sidebar-menu-item :name='__("modules.settings.cancelSettings")' :link='route("settings.index")."?tab=cancelSettings"' :active='$activeSetting == "cancelSettings"' />
                </li>

                <li class="me-2">
                    <x-settings.sidebar-menu-item :name='__("modules.settings.orderSettings")' :link='route("settings.index")."?tab=orderSettings"' :active='$activeSetting == "orderSettings"' />
                </li>

                <li class="me-2">
                    <x-settings.sidebar-menu-item :name='__("modules.settings.refundReasons")' :link='route("settings.index")."?tab=refundReasons"' :active='$activeSetting == "refundReasons"' />
                </li>

                <!-- NAV ITEM - CUSTOM MODULES  -->
                @foreach (custom_module_plugins() as $item)
                    @includeIf(strtolower($item) . '::sections.settings.restaurant.sidebar')
                @endforeach
            </ul>
            </nav>
        </div>
    @once
        @push('scripts')
            <script>
                (function () {
                    function initSettingsTabSearch() {
                        const input = document.getElementById('settingsTabSearch');
                        const clearBtn = document.getElementById('settingsTabSearchClear');
                        const list = document.getElementById('settingsTabsList');
                        if (!input || !list) return;
                        if (input.dataset.bound === '1') return;
                        input.dataset.bound = '1';

                        const filter = () => {
                            const q = (input.value || '').trim().toLowerCase();
                            if (clearBtn) {
                                clearBtn.classList.toggle('hidden', q.length === 0);
                                clearBtn.classList.toggle('inline-flex', q.length > 0);
                            }

                            Array.from(list.children).forEach((child) => {
                                if (!child || child.tagName !== 'LI') return;
                                const text = (child.textContent || '').replace(/\s+/g, ' ').trim().toLowerCase();
                                child.classList.toggle('hidden', q.length > 0 && !text.includes(q));
                            });
                        };

                        input.addEventListener('input', filter);
                        input.addEventListener('search', filter);
                        if (clearBtn) {
                            clearBtn.addEventListener('click', () => {
                                input.value = '';
                                input.focus();
                                filter();
                            });
                        }
                        input.addEventListener('keydown', (e) => {
                            if (e.key === 'Escape') {
                                input.value = '';
                                filter();
                                input.blur();
                            }
                        });

                        filter();
                    }

                    document.addEventListener('DOMContentLoaded', initSettingsTabSearch);
                    document.addEventListener('livewire:navigated', () => {
                        initSettingsTabSearch();
                        window.dispatchEvent(new CustomEvent('settings-close-sections-menu'));
                    });
                })();
            </script>
        @endpush
    @endonce

        <div class="min-h-0 min-w-0 flex-1 space-y-5 py-4 px-0 lg:min-h-0 lg:overflow-y-auto lg:overscroll-contain [scrollbar-width:thin] [&::-webkit-scrollbar]:w-1 [&::-webkit-scrollbar-track]:rounded-xl [&::-webkit-scrollbar-thumb]:rounded-xl [&::-webkit-scrollbar-track]:bg-gray-300 [&::-webkit-scrollbar-thumb]:bg-gray-400 hover:[&::-webkit-scrollbar-thumb]:bg-gray-500 dark:[&::-webkit-scrollbar-track]:bg-gray-700 dark:[&::-webkit-scrollbar-thumb]:bg-gray-500 dark:hover:[&::-webkit-scrollbar-thumb]:bg-gray-400">
            <div class="grid grid-cols-1 dark:bg-gray-900">

                <div>
                    @switch($activeSetting)
                        @case('restaurant')
                        @livewire('settings.generalSettings', ['settings' => $settings])
                        @break

                        @case('app')
                        @livewire('settings.timezoneSettings', ['settings' => $settings])
                        @break

                        @case('email')
                        @livewire('settings.notificationSettings', ['settings' => $settings])
                        @break

                        @case('currency')
                        @livewire('settings.currencySettings')
                        @break

                        @case('payment')
                        @livewire('settings.paymentSettings', ['settings' => $settings])
                        @break

                        @case('theme')
                        @livewire('settings.themeSettings', ['settings' => $settings])
                        @break

                        @case('role')
                        @livewire('settings.roleSettings', ['settings' => $settings])
                        @break

                        @case('tax')
                        @livewire('settings.taxSettings', ['settings' => $settings])
                        @break

                        @case('reservation')
                        @livewire('settings.reservationSettings')
                        @break

                        @case('branch')
                        @livewire('settings.branchSettings')
                        @break
                        @case('billing')
                        @livewire('settings.billingSettings')
                        @break

                        @case('aboutus')
                        @livewire('settings.aboutUsSettings', ['settings' => $settings])
                        @break

                        @case('customerSite')
                        @livewire('settings.customerSiteSettings', ['settings' => $settings])
                        @break

                        @case('receipt')
                        @livewire('settings.ReceiptSetting', ['settings' => $settings])
                        @break

                        @case('printer')
                        @livewire('settings.PrinterSetting', ['settings' => $settings])
                        @break

                        @case('downloads')
                        @livewire('settings.DownloadSettings')
                        @break

                        @case('menuItemImageSettings')
                        @livewire('settings.menuItemImageSettings', ['settings' => $settings])
                        @break

                        @case('euAllergens')
                        @livewire('settings.euAllergensSettings', ['settings' => $settings])
                        @break

                        @case('deliverySettings')
                        @livewire('settings.branchDeliverySettings', ['settings' => $settings])
                        @break

                        @case('operationalShifts')
                        @livewire('settings.branchOperationalShifts', ['settings' => $settings])
                        @break

                        @case('restaurantOpenClose')
                        @if (user()->hasRole('Admin_'.user()->restaurant_id) || user_can('Show Restaurant Open/Close'))
                            @livewire('settings.restaurantOpenCloseSettings', ['settings' => $settings])
                        @endif
                        @break

                        @case('kotSettings')
                        @livewire('settings.kotSettings', ['settings' => $settings])
                        @break

                        @case('cancelSettings')
                        @livewire('settings.CancellationSettings', ['settings' => $settings])
                        @break

                        @case('orderSettings')
                        @livewire('settings.OrderSettings', ['settings' => $settings])
                        @break

                        @case('refundReasons')
                        @livewire('settings.RefundReasonSettings', ['settings' => $settings])
                        @break


                        @default

                    @endswitch

                    <!-- NAV ITEM - CUSTOM MODULES  -->
                    @foreach (custom_module_plugins() as $item)
                        @if($activeSetting == strtolower($item).'Settings')
                            @livewire(strtolower($item).'::restaurant.setting', ['settings' => $settings])
                        @endif
                @endforeach
                </div>

            </div>
        </div>
    </div>

</div>
