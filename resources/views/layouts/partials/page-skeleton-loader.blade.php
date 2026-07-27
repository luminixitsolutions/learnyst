{{-- Full-page overlay skeleton shown during in-app navigation --}}
<div id="panelNavSkeleton" class="panel-nav-skeleton hidden" aria-hidden="true">
    <div class="panel-nav-skeleton__backdrop"></div>
    <div class="panel-nav-skeleton__content p-4 sm:p-6 lg:p-8">
        <x-admin.page-skeleton />
    </div>
</div>

<style>
    /* Navigation overlay — content area only; sidebar skeleton stays visible above */
    .panel-nav-skeleton {
        position: fixed;
        inset: 0;
        z-index: 54;
        pointer-events: none;
    }
    .panel-nav-skeleton.hidden { display: none; }
    .panel-nav-skeleton__backdrop {
        position: absolute;
        inset: 0;
        background: rgba(248, 250, 252, 0.72);
        backdrop-filter: blur(2px);
    }
    .panel-nav-skeleton__content {
        position: absolute;
        top: 4rem;
        left: 0;
        right: 0;
        bottom: 0;
        overflow: hidden;
    }

    /* Vertical layout — content beside sidebar */
    @media (min-width: 1024px) {
        body.panel-layout-vertical .panel-nav-skeleton__content {
            left: 18rem;
        }
    }

    /* Horizontal layout — content below top menu bar */
    body.panel-layout-horizontal .panel-nav-skeleton__content {
        top: 7.5rem;
        left: 0;
    }
</style>

<style>
    body.panel-page-ready .panel-page-content {
        animation: panelContentFadeIn .35s ease-out;
    }
    @keyframes panelContentFadeIn {
        from { opacity: 0; transform: translateY(6px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
(function () {
    function markPageReady() {
        document.body.classList.add('panel-page-ready');
        var overlay = document.getElementById('panelNavSkeleton');
        if (overlay) overlay.classList.add('hidden');
        try { sessionStorage.removeItem('panelNavLoading'); } catch (e) {}
    }

    function showNavSkeleton() {
        var overlay = document.getElementById('panelNavSkeleton');
        if (overlay) overlay.classList.remove('hidden');
        document.body.classList.remove('panel-page-ready');
    }

    function isCompanyNavLink(link) {
        if (!link || link.target === '_blank' || link.hasAttribute('download')) return false;
        if (link.dataset.noSkeleton !== undefined) return false;
        var href = link.getAttribute('href');
        if (!href || href.charAt(0) === '#' || href.indexOf('javascript:') === 0) return false;
        try {
            var url = new URL(href, window.location.origin);
            return url.origin === window.location.origin && url.pathname.indexOf('/company') === 0;
        } catch (e) {
            return false;
        }
    }

    document.addEventListener('click', function (event) {
        var link = event.target.closest('a[href]');
        if (!isCompanyNavLink(link)) return;
        if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
        try { sessionStorage.setItem('panelNavLoading', '1'); } catch (e) {}
        showNavSkeleton();
    }, true);

    document.addEventListener('submit', function (event) {
        var form = event.target;
        if (!form || form.target === '_blank' || form.dataset.noSkeleton !== undefined) return;
        var action = form.getAttribute('action') || '';
        try {
            var url = new URL(action, window.location.origin);
            if (url.origin === window.location.origin && url.pathname.indexOf('/company') === 0) {
                try { sessionStorage.setItem('panelNavLoading', '1'); } catch (e) {}
                showNavSkeleton();
            }
        } catch (e) {}
    }, true);

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initReady);
    } else {
        initReady();
    }

    function initReady() {
        var wasNavigating = false;
        try { wasNavigating = sessionStorage.getItem('panelNavLoading') === '1'; } catch (e) {}

        var delay = wasNavigating ? 280 : 120;

        window.requestAnimationFrame(function () {
            window.setTimeout(markPageReady, delay);
        });
    }

    window.addEventListener('pageshow', function (event) {
        if (event.persisted) {
            markPageReady();
        }
    });
})();
</script>
