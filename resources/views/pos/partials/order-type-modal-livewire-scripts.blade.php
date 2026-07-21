{{-- Livewire POS: read bootstrap from #lw-pos-order-type-client-data so large JSON never sits inside a JS <script> (HTML closes script at any literal </script>, including inside strings — breaks parser & Livewire). --}}
(function() {
    var __lw = {};
    try {
        var el = document.getElementById('lw-pos-order-type-client-data');
        if (el && el.textContent) {
            __lw = JSON.parse(el.textContent);
        }
    } catch (e) {
        console.error('lw-pos-order-type-client-data: invalid JSON', e);
    }

    var L = __lw.labels || {};

    window.posState = window.posState || {};
    if (__lw.orderTypeId) {
        window.posState.orderTypeId = __lw.orderTypeId;
    }
    if (__lw.orderType) {
        window.posState.orderType = __lw.orderType;
    }
    if (__lw.orderTypeSlug) {
        window.posState.orderTypeSlug = __lw.orderTypeSlug;
    }

    window.posOrderTypePriceMaps = __lw.posOrderTypePriceMaps || {};
    window.posExtraChargesBySlug = __lw.posExtraChargesBySlug || {};
    window.posDeliveryDefaultFee = typeof __lw.posDeliveryDefaultFee === 'number' ? __lw.posDeliveryDefaultFee : 0;
    window.posOrderTypesForModal = __lw.posOrderTypesForModal || [];
    window.posDeliveryPlatformsForModal = __lw.posDeliveryPlatformsForModal || [];
    window.posOrderTypeDefaultSaveUrl = __lw.posOrderTypeDefaultSaveUrl || '';

    window.__posOrderTypeModalPending = { orderTypeId: null, slug: null };

    window.posNormalizedDeliveryAppId = function(v) {
        if (v === undefined || v === null || v === '' || v === 'default') {
            return null;
        }
        const n = parseInt(String(v), 10);
        return Number.isNaN(n) ? null : n;
    };

    window.posOrderTypeModalPickType = function(orderTypeId, slug) {
        window.__posOrderTypeModalPending = { orderTypeId: orderTypeId, slug: slug };
        if (slug === 'delivery') {
            if (typeof jQuery !== 'undefined') {
                jQuery('#pos-otm-stage-types').addClass('hidden');
                jQuery('#pos-otm-stage-platforms').removeClass('hidden');
                jQuery('#pos-otm-back-btn').removeClass('hidden');
                jQuery('#pos-otm-title').text(L.selectDeliveryPlatform || '');
                jQuery('#pos-otm-description').text(L.selectDeliveryPlatformDescription || '');
            }
            return;
        }
        window.posFinalizeOrderTypeSelection(orderTypeId, slug, null);
    };

    window.posOrderTypeModalPickPlatform = function(platform) {
        const p = window.__posOrderTypeModalPending;
        if (!p || !p.orderTypeId || !p.slug) {
            return;
        }
        window.posFinalizeOrderTypeSelection(p.orderTypeId, p.slug, platform);
    };

    window.posOrderTypeModalGoBack = function() {
        if (typeof jQuery !== 'undefined') {
            jQuery('#pos-otm-stage-types').removeClass('hidden');
            jQuery('#pos-otm-stage-platforms').addClass('hidden');
            jQuery('#pos-otm-back-btn').addClass('hidden');
            jQuery('#pos-otm-title').text(L.selectOrderType || '');
            jQuery('#pos-otm-description').text(L.selectOrderTypeDescription || '');
        }
        window.__posOrderTypeModalPending = { orderTypeId: null, slug: null };
    };

    window.showPosOrderTypeModal = function() {
        window.posOrderTypeModalGoBack();
        if (typeof jQuery !== 'undefined') {
            jQuery('#pos-set-order-type-default').prop('checked', false);
            jQuery('#pos-order-type-modal').css('display', 'flex');
        }
    };

    window.hidePosOrderTypeModal = function() {
        if (typeof jQuery !== 'undefined') {
            jQuery('#pos-order-type-modal').hide();
        }
        window.posOrderTypeModalGoBack();
    };

    window.posFinalizeOrderTypeSelection = function(orderTypeId, slug, deliveryPlatform) {
        const plat = (deliveryPlatform === undefined || deliveryPlatform === null || deliveryPlatform === 'default')
            ? null
            : deliveryPlatform;

        if (typeof jQuery !== 'undefined' && jQuery('#pos-set-order-type-default').is(':checked')) {
            jQuery.easyAjax({
                url: window.posOrderTypeDefaultSaveUrl,
                type: 'POST',
                data: {
                    order_type_id: orderTypeId,
                    _token: __lw.csrfToken || ''
                },
                success: function() {},
                error: function() {}
            });
        }
        if (typeof jQuery !== 'undefined') {
            jQuery('#pos-set-order-type-default').prop('checked', false);
        }

        window.hidePosOrderTypeModal();

        var meta = (window.posOrderTypesForModal || []).find(function(t) {
            return String(t.id) === String(orderTypeId);
        });
        window.posState.orderTypeId = orderTypeId;
        window.posState.orderTypeSlug = slug;
        window.posState.orderType = (meta && meta.type) ? meta.type : slug;
        window.posState.selectedDeliveryApp = plat;

        if (typeof window.syncPosSidebarOrderTypeSections === 'function') {
            window.syncPosSidebarOrderTypeSections();
        }

        var lwCall = $wire.call('setOrderTypeChoice', {
            orderTypeId: orderTypeId,
            deliveryPlatform: plat
        });
        if (lwCall && typeof lwCall.then === 'function') {
            lwCall.then(function() {
                if (typeof window.syncPosSidebarOrderTypeSections === 'function') {
                    window.syncPosSidebarOrderTypeSections();
                }
            });
        }
    };

    function bindBackdropClose() {
        if (typeof jQuery === 'undefined') {
            return;
        }
        const $modal = jQuery('#pos-order-type-modal');
        if (!$modal.length) {
            return;
        }
        $modal.off('click.posLwBackdrop').on('click.posLwBackdrop', function(e) {
            if (e.target === this && window.posState && window.posState.orderTypeId) {
                window.hidePosOrderTypeModal();
            }
        });
    }

    function tryOpenModalOnReady() {
        if (__lw.hasOrderTypeId) {
            if (typeof window.syncPosSidebarOrderTypeSections === 'function') {
                window.syncPosSidebarOrderTypeSections();
            }
            return;
        }
        if (typeof window.showPosOrderTypeModal === 'function') {
            window.showPosOrderTypeModal();
        }
        if (typeof window.syncPosSidebarOrderTypeSections === 'function') {
            window.syncPosSidebarOrderTypeSections();
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            bindBackdropClose();
            tryOpenModalOnReady();
        });
    } else {
        bindBackdropClose();
        tryOpenModalOnReady();
    }
})();
