<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ isRtl() ? 'rtl' : 'ltr' }}" class="h-full">

<head>
   @php
        $lastSegment = last(request()->segments());
    @endphp
    @if (user()->restaurant_id)
        <link rel="manifest" href="{{ asset('manifest.json') }}@if($lastSegment)?url={{ $lastSegment }}&hash={{ user()->restaurant->hash }}@endif" crossorigin="use-credentials">
    @else
        <link rel="manifest" href="{{ asset('manifest.json') }}@if($lastSegment)?url={{ $lastSegment }}@endif" crossorigin="use-credentials">
    @endif
    <meta name="theme-color" content="#ffffff">
    <meta name="description" content="{{ global_setting()->name }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('vendor/trix/trix.css') }}" />
    <link rel="stylesheet" href="{{ asset('vendor/pikaday.css') }}" />

    <link rel="apple-touch-icon" sizes="180x180" href="{{ restaurantOrGlobalSetting()->upload_fav_icon_apple_touch_icon_url }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ restaurantOrGlobalSetting()->upload_fav_icon_android_chrome_192_url }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ restaurantOrGlobalSetting()->upload_fav_icon_android_chrome_512_url }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ restaurantOrGlobalSetting()->upload_favicon_16_url }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ restaurantOrGlobalSetting()->upload_favicon_32_url }}">
    <link rel="shortcut icon" href="{{ restaurantOrGlobalSetting()->favicon_url }}">



    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ global_setting()->logoUrl }}">

    <title>{{ global_setting()->name }}</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles

    @stack('styles')

    <style id="app-nav-offset">
        :root {
            --pos-nav-offset: 4rem;
        }
    </style>
    <script>
        window.syncPosNavOffset = function () {
            var nav = document.querySelector('body > nav.fixed');
            if (!nav) {
                return;
            }
            var height = Math.ceil(nav.getBoundingClientRect().height);
            if (height > 0) {
                document.documentElement.style.setProperty('--pos-nav-offset', height + 'px');
            }
        };
        document.addEventListener('DOMContentLoaded', window.syncPosNavOffset);
        window.addEventListener('resize', window.syncPosNavOffset);
        document.addEventListener('livewire:navigated', window.syncPosNavOffset);
    </script>

    @if (request()->routeIs('pos.*'))
        {{-- POS cart uses position:fixed to dock to viewport top; overflow:hidden on #main-content can trap fixed layout in some browsers. --}}
        <style id="pos-cart-dock-layout">
            @media (max-width: 1023px) {
                #main-content:has(#order-items-container) {
                    overflow: visible !important;
                }

                #pos-container > .relative.flex.flex-col {
                    flex: 1 1 auto;
                    min-height: 0;
                }

                html.pos-mobile-menu-open #order-items-container {
                    visibility: hidden !important;
                    pointer-events: none !important;
                }

                #order-items-container {
                    position: fixed !important;
                    top: var(--pos-nav-offset);
                    right: 0;
                    left: 0;
                    bottom: env(safe-area-inset-bottom, 0px);
                    z-index: 30;
                    display: flex !important;
                    flex-direction: column !important;
                    width: 100% !important;
                    max-width: 100% !important;
                    min-height: 0 !important;
                    max-height: none !important;
                    overflow: hidden !important;
                }

                #order-items-container .pos-kot-cart-panel,
                #order-items-container #pos-order-items-panel {
                    flex: 1 1 auto;
                    min-height: 0;
                    height: 100%;
                    display: flex;
                    flex-direction: column;
                }

                #pos-kot-cart-scroll,
                #pos-cart-scroll {
                    flex: 1 1 auto;
                    min-height: 0;
                }

                #pos-kot-cart-footer,
                #pos-cart-action-footer {
                    flex-shrink: 0;
                    margin-top: auto;
                }

                #pos-cart-action-footer #pos-mobile-action-dock {
                    width: 100%;
                    max-width: 100%;
                }
            }

            @media (min-width: 1024px) {
                #main-content:has(#order-items-container) {
                    overflow: visible !important;
                }

                #order-items-container {
                    position: fixed !important;
                    top: 0 !important;
                    right: 0 !important;
                    bottom: 0 !important;
                    left: auto !important;
                    width: 33.333333% !important;
                    max-width: 33.333333% !important;
                    display: flex !important;
                    flex-direction: column !important;
                    min-height: 0 !important;
                    z-index: 40 !important;
                    padding-top: 0 !important;
                    margin-top: 0 !important;
                    box-sizing: border-box !important;
                }

                [dir="rtl"] #order-items-container {
                    right: auto !important;
                    left: 0 !important;
                }

                /* Full-height cart column (top: 0): pad panel content below fixed nav without shrinking the column. */
                #order-items-container .pos-kot-cart-panel,
                #order-items-container #pos-order-items-panel,
                #order-items-container .pos-order-detail-panel {
                    padding-top: var(--pos-nav-offset);
                    box-sizing: border-box;
                }
            }
        </style>
    @endif

    @include('sections.theme_style', [
        'baseColor' => restaurantOrGlobalSetting()->theme_rgb,
        'baseColorHex' => restaurantOrGlobalSetting()->theme_hex,
    ])


    @if (File::exists(public_path() . '/css/app-custom.css'))
        <link href="{{ asset('css/app-custom.css') }}" rel="stylesheet">
    @endif

    {{-- Pusher Beams SDK is pulled in later alongside the initialization block below so that
         it executes before our inline script; removing from head prevents deferred loading
         from running after the body script and causing the warning. --}}

    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia(
                '(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>

    <script>
        if (localStorage.getItem("menu-collapsed") === "true") {
            document.documentElement.style.visibility = 'hidden';
            window.addEventListener('DOMContentLoaded', () => {
                const sidebar = document.getElementById('sidebar');
                const openIcon = document.getElementById('toggle-sidebar-open');
                const closeIcon = document.getElementById('toggle-sidebar-close');

                if (sidebar) {
                    sidebar.classList.add('hidden');
                    sidebar.classList.remove('flex', 'lg:flex');
                }

                if (openIcon && closeIcon) {
                    openIcon.classList.remove('hidden');
                    closeIcon.classList.add('hidden');
                }

                setTimeout(() => {
                    document.documentElement.style.visibility = 'visible';
                }, 50);
            });
        } else {
            // Handle expanded state icons without hiding the page
            window.addEventListener('DOMContentLoaded', () => {
                const openIcon = document.getElementById('toggle-sidebar-open');
                const closeIcon = document.getElementById('toggle-sidebar-close');

                if (openIcon && closeIcon) {
                    openIcon.classList.add('hidden');
                    closeIcon.classList.remove('hidden');
                }
            });
        }
    </script>

    <script>
        window.recentOrdersModal = function () {
            return {
                showRecentOrdersModal: false,
                loadingRecentOrders: false,
                recentOrdersLoaded: false,
                recentOrdersError: '',
                recentOrders: [],
                expandedOrderUuid: null,

                async openRecentOrdersModal() {
                    this.showRecentOrdersModal = true;
                    await this.fetchRecentOrders();
                },

                closeRecentOrdersModal() {
                    this.showRecentOrdersModal = false;
                },

                toggleRecentOrderDetails(orderUuid) {
                    this.expandedOrderUuid = this.expandedOrderUuid === orderUuid ? null : orderUuid;
                },

                async fetchRecentOrders() {
                    this.loadingRecentOrders = true;
                    this.recentOrdersError = '';

                    try {
                        const recentOrdersUrl = '{{ route('orders.recent') }}' + '?_ts=' + Date.now();
                        const response = await fetch(recentOrdersUrl, {
                            cache: 'no-store',
                            headers: {
                                Accept: 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        });

                        if (!response.ok) {
                            throw new Error('Unable to load recent orders');
                        }

                        const data = await response.json();
                        this.recentOrders = data.orders ?? [];
                        this.expandedOrderUuid = null;
                        this.recentOrdersLoaded = true;
                    } catch (error) {
                        this.recentOrdersError = @js(__('messages.invalidRequest'));
                    } finally {
                        this.loadingRecentOrders = false;
                    }
                },

                handleRecentOrderView(order) {
                    const isPosPage = @json(request()->routeIs('pos.*'));
                    const orderId = order && order.id ? Number(order.id) : null;
                    const lifecycleStatus = String(order && order.status_label ? order.status_label : '').toUpperCase();

                    if (isPosPage && orderId) {
                        this.closeRecentOrdersModal();

                        // KOT requires full page context (route-bound data/bootstrap),
                        // but we still keep it smooth via Livewire SPA navigation.
                        if (lifecycleStatus === 'KOT' && order && order.pos_detail_url) {
                            if (typeof Livewire !== 'undefined' && typeof Livewire.navigate === 'function') {
                                Livewire.navigate(order.pos_detail_url);
                                return;
                            }
                            window.location.href = order.pos_detail_url;
                            return;
                        }

                        if (typeof Livewire !== 'undefined' && typeof Livewire.dispatch === 'function') {
                            Livewire.dispatch('showOrderDetail', {
                                id: orderId,
                                fromPos: true,
                            });
                            // Keep navigation smooth: reflect selected order in URL without full reload.
                            if (order && order.pos_detail_url && window.history && typeof window.history.pushState === 'function') {
                                window.history.pushState({}, '', order.pos_detail_url);
                            }
                            return;
                        }

                        if (order && order.pos_detail_url) {
                            window.location.href = order.pos_detail_url;
                            return;
                        }
                    }

                    if (order && order.view_url) {
                        window.location.href = order.view_url;
                    }
                },
            };
        };
    </script>

    {{-- Include file for widgets if exist --}}
    @includeIf('sections.custom_script_admin')
</head>


<body class="font-sans antialiased dark:bg-gray-900 h-full min-h-0" id="main-body">
    @include('sections.offline-banner')

    @if (user()->restaurant_id)
        @livewire('navigation-menu')
    @else
        @livewire('superadmin-navigation-menu')
    @endif

    <div @class([
        'flex rtl:flex-row-reverse overflow-hidden bg-gray-50 dark:bg-gray-900 h-screen md:pt-12',
        // POS: no lg top padding so fixed cart can span full viewport height (behind nav); menu adds its own lg:pt-16.
        'lg:pt-16' => !request()->routeIs('pos.*'),
        'lg:!pt-0' => request()->routeIs('pos.*'),
    ])>

        @if (!request()->routeIs('pos.*'))
            @if (user()->restaurant_id)
                @livewire('sidebar')
            @else
                @livewire('superadmin-sidebar')
            @endif
        @endif


        <div id="main-content"
            @class([
                'relative w-full min-w-0 h-full bg-gray-50 dark:bg-gray-900',
                'overflow-y-auto overflow-x-hidden' => !request()->routeIs('pos.*'),
                'overflow-hidden flex flex-col min-h-0' => request()->routeIs('pos.*'),
                'ltr:lg:ml-0 rtl:lg:mr-0' => request()->routeIs('pos.*'),
                'ltr:lg:ml-56 rtl:lg:mr-56' => !request()->routeIs('pos.*'),
            ])>
             <main @class(['pt-16 lg:pt-0 md:pt-3 sm:pt-16' => !request()->routeIs('pos.*'),
                'flex-1 min-h-0 flex flex-col min-w-0 max-lg:overflow-y-auto lg:overflow-hidden' => request()->routeIs('pos.*'),
            ])>
                @yield('content')
                {{ $slot ?? '' }}
            </main>


        </div>


    </div>

    @stack('modals')

    {{-- Must run before @livewireScripts so alpine:init listeners register before Alpine starts. --}}
    @stack('before-livewire-scripts')

    @livewireScripts

    @include('layouts.update-uri')

    @include('livewire.raise-support-ticket')

    <script src="{{ asset('vendor/livewire-alert/livewire-alert.js') }}" defer data-navigate-track></script>
    <x-livewire-alert::flash />

    @if (superadminPaymentGateway()->razorpay_status)
        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    @endif

    @if (user()->restaurant_id)

        @livewire('kot.kot-pusher-listener', key('kot-pusher-listener'))

        @livewire('order.OrderDetail')

        @include('customer.add-customer-modal')

        @livewire('settings.upgradeLicense')

        @livewire('order.addPayment')

        @include('sections.payment-gateway-include')

        <script>
            (function () {
                function normalizeOptionalInt(value) {
                    if (value === undefined || value === null || value === '' || value === 'null') {
                        return null;
                    }
                    const n = parseInt(String(value), 10);
                    return Number.isNaN(n) ? null : n;
                }

                function resolveOrderIdFromEnvironment() {
                    if (typeof window.getCurrentPosOrderId === 'function') {
                        const id = window.getCurrentPosOrderId();
                        if (id) {
                            return id;
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

                window.showAddCustomerModal = function (customerId, orderId, fromPos) {
                    const argCount = arguments.length;
                    const cid = argCount >= 1 ? normalizeOptionalInt(customerId) : null;
                    // If the Blade onclick passed null (e.g. draft order id not in PHP scope), still resolve from POS JS state.
                    let oid = argCount >= 2 ? normalizeOptionalInt(orderId) : null;
                    if (oid === null || oid === undefined) {
                        oid = resolveOrderIdFromEnvironment();
                    }
                    const fpos = argCount >= 3 ? Boolean(fromPos) : (typeof window.posState !== 'undefined' && !!window.posState);

                    if (window.PosAddCustomerModal && typeof window.PosAddCustomerModal.open === 'function') {
                        window.PosAddCustomerModal.open({
                            customerId: cid,
                            orderId: oid,
                            fromPos: fpos,
                        }).catch(function (error) {
                            console.error('Unable to open add customer modal:', error);
                        });
                        return;
                    }

                    window.dispatchEvent(new CustomEvent('add-customer-modal-open'));
                };
            })();
        </script>

    @endif


    @if (App::environment('codecanyon') && pusherSettings()->beamer_status)
        <!-- include SDK right before using it so browser downloads and executes it synchronously -->
        <script src="https://js.pusher.com/beams/2.1.0/push-notifications-cdn.js"></script>

        <script>
            window.__beamsRegisterCurrentUser = async function () {
                const currentUserId = "{{ Str::slug(global_setting()->name) }}-{{ auth()->id() }}";

                if (typeof PusherPushNotifications === 'undefined') {
                    console.warn('Pusher Beams SDK not available yet, skipping initialization');
                    return;
                }

                const beamsClient = new PusherPushNotifications.Client({
                    instanceId: "{{ pusherSettings()->instance_id }}",
                });

                const beamsTokenProvider = new PusherPushNotifications.TokenProvider({
                    url: "{{ route('beam_auth') }}",
                });

                await beamsClient.start();
                await beamsClient.addDeviceInterest('{{ Str::slug(global_setting()->name) }}');
                await beamsClient.setUserId(currentUserId, beamsTokenProvider);
            };
        </script>
    @endif

    <script>
        function getFullscreenElement() {
            // Must use <html>: Livewire wire:navigate replaces document.body entirely
            // (see swapCurrentPageWithNewHtml), which ends fullscreen if the target was <body>.
            return document.documentElement;
        }

        function isDocumentFullscreen() {
            return !!(document.fullscreenElement ||
                document.webkitFullscreenElement ||
                document.msFullscreenElement ||
                document.mozFullScreenElement);
        }

        /** After SPA navigation, wait two frames so the DOM is stable before re-requesting fullscreen. */
        function afterNextPaint(callback) {
            if (typeof requestAnimationFrame === 'function') {
                requestAnimationFrame(function () {
                    requestAnimationFrame(callback);
                });
            } else {
                setTimeout(callback, 0);
            }
        }

        var fullscreenExitPersistTimer = null;
        var FULLSCREEN_EXIT_PERSIST_MS = 450;

        function openFullscreen() {
            var elem = getFullscreenElement();

            if (!isDocumentFullscreen()) {
                // Enter fullscreen
                if (elem.requestFullscreen) {
                    elem.requestFullscreen().then(() => {
                        localStorage.setItem('fullscreen-enabled', 'true');
                    }).catch(err => {
                        console.error('Error entering fullscreen:', err);
                        localStorage.setItem('fullscreen-enabled', 'false');
                    });
                } else if (elem.webkitRequestFullscreen) {
                    /* Safari */
                    elem.webkitRequestFullscreen();
                    localStorage.setItem('fullscreen-enabled', 'true');
                } else if (elem.webkitEnterFullscreen) {
                    /* iOS Safari */
                    elem.webkitEnterFullscreen();
                    localStorage.setItem('fullscreen-enabled', 'true');
                } else if (elem.msRequestFullscreen) {
                    /* IE11 */
                    elem.msRequestFullscreen();
                    localStorage.setItem('fullscreen-enabled', 'true');
                } else if (elem.mozRequestFullScreen) {
                    /* Firefox */
                    elem.mozRequestFullScreen();
                    localStorage.setItem('fullscreen-enabled', 'true');
                }
            } else {
                // Exit fullscreen
                if (document.exitFullscreen) {
                    document.exitFullscreen().then(() => {
                        localStorage.setItem('fullscreen-enabled', 'false');
                    }).catch(err => {
                        console.error('Error exiting fullscreen:', err);
                    });
                } else if (document.webkitExitFullscreen) {
                    /* Safari */
                    document.webkitExitFullscreen();
                    localStorage.setItem('fullscreen-enabled', 'false');
                } else if (document.webkitCancelFullScreen) {
                    /* iOS Safari */
                    document.webkitCancelFullScreen();
                    localStorage.setItem('fullscreen-enabled', 'false');
                } else if (document.msExitFullscreen) {
                    /* IE11 */
                    document.msExitFullscreen();
                    localStorage.setItem('fullscreen-enabled', 'false');
                } else if (document.mozCancelFullScreen) {
                    /* Firefox */
                    document.mozCancelFullScreen();
                    localStorage.setItem('fullscreen-enabled', 'false');
                }
            }
        }

        // Restore fullscreen state on page load
        function restoreFullscreen() {
            const fullscreenEnabled = localStorage.getItem('fullscreen-enabled');
            if (fullscreenEnabled === 'true') {
                if (isDocumentFullscreen()) {
                    return;
                }
                var elem = getFullscreenElement();
                afterNextPaint(function () {
                    if (isDocumentFullscreen()) {
                        return;
                    }
                    elem = getFullscreenElement();
                    if (elem && elem.requestFullscreen) {
                        elem.requestFullscreen().catch(err => {
                            // Chrome blocks automatic fullscreen without user interaction
                            // This is expected behavior - user needs to click the button
                            console.log('Fullscreen restoration requires user interaction:', err.message);
                            // Don't clear the preference, let user manually toggle if needed
                        });
                    } else if (elem && elem.webkitRequestFullscreen) {
                        elem.webkitRequestFullscreen();
                    } else if (elem && elem.msRequestFullscreen) {
                        elem.msRequestFullscreen();
                    } else if (elem && elem.mozRequestFullScreen) {
                        elem.mozRequestFullScreen();
                    }
                });
            }
        }

        // Listen for fullscreen changes (e.g., user presses ESC)
        function handleFullscreenChange() {
            if (isDocumentFullscreen()) {
                if (fullscreenExitPersistTimer) {
                    clearTimeout(fullscreenExitPersistTimer);
                    fullscreenExitPersistTimer = null;
                }
                localStorage.setItem('fullscreen-enabled', 'true');
                return;
            }
            // Avoid clearing preference on brief exits (e.g. Livewire wire:navigate) before restore runs.
            if (fullscreenExitPersistTimer) {
                clearTimeout(fullscreenExitPersistTimer);
            }
            fullscreenExitPersistTimer = setTimeout(function () {
                fullscreenExitPersistTimer = null;
                if (!isDocumentFullscreen()) {
                    localStorage.setItem('fullscreen-enabled', 'false');
                }
            }, FULLSCREEN_EXIT_PERSIST_MS);
        }

        // Set up event listeners
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', restoreFullscreen);
        } else {
            // DOM already loaded
            restoreFullscreen();
        }

        document.addEventListener('fullscreenchange', handleFullscreenChange);
        document.addEventListener('webkitfullscreenchange', handleFullscreenChange);
        document.addEventListener('msfullscreenchange', handleFullscreenChange);
        document.addEventListener('mozfullscreenchange', handleFullscreenChange);

        // Also restore on Livewire navigation (without an extra delay — restore waits two rAF frames)
        document.addEventListener('livewire:navigated', restoreFullscreen);
    </script>

    <script>
        function hideNotificationIfResponded() {
            const permission = Notification.permission;
            if (permission === 'granted' || permission === 'denied') {
                const alertBox = document.getElementById('notification-alert');
                if (alertBox) {
                    alertBox.style.display = 'none';
                }
            }
        }

        document.addEventListener('DOMContentLoaded', async () => {
            hideNotificationIfResponded();
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register("{{ asset('service-worker.js') }}")
                    .then(registration => console.log("Service Worker registered:", registration))
                    .catch(error => console.error("Service Worker registration failed:", error));
            }

            // If notifications are already granted, try to register this device for Beams.
            try {
                if (Notification.permission === 'granted' && typeof window.__beamsRegisterCurrentUser === 'function') {
                    await window.__beamsRegisterCurrentUser();
                    console.log('✅ Beams registration complete (permission already granted)');
                }
            } catch (e) {
                console.error('Beams registration failed:', e);
            }
        });

        document.addEventListener('livewire:navigated', () => {
            hideNotificationIfResponded();
        });

        document.addEventListener('click', async (e) => {
            if (e.target && e.target.id === 'subscribe-button') {
                if ('Notification' in window && 'serviceWorker' in navigator) {
                    const permission = await Notification.requestPermission();

                    localStorage.setItem('notificationPermission', permission);

                    hideNotificationIfResponded();

                    if (permission !== 'granted') {
                        console.warn("Push notifications permission denied.");
                        return;
                    }
                    try {
                        const registration = await navigator.serviceWorker.register("{{ asset('service-worker.js') }}");
                        console.log("Service Worker registered:", registration);
                        subscribeUserToPush(registration);

                        // Also register with Pusher Beams (user-targeted browser push).
                        if (typeof window.__beamsRegisterCurrentUser === 'function') {
                            await window.__beamsRegisterCurrentUser();
                            console.log('✅ Beams registration complete (after subscribe click)');
                        }
                    } catch (error) {
                        console.error("Service Worker registration failed:", error);
                    }
                } else if ('safari' in window && 'pushNotification' in window.safari) {
                    handleSafariPush();
                } else {
                    console.error("Push notifications are not supported in this browser.");
                }
            }
        });
        async function subscribeUserToPush(registration) {
            try {
                const applicationServerKey = "{{ global_setting()->vapid_public_key }}";

                const subscription = await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: applicationServerKey
                });

                console.log("Push Subscription:", subscription);

                await fetch("/subscribe", {
                    method: "POST",
                    body: JSON.stringify(subscription),
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    }
                });

                console.log("Push subscription saved on the server.");
            } catch (error) {
                console.error("Subscription error:", error);
            }
        }

        function handleSafariPush() {
            const permissionData = window.safari.pushNotification.permission("{{ config('app.safari_push_id') }}");

            if (permissionData.permission === "default") {
                window.safari.pushNotification.requestPermission(
                    "https://yourdomain.com",
                    "{{ config('app.safari_push_id') }}",
                    {},
                    (permission) => {
                        localStorage.setItem('notificationPermission', permission.permission);
                        hideNotificationIfResponded();
                        console.log("Safari push permission:", permission);
                    }
                );
            } else {
                localStorage.setItem('notificationPermission', permissionData.permission);
                hideNotificationIfResponded();
                console.log("Safari push subscription:", permissionData.deviceToken);
            }
        }
    </script>

    @include('sections.pusher-script')

    @include('layouts.service-worker-js')
    @stack('scripts')
    <script src="{{ asset('vendor/trix/trix.umd.min.js') }}"></script>

    <!-- Print Image Handler -->
    <script src="https://cdn.jsdelivr.net/npm/html-to-image@1.11.11/dist/html-to-image.min.js" data-navigate-track></script>
    <script src="{{ asset('js/print-image-handler.js') }}" data-navigate-track></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="{{ asset('vendor/pikaday.js') }}"></script>




</body>
</html>
