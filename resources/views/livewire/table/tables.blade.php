@php
    $tablesAlpineConfig = [
        'payload' => $tablesPayload,
        'areasList' => $areasList,
        'authUserId' => $authUserId,
        'isRestaurantAdmin' => $isRestaurantAdmin,
        'isWaiterRole' => $isWaiterRole,
        'waiters' => $waiters,
        'canCreateTable' => $canCreateTable,
        'canUpdateTable' => $canUpdateTable,
        'canShowOrder' => (bool) $canShowOrder,
        'canCreateOrder' => (bool) $canCreateOrder,
        'i18n' => $tablesUi,
    ];
@endphp

@once
    @push('before-livewire-scripts')
        <script data-navigate-track>
            (function () {
                function registerTablesPage() {
                    if (window.__tablesPageAlpineRegistered) {
                        return;
                    }
                    window.__tablesPageAlpineRegistered = true;
                    Alpine.data('tablesPage', (config) => ({
                    areasAll: config.payload.areas ?? [],
                    areasForTabs: config.areasList ?? [],
                    viewType: (() => {
                        const v = localStorage.getItem('table_view_type');
                        return ['list', 'grid', 'layout'].includes(v) ? v : 'list';
                    })(),
                    filterAvailable: null,
                    areaID: null,
                    authUserId: config.authUserId,
                    isRestaurantAdmin: config.isRestaurantAdmin,
                    isWaiterRole: config.isWaiterRole,
                    waiters: config.waiters ?? [],
                    canCreateTable: config.canCreateTable,
                    canUpdateTable: config.canUpdateTable,
                    canShowOrder: !!config.canShowOrder,
                    canCreateOrder: !!config.canCreateOrder,
                    i18n: config.i18n,
                    addTableOpen: false,
                    filterMenuOpen: false,
                    setViewType(v) {
                        this.viewType = v;
                        localStorage.setItem('table_view_type', v);
                    },
                    setFilter(f) {
                        this.filterAvailable = f;
                        this.filterMenuOpen = false;
                    },
                    setArea(id) {
                        this.areaID = id;
                    },
                    openAddTable() {
                        this.addTableOpen = true;
                        this.$wire.set('showAddTableModal', true);
                    },
                    closeAddTable() {
                        this.addTableOpen = false;
                        this.$wire.set('showAddTableModal', false);
                    },
                    visibleAreas() {
                        let areas = this.areasAll;
                        if (this.areaID !== null) {
                            areas = areas.filter((a) => Number(a.id) === Number(this.areaID));
                        }
                        return areas
                            .map((area) => ({
                                ...area,
                                tables: (area.tables || []).filter((t) => {
                                    if (this.filterAvailable && t.available_status !== this.filterAvailable) {
                                        return false;
                                    }
                                    return true;
                                }),
                            }))
                            .filter((a) => a.tables.length > 0);
                    },
                    sessionLocked(session) {
                        return !!(session && session.locked_by_user_id);
                    },
                    lockClasses(table) {
                        const s = table.session;
                        if (!this.sessionLocked(s)) {
                            return '';
                        }
                        if (Number(s.locked_by_user_id) === Number(this.authUserId)) {
                            return 'bg-blue-500 hover:bg-blue-600';
                        }
                        return 'bg-red-500 hover:bg-red-600';
                    },
                    gridTableClass(table) {
                        const base = 'relative flex w-full min-h-[11rem] cursor-pointer flex-col items-center justify-center rounded-lg border p-2 text-center text-gray-800 transition-all duration-150 hover:shadow-md dark:text-gray-100 sm:min-h-[12rem] sm:p-3 md:hover:scale-[1.02]';
                        if (table.status === 'inactive') {
                            return base + ' border-gray-200 bg-gray-100 opacity-50';
                        }
                        const s = table.session;
                        if (s && s.locked_by_user_id) {
                            if (Number(s.locked_by_user_id) === Number(this.authUserId)) {
                            return base + ' border-blue-300 bg-blue-100 dark:border-blue-700 dark:bg-blue-900/40';
                            }
                            return base + ' border-orange-300 bg-orange-100 opacity-75 dark:border-orange-700 dark:bg-orange-900/40';
                        }
                        if (table.available_status === 'available') {
                            return base + ' border-green-200 bg-green-100 dark:border-green-700 dark:bg-green-900/35';
                        }
                        if (table.available_status === 'reserved') {
                            return base + ' border-red-200 bg-red-100 dark:border-red-700 dark:bg-red-900/35';
                        }
                        if (table.available_status === 'running') {
                            return base + ' border-blue-200 bg-blue-100 dark:border-blue-700 dark:bg-blue-900/35';
                        }
                        return base + ' border-gray-200 bg-gray-100 dark:border-gray-600 dark:bg-gray-700';
                    },
                }));
                }
                if (typeof Alpine !== 'undefined') {
                    registerTablesPage();
                } else {
                    document.addEventListener('alpine:init', registerTablesPage);
                }
            })();
        </script>
    @endpush
@endonce

<div class="mb-12 pb-14 sm:pb-16">
    {{-- wire:key must NOT be on the Livewire root: changing it breaks morph and can blank the page. --}}
    <div wire:key="tables-board-{{ $tablesPayloadSignature }}" x-data="tablesPage(@js($tablesAlpineConfig))">
    <div class="block items-center justify-between bg-white p-4 dark:bg-gray-800 dark:border-gray-700 sm:flex">
        <div class="mb-1 w-full">
            <div class="mb-4">
                <h1 class="text-base font-semibold text-gray-900 dark:text-white" x-text="i18n.tableView"></h1>
            </div>
            <div class="block items-center justify-between sm:flex dark:divide-gray-700">
                <div class="flex flex-wrap items-center gap-4">
                    <div class="inline-flex max-w-full shrink-0 overflow-x-auto rounded-lg shadow-sm [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                        <button type="button" @click="setViewType('list')"
                            :class="viewType === 'list'
                                ? 'relative z-10 inline-flex items-center rounded-l-lg bg-skin-base px-3 py-2 text-sm font-medium text-white dark:bg-skin-base/50'
                                : 'relative inline-flex items-center rounded-l-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">
                            <svg class="me-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                            <span x-text="i18n.list"></span>
                        </button>
                        <button type="button" @click="setViewType('grid')"
                            :class="viewType === 'grid'
                                ? 'relative z-10 inline-flex items-center bg-skin-base px-3 py-2 text-sm font-medium text-white dark:bg-skin-base/50'
                                : 'relative inline-flex items-center border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">
                            <svg class="me-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                            </svg>
                            <span x-text="i18n.grid"></span>
                        </button>
                        <button type="button" @click="setViewType('layout')"
                            :class="viewType === 'layout'
                                ? 'relative z-10 inline-flex items-center rounded-r-lg bg-skin-base px-3 py-2 text-sm font-medium text-white dark:bg-skin-base/50'
                                : 'relative inline-flex items-center rounded-r-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">
                            <svg class="me-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" />
                            </svg>
                            <span x-text="i18n.layout"></span>
                        </button>
                    </div>

                    <div class="relative z-10" @keydown.escape.window="filterMenuOpen = false">
                        <button type="button" @click="filterMenuOpen = !filterMenuOpen"
                            class="inline-flex items-center rounded-md border border-transparent py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none">
                            <span x-show="filterAvailable === null" x-text="i18n.filterAvailable"></span>
                            <span x-show="filterAvailable !== null" class="font-bold text-gray-800 dark:text-neutral-200">
                                <span x-text="i18n.showing"></span>
                                <span x-text="filterAvailable === 'available' ? i18n.available : (filterAvailable === 'running' ? i18n.running : i18n.reserved)"></span>
                            </span>
                            <svg class="-mr-1 ml-1.5 h-5 w-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path clip-rule="evenodd" fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                            </svg>
                        </button>
                        <div x-show="filterMenuOpen" x-cloak @click.outside="filterMenuOpen = false"
                            class="absolute left-0 mt-1 w-48 rounded-md border border-gray-200 bg-white py-1 shadow-lg dark:border-gray-700 dark:bg-gray-800">
                            <button type="button" class="block w-full px-4 py-2 text-left text-sm text-gray-600 hover:bg-gray-100 dark:text-neutral-400 dark:hover:bg-gray-700" @click="setFilter(null)" x-text="i18n.showAll"></button>
                            <button type="button" class="flex w-full items-center gap-2 px-4 py-2 text-left text-sm text-gray-600 hover:bg-gray-100 dark:text-neutral-400 dark:hover:bg-gray-700" @click="setFilter('available')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-circle-fill text-green-500" viewBox="0 0 16 16"><circle cx="8" cy="8" r="8"/></svg>
                                <span x-text="i18n.available"></span>
                            </button>
                            <button type="button" class="flex w-full items-center gap-2 px-4 py-2 text-left text-sm text-gray-600 hover:bg-gray-100 dark:text-neutral-400 dark:hover:bg-gray-700" @click="setFilter('running')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-circle-fill text-blue-500" viewBox="0 0 16 16"><circle cx="8" cy="8" r="8"/></svg>
                                <span x-text="i18n.running"></span>
                            </button>
                            <button type="button" class="flex w-full items-center gap-2 px-4 py-2 text-left text-sm text-gray-600 hover:bg-gray-100 dark:text-neutral-400 dark:hover:bg-gray-700" @click="setFilter('reserved')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-circle-fill text-red-500" viewBox="0 0 16 16"><circle cx="8" cy="8" r="8"/></svg>
                                <span x-text="i18n.reserved"></span>
                            </button>
                        </div>
                    </div>
                </div>

                @if ($canCreateTable)
                    <div class="mt-3 flex gap-2 sm:mt-0">
                        <x-button type="button" @click="openAddTable()">{{ __('modules.table.addTable') }}</x-button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="my-4 flex flex-col px-4">
        <div class="mb-6">
            <ul class="mb-4 inline-flex max-w-full flex-wrap gap-x-1 gap-y-2 text-center text-sm font-medium text-gray-500 dark:text-gray-400">
                <li class="me-2">
                    <a href="javascript:;" @click.prevent="setArea(null)"
                        :class="areaID === null ? 'inline-block rounded-lg bg-skin-base/[.2] px-4 py-3 text-skin-base dark:bg-skin-base/[.1]' : 'inline-block rounded-lg px-4 py-3 hover:bg-gray-100 hover:text-gray-900 dark:hover:bg-gray-800 dark:hover:text-white'"
                        x-text="i18n.allAreas"></a>
                </li>
                <template x-for="item in areasForTabs" :key="item.id">
                    <li class="me-2">
                        <a href="javascript:;" @click.prevent="setArea(item.id)"
                            :class="Number(areaID) === Number(item.id) ? 'inline-block rounded-lg bg-skin-base/[.2] px-4 py-3 text-skin-base dark:bg-skin-base/[.1]' : 'inline-block rounded-lg px-4 py-3 hover:bg-gray-100 hover:text-gray-900 dark:hover:bg-gray-800 dark:hover:text-white'"
                            x-text="item.area_name"></a>
                    </li>
                </template>
            </ul>
        </div>

        {{-- Status legend: fixed bottom-right with margin; pointer-events none so it never blocks taps (modals use z-40+) --}}
        <div
            class="pointer-events-none fixed end-4 bottom-[max(1.25rem,calc(0.75rem+env(safe-area-inset-bottom,0px)))] z-30 sm:end-5 sm:bottom-[max(1.5rem,calc(1rem+env(safe-area-inset-bottom,0px)))]">
            <div
                class="inline-flex max-w-[calc(100vw-2rem)] flex-wrap items-center justify-center gap-3 rounded-xl border border-gray-200 bg-white/95 px-3 py-2 shadow-md backdrop-blur-sm sm:max-w-none sm:gap-4 sm:px-4 sm:py-2.5 dark:border-gray-600 dark:bg-gray-800/95 dark:shadow-black/20"
                role="note">
                <div class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-600 dark:text-neutral-400">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-circle-fill shrink-0 text-green-500" viewBox="0 0 16 16"><circle cx="8" cy="8" r="8"/></svg>
                    <span x-text="i18n.available"></span>
                </div>
                <div class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-600 dark:text-neutral-400">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-circle-fill shrink-0 text-blue-500" viewBox="0 0 16 16"><circle cx="8" cy="8" r="8"/></svg>
                    <span x-text="i18n.running"></span>
                </div>
                <div class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-600 dark:text-neutral-400">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-circle-fill shrink-0 text-red-500" viewBox="0 0 16 16"><circle cx="8" cy="8" r="8"/></svg>
                    <span x-text="i18n.reserved"></span>
                </div>
            </div>
        </div>

        <div class="space-y-8">
            <template x-for="area in visibleAreas()" :key="area.id">
                <div class="flex flex-col gap-3 space-y-1 sm:gap-4">
                    <h3 class="f-15 inline-flex items-center gap-2 font-medium dark:text-neutral-200">
                        <span x-text="area.area_name"></span>
                        <span class="rounded border border-gray-300 bg-slate-100 px-2 py-1 text-sm text-gray-800 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                            <span x-text="area.tables.length"></span> {{ __('modules.table.table') }}
                        </span>
                    </h3>

                    {{-- Layout (compact grid, same data as grid view) --}}
                    <div x-show="viewType === 'layout'" x-cloak class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800 sm:p-6">
                        <div class="relative grid grid-cols-3 gap-2 p-2 sm:grid-cols-4 sm:gap-3 sm:p-3 md:grid-cols-6 lg:grid-cols-8">
                            <template x-for="table in area.tables" :key="table.id">
                                <div class="min-w-0 cursor-pointer" @click="$wire.showTableOrder(table.id)">
                                    <div class="flex min-h-[4.5rem] flex-col items-center justify-center rounded-lg border-2 p-2 text-center transition hover:shadow-md sm:min-h-0 sm:p-3"
                                        :class="{
                                            'border-green-300 bg-green-100 dark:border-green-700 dark:bg-green-900/35': table.available_status === 'available' && !sessionLocked(table.session),
                                            'border-red-300 bg-red-100 dark:border-red-700 dark:bg-red-900/35': table.available_status === 'reserved' && !sessionLocked(table.session),
                                            'border-blue-300 bg-blue-100 dark:border-blue-700 dark:bg-blue-900/35': table.available_status === 'running' && !sessionLocked(table.session),
                                            'border-blue-300 bg-blue-100 opacity-90 dark:border-blue-700 dark:bg-blue-900/40': sessionLocked(table.session) && Number(table.session?.locked_by_user_id) === authUserId,
                                            'border-orange-300 bg-orange-100 opacity-75 dark:border-orange-700 dark:bg-orange-900/40': sessionLocked(table.session) && Number(table.session?.locked_by_user_id) !== authUserId,
                                            'opacity-50': table.status === 'inactive'
                                        }">
                                        <span class="text-sm font-bold text-gray-900 dark:text-gray-100" x-text="table.table_code"></span>
                                        <span class="text-xs text-gray-700 dark:text-gray-200" x-text="table.seating_capacity + ' ' + i18n.seats"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Grid: responsive columns — avoid fixed 8 cols on mobile --}}
                    <div x-show="viewType === 'grid'" x-cloak class="grid grid-cols-1 gap-3 rounded-lg bg-gray-50 p-3 dark:bg-gray-800 sm:grid-cols-2 sm:gap-4 sm:p-4 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 min-[1920px]:grid-cols-8">
                        <template x-for="table in area.tables" :key="table.id">
                            <div class="min-w-0" @click="$wire.showTableOrder(table.id)" :class="gridTableClass(table)">
                                <template x-if="sessionLocked(table.session)">
                                    <div class="absolute right-1.5 top-1.5 z-10">
                                        <template x-if="isRestaurantAdmin">
                                            <button type="button" class="group relative rounded-full p-1 text-xs text-white shadow-sm hover:shadow-md"
                                                :class="lockClasses(table)"
                                                @click.stop="$wire.forceUnlockTable(table.id)"
                                                :title="Number(table.session.locked_by_user_id) === authUserId ? i18n.lockedByYou + ' at ' + table.session.locked_at : i18n.forceUnlock + ' at ' + table.session.locked_at">
                                                <svg class="h-4 w-4 transition-all group-hover:scale-0 group-hover:opacity-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14v3m-3-6V7a3 3 0 1 1 6 0v4m-8 0h10a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1v-7a1 1 0 0 1 1-1"/></svg>
                                            </button>
                                        </template>
                                        <template x-if="!isRestaurantAdmin && Number(table.session.locked_by_user_id) === authUserId">
                                            <button type="button" class="rounded-full bg-blue-500 p-1 text-white shadow hover:shadow-md" @click.stop="$wire.forceUnlockTable(table.id)" :title="i18n.lockedByYou + ' at ' + table.session.locked_at">
                                                <svg class="h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14v3m4-6V7a3 3 0 1 1 6 0v4M5 11h10a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-7a1 1 0 0 1 1-1Z"/></svg>
                                            </button>
                                        </template>
                                        <template x-if="!isRestaurantAdmin && Number(table.session.locked_by_user_id) !== authUserId">
                                            <div class="cursor-help rounded-full bg-orange-500 p-1 text-white shadow hover:shadow-md" :title="i18n.locked + ' — ' + (table.session.locked_by_name || '') + ' at ' + table.session.locked_at">
                                                <svg class="h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14v3m-3-6V7a3 3 0 1 1 6 0v4m-8 0h10a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1v-7a1 1 0 0 1 1-1"/></svg>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                                <span class="text-lg font-bold" x-text="table.table_code"></span>
                                <span class="text-xs" x-text="table.seating_capacity + ' ' + i18n.seats"></span>
                                <template x-if="table.is_reservation_active">
                                    <div class="mt-1 rounded bg-white px-2 py-1 text-xs font-medium text-red-600 shadow-sm" x-text="i18n.reserved"></div>
                                </template>
                                <template x-if="(table.running_orders_count || 0) > 0">
                                    <div class="mt-1 text-[11px] text-gray-600 dark:text-gray-300">
                                        <span x-text="(table.running_orders_count || 0) + ' ' + i18n.running"></span>
                                    </div>
                                </template>
                                <template x-if="table.running_orders && table.running_orders.length">
                                    <div class="mt-1 w-full max-w-full space-y-1.5 sm:flex sm:flex-wrap sm:justify-center sm:gap-1 sm:space-y-0">
                                        <template x-for="order in table.running_orders" :key="'grid-order-' + table.id + '-' + order.id">
                                            <div class="flex w-full min-w-0 items-center justify-between gap-2 rounded-md border border-white/40 bg-white/50 px-1.5 py-1 dark:border-gray-600/50 dark:bg-gray-900/30 sm:w-auto sm:inline-flex sm:justify-center sm:border-0 sm:bg-transparent sm:p-0">
                                                <div class="group relative min-w-0 flex-1 sm:flex-initial">
                                                    <button type="button" @click.stop.prevent="$wire.showSpecificOrder(order.id)"
                                                        :class="[
                                                            'inline-flex max-w-full items-center truncate rounded px-1.5 py-0.5 text-left text-[10px] font-medium underline-offset-2 hover:underline',
                                                            (order.status === 'billed' || order.status === 'payment_due')
                                                                ? 'border border-amber-300 bg-amber-50 text-amber-800 dark:border-amber-700 dark:bg-amber-900/30 dark:text-amber-200'
                                                                : 'bg-white/80 text-gray-700 dark:bg-gray-900/40 dark:text-gray-200'
                                                        ]"
                                                        x-text="(order.formatted_order_number || order.order_number || ('#' + order.id)) + ' • ' + i18n.pax + ': ' + (order.number_of_pax || 0)"></button>
                                                    <div class="pointer-events-none absolute -top-8 left-0 z-20 rounded bg-gray-900 px-2 py-1 text-[10px] font-medium whitespace-nowrap text-white opacity-0 shadow transition-opacity duration-150 group-hover:opacity-100 dark:bg-gray-700">
                                                        <span x-text="(order.status === 'billed' || order.status === 'payment_due') ? (i18n.awaitingPayment + ' · ' + i18n.showOrder) : i18n.showOrder"></span>
                                                    </div>
                                                </div>
                                                <div class="group relative shrink-0">
                                                    <button type="button" @click.stop.prevent="$wire.newKotForSpecificOrder(order.id)"
                                                        class="inline-flex items-center rounded border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-medium text-blue-700 hover:bg-blue-100 dark:border-blue-700 dark:bg-blue-900/30 dark:text-blue-300">+KOT</button>
                                                    <div class="pointer-events-none absolute -top-8 right-0 z-20 rounded bg-gray-900 px-2 py-1 text-[10px] font-medium whitespace-nowrap text-white opacity-0 shadow transition-opacity duration-150 group-hover:opacity-100 dark:bg-gray-700">
                                                        <span x-text="i18n.newKot"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="table.seats_left !== null">
                                    <div class="mt-1 text-[11px] text-gray-600 dark:text-gray-300" x-text="i18n.remaining + ': ' + table.seats_left + ' ' + i18n.seats"></div>
                                </template>
                                <template x-if="waiters && waiters.length && !isWaiterRole">
                                    <button type="button"
                                        class="mt-1 rounded-md border px-2 py-1 text-xs shadow-sm dark:bg-gray-700 dark:text-white"
                                        :class="table.assigned_waiter ? 'border-green-300 bg-green-50 text-green-700 dark:border-green-600 dark:text-green-300' : 'border-gray-300 bg-white dark:border-gray-600'"
                                        @click.stop="$wire.showWaiterSelect(table.id)"
                                        x-text="table.assigned_waiter ? table.assigned_waiter.name : i18n.assignWaiter"></button>
                                </template>
                            </div>
                        </template>
                    </div>

                    {{-- List --}}
                    <div x-show="viewType === 'list'" x-cloak class="grid grid-cols-1 items-start gap-3 sm:grid-cols-2 sm:gap-6 md:grid-cols-3 lg:grid-cols-4">
                        <template x-for="table in area.tables" :key="table.id">
                            <a href="javascript:;"
                                :class="{
                                    'relative flex cursor-pointer flex-col gap-2 overflow-hidden rounded-lg border p-3 shadow-sm hover:shadow-md dark:border-gray-600 dark:bg-gray-700': true,
                                    'bg-red-50': table.status === 'inactive',
                                    'bg-white hover:bg-gray-50': table.status === 'active'
                                }"
                                @click.prevent="
                                    (table.running_orders_count || 0) === 1
                                        ? $wire.showSpecificOrder(table.running_orders[0].id)
                                        : ((table.running_orders_count || 0) > 1
                                            ? null
                                            : $wire.showTableOrder(table.id))
                                ">
                                <div class="space-y-2.5">
                                    <div class="flex items-center gap-3">
                                        <div class="flex items-center gap-x-2.5">
                                            <div :class="{
                                                'rounded-lg p-3 tracking-wide whitespace-nowrap': true,
                                                'bg-green-100 text-green-700 dark:bg-green-900/35 dark:text-green-300': table.available_status === 'available',
                                                'bg-red-100 text-red-700 dark:bg-red-900/35 dark:text-red-300': table.available_status === 'reserved',
                                                'bg-blue-100 text-blue-700 dark:bg-blue-900/35 dark:text-blue-300': table.available_status === 'running'
                                            }">
                                                <h3 class="font-semibold whitespace-nowrap" x-text="table.table_code"></h3>
                                            </div>
                                            <template x-if="sessionLocked(table.session)">
                                                <div>
                                                    <template x-if="isRestaurantAdmin">
                                                        <button type="button" class="group relative rounded-full p-1 text-xs text-white shadow-sm hover:shadow-md"
                                                            :class="lockClasses(table)"
                                                            @click.stop.prevent="$wire.forceUnlockTable(table.id)"
                                                            :title="Number(table.session.locked_by_user_id) === authUserId ? i18n.lockedByYou + ' at ' + table.session.locked_at : i18n.forceUnlock + ' at ' + table.session.locked_at">
                                                            <svg class="h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14v3m-3-6V7a3 3 0 1 1 6 0v4m-8 0h10a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1v-7a1 1 0 0 1 1-1"/></svg>
                                                        </button>
                                                    </template>
                                                    <template x-if="!isRestaurantAdmin && Number(table.session.locked_by_user_id) === authUserId">
                                                        <button type="button" class="rounded-full bg-blue-500 p-1 text-white shadow hover:shadow-md" @click.stop.prevent="$wire.forceUnlockTable(table.id)" :title="i18n.lockedByYou + ' at ' + table.session.locked_at">
                                                            <svg class="h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14v3m4-6V7a3 3 0 1 1 6 0v4M5 11h10a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-7a1 1 0 0 1 1-1Z"/></svg>
                                                        </button>
                                                    </template>
                                                    <template x-if="!isRestaurantAdmin && Number(table.session.locked_by_user_id) !== authUserId">
                                                        <div class="cursor-help rounded-full bg-orange-500 p-1 text-white shadow hover:shadow-md" :title="i18n.locked + ' — ' + (table.session.locked_by_name || '') + ' at ' + table.session.locked_at">
                                                            <svg class="h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14v3m-3-6V7a3 3 0 1 1 6 0v4m-8 0h10a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1v-7a1 1 0 0 1 1-1"/></svg>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                        <div class="mx-auto text-center text-xs font-medium text-gray-500 dark:text-neutral-200">
                                            <p class="whitespace-nowrap" x-text="table.seating_capacity + ' ' + i18n.seats"></p>
                                            <p class="whitespace-nowrap" x-text="i18n.remaining + ': ' + (table.seats_left !== null ? table.seats_left + ' ' + i18n.seats : '--')"></p>
                                        </div>
                                        @if ($canUpdateTable)
                                            <div class="group relative">
                                                <x-secondary-button class="!p-1.5 text-xs leading-none" type="button" @click.stop.prevent="$wire.showEditTable(table.id)">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                                        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                                    </svg>
                                                </x-secondary-button>
                                                <div class="pointer-events-none absolute -top-5 right-0 rounded-md bg-gray-900 px-2 py-1 text-[10px] font-medium leading-none whitespace-nowrap text-white opacity-0 shadow transition-opacity duration-150 group-hover:opacity-100 dark:bg-gray-700">
                                                    {{ __('modules.table.editTable') }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <template x-if="table.running_orders && table.running_orders.length">
                                        <div class="border-t border-gray-200 pt-2 dark:border-gray-600">
                                            <div class="flex max-w-full flex-wrap items-center gap-2">
                                            <template x-for="order in table.running_orders" :key="'list-order-' + table.id + '-' + order.id">
                                                <div class="inline-flex items-center gap-1 rounded-md border border-gray-200 bg-white px-1 py-1 dark:border-gray-600 dark:bg-gray-700/40">
                                                    <div class="group relative">
                                                        <button type="button" @click.stop.prevent="$wire.showSpecificOrder(order.id)"
                                                            :class="[
                                                                'inline-flex items-center rounded-md border px-2 py-0.5 text-[11px] font-medium leading-5 transition',
                                                                (order.status === 'billed' || order.status === 'payment_due')
                                                                    ? 'border-amber-300 bg-amber-50 text-amber-800 hover:bg-amber-100 dark:border-amber-700 dark:bg-amber-900/30 dark:text-amber-200'
                                                                    : 'border-gray-200 bg-gray-50 text-gray-700 hover:border-skin-base hover:bg-skin-base/10 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200'
                                                            ]"
                                                            x-text="'#' + (order.formatted_order_number || order.order_number || order.id) + ' · P' + (order.number_of_pax || 0)"></button>
                                                        <div class="pointer-events-none absolute -top-8 left-0 rounded bg-gray-900 px-2 py-1 text-[10px] font-medium whitespace-nowrap text-white opacity-0 shadow transition-opacity duration-150 group-hover:opacity-100 dark:bg-gray-700">
                                                            <span x-text="(order.status === 'billed' || order.status === 'payment_due') ? (i18n.awaitingPayment + ' · ' + i18n.showOrder) : i18n.showOrder"></span>
                                                        </div>
                                                    </div>
                                                    <div class="group relative">
                                                        <button type="button" @click.stop.prevent="$wire.newKotForSpecificOrder(order.id)"
                                                            class="inline-flex items-center rounded-md border border-blue-200 bg-blue-50 px-2 py-0.5 text-[11px] font-medium leading-5 text-blue-700 transition hover:bg-blue-100 dark:border-blue-700 dark:bg-blue-900/30 dark:text-blue-300">+KOT</button>
                                                        <div class="pointer-events-none absolute -top-8 right-0 rounded bg-gray-900 px-2 py-1 text-[10px] font-medium whitespace-nowrap text-white opacity-0 shadow transition-opacity duration-150 group-hover:opacity-100 dark:bg-gray-700">
                                                            <span x-text="i18n.newKot"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="waiters && waiters.length && !isWaiterRole">
                                        <button type="button"
                                            class="mt-0.5 rounded-md border px-2 py-1 text-xs shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                            :class="table.assigned_waiter ? 'border-green-300 bg-green-50 text-green-700 dark:border-green-600 dark:text-green-300' : 'border-gray-300 bg-white dark:border-gray-600'"
                                            @click.stop.prevent="$wire.showWaiterSelect(table.id)"
                                            x-text="table.assigned_waiter ? table.assigned_waiter.name : i18n.assignWaiter"></button>
                                    </template>
                                </div>
                                <div class="flex w-full flex-wrap items-center justify-between gap-4">
                                    <template x-if="table.is_reservation_active">
                                        <div class="inline-flex flex-col items-start rounded border border-red-400 bg-red-100 px-2 py-1 text-xs font-medium uppercase tracking-wide whitespace-nowrap text-red-800 dark:border-red-700 dark:bg-red-900/35 dark:text-red-300">
                                            <span x-text="i18n.reserved"></span>
                                        </div>
                                    </template>
                                </div>
                            </a>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- Add Table: JS modal --}}
    <div
        x-cloak
        class="jetstream-modal fixed inset-0 z-40 overflow-y-auto overflow-x-hidden px-4 py-6 sm:px-0"
        style="display: none;"
        x-show="addTableOpen"
        x-on:keydown.escape.window="closeAddTable()"
        @close-add-table-modal.window="closeAddTable()"
        >
        <div
            x-show="addTableOpen"
            class="fixed inset-0 transform transition-all"
            x-on:click="closeAddTable()"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <div class="absolute inset-0 bg-gray-500 opacity-75 dark:bg-gray-900"></div>
        </div>

        <div
            x-show="addTableOpen"
            class="jetstream-modal fixed top-0 right-0 left-0 mb-6 flex h-screen max-h-full w-screen max-w-full transform flex-col overflow-y-auto overflow-x-hidden border-0 bg-white shadow-xl transition-all dark:bg-gray-800 sm:left-auto sm:max-w-md"
            x-trap.noscroll="addTableOpen"
            x-on:click.stop
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
        >
            <div class="flex-1 px-6 py-4">
                <div class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ __("modules.table.addTable") }}
                </div>
                <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                    @if ($showAddTableModal)
                        @livewire('forms.addTable', key('tables-add-table-form'))
                    @endif
                </div>
            </div>
            <div class="flex flex-row justify-end border-t border-gray-200 bg-gray-100 px-6 py-4 dark:border-gray-700 dark:bg-gray-800">
                <button type="button" @click="closeAddTable()"
                    class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:border-gray-500 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:focus:ring-offset-gray-800">
                    {{ __('app.close') }}
                </button>
            </div>
        </div>
    </div>

    @if ($activeTable)
    <x-right-modal wire:model.live="showEditTableModal">
        <x-slot name="title">
            {{ __("modules.table.editTable") }}
        </x-slot>

        <x-slot name="content">
            @livewire('forms.editTable', ['activeTable' => $activeTable], key(str()->random(50)))
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showEditTableModal', false)" wire:loading.attr="disabled">
                {{ __('app.close') }}
            </x-secondary-button>
        </x-slot>
    </x-right-modal>
    @endif

    <x-right-modal wire:model.live="showAssignWaiterModal" maxWidth="md" wire:key="assign-waiter-modal-{{ $selectedTableId ?? 'new' }}">
        <x-slot name="title">
            @if($selectedTable)
                {{ __('modules.table.assignWaiter') }} - {{ $selectedTable->table_code ?? '' }}
            @endif
        </x-slot>

        <x-slot name="content">
            @if($selectedTable)
                <div class="mt-6" wire:key="assign-form-{{ $selectedTableId }}">
                    @livewire('forms.assign-waiter-to-table-form', ['tableId' => $selectedTableId], key('assign-waiter-' . $selectedTableId))
                </div>
            @endif
        </x-slot>

        <x-slot name="footer">
            <div class="flex gap-2">
                <x-secondary-button wire:click="$set('showAssignWaiterModal', false)" wire:loading.attr="disabled">
                    {{ __('app.close') }}
                </x-secondary-button>
                <x-button wire:click="$dispatch('saveWaiterAssignment')" wire:loading.attr="disabled">
                    {{ __('app.save') }}
                </x-button>
            </div>
        </x-slot>
    </x-right-modal>

    <x-right-modal wire:model.live="showUpdateWaiterModal" maxWidth="md" wire:key="update-waiter-modal-{{ $selectedTableId ?? 'new' }}">
        <x-slot name="title">
            @if($selectedTable)
                {{ __('modules.table.updateWaiterAssignment') }} - {{ $selectedTable->table_code ?? '' }}
            @endif
        </x-slot>

        <x-slot name="content">
            @if($selectedTable)
                <div class="mt-6" wire:key="update-form-{{ $selectedTableId }}">
                    @livewire('forms.update-waiter-to-table-form', ['tableId' => $selectedTableId], key('update-waiter-' . $selectedTableId))
                </div>
            @endif
        </x-slot>

        <x-slot name="footer">
            <div class="flex gap-2">
                <x-secondary-button wire:click="$set('showUpdateWaiterModal', false)" wire:loading.attr="disabled">
                    {{ __('app.close') }}
                </x-secondary-button>
                <x-button wire:click="showUpdateConfirmation" wire:loading.attr="disabled">
                    {{ __('app.update') }}
                </x-button>
            </div>
        </x-slot>
    </x-right-modal>

    <x-confirmation-modal wire:model.defer="showUpdateConfirmationModal">
        <x-slot name="title">
            {{ __('app.update') }} {{ __('modules.table.assignWaiter') }}?
        </x-slot>

        <x-slot name="content">
            @if($currentWaiter && $this->currentWaiterUser)
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('modules.table.currentWaiter') }}: <strong>{{ $this->currentWaiterUser->name }}</strong>
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                    {{ __('messages.waiterAssignmentUpdateConfirmation') }}
                </p>
            @else
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('messages.waiterAssignmentUpdateConfirmation') }}
                </p>
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showUpdateConfirmationModal', false)" wire:loading.attr="disabled">
                {{ __('app.cancel') }}
            </x-secondary-button>

            <x-button class="ml-3" wire:click="confirmUpdateWaiter" wire:loading.attr="disabled">
                {{ __('app.update') }}
            </x-button>
        </x-slot>
    </x-confirmation-modal>

    </div>
</div>
