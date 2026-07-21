/**
 * Blade POS (/pos) offline queue — UI aligned with posvue OfflineIndicator:
 * top status line, pill badge (Offline / Syncing N / Online), modal listing queued saves.
 */
const STORAGE_KEY = "pos_blade_offline_queue";

let saveOrderUrl = "";
let syncPaymentUrl = "";
let initDone = false;
let syncInProgress = false;
let pendingModalOpen = false;
let optsRef = {
    currencyCode: "USD",
    currencySymbol: "$",
    labels: {},
    navGuardLeaveUrl: "/",
};

let navGuardModalOpen = false;
let reloadGuardModalOpen = false;
let navGuardPendingLeaveUrl = null;
let navGuardHistoryInstalled = false;
let navGuardHardLeaving = false;
let navGuardPopstateBound = false;
let navGuardClickBound = false;

/**
 * Effective connectivity for POS offline UX (queue, chrome, nav guard).
 * When window.__posForceOfflineTest is true, behave as offline while the real network stays up.
 */
function isPosEffectiveOnline() {
    if (typeof navigator === "undefined" || navigator.onLine === false) {
        return false;
    }
    if (typeof window !== "undefined" && window.__posForceOfflineTest) {
        return false;
    }
    return true;
}

function deepClone(obj) {
    try {
        return JSON.parse(JSON.stringify(obj));
    } catch (e) {
        return null;
    }
}

function getCsrfToken() {
    const m = document.querySelector('meta[name="csrf-token"]');
    return m ? m.getAttribute("content") || "" : "";
}

function loadQueue() {
    try {
        const raw = localStorage.getItem(STORAGE_KEY);
        if (!raw) {
            return [];
        }
        const parsed = JSON.parse(raw);
        return Array.isArray(parsed) ? parsed : [];
    } catch (e) {
        return [];
    }
}

function saveQueue(queue) {
    try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(queue));
    } catch (e) {
        console.error("POS offline: could not persist queue", e);
    }
}

function esc(s) {
    if (s == null || s === "") {
        return "";
    }
    return String(s)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;");
}

function lbl(key, fallback) {
    const v = optsRef.labels && optsRef.labels[key];
    return v != null && v !== "" ? v : fallback;
}

function tpl(key, fallback, vars) {
    let s = lbl(key, fallback);
    if (vars && typeof vars === "object") {
        Object.keys(vars).forEach((k) => {
            s = s.split(`:${k}`).join(String(vars[k]));
        });
    }
    return s;
}

function formatOrderTypeLabel(rawOrderType) {
    const raw = (rawOrderType == null ? "" : String(rawOrderType)).trim();
    if (!raw) {
        return "—";
    }
    const normalizedKey = raw.toLowerCase().replace(/[\s-]+/g, "_");
    const orderTypeMap =
        optsRef && optsRef.labels && typeof optsRef.labels.orderTypeMap === "object"
            ? optsRef.labels.orderTypeMap
            : null;
    if (orderTypeMap && orderTypeMap[normalizedKey]) {
        return String(orderTypeMap[normalizedKey]);
    }
    // Humanized fallback for unknown/custom slugs.
    return raw
        .replace(/[_-]+/g, " ")
        .replace(/\s+/g, " ")
        .trim()
        .replace(/\b\w/g, (m) => m.toUpperCase());
}

function formatCurrency(amount) {
    const n = Number(amount) || 0;
    // Keep offline queue currency rendering identical to main POS totals.
    if (typeof window !== "undefined" && typeof window.formatCurrency === "function") {
        try {
            return window.formatCurrency(n);
        } catch (e) {
            // Fall through to local formatter
        }
    }
    try {
        return new Intl.NumberFormat(undefined, {
            style: "currency",
            currency: optsRef.currencyCode || "USD",
        }).format(n);
    } catch (e) {
        return `${optsRef.currencySymbol || "$"}${n.toFixed(2)}`;
    }
}

function formatDate(iso) {
    if (!iso) {
        return "—";
    }
    try {
        const d = new Date(iso);
        return d.toLocaleString(undefined, {
            month: "short",
            day: "numeric",
            year: "numeric",
            hour: "2-digit",
            minute: "2-digit",
        });
    } catch (e) {
        return iso;
    }
}

function resolveCurrentPosOrderIdFallback() {
    // Prefer shared POS resolver when available.
    if (
        typeof window !== "undefined" &&
        typeof window.getCurrentPosOrderId === "function"
    ) {
        const resolved = Number(window.getCurrentPosOrderId());
        if (Number.isFinite(resolved) && resolved > 0) {
            return resolved;
        }
    }

    // Fallback to current location /pos/kot/{id}.
    try {
        const path =
            typeof window !== "undefined" && window.location
                ? String(window.location.pathname || "")
                : "";
        const m = path.match(/\/pos\/kot\/(\d+)(?:\/)?$/);
        if (m && m[1]) {
            const id = Number(m[1]);
            if (Number.isFinite(id) && id > 0) {
                return id;
            }
        }
    } catch (e) {
        // ignore
    }

    // Last chance: direct posState fields.
    const ps = typeof window !== "undefined" ? window.posState || {} : {};
    const id = Number(ps.orderID || (ps.orderDetail && ps.orderDetail.id) || 0);
    return Number.isFinite(id) && id > 0 ? id : null;
}

function toFiniteNumber(value, fallback = 0) {
    const n = Number(value);
    return Number.isFinite(n) ? n : fallback;
}

function hydrateOfflineOrderIntoPos(ops, orderId, options) {
    const opts = options && typeof options === "object" ? options : {};
    const newKotEmptyCart = !!opts.newKotEmptyCart;

    if (
        typeof window === "undefined" ||
        !window.posState ||
        !Array.isArray(ops) ||
        !ops.length
    ) {
        return false;
    }

    const sortedOps = ops
        .slice()
        .sort((a, b) => {
            const ta = new Date(a && a.createdAt ? a.createdAt : 0).getTime();
            const tb = new Date(b && b.createdAt ? b.createdAt : 0).getTime();
            return (Number.isFinite(ta) ? ta : 0) - (Number.isFinite(tb) ? tb : 0);
        });

    const lastOp = sortedOps[sortedOps.length - 1] || {};
    const payload = (lastOp && lastOp.payload) || {};
    const summary = mergeSummary((lastOp && lastOp.summary) || {}, payload);

    const ps = window.posState;

    ps.__posOfflineAppendToQueuedOrder = !!newKotEmptyCart;

    // Keep current POS screen, but rebuild state from offline cache.
    const resolvedOrderId =
        Number.isFinite(Number(orderId)) && Number(orderId) > 0
            ? Number(orderId)
            : null;
    ps.orderID = resolvedOrderId;
    ps.orderDetail = {
        ...(ps.orderDetail || {}),
        id: resolvedOrderId,
        status: "kot",
    };
    ps.orderStatus = "kot";
    ps.showOrderDetail = false;
    ps.orderType = payload.order_type_display || payload.order_type || ps.orderType;
    ps.orderTypeSlug = payload.order_type || payload.order_type_slug || ps.orderTypeSlug;
    ps.orderTypeId = payload.order_type_id || ps.orderTypeId;
    ps.tableId = payload.table_id || ps.tableId || null;
    ps.customerId = payload.customer_id || ps.customerId || null;
    ps.customer = payload.customer || summary.customer || ps.customer || null;
    ps.selectWaiter = payload.waiter_id || ps.selectWaiter || null;
    ps.noOfPax = toFiniteNumber(payload.pax, ps.noOfPax || 1);

    ps.orderItemList = {};
    ps.orderItemVariation = {};
    ps.orderItemQty = {};
    ps.orderItemAmount = {};
    ps.itemModifiersSelected = {};
    ps.orderItemModifiersPrice = {};
    ps.itemNotes = {};
    ps.orderItemTaxDetails = {};

    if (!newKotEmptyCart) {
        sortedOps.forEach((currentOp) => {
            const p = (currentOp && currentOp.payload) || {};
            const s = mergeSummary((currentOp && currentOp.summary) || {}, p);
            const payloadItems = Array.isArray(p.items) ? p.items : [];
            const summaryItems = Array.isArray(s.items) ? s.items : [];

            payloadItems.forEach((row, idx) => {
                const menuItemId = parseInt(row && row.id, 10);
                if (!Number.isFinite(menuItemId) || menuItemId <= 0) {
                    return;
                }

                const sItem = summaryItems[idx] || {};
                const qty = Math.max(1, toFiniteNumber(row.quantity, 1));
                const price = toFiniteNumber(row.price, 0);
                const amount = toFiniteNumber(row.amount, price * qty);
                const key = `offline_kot_${currentOp.id}_${idx}_${menuItemId}`;

                ps.orderItemList[key] = {
                    id: menuItemId,
                    item_name:
                        sItem.name ||
                        (row && row.item_name) ||
                        (row && row.name) ||
                        `Item #${menuItemId}`,
                    name:
                        sItem.name ||
                        (row && row.item_name) ||
                        (row && row.name) ||
                        `Item #${menuItemId}`,
                    price: price,
                };
                ps.orderItemQty[key] = qty;
                ps.orderItemAmount[key] = amount;
                ps.itemModifiersSelected[key] = Array.isArray(row.modifier_ids)
                    ? row.modifier_ids
                          .map((m) => parseInt(m, 10))
                          .filter((m) => Number.isFinite(m) && m > 0)
                    : [];
                ps.orderItemModifiersPrice[key] = 0;
                if (row && row.note) {
                    ps.itemNotes[key] = row.note;
                }

                const variantId = parseInt(row && row.variant_id, 10);
                if (Number.isFinite(variantId) && variantId > 0) {
                    ps.orderItemVariation[key] = {
                        id: variantId,
                        price: price,
                    };
                }
            });
        });
    }

    ps.tableNo = summary.table_no || ps.tableNo || "";
    const runningOrderLabel =
        summary.order_number_label ||
        payload.formatted_order_number ||
        payload.order_number ||
        "";
    ps.orderNumber = runningOrderLabel || "";
    ps.formattedOrderNumber = runningOrderLabel || "";

    if (newKotEmptyCart) {
        const taxInclusive =
            typeof window !== "undefined" &&
            window.posConfig &&
            window.posConfig.taxInclusive;
        ps.discountType = null;
        ps.discountValue = null;
        ps.discountAmount = 0;
        ps.discountApplyOn = taxInclusive ? "total" : "sub_total";
        ps.deliveryFee = 0;
        ps.tipAmount = 0;
        ps.orderNote = null;
        ps.subTotal = 0;
        ps.total = 0;
        ps.discountedTotal = 0;
        ps.totalTaxAmount = 0;
        ps.taxBase = 0;
    } else {
        ps.discountType = payload.discount_type || ps.discountType || "";
        ps.discountValue = toFiniteNumber(payload.discount_value, 0);
        ps.discountAmount = toFiniteNumber(payload.discount_amount, 0);
        ps.discountApplyOn =
            payload.discount_apply_on || ps.discountApplyOn || "total";
        ps.deliveryFee = toFiniteNumber(payload.delivery_fee, 0);
        ps.tipAmount = toFiniteNumber(payload.tip_amount, 0);
        ps.orderNote = payload.order_note || ps.orderNote || "";
        ps.subTotal = toFiniteNumber(payload.sub_total, 0);
        ps.total = toFiniteNumber(payload.total, 0);
        ps.discountedTotal = toFiniteNumber(payload.discounted_total, ps.total);
        ps.totalTaxAmount = toFiniteNumber(payload.total_tax_amount, 0);
        ps.taxBase = toFiniteNumber(payload.tax_base, ps.subTotal);
    }

    if (typeof window.updateOrderTypeDisplay === "function") {
        window.updateOrderTypeDisplay();
    }
    if (typeof window.updateCustomerDisplay === "function") {
        window.updateCustomerDisplay(ps.customer || null);
    }
    if (typeof window.updateTableDisplay === "function" && ps.tableId) {
        window.updateTableDisplay({ id: ps.tableId, table_code: ps.tableNo || "" });
    }
    if (typeof window.updateOrderItemsContainer === "function") {
        window.updateOrderItemsContainer();
    }
    if (typeof window.calculateTotal === "function") {
        window.calculateTotal();
    }
    if (typeof window.updateTotalsDisplay === "function") {
        window.updateTotalsDisplay();
    }

    if (typeof window.__posUpdateRunningOrderBanner === "function") {
        window.__posUpdateRunningOrderBanner();
    }

    if (!newKotEmptyCart && typeof window.showToast === "function") {
        window.showToast(
            "success",
            lbl(
                "offlineOrderLoaded",
                "Offline order loaded. You can add a new KOT now."
            )
        );
    }
    return true;
}

function buildSummaryFromPayload(payload) {
    if (!payload) {
        return { items: [] };
    }
    const items = (payload.items || []).map((it) => ({
        name: `Item #${it.id != null ? it.id : "?"}`,
        quantity: it.quantity,
        price: it.price,
        amount: it.amount,
    }));
    return {
        order_type:
            payload.order_type_display ||
            payload.order_type ||
            payload.order_type_slug ||
            "—",
        order_number_label:
            payload.order_number_label ||
            payload.formatted_order_number ||
            payload.order_number ||
            null,
        table_no: null,
        items,
        customer: payload.customer || null,
        total: payload.total,
        sub_total: payload.sub_total,
        discount_amount: payload.discount_amount,
        total_tax_amount: payload.total_tax_amount,
        delivery_fee: payload.delivery_fee,
        tip_amount: payload.tip_amount,
        extra_charges: Array.isArray(payload.extra_charges)
            ? payload.extra_charges
            : [],
        actions: Array.isArray(payload.actions)
            ? payload.actions.slice()
            : [],
    };
}

function mergeSummary(summary, payload) {
    const fb = buildSummaryFromPayload(payload);
    const s = summary && typeof summary === "object" ? summary : {};
    const items =
        Array.isArray(s.items) && s.items.length > 0 ? s.items : fb.items;
    return { ...fb, ...s, items };
}

function getDisplayForOp(op, index) {
    const payload = op.payload || {};
    const base = mergeSummary(op.summary || {}, payload);
    const items = base.items || [];
    const subtotal =
        base.sub_total != null
            ? base.sub_total
            : items.reduce(
                  (sum, it) =>
                      sum +
                      (Number(it.amount) ||
                          Number(it.price || 0) * Number(it.quantity || 1)),
                  0
              );
    const discount = Number(base.discount_amount || payload.discount_amount || 0);
    const totalTaxAmount = Number(
        base.total_tax_amount != null
            ? base.total_tax_amount
            : payload.total_tax_amount || 0
    );
    const deliveryFee = Number(
        base.delivery_fee != null ? base.delivery_fee : payload.delivery_fee || 0
    );
    const tipAmount = Number(
        base.tip_amount != null ? base.tip_amount : payload.tip_amount || 0
    );
    const extraCharges = Array.isArray(base.extra_charges)
        ? base.extra_charges
        : Array.isArray(payload.extra_charges)
          ? payload.extra_charges
          : [];
    const extraChargeAmount = extraCharges.reduce((sum, charge) => {
        return sum + Number(charge && charge.amount != null ? charge.amount : 0);
    }, 0);
    const total =
        base.total != null
            ? base.total
            : Math.max(
                  0,
                  Number(subtotal) -
                      discount +
                      totalTaxAmount +
                      deliveryFee +
                      tipAmount +
                      extraChargeAmount
              );
    return {
        orderId: payload.order_id || null,
        orderType: formatOrderTypeLabel(base.order_type || payload.order_type || ""),
        orderNumberLabel:
            base.order_number_label ||
            payload.formatted_order_number ||
            payload.order_number ||
            null,
        items,
        customer: base.customer || payload.customer,
        tableNo: base.table_no,
        tableId: payload.table_id,
        total,
        subtotal,
        discount,
        totalTaxAmount,
        deliveryFee,
        tipAmount,
        extraChargeAmount,
        actions: base.actions || payload.actions || [],
        createdAt: op.createdAt,
        badgeLabel:
            base.order_number_label ||
            payload.formatted_order_number ||
            payload.order_number ||
            `${lbl("orderNumberPrefix", "Order")} #${index + 1}`,
    };
}

/**
 * Queue rows for the same POS order (same server order_id, or same offline order label)
 * are grouped into one modal card with multiple KOT sections.
 */
function offlineOrderGroupKey(op) {
    if (!op) {
        return "op:unknown";
    }
    const p = op.payload || {};
    const sum = op.summary || {};
    const ogk = p.offline_queue_group_key || sum.offline_queue_group_key;
    if (ogk != null && String(ogk).trim() !== "") {
        return `ogk:${String(ogk).trim()}`;
    }
    const oid = Number(p.order_id);
    if (Number.isFinite(oid) && oid > 0) {
        return `id:${oid}`;
    }
    const label =
        (op.summary && op.summary.order_number_label) ||
        p.formatted_order_number ||
        p.order_number ||
        "";
    const labelKey = String(label).trim();
    if (labelKey) {
        return `lbl:${labelKey}`;
    }
    return `op:${op.id}`;
}

function groupSaveOrderOps(pending) {
    const map = new Map();
    pending.forEach((op) => {
        const k = offlineOrderGroupKey(op);
        if (!map.has(k)) {
            map.set(k, []);
        }
        map.get(k).push(op);
    });
    return Array.from(map.entries()).map(([key, ops]) => ({
        key,
        ops: ops.slice().sort((a, b) => {
            const ta = new Date(a && a.createdAt ? a.createdAt : 0).getTime();
            const tb = new Date(b && b.createdAt ? b.createdAt : 0).getTime();
            return (Number.isFinite(ta) ? ta : 0) - (Number.isFinite(tb) ? tb : 0);
        }),
    }));
}

/**
 * Pending payments indexed by the same key as save_order groups (ogk:… / id:… / lbl:…).
 */
function indexRecordPaymentsByGroup(queue) {
    const map = new Map();
    queue.forEach((op) => {
        if (!op || op.type !== "record_payment") {
            return;
        }
        const k = offlineOrderGroupKey(op);
        if (!map.has(k)) {
            map.set(k, []);
        }
        map.get(k).push(op);
    });
    map.forEach((ops) => {
        ops.sort((a, b) => {
            const ta = new Date(a && a.createdAt ? a.createdAt : 0).getTime();
            const tb = new Date(b && b.createdAt ? b.createdAt : 0).getTime();
            return (Number.isFinite(ta) ? ta : 0) - (Number.isFinite(tb) ? tb : 0);
        });
    });
    return map;
}

function resolveOfflineSessionKeyForPay(group) {
    if (!group || !group.ops || !group.ops.length) {
        return "";
    }
    const lastOp = group.ops[group.ops.length - 1];
    const p = lastOp && lastOp.payload ? lastOp.payload : {};
    const s = lastOp && lastOp.summary ? lastOp.summary : {};
    const ogk = p.offline_queue_group_key || s.offline_queue_group_key;
    if (ogk != null && String(ogk).trim() !== "") {
        return String(ogk).trim();
    }
    if (group.key && String(group.key).startsWith("ogk:")) {
        return String(group.key).slice(4);
    }
    return "";
}

function paymentQueuedRowsHtml(paymentOps) {
    if (!paymentOps || !paymentOps.length) {
        return "";
    }
    return paymentOps
        .map((op) => {
            const sum = op.summary || {};
            const p = op.payload || {};
            const method = esc(
                String(sum.payment_method || p.payment_method || "—")
            );
            const tendered = formatCurrency(
                sum.tendered != null ? sum.tendered : p.payment_amount
            );
            const change = formatCurrency(
                sum.change != null ? sum.change : p.return_amount
            );
            const due = formatCurrency(
                sum.due_amount != null ? sum.due_amount : p.due_amount
            );
            return `
<div class="rounded-md border border-amber-200/80 dark:border-amber-700/60 bg-white/60 dark:bg-gray-800/40 p-2.5 mb-2 last:mb-0" data-pos-offline-payment-op-id="${esc(op.id)}">
  <div class="flex items-start justify-between gap-2 mb-1.5">
    <span class="text-[11px] font-semibold uppercase tracking-wide text-amber-900 dark:text-amber-200">${esc(
        lbl("offlineQueuedPaymentTitle", "Queued payment")
    )}</span>
    <span class="text-[10px] text-gray-500 dark:text-gray-400">${esc(formatDate(op.createdAt))}</span>
  </div>
  <p class="text-[13px] text-gray-800 dark:text-gray-200 mb-1">${esc(
      lbl("offlinePaymentMethodLabel", "Method")
  )}: <span class="font-semibold uppercase">${method}</span></p>
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-1 text-[12px] text-gray-700 dark:text-gray-300">
    <div><span class="text-gray-500 dark:text-gray-400">${esc(lbl("offlinePaymentDueLabel", "Due"))}</span> ${due}</div>
    <div><span class="text-gray-500 dark:text-gray-400">${esc(lbl("offlinePaymentTenderedLabel", "Tendered"))}</span> ${tendered}</div>
    <div><span class="text-gray-500 dark:text-gray-400">${esc(lbl("offlinePaymentChangeLabel", "Change"))}</span> ${change}</div>
  </div>
</div>`;
        })
        .join("");
}

function aggregateGroupDisplay(ops) {
    const displays = ops.map((op, i) => getDisplayForOp(op, i));
    const d0 = displays[0] || {};
    const subtotal = displays.reduce((s, d) => s + Number(d.subtotal || 0), 0);
    const discount = displays.reduce((s, d) => s + Number(d.discount || 0), 0);
    const totalTaxAmount = displays.reduce(
        (s, d) => s + Number(d.totalTaxAmount || 0),
        0
    );
    const deliveryFee = displays.reduce(
        (s, d) => s + Number(d.deliveryFee || 0),
        0
    );
    const tipAmount = displays.reduce((s, d) => s + Number(d.tipAmount || 0), 0);
    const extraChargeAmount = displays.reduce(
        (s, d) => s + Number(d.extraChargeAmount || 0),
        0
    );
    const total = displays.reduce((s, d) => s + Number(d.total || 0), 0);
    const itemCount = displays.reduce(
        (n, d) => n + (Array.isArray(d.items) ? d.items.length : 0),
        0
    );
    return {
        ...d0,
        subtotal,
        discount,
        totalTaxAmount,
        deliveryFee,
        tipAmount,
        extraChargeAmount,
        total,
        itemCount,
        createdAt:
            ops[0] && ops[0].createdAt ? ops[0].createdAt : d0.createdAt,
    };
}

function postSaveOrder(payload) {
    return new Promise((resolve, reject) => {
        if (typeof jQuery === "undefined") {
            reject(new Error("jQuery is required for POS offline sync"));
            return;
        }

        const data = { ...payload, _token: getCsrfToken() };

        jQuery.ajax({
            url: saveOrderUrl,
            type: "POST",
            dataType: "json",
            data,
            success(response) {
                if (response && response.success) {
                    resolve(response);
                    return;
                }
                reject(
                    new Error(
                        (response && response.message) || "Save failed"
                    )
                );
            },
            error(xhr) {
                let msg = "Save failed";
                if (xhr.status === 419) {
                    msg = lbl(
                        "sessionExpired",
                        "Session expired. Refresh the page, then open POS again to sync queued orders."
                    );
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                reject(new Error(msg));
            },
        });
    });
}

function postSyncPayment(payload) {
    return new Promise((resolve, reject) => {
        if (typeof jQuery === "undefined") {
            reject(new Error("jQuery is required for POS offline sync"));
            return;
        }
        if (!syncPaymentUrl) {
            reject(new Error("Sync payment URL is not configured"));
            return;
        }

        const data = { ...payload, _token: getCsrfToken() };

        jQuery.ajax({
            url: syncPaymentUrl,
            type: "POST",
            dataType: "json",
            data,
            success(response) {
                if (response && response.success) {
                    resolve(response);
                    return;
                }
                reject(
                    new Error(
                        (response && response.message) || "Payment sync failed"
                    )
                );
            },
            error(xhr) {
                let msg = "Payment sync failed";
                if (xhr.status === 419) {
                    msg = lbl(
                        "sessionExpired",
                        "Session expired. Refresh the page, then open POS again to sync queued orders."
                    );
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                reject(new Error(msg));
            },
        });
    });
}

function isBladePosPage() {
    return !!document.getElementById("pos-container");
}

function shouldGuardOfflineNavigation() {
    return !isPosEffectiveOnline() && isBladePosPage();
}

function showOfflineNavGuardModal(leaveUrl) {
    ensureShell();
    hideOfflineReloadConfirmModal();
    navGuardModalOpen = true;
    navGuardPendingLeaveUrl =
        leaveUrl || optsRef.navGuardLeaveUrl || "/";

    const modal = document.getElementById("pos-offline-nav-guard-modal");
    const titleEl = document.getElementById("pos-offline-nav-guard-title");
    const bodyEl = document.getElementById("pos-offline-nav-guard-body");
    const stayBtn = document.getElementById("pos-offline-nav-guard-stay");
    const leaveBtn = document.getElementById("pos-offline-nav-guard-leave");
    if (!modal || !titleEl || !bodyEl || !stayBtn || !leaveBtn) {
        return;
    }

    titleEl.textContent = lbl(
        "navGuardTitle",
        "You are offline on POS"
    );
    bodyEl.textContent = lbl(
        "navGuardBody",
        "You cannot leave this page while offline. Going to another page or using the browser back button can discard unsaved POS data on this device."
    );
    stayBtn.textContent = lbl("navGuardStay", "Stay on POS");
    leaveBtn.textContent = lbl("navGuardLeave", "Leave anyway");
    leaveBtn.dataset.leaveUrl = navGuardPendingLeaveUrl;

    modal.classList.remove("hidden");
}

function hideOfflineNavGuardModal() {
    navGuardModalOpen = false;
    navGuardPendingLeaveUrl = null;
    const modal = document.getElementById("pos-offline-nav-guard-modal");
    if (modal) {
        modal.classList.add("hidden");
    }
}

function showOfflineReloadConfirmModal() {
    ensureShell();
    hideOfflineNavGuardModal();
    reloadGuardModalOpen = true;
    const modal = document.getElementById("pos-offline-reload-modal");
    const titleEl = document.getElementById("pos-offline-reload-title");
    const bodyEl = document.getElementById("pos-offline-reload-body");
    const stayBtn = document.getElementById("pos-offline-reload-stay");
    const reloadBtn = document.getElementById("pos-offline-reload-proceed");
    if (!modal || !titleEl || !bodyEl || !stayBtn || !reloadBtn) {
        return;
    }
    titleEl.textContent = lbl(
        "navGuardTitle",
        "You are offline on POS"
    );
    bodyEl.textContent = lbl(
        "reloadBody",
        "Reloading now may discard POS data on this device that has not been sent to the server yet."
    );
    stayBtn.textContent = lbl("navGuardStay", "Stay on POS");
    reloadBtn.textContent = lbl("reloadProceed", "Reload anyway");
    modal.classList.remove("hidden");
}

function hideOfflineReloadConfirmModal() {
    reloadGuardModalOpen = false;
    const modal = document.getElementById("pos-offline-reload-modal");
    if (modal) {
        modal.classList.add("hidden");
    }
}

/** Same conditions as the former beforeunload guard (offline on Blade POS). */
function shouldInterceptPosReload() {
    return isBladePosPage() && !isPosEffectiveOnline();
}

function installOfflinePosHistoryTrap() {
    if (!shouldGuardOfflineNavigation() || navGuardHistoryInstalled) {
        return;
    }
    try {
        history.pushState({ __posOfflineNavGuard: 1 }, "", location.href);
        navGuardHistoryInstalled = true;
    } catch (e) {
        console.warn("POS offline: history trap failed", e);
    }
}

function uninstallOfflinePosHistoryTrap() {
    navGuardHistoryInstalled = false;
}

/**
 * One delegated click handler on #pos-offline-modal-body (survives innerHTML refreshes).
 * Also avoids relying on document capture ordering vs other scripts.
 */
function installOfflineModalPrintDelegation() {
    if (typeof window !== "undefined" && window.__posOfflineModalPrintDelegationInstalled) {
        return;
    }
    const modalBody = document.getElementById("pos-offline-modal-body");
    if (!modalBody) {
        return;
    }
    if (typeof window !== "undefined") {
        window.__posOfflineModalPrintDelegationInstalled = true;
    }
    modalBody.addEventListener("click", function (e) {
        const payQueueBtn = e.target.closest(".pos-offline-queue-pay-btn");
        if (payQueueBtn) {
            const modal = document.getElementById("pos-offline-modal");
            if (!modal || modal.classList.contains("hidden")) {
                return;
            }
            if (!modalBody.contains(payQueueBtn)) {
                return;
            }
            e.preventDefault();
            e.stopPropagation();
            const sk =
                payQueueBtn.dataset && payQueueBtn.dataset.offlineSessionKey
                    ? String(payQueueBtn.dataset.offlineSessionKey).trim()
                    : "";
            const dueRaw = payQueueBtn.dataset && payQueueBtn.dataset.due;
            const due = parseFloat(dueRaw);
            const label =
                payQueueBtn.dataset && payQueueBtn.dataset.orderLabel
                    ? String(payQueueBtn.dataset.orderLabel)
                    : "";
            const oidRaw =
                payQueueBtn.dataset && payQueueBtn.dataset.orderId
                    ? String(payQueueBtn.dataset.orderId).trim()
                    : "";
            const oid = oidRaw ? parseInt(oidRaw, 10) : NaN;
            if (
                typeof window !== "undefined" &&
                typeof window.openPosOfflinePaymentModal === "function"
            ) {
                window.openPosOfflinePaymentModal({
                    order_id: Number.isFinite(oid) && oid > 0 ? oid : null,
                    offline_queue_group_key: sk || null,
                    due_amount: Number.isFinite(due) ? due : 0,
                    formatted_order_number: label,
                });
            }
            return;
        }

        const btn = e.target.closest(
            ".pos-offline-print-kot-btn, .pos-offline-print-bill-btn, .pos-offline-new-kot-btn"
        );
        if (!btn) {
            return;
        }
        const modal = document.getElementById("pos-offline-modal");
        if (!modal || modal.classList.contains("hidden")) {
            return;
        }
        if (!modalBody.contains(btn)) {
            return;
        }
        e.preventDefault();
        e.stopPropagation();
        const opId =
            btn.dataset && btn.dataset.opId ? String(btn.dataset.opId) : "";
        if (!opId) {
            return;
        }
        const op = loadQueue().find((o) => o.id === opId);
        if (!op || !op.summary) {
            return;
        }
        const isKot = btn.classList.contains("pos-offline-print-kot-btn");
        const isBill = btn.classList.contains("pos-offline-print-bill-btn");
        const isNewKot = btn.classList.contains("pos-offline-new-kot-btn");
        if (isNewKot) {
            const groupKeyRaw =
                btn.dataset && btn.dataset.groupKey
                    ? String(btn.dataset.groupKey)
                    : "";
            let orderIdRaw =
                (btn.dataset && btn.dataset.orderId) ||
                (op.payload && op.payload.order_id) ||
                null;
            // For grouped offline cards, do NOT fallback to currently open POS order.
            // That can incorrectly attach "New KOT" to the latest on-screen order.
            if (!orderIdRaw && !groupKeyRaw) {
                orderIdRaw = resolveCurrentPosOrderIdFallback();
            }
            const orderId = Number(orderIdRaw);

            // Always close modal first so user sees immediate response.
            pendingModalOpen = false;
            render();

            let relatedOps;
            if (groupKeyRaw) {
                relatedOps = loadQueue().filter((entry) => {
                    if (!entry || entry.type !== "save_order") {
                        return false;
                    }
                    return offlineOrderGroupKey(entry) === groupKeyRaw;
                });
                relatedOps.sort((a, b) => {
                    const ta = new Date(a && a.createdAt ? a.createdAt : 0).getTime();
                    const tb = new Date(b && b.createdAt ? b.createdAt : 0).getTime();
                    return (Number.isFinite(ta) ? ta : 0) - (Number.isFinite(tb) ? tb : 0);
                });
            } else {
                const hasValidOrderId = Number.isFinite(orderId) && orderId > 0;
                relatedOps = hasValidOrderId
                    ? loadQueue().filter((entry) => {
                          if (!entry || entry.type !== "save_order") {
                              return false;
                          }
                          const oid = Number(entry.payload && entry.payload.order_id);
                          return Number.isFinite(oid) && oid === orderId;
                      })
                    : [op];
            }

            // Bind append-session to the selected offline card before hydration, so
            // next KOT save stays on this exact queued order (not previously selected one).
            if (
                groupKeyRaw &&
                typeof window !== "undefined" &&
                typeof window.__posSetOfflineQueueSessionForAppend === "function"
            ) {
                const lastRelated =
                    relatedOps && relatedOps.length
                        ? relatedOps[relatedOps.length - 1]
                        : op;
                const lastPayload =
                    lastRelated && lastRelated.payload ? lastRelated.payload : {};
                const lastSummary =
                    lastRelated && lastRelated.summary ? lastRelated.summary : {};
                const orderLabel =
                    lastSummary.order_number_label ||
                    lastPayload.order_number_label ||
                    lastPayload.formatted_order_number ||
                    lastPayload.order_number ||
                    "";
                const sessionGroupKey = groupKeyRaw.startsWith("ogk:")
                    ? groupKeyRaw.slice(4)
                    : groupKeyRaw;
                window.__posSetOfflineQueueSessionForAppend(
                    sessionGroupKey,
                    orderLabel
                );
            }

            const hasValidOrderId = Number.isFinite(orderId) && orderId > 0;
            if (
                hydrateOfflineOrderIntoPos(
                    relatedOps.length ? relatedOps : [op],
                    hasValidOrderId ? orderId : null,
                    { newKotEmptyCart: true }
                )
            ) {
                return;
            }

            if (hasValidOrderId) {
                window.location.href = `/pos/kot/${orderId}`;
                return;
            }
            if (typeof window.showToast === "function") {
                window.showToast(
                    "error",
                    lbl(
                        "newKotUnavailable",
                        "Unable to load this cached order for New KOT."
                    )
                );
            }
            return;
        }
        if (
            isKot &&
            op.summary.kot_print_context &&
            Array.isArray(op.summary.kot_print_context.items) &&
            op.summary.kot_print_context.items.length &&
            typeof window.openPosOfflineKotPrintTab === "function"
        ) {
            window.openPosOfflineKotPrintTab(op.summary.kot_print_context);
        }
        if (
            isBill &&
            op.summary.bill_print_context &&
            Array.isArray(op.summary.bill_print_context.items) &&
            op.summary.bill_print_context.items.length &&
            typeof window.openPosOfflineBillPrintTab === "function"
        ) {
            window.openPosOfflineBillPrintTab(op.summary.bill_print_context);
        }
    });
}

function ensureShell() {
    if (document.getElementById("pos-offline-root")) {
        return;
    }

    const root = document.createElement("div");
    root.id = "pos-offline-root";
    root.className =
        "fixed top-0 left-0 right-0 z-[9999] pointer-events-none print:hidden";
    root.innerHTML = `
<div id="pos-offline-chrome" class="transition-all duration-300 ease-in-out overflow-hidden max-h-0 opacity-0 pointer-events-none">
  <div id="pos-offline-topbar" class="w-full h-0.5 transition-colors duration-500 ease-in-out bg-red-500"></div>
  <div class="flex justify-center items-center gap-2 -mt-2 mb-2 pointer-events-none">
    <div class="pointer-events-auto flex items-center gap-2">
      <button type="button" id="pos-offline-badge" class="flex items-center gap-1.5 px-4 py-1.5 rounded-full shadow-lg text-xs font-semibold uppercase tracking-wide border-2 transition-colors duration-500 ease-in-out"></button>
    </div>
  </div>
</div>
<div id="pos-offline-modal" class="hidden fixed inset-0 z-[10000] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 pointer-events-auto">
  <div id="pos-offline-modal-panel" class="pointer-events-auto bg-white dark:bg-gray-800 rounded-lg shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden border border-gray-200 dark:border-gray-700">
    <div id="pos-offline-modal-header" class="px-6 py-4 border-b border-gray-200 dark:border-gray-700"></div>
    <div id="pos-offline-modal-body" class="flex-1 overflow-y-auto p-6"></div>
    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex items-center justify-between gap-3">
      <p id="pos-offline-modal-footer-text" class="text-xs text-gray-500 dark:text-gray-400 flex-1"></p>
      <button type="button" id="pos-offline-modal-close" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-900 dark:text-white rounded-lg text-sm font-medium shrink-0"></button>
    </div>
  </div>
</div>
<div id="pos-offline-nav-guard-modal" class="hidden fixed inset-0 z-[10001] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 pointer-events-auto">
  <div class="bg-white dark:bg-gray-800 rounded-lg shadow-2xl w-full max-w-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-red-50 to-white dark:from-red-900/20 dark:to-gray-900">
      <h2 id="pos-offline-nav-guard-title" class="text-lg font-bold text-gray-900 dark:text-white"></h2>
    </div>
    <div class="px-6 py-4">
      <p id="pos-offline-nav-guard-body" class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed"></p>
    </div>
    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex flex-wrap justify-end gap-2">
      <button type="button" id="pos-offline-nav-guard-stay" class="px-4 py-2 rounded-lg text-sm font-medium bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-900 dark:text-white"></button>
      <button type="button" id="pos-offline-nav-guard-leave" class="px-4 py-2 rounded-lg text-sm font-medium bg-red-600 hover:bg-red-700 text-white"></button>
    </div>
  </div>
</div>
<div id="pos-offline-reload-modal" class="hidden fixed inset-0 z-[10002] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 pointer-events-auto">
  <div class="bg-white dark:bg-gray-800 rounded-lg shadow-2xl w-full max-w-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-red-50 to-white dark:from-red-900/20 dark:to-gray-900">
      <h2 id="pos-offline-reload-title" class="text-lg font-bold text-gray-900 dark:text-white"></h2>
    </div>
    <div class="px-6 py-4">
      <p id="pos-offline-reload-body" class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed"></p>
    </div>
    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex flex-wrap justify-end gap-2">
      <button type="button" id="pos-offline-reload-stay" class="px-4 py-2 rounded-lg text-sm font-medium bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-900 dark:text-white"></button>
      <button type="button" id="pos-offline-reload-proceed" class="px-4 py-2 rounded-lg text-sm font-medium bg-red-600 hover:bg-red-700 text-white"></button>
    </div>
  </div>
</div>`;
    document.body.appendChild(root);

    root.addEventListener("click", (e) => {
        if (e.target.closest("#pos-offline-reload-stay")) {
            hideOfflineReloadConfirmModal();
            return;
        }
        if (e.target.closest("#pos-offline-reload-proceed")) {
            navGuardHardLeaving = true;
            hideOfflineReloadConfirmModal();
            window.location.reload();
            return;
        }
        if (e.target.closest("#pos-offline-nav-guard-stay")) {
            hideOfflineNavGuardModal();
            return;
        }
        if (e.target.closest("#pos-offline-nav-guard-leave")) {
            const btn = e.target.closest("#pos-offline-nav-guard-leave");
            const url = (btn && btn.dataset && btn.dataset.leaveUrl) || "/";
            navGuardHardLeaving = true;
            hideOfflineNavGuardModal();
            window.location.assign(url);
            return;
        }
        const badge = e.target.closest("#pos-offline-badge");
        if (badge && !badge.classList.contains("cursor-not-allowed")) {
            const q = loadQueue().filter(
                (o) => o.type === "save_order" || o.type === "record_payment"
            );
            if (q.length > 0) {
                pendingModalOpen = true;
                render();
            }
            return;
        }
        if (e.target.closest("#pos-offline-modal-x")) {
            pendingModalOpen = false;
            render();
            return;
        }
        if (e.target.closest("#pos-offline-modal-close")) {
            pendingModalOpen = false;
            render();
        }
    });

    // Backdrop: click the dimmed area (modal root), not the white panel — do not use
    // stopPropagation on the panel or Close / X never reach the handler above.
    const modalBackdrop = document.getElementById("pos-offline-modal");
    modalBackdrop?.addEventListener("click", (e) => {
        if (e.target === modalBackdrop) {
            pendingModalOpen = false;
            render();
        }
    });

    const navGuardBackdrop = document.getElementById(
        "pos-offline-nav-guard-modal"
    );
    navGuardBackdrop?.addEventListener("click", (e) => {
        if (e.target === navGuardBackdrop) {
            hideOfflineNavGuardModal();
        }
    });

    const reloadGuardBackdrop = document.getElementById(
        "pos-offline-reload-modal"
    );
    reloadGuardBackdrop?.addEventListener("click", (e) => {
        if (e.target === reloadGuardBackdrop) {
            hideOfflineReloadConfirmModal();
        }
    });

    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape" && reloadGuardModalOpen) {
            hideOfflineReloadConfirmModal();
            return;
        }
        if (e.key === "Escape" && navGuardModalOpen) {
            hideOfflineNavGuardModal();
            return;
        }
        if (e.key === "Escape" && pendingModalOpen) {
            pendingModalOpen = false;
            render();
        }
    });

    installOfflineModalPrintDelegation();
}

function renderModalList(pending, online) {
    const body = document.getElementById("pos-offline-modal-body");
    const header = document.getElementById("pos-offline-modal-header");
    if (!body || !header) {
        return;
    }

    const savePending = pending.filter((o) => o.type === "save_order");
    const payPending = pending.filter((o) => o.type === "record_payment");
    const saveGroups = savePending.length ? groupSaveOrderOps(savePending) : [];
    const saveGroupKeys = new Set(saveGroups.map((g) => g.key));
    const paymentsByGroup = indexRecordPaymentsByGroup(pending);
    const orphanPayOps = payPending.filter(
        (op) => !saveGroupKeys.has(offlineOrderGroupKey(op))
    );
    const groupCount = saveGroups.length + orphanPayOps.length;
    const count = pending.length;
    header.className =
        "px-6 py-4 border-b border-gray-200 dark:border-gray-700 " +
        (online
            ? "bg-gradient-to-r from-yellow-50 to-white dark:from-yellow-900/20 dark:to-gray-900"
            : "bg-gradient-to-r from-red-50 to-white dark:from-red-900/20 dark:to-gray-900");

    const title = online
        ? esc(lbl("modalTitleOnline", "Syncing orders"))
        : esc(lbl("modalTitleOffline", "Pending orders (offline)"));
    const sub = esc(
        tpl(
            online ? "modalSubtitleOnlineTpl" : "modalSubtitleOfflineTpl",
            online
                ? ":count order(s) in the sync queue"
                : ":count order(s) waiting to sync when online",
            { count: groupCount }
        )
    );

    header.innerHTML = `
<div class="flex items-center justify-between gap-3">
  <div class="min-w-0">
    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white truncate">${title}</h2>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">${sub}</p>
  </div>
  <button type="button" id="pos-offline-modal-x" class="shrink-0 p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg text-gray-500 dark:text-gray-400" aria-label="${esc(lbl("modalClose", "Close"))}">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
  </button>
</div>`;

    if (count === 0) {
        body.innerHTML = `<div class="text-center py-12 text-gray-500 dark:text-gray-400">${esc(
            lbl("noPending", "No pending orders")
        )}</div>`;
        return;
    }

    const groups = saveGroups;

    const cards = groups
        .map((group) => {
            const g = aggregateGroupDisplay(group.ops);
            const paymentOpsForGroup =
                paymentsByGroup.get(group.key) || [];
            const hasQueuedPayment = paymentOpsForGroup.length > 0;
            const sessionKeyForPay = resolveOfflineSessionKeyForPay(group);
            const resolvedOrderIdForNewKot = group.ops
                .map((entry) =>
                    Number(entry && entry.payload ? entry.payload.order_id : 0)
                )
                .find((id) => Number.isFinite(id) && id > 0);
            const orderIdStr =
                Number.isFinite(resolvedOrderIdForNewKot) &&
                resolvedOrderIdForNewKot > 0
                    ? String(resolvedOrderIdForNewKot)
                    : "";
            const showPayNow =
                !hasQueuedPayment && Number(g.total) > 0;
            const paymentAttachedSection = hasQueuedPayment
                ? `<div class="mt-3 pt-3 border-t border-amber-200 dark:border-amber-800 rounded-md bg-amber-50/90 dark:bg-amber-900/25 -mx-3 px-3 pb-3 mb-0">
  <p class="text-[11px] font-semibold text-amber-900 dark:text-amber-100 mb-2">${esc(
      lbl("offlinePaymentAttachedSection", "Payment (will sync when online)")
  )}</p>
  ${paymentQueuedRowsHtml(paymentOpsForGroup)}
</div>`
                : showPayNow
                  ? `<div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600 flex flex-wrap items-center gap-2">
  <button type="button" class="pos-offline-queue-pay-btn px-4 py-2 rounded-lg text-sm font-semibold bg-skin-base text-white hover:opacity-90 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-skin-base"
    data-offline-session-key="${esc(sessionKeyForPay)}"
    data-due="${Number(g.total)}"
    data-order-label="${esc(String(g.badgeLabel || ""))}"
    data-order-id="${esc(orderIdStr)}">${esc(lbl("payNowQueue", "Pay now"))}</button>
  <span class="text-[11px] text-gray-500 dark:text-gray-400">${esc(
      lbl("offlinePayHint", "Record payment on this device; it syncs after the order.")
  )}</span>
</div>`
                  : "";
            const cust = g.customer;
            let custBlock = "";
            if (cust && (cust.name || cust.phone || cust.email)) {
                custBlock = `
<div class="mb-2 pb-2 border-b border-gray-200 dark:border-gray-700">
  <p class="text-[11px] font-medium text-gray-700 dark:text-gray-300 mb-1">${esc(lbl("customerLabel", "Customer"))}</p>
  <p class="text-[13px] text-gray-900 dark:text-white leading-snug">${esc(cust.name || "—")}</p>
  <div class="flex flex-wrap gap-3 mt-0.5">
    ${cust.phone ? `<span class="text-[11px] text-gray-600 dark:text-gray-400">${esc(cust.phone)}</span>` : ""}
    ${cust.email ? `<span class="text-[11px] text-gray-600 dark:text-gray-400">${esc(cust.email)}</span>` : ""}
  </div>
</div>`;
            }

            let tableBlock = "";
            if (g.tableNo) {
                tableBlock = `
<div class="mb-2 pb-2 border-b border-gray-200 dark:border-gray-700">
  <p class="text-[11px] font-medium text-gray-700 dark:text-gray-300 mb-1">${esc(lbl("tableLabel", "Table"))}</p>
  <p class="text-[13px] text-gray-900 dark:text-white">${esc(g.tableNo)}</p>
</div>`;
            } else if (g.tableId) {
                tableBlock = `
<div class="mb-2 pb-2 border-b border-gray-200 dark:border-gray-700">
  <p class="text-[11px] font-medium text-gray-700 dark:text-gray-300 mb-1">${esc(lbl("tableLabel", "Table"))}</p>
  <p class="text-[13px] text-gray-900 dark:text-white">${esc(
                    tpl("tableIdTpl", "Table ID: :id", { id: g.tableId })
                )}</p>
</div>`;
            }

            const kotSectionsHtml = group.ops
                .map((op, kotIndex) => {
                    const d = getDisplayForOp(op, kotIndex);
                    const itemRows = (d.items || [])
                        .map(
                            (it) => `
<div class="flex items-start justify-between text-[13px] gap-2">
  <span class="text-gray-700 dark:text-gray-300 leading-snug break-words min-w-0">${esc(it.quantity)}x ${esc(it.name)}</span>
  <span class="text-gray-900 dark:text-white font-medium shrink-0">${formatCurrency(
                                it.amount != null
                                    ? it.amount
                                    : Number(it.price || 0) *
                                          Number(it.quantity || 1)
                            )}</span>
</div>`
                        )
                        .join("");

                    const opActions =
                        Array.isArray(d.actions) && d.actions.length
                            ? `<div class="mb-1.5">
  <div class="flex flex-wrap gap-1.5">${d.actions
      .map(
          (a) =>
              `<span class="px-2 py-0.5 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 text-[10px] rounded font-medium">${esc(
                  String(a).toUpperCase()
              )}</span>`
      )
      .join("")}</div>
</div>`
                            : "";

                    const kotCtx =
                        op.summary &&
                        op.summary.kot_print_context &&
                        Array.isArray(op.summary.kot_print_context.items) &&
                        op.summary.kot_print_context.items.length
                            ? op.summary.kot_print_context
                            : null;

                    const printKotBtn = kotCtx
                        ? `<button type="button" class="pos-offline-print-kot-btn rounded-lg text-xs font-semibold shadow-sm transition hover:opacity-95 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-slate-500 px-2.5 py-1.5 bg-slate-800 text-white hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600" style="cursor:pointer;background:#1e293b;color:#fff;border:1px solid #0f172a;padding:0.3rem 0.65rem;border-radius:0.5rem" data-op-id="${esc(op.id)}">${esc(
                              lbl("printKot", "Print KOT")
                          )}</button>`
                        : "";

                    const kotTitle = esc(
                        tpl("offlineKotSectionTpl", "KOT :num", {
                            num: kotIndex + 1,
                        })
                    );

                    return `
<div class="rounded-md border border-gray-200 dark:border-gray-600 bg-white/70 dark:bg-gray-800/50 p-2.5 mb-2 last:mb-0" data-pos-offline-kot-op-id="${esc(op.id)}">
  <div class="flex flex-wrap items-start justify-between gap-2 mb-1.5">
    <span class="text-xs font-semibold text-gray-800 dark:text-gray-200">${kotTitle}</span>
    ${printKotBtn ? `<div class="shrink-0 flex flex-wrap gap-2">${printKotBtn}</div>` : ""}
  </div>
  ${opActions}
  <div class="space-y-1.5">${itemRows}</div>
</div>`;
                })
                .join("");

            const billButtonsRow =
                group.ops
                    .map((op) => {
                        const billCtx =
                            op.summary &&
                            op.summary.bill_print_context &&
                            Array.isArray(op.summary.bill_print_context.items) &&
                            op.summary.bill_print_context.items.length
                                ? op.summary.bill_print_context
                                : null;
                        return billCtx
                            ? `<button type="button" class="pos-offline-print-bill-btn rounded-lg text-xs font-semibold shadow-sm transition hover:opacity-95 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-emerald-600 px-2.5 py-1.5 bg-emerald-700 text-white hover:bg-emerald-800 dark:bg-emerald-600 dark:hover:bg-emerald-500" style="cursor:pointer;background:#166534;color:#fff;border:1px solid #14532d;padding:0.3rem 0.65rem;border-radius:0.5rem" data-op-id="${esc(op.id)}">${esc(
                                  lbl("printBill", "Print bill")
                              )}</button>`
                            : "";
                    })
                    .filter(Boolean)
                    .join("") || "";

            const billRow = billButtonsRow
                ? `<div class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-600 flex flex-wrap items-center gap-2">
  <span class="text-[11px] font-semibold text-gray-700 dark:text-gray-200 w-full sm:w-auto">${esc(lbl("printReceiptsLabel", "Print receipts"))}</span>
  <div class="flex flex-wrap gap-2">${billButtonsRow}</div>
</div>`
                : "";

            const kotCountBadge =
                group.ops.length > 1
                    ? `<span class="px-2 py-0.5 text-[10px] font-medium rounded bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200">${esc(
                          tpl("offlineKotCountTpl", ":count KOTs", {
                              count: group.ops.length,
                          })
                      )}</span>`
                    : "";

            const newKotActionRow = `<div class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-600 flex flex-wrap items-center gap-2">
  <button type="button" class="pos-offline-new-kot-btn rounded-lg text-xs font-semibold shadow-sm transition hover:opacity-95 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-indigo-500 px-2.5 py-1.5 bg-indigo-600 text-white hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-400" data-group-key="${esc(group.key)}" data-op-id="${esc(group.ops[group.ops.length - 1].id)}" data-order-id="${esc(orderIdStr)}">${esc(lbl("addNewKot", "New KOT"))}</button>
</div>`;

            const discRow =
                g.discount > 0
                    ? `<div class="flex justify-between text-[13px] mb-1">
  <span class="text-gray-600 dark:text-gray-400">${esc(lbl("discountLabel", "Discount"))}</span>
  <span class="text-red-600 dark:text-red-400">-${formatCurrency(g.discount)}</span>
</div>`
                    : "";
            const taxRow =
                g.totalTaxAmount > 0
                    ? `<div class="flex justify-between text-[13px] mb-1">
  <span class="text-gray-600 dark:text-gray-400">${esc(lbl("taxLabel", "Tax"))}</span>
  <span class="text-gray-900 dark:text-white">${formatCurrency(g.totalTaxAmount)}</span>
</div>`
                    : "";
            const extraChargesRow =
                g.extraChargeAmount > 0
                    ? `<div class="flex justify-between text-[13px] mb-1">
  <span class="text-gray-600 dark:text-gray-400">${esc(lbl("extraChargesLabel", "Extra charges"))}</span>
  <span class="text-gray-900 dark:text-white">${formatCurrency(g.extraChargeAmount)}</span>
</div>`
                    : "";
            const deliveryFeeRow =
                g.deliveryFee > 0
                    ? `<div class="flex justify-between text-[13px] mb-1">
  <span class="text-gray-600 dark:text-gray-400">${esc(lbl("deliveryFeeLabel", "Delivery fee"))}</span>
  <span class="text-gray-900 dark:text-white">${formatCurrency(g.deliveryFee)}</span>
</div>`
                    : "";
            const tipRow =
                g.tipAmount > 0
                    ? `<div class="flex justify-between text-[13px] mb-1">
  <span class="text-gray-600 dark:text-gray-400">${esc(lbl("tipLabel", "Tip"))}</span>
  <span class="text-gray-900 dark:text-white">${formatCurrency(g.tipAmount)}</span>
</div>`
                    : "";

            return `
<div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3 bg-gray-50 dark:bg-gray-900/50 mb-3 last:mb-0" data-pos-offline-group-card="1" data-pos-offline-group-key="${esc(group.key)}">
  <div class="flex items-start justify-between mb-2 gap-2">
    <div class="min-w-0">
      <div class="flex items-center gap-1.5 mb-1 flex-wrap">
        <span class="px-2 py-0.5 text-[11px] font-semibold rounded ${
            online
                ? "bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300"
                : "bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300"
        }">${esc(g.badgeLabel)}</span>
        ${kotCountBadge}
        <span class="text-[11px] text-gray-500 dark:text-gray-400">${esc(formatDate(g.createdAt))}</span>
      </div>
      <p class="text-[13px] font-medium text-gray-900 dark:text-white break-words leading-snug">${esc(g.orderType)}</p>
    </div>
    <div class="text-right shrink-0">
      <p class="text-xl font-bold text-gray-900 dark:text-white leading-none">${formatCurrency(g.total)}</p>
      <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">${esc(
                tpl("itemCountTpl", ":count item(s)", {
                    count: g.itemCount,
                })
            )}</p>
    </div>
  </div>
  ${custBlock}
  ${tableBlock}
  <div class="grid grid-cols-1 md:grid-cols-2 gap-2 md:gap-3">
    <div class="min-w-0">
      <p class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">${esc(lbl("itemsLabel", "Items"))}</p>
      <div class="min-w-0">${kotSectionsHtml}</div>
      ${newKotActionRow}
      ${billRow}
    </div>
    <div class="pt-2 md:pt-0 border-t md:border-t-0 border-gray-200 dark:border-gray-700">
      <p class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">${esc(lbl("totalLabel", "Total"))}</p>
    <div class="flex justify-between text-[13px] mb-1">
      <span class="text-gray-600 dark:text-gray-400">${esc(lbl("subtotalLabel", "Subtotal"))}</span>
      <span class="text-gray-900 dark:text-white">${formatCurrency(g.subtotal)}</span>
    </div>
    ${discRow}
    ${taxRow}
    ${extraChargesRow}
    ${deliveryFeeRow}
    ${tipRow}
    <div class="flex justify-between text-sm font-bold mt-2 pt-2 border-t border-gray-200 dark:border-gray-700">
      <span class="text-gray-900 dark:text-white">${esc(lbl("totalLabel", "Total"))}</span>
      <span class="text-gray-900 dark:text-white">${formatCurrency(g.total)}</span>
    </div>
  </div>
  </div>
  ${paymentAttachedSection}
</div>`;
        })
        .join("");

    const orphanPayCards =
        orphanPayOps.length > 0
            ? orphanPayOps
                  .map((op) => {
                      const sum = op.summary || {};
                      const p = op.payload || {};
                      const method = esc(
                          String(sum.payment_method || p.payment_method || "—")
                      );
                      const tendered = formatCurrency(
                          sum.tendered != null ? sum.tendered : p.payment_amount
                      );
                      const change = formatCurrency(
                          sum.change != null ? sum.change : p.return_amount
                      );
                      const due = formatCurrency(
                          sum.due_amount != null ? sum.due_amount : p.due_amount
                      );
                      const label = esc(
                          sum.order_number_label ||
                              p.formatted_order_number ||
                              lbl("offlinePaymentPending", "Pending payment")
                      );
                      return `
<div class="border border-amber-200 dark:border-amber-800 rounded-lg p-3 bg-amber-50/80 dark:bg-amber-900/20 mb-3 last:mb-0" data-pos-offline-op-id="${esc(op.id)}">
  <p class="text-[11px] font-medium text-amber-900 dark:text-amber-200 mb-2">${esc(
      lbl("offlineOrphanPaymentNote", "Payment waiting for order sync")
  )}</p>
  <div class="flex items-start justify-between gap-2 mb-1">
    <span class="px-2 py-0.5 text-[11px] font-semibold rounded bg-amber-100 dark:bg-amber-900/40 text-amber-900 dark:text-amber-200">${label}</span>
    <span class="text-[11px] text-gray-500 dark:text-gray-400">${esc(formatDate(op.createdAt))}</span>
  </div>
  <p class="text-sm text-gray-800 dark:text-gray-200">${esc(
      lbl("offlinePaymentMethodLabel", "Method")
  )}: <span class="font-medium uppercase">${method}</span></p>
  <div class="mt-1 grid grid-cols-1 sm:grid-cols-3 gap-1 text-[13px] text-gray-700 dark:text-gray-300">
    <div><span class="text-gray-500 dark:text-gray-400">${esc(lbl("offlinePaymentDueLabel", "Due"))}</span> ${due}</div>
    <div><span class="text-gray-500 dark:text-gray-400">${esc(lbl("offlinePaymentTenderedLabel", "Tendered"))}</span> ${tendered}</div>
    <div><span class="text-gray-500 dark:text-gray-400">${esc(lbl("offlinePaymentChangeLabel", "Change"))}</span> ${change}</div>
  </div>
</div>`;
                  })
                  .join("")
            : "";

    body.innerHTML = cards + orphanPayCards;
}

function animateSyncedOrderCard(opId) {
    return new Promise((resolve) => {
        if (!pendingModalOpen || !opId) {
            resolve();
            return;
        }
        const body = document.getElementById("pos-offline-modal-body");
        if (!body) {
            resolve();
            return;
        }
        const idStr = String(opId);
        const kotBlock = Array.from(
            body.querySelectorAll("[data-pos-offline-kot-op-id]")
        ).find(
            (el) =>
                String(el.getAttribute("data-pos-offline-kot-op-id")) === idStr
        );
        const targetEl =
            kotBlock ||
            Array.from(body.querySelectorAll("[data-pos-offline-op-id]")).find(
                (el) =>
                    String(el.getAttribute("data-pos-offline-op-id")) === idStr
            );
        if (!targetEl) {
            resolve();
            return;
        }

        const rowHeight = Math.max(targetEl.offsetHeight, 1);
        targetEl.style.willChange = "opacity, transform, max-height";
        targetEl.style.overflow = "hidden";
        targetEl.style.maxHeight = `${rowHeight}px`;
        targetEl.style.transition =
            "opacity 260ms ease, transform 260ms ease, max-height 300ms ease, margin 300ms ease, padding 300ms ease, border-width 300ms ease, background-color 200ms ease";
        targetEl.style.backgroundColor = "#dcfce7";

        window.setTimeout(() => {
            targetEl.style.backgroundColor = "";
            targetEl.style.opacity = "0";
            targetEl.style.transform = "translateY(-4px) scale(0.985)";
            targetEl.style.maxHeight = "0px";
            targetEl.style.marginBottom = "0";
            targetEl.style.paddingTop = "0";
            targetEl.style.paddingBottom = "0";
            targetEl.style.borderWidth = "0";
        }, 140);

        window.setTimeout(() => {
            try {
                const groupCard = targetEl.closest(
                    "[data-pos-offline-group-card]"
                );
                targetEl.remove();
                if (
                    groupCard &&
                    !groupCard.querySelector("[data-pos-offline-kot-op-id]")
                ) {
                    groupCard.remove();
                }
            } catch (e) {
                // ignore
            }
            resolve();
        }, 470);
    });
}

function render() {
    ensureShell();

    const online = isPosEffectiveOnline();
    const queue = loadQueue();
    const savePending = queue
        .filter((o) => o.type === "save_order")
        .sort((a, b) => {
            const ta = new Date(a && a.createdAt ? a.createdAt : 0).getTime();
            const tb = new Date(b && b.createdAt ? b.createdAt : 0).getTime();
            return (Number.isFinite(tb) ? tb : 0) - (Number.isFinite(ta) ? ta : 0);
        });
    const payPending = queue
        .filter((o) => o.type === "record_payment")
        .sort((a, b) => {
            const ta = new Date(a && a.createdAt ? a.createdAt : 0).getTime();
            const tb = new Date(b && b.createdAt ? b.createdAt : 0).getTime();
            return (Number.isFinite(tb) ? tb : 0) - (Number.isFinite(ta) ? ta : 0);
        });
    const pending = [...savePending, ...payPending].sort((a, b) => {
        const ta = new Date(a && a.createdAt ? a.createdAt : 0).getTime();
        const tb = new Date(b && b.createdAt ? b.createdAt : 0).getTime();
        return (Number.isFinite(tb) ? tb : 0) - (Number.isFinite(ta) ? ta : 0);
    });
    const count = pending.length;
    /** Pending order cards only (save_order groups). Never includes record_payment. */
    const saveOrderGroupCount = savePending.length
        ? groupSaveOrderOps(savePending).length
        : 0;

    const chrome = document.getElementById("pos-offline-chrome");
    const top = document.getElementById("pos-offline-topbar");
    const badge = document.getElementById("pos-offline-badge");
    const modal = document.getElementById("pos-offline-modal");
    const footerText = document.getElementById("pos-offline-modal-footer-text");
    const closeBtn = document.getElementById("pos-offline-modal-close");

    if (!chrome || !top || !badge || !modal || !footerText || !closeBtn) {
        return;
    }

    if (count === 0) {
        pendingModalOpen = false;
    }

    const showChrome = !online || count > 0;
    // Offline with no queued orders: slim chrome only (no tall empty strip).
    const compactOfflineChrome = !online && count === 0;
    const pc = document.getElementById("pos-container");

    // Inline max-height: Tailwind content paths may not scan this file, so utility classes can be purged.
    if (showChrome) {
        chrome.className =
            "transition-all duration-300 ease-in-out overflow-hidden opacity-100 pointer-events-auto";
        chrome.style.maxHeight = compactOfflineChrome ? "3.25rem" : "120px";
    } else {
        chrome.className =
            "transition-all duration-300 ease-in-out overflow-hidden max-h-0 opacity-0 pointer-events-none";
        chrome.style.maxHeight = "";
    }

    top.className =
        "w-full h-0.5 transition-colors duration-500 ease-in-out " +
        (!online ? "bg-red-500" : "bg-yellow-500");

    badge.className =
        "flex items-center gap-1.5 px-4 py-1.5 rounded-full shadow-lg text-xs font-semibold uppercase tracking-wide border-2 transition-colors duration-500 ease-in-out " +
        (!online
            ? "bg-red-500 text-white border-red-600"
            : syncInProgress
              ? "bg-yellow-500 text-white border-yellow-600 cursor-pointer animate-pulse"
              : "bg-yellow-500 text-white border-yellow-600 cursor-pointer hover:scale-105");

    let iconSvg = "";
    if (!online) {
        iconSvg = `<svg class="w-3.5 h-3.5 shrink-0 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728M15.536 8.464a5 5 0 010 7.072M3 3l18 18"/></svg>`;
    } else {
        iconSvg = `<svg class="w-3.5 h-3.5 shrink-0 ${syncInProgress ? "animate-spin" : ""}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>`;
    }

    let text = "";
    if (!online) {
        text = esc(lbl("statusOffline", "Offline"));
    } else {
        text = esc(lbl("statusSyncing", "Syncing"));
    }

    const countBadge =
        saveOrderGroupCount > 0
            ? `<span class="px-1.5 py-0.5 text-[10px] font-bold bg-white/20 rounded-full">${saveOrderGroupCount}</span>`
            : "";

    badge.innerHTML = `${iconSvg}<span>${text}</span>${countBadge}`;
    badge.title =
        count > 0
            ? online
                ? lbl("badgeTitleOnline", "Click to view orders being synced")
                : lbl("badgeTitleOffline", "Click to view pending orders")
            : !online
              ? lbl("statusOffline", "Offline")
              : "";

    if (count === 0) {
        badge.classList.add("cursor-default");
        badge.classList.remove("cursor-pointer", "hover:scale-105");
    } else {
        badge.classList.remove("cursor-default");
        badge.classList.add("cursor-pointer");
    }

    if (pendingModalOpen && count > 0) {
        modal.classList.remove("hidden");
        footerText.textContent = online
            ? lbl(
                  "footerOnline",
                  "These orders are being synced automatically when possible."
              )
            : lbl(
                  "footerOffline",
                  "These orders will be sent automatically when you are back online."
              );
        closeBtn.textContent = lbl("modalClose", "Close");
        renderModalList(pending, online);
    } else {
        modal.classList.add("hidden");
    }

    /**
     * The offline chrome is fixed to the viewport top and often sits over the global navbar only.
     * A fixed paddingTop on #pos-container used to reserve space even when nothing overlapped the
     * POS body — that produced a large empty gap below the app header. Only add inset when the
     * chrome actually extends into the POS content area.
     */
    function applyPosContainerChromeInset() {
        if (!pc) {
            return;
        }
        if (!showChrome) {
            pc.style.paddingTop = "";
            return;
        }
        requestAnimationFrame(function() {
            try {
                const cr = chrome.getBoundingClientRect();
                const pr = pc.getBoundingClientRect();
                const overlap = Math.max(0, cr.bottom - pr.top);
                if (overlap < 2) {
                    pc.style.paddingTop = "";
                    return;
                }
                pc.style.paddingTop = Math.ceil(overlap + 4) + "px";
            } catch (e) {
                pc.style.paddingTop = "";
            }
        });
    }
    applyPosContainerChromeInset();

    try {
        if (typeof window !== "undefined") {
            window.dispatchEvent(new CustomEvent("posOfflineRender"));
        }
    } catch (e) {
        // ignore
    }
}

async function syncQueue() {
    if (!isPosEffectiveOnline()) {
        render();
        return;
    }

    let queue = loadQueue();
    if (!queue.length) {
        render();
        return;
    }

    const needsSave = queue.some((o) => o.type === "save_order");
    const needsPay = queue.some((o) => o.type === "record_payment");
    if ((needsSave && !saveOrderUrl) || (needsPay && !syncPaymentUrl)) {
        render();
        return;
    }

    syncInProgress = true;
    render();

    let synced = 0;
    // During one sync run, remember which server order id belongs to each
    // offline queue group so follow-up KOT ops attach to the same order.
    const groupOrderIdMap = new Map();
    try {
        while (queue.length) {
            const op = queue[0];
            try {
                if (op.type === "record_payment") {
                    const payload =
                        op && op.payload && typeof op.payload === "object"
                            ? { ...op.payload }
                            : {};
                    const payGroupKey = offlineOrderGroupKey(op);
                    let resolvedId = Number(payload.order_id);
                    if (
                        !Number.isFinite(resolvedId) ||
                        resolvedId <= 0
                    ) {
                        const mapped = groupOrderIdMap.get(payGroupKey);
                        if (
                            Number.isFinite(Number(mapped)) &&
                            Number(mapped) > 0
                        ) {
                            resolvedId = Number(mapped);
                        }
                    }
                    if (!Number.isFinite(resolvedId) || resolvedId <= 0) {
                        break;
                    }
                    payload.order_id = resolvedId;
                    await postSyncPayment(payload);
                    await animateSyncedOrderCard(op.id);
                    queue.shift();
                    saveQueue(queue);
                    synced++;
                    continue;
                }

                if (op.type !== "save_order") {
                    queue.shift();
                    saveQueue(queue);
                    continue;
                }

                const payload =
                    op && op.payload && typeof op.payload === "object"
                        ? { ...op.payload }
                        : {};
                const groupKey = offlineOrderGroupKey(op);
                const knownOrderId = groupOrderIdMap.get(groupKey);

                if (
                    Number.isFinite(Number(knownOrderId)) &&
                    Number(knownOrderId) > 0 &&
                    (!payload.order_id || Number(payload.order_id) <= 0)
                ) {
                    payload.order_id = Number(knownOrderId);
                }

                const response = await postSaveOrder(payload);
                const responseOrderId = Number(
                    (response &&
                        ((response.order && response.order.id) ||
                            response.order_id)) ||
                        0
                );
                if (Number.isFinite(responseOrderId) && responseOrderId > 0) {
                    groupOrderIdMap.set(groupKey, responseOrderId);
                    // Persist the resolved server order id onto remaining queued ops
                    // in this group so retries/reloads still append to the same order.
                    queue.forEach((pendingOp) => {
                        if (
                            pendingOp &&
                            (pendingOp.type === "save_order" ||
                                pendingOp.type === "record_payment") &&
                            offlineOrderGroupKey(pendingOp) === groupKey
                        ) {
                            if (
                                !pendingOp.payload ||
                                typeof pendingOp.payload !== "object"
                            ) {
                                pendingOp.payload = {};
                            }
                            if (
                                !pendingOp.payload.order_id ||
                                Number(pendingOp.payload.order_id) <= 0
                            ) {
                                pendingOp.payload.order_id = responseOrderId;
                            }
                        }
                    });
                    saveQueue(queue);
                }
                await animateSyncedOrderCard(op.id);
                queue.shift();
                saveQueue(queue);
                synced++;
            } catch (e) {
                console.error("POS offline sync error:", e);
                if (typeof window.showToast === "function" && e.message) {
                    window.showToast("error", e.message);
                }
                break;
            }
        }
    } finally {
        syncInProgress = false;
    }

    queue = loadQueue();
    render();

    if (
        synced > 0 &&
        !queue.length &&
        typeof window.showToast === "function"
    ) {
        const msg =
            window.__posOfflineSyncToast ||
            lbl("syncComplete", "Queued orders were sent successfully.");
        window.showToast("success", msg);
    }
}

function setupNavigationGuard() {
    if (navGuardClickBound) {
        return;
    }
    navGuardClickBound = true;

    document.addEventListener(
        "click",
        (e) => {
            if (!shouldGuardOfflineNavigation()) {
                return;
            }
            if (navGuardHardLeaving) {
                return;
            }
            const a = e.target.closest("a[href]");
            if (!a) {
                return;
            }
            if (a.closest("#pos-offline-nav-guard-modal")) {
                return;
            }
            if (a.closest("#pos-offline-modal")) {
                return;
            }
            if (a.closest("#pos-offline-payment-modal")) {
                return;
            }
            if (a.closest("#pos-offline-reload-modal")) {
                return;
            }
            const hrefAttr = a.getAttribute("href");
            if (
                !hrefAttr ||
                hrefAttr === "#" ||
                hrefAttr.toLowerCase().startsWith("javascript:")
            ) {
                return;
            }
            if (a.target === "_blank" || a.hasAttribute("download")) {
                return;
            }
            let url;
            try {
                url = new URL(a.href, location.href);
            } catch (err) {
                return;
            }
            if (url.protocol === "mailto:" || url.protocol === "tel:") {
                return;
            }
            const here =
                location.origin + location.pathname + location.search;
            const there = url.origin + url.pathname + url.search;
            if (there === here) {
                return;
            }

            e.preventDefault();
            e.stopPropagation();
            if (typeof e.stopImmediatePropagation === "function") {
                e.stopImmediatePropagation();
            }
            showOfflineNavGuardModal(url.href);
        },
        true
    );

    if (!navGuardPopstateBound) {
        navGuardPopstateBound = true;
        window.addEventListener("popstate", () => {
            if (navGuardHardLeaving) {
                return;
            }
            if (!shouldGuardOfflineNavigation()) {
                return;
            }
            try {
                history.pushState(
                    { __posOfflineNavGuard: Date.now() },
                    "",
                    location.href
                );
            } catch (err) {
                console.warn("POS offline: popstate guard push failed", err);
            }
            const fallback =
                optsRef.navGuardLeaveUrl ||
                (typeof location !== "undefined" ? location.origin : "/");
            showOfflineNavGuardModal(fallback);
        });
    }
}

function setupListeners() {
    if (setupListeners.done) {
        return;
    }
    setupListeners.done = true;

    window.addEventListener("online", () => {
        if (!isPosEffectiveOnline()) {
            render();
            return;
        }
        uninstallOfflinePosHistoryTrap();
        hideOfflineNavGuardModal();
        hideOfflineReloadConfirmModal();
        render();
        syncQueue();
    });

    window.addEventListener("offline", () => {
        installOfflinePosHistoryTrap();
        render();
    });

    /**
     * Replace the browser's generic beforeunload dialog with our in-app modal for
     * common reload shortcuts (F5 / Ctrl+R / Cmd+R). Chrome does not allow custom
     * beforeunload text; omitting beforeunload removes "Reload site?" entirely.
     */
    window.addEventListener(
        "keydown",
        (e) => {
            if (navGuardHardLeaving || !shouldInterceptPosReload()) {
                return;
            }
            if (reloadGuardModalOpen || navGuardModalOpen) {
                return;
            }
            const t = e.target;
            const tag =
                t && t.tagName ? String(t.tagName).toLowerCase() : "";
            if (
                tag === "input" ||
                tag === "textarea" ||
                tag === "select" ||
                (t && t.isContentEditable)
            ) {
                return;
            }
            const ctrlOrMeta = e.ctrlKey || e.metaKey;
            const k = e.key || "";
            const code = e.code || "";
            const isReload =
                k === "F5" ||
                code === "F5" ||
                (ctrlOrMeta &&
                    !e.altKey &&
                    (k === "r" || k === "R" || code === "KeyR"));
            if (!isReload) {
                return;
            }
            e.preventDefault();
            if (typeof e.stopImmediatePropagation === "function") {
                e.stopImmediatePropagation();
            }
            e.stopPropagation();
            showOfflineReloadConfirmModal();
        },
        true
    );

    setupNavigationGuard();
    installOfflinePosHistoryTrap();
}

window.PosOffline = {
    init(opts) {
        const o = opts || {};
        if (o.saveOrderUrl) {
            saveOrderUrl = o.saveOrderUrl;
        }
        if (o.syncPaymentUrl) {
            syncPaymentUrl = o.syncPaymentUrl;
        }
        optsRef.currencyCode =
            o.currencyCode || optsRef.currencyCode || "USD";
        optsRef.currencySymbol =
            o.currencySymbol || optsRef.currencySymbol || "$";
        optsRef.labels = { ...optsRef.labels, ...(o.labels || {}) };
        if (o.navGuardLeaveUrl) {
            optsRef.navGuardLeaveUrl = o.navGuardLeaveUrl;
        }

        if (!saveOrderUrl) {
            console.warn("PosOffline.init: saveOrderUrl is missing");
        }
        if (!syncPaymentUrl) {
            console.warn("PosOffline.init: syncPaymentUrl is missing");
        }

        setupListeners();

        if (!initDone) {
            initDone = true;
        }

        render();

        if (isPosEffectiveOnline()) {
            syncQueue();
        }
    },

    shouldQueueNow() {
        return !isPosEffectiveOnline();
    },

    isEffectiveOnline() {
        return isPosEffectiveOnline();
    },

    /**
     * Debug: force this tab to behave as offline (or release) without toggling system Wi‑Fi.
     * @param {boolean} forced
     */
    setForceOfflineTest(forced) {
        if (typeof window !== "undefined") {
            window.__posForceOfflineTest = !!forced;
        }
        if (isPosEffectiveOnline()) {
            uninstallOfflinePosHistoryTrap();
            hideOfflineNavGuardModal();
            render();
            syncQueue();
        } else {
            installOfflinePosHistoryTrap();
            render();
        }
    },

    /**
     * @param {object} orderData — POST body for ajax.pos.save-order
     * @param {object} [summary] — display-only fields: items[{name,quantity,price,amount}], order_type, table_no, customer, totals, actions
     */
    queueSaveOrder(orderData, summary) {
        const payload = deepClone(orderData);
        if (!payload) {
            return null;
        }

        const queue = loadQueue();
        const op = {
            id: `${Date.now()}_${Math.random().toString(16).slice(2)}`,
            type: "save_order",
            createdAt: new Date().toISOString(),
            payload,
            summary:
                summary && typeof summary === "object"
                    ? deepClone(summary)
                    : buildSummaryFromPayload(payload),
        };
        queue.push(op);
        saveQueue(queue);
        render();
        return op.id;
    },

    /**
     * Queue a payment to POST after the matching order save has synced (same offline_queue_group_key).
     * @param {object} paymentPayload — order_id (optional until sync), offline_queue_group_key, payment_method, payment_amount (tendered), return_amount (change)
     * @param {object} [summary] — display-only fields for the offline modal
     */
    queueRecordPayment(paymentPayload, summary) {
        const payload = deepClone(paymentPayload);
        if (!payload) {
            return null;
        }

        const queue = loadQueue();
        const op = {
            id: `${Date.now()}_${Math.random().toString(16).slice(2)}`,
            type: "record_payment",
            createdAt: new Date().toISOString(),
            payload,
            summary:
                summary && typeof summary === "object"
                    ? deepClone(summary)
                    : {},
        };
        queue.push(op);
        saveQueue(queue);
        render();
        return op.id;
    },

    getPendingCount() {
        const q = loadQueue().filter((x) => x.type === "save_order");
        return q.length ? groupSaveOrderOps(q).length : 0;
    },

    syncPending() {
        return syncQueue();
    },

    refreshUI() {
        render();
    },
};
