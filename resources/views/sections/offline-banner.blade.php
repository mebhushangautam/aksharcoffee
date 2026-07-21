<div
    id="global-offline-banner"
    class="hidden fixed top-0 inset-x-0 z-[9999] bg-red-600 text-white text-center text-sm font-semibold px-6 py-2.5 shadow-md"
    role="status"
    aria-live="polite"
>
    No internet connection
</div>

<style>
    body {
        --offline-banner-offset: 0px;
    }

    body.offline-banner-visible {
        --offline-banner-offset: 40px;
        padding-top: 40px;
    }

    body.offline-banner-visible nav.fixed {
        top: var(--offline-banner-offset);
    }

    body.offline-banner-visible #sidebar {
        top: var(--offline-banner-offset);
        height: calc(100% - var(--offline-banner-offset));
    }

    body.offline-banner-visible #sidebarBackdrop.fixed {
        top: var(--offline-banner-offset);
    }
</style>

<script>
    (function () {
        if (window.__globalOfflineBannerInitialized) {
            return;
        }
        window.__globalOfflineBannerInitialized = true;

        function getBanner() {
            return document.getElementById('global-offline-banner');
        }

        function updateOfflineBanner() {
            var banner = getBanner();
            if (!banner) {
                return;
            }

            var isOffline = !navigator.onLine;
            banner.classList.toggle('hidden', !isOffline);
            document.body.classList.toggle('offline-banner-visible', isOffline);
        }

        window.addEventListener('online', updateOfflineBanner);
        window.addEventListener('offline', updateOfflineBanner);
        document.addEventListener('DOMContentLoaded', updateOfflineBanner);
        document.addEventListener('livewire:navigated', updateOfflineBanner);

        updateOfflineBanner();
    })();
</script>
