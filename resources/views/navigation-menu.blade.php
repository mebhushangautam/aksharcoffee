<nav @class([
    'fixed w-full',
    // POS: cart column is z-40 (layouts/app.blade.php); nav must stack above it or modals/dropdowns in the bar stay behind the cart.
    'z-50' => request()->routeIs('pos.*'),
    'z-30' => !request()->routeIs('pos.*'),
    // POS + lg: transparent nav over the right column so the fixed cart (full viewport height) shows through; menu column gets its own strip below.
    'bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700 lg:bg-transparent lg:border-transparent lg:shadow-none lg:dark:bg-transparent lg:dark:border-transparent' => request()->routeIs('pos.*'),
    'bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700' => !request()->routeIs('pos.*'),
    // POS lg+: <nav> defaults to pointer-events:auto, so empty bar width over the cart still ate clicks; none here + auto on the left cluster lets hits reach #order-items-container (z-40) below z-50 nav.
    'lg:pointer-events-none' => request()->routeIs('pos.*'),
])>
  @if (request()->routeIs('pos.*'))
    <div class="pointer-events-none absolute inset-x-0 top-0 bottom-0 z-0 hidden lg:block" aria-hidden="true">
      <div class="absolute inset-y-0 start-0 w-8/12 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700"></div>
    </div>
  @endif
  <div @class([
    'px-3 lg:px-5 lg:pl-3',
    'py-2 md:py-2' => request()->routeIs('pos.*'),
    'py-3 md:py-2' => !request()->routeIs('pos.*'),
    'relative z-10' => request()->routeIs('pos.*'),
    'lg:pointer-events-none' => request()->routeIs('pos.*'),
  ])>
    <div @class([
      'flex items-center justify-between',
      'lg:pointer-events-none' => request()->routeIs('pos.*'),
    ])>

      <div @class([
        'flex items-center justify-start',
        'lg:pointer-events-auto' => request()->routeIs('pos.*'),
      ])>
        @if (!request()->routeIs('pos.*'))
          <button id="toggleSidebarMobile" aria-expanded="true" aria-controls="sidebar"
            class="p-2 text-gray-600 rounded cursor-pointer lg:hidden hover:text-gray-900 hover:bg-gray-100 focus:bg-gray-100 dark:focus:bg-gray-700 focus:ring-2 focus:ring-gray-100 dark:focus:ring-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
            <svg id="toggleSidebarMobileHamburger" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
              xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd"
                d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                clip-rule="evenodd"></path>
            </svg>
            <svg id="toggleSidebarMobileClose" class="hidden w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
              xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd"
                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                clip-rule="evenodd"></path>
            </svg>
          </button>
        @endif

        <a href="{{ route('dashboard') }}" class="flex ltr:ml-2 rtl:mr-2 items-center app-logo gap-2 min-w-fit">
          <x-restaurant-logo class="h-7 w-7 ltr:mr-1 sm:ltr:mr-2 rtl:ml-1 sm:rtl:ml-2" />

          @if (restaurant()->show_logo_text)
          <span class="self-center text-md font-semibold whitespace-nowrap dark:text-white hidden lg:block ltr:mr-2 rtl:ml-2 truncate max-w-[45vw]">{{ Str::limit(restaurant()->name, 10) }}</span>
          @endif
        </a>

        @if (!request()->routeIs('pos.*'))
        <button id="toggle-sidebar" type="button" class="lg:inline-flex items-center p-2 text-sm text-gray-500 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600 mx-2 hidden">
          <!-- Menu expand icon (shows when sidebar is collapsed) -->
          <svg id="toggle-sidebar-open" class="hidden w-6 h-6 transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
          </svg>
          <!-- Menu collapse icon (shows when sidebar is expanded) -->
          <svg id="toggle-sidebar-close" class=" w-6 h-6 transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7M19 19l-7-7 7-7"/>
          </svg>
         </button>
         @endif

        <div class="flex flex-wrap items-center gap-2">
          @if (in_array('Order', restaurant_modules()) && (request()->routeIs('pos.*') || (user_can('Show Order') && restaurant()->hide_new_orders == 0)))
            @livewire('dashboard.todayOrders')
          @endif

          @if (in_array('Reservation', restaurant_modules()) && user_can('Show Reservation') && restaurant()->hide_new_reservations == 0 && in_array('Table Reservation', restaurant_modules()))
            @livewire('dashboard.todayReservations')
          @endif

          @if (in_array('Waiter Request', restaurant_modules()) && user_can('Manage Waiter Request') && restaurant()->hide_new_waiter_request == 0)
            @livewire('dashboard.activeWaiterRequests')
          @endif

          @if (!request()->routeIs('pos.*'))
            @livewire('dashboard.posShortCut')
          @endif

          @if (in_array('Table', restaurant_modules()) && user_can('Show Table') && !request()->routeIs('tables.*'))
          <div>
            <div class="relative">
              <a href="{{ route('tables.index') }}" wire:navigate class="inline-flex items-center px-2 py-1 gap-1 text-sm font-medium text-center  bg-skin-base text-white border border-skin-base rounded-md ltr:mr-2 rtl:ml-2 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-gray-800 dark:text-gray-300 hover:bg-skin-base/[.8] dark:hover:text-white">
                  <svg fill="currentColor" class="h-3.5 w-3.5 shrink-0 transition duration-75 group-hover:text-gray-900 dark:text-gray-200  dark:group-hover:text-white" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 44.999 44.999" xml:space="preserve"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <g> <path d="M42.558,23.378l2.406-10.92c0.18-0.816-0.336-1.624-1.152-1.803c-0.816-0.182-1.623,0.335-1.802,1.151l-2.145,9.733 h-9.647c-0.835,0-1.512,0.677-1.512,1.513c0,0.836,0.677,1.513,1.512,1.513h0.573l-3.258,7.713 c-0.325,0.771,0.034,1.657,0.805,1.982c0.19,0.081,0.392,0.12,0.588,0.12c0.59,0,1.15-0.348,1.394-0.925l2.974-7.038l4.717,0.001 l2.971,7.037c0.327,0.77,1.215,1.127,1.982,0.805c0.77-0.325,1.13-1.212,0.805-1.982l-3.257-7.713h0.573 C41.791,24.564,42.403,24.072,42.558,23.378z"></path> <path d="M14.208,24.564h0.573c0.835,0,1.512-0.677,1.512-1.513c0-0.836-0.677-1.513-1.512-1.513H5.134L2.99,11.806 C2.809,10.99,2,10.472,1.188,10.655c-0.815,0.179-1.332,0.987-1.152,1.803l2.406,10.92c0.153,0.693,0.767,1.187,1.477,1.187h0.573 L1.234,32.28c-0.325,0.77,0.035,1.655,0.805,1.98c0.768,0.324,1.656-0.036,1.982-0.805l2.971-7.037l4.717-0.001l2.972,7.038 c0.244,0.577,0.804,0.925,1.394,0.925c0.196,0,0.396-0.039,0.588-0.12c0.77-0.325,1.13-1.212,0.805-1.98L14.208,24.564z"></path> <path d="M24.862,31.353h-0.852V18.308h8.13c0.835,0,1.513-0.677,1.513-1.512s-0.678-1.513-1.513-1.513H12.856 c-0.835,0-1.513,0.678-1.513,1.513c0,0.834,0.678,1.512,1.513,1.512h8.13v13.045h-0.852c-0.835,0-1.512,0.679-1.512,1.514 s0.677,1.513,1.512,1.513h4.728c0.837,0,1.514-0.678,1.514-1.513S25.699,31.353,24.862,31.353z"></path> </g> </g> </g></svg>
                <span class="hidden lg:block">@lang('menu.tables')</span>
              </a>
            </div>
            </div>
          @endif

          @if (in_array('Table', restaurant_modules()) && user_can('Create Order'))

            <div x-data="recentOrdersModal()" @keydown.escape.window="closeRecentOrdersModal()">
              <button type="button" @click="openRecentOrdersModal()" class="inline-flex items-center px-2 py-1 gap-1 text-sm font-medium text-center  bg-skin-base text-white border border-skin-base rounded-md ltr:mr-2 rtl:ml-2 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-gray-800 dark:text-gray-300 hover:bg-skin-base/[.8] dark:hover:text-white">
                <svg class="h-3.5 w-3.5 shrink-0 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" xml:space="preserve"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round"></g><path d="M476.554 371.269H35.446C15.87 371.269 0 387.138 0 406.716c0 19.577 15.87 35.446 35.446 35.446h441.108c19.577 0 35.446-15.869 35.446-35.446 0-19.578-15.869-35.447-35.446-35.447M278.716 133.777c8.1-6.623 13.384-16.561 13.384-27.838 0-19.938-16.161-36.1-36.1-36.1-19.938 0-36.1 16.162-36.1 36.1 0 11.277 5.285 21.216 13.384 27.838-108.954 11.354-193.9 103.47-193.9 215.423h433.231c.001-111.953-84.946-204.069-193.899-215.423M164.908 313.754H94.523c0-70.668 53.2-128.822 121.716-136.823z" style="fill:currentColor"></path></svg>
                <span class="hidden lg:block">@lang('modules.dashboard.recentOrders')</span>
              </button>

              <div x-show="showRecentOrdersModal" x-cloak x-transition.opacity class="fixed inset-0 z-[70] flex items-center justify-center p-4" style="display: none;">
                <div class="absolute inset-0 bg-black/50" @click="closeRecentOrdersModal()"></div>

                <div class="relative w-full max-w-3xl overflow-hidden rounded-xl border border-gray-200 bg-white shadow-2xl dark:border-gray-700 dark:bg-gray-800">
                  <div class="flex items-start justify-between gap-4 border-b border-gray-200 px-6 py-5 dark:border-gray-700">
                    <div>
                      <h3 class="text-lg font-medium text-gray-900 dark:text-white">@lang('modules.dashboard.recentOrders')</h3>
                      <p class="text-xs text-gray-500 dark:text-gray-400">Showing latest 10 of <span x-text="recentOrders.length"></span> orders.</p>
                      <p class="text-xs text-gray-500 dark:text-gray-400">
                        To view all orders, <a href="{{ route('orders.index') }}" wire:navigate class="text-skin-base hover:underline" @click="closeRecentOrdersModal()">click here</a>. You will be redirected to the Orders page.
                      </p>
                    </div>
                    <a href="{{ route('orders.index') }}" wire:navigate class="inline-flex items-center rounded-md border border-skin-base px-4 py-1.5 text-sm font-medium text-skin-base hover:bg-skin-base hover:text-white" @click="closeRecentOrdersModal()">
                      {{ __('app.view') }} {{ __('menu.orders') }}
                    </a>
                  </div>

                  <div class="max-h-[60vh] overflow-y-auto">
                    <template x-if="loadingRecentOrders">
                      <div class="space-y-2 p-4">
                        <div class="h-11 animate-pulse rounded-md bg-gray-100 dark:bg-gray-700"></div>
                        <div class="h-11 animate-pulse rounded-md bg-gray-100 dark:bg-gray-700"></div>
                        <div class="h-11 animate-pulse rounded-md bg-gray-100 dark:bg-gray-700"></div>
                      </div>
                    </template>

                    <template x-if="!loadingRecentOrders && recentOrdersError">
                      <p class="p-6 text-center text-sm text-gray-500 dark:text-gray-400" x-text="recentOrdersError"></p>
                    </template>

                    <template x-if="!loadingRecentOrders && !recentOrdersError && recentOrders.length === 0">
                      <p class="p-6 text-center text-sm text-gray-500 dark:text-gray-400">@lang('messages.noRecordFound')</p>
                    </template>

                    <template x-if="!loadingRecentOrders && !recentOrdersError && recentOrders.length > 0">
                      <div>
                        <template x-for="order in recentOrders" :key="order.uuid">
                          <div class="border-b border-gray-200 dark:border-gray-700">
                            <button type="button" class="flex w-full items-center gap-3 px-6 py-3 text-left hover:bg-gray-50 dark:hover:bg-gray-700/40" @click="toggleRecentOrderDetails(order.uuid)">
                              <svg class="h-3 w-3 shrink-0 text-gray-500 transition-transform duration-200" :class="expandedOrderUuid === order.uuid ? 'rotate-90' : ''" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M7 5l6 5-6 5V5z"/>
                              </svg>
                              <span class="min-w-[90px] text-sm font-medium text-gray-900 dark:text-white" x-text="order.order_number"></span>
                              <span class="flex-1 truncate text-xs text-gray-500 dark:text-gray-400" x-text="order.customer_name"></span>
                              <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-[11px] font-medium" :class="order.status_badge_class" x-text="order.status_label"></span>
                              <span class="min-w-[74px] text-right text-sm font-medium text-gray-900 dark:text-white" x-text="order.total"></span>
                              <span class="min-w-[72px] text-right text-xs text-gray-500 dark:text-gray-400" x-text="order.date_label"></span>
                              <span class="min-w-[44px] text-right">
                                <span class="text-sm text-skin-base hover:underline" @click.stop="handleRecentOrderView(order)">@lang('app.view')</span>
                              </span>
                            </button>

                            <div x-show="expandedOrderUuid === order.uuid" style="display: none;" class="grid grid-cols-1 gap-x-3 gap-y-2 bg-gray-50 px-11 py-3 text-xs sm:grid-cols-3 dark:bg-gray-900/30">
                              <p><span class="text-gray-500 dark:text-gray-400">@lang('modules.order.orderTypeLabel')</span><br><span class="text-gray-900 dark:text-white" x-text="order.order_type_label"></span></p>
                              <p><span class="text-gray-500 dark:text-gray-400">@lang('modules.order.paymentStatus')</span><br><span class="text-gray-900 dark:text-white" x-text="order.payment_status_label"></span></p>
                              <p><span class="text-gray-500 dark:text-gray-400">@lang('modules.order.totalItem')</span><br><span class="text-gray-900 dark:text-white" x-text="order.items_count"></span></p>
                              <p x-show="order.order_type !== 'delivery' && order.order_type !== 'pickup'" x-cloak><span class="text-gray-500 dark:text-gray-400">@lang('modules.settings.tableNumber')</span><br><span class="text-gray-900 dark:text-white" x-text="order.table_label"></span></p>
                              <p><span class="text-gray-500 dark:text-gray-400">@lang('modules.order.waiter')</span><br><span class="text-gray-900 dark:text-white" x-text="order.waiter_name"></span></p>
                              <p x-show="order.order_type === 'delivery'" x-cloak><span class="text-gray-500 dark:text-gray-400">@lang('modules.order.deliveryExecutive')</span><br><span class="text-gray-900 dark:text-white" x-text="order.delivery_executive_name"></span></p>
                              <p><span class="text-gray-500 dark:text-gray-400">@lang('app.date')</span><br><span class="text-gray-900 dark:text-white" x-text="order.created_at_label"></span></p>
                            </div>
                          </div>
                        </template>
                      </div>
                    </template>
                  </div>

                  <div class="flex items-center justify-between border-t border-gray-200 px-6 py-3 dark:border-gray-700">
                    <p class="text-xs text-gray-500 dark:text-gray-400">@lang('modules.order.totalOrder'): <span class="font-semibold text-gray-900 dark:text-white" x-text="recentOrders.length"></span></p>
                    <button type="button" @click="closeRecentOrdersModal()" class="inline-flex items-center rounded-md border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
                      @lang('app.close')
                    </button>
                  </div>
                </div>
              </div>
            </div>
          @endif

          @if (request()->routeIs('pos.*'))
            <a href="{{ route('dashboard') }}" wire:navigate class="inline-flex h-8 shrink-0 items-center justify-center gap-1 rounded-md border border-skin-base bg-skin-base px-2 text-xs font-medium text-white hover:bg-skin-base/[.8] focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-gray-800 dark:text-gray-300">
              <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="h-3.5 w-3.5 shrink-0" viewBox="0 0 16 16">
                <path d="M8 2a.5.5 0 0 1 .5.5V4a.5.5 0 0 1-1 0V2.5A.5.5 0 0 1 8 2M3.732 3.732a.5.5 0 0 1 .707 0l.915.914a.5.5 0 1 1-.708.708l-.914-.915a.5.5 0 0 1 0-.707M2 8a.5.5 0 0 1 .5-.5h1.586a.5.5 0 0 1 0 1H2.5A.5.5 0 0 1 2 8m9.5 0a.5.5 0 0 1 .5-.5h1.5a.5.5 0 0 1 0 1H12a.5.5 0 0 1-.5-.5m.754-4.246a.39.39 0 0 0-.527-.02L7.547 7.31A.91.91 0 1 0 8.85 8.569l3.434-4.297a.39.39 0 0 0-.029-.518z"/>
                <path fill-rule="evenodd" d="M6.664 15.889A8 8 0 1 1 9.336.11a8 8 0 0 1-2.672 15.78zm-4.665-4.283A11.95 11.95 0 0 1 8 10c2.186 0 4.236.585 6.001 1.606a7 7 0 1 0-12.002 0"/>
              </svg>
              <span class="hidden lg:block">@lang('menu.dashboard')</span>
            </a>

            @include('pos.partials.header-more-dropdown')
          @endif

        </div>
      </div>

      @unless (request()->routeIs('pos.*'))
      <div class="flex items-center gap-1 sm:gap-2 md:gap-4 w-fit justify-end">

        <!-- Search mobile -->
        @if (!request()->routeIs('pos.*'))
          <button id="toggleSidebarMobileSearch" type="button"
            class="p-2 text-gray-500 rounded-lg hidden hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
            <span class="sr-only">Search</span>
            <!-- Search icon -->
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd"
                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                clip-rule="evenodd"></path>
            </svg>
          </button>
        @endif


        @if (languages()->count() > 1)
         @livewire('settings.languageSwitcher')
        @endif

        @if (request()->routeIs('pos.*') && function_exists('module_enabled') && module_enabled('MultiPOS') && in_array('MultiPOS', restaurant_modules()))
            @includeIf('multipos::partials.pos-active-header-chip')
        @endif

        @livewire('restaurant.stop-impersonate-restaurant')

        @livewire('restaurant.restaurantOpenCloseToggle')

        @if (restaurant()->package->package_type == \App\Enums\PackageType::DEFAULT)
            @php $upgradeText = __('modules.settings.upgradeLicense'); @endphp
            <a href="{{ route('pricing.plan') }}" wire:navigate class="hidden md:block inline-flex" data-tooltip-target="upgrade-tooltip-toggle" data-tooltip-placement="bottom">
                <x-secondary-button class="inline-flex items-center gap-2 shadow-md text-skin-base dark:text-skin-base hover:origin-center group px-2 sm:px-3" aria-label="{{ $upgradeText }}">
                    <svg class="w-5 h-5 text-current group-hover:scale-110 duration-500 sm:hidden" width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="currentColor">
                        <path d="M3 7a2 2 0 0 1 2-2h2.586l1.707-1.707A1 1 0 0 1 10 3h4a1 1 0 0 1 .707.293L16.414 5H19a2 2 0 0 1 2 2v2H3V7Zm0 4h18v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-7Zm8 2v4a1 1 0 1 0 2 0v-4a1 1 0 1 0-2 0Z"/>
                    </svg>
                    <span class="hidden sm:inline">{{ $upgradeText }}</span>
                </x-secondary-button>
            </a>
            <div id="upgrade-tooltip-toggle" role="tooltip"
                class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip">
                {{ $upgradeText }}
                <div class="tooltip-arrow" data-popper-arrow></div>
            </div>
        @elseif (restaurant()->package->package_type == \App\Enums\PackageType::TRIAL)
            @php
                $daysLeftInTrial = floor(now(timezone())->diffInDays(\Carbon\Carbon::parse(restaurant()->trial_ends_at)->addDays(1)));
                $trialText = $daysLeftInTrial > 0 ? $daysLeftInTrial .' ' . __('modules.package.daysLeftTrial') : __('modules.package.trialExpired');
            @endphp
            <a href="{{ route('pricing.plan') }}" wire:navigate class="hidden md:block inline-flex" data-tooltip-target="trial-tooltip-toggle" data-tooltip-placement="bottom">
                <button aria-label="{{ $trialText }}">
                    <svg class="w-5 h-5 text-current group-hover:scale-110 duration-500 sm:hidden" width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="currentColor">
                        <path d="M3 7a2 2 0 0 1 2-2h2.586l1.707-1.707A1 1 0 0 1 10 3h4a1 1 0 0 1 .707.293L16.414 5H19a2 2 0 0 1 2 2v2H3V7Zm0 4h18v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-7Zm8 2v4a1 1 0 1 0 2 0v-4a1 1 0 1 0-2 0Z"/>
                    </svg>
                    <span class="hidden sm:inline text-xs px-3 py-1.5 rounded-full font-medium bg-amber-50 text-amber-700 border border-amber-200">{{ $trialText }}</span>
                </button>
            </a>
            <div id="trial-tooltip-toggle" role="tooltip"
                class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip">
                {{ $trialText }}
                <div class="tooltip-arrow" data-popper-arrow></div>
            </div>
        @endif

        <button onclick="openFullscreen();" type="button" data-tooltip-target="fullscreen-tooltip-toggle"
        class="hidden md:block text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-fullscreen" viewBox="0 0 16 16">
            <path d="M1.5 1a.5.5 0 0 0-.5.5v4a.5.5 0 0 1-1 0v-4A1.5 1.5 0 0 1 1.5 0h4a.5.5 0 0 1 0 1zM10 .5a.5.5 0 0 1 .5-.5h4A1.5 1.5 0 0 1 16 1.5v4a.5.5 0 0 1-1 0v-4a.5.5 0 0 0-.5-.5h-4a.5.5 0 0 1-.5-.5M.5 10a.5.5 0 0 1 .5.5v4a.5.5 0 0 0 .5.5h4a.5.5 0 0 1 0 1h-4A1.5 1.5 0 0 1 0 14.5v-4a.5.5 0 0 1 .5-.5m15 0a.5.5 0 0 1 .5.5v4a1.5 1.5 0 0 1-1.5 1.5h-4a.5.5 0 0 1 0-1h4a.5.5 0 0 0 .5-.5v-4a.5.5 0 0 1 .5-.5"/>
          </svg>
        </button>

        <div id="fullscreen-tooltip-toggle" role="tooltip"
          class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip">
          View in Fullscreen
          <div class="tooltip-arrow" data-popper-arrow></div>
        </div>

        <button id="theme-toggle" data-tooltip-target="tooltip-toggle" type="button"
          class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-1.5 sm:p-2.5">
          <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
            xmlns="http://www.w3.org/2000/svg">
            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
          </svg>
          <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
            xmlns="http://www.w3.org/2000/svg">
            <path
              d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
              fill-rule="evenodd" clip-rule="evenodd"></path>
          </svg>
        </button>

        <div id="tooltip-toggle" role="tooltip"
          class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip">
          @lang('app.toggleDarkMode')

          <div class="tooltip-arrow" data-popper-arrow></div>
        </div>


        <!-- Profile -->
        <div class="flex items-center w-7 sm:w-8">
          <div class="flex w-full">
            <button type="button"
              class="inline-flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600"
              id="user-menu-button-2" aria-expanded="false" data-dropdown-toggle="dropdown-2">
              <span class="sr-only">Open user menu</span>
              <img class="w-8 h-8 rounded-full" src="{{ auth()->user()->profile_photo_path ? asset_url_local_s3(auth()->user()->profile_photo_path):auth()->user()->profile_photo_url }}" alt="user photo">
            </button>
          </div>
          <!-- Dropdown menu -->
          <div
            class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded shadow dark:bg-gray-700 dark:divide-gray-600"
            id="dropdown-2">
            <div class="px-4 py-3" role="none">
              <p class="text-sm text-gray-900 dark:text-white" role="none">
                {{ auth()->user()->name }}
              </p>
              <p class="text-sm font-medium text-gray-500 truncate dark:text-gray-300" role="none">
                {{ auth()->user()->email }}
              </p>
            </div>
            <ul class="py-1" role="none">
              <li>
                <a href="{{ route('profile.show') }}" wire:navigate
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white"
                  role="menuitem">@lang('menu.profile')</a>
              </li>

              @if (user_can('Manage Settings') && in_array('Settings', restaurant_modules()))
              <li>
                <a href="{{ route('settings.index') }}" wire:navigate
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white"
                  role="menuitem">@lang('menu.settings')</a>
              </li>

              @endif

              <li>
                <form method="POST" action="{{ route('logout') }}" x-data>
                  @csrf
                  <a href="{{ route('logout') }}" @click.prevent="$root.submit();"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white"
                    role="menuitem">@lang('menu.signOut')</a>
                </form>
              </li>
            </ul>
          </div>
        </div>


        <!-- Profile -->
        <div class="hidden sm:flex items-center w-8">
          <div class="flex w-full">
            <button type="button"
              class="inline-flex items-center justify-center w-10 h-10 text-gray-500 bg-gray-100 rounded-full hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:focus:ring-gray-600"
              id="user-menu-button-3" aria-expanded="false" data-dropdown-toggle="dropdown-3" data-dropdown-placement="left-end">
              <span class="sr-only">Open user menu</span>
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" width="24" height="24" viewBox="0 0 512 512" xml:space="preserve"><path d="M112 441.328h288v32H112zM0 38.672v352h200v34.656h112v-34.656h200v-352zm216 323.25v-16h80v16zm248-35.25H48v-240h416z" style="fill:currentColor"/></svg>

            </button>
          </div>
          <!-- Dropdown menu -->
          <div
            class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded shadow dark:bg-gray-700 dark:divide-gray-600"
            id="dropdown-3">

            <ul class="py-1" role="none">
              @if (in_array('Customer Display', restaurant_modules()))
              <li>
                <a href="{{ route('customer.display') }}" target="_blank"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white"
                  role="menuitem">@lang('menu.customerDisplay')</a>
              </li>
              <li>
                <a href="{{ route('customer.order-board') }}" target="_blank"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white"
                  role="menuitem">@lang('modules.order.customerOrderBoard')</a>
              </li>
              @endif

              @if (module_enabled('Kiosk') && in_array('Kiosk', restaurant_modules()))
                <li>
                    <a href="{{ route('kiosk.restaurant', restaurant()->hash). '?branch=' . branch()->unique_hash }}" target="_blank"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white"
                    role="menuitem">@lang('kiosk::modules.menu.kiosk')</a>
                </li>
              @endif

            </ul>
          </div>
        </div>



      </div>
      @endunless

      </div>
    </div>
</nav>
