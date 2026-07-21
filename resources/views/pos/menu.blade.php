<div class="w-full h-full">
    @php
        $orderStats = getRestaurantOrderStats(branch()->id);
        $orderLimitReached = !$orderStats['unlimited'] && $orderStats['current_count'] >= $orderStats['order_limit'];
        $posMenuClientSideCatalog = (bool) ($posMenuClientSideCatalog ?? false);
        $posCategoryMeta = [];
        foreach ($categoryList ?? [] as $category) {
            $posCategoryMeta[(string) $category->id] = [
                'name' => $category->category_name,
                'sort_order' => (int) ($category->sort_order ?? $category->id),
            ];
        }
        $posEuSelectableMenuInput = restaurant()->selectableEuAllergenKeys();
        $posDietaryLabelOrder = \App\Support\DietaryLabels::keys();
        $posDietaryLabelText = [];
        $posDietaryLabelIconUrls = [];
        foreach ($posDietaryLabelOrder as $k) {
            $posDietaryLabelText[$k] = __(\App\Support\DietaryLabels::langKey($k));
            $posDietaryLabelIconUrls[$k] = \App\Support\DietaryLabels::defaultIconUrl($k);
        }
    @endphp
    <div x-data="{
        showMenu: false,
        mobileCartLineCount: 0,
        filterView: getCookie('posFilterView') ?? 'select',
        isMobilePos() {
            return window.matchMedia('(max-width: 1023px)').matches;
        },
        syncMobileMenuOpenClass() {
            document.documentElement.classList.toggle('pos-mobile-menu-open', this.showMenu && this.isMobilePos());
        },
        closeMobileMenu() {
            this.showMenu = false;
            document.documentElement.classList.remove('pos-mobile-menu-open');
        },
        toggleMenu() {
            this.showMenu = !this.showMenu;
            this.syncMobileMenuOpenClass();
            if (this.showMenu) {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },
        setFilterView(view) {
            this.filterView = view;
            setCookie('posFilterView', view, 30);
        }
    }"
        x-init='window.addEventListener("pos-cart-summary-sync", function (e) { var d = e.detail || {}; var c = d.count; mobileCartLineCount = Number(c) || 0; })'
        @resize.window="if (!isMobilePos()) { document.documentElement.classList.remove('pos-mobile-menu-open') } else { syncMobileMenuOpenClass() }"
        @pos-focus-cart.window="closeMobileMenu()">
        {{-- Mobile: cart bar + menu FAB side by side (no overlap on narrow screens). --}}
        {{-- pointer-events-none on the full-width row so cart action buttons underneath stay tappable; only FAB/cart chip capture touches. --}}
        <div class="pointer-events-none fixed bottom-[max(1.5rem,env(safe-area-inset-bottom,0px))] start-3 end-3 z-[48] print:hidden md:hidden">
            <div class="pointer-events-none mx-auto flex w-full max-w-full items-end gap-2"
                :class="showMenu && mobileCartLineCount > 0 ? '' : 'justify-end'">
                <div
                    id="pos-mobile-cart-summary-wrap"
                    x-show="showMenu && mobileCartLineCount > 0"
                    x-cloak
                    class="min-w-0 flex-1 pointer-events-auto"
                    role="region"
                    aria-label="{{ __('modules.order.viewCart') }}">
                <button type="button" id="pos-mobile-cart-summary"
                    class="w-full cursor-pointer touch-manipulation rounded-2xl border border-gray-200/90 bg-white/95 p-1.5 text-start shadow-2xl ring-1 ring-black/5 backdrop-blur-md transition hover:bg-white dark:border-gray-600 dark:bg-gray-900/95 dark:ring-white/10 dark:hover:bg-gray-900"
                    aria-controls="order-items-container">
                    <div class="flex items-center justify-between gap-2 rounded-full bg-skin-base px-3 py-2.5 text-white shadow-md ring-1 ring-black/10 dark:ring-white/15 sm:gap-3 sm:px-4">
                        <span id="pos-mobile-cart-summary-text" class="min-w-0 flex-1 truncate text-sm font-semibold tabular-nums tracking-tight"></span>
                        <span class="inline-flex shrink-0 items-center gap-1 text-xs font-medium text-white/95">
                            <span class="max-[360px]:sr-only">@lang('modules.order.viewCart')</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                            </svg>
                        </span>
                    </div>
                </button>
                </div>
                <button
                    type="button"
                    @click="toggleMenu()"
                    class="relative flex h-14 w-14 shrink-0 pointer-events-auto items-center justify-center rounded-full bg-skin-base text-white shadow-lg transition focus:outline-none focus:ring-2 focus:ring-skin-base focus:ring-offset-2 touch-manipulation"
                    aria-label="{{ __('menu.menu') }}">
                    <span id="pos-menu-fab-cart-badge" class="pointer-events-none absolute -top-0.5 -end-0.5 hidden min-h-[1.125rem] min-w-[1.125rem] items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold leading-none text-white ring-2 ring-skin-base" aria-hidden="true">0</span>
                    <svg x-show="!showMenu" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    <svg x-show="showMenu" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Menu Panel (mobile: fixed overlay above cart when open) -->
        <div
            :class="showMenu
                ? 'max-lg:fixed max-lg:inset-x-0 max-lg:top-16 max-lg:bottom-0 max-lg:z-[45] max-lg:flex max-lg:flex-col max-lg:overflow-y-auto max-lg:pb-[calc(5.5rem+env(safe-area-inset-bottom,0px))]'
                : 'max-lg:hidden'"
            class="md:flex flex-col bg-gray-50 lg:h-full w-full ps-1 dark:bg-gray-900 transition-transform duration-300 md:static md:inset-auto md:z-auto md:translate-x-0 overflow-y-auto md:overflow-visible md:max-h-none"
            style="backdrop-filter: blur(2px);"
            x-cloak>
            {{-- Search + Filters --}}
            <div class="bg-white/70 dark:bg-gray-800/70 rounded-xl border border-gray-100 dark:border-gray-700 px-3 py-2 shadow-sm space-y-2">
                <div class="flex flex-col lg:flex-row lg:items-center gap-3">
                    <div class="flex-1 order-2 lg:order-1">
                        <form action="#" method="GET" onsubmit="event.preventDefault(); return false;">
                            <label for="products-search" class="sr-only">Search</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                    </svg>
                                </div>
                                <x-input id="products-search" class="block w-full pl-10 pr-12 py-2 border-gray-200 rounded-lg text-sm focus:ring-skin-base focus:border-skin-base" type="text"
                                    placeholder="{{ __('placeholders.searchMenuItems') }}"
                                    value="{{ $search }}" />
                                <button
                                    id="products-search-clear"
                                    type="button"
                                    onclick="clearSearch()"
                                    class="absolute inset-y-0 right-2 z-20 my-auto inline-flex h-7 w-7 items-center justify-center rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white {{ empty($search) ? 'hidden' : '' }}"
                                    aria-label="{{ __('app.reset') }}"
                                >
                                    <svg class="w-4 h-4" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 20 4 4m16 0L4 20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="flex items-center justify-between gap-2 order-1 lg:order-2">

                        <div class="flex items-center gap-2">
                            <div class="flex items-center gap-1 bg-gray-100 dark:bg-gray-800 rounded-md p-1">
                                <button type="button"
                                    @click="setFilterView('select')"
                                    :aria-pressed="filterView === 'select'"
                                    :class="filterView === 'select' ? 'bg-skin-base text-white shadow-sm' : 'text-gray-700 dark:text-gray-200'"
                                    class="px-2 py-1 rounded-md transition"
                                    aria-label="@lang('app.dropdown')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                                    </svg>
                                </button>
                                <button type="button"
                                    @click="setFilterView('grid')"
                                    :aria-pressed="filterView === 'grid'"
                                    :class="filterView === 'grid' ? 'bg-skin-base text-white shadow-sm' : 'text-gray-700 dark:text-gray-200'"
                                    class="px-2 py-1 rounded-md transition"
                                    aria-label="@lang('app.grid')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4h7v7H4zM13 4h7v7h-7zM4 13h7v7H4zM13 13h7v7h-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <button type="button"
                            onclick="if (typeof window.startNewOrder === 'function') { window.startNewOrder(); } else if (typeof window.resetPosState === 'function') { window.resetPosState(); }"
                            class="inline-flex items-center px-3 py-1.5 gap-1 text-sm whitespace-nowrap rounded-md bg-skin-base text-white hover:bg-skin-base/90 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2z" />
                                <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466" />
                            </svg>
                            @lang('modules.order.newOrder')
                        </button>
                        <button
                            type="button"
                            id="pos-clear-cache-btn"
                            class="hidden inline-flex items-center px-3 py-1.5 gap-1 text-sm whitespace-nowrap rounded-md bg-red-600 text-white hover:bg-red-700 transition"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0A.5.5 0 0 1 8.5 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 1 1 0-2H5a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1h2.5a1 1 0 0 1 1 1M6 2v1h4V2zM4 4v9a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4z"/>
                            </svg>
                            @lang('messages.posClearCache')
                        </button>
                    </div>
                </div>

                @php
                    $posMenuFiltersInline = filter_var($posMenuFiltersInline ?? false, FILTER_VALIDATE_BOOLEAN);
                @endphp
                <div>
                    {{-- Dropdown view: side-by-side only on JS /pos when posMenuFiltersInline is true --}}
                    <template x-if="filterView === 'select'">
                        <div class="{{ $posMenuFiltersInline ? 'grid grid-cols-2 gap-2 sm:gap-3' : 'flex flex-col gap-3' }}">
                            <div class="relative space-y-2 {{ $posMenuFiltersInline ? 'min-w-0' : '' }}">
                                <div class="relative">
                                    <label for="menu-filter" class="sr-only">@lang('modules.menu.menus')</label>
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-500 dark:text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round"/></svg>
                                    </div>
                                    <select id="menu-filter" onchange="filterByMenu(this.value)" class="block w-full pl-9 pr-3 py-1.5 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-200 focus:ring-skin-base focus:border-skin-base">
                                        <option value="">{{ __('app.filterByMenu') }}</option>
                                        @foreach ($menuList as $menu)
                                            <option value="{{ $menu->id }}" {{ $menuId == $menu->id ? 'selected' : '' }}>
                                                {{ $menu->getTranslation('menu_name', session('locale', app()->getLocale())) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="relative space-y-2 {{ $posMenuFiltersInline ? 'min-w-0' : '' }}">
                                <div class="relative">
                                    <label for="category-filter" class="sr-only">@lang('modules.menu.categories')</label>
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-500 dark:text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z" stroke-linejoin="round"/></svg>
                                    </div>
                                    <select id="category-filter" onchange="filterByCategory(this.value)" class="block w-full pl-9 pr-3 py-1.5 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-200 focus:ring-skin-base focus:border-skin-base">
                                        <option value="">{{ __('app.filterByCategory') }}</option>
                                        @foreach ($categoryList as $category)
                                            <option value="{{ $category->id }}" {{ $filterCategories == $category->id ? 'selected' : '' }}>
                                                {{ $category->category_name }} ({{ $category->items_count }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- Grid view: always stacked (menu row, then category row) --}}
                    <template x-if="filterView === 'grid'">
                        <div class="flex flex-col gap-3">
                            <div class="relative space-y-2">
                                <div class="flex gap-2 items-center flex-wrap">
                                    <button type="button"
                                        onclick="filterByMenu(null)"
                                        @class([
                                            'px-3 py-1.5 text-xs rounded-lg border transition text-left',
                                            'border-skin-base bg-skin-base text-white shadow-sm' => $menuId === null,
                                            'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-200 hover:border-skin-base hover:text-skin-base' => $menuId !== null,
                                        ])>
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="font-medium">Show All</span>
                                        </div>
                                    </button>
                                    @foreach ($menuList as $menu)
                                        @php
                                            $isActiveMenu = (string) $menuId === (string) $menu->id;
                                        @endphp
                                        <button type="button"
                                            onclick="filterByMenu({{ $menu->id }})"
                                            @class([
                                                'px-3 py-1.5 text-xs rounded-lg border transition text-left',
                                                'border-skin-base bg-skin-base text-white shadow-sm' => $isActiveMenu,
                                                'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-200 hover:border-skin-base hover:text-skin-base' => ! $isActiveMenu,
                                            ])>
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="font-medium">{{ $menu->getTranslation('menu_name', session('locale', app()->getLocale())) }}</span>
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                            <div class="relative space-y-2">
                                <div class="flex gap-2 items-center flex-wrap">
                                    <button type="button"
                                        onclick="filterByCategory(null)"
                                        @class([
                                            'px-3 py-1.5 text-xs rounded-lg border transition text-left',
                                            'border-skin-base bg-skin-base text-white shadow-sm' => $filterCategories === null,
                                            'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-200 hover:border-skin-base hover:text-skin-base' => $filterCategories !== null,
                                        ])>
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="font-medium">Show All</span>
                                        </div>
                                    </button>
                                    @foreach ($categoryList as $category)
                                        @php
                                            $isActiveCategory = (string) $filterCategories === (string) $category->id;
                                        @endphp
                                        <button type="button"
                                            onclick="filterByCategory({{ $category->id }})"
                                            @class([
                                                ' px-3 py-1.5 text-xs rounded-lg border transition text-left',
                                                'border-skin-base bg-skin-base text-white shadow-sm' => $isActiveCategory,
                                                'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-200 hover:border-skin-base hover:text-skin-base' => ! $isActiveCategory,
                                            ])>
                                            <div class="inline-flex items-center gap-2">
                                                <span class="font-medium">{{ $category->category_name }}</span>
                                                <span class="text-[11px] text-gray-500 dark:text-gray-300">({{ $category->items_count }})</span>
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Menu Items Grid --}}
            <div
                class="mt-4 overflow-y-auto
                    [&::-webkit-scrollbar]:w-2
                    [&::-webkit-scrollbar-track]:bg-gray-300
                    [&::-webkit-scrollbar-thumb]:bg-gray-400
                    hover:[&::-webkit-scrollbar-thumb]:bg-gray-500
                    dark:[&::-webkit-scrollbar-track]:bg-gray-700
                    dark:[&::-webkit-scrollbar-thumb]:bg-gray-500
                    dark:hover:[&::-webkit-scrollbar-thumb]:bg-gray-400"
                x-data="{
                    loadedCount: {{ $posMenuClientSideCatalog ? $totalMenuItemsCount : $menuItemsLoaded }},
                    totalCount: {{ $totalMenuItemsCount }},

                    get allItemsLoaded() {
                        return this.loadedCount >= this.totalCount;
                    },

                    scrollHandler(scrollEl = $el) {
                        if (window.__posMenuClientSideCatalog) {
                            return;
                        }
                        if (this.allItemsLoaded) {
                            return;
                        }
                        if (!scrollEl) {
                            return;
                        }

                        if (scrollEl.scrollHeight - scrollEl.scrollTop <= scrollEl.clientHeight + 250) {
                            loadMoreMenuItems();
                        }
                    }
                }"
                >
                <ul class="pos-menu-items-ul flex max-h-[calc(100vh-12rem)] max-md:scroll-pb-44 flex-wrap gap-3 overflow-y-auto max-md:pb-44 md:scroll-pb-0 md:pb-0"
                    @scroll.throttle.100ms="scrollHandler($event.target)">
                    @if ($posMenuClientSideCatalog)
                        <li class="w-full basis-full shrink-0 flex flex-col items-center justify-center gap-3 py-12 text-gray-500 dark:text-gray-400"
                            id="pos-menu-catalog-placeholder">
                            <div class="inline-block animate-spin rounded-full h-10 w-10 border-b-2 border-skin-base"></div>
                            <span class="text-sm font-medium">@lang('messages.loadingData')</span>
                        </li>
                    @else
                    @forelse ($menuItems as $item)
                        <li class="menu-card  shrink-0 bg-white rounded-xl border border-gray-100 overflow-hidden cursor-pointer group dark:border-gray-700 hover:shadow-md">
                            <input type="checkbox" id="item-{{ $item->id }}" value="{{ $item->id }}"
                                onclick="addCartItemClientSide({{ $item->id }})"
                                data-item-id="{{ $item->id }}"
                                data-item-name="{{ $item->item_name }}"
                                data-item-price="{{ $item->price }}"
                                data-item-image="{{ $item->item_photo_url }}"
                                data-item-taxes="{{ e(json_encode(($item->taxes ?? collect())->map(fn($tax) => ['id' => $tax->id, 'tax_name' => $tax->tax_name, 'tax_percent' => $tax->tax_percent])->values()->toArray())) }}"
                                data-item-eu-allergens="{{ e(json_encode(array_values(array_unique(array_intersect(
                                    \App\Support\EuAnnexIiAllergens::keys(),
                                    $posEuSelectableMenuInput,
                                    array_filter((array) ($item->eu_allergen_keys ?? []), 'is_string')
                                ))))) }}"
                                data-item-dietary-labels="{{ e(json_encode(\App\Support\DietaryLabels::normalize(
                                    is_array($item->dietary_labels ?? null) ? $item->dietary_labels : []
                                ))) }}"
                                data-variations-count="{{ $item->variations_count }}"
                                data-modifiers-count="{{ $item->modifier_groups_count }}"
                                data-item-in-stock="{{ $item->in_stock ? '1' : '0' }}"
                                {{ ($orderLimitReached || !$item->in_stock) ? 'disabled' : '' }}
                                class="hidden peer">
                            <label for="item-{{ $item->id }}"
                                @class([
                                    "block w-full rounded-lg transition-all duration-100 dark:shadow-gray-700 relative outline-none",
                                    "cursor-pointer dark:hover:bg-gray-700/30 active:scale-95 focus-visible:scale-95" => !$orderLimitReached && $item->in_stock,
                                    "cursor-not-allowed opacity-60" => $orderLimitReached || !$item->in_stock,
                                    "bg-gray-100 dark:bg-gray-800" => !$item->in_stock,
                                    "bg-white dark:bg-gray-900" => $item->in_stock && !$orderLimitReached,
                                    "bg-gray-200 dark:bg-gray-800" => $orderLimitReached,
                                ])
                                tabindex="{{ $orderLimitReached ? '-1' : '0' }}"
                        >

                                    {{-- Loading Overlay --}}
                                    <div id="loading-{{ $item->id }}" class="absolute inset-0 bg-white/80 dark:bg-gray-800/80 rounded-lg z-10 items-center justify-center hidden">
                                        <svg class="animate-spin h-6 w-6 text-skin-base" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>

                                    @php
                                        $__dlabels = \App\Support\DietaryLabels::normalize(
                                            is_array($item->dietary_labels ?? null) ? $item->dietary_labels : []
                                        );
                                    @endphp

                                    {{-- Image Section --}}
                                    @if (!$restaurant->hide_menu_item_image_on_pos)
                                    <div class="relative  hidden md:block select-none">
                                        @if (count($__dlabels))
                                            <div class="absolute top-1 left-1 z-[1] flex max-w-[calc(100%-2.75rem)] flex-wrap gap-0.5 justify-start"
                                                role="group"
                                                aria-label="@lang('modules.menu.dietaryLabelsSectionTitle')">
                                                @foreach ($__dlabels as $__dk)
                                                    <span class="inline-flex shrink-0 rounded-full bg-white/90 p-1 shadow-sm dark:bg-gray-800/90"
                                                        title="@lang(\App\Support\DietaryLabels::langKey($__dk))">
                                                        <img src="{{ \App\Support\DietaryLabels::defaultIconUrl($__dk) }}" alt=""
                                                            class="h-4 w-4 object-contain" width="16" height="16" loading="lazy">
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                        <img class="w-full h-28 object-cover rounded-t-lg select-none" draggable="false"
                                            src="{{ $item->item_photo_url }}"
                                            alt="{{ $item->item_name }}" />
                                        <span class="absolute top-1 right-1 bg-white/90 dark:bg-gray-800/90 rounded-full p-1 shadow-sm">
                                            <img src="{{ asset('img/' . $item->type . '.svg') }}"
                                                class="h-4 w-4" title="@lang('modules.menu.' . $item->type)"
                                                alt="" />
                                        </span>
                                    </div>
                                    @endif

                                    {{-- Content Section --}}
                                    <div class="p-2">
                                        <h5 class="text-xs font-medium text-gray-900 dark:text-white select-none">
                                            {{ $item->item_name }}
                                        </h5>
                                        @if (count($__dlabels))
                                            <div @class([
                                                'mb-1 flex flex-wrap gap-0.5',
                                                'md:hidden' => !$restaurant->hide_menu_item_image_on_pos,
                                            ])
                                                role="group"
                                                aria-label="@lang('modules.menu.dietaryLabelsSectionTitle')">
                                                @foreach ($__dlabels as $__dk)
                                                    <span class="inline-flex shrink-0 rounded-full bg-white/90 p-1 shadow-sm dark:bg-gray-800/90"
                                                        title="@lang(\App\Support\DietaryLabels::langKey($__dk))">
                                                        <img src="{{ \App\Support\DietaryLabels::defaultIconUrl($__dk) }}" alt=""
                                                            class="h-4 w-4 object-contain" width="16" height="16" loading="lazy">
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                        @if ($orderLimitReached)
                                            <div class="text-red-500 text-xs">@lang('messages.orderLimitReached')</div>
                                        @elseif (!$item->in_stock)
                                            <div class="text-red-500">Out of stock</div>
                                        @else

                                        <div class="mt-1 flex items-center justify-between gap-2 select-none">
                                            @if ($item->variations_count == 0)
                                                <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                                    {{ currency_format($item->price, $restaurant->currency_id) }}
                                                </span>
                                            @else
                                                <span class="text-xs text-gray-600 dark:text-gray-300 flex items-center gap-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                                    </svg>
                                                    @lang('modules.menu.showVariations')
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </label>
                        </li>
                    @empty
                        <li class="w-full basis-full shrink-0 text-center py-8 text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m6 4.125l2.25 2.25m0 0l2.25 2.25M12 13.875l2.25-2.25M12 13.875l-2.25 2.25M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                </svg>
                                <p>@lang('messages.noItemAdded')</p>
                            </div>
                        </li>
                    @endforelse
                    @endif


                </ul>

                <div class="flex items-center justify-center py-6 px-4">
                    <div id="menu-loading-indicator" class="flex items-center justify-center gap-3 text-gray-600 dark:text-gray-400 hidden">
                        <svg class="inline animate-spin h-6 w-6 text-skin-base " xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12zm2 5.291A7.96 7.96 0 0 1 4 12H0c0 3.042 1.135 5.824 3 7.938z"/></svg>
                        <span class="text-sm font-medium">@lang('messages.loadingData')</span>
                    </div>
                    <div id="menu-all-loaded" class="flex items-center gap-x-1 text-gray-500 dark:text-gray-400 {{ $posMenuClientSideCatalog ? 'hidden' : ($menuItemsLoaded >= $totalMenuItemsCount ? '' : 'hidden') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0"/></svg>
                        <span class="text-sm font-medium">@lang('messages.allItemsLoaded')</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@php
    $posMenuScriptBootstrap = [
        'posMenuClientSideCatalog' => (bool) $posMenuClientSideCatalog,
        'posCategoryMeta' => $posCategoryMeta,
        'posMenuItemIconBase' => rtrim((string) asset('img'), '/'),
        'posDietaryLabelOrder' => $posDietaryLabelOrder,
        'posDietaryLabelText' => $posDietaryLabelText,
        'posDietaryLabelIconUrls' => $posDietaryLabelIconUrls,
        'posDietaryLabelsAria' => __('modules.menu.dietaryLabelsSectionTitle'),
    ];
@endphp
<script type="application/json" id="pos-menu-script-bootstrap">{!! json_encode($posMenuScriptBootstrap, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) !!}</script>
<script>
    (function () {
        try {
            var el = document.getElementById('pos-menu-script-bootstrap');
            if (!el || !el.textContent) {
                return;
            }
            var d = JSON.parse(el.textContent);
            window.__posMenuClientSideCatalog = !!d.posMenuClientSideCatalog;
            window.__posCategoryMeta = d.posCategoryMeta || {};
            window.__posMenuItemIconBase = d.posMenuItemIconBase || '';
            window.__posDietaryLabelOrder = Array.isArray(d.posDietaryLabelOrder) ? d.posDietaryLabelOrder : [];
            window.__posDietaryLabelText = d.posDietaryLabelText && typeof d.posDietaryLabelText === 'object' ? d.posDietaryLabelText : {};
            window.__posDietaryLabelIconUrls = d.posDietaryLabelIconUrls && typeof d.posDietaryLabelIconUrls === 'object' ? d.posDietaryLabelIconUrls : {};
            window.__posDietaryLabelsAria = typeof d.posDietaryLabelsAria === 'string' ? d.posDietaryLabelsAria : '';
        } catch (e) {
            console.error('pos-menu-script-bootstrap', e);
            window.__posMenuClientSideCatalog = false;
            window.__posCategoryMeta = {};
            window.__posMenuItemIconBase = '';
            window.__posDietaryLabelOrder = [];
            window.__posDietaryLabelText = {};
            window.__posDietaryLabelIconUrls = {};
            window.__posDietaryLabelsAria = '';
        }
    })();

    // Cookie functions (don't need jQuery)
    function setCookie(name, value, days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        document.cookie = `${name}=${value};expires=${date.toUTCString()};path=/`;
    }

    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) {
            return parts.pop().split(';').shift();
        }
        return null;
    }

    // Define functions immediately (they will check for jQuery when called)
    // Initialize menu filters from URL parameters or defaults
    var urlParams = new URLSearchParams(window.location.search);
    window.menuFilters = {
        menuId: urlParams.get('menuId') || null,
        categoryId: urlParams.get('filterCategories') || null,
        search: urlParams.get('search') || '',
        limit: parseInt(urlParams.get('limit')) || {{ (int) $menuItemsLoaded }}
    };

    window.posMenuClientCatalog = window.posMenuClientCatalog || null;
    window.posMenuClientCatalogLoading = window.posMenuClientCatalogLoading || false;

    @if($posMenuClientSideCatalog)
    window.__posMenuCatalogCacheApi = (function() {
        var FRESH_MS = 1000 * 60 * 15;
        var STALE_MS = 1000 * 60 * 60 * 24;
        var branchId = {{ (int) branch()->id }};
        var locale = @json(app()->getLocale());

        function buildKey(orderTypeId, deliveryAppId) {
            return 'pos_menu_catalog_v4_' + branchId + '_' + locale + '_' + (orderTypeId || 'none') + '_' + (deliveryAppId || 'none');
        }

        function currentTaxRevision() {
            return String((window.posConfig && window.posConfig.taxRevision != null) ? window.posConfig.taxRevision : 0);
        }

        function entryRevisionMatches(parsed) {
            if (!parsed || parsed.tax_revision == null) {
                return false;
            }
            return String(parsed.tax_revision) === currentTaxRevision();
        }

        function parseEntry(cacheKey) {
            try {
                var raw = window.localStorage.getItem(cacheKey);
                if (!raw) {
                    return null;
                }
                var parsed = JSON.parse(raw);
                if (!parsed || !Array.isArray(parsed.items) || !parsed.cached_at) {
                    return null;
                }
                if (!entryRevisionMatches(parsed)) {
                    return null;
                }
                return parsed;
            } catch (e) {
                return null;
            }
        }

        return {
            buildKey: buildKey,
            getPrefix: function() {
                return 'pos_menu_catalog_v4_' + branchId + '_' + locale + '_';
            },
            readFresh: function(cacheKey) {
                var parsed = parseEntry(cacheKey);
                if (!parsed) {
                    return null;
                }
                if ((Date.now() - parsed.cached_at) > FRESH_MS) {
                    return null;
                }
                if (typeof window.__posCatalogItemsIncludeModifierPayload === 'function' &&
                    !window.__posCatalogItemsIncludeModifierPayload(parsed.items)) {
                    return null;
                }
                return parsed.items;
            },
            readAny: function(cacheKey) {
                var parsed = parseEntry(cacheKey);
                if (!parsed) {
                    return null;
                }
                if (typeof window.__posCatalogItemsIncludeModifierPayload === 'function' &&
                    !window.__posCatalogItemsIncludeModifierPayload(parsed.items)) {
                    return null;
                }
                return parsed.items;
            },
            readStale: function(cacheKey) {
                var parsed = parseEntry(cacheKey);
                if (!parsed) {
                    return null;
                }
                if ((Date.now() - parsed.cached_at) > STALE_MS) {
                    return null;
                }
                return parsed.items;
            },
            write: function(cacheKey, items) {
                try {
                    window.localStorage.setItem(cacheKey, JSON.stringify({
                        items: items || [],
                        cached_at: Date.now(),
                        tax_revision: currentTaxRevision()
                    }));
                } catch (e) {
                    // Ignore storage quota / private mode
                }
            },
            clearAllForBranchLocale: function() {
                try {
                    var prefix = this.getPrefix();
                    var keys = [];
                    for (var i = 0; i < window.localStorage.length; i++) {
                        var k = window.localStorage.key(i);
                        if (k && k.indexOf(prefix) === 0) {
                            keys.push(k);
                        }
                    }
                    keys.forEach(function(k) {
                        window.localStorage.removeItem(k);
                    });
                } catch (e) {
                    // Ignore localStorage failures.
                }
            }
        };
    })();
    @endif

    window.__posCatalogItemsIncludeModifierPayload = function(items) {
        if (!items || !items.length) {
            return true;
        }
        var max = Math.min(items.length, 300);
        for (var i = 0; i < max; i++) {
            var it = items[i];
            if ((parseInt(it.modifier_groups_count, 10) || 0) > 0) {
                if (!it.modifier_catalog || typeof it.modifier_catalog !== 'object') {
                    return false;
                }
            }
        }
        return true;
    };

    window.updateSearchClearButtonVisibility = function() {
        if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
            return;
        }

        const $clearBtn = $('#products-search-clear');
        if (!$clearBtn.length) {
            return;
        }

        const searchValue = ($('#products-search').val() || '').toString().trim();
        $clearBtn.toggleClass('hidden', searchValue.length === 0);
    };

    // Wait for jQuery before initializing event listeners
    (function() {
        function initMenuScripts() {
            if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
                setTimeout(initMenuScripts, 100);
                return;
            }

            // Search functionality (client catalog: filter in memory; shorter debounce)
            let searchTimeout;
            window.updateSearchClearButtonVisibility();
            $('#products-search').on('input', function() {
                window.updateSearchClearButtonVisibility();
                clearTimeout(searchTimeout);
                const search = $(this).val();
                const debounceMs = (window.__posMenuClientSideCatalog && window.posMenuClientCatalog !== null) ? 150 : 500;
                searchTimeout = setTimeout(function() {
                    if (typeof window.filterMenuItems === 'function') {
                        window.filterMenuItems();
                    }
                }, debounceMs);
            });

            // Client catalog: one bulk fetch then filter in JS (category counts from same catalog; no AJAX search).
            if (window.__posMenuClientSideCatalog) {
                setTimeout(function() {
                    if (typeof window.loadMenuItems === 'function') {
                        window.loadMenuItems();
                    }
                }, 100);
            } else if (window.menuFilters.menuId || window.menuFilters.categoryId || window.menuFilters.search) {
                setTimeout(function() {
                    if (typeof window.loadMenuItems === 'function') {
                        window.loadMenuItems();
                    }
                    if (typeof window.updateCategoryCounts === 'function') {
                        window.updateCategoryCounts();
                    }
                }, 100);
            } else {
                if (typeof window.updateCategoryCounts === 'function') {
                    setTimeout(function() {
                        window.updateCategoryCounts();
                    }, 200);
                }
            }
        }

        // Start initialization when jQuery is ready
        if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
            $(document).ready(function() {
                initMenuScripts();
            });
        } else {
            // Wait for jQuery to load
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    initMenuScripts();
                });
            } else {
                initMenuScripts();
            }
        }
    })();

    window.clearSearch = function() {
        if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
            console.error('jQuery is not loaded');
            return;
        }

        $('#products-search').val('');
        window.menuFilters.search = '';
        window.updateSearchClearButtonVisibility();
        window.loadMenuItems();
    };

    // Keep existing filters initialized from URL/query if present.
    window.menuFilters = window.menuFilters || {
        menuId: null,
        categoryId: null,
        search: '',
        limit: {{ (int) $menuItemsLoaded }}
    };

    window.filterByMenu = function(menuId) {
        // Ensure menuFilters is initialized
        if (!window.menuFilters) {
            window.menuFilters = {
                menuId: null,
                categoryId: null,
                search: '',
                limit: {{ (int) $menuItemsLoaded }}
            };
        }

        window.menuFilters.menuId = menuId || null;
        window.menuFilters.categoryId = null; // Reset category when menu changes

        // Wait for jQuery if not loaded, then call loadMenuItems and updateCategories
        if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
            const checkJQuery = setInterval(function() {
                if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
                    clearInterval(checkJQuery);
                    window.updateMenuSelection(menuId);
                    if (typeof window.loadMenuItems === 'function') {
                        window.loadMenuItems();
                    }
                    if (typeof window.updateCategoryCounts === 'function' &&
                        !(window.__posMenuClientSideCatalog && Array.isArray(window.posMenuClientCatalog))) {
                        window.updateCategoryCounts();
                    }
                }
            }, 100);
            // Stop checking after 5 seconds
            setTimeout(function() {
                clearInterval(checkJQuery);
            }, 5000);
            return;
        }

        // Update menu selection UI
        window.updateMenuSelection(menuId);

        if (typeof window.loadMenuItems === 'function') {
            window.loadMenuItems();
        }
        if (typeof window.updateCategoryCounts === 'function' &&
            !(window.__posMenuClientSideCatalog && Array.isArray(window.posMenuClientCatalog))) {
            window.updateCategoryCounts();
        }
    };

    // Update menu selection UI (dropdown and grid buttons)
    window.updateMenuSelection = function(menuId) {
        if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
            return;
        }

        // Normalize menuId to string for comparison
        const selectedMenuId = menuId ? String(menuId) : null;

        // Update select dropdown
        const $select = $('#menu-filter');
        if ($select.length) {
            $select.val(selectedMenuId || '');
        }

        // Update grid view buttons
        const $menuButtons = $('button[onclick*="filterByMenu"]');
        $menuButtons.each(function() {
            const $button = $(this);
            const onclickAttr = $button.attr('onclick') || '';

            // Check if this is the "Show All" button (filterByMenu(null))
            if (onclickAttr.includes('filterByMenu(null)')) {
                if (!selectedMenuId) {
                    // Show All is selected - add active classes
                    $button.removeClass('border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-200 hover:border-skin-base hover:text-skin-base');
                    $button.addClass('border-skin-base bg-skin-base text-white shadow-sm');
                } else {
                    // Show All is not selected - remove active classes
                    $button.removeClass('border-skin-base bg-skin-base text-white shadow-sm');
                    $button.addClass('border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-200 hover:border-skin-base hover:text-skin-base');
                }
            } else {
                // Extract menu ID from onclick attribute
                const match = onclickAttr.match(/filterByMenu\((\d+)\)/);
                if (match) {
                    const buttonMenuId = String(match[1]);
                    if (selectedMenuId && buttonMenuId === selectedMenuId) {
                        // This menu is selected - add active classes
                        $button.removeClass('border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-200 hover:border-skin-base hover:text-skin-base');
                        $button.addClass('border-skin-base bg-skin-base text-white shadow-sm');
                    } else {
                        // This menu is not selected - remove active classes
                        $button.removeClass('border-skin-base bg-skin-base text-white shadow-sm');
                        $button.addClass('border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-200 hover:border-skin-base hover:text-skin-base');
                    }
                }
            }
        });
    };

    // Update category selection UI (dropdown and grid buttons)
    window.updateCategorySelection = function(categoryId) {
        if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
            return;
        }

        // Normalize categoryId to string for comparison
        const selectedCategoryId = categoryId ? String(categoryId) : null;

        // Update select dropdown
        const $select = $('#category-filter');
        if ($select.length) {
            $select.val(selectedCategoryId || '');
        }

        // Update grid view buttons
        const $categoryButtons = $('button[onclick*="filterByCategory"]');
        $categoryButtons.each(function() {
            const $button = $(this);
            const onclickAttr = $button.attr('onclick') || '';

            // Check if this is the "Show All" button (filterByCategory(null))
            if (onclickAttr.includes('filterByCategory(null)')) {
                if (!selectedCategoryId) {
                    // Show All is selected - add active classes
                    $button.removeClass('border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-200 hover:border-skin-base hover:text-skin-base');
                    $button.addClass('border-skin-base bg-skin-base text-white shadow-sm');
                } else {
                    // Show All is not selected - remove active classes
                    $button.removeClass('border-skin-base bg-skin-base text-white shadow-sm');
                    $button.addClass('border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-200 hover:border-skin-base hover:text-skin-base');
                }
            } else {
                // Extract category ID from onclick attribute
                const match = onclickAttr.match(/filterByCategory\((\d+)\)/);
                if (match) {
                    const buttonCategoryId = String(match[1]);
                    if (selectedCategoryId && buttonCategoryId === selectedCategoryId) {
                        // This category is selected - add active classes
                        $button.removeClass('border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-200 hover:border-skin-base hover:text-skin-base');
                        $button.addClass('border-skin-base bg-skin-base text-white shadow-sm');
                    } else {
                        // This category is not selected - remove active classes
                        $button.removeClass('border-skin-base bg-skin-base text-white shadow-sm');
                        $button.addClass('border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-200 hover:border-skin-base hover:text-skin-base');
                    }
                }
            }
        });
    };

    window.filterByCategory = function(categoryId) {
        // Ensure menuFilters is initialized
        if (!window.menuFilters) {
            window.menuFilters = {
                menuId: null,
                categoryId: null,
                search: '',
                limit: {{ (int) $menuItemsLoaded }}
            };
        }

        window.menuFilters.categoryId = categoryId || null;

        // Wait for jQuery if not loaded, then call loadMenuItems
        if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
            const checkJQuery = setInterval(function() {
                if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
                    clearInterval(checkJQuery);
                    window.updateCategorySelection(categoryId);
                    if (typeof window.loadMenuItems === 'function') {
                        window.loadMenuItems();
                    }
                }
            }, 100);
            // Stop checking after 5 seconds
            setTimeout(function() {
                clearInterval(checkJQuery);
            }, 5000);
            return;
        }

        // Update category selection UI
        window.updateCategorySelection(categoryId);

        if (typeof window.loadMenuItems === 'function') {
            window.loadMenuItems();
        }
    };

    window.filterMenuItems = function() {
        // Ensure menuFilters is initialized
        if (!window.menuFilters) {
            window.menuFilters = {
                menuId: null,
                categoryId: null,
                search: '',
                limit: {{ (int) $menuItemsLoaded }}
            };
        }

        // Wait for jQuery if not loaded
        if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
            const checkJQuery = setInterval(function() {
                if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
                    clearInterval(checkJQuery);
                    const search = $('#products-search').val() || '';
                    window.menuFilters.search = search;
                    if (typeof window.loadMenuItems === 'function') {
                        window.loadMenuItems();
                    }
                    if (typeof window.updateCategoryCounts === 'function' &&
                        !(window.__posMenuClientSideCatalog && Array.isArray(window.posMenuClientCatalog))) {
                        window.updateCategoryCounts();
                    }
                }
            }, 100);
            // Stop checking after 5 seconds
            setTimeout(function() {
                clearInterval(checkJQuery);
            }, 5000);
            return;
        }

        const search = $('#products-search').val() || '';
        window.menuFilters.search = search;
        if (typeof window.loadMenuItems === 'function') {
            window.loadMenuItems();
        }
        if (typeof window.updateCategoryCounts === 'function' &&
            !(window.__posMenuClientSideCatalog && Array.isArray(window.posMenuClientCatalog))) {
            window.updateCategoryCounts();
        }
    };

    window.applyClientMenuFilter = function() {
        if (!window.__posMenuClientSideCatalog || !Array.isArray(window.posMenuClientCatalog)) {
            return;
        }

        const menuId = window.menuFilters.menuId;
        const categoryId = window.menuFilters.categoryId;
        const search = (window.menuFilters.search || '').toString().trim().toLowerCase();

        const filtered = window.posMenuClientCatalog.filter(function(item) {
            if (menuId && String(item.menu_id) !== String(menuId)) {
                return false;
            }
            if (categoryId && String(item.item_category_id) !== String(categoryId)) {
                return false;
            }
            if (search) {
                const name = (item.item_name || '').toString().toLowerCase();
                const description = (item.description || '').toString().toLowerCase();
                if (!name.includes(search) && !description.includes(search)) {
                    return false;
                }
            }
            return true;
        });

        if (typeof window.renderMenuItems === 'function') {
            window.renderMenuItems(filtered, filtered.length, filtered.length);
        }
    };

    // Load menu items via AJAX (or one bulk fetch + client-side filter when enabled)
    window.loadMenuItems = function(options) {
        options = options || {};

        if (!window.menuFilters) {
            window.menuFilters = {
                menuId: null,
                categoryId: null,
                search: '',
                limit: {{ (int) $menuItemsLoaded }}
            };
        }

        if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
            const checkJQuery = setInterval(function() {
                if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
                    clearInterval(checkJQuery);
                    window.loadMenuItems(options);
                }
            }, 100);
            setTimeout(function() {
                clearInterval(checkJQuery);
            }, 5000);
            return;
        }

        if (window.__posMenuClientSideCatalog) {
            const buildCatalogCacheKey = function(orderTypeId, deliveryAppId) {
                const branchId = {{ (int) branch()->id }};
                const locale = @json(app()->getLocale());
                return 'pos_menu_catalog_v4_' + branchId + '_' + locale + '_' + (orderTypeId || 'none') + '_' + (deliveryAppId || 'none');
            };

            const catalogTaxRevision = function() {
                return String((window.posConfig && window.posConfig.taxRevision != null) ? window.posConfig.taxRevision : 0);
            };

            const readCatalogCache = function(cacheKey) {
                if (window.__posMenuCatalogCacheApi && typeof window.__posMenuCatalogCacheApi.readAny === 'function') {
                    return window.__posMenuCatalogCacheApi.readAny(cacheKey);
                }
                try {
                    const raw = window.localStorage.getItem(cacheKey);
                    if (!raw) return null;
                    const parsed = JSON.parse(raw);
                    if (!parsed || !Array.isArray(parsed.items) || !parsed.cached_at) {
                        return null;
                    }
                    if (parsed.tax_revision == null || String(parsed.tax_revision) !== catalogTaxRevision()) {
                        return null;
                    }
                    if (typeof window.__posCatalogItemsIncludeModifierPayload === 'function' &&
                        !window.__posCatalogItemsIncludeModifierPayload(parsed.items)) {
                        return null;
                    }
                    return parsed.items;
                } catch (e) {
                    return null;
                }
            };

            const writeCatalogCache = function(cacheKey, items) {
                if (window.__posMenuCatalogCacheApi && typeof window.__posMenuCatalogCacheApi.write === 'function') {
                    window.__posMenuCatalogCacheApi.write(cacheKey, items);
                    return;
                }
                try {
                    window.localStorage.setItem(cacheKey, JSON.stringify({
                        items: items || [],
                        cached_at: Date.now(),
                        tax_revision: catalogTaxRevision()
                    }));
                } catch (e) {
                    // Ignore storage quota/private mode errors.
                }
            };

            if (window.posMenuClientCatalog && !options.forceReloadCatalog) {
                window.applyClientMenuFilter();
                if (typeof window.updateCategoryCounts === 'function') {
                    window.updateCategoryCounts();
                }
                return;
            }
            if (window.posMenuClientCatalogLoading) {
                return;
            }
            window.posMenuClientCatalogLoading = true;

            const $menuContainer = $('ul.pos-menu-items-ul');
            if ($menuContainer.length && options.showSpinner !== false) {
                $menuContainer.html('<li class="w-full basis-full shrink-0 text-center py-8"><div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900 dark:border-white"></div></li>');
            }

            const orderTypeId = window.posState ? window.posState.orderTypeId : null;
            const deliveryAppId = window.posState ? window.posState.selectedDeliveryApp : null;
            const catalogCacheKey = buildCatalogCacheKey(orderTypeId, deliveryAppId);

            const applyCatalogToUi = function(items) {
                window.posMenuClientCatalog = items || [];
                if (typeof window.applyPosMenuPricesForCurrentOrderContext === 'function') {
                    window.applyPosMenuPricesForCurrentOrderContext();
                }
                if (typeof window.applyClientMenuFilter === 'function') {
                    window.applyClientMenuFilter();
                }
                if (typeof window.updateCategoryCounts === 'function') {
                    window.updateCategoryCounts();
                }
            };

            // Always serve from local cache first (both online and offline) for speed.
            if (!options.forceReloadCatalog) {
                let cachedItems = readCatalogCache(catalogCacheKey);
                if ((!cachedItems || !cachedItems.length) && window.__posMenuCatalogCacheApi &&
                    typeof window.__posMenuCatalogCacheApi.getPrefix === 'function' &&
                    typeof window.__posMenuCatalogCacheApi.readAny === 'function') {
                    try {
                        const prefix = window.__posMenuCatalogCacheApi.getPrefix();
                        for (let i = 0; i < window.localStorage.length; i++) {
                            const key = window.localStorage.key(i);
                            if (!key || key.indexOf(prefix) !== 0) {
                                continue;
                            }
                            const anyCached = window.__posMenuCatalogCacheApi.readAny(key);
                            if (anyCached && anyCached.length) {
                                cachedItems = anyCached;
                                break;
                            }
                        }
                    } catch (e) {
                        // Ignore localStorage issues; network fallback below.
                    }
                }
                if (cachedItems && cachedItems.length) {
                    window.posMenuClientCatalogLoading = false;
                    applyCatalogToUi(cachedItems);
                    return;
                }
            }

            if (typeof navigator !== 'undefined' && navigator.onLine === false) {
                window.posMenuClientCatalogLoading = false;
                if ($menuContainer.length) {
                    $menuContainer.html('<li class="w-full basis-full shrink-0 text-center py-8 text-red-500">Error loading menu items</li>');
                }
                return;
            }

            $.easyAjax({
                url: "{{ route('ajax.pos.items') }}",
                type: "GET",
                data: {
                    load_all: 1,
                    order_type_id: orderTypeId,
                    delivery_app_id: deliveryAppId,
                    catalog_schema: 2,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    window.posMenuClientCatalogLoading = false;
                    if (response.success && response.items) {
                        const incomingItems = response.items || [];
                        const existingItems = readCatalogCache(catalogCacheKey) || [];
                        const hasChanged = JSON.stringify(existingItems) !== JSON.stringify(incomingItems);

                        if (hasChanged && window.__posMenuCatalogCacheApi &&
                            typeof window.__posMenuCatalogCacheApi.clearAllForBranchLocale === 'function') {
                            window.__posMenuCatalogCacheApi.clearAllForBranchLocale();
                        }
                        if (hasChanged || !existingItems.length) {
                            writeCatalogCache(catalogCacheKey, incomingItems);
                        }

                        if (!options._backgroundRefresh || hasChanged || !window.posMenuClientCatalog) {
                            applyCatalogToUi(incomingItems);
                        }
                    } else {
                        console.error('Failed to load menu catalog');
                        if ($menuContainer.length && !options._backgroundRefresh) {
                            $menuContainer.html('<li class="w-full basis-full shrink-0 text-center py-8 text-red-500">Error loading menu items</li>');
                        }
                    }
                },
                error: function(xhr) {
                    window.posMenuClientCatalogLoading = false;
                    console.error('Error loading menu catalog:', xhr);
                    if ($menuContainer.length && !options._backgroundRefresh) {
                        $menuContainer.html('<li class="w-full basis-full shrink-0 text-center py-8 text-red-500">Error loading menu items</li>');
                    }
                }
            });
            return;
        }

        const $menuContainer = $('ul.pos-menu-items-ul');
        if ($menuContainer.length) {
            $menuContainer.html('<li class="w-full basis-full shrink-0 text-center py-8"><div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900 dark:border-white"></div></li>');
        }

        const orderTypeId = window.posState ? window.posState.orderTypeId : null;
        const deliveryAppId = window.posState ? window.posState.selectedDeliveryApp : null;

        $.easyAjax({
            url: "{{ route('ajax.pos.items') }}",
            type: "GET",
            data: {
                menu_id: window.menuFilters.menuId,
                category_id: window.menuFilters.categoryId,
                search: window.menuFilters.search,
                limit: window.menuFilters.limit,
                order_type_id: orderTypeId,
                delivery_app_id: deliveryAppId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success && response.items) {
                    if (typeof window.renderMenuItems === 'function') {
                        window.renderMenuItems(response.items, response.total_count, response.loaded_count);
                    }
                } else {
                    console.error('Failed to load menu items');
                    if ($menuContainer.length) {
                        $menuContainer.html('<li class="w-full basis-full shrink-0 text-center py-8 text-red-500">Error loading menu items</li>');
                    }
                }
            },
            error: function(xhr) {
                console.error('Error loading menu items:', xhr);
                if ($menuContainer.length) {
                    $menuContainer.html('<li class="w-full basis-full shrink-0 text-center py-8 text-red-500">Error loading menu items</li>');
                }
            }
        });
    };

    window.reloadPosMenuCatalog = function() {
        if (window.__posMenuCatalogCacheApi && window.__posMenuClientSideCatalog &&
            typeof navigator !== 'undefined' && navigator.onLine === false) {
            window.posMenuClientCatalogLoading = false;
            var orderTypeId = window.posState ? window.posState.orderTypeId : null;
            var deliveryAppId = window.posState ? window.posState.selectedDeliveryApp : null;
            var key = window.__posMenuCatalogCacheApi.buildKey(orderTypeId, deliveryAppId);
            var stale = window.__posMenuCatalogCacheApi.readStale(key);
            if (stale && stale.length) {
                window.posMenuClientCatalog = stale;
                if (typeof window.applyPosMenuPricesForCurrentOrderContext === 'function') {
                    window.applyPosMenuPricesForCurrentOrderContext();
                }
                if (typeof window.applyClientMenuFilter === 'function') {
                    window.applyClientMenuFilter();
                }
                if (typeof window.updateCategoryCounts === 'function') {
                    window.updateCategoryCounts();
                }
                if (typeof window.showToast === 'function') {
                    window.showToast('warning', @json(__('messages.posMenuStaleCacheNotice')));
                }
                return;
            }
        // Fallback: keep current in-memory catalog or any cached catalog, then remap prices for selected order type.
        if (Array.isArray(window.posMenuClientCatalog) && window.posMenuClientCatalog.length) {
            if (typeof window.applyPosMenuPricesForCurrentOrderContext === 'function') {
                window.applyPosMenuPricesForCurrentOrderContext();
            }
            if (typeof window.applyClientMenuFilter === 'function') {
                window.applyClientMenuFilter();
            }
            if (typeof window.updateCategoryCounts === 'function') {
                window.updateCategoryCounts();
            }
            return;
        }
        try {
            var branchId = {{ (int) branch()->id }};
            var locale = @json(app()->getLocale());
            var prefix = 'pos_menu_catalog_v4_' + branchId + '_' + locale + '_';
            var anyStale = null;
            for (var i = 0; i < window.localStorage.length; i++) {
                var k = window.localStorage.key(i);
                if (!k || k.indexOf(prefix) !== 0) {
                    continue;
                }
                var items = window.__posMenuCatalogCacheApi.readStale(k);
                if (items && items.length) {
                    anyStale = items;
                    break;
                }
            }
            if (anyStale) {
                window.posMenuClientCatalog = anyStale;
                if (typeof window.applyPosMenuPricesForCurrentOrderContext === 'function') {
                    window.applyPosMenuPricesForCurrentOrderContext();
                }
                if (typeof window.applyClientMenuFilter === 'function') {
                    window.applyClientMenuFilter();
                }
                if (typeof window.updateCategoryCounts === 'function') {
                    window.updateCategoryCounts();
                }
                return;
            }
        } catch (e) {
            // Ignore localStorage read errors.
        }
        }
        window.posMenuClientCatalog = null;
        window.posMenuClientCatalogLoading = false;
        if (typeof window.loadMenuItems === 'function') {
            window.loadMenuItems({ forceReloadCatalog: true });
        }
    };

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function buildDietaryLabelIconSpansHtml(item) {
        var keys = Array.isArray(item.dietary_labels)
            ? item.dietary_labels.filter(function(k) { return typeof k === 'string'; })
            : [];
        var order = window.__posDietaryLabelOrder || [];
        var labels = window.__posDietaryLabelText || {};
        var iconUrls = window.__posDietaryLabelIconUrls || {};
        if (!keys.length || !order.length) {
            return '';
        }
        var parts = [];
        for (var i = 0; i < order.length; i++) {
            var k = order[i];
            if (keys.indexOf(k) === -1) {
                continue;
            }
            var t = labels[k] || k;
            var u = iconUrls[k] || '';
            if (!u) {
                continue;
            }
            parts.push(
                '<span class="inline-flex shrink-0 rounded-full bg-white/90 p-1 shadow-sm dark:bg-gray-800/90" title="' +
                    escapeHtml(t) +
                    '"><img src="' +
                    escapeHtml(u) +
                    '" alt="" class="h-4 w-4 object-contain" width="16" height="16" loading="lazy" /></span>'
            );
        }
        return parts.join('');
    }

    function buildDietaryLabelsMenuMarkup(item, showImageBlock) {
        var inner = buildDietaryLabelIconSpansHtml(item);
        if (!inner) {
            return { overlay: '', strip: '' };
        }
        var aria = escapeHtml(window.__posDietaryLabelsAria || '');
        var overlay = showImageBlock
            ? '<div class="absolute top-1 left-1 z-[1] flex max-w-[calc(100%-2.75rem)] flex-wrap gap-0.5 justify-start" role="group" aria-label="' +
                aria +
                '">' +
                inner +
                '</div>'
            : '';
        var stripClass = showImageBlock
            ? 'mb-1 flex flex-wrap gap-0.5 md:hidden'
            : 'mb-1 flex flex-wrap gap-0.5';
        var strip =
            '<div class="' + stripClass + '" role="group" aria-label="' + aria + '">' + inner + '</div>';
        return { overlay: overlay, strip: strip };
    }

    // Render menu items in the grid
    window.renderMenuItems = function(items, totalCount, loadedCount) {
        if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
            console.error('jQuery is not loaded');
            return;
        }

        const $menuContainer = $('ul.pos-menu-items-ul');
        if (!$menuContainer.length) {
            console.error('Menu container not found');
            return;
        }

        if (!items || items.length === 0) {
            $menuContainer.html(`
                <li class="w-full basis-full shrink-0 text-center py-8 text-gray-500 dark:text-gray-400">
                    <div>No items found</div>
                </li>
            `);
            $('#menu-all-loaded').addClass('hidden');
            return;
        }

        const hideImage = {{ $restaurant->hide_menu_item_image_on_pos ? 'true' : 'false' }};
        const currencyId = {{ $restaurant->currency_id ?? 'null' }};
        const orderLimitReached = {{ $orderLimitReached ? 'true' : 'false' }};
        const iconBase = (window.__posMenuItemIconBase || '').replace(/\/$/, '');

        let html = '';
        items.forEach(function(item) {
            const itemPrice = parseFloat(item.price) || 0;
            const variationsCount = item.variations_count || 0;
            const modifiersCount = item.modifier_groups_count || 0;
            const itemImage = item.item_photo_url || '';
            const itemName = item.item_name || 'Item';
            const itemNameAttr = escapeHtml(itemName);
            const itemType = item.type || 'veg';
            const stockValue = item.in_stock;
            const isInStock = !(
                stockValue === false ||
                stockValue === 0 ||
                stockValue === '0' ||
                stockValue === 'false' ||
                stockValue === null
            );
            const itemTaxes = Array.isArray(item.taxes)
                ? item.taxes.map(function(tax) {
                    return {
                        id: parseInt(tax.id) || null,
                        tax_name: tax.tax_name || '',
                        tax_percent: parseFloat(tax.tax_percent || 0)
                    };
                })
                : [];
            if (!window.menuItemTaxesIndex) {
                window.menuItemTaxesIndex = {};
            }
            window.menuItemTaxesIndex[item.id] = itemTaxes;
            const itemTaxesJson = JSON.stringify(itemTaxes).replace(/"/g, '&quot;');
            const itemEuAllergensJson = JSON.stringify(
                Array.isArray(item.eu_allergen_keys) ? item.eu_allergen_keys : []
            ).replace(/"/g, '&quot;');
            const dietaryKeys = Array.isArray(item.dietary_labels)
                ? item.dietary_labels.filter(function(k) { return typeof k === 'string'; })
                : [];
            const itemDietaryJson = JSON.stringify(dietaryKeys).replace(/"/g, '&quot;');
            const inputDisabled = orderLimitReached || !isInStock;

            // Format price using the same format as Blade
            let formattedPrice = '';
            if (typeof window.formatCurrency === 'function') {
                formattedPrice = window.formatCurrency(itemPrice);
            } else {
                // Fallback formatting
                formattedPrice = new Intl.NumberFormat('en-US', {
                    style: 'currency',
                    currency: 'USD'
                }).format(itemPrice);
            }

            const labelClasses = [
                "block lg:w-32 w-full rounded-lg shadow-sm transition-all duration-100 dark:shadow-gray-700 relative outline-none",
                (!orderLimitReached && isInStock) ? "cursor-pointer hover:shadow-md dark:hover:bg-gray-700/30 active:scale-95 focus-visible:scale-95 bg-white dark:bg-gray-900" : "cursor-not-allowed opacity-60",
                !isInStock ? "bg-gray-100 dark:bg-gray-800" : "",
                orderLimitReached ? "bg-gray-200 dark:bg-gray-800" : ""
            ].filter(c => c).join(' ');

            const typeIconUrl = iconBase ? `${iconBase}/${encodeURIComponent(itemType)}.svg` : '';
            const showImageBlock = !hideImage && !!itemImage;
            const dietaryMarkup = buildDietaryLabelsMenuMarkup(item, showImageBlock);

            html += `
                <li class="group relative flex items-center justify-center shrink-0">
                    <input type="checkbox" id="item-${item.id}" value="${item.id}"
                        onclick="addCartItemClientSide(${item.id})"
                        data-item-id="${item.id}"
                        data-item-name="${itemNameAttr}"
                        data-item-price="${itemPrice}"
                        data-item-image="${escapeHtml(itemImage)}"
                        data-item-taxes="${itemTaxesJson}"
                        data-item-eu-allergens="${itemEuAllergensJson}"
                        data-item-dietary-labels="${itemDietaryJson}"
                        data-variations-count="${variationsCount}"
                        data-modifiers-count="${modifiersCount}"
                        data-item-in-stock="${isInStock ? '1' : '0'}"
                        ${inputDisabled ? 'disabled' : ''}
                        class="hidden peer">
                    <label for="item-${item.id}" class="${labelClasses}" tabindex="${inputDisabled ? '-1' : '0'}">
                        ${showImageBlock ? `
                        <div class="relative aspect-square hidden md:block">
                            ${dietaryMarkup.overlay}
                            <img class="w-full lg:w-32 lg:h-32 md:w-20 md:h-20 object-cover select-none rounded-t-lg"
                                src="${escapeHtml(itemImage)}"
                                alt="${itemNameAttr}" />
                            <span class="absolute top-1 right-1 bg-white/90 dark:bg-gray-800/90 rounded-full p-1 shadow-sm">
                                <img src="${typeIconUrl}"
                                    class="h-4 w-4" title="${escapeHtml(itemType)}"
                                    alt="" />
                            </span>
                        </div>
                        ` : ''}
                        <div class="p-2">
                            <h5 class="text-xs font-medium text-gray-900 dark:text-white min-h-[1rem]">
                                ${itemNameAttr}
                            </h5>
                            ${dietaryMarkup.strip}
                            ${orderLimitReached ? '<div class="text-red-500 text-xs">Order limit reached</div>' : !isInStock ? '<div class="text-red-500">Out of stock</div>' : `
                            <div class="mt-1 flex items-center justify-between gap-2">
                                ${variationsCount == 0 ? `
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                    ${formattedPrice}
                                </span>
                                ` : `
                                <span class="text-xs text-gray-600 dark:text-gray-300 flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                    </svg>
                                    Show Variations
                                </span>
                                `}
                            </div>
                            `}
                        </div>
                    </label>
                </li>
            `;
        });

        $menuContainer.html(html);

        const $allLoaded = $('#menu-all-loaded');
        if ($allLoaded.length) {
            if (window.__posMenuClientSideCatalog) {
                $allLoaded.addClass('hidden');
            } else {
                $allLoaded.toggleClass('hidden', loadedCount < totalCount);
            }
        }
    };

    // Client-side addCartItem (no server request)
    window.addCartItem = function(menuItemId) {
        if (typeof window.addCartItemClientSide === 'function') {
            window.addCartItemClientSide(menuItemId);
        }
    };

    window.loadMoreMenuItems = function() {
        if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
            console.error('jQuery is not loaded');
            return;
        }

        if (window.__posMenuClientSideCatalog) {
            return;
        }

        // Show loading indicator
        $('#menu-loading-indicator').removeClass('hidden');

        // Increase limit and load more items
        window.menuFilters.limit = (window.menuFilters.limit || {{ (int) $menuItemsLoaded }}) + {{ (int) $menuItemsLoaded }};

        // Get current order type from state
        const orderTypeId = window.posState ? window.posState.orderTypeId : null;
        const deliveryAppId = window.posState ? window.posState.selectedDeliveryApp : null;

        $.easyAjax({
            url: "{{ route('ajax.pos.items') }}",
            type: "GET",
            data: {
                menu_id: window.menuFilters.menuId,
                category_id: window.menuFilters.categoryId,
                search: window.menuFilters.search,
                limit: window.menuFilters.limit,
                order_type_id: orderTypeId,
                delivery_app_id: deliveryAppId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success && response.items) {
                    window.renderMenuItems(response.items, response.total_count, response.loaded_count);
                }
                $('#menu-loading-indicator').addClass('hidden');
            },
            error: function() {
                $('#menu-loading-indicator').addClass('hidden');
            }
        });
    };

    window.applyClientCategoryCountsFromCatalog = function() {
        if (!window.__posMenuClientSideCatalog || !Array.isArray(window.posMenuClientCatalog)) {
            return [];
        }

        const menuId = window.menuFilters.menuId;
        const search = (window.menuFilters.search || '').toString().trim().toLowerCase();
        const meta = window.__posCategoryMeta || {};
        const counts = {};

        window.posMenuClientCatalog.forEach(function(item) {
            if (menuId && String(item.menu_id) !== String(menuId)) {
                return;
            }
            if (search) {
                const name = (item.item_name || '').toString().toLowerCase();
                const description = (item.description || '').toString().toLowerCase();
                if (!name.includes(search) && !description.includes(search)) {
                    return;
                }
            }
            const cid = item.item_category_id;
            if (cid == null) {
                return;
            }
            const key = String(cid);
            counts[key] = (counts[key] || 0) + 1;
        });

        const rows = [];
        Object.keys(counts).forEach(function(key) {
            const id = parseInt(key, 10);
            const m = meta[key] || meta[String(id)] || {};
            const sortOrder = typeof m.sort_order === 'number' ? m.sort_order : id;
            const name = m.name || ('#' + id);
            rows.push({
                id: id,
                count: counts[key],
                category_name: name,
                sort_order: sortOrder
            });
        });

        rows.sort(function(a, b) {
            if (a.sort_order !== b.sort_order) {
                return a.sort_order - b.sort_order;
            }
            return a.id - b.id;
        });

        return rows;
    };

    window.renderCategoryFilterFromCounts = function(response) {
        if (typeof jQuery === 'undefined' || typeof $ === 'undefined' || !response || !Array.isArray(response)) {
            return;
        }

        const $select = $('#category-filter');
        if ($select.length) {
            const currentValue = $select.val();
            let html = '<option value="">' + @json(__('app.filterByCategory')) + '</option>';
            response.forEach(function(category) {
                const selected = currentValue == category.id ? 'selected' : '';
                const label = escapeHtml(String(category.category_name ?? ''));
                html += `<option value="${category.id}" ${selected}>${label} (${category.count})</option>`;
            });
            $select.html(html);
        }

        const $firstCategoryButton = $('button[onclick*="filterByCategory"]').first();
        let $categoryGrid = null;
        if ($firstCategoryButton.length) {
            $categoryGrid = $firstCategoryButton.closest('.grid');
        }

        if ($categoryGrid && $categoryGrid.length) {
            let html = '';
            const allSelected = !window.menuFilters.categoryId;
            html += `
                <button type="button" onclick="filterByCategory(null)"
                    class="w-full px-3 py-3 text-xs rounded-lg border transition text-left ${allSelected ? 'border-skin-base bg-skin-base text-white shadow-sm' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-200 hover:border-skin-base hover:text-skin-base'}">
                    <div class="flex items-center justify-between gap-2">
                        <span class="font-medium">Show All</span>
                    </div>
                </button>
            `;

            response.forEach(function(category) {
                const isSelected = window.menuFilters.categoryId == category.id;
                const label = escapeHtml(String(category.category_name ?? ''));
                html += `
                    <button type="button" onclick="filterByCategory(${category.id})"
                        class="w-full px-3 py-3 text-xs rounded-lg border transition text-left ${isSelected ? 'border-skin-base bg-skin-base text-white shadow-sm' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-200 hover:border-skin-base hover:text-skin-base'}">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-medium">${label}</span>
                            <span class="text-[11px] text-gray-500 dark:text-gray-300">(${category.count})</span>
                        </div>
                    </button>
                `;
            });

            $categoryGrid.html(html);
        }
    };

    // Update category counts based on current filters (client catalog: pure JS; else AJAX)
    window.updateCategoryCounts = function() {
        if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
            console.error('jQuery is not loaded');
            return;
        }

        if (!window.menuFilters) {
            window.menuFilters = {
                menuId: null,
                categoryId: null,
                search: '',
                limit: {{ (int) $menuItemsLoaded }}
            };
        }

        if (window.__posMenuClientSideCatalog) {
            if (!Array.isArray(window.posMenuClientCatalog)) {
                return;
            }
            const rows = window.applyClientCategoryCountsFromCatalog();
            window.renderCategoryFilterFromCounts(rows);
            return;
        }

        $.easyAjax({
            url: "{{ route('ajax.pos.categories') }}",
            type: "GET",
            data: {
                menu_id: window.menuFilters.menuId,
                search: window.menuFilters.search,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response && Array.isArray(response)) {
                    window.renderCategoryFilterFromCounts(response);
                }
            },
            error: function(xhr) {
                console.error('Error loading category counts:', xhr);
            }
        });
    };

    // Reset menu filters
    window.resetMenuFilters = function() {
        if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
            console.error('jQuery is not loaded');
            return;
        }

        // Reset filter state
        window.menuFilters = {
            menuId: null,
            categoryId: null,
            search: '',
            limit: {{ (int) $menuItemsLoaded }}
        };

        // Clear form inputs
        $('#products-search').val('');
        $('#menu-filter').val('');
        $('#category-filter').val('');

        // Update menu selection UI
        if (typeof window.updateMenuSelection === 'function') {
            window.updateMenuSelection(null);
        }

        window.loadMenuItems();
        if (!(window.__posMenuClientSideCatalog && Array.isArray(window.posMenuClientCatalog))) {
            window.updateCategoryCounts();
        }
    };
</script>
