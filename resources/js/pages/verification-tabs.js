export function registerPage(Alpine) {
Alpine.data('verificationTabs', (initial) => ({
    status: initial.status,
    description: initial.description,
    loading: false,
    requestController: null,
    requestVersion: 0,
    popstateHandler: null,

    init() {
        this.popstateHandler = () => this.load(window.location.href, false);
        window.addEventListener('popstate', this.popstateHandler);
    },

    destroy() {
        window.removeEventListener('popstate', this.popstateHandler);
        this.requestController?.abort();
    },

    navigate(event) {
        const link = event.target.closest('a[data-verification-tab], #verification-results nav a');
        if (!link || link.target || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;

        event.preventDefault();
        this.load(link.href, true);
    },

    async load(url, addHistoryEntry) {
        this.requestController?.abort();
        const controller = new AbortController();
        const version = ++this.requestVersion;
        this.requestController = controller;
        this.loading = true;

        try {
            const response = await fetch(url, {
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                signal: controller.signal,
            });

            if (!response.ok || !response.headers.get('content-type')?.includes('application/json')) {
                window.location.assign(url);
                return;
            }

            const payload = await response.json();
            const results = this.$root.querySelector('#verification-results');
            if (!results) {
                window.location.assign(url);
                return;
            }

            Array.from(results.children).forEach(child => window.Alpine.destroyTree(child));
            results.innerHTML = payload.html;
            Array.from(results.children).forEach(child => window.Alpine.initTree(child));

            this.status = payload.status;
            this.description = payload.description;
            const statusInput = this.$root.querySelector('input[name="status"]');
            if (statusInput) statusInput.value = payload.status;

            if (addHistoryEntry) window.history.pushState({}, '', url);
        } catch (error) {
            if (error.name !== 'AbortError') window.location.assign(url);
        } finally {
            if (version === this.requestVersion) {
                this.loading = false;
                this.requestController = null;
            }
        }
    },
}));
}
