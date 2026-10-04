<x-app-layout>
    <x-slot name="headerWidth">max-w-6xl</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Incident Map') }}</h2>
    </x-slot>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css">

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div class="cg-card flex flex-wrap items-center gap-2">
                @foreach (['critical' => '#dc2626', 'high' => '#f97316', 'moderate' => '#facc15', 'low' => '#22c55e'] as $sev => $color)
                    <button type="button" data-sev="{{ $sev }}" aria-pressed="true"
                            class="sev-chip inline-flex min-h-[40px] items-center gap-2 rounded-full border border-gray-300 dark:border-gray-600 px-3 py-1.5 text-sm font-medium text-gray-800 dark:text-gray-200">
                        <span class="h-3 w-3 rounded-full" style="background: {{ $color }}"></span>{{ __(ucfirst($sev)) }}
                    </button>
                @endforeach
                <label class="ml-auto inline-flex min-h-[40px] items-center gap-2 text-sm text-gray-800 dark:text-gray-200">
                    <input id="active-only" type="checkbox" checked class="rounded border-gray-300 text-maroon-600 focus:ring-maroon-500">
                    {{ __('Active reports only') }}
                </label>
            </div>

            <div class="cg-card p-2">
                <div id="incident-map" style="height:65vh;min-height:360px" class="w-full rounded-lg" role="region" aria-label="{{ __('Incident map') }}"></div>
            </div>

            <p class="text-sm cg-muted">
                <span id="pin-count">0</span> {{ __('reports shown.') }}
                @if ($missing > 0)
                    {{ $missing }} {{ __('open report(s) have no pinned location and are not on the map.') }}
                @endif
            </p>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>
    <script>
        const pins = {{ Js::from($pins) }};
        const center = {{ Js::from($center) }};
        const boundaryUrl = {{ Js::from($boundaryUrl) }};
        let locked = false;
        const colors = { low: '#22c55e', moderate: '#facc15', high: '#f97316', critical: '#dc2626' };
        const openLabel = {{ Js::from(__('Open report')) }};

        const map = L.map('incident-map');
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);
        const layer = L.layerGroup().addTo(map);
        const shown = new Set(['low', 'moderate', 'high', 'critical']);

        function popup(p) {
            const box = document.createElement('div');
            const title = document.createElement('strong');
            title.textContent = p.category + ' #' + p.id;
            const info = document.createElement('div');
            info.textContent = p.location + ' · ' + p.age;
            const link = document.createElement('a');
            link.href = p.url;
            link.textContent = openLabel;
            link.style.fontWeight = '600';
            box.append(title, info, link);
            return box;
        }

        function render() {
            const activeOnly = document.getElementById('active-only').checked;
            layer.clearLayers();
            const pts = [];
            pins.forEach(p => {
                if (!shown.has(p.severity)) return;
                if (activeOnly && p.status === 'resolved') return;
                L.circleMarker([p.lat, p.lng], {
                    radius: 9, color: '#ffffff', weight: 2, fillColor: colors[p.severity], fillOpacity: 0.95
                }).bindPopup(popup(p)).addTo(layer);
                pts.push([p.lat, p.lng]);
            });
            document.getElementById('pin-count').textContent = pts.length;
            if (!locked) { if (pts.length) { map.fitBounds(pts, { padding: [40, 40], maxZoom: 17 }); } else { map.setView(center, 15); } }
        }

        document.querySelectorAll('.sev-chip').forEach(btn => {
            btn.addEventListener('click', () => {
                const sev = btn.dataset.sev;
                if (shown.has(sev)) { shown.delete(sev); } else { shown.add(sev); }
                btn.setAttribute('aria-pressed', shown.has(sev));
                btn.style.opacity = shown.has(sev) ? '1' : '0.45';
                render();
            });
        });
        document.getElementById('active-only').addEventListener('change', render);

        function lockTo(bounds) {
            const padded = bounds.pad(0.3);
            map.setMaxBounds(padded);
            map.setMinZoom(map.getBoundsZoom(padded));
            map.fitBounds(bounds);
            locked = true;
        }
        map.setView(center, 15);
        lockTo(L.latLngBounds([center[0] - 0.03, center[1] - 0.03], [center[0] + 0.03, center[1] + 0.03]));

        if (boundaryUrl) {
            fetch(boundaryUrl).then(r => r.json()).then(data => {
                const polys = (data.features || []).filter(f => /Polygon/.test(f.geometry.type));
                if (!polys.length) return;
                const outline = L.geoJSON(polys, { style: { color: '#7f1d1d', weight: 3, fillOpacity: 0.05, dashArray: '6 4' } }).addTo(map);
                lockTo(outline.getBounds());
            }).catch(() => {});
        }
        render();
    </script>
</x-app-layout>
