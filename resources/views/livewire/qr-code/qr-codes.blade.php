<div
    x-data="ttQrCodesPage(@js($printableQrItems), @js($restaurantName), @js(__('modules.table.bulkQrPrintTitle')))"
    x-on:keydown.escape.window="closeModal()"
    class="tt-qr-codes-root"
>
    <div class="tt-qr-no-print p-4 bg-white block sm:flex items-center justify-between dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full mb-1">
            <h1 class="text-base font-semibold text-gray-900 dark:text-white">@lang('menu.qrCodes')</h1>
        </div>
    </div>

    <div class="tt-qr-no-print flex flex-col my-4 px-4">
        <div class="mb-4 lg:flex lg:items-start lg:justify-between gap-4">
            <ul class="inline-flex flex-wrap text-sm font-medium text-center text-gray-500 dark:text-gray-400 mb-4 lg:mb-0">
                <li class="me-2">
                    <button type="button" @click="setAreaFilter(null)" :class="areaTabClass(null)">
                        @lang('modules.table.allAreas')
                    </button>
                </li>
                @foreach ($areas as $item)
                    <li class="me-2">
                        <button type="button" @click="setAreaFilter({{ $item->id }})" :class="areaTabClass({{ $item->id }})">
                            {{ $item->area_name }}
                        </button>
                    </li>
                @endforeach
            </ul>

            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <span class="text-xs text-gray-500 dark:text-gray-400" x-text="selectedCountLabel"></span>
                <x-secondary-button type="button" @click="toggleSelectAll()" class="text-xs" x-bind:disabled="visibleItems.length === 0">
                    <span x-text="allSelected ? @js(__('modules.table.unselectAll')) : @js(__('modules.package.selectAll'))"></span>
                </x-secondary-button>
                <x-button type="button" @click="printSelected()" class="text-xs" x-bind:disabled="selectedCount === 0">
                    @lang('modules.table.printSelectedQrCodes')
                </x-button>
                <x-secondary-button type="button" @click="printAll()" class="text-xs">
                    @lang('modules.table.printAllQrCodes')
                </x-secondary-button>
            </div>
        </div>

        <div class="mb-6 lg:flex lg:justify-end">
            <div
                class="inline-flex items-center gap-3 lg:fixed bottom-10 right-5 lg:bg-white lg:px-3 lg:py-2 lg:shadow-md lg:rounded-md dark:lg:bg-gray-800">
                <div class="inline-flex items-center text-sm text-gray-600 gap-1 font-medium dark:text-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-circle-fill text-green-500" viewBox="0 0 16 16">
                        <circle cx="8" cy="8" r="8" />
                    </svg>
                    @lang('modules.table.available')
                </div>
                <div class="inline-flex items-center text-sm text-gray-600 gap-1 font-medium dark:text-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-circle-fill text-blue-500" viewBox="0 0 16 16">
                        <circle cx="8" cy="8" r="8" />
                    </svg>
                    @lang('modules.table.running')
                </div>
                <div class="inline-flex items-center text-sm text-gray-600 gap-1 font-medium dark:text-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-circle-fill text-red-500" viewBox="0 0 16 16">
                        <circle cx="8" cy="8" r="8" />
                    </svg>
                    @lang('modules.table.reserved')
                </div>
            </div>
        </div>

        <div class="space-y-8">
            @if (branch()->qRCodeUrl)
                @php
                    $branchItem = collect($printableQrItems)->firstWhere('kind', 'branch');
                @endphp
                @if ($branchItem)
                    <div class="flex flex-col gap-3" x-show="branchSectionVisible()" x-cloak>
                        <h3 class="text-sm font-medium text-gray-700 dark:text-neutral-200">
                            @lang('modules.table.branchMenuQr')
                        </h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3">
                            @include('livewire.qr-code.partials.qr-code-card', [
                                'item' => $branchItem,
                                'tableModel' => null,
                            ])
                        </div>
                    </div>
                @endif
            @endif

            @foreach ($tables as $area)
                <div class="flex flex-col gap-3" x-show="areaSectionVisible({{ $area->id }})" x-cloak>
                    <h3 class="f-15 font-medium inline-flex gap-2 items-center dark:text-neutral-200">
                        {{ $area->area_name }}
                        <span
                            class="px-2 py-1 text-sm rounded bg-slate-100 border-gray-300 border text-gray-800 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                            {{ $area->tables->count() }} @lang('modules.table.table')
                        </span>
                    </h3>

                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3">
                        @foreach ($area->tables as $item)
                            @php
                                $tableItem = collect($printableQrItems)->firstWhere('id', 'table-' . $item->id);
                            @endphp
                            @if ($tableItem)
                                @include('livewire.qr-code.partials.qr-code-card', [
                                    'item' => $tableItem,
                                    'tableModel' => $item,
                                ])
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Preview modal --}}
    <div
        x-show="modalOpen"
        x-cloak
        class="tt-qr-no-print fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
        role="dialog"
        aria-modal="true"
        @click.self="closeModal()"
    >
        <div class="w-full max-w-md overflow-hidden bg-white rounded-xl shadow-xl dark:bg-gray-800" @click.stop>
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                <div class="min-w-0">
                    <h3 class="text-base font-semibold text-gray-900 truncate dark:text-white" x-text="modalItem?.label"></h3>
                    <p class="text-xs text-gray-500 truncate dark:text-gray-400" x-text="modalItem?.subtitle"></p>
                </div>
                <button type="button" @click="closeModal()"
                    class="p-1 text-gray-400 rounded-lg hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="flex justify-center p-6 bg-gray-50 dark:bg-gray-900/50">
                <img :src="modalItem?.image_url" :alt="modalItem?.label"
                    class="max-h-64 max-w-full object-contain rounded-lg bg-white p-2 shadow-sm">
            </div>
            <div class="flex flex-wrap items-center justify-center gap-3 px-4 py-4 border-t border-gray-100 dark:border-gray-700">
                <button type="button" x-show="modalItem?.kind === 'table'" x-cloak
                    @click="$wire.downloadQrCode(modalItem.table_code, modalItem.branch_id)"
                    class="inline-flex items-center px-3 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-4 h-4 me-1">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    @lang('app.download')
                </button>
                <button type="button" x-show="modalItem?.kind === 'branch'" x-cloak
                    @click="$wire.downloadBranchQrCode()"
                    class="inline-flex items-center px-3 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-4 h-4 me-1">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    @lang('app.download')
                </button>
                <a :href="modalItem?.visit_url" target="_blank" rel="noopener"
                    class="inline-flex items-center px-3 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-4 h-4 me-1">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                    </svg>
                    @lang('app.visitLink')
                </a>
                <button type="button" x-show="modalItem" x-cloak @click="printModalItem()"
                    class="inline-flex items-center px-3 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-4 h-4 me-1">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6.72 13.829A2.25 2.25 0 009 11.25h6A2.25 2.25 0 0117.28 13.83M6.72 13.829l-1.09 3.255A2.25 2.25 0 007.5 19.5h9a2.25 2.25 0 002.87-2.416l-1.09-3.255M6.72 13.829V9.75A2.25 2.25 0 019 7.5h6a2.25 2.25 0 012.25 2.25v4.079M6.72 13.829H4.5m13.5 0H19.5" />
                    </svg>
                    @lang('app.print')
                </button>
                <button type="button" x-show="modalItem?.kind === 'table'" x-cloak
                    @click="$wire.generateQrCode(modalItem.table_id)"
                    class="inline-flex items-center px-3 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-4 h-4 me-1">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                    @lang('modules.table.generateQrCode')
                </button>
                <button type="button" x-show="modalItem?.kind === 'branch'" x-cloak
                    @click="$wire.generateQrCode()"
                    class="inline-flex items-center px-3 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-4 h-4 me-1">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                    @lang('modules.table.generateQrCode')
                </button>
            </div>
        </div>
    </div>

    {{-- Bulk print sheet (browser print only) --}}
    <div class="tt-qr-print-sheet">
        <div class="tt-qr-print-header">
            <div class="tt-qr-print-meta" x-text="printDate"></div>
            <h1 class="tt-qr-print-title" x-text="printTitle"></h1>
            <div class="tt-qr-print-meta" x-text="restaurantName"></div>
        </div>
        <div class="tt-qr-print-grid">
            <template x-for="item in printBatch" :key="'print-' + item.id">
                <div class="tt-qr-print-cell">
                    <div class="tt-qr-print-label" x-text="item.label"></div>
                    <div class="tt-qr-print-sub" x-text="item.subtitle"></div>
                    <img :src="item.image_url" :alt="item.label" class="tt-qr-print-img">
                </div>
            </template>
        </div>
    </div>

    @push('styles')
        <style>
            @media screen {
                .tt-qr-print-sheet {
                    position: fixed;
                    left: -10000px;
                    top: 0;
                    width: 1px;
                    height: 1px;
                    overflow: hidden;
                    opacity: 0;
                    pointer-events: none;
                }
            }

            @media print {
                body * {
                    visibility: hidden;
                }

                .tt-qr-print-sheet,
                .tt-qr-print-sheet * {
                    visibility: visible;
                }

                .tt-qr-print-sheet {
                    position: absolute;
                    left: 0;
                    top: 0;
                    width: 100%;
                    height: auto;
                    overflow: visible;
                    opacity: 1;
                    pointer-events: auto;
                    background: #fff;
                    color: #111;
                    padding: 8mm;
                    box-sizing: border-box;
                }

                .tt-qr-no-print {
                    display: none !important;
                }
            }

            .tt-qr-print-header {
                text-align: center;
                margin-bottom: 6mm;
            }

            .tt-qr-print-title {
                font-size: 16pt;
                font-weight: 700;
                margin: 2mm 0;
            }

            .tt-qr-print-meta {
                font-size: 9pt;
                color: #444;
            }

            .tt-qr-print-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 5mm;
            }

            .tt-qr-print-cell {
                break-inside: avoid;
                page-break-inside: avoid;
                border: 1px solid #ccc;
                border-radius: 2mm;
                padding: 4mm;
                text-align: center;
            }

            .tt-qr-print-label {
                font-size: 12pt;
                font-weight: 700;
                margin-bottom: 1mm;
            }

            .tt-qr-print-sub {
                font-size: 9pt;
                color: #555;
                margin-bottom: 3mm;
            }

            .tt-qr-print-img {
                display: block;
                margin: 0 auto;
                max-width: 100%;
                max-height: 42mm;
                width: auto;
                height: auto;
                object-fit: contain;
            }
        </style>
    @endpush

    <script>
        if (typeof window.ttQrCodesPage !== 'function') {
            window.ttQrCodesPage = function ttQrCodesPage(items, restaurantName, bulkPrintTitle) {
                return {
                    items: items || [],
                    activeAreaId: null,
                    restaurantName: restaurantName || '',
                    printTitle: bulkPrintTitle || 'Bulk QR Code Print',
                    selected: {},
                    modalOpen: false,
                    modalItem: null,
                    printBatch: [],
                    printDate: '',
                    selectedCountLabel: '',

                    init() {
                        this.updateSelectedLabel();
                    },

                    setAreaFilter(id) {
                        this.activeAreaId = id;
                    },

                    areaTabClass(id) {
                        const active = id === null
                            ? this.activeAreaId === null
                            : Number(this.activeAreaId) === Number(id);

                        return active
                            ? 'inline-block px-4 py-3 rounded-lg text-skin-base dark:bg-skin-base/[.1] bg-skin-base/[.2]'
                            : 'inline-block px-4 py-3 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-white';
                    },

                    branchSectionVisible() {
                        return this.activeAreaId === null;
                    },

                    areaSectionVisible(areaId) {
                        return this.activeAreaId === null
                            || Number(this.activeAreaId) === Number(areaId);
                    },

                    get visibleItems() {
                        if (this.activeAreaId === null) {
                            return this.items;
                        }

                        return this.items.filter((item) => {
                            if (item.kind === 'branch') {
                                return false;
                            }

                            return Number(item.area_id) === Number(this.activeAreaId);
                        });
                    },

                    get selectedCount() {
                        return Object.keys(this.selected).filter((k) => this.selected[k]).length;
                    },

                    get allSelected() {
                        const visible = this.visibleItems;

                        return visible.length > 0
                            && visible.every((item) => this.selected[item.id]);
                    },

                    isSelected(id) {
                        return !!this.selected[id];
                    },

                    toggleSelected(id) {
                        this.selected[id] = !this.selected[id];
                        this.updateSelectedLabel();
                    },

                    selectAll() {
                        this.visibleItems.forEach((item) => {
                            this.selected[item.id] = true;
                        });
                        this.updateSelectedLabel();
                    },

                    unselectAll() {
                        this.selected = {};
                        this.updateSelectedLabel();
                    },

                    toggleSelectAll() {
                        if (this.allSelected) {
                            this.unselectAll();
                        } else {
                            this.selectAll();
                        }
                    },

                    updateSelectedLabel() {
                        const n = this.selectedCount;
                        const template = @js(__('modules.table.selectedQrLabels'));
                        this.selectedCountLabel = template.replace(':count', String(n));
                    },

                    openModal(item) {
                        this.modalItem = item;
                        this.modalOpen = true;
                    },

                    closeModal() {
                        this.modalOpen = false;
                        this.modalItem = null;
                    },

                    getSelectedItems() {
                        return this.items.filter((item) => this.selected[item.id]);
                    },

                    runPrint(batch) {
                        if (!batch.length) {
                            return;
                        }
                        this.printBatch = batch;
                        this.printDate = new Date().toLocaleString();
                        this.$nextTick(() => {
                            setTimeout(() => window.print(), 150);
                        });
                    },

                    printSelected() {
                        this.runPrint(this.getSelectedItems());
                    },

                    printAll() {
                        this.runPrint(this.visibleItems);
                    },

                    printModalItem() {
                        if (!this.modalItem) {
                            return;
                        }
                        this.runPrint([this.modalItem]);
                    },
                };
            };
        }
    </script>
</div>
