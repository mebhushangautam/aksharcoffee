@php
    $lwId = $this->getId();
@endphp

<div wire:ignore class="tt-shop-client-menu-root">
    <script type="application/json" id="tt-shop-client-catalog-json">{!! json_encode($clientShopCatalog, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) !!}</script>
    <script type="application/json" id="tt-shop-client-browse-config-json">{!! json_encode($clientShopBrowseConfig, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) !!}</script>

    <div x-data="ttShopClientMenuBrowse('{{ $lwId }}', @js($clientShopBrowseConfig['labels'] ?? []))" x-init="init()" class="min-w-0 w-full max-w-full">
        <div x-show="showOrderTypeOverlay && !cfg.force_dine_in_qr" x-cloak x-transition.opacity
            class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/60 dark:bg-black/70"
            role="dialog" aria-modal="true"
            @keydown.escape.window="dismissOrderTypeOverlay()">
            <div class="relative w-full max-w-xl overflow-hidden bg-white rounded-lg shadow-xl dark:bg-gray-900" @click.stop>
                <button type="button"
                    class="absolute top-3 end-3 z-10 inline-flex items-center justify-center rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-800 dark:hover:text-gray-200"
                    @click="dismissOrderTypeOverlay()"
                    :aria-label="labels.close">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <div class="px-6 pt-6 pb-2 text-center">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white" x-text="labels.selectOrderType"></h2>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400" x-text="labels.selectOrderTypeDescription"></p>
                </div>
                <div class="grid grid-cols-1 gap-4 p-6 sm:grid-cols-2 lg:grid-cols-3">
                    <template x-for="ot in orderTypesPick" :key="'ot-pick-' + ot.id">
                        <button type="button" @click="pickOrderType(ot.id)"
                            :class="Number(orderTypeId) === Number(ot.id)
                                ? 'border-skin-base ring-2 ring-skin-base/30 bg-skin-base/[0.06] dark:bg-skin-base/10'
                                : 'border-gray-200 dark:border-gray-600'"
                            class="flex flex-col items-center justify-center p-6 transition-all duration-200 border-2 rounded-lg hover:border-skin-base hover:shadow-lg dark:hover:border-skin-base group">
                            <div class="flex items-center justify-center w-16 h-16 mb-4 transition-colors rounded-full bg-gray-50 group-hover:bg-skin-base dark:bg-gray-700">
                                <svg class="w-8 h-8 text-gray-600 transition-colors group-hover:text-white dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path x-show="ot.type === 'dine_in'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    <path x-show="ot.type === 'delivery'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                    <path x-show="ot.type === 'pickup'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                            </div>
                            <span class="text-lg font-semibold text-gray-900 dark:text-white" x-text="ot.name"></span>
                            <span class="mt-2 text-sm text-center text-gray-600 dark:text-gray-400" x-show="ot.type === 'dine_in'" x-text="labels.dineInDescription"></span>
                            <span class="mt-2 text-sm text-center text-gray-600 dark:text-gray-400" x-show="ot.type === 'delivery'" x-text="labels.deliveryDescription"></span>
                            <span class="mt-2 text-sm text-center text-gray-600 dark:text-gray-400" x-show="ot.type === 'pickup'" x-text="labels.pickupDescription"></span>
                        </button>
                    </template>
                </div>
                <div class="px-6 pb-4 text-center">
                    <button type="button"
                        class="text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                        @click="dismissOrderTypeOverlay()"
                        x-text="labels.browseMenuContinue"></button>
                </div>
                <div class="px-6 pb-6 text-center" x-show="showBookTableEscape && bookTableUrl" x-cloak>
                    <p class="text-sm text-gray-600 dark:text-gray-400" x-text="labels.bookTableInstead"></p>
                    <a :href="bookTableUrl"
                        class="inline-flex items-center justify-center mt-2 text-sm font-semibold text-skin-base hover:underline">
                        <span x-text="labels.bookTable"></span>
                    </a>
                </div>
            </div>
        </div>

        <div x-show="orderTypesPick.length > 1 && !cfg.force_dine_in_qr" x-cloak class="px-4 mt-3">
            <div class="flex flex-wrap items-center justify-between gap-2 rounded-lg border border-gray-100 bg-white px-3 py-2 shadow-sm dark:border-gray-700 dark:bg-gray-800/80 sm:px-4">
                <div class="min-w-0 text-sm">
                    <template x-if="orderTypeConfirmedByUser && selectedOrderTypeLabel()">
                        <p class="truncate text-gray-700 dark:text-gray-200">
                            <span class="text-xs text-gray-500 dark:text-gray-400" x-text="labels.currentOrderType + ':'"></span>
                            <span class="ms-1 font-semibold" x-text="selectedOrderTypeLabel()"></span>
                        </p>
                    </template>
                    <template x-if="!orderTypeConfirmedByUser">
                        <p class="text-xs text-amber-700 dark:text-amber-300" x-text="labels.orderTypeNotSelectedYet"></p>
                    </template>
                </div>
                <button type="button"
                    @click="openOrderTypeOverlay()"
                    class="inline-flex shrink-0 items-center gap-1 rounded-md border border-skin-base/30 bg-skin-base/[0.08] px-3 py-1.5 text-xs font-semibold text-skin-base transition hover:bg-skin-base/[0.14] dark:border-skin-base/40 dark:bg-skin-base/10">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span x-text="labels.changeOrderType"></span>
                </button>
            </div>
        </div>

        <div class="flex min-w-0 max-w-full flex-col overflow-x-hidden px-4 mt-4 mb-4">
            <div class="grid grid-cols-2 gap-3 lg:grid-cols-4 sm:gap-4">
                <a href="javascript:;" @click.prevent="setMenuId(null)"
                    :class="menuId === null ? 'bg-skin-base dark:bg-skin-base' : 'bg-white dark:bg-gray-700'"
                    class="group flex items-center border shadow-sm rounded-lg hover:shadow-md transition dark:border-gray-600">
                    <div class="p-2 sm:p-3 w-full">
                        <div class="flex items-center gap-3">
                            <div class="hidden p-2 bg-gray-100 rounded-md sm:block dark:bg-gray-600">
                                <svg class="flex-shrink-0 size-5 fill-current text-gray-800 dark:text-gray-100" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 409.221 409.221">
                                    <path d="M387.059 389.218H372.73v-18.114h14.327c5.523 0 10-4.477 10-10 0-55.795-42.81-101.781-97.305-106.843v-17.29c0-5.523-4.477-10-10-10s-10 4.477-10 10v17.29c-54.496 5.062-97.305 51.048-97.305 106.843 0 5.523 4.477 10 10 10h14.327v18.114h-14.327c-5.523 0-10 4.477-10 10s4.477 10 10 10h24.13q.197.004.393 0h145.564l.196.002.196-.002h24.133c5.523 0 10-4.477 10-10s-4.478-10-10-10m-34.33 0H226.772v-18.114h125.957zm-149.714-38.113c4.978-43.447 41.978-77.305 86.736-77.305s81.758 33.858 86.736 77.305zM131.63 97.306c-29.383 0-52.4 16.809-52.4 38.267 0 21.457 23.017 38.265 52.4 38.265s52.399-16.808 52.399-38.265c0-21.459-23.016-38.267-52.399-38.267m0 56.531c-19.094 0-32.4-9.625-32.4-18.265s13.306-18.267 32.4-18.267c19.093 0 32.399 9.627 32.399 18.267s-13.306 18.265-32.399 18.265m23.553 235.383H32.162V68.652h198.936v166.52c0 5.523 4.477 10 10 10s10-4.477 10-10V58.652c0-5.523-4.477-10-10-10h-4.701V10A10.002 10.002 0 0 0 225.215.07L20.979 24.397a10 10 0 0 0-8.817 9.93V399.22c0 5.523 4.477 10 10 10h133.021c5.523 0 10-4.477 10-10s-4.477-10-10-10M32.162 43.206l184.235-21.944v27.391H32.162zm82.627 317.362c-5.523 0-10-4.477-10-10s4.477-10 10-10h33.681c5.523 0 10 4.477 10 10s-4.477 10-10 10z" />
                                </svg>
                            </div>
                            <div class="grow">
                                <h3 :class="menuId === null ? 'text-white' : 'text-gray-800 dark:text-neutral-200'"
                                    class="font-semibold dark:group-hover:text-neutral-400 text-xs lg:text-base"
                                    x-text="labels.showAll"></h3>
                            </div>
                        </div>
                    </div>
                </a>

                <template x-for="(m, index) in menus" :key="'menu-card-' + m.id">
                    <div x-show="showAllMenus || index < 7" x-transition>
                        <a href="javascript:;" @click.prevent="setMenuId(m.id)"
                            :class="menuId === m.id ? 'bg-skin-base dark:bg-skin-base' : 'bg-white dark:bg-gray-700'"
                            class="group flex flex-col border shadow-sm rounded-lg hover:shadow-md transition dark:border-gray-600 dark:hover:bg-gray-600">
                            <div class="p-2 sm:p-3">
                                <div class="flex items-center gap-3">
                                    <svg class="flex-shrink-0 size-5 fill-current text-gray-800 dark:text-gray-100" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 409.221 409.221">
                                        <path d="M387.059 389.218H372.73v-18.114h14.327c5.523 0 10-4.477 10-10 0-55.795-42.81-101.781-97.305-106.843v-17.29c0-5.523-4.477-10-10-10s-10 4.477-10 10v17.29c-54.496 5.062-97.305 51.048-97.305 106.843 0 5.523 4.477 10 10 10h14.327v18.114h-14.327c-5.523 0-10 4.477-10 10s4.477 10 10 10h24.13q.197.004.393 0h145.564l.196.002.196-.002h24.133c5.523 0 10-4.477 10-10s-4.478-10-10-10m-34.33 0H226.772v-18.114h125.957zm-149.714-38.113c4.978-43.447 41.978-77.305 86.736-77.305s81.758 33.858 86.736 77.305zM131.63 97.306c-29.383 0-52.4 16.809-52.4 38.267 0 21.457 23.017 38.265 52.4 38.265s52.399-16.808 52.399-38.265c0-21.459-23.016-38.267-52.399-38.267m0 56.531c-19.094 0-32.4-9.625-32.4-18.265s13.306-18.267 32.4-18.267c19.093 0 32.399 9.627 32.399 18.267s-13.306 18.265-32.399 18.265m23.553 235.383H32.162V68.652h198.936v166.52c0 5.523 4.477 10 10 10s10-4.477 10-10V58.652c0-5.523-4.477-10-10-10h-4.701V10A10.002 10.002 0 0 0 225.215.07L20.979 24.397a10 10 0 0 0-8.817 9.93V399.22c0 5.523 4.477 10 10 10h133.021c5.523 0 10-4.477 10-10s-4.477-10-10-10M32.162 43.206l184.235-21.944v27.391H32.162zm82.627 317.362c-5.523 0-10-4.477-10-10s4.477-10 10-10h33.681c5.523 0 10 4.477 10 10s-4.477 10-10 10z" />
                                    </svg>
                                    <div class="grow min-w-0">
                                        <h3 class="font-semibold group-hover:text-skin-base dark:group-hover:text-gray-100 text-xs lg:text-base truncate"
                                            :class="menuId === m.id ? 'text-white' : 'text-gray-800 dark:text-neutral-200'"
                                            x-text="m.name"></h3>
                                        <p class="text-sm hidden sm:block truncate"
                                            :class="menuId === m.id ? 'text-gray-100' : 'text-gray-500 dark:text-neutral-500'">
                                            <span x-text="m.items_count"></span> <span x-text="labels.itemLabel"></span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </template>

                <template x-if="menus.length === 0">
                    <div class="inline-flex items-center col-span-2 dark:text-gray-400" x-text="labels.noMenuAdded"></div>
                </template>
            </div>

            <template x-if="menus.length > 8">
                <div class="flex justify-center mt-4" x-cloak>
                    <button type="button" @click="showAllMenus = !showAllMenus"
                        class="flex items-center gap-1 text-sm text-skin-base hover:underline">
                        <span x-text="showAllMenus ? labels.showLess : labels.showMore"></span>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': showAllMenus }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                </div>
            </template>
        </div>

        <div class="mx-4 mt-6 min-w-0 max-w-full overflow-x-hidden">
            <div class="relative min-w-0 lg:hidden" @click.away="catDdOpen = false">
                <button type="button" @click="catDdOpen = !catDdOpen"
                    class="w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg px-4 py-2.5 flex items-center justify-between shadow-sm hover:bg-gray-50 transition-colors duration-200">
                    <span class="text-sm font-medium truncate" x-text="categoryDropdownLabel()"></span>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': catDdOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="catDdOpen" x-cloak x-transition
                    class="absolute inset-x-0 z-50 mt-2 max-w-full overflow-hidden bg-white rounded-lg shadow-lg dark:bg-gray-700">
                    <div class="overflow-y-auto max-h-80">
                        <button type="button" @click="setCategory(null); catDdOpen = false"
                            class="w-full px-4 py-2.5 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors"
                            :class="filterCategoryId === null ? 'bg-gray-50 dark:bg-gray-600 text-skin-base' : 'text-gray-700 dark:text-gray-200'"
                            x-text="labels.showAll"></button>
                        <template x-for="c in visibleCategories()" :key="'cat-m-' + c.id">
                            <button type="button" @click="setCategory(c.id); catDdOpen = false"
                                class="w-full px-4 py-2.5 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors flex items-center justify-between"
                                :class="filterCategoryId === c.id ? 'bg-gray-50 dark:bg-gray-600 text-skin-base' : 'text-gray-700 dark:text-gray-200'">
                                <span x-text="c.name"></span>
                                <span class="px-2 py-1 text-xs text-gray-600 bg-gray-100 rounded-full dark:bg-gray-600 dark:text-gray-300" x-text="c.count"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            <div class="hidden p-2 rounded-md lg:block group bg-gray-50 dark:bg-gray-800">
                <nav class="flex gap-2 overflow-x-auto group-hover:[&::-webkit-scrollbar-thumb]:bg-gray-300 dark:group-hover:[&::-webkit-scrollbar-thumb]:bg-gray-600 [&::-webkit-scrollbar]:h-1.5 [&::-webkit-scrollbar-track]:hidden [&::-webkit-scrollbar-thumb]:rounded-full py-2" aria-label="Categories">
                    <button type="button" @click="setCategory(null)"
                        class="px-4 py-2 text-sm font-medium rounded-lg transition-colors whitespace-nowrap"
                        :class="filterCategoryId === null ? 'bg-skin-base text-white shadow-sm' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"
                        x-text="labels.showAll"></button>
                    <template x-for="c in visibleCategories()" :key="'cat-d-' + c.id">
                        <button type="button" @click="setCategory(c.id)"
                            class="px-4 py-2 text-sm font-medium rounded-lg transition-colors inline-flex items-center gap-2 whitespace-nowrap"
                            :class="filterCategoryId === c.id ? 'bg-skin-base text-white shadow-sm' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'">
                            <span x-text="c.name"></span>
                            <span class="px-2 py-0.5 text-xs rounded-full"
                                :class="filterCategoryId === c.id ? 'bg-white/20 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300'"
                                x-text="c.count"></span>
                        </button>
                    </template>
                </nav>
            </div>
        </div>

        <div class="mx-4 my-6 grid min-w-0 max-w-full grid-cols-1 gap-4 overflow-x-hidden sm:grid-cols-3 sm:items-center">
            <div class="col-span-full md:col-span-2">
                <input type="text" x-model.debounce.400ms="search"
                    class="block w-full rounded-md shadow-sm border-gray-300 dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:border-skin-base focus:ring-skin-base"
                    :placeholder="labels.searchPlaceholder" />
            </div>
            <div class="flex flex-row flex-wrap items-center justify-end w-full col-span-2 gap-4 mt-2 md:col-span-1 sm:w-auto">
                @if ($clientShopBrowseConfig['showVeg'] ?? false)
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" x-model="showVeg" class="sr-only peer">
                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 dark:peer-focus:ring-green-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600"></div>
                        <span class="text-sm font-medium text-gray-900 ms-3 dark:text-gray-300">{{ $clientShopBrowseConfig['labels']['typeVeg'] }}</span>
                    </label>
                @endif
                @if ($clientShopBrowseConfig['showHalal'] ?? false)
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" x-model="showHalal" class="sr-only peer">
                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 dark:peer-focus:ring-green-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600"></div>
                        <span class="text-sm font-medium text-gray-900 ms-3 dark:text-gray-300">{{ $clientShopBrowseConfig['labels']['typeHalal'] }}</span>
                    </label>
                @endif
            </div>
        </div>

        <div class="min-w-0 max-w-full overflow-x-hidden px-4 mb-32 space-y-4 lg:gap-8" x-show="cfg.showMenu" x-cloak>
            <template x-for="group in groupedForDisplay()" :key="'grp-' + group.key">
                <div>
                    <h3 class="my-4 text-base font-semibold text-gray-900 lg:text-xl dark:text-white" x-text="group.key"></h3>
                    <div class="space-y-4 lg:space-y-0 lg:grid lg:grid-cols-3 lg:gap-8">
                        <template x-for="item in group.items" :key="'it-' + item.id">
                            <div :class="item.in_stock ? 'bg-white dark:bg-gray-900' : 'bg-gray-100 dark:bg-gray-800'"
                                class="flex min-w-0 max-w-full items-center justify-between gap-4 border shadow-sm rounded-lg hover:shadow-md transition dark:border-gray-600 dark:lg:bg-gray-900 dark:shadow-sm lg:rounded-md">
                                <div class="flex min-w-0 w-full gap-3 p-3 cursor-pointer sm:gap-4"
                                    @click="openItemDetail(item.id)">
                                    <template x-if="!cfg.hideImages">
                                        <img class="object-cover w-16 h-16 rounded-md cursor-pointer lg:w-24 lg:h-24" :src="item.item_photo_url" :alt="item.item_name"
                                            @click="openItemDetail(item.id)" />
                                    </template>
                                    <div class="flex flex-col w-full gap-1 text-sm font-normal text-gray-500 lg:text-base dark:text-gray-400">
                                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm font-semibold text-gray-900 lg:text-base dark:text-white cursor-pointer hover:opacity-90"
                                            @click="openItemDetail(item.id)">
                                            <span class="inline-flex min-w-0 flex-1 items-center">
                                                <img :src="typeIconUrl(item.type)" class="h-4 mr-1 shrink-0" alt="" />
                                                <span class="min-w-0 break-words" x-text="item.item_name"></span>
                                            </span>
                                        </div>
                                        <template x-if="cfg && cfg.euAllergensEnabled !== false && item.eu_allergens_display && item.eu_allergens_display.length">
                                            <div class="flex w-full min-w-0 flex-wrap gap-1.5" @click.stop>
                                                <template x-for="row in item.eu_allergens_display" :key="'eu-card-' + item.id + '-' + row.key">
                                                    <span class="inline-flex max-w-full items-center gap-1.5 rounded-md border border-amber-200/85 bg-amber-50/95 px-2 py-1 dark:border-amber-700/50 dark:bg-amber-950/35">
                                                        <img :src="row.icon" alt="" class="h-4 w-4 shrink-0 object-contain" width="16" height="16" loading="lazy" />
                                                        <span class="max-w-[12rem] truncate text-xs font-medium leading-tight text-gray-800 dark:text-gray-100 sm:max-w-[14rem]" x-text="row.label"></span>
                                                    </span>
                                                </template>
                                            </div>
                                        </template>
                                        <template x-if="item.dietary_labels_display && item.dietary_labels_display.length">
                                            <div class="flex w-full min-w-0 flex-wrap gap-1.5" @click.stop
                                                role="group"
                                                :aria-label="labels.dietaryLabelsHeading">
                                                <template x-for="row in item.dietary_labels_display" :key="'diet-card-' + item.id + '-' + row.key">
                                                    <span class="inline-flex max-w-full items-center gap-1.5 rounded-md border border-emerald-200/85 bg-emerald-50/95 px-2 py-1 dark:border-emerald-700/50 dark:bg-emerald-950/35">
                                                        <img :src="row.icon" alt="" class="h-4 w-4 shrink-0 object-contain" width="16" height="16" loading="lazy" />
                                                        <span class="max-w-[12rem] truncate text-xs font-medium leading-tight text-emerald-900 dark:text-emerald-100 sm:max-w-[14rem]" x-text="row.label"></span>
                                                    </span>
                                                </template>
                                            </div>
                                        </template>
                                        <template x-if="item.description">
                                            <div class="w-full text-xs font-normal text-gray-500 cursor-pointer lg:text-sm dark:text-gray-400"
                                                @click="openItemDetail(item.id)"
                                                x-text="truncate(item.description, 50)"></div>
                                        </template>
                                        <template x-if="item.preparation_time">
                                            <div class="inline-flex items-center my-1 text-xs font-normal text-gray-700 dark:text-gray-400 max-w-56">
                                                <span x-text="labels.preparationTime + ' : ' + item.preparation_time + ' ' + labels.minutes"></span>
                                            </div>
                                        </template>
                                        <div class="flex items-center justify-between w-full" @click.stop>
                                            <div>
                                                <template x-if="item.variations_count == 0">
                                                    <span class="font-semibold text-gray-900 dark:text-white" x-text="itemPriceLabel(item)"></span>
                                                </template>
                                            </div>
                                            {{-- x-show (not nested template x-if) so controls appear after init() merges cfg inside x-for --}}
                                            <div class="flex flex-col items-end gap-2 shrink-0" x-show="cfg.canCreateOrder">
                                                <div class="text-red-500" x-show="!item.in_stock" x-text="labels.outOfStock"></div>
                                                <div class="flex flex-col items-end gap-2 w-full min-w-0 sm:w-auto" x-show="item.in_stock && cfg.allowCustomerOrders">
                                                    <div class="relative flex items-center justify-start max-w-24 me-2" x-show="(cartItemQty[item.id] || 0) > 0">
                                                        <button type="button" class="h-8 p-3 border border-gray-300 bg-gray-50 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 rounded-s-md"
                                                            @click="item.variations_count > 0 ? callLw('subCartItems', item.id) : (item.modifier_groups_count > 0 ? callLw('subModifiers', item.id) : browseCartMutate('dec', item.id, item.variations_count, item.modifier_groups_count))">
                                                            <svg class="w-2 h-2 text-gray-900 dark:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16"/></svg>
                                                        </button>
                                                        <input type="text" readonly :value="cartItemQty[item.id] || 0"
                                                            class="min-w-10 bg-white border-x-0 border-gray-300 h-8 text-center text-gray-900 text-sm block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                                                        <button type="button" class="h-8 p-3 border border-gray-300 bg-gray-50 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 rounded-e-md"
                                                            @click="(item.variations_count > 0 || item.modifier_groups_count > 0) ? callLw('addCartItems', item.id, item.variations_count, item.modifier_groups_count) : browseCartMutate('inc', item.id, item.variations_count, item.modifier_groups_count)">
                                                            <svg class="w-2 h-2 text-gray-900 dark:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16"/></svg>
                                                        </button>
                                                    </div>
                                                    <button type="button"
                                                        x-show="(cartItemQty[item.id] || 0) <= 0 && cfg.orderLimitOk"
                                                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-white border border-transparent rounded-md shadow-sm bg-skin-base hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-skin-base"
                                                        @click.stop="browseCartMutate('add', item.id, item.variations_count, item.modifier_groups_count)"
                                                        x-text="labels.add"></button>
                                                </div>
                                                <button type="button" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600"
                                                    x-show="item.in_stock && !cfg.allowCustomerOrders && item.variations_count > 0"
                                                    @click="callLw('showItemVariations', item.id)">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="w-4 h-4 mr-1" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/></svg>
                                                    <span x-text="labels.showVariations + ' (' + item.variations_count + ')'"></span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            <template x-if="groupedForDisplay().length === 0">
                <div class="flex flex-col items-center justify-center p-6 text-center text-gray-500 dark:text-gray-400">
                    <svg width="100" height="100" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none">
                        <path d="M4 14a8 8 0 0 1 16 0z" fill="#e5e7eb" />
                        <rect x="3" y="14" width="18" height="2.5" rx=".5" fill="#d1d5db" />
                        <circle cx="12" cy="4.5" r=".8" fill="#9ca3af" />
                        <circle cx="9.5" cy="10" r=".5" fill="#4b5563" />
                        <circle cx="14.5" cy="10" r=".5" fill="#4b5563" />
                    </svg>
                    <span class="text-lg" x-text="labels.noItemAdded"></span>
                </div>
            </template>

        </div>

        <div x-show="itemDetailOpen" x-cloak x-transition.opacity
            class="fixed inset-0 z-[80] flex items-center justify-center p-4 bg-black/60 dark:bg-black/70"
            role="dialog" aria-modal="true" @keydown.escape.window="closeItemDetail()">
            <div class="flex flex-col w-full max-w-sm max-h-[85vh] overflow-hidden bg-white rounded-lg shadow-xl dark:bg-gray-900"
                @click.stop>
                <div class="flex items-center justify-between flex-shrink-0 px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="labels.itemDescription"></h3>
                    <button type="button" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                        @click="closeItemDetail()">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    <template x-if="selectedPreviewItem">
                        <div class="flex flex-col flex-1 min-h-0 overflow-hidden">
                            <template x-if="!cfg.hideImages && selectedPreviewItem.item_photo_url">
                                <div class="flex w-full h-48 shrink-0 items-center justify-center overflow-hidden bg-gray-100 dark:bg-gray-800">
                                    <img :src="selectedPreviewItem.item_photo_url" :alt="selectedPreviewItem.item_name"
                                        class="max-h-48 max-w-full w-auto h-auto object-contain"
                                        loading="lazy"
                                        decoding="async" />
                                </div>
                            </template>
                            <div class="flex flex-col flex-1 min-h-0 gap-2 px-4 pb-4 overflow-y-auto"
                                :class="(!cfg.hideImages && selectedPreviewItem.item_photo_url) ? 'pt-3' : 'pt-4'">
                                <div class="flex flex-col gap-1">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="selectedPreviewItem.item_name"></h3>
                                    <template x-if="selectedPreviewItem.description">
                                        <div>
                                            <template x-if="String(selectedPreviewItem.description || '').length > 100">
                                                <div>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                                        <span x-show="!itemDetailExpanded" x-text="truncate(selectedPreviewItem.description, 100)"></span>
                                                        <span x-show="itemDetailExpanded" x-text="selectedPreviewItem.description"></span>
                                                    </p>
                                                    <button type="button" class="mt-1 text-sm font-medium text-skin-base"
                                                        @click="itemDetailExpanded = !itemDetailExpanded"
                                                        x-text="itemDetailExpanded ? labels.showLess : labels.showMore"></button>
                                                </div>
                                            </template>
                                            <template x-if="String(selectedPreviewItem.description || '').length <= 100">
                                                <p class="text-sm text-gray-500 dark:text-gray-400" x-text="selectedPreviewItem.description"></p>
                                            </template>
                                        </div>
                                    </template>
                                    <template x-if="selectedPreviewItem.preparation_time">
                                        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 shrink-0">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span x-text="labels.preparationTime + ' ' + selectedPreviewItem.preparation_time + ' ' + labels.minutes"></span>
                                        </div>
                                    </template>
                                    <template x-if="cfg && cfg.euAllergensEnabled !== false && selectedPreviewItem.eu_allergens_display && selectedPreviewItem.eu_allergens_display.length">
                                        <div class="mt-2 rounded-lg border border-amber-200/90 bg-amber-50 px-3 py-2.5 dark:border-amber-700/60 dark:bg-amber-950/35"
                                            role="region"
                                            :aria-label="labels.allergensHeading">
                                            <div class="mb-2 text-xs font-semibold uppercase tracking-wide text-amber-900 dark:text-amber-200"
                                                x-text="labels.allergensHeading"></div>
                                            <ul class="m-0 flex list-none flex-col gap-2.5 p-0">
                                                <template x-for="row in selectedPreviewItem.eu_allergens_display" :key="'eu-' + selectedPreviewItem.id + '-' + row.key">
                                                    <li class="flex items-center gap-3">
                                                        <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center sm:h-10 sm:w-10" aria-hidden="true">
                                                            <img :src="row.icon" alt="" class="max-h-8 max-w-8 object-contain sm:max-h-9 sm:max-w-9" width="36" height="36" loading="lazy" />
                                                        </span>
                                                        <span class="flex-1 text-sm font-medium leading-normal text-gray-900 dark:text-gray-100" x-text="row.label"></span>
                                                    </li>
                                                </template>
                                            </ul>
                                        </div>
                                    </template>
                                    <template x-if="selectedPreviewItem.dietary_labels_display && selectedPreviewItem.dietary_labels_display.length">
                                        <div class="mt-2 rounded-lg border border-emerald-200/90 bg-emerald-50 px-3 py-2.5 dark:border-emerald-700/60 dark:bg-emerald-950/35"
                                            role="region"
                                            :aria-label="labels.dietaryLabelsHeading">
                                            <div class="mb-2 text-xs font-semibold uppercase tracking-wide text-emerald-900 dark:text-emerald-200"
                                                x-text="labels.dietaryLabelsHeading"></div>
                                            <ul class="m-0 flex list-none flex-col gap-2.5 p-0">
                                                <template x-for="row in selectedPreviewItem.dietary_labels_display" :key="'diet-' + selectedPreviewItem.id + '-' + row.key">
                                                    <li class="flex items-center gap-3">
                                                        <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center sm:h-10 sm:w-10" aria-hidden="true">
                                                            <img :src="row.icon" alt="" class="max-h-8 max-w-8 object-contain sm:max-h-9 sm:max-w-9" width="36" height="36" loading="lazy" />
                                                        </span>
                                                        <span class="flex-1 text-sm font-medium leading-normal text-gray-900 dark:text-gray-100" x-text="row.label"></span>
                                                    </li>
                                                </template>
                                            </ul>
                                        </div>
                                    </template>
                                    <p class="text-base font-semibold text-gray-900 dark:text-white" x-text="itemPriceLabel(selectedPreviewItem)"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="flex justify-end flex-shrink-0 px-4 py-3 space-x-2 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/60">
                    <button type="button"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-white border border-transparent rounded-md shadow-sm bg-skin-base hover:opacity-90"
                        x-show="selectedPreviewItem && cfg.canCreateOrder && cfg.allowCustomerOrders && selectedPreviewItem.in_stock"
                        @click="browseCartMutate('add', selectedPreviewItem.id, selectedPreviewItem.variations_count || 0, selectedPreviewItem.modifier_groups_count || 0); closeItemDetail()"
                        x-text="labels.add"></button>
                    <button type="button" class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600"
                        @click="closeItemDetail()"
                        x-text="labels.cancel || 'Cancel'"></button>
                </div>
            </div>
        </div>
    </div>
</div>
