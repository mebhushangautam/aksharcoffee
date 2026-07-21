{{-- Defines window.syncPosSidebarOrderTypeSections — include inside a <script> block only (no nested script tags). --}}
window.posNormalizeSidebarOrderTypeKey = function(v) {
    if (v === undefined || v === null || v === '') {
        return '';
    }
    var s = String(v).trim().toLowerCase().replace(/-/g, '_');
    // Some installs use DB `type` values that differ from slug `room_service`.
    if (s === 'hotel_room' || s === 'hotel_room_service' || s === 'hotel_roomservice') {
        return 'room_service';
    }
    return s;
};
window.syncPosSidebarOrderTypeSections = function() {
    var st = window.posState || {};
    var oid = st.orderTypeId;
    // Sidebar sections use slug-shaped keys (dine_in, delivery, pickup, room_service).
    // `posState.orderType` comes from OrderType.type and can differ from slug for some branches;
    // normalize slug/type so room service stays visible across casing, hyphens, and Hotel type aliases.
    var slugN = window.posNormalizeSidebarOrderTypeKey(st.orderTypeSlug);
    var typeN = window.posNormalizeSidebarOrderTypeKey(st.orderType);
    document.querySelectorAll('[data-pos-sidebar-order-type]').forEach(function(el) {
        var wanted = el.getAttribute('data-pos-sidebar-order-type');
        var wantedN = window.posNormalizeSidebarOrderTypeKey(wanted);
        var show = !!(oid && (wantedN === slugN || wantedN === typeN));
        if (el.getAttribute('data-pos-sidebar-use-contents') === '1') {
            el.style.display = show ? 'contents' : 'none';
        } else {
            el.classList.toggle('hidden', !show);
        }
    });
};
