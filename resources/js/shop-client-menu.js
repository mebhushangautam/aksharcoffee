/**
 * Customer-site menu browse: full catalog hydration + client-side filters (no Livewire round-trips).
 */
function parseJsonScript(id) {
    const el = document.getElementById(id);
    if (!el || !el.textContent) {
        return null;
    }
    try {
        return JSON.parse(el.textContent);
    } catch (e) {
        console.warn('shop-client-menu: invalid JSON in #' + id, e);
        return null;
    }
}

function csrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

document.addEventListener('alpine:init', () => {
    Alpine.data('ttShopClientMenuBrowse', (livewireId, labels) => ({
        lwId: livewireId,
        labels: labels || {},
        cfg: {},
        catalog: null,
        items: [],
        menus: [],
        menuId: null,
        filterCategoryId: null,
        search: '',
        showVeg: false,
        showHalal: false,
        showAllMenus: false,
        catDdOpen: false,
        orderTypeId: 0,
        cartItemQty: {},
        showOrderTypeOverlay: false,
        orderTypesPick: [],
        orderTypeConfirmedByUser: false,
        pendingCartMutate: null,
        itemDetailOpen: false,
        itemDetailExpanded: false,
        selectedPreviewItem: null,
        typeIconBase: '/img/',

        init() {
            const browseCfg = parseJsonScript('tt-shop-client-browse-config-json');
            const catalog = parseJsonScript('tt-shop-client-catalog-json');

            if (!browseCfg || !catalog || !Array.isArray(catalog.items)) {
                console.warn('shop-client-menu: missing catalog or browse config');
                return;
            }

            this.cfg = { ...browseCfg };
            this.catalog = catalog;
            this.items = catalog.items;
            this.menus = catalog.menus || [];
            this.orderTypeId = Number(catalog.resolved_order_type_id || 0);
            this.orderTypesPick = browseCfg.order_types_pick || [];
            if (browseCfg.force_dine_in_qr) {
                this.orderTypeConfirmedByUser = true;
            } else {
                this.syncOrderTypeConfirmedFromStorage();
            }
            this.showOrderTypeOverlay = this.shouldPromptOrderTypeOnLoad();
            this.bookTableUrl = browseCfg.book_table_url || null;
            this.showBookTableEscape = !!browseCfg.show_book_table_escape;
            if (this.shouldStartFreshOrderFromUrl()) {
                this.resetClientCartQty();
            } else {
                this.cartItemQty = { ...(browseCfg.initialCartItemQty || {}) };
                this.restoreCartQtyFromStorage();
            }

            this.$watch('search', () => {});
            this.$watch('showVeg', () => {});
            this.$watch('showHalal', () => {});

            this.bindLivewireEvents();
            this.bindNewOrderNavigation();
        },

        bindNewOrderNavigation() {
            const self = this;

            const handleNewOrderNav = () => {
                if (!self.shouldStartFreshOrderFromUrl()) {
                    return;
                }
                self.stripNewOrderQueryParam();
                self.resetClientCartQty();
                self.clearOrderTypeConfirmedStorage();
                self.orderTypeConfirmedByUser = false;
                self.showOrderTypeOverlay = self.shouldPromptOrderTypeOnLoad();

                if (typeof Livewire === 'undefined') {
                    return;
                }

                const wires = Livewire.getByName('shop.cart') || [];
                const wire = wires[0];
                if (wire?.call) {
                    wire.call('startNewShopOrder');
                }
            };

            document.addEventListener('livewire:navigated', handleNewOrderNav);
            handleNewOrderNav();
        },

        shouldStartFreshOrderFromUrl() {
            return new URLSearchParams(window.location.search).get('new_order') === '1';
        },

        stripNewOrderQueryParam() {
            const url = new URL(window.location.href);
            if (!url.searchParams.has('new_order')) {
                return;
            }
            url.searchParams.delete('new_order');
            const next = url.pathname + (url.searchParams.toString() ? '?' + url.searchParams.toString() : '');
            window.history.replaceState({}, '', next);
        },

        resetClientCartQty() {
            this.cartItemQty = {};
            const key = this.cfg.cart_storage_key;
            if (!key) {
                return;
            }
            try {
                localStorage.removeItem(key);
            } catch (e) {
                // ignore
            }
        },

        orderTypeConfirmedStorageKey() {
            const cartKey = this.cfg.cart_storage_key || '';
            const hash = cartKey.replace(/^tt_shop_cart_qty_/, '');
            return hash ? `tt_shop_ot_confirmed_${hash}` : null;
        },

        syncOrderTypeConfirmedFromStorage() {
            const key = this.orderTypeConfirmedStorageKey();
            if (key && sessionStorage.getItem(key) === '1') {
                this.orderTypeConfirmedByUser = true;
            }
        },

        persistOrderTypeConfirmedToStorage() {
            const key = this.orderTypeConfirmedStorageKey();
            if (!key) {
                return;
            }
            try {
                sessionStorage.setItem(key, '1');
            } catch (e) {
                // ignore
            }
        },

        clearOrderTypeConfirmedStorage() {
            const key = this.orderTypeConfirmedStorageKey();
            if (!key) {
                return;
            }
            try {
                sessionStorage.removeItem(key);
            } catch (e) {
                // ignore
            }
        },

        shouldPromptOrderTypeOnLoad() {
            if (this.cfg.force_dine_in_qr) {
                return false;
            }
            if (this.orderTypesPick.length <= 1) {
                return false;
            }
            if (this.orderTypeConfirmedByUser) {
                return false;
            }
            return true;
        },

        selectedOrderTypeLabel() {
            const match = this.orderTypesPick.find((ot) => Number(ot.id) === Number(this.orderTypeId));
            return match ? match.name : '';
        },

        openOrderTypeOverlay() {
            if (this.cfg.force_dine_in_qr || this.orderTypesPick.length <= 1) {
                return;
            }
            this.showOrderTypeOverlay = true;
        },

        dismissOrderTypeOverlay() {
            this.showOrderTypeOverlay = false;
        },

        restoreCartQtyFromStorage() {
            const key = this.cfg.cart_storage_key;
            if (!key) {
                return;
            }
            try {
                const raw = localStorage.getItem(key);
                if (!raw) {
                    return;
                }
                const parsed = JSON.parse(raw);
                if (parsed && typeof parsed === 'object') {
                    Object.assign(this.cartItemQty, parsed);
                }
            } catch (e) {
                // ignore
            }
        },

        persistCartQtyToStorage() {
            const key = this.cfg.cart_storage_key;
            if (!key) {
                return;
            }
            try {
                localStorage.setItem(key, JSON.stringify(this.cartItemQty));
            } catch (e) {
                // ignore
            }
        },

        bindLivewireEvents() {
            if (typeof Livewire === 'undefined') {
                return;
            }

            const self = this;

            Livewire.on('shop-client-cart-reset', () => {
                self.resetClientCartQty();
            });

            Livewire.on('shop-client-cart-qty-sync', (payload) => {
                const data = payload?.cartItemQty ?? payload?.[0]?.cartItemQty ?? payload;
                if (data && typeof data === 'object') {
                    self.cartItemQty = { ...data };
                    if (Object.keys(self.cartItemQty).length === 0) {
                        self.resetClientCartQty();
                    } else {
                        self.persistCartQtyToStorage();
                    }
                }
            });

            Livewire.on('shop-client-show-menu', (payload) => {
                const show = payload?.showMenu ?? payload?.[0]?.showMenu ?? payload;
                if (typeof show === 'boolean') {
                    this.cfg.showMenu = show;
                }
            });

            Livewire.on('shop-client-order-type-changed', (payload) => {
                const id = payload?.orderTypeId ?? payload?.[0]?.orderTypeId;
                if (id) {
                    this.orderTypeId = Number(id);
                }
            });
        },

        filteredItems() {
            const menuId = this.menuId;
            const categoryId = this.filterCategoryId;
            const search = (this.search || '').trim().toLowerCase();

            return this.items.filter((item) => {
                if (menuId !== null && Number(item.menu_id) !== Number(menuId)) {
                    return false;
                }
                if (categoryId !== null && Number(item.item_category_id) !== Number(categoryId)) {
                    return false;
                }
                if (this.showVeg && item.type !== 'veg') {
                    return false;
                }
                if (this.showHalal && item.type !== 'halal') {
                    return false;
                }
                if (search) {
                    const blob = (item.search_blob || item.item_name || '').toString().toLowerCase();
                    if (!blob.includes(search)) {
                        return false;
                    }
                }
                return true;
            });
        },

        /** Categories with counts for current menu/search/veg/halal (excluding category tab filter). */
        visibleCategories() {
            const menuId = this.menuId;
            const search = (this.search || '').trim().toLowerCase();
            const counts = {};

            this.items.forEach((item) => {
                if (menuId !== null && Number(item.menu_id) !== Number(menuId)) {
                    return;
                }
                if (this.showVeg && item.type !== 'veg') {
                    return;
                }
                if (this.showHalal && item.type !== 'halal') {
                    return;
                }
                if (search) {
                    const blob = (item.search_blob || item.item_name || '').toString().toLowerCase();
                    if (!blob.includes(search)) {
                        return;
                    }
                }
                const cid = item.item_category_id;
                if (!counts[cid]) {
                    counts[cid] = {
                        id: cid,
                        name: item.category_label || '',
                        sort_order: item.category_sort_order || 0,
                        count: 0,
                    };
                }
                counts[cid].count += 1;
            });

            return Object.values(counts)
                .filter((c) => c.count > 0)
                .sort((a, b) => a.sort_order - b.sort_order || a.id - b.id);
        },

        categoryDropdownLabel() {
            if (this.filterCategoryId === null) {
                return this.labels.showAll || 'Show all';
            }
            const cat = this.visibleCategories().find(
                (c) => Number(c.id) === Number(this.filterCategoryId),
            );
            return cat ? cat.name : this.labels.showAll || 'Show all';
        },

        groupedForDisplay() {
            const filtered = this.filteredItems();
            const byCategoryId = {};
            const catalogCategoryOrder = (this.catalog?.categories || []).map((c) => Number(c.id));

            filtered.forEach((item) => {
                const cid = Number(item.item_category_id);
                if (!byCategoryId[cid]) {
                    byCategoryId[cid] = {
                        key: item.category_label || String(cid),
                        sort: Number(item.category_sort_order) || 0,
                        items: [],
                    };
                }
                byCategoryId[cid].items.push(item);
            });

            const categoryIds = Object.keys(byCategoryId).map(Number);
            categoryIds.sort((a, b) => {
                const sortDiff = byCategoryId[a].sort - byCategoryId[b].sort;
                if (sortDiff !== 0) {
                    return sortDiff;
                }
                const idxA = catalogCategoryOrder.indexOf(a);
                const idxB = catalogCategoryOrder.indexOf(b);
                if (idxA !== -1 && idxB !== -1) {
                    return idxA - idxB;
                }
                return a - b;
            });

            return categoryIds.map((cid) => ({
                key: byCategoryId[cid].key,
                items: byCategoryId[cid].items.sort(
                    (x, y) => (x.sort_order || 0) - (y.sort_order || 0) || x.id - y.id,
                ),
            }));
        },

        setMenuId(id) {
            this.menuId = id === undefined || id === null ? null : Number(id);
            this.filterCategoryId = null;
        },

        setCategory(id) {
            this.filterCategoryId = id === undefined || id === null ? null : Number(id);
        },

        typeIconUrl(type) {
            return this.typeIconBase + (type || 'non-veg') + '.svg';
        },

        truncate(str, max) {
            const s = (str || '').toString();
            if (s.length <= max) {
                return s;
            }
            return s.slice(0, max) + '…';
        },

        itemPriceLabel(item) {
            if (!item) {
                return '';
            }
            const labels = item.price_labels_by_order_type || {};
            const prices = item.prices_by_order_type || {};
            const key = String(this.orderTypeId || 0);
            if (labels[key]) {
                return labels[key];
            }
            const firstKey = Object.keys(labels)[0];
            if (firstKey && labels[firstKey]) {
                return labels[firstKey];
            }
            const p = prices[key] ?? prices[firstKey];
            return p !== undefined ? String(p) : '';
        },

        openItemDetail(itemId) {
            const item = this.items.find((i) => Number(i.id) === Number(itemId));
            if (!item) {
                this.callLw('showItemDetail', itemId);
                return;
            }
            this.selectedPreviewItem = item;
            this.itemDetailExpanded = false;
            this.itemDetailOpen = true;
        },

        closeItemDetail() {
            this.itemDetailOpen = false;
            this.selectedPreviewItem = null;
            this.itemDetailExpanded = false;
        },

        callLw(method, ...args) {
            if (typeof Livewire === 'undefined') {
                return;
            }

            // Livewire.find(id) returns the $wire proxy (not the component instance).
            const wire = Livewire.find(this.lwId);
            if (!wire) {
                console.warn('shop-client-menu: Livewire component not found', this.lwId);
                return;
            }

            if (typeof wire.call === 'function') {
                wire.call(method, ...args);
                return;
            }

            if (typeof wire[method] === 'function') {
                wire[method](...args);
                return;
            }

            const name = this.cfg.livewire_component_name || 'shop.cart';
            Livewire.dispatchTo(name, 'client-menu-remote', { method, args });
        },

        async pickOrderType(orderTypeId) {
            const url = this.cfg.order_type_sync_url;
            if (!url) {
                return;
            }
            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrfToken(),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        order_type_id: orderTypeId,
                        branch_id: this.cfg.branch_id,
                    }),
                });
                const data = await res.json();
                if (!data.ok) {
                    return;
                }
                this.orderTypeId = Number(orderTypeId);
                this.orderTypeConfirmedByUser = true;
                this.persistOrderTypeConfirmedToStorage();
                this.showOrderTypeOverlay = false;
                this.callLw('selectOrderTypeFromModal', orderTypeId);
                this.flushPendingCartMutate();
            } catch (e) {
                console.error('shop-client-menu: order type sync failed', e);
            }
        },

        flushPendingCartMutate() {
            if (!this.pendingCartMutate) {
                return;
            }
            const pending = this.pendingCartMutate;
            this.pendingCartMutate = null;
            this.browseCartMutate(
                pending.action,
                pending.menuItemId,
                pending.variationsCount,
                pending.modifierGroupsCount,
            );
        },

        browseCartMutate(action, menuItemId, variationsCount, modifierGroupsCount) {
            const v = Number(variationsCount || 0);
            const m = Number(modifierGroupsCount || 0);

            if (
                action === 'add'
                && this.orderTypesPick.length > 1
                && !this.orderTypeConfirmedByUser
                && !this.cfg.force_dine_in_qr
            ) {
                this.pendingCartMutate = { action, menuItemId, variationsCount, modifierGroupsCount };
                this.showOrderTypeOverlay = true;
                return;
            }

            if (v > 0 || m > 0) {
                this.callLw('addCartItems', menuItemId, v, m);
                return;
            }

            const url = this.cfg.browse_cart_mutate_url;
            if (!url) {
                return;
            }

            const sessionLocation = this.readSessionLocation();

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    action,
                    menu_item_id: menuItemId,
                    branch_id: this.cfg.branch_id,
                    came_from_qr: !!this.cfg.came_from_qr,
                    address_lat: sessionLocation?.lat ?? null,
                    address_lng: sessionLocation?.lng ?? null,
                }),
            })
                .then((res) => res.json().then((data) => ({ ok: res.ok, data })))
                .then(({ ok, data }) => {
                    if (!ok || !data.ok) {
                        if (data?.error && typeof window.Swal !== 'undefined') {
                            window.Swal.fire({
                                icon: 'error',
                                text: data.error,
                                toast: true,
                                position: 'center',
                                showConfirmButton: true,
                            });
                        }
                        return;
                    }
                    if (data.cart_item_qty) {
                        this.cartItemQty = { ...data.cart_item_qty };
                        this.persistCartQtyToStorage();
                    }
                    if (typeof Livewire !== 'undefined') {
                        const wire = Livewire.find(this.lwId);
                        if (wire?.call) {
                            wire.call('mergeBrowseCartFromSession');
                        } else {
                            Livewire.dispatchTo(
                                this.cfg.livewire_component_name || 'shop.cart',
                                'shop-browse-merge-cart-from-session',
                            );
                        }
                    }
                })
                .catch((e) => console.error('shop-client-menu: cart mutate failed', e));
        },

        readSessionLocation() {
            return null;
        },
    }));
});
