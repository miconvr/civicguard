<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Analytics
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="flex justify-end">
                <a href="{{ route('admin.analytics.exportPdf') }}" class="inline-flex items-center bg-maroon-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium shadow-sm hover:bg-maroon-800 hover:shadow-md transition">
                    Export PDF
                </a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="cg-card text-center">
                    <p class="text-3xl font-bold text-maroon-700">{{ $totalReports }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Reports</p>
                </div>
                <div class="cg-card text-center">
                    <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ $pendingCount }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Pending</p>
                </div>
                <div class="cg-card text-center">
                    <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $resolvedCount }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Resolved</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="cg-card">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Reports by Category</h3>
                    <canvas id="categoryChart"></canvas>
                </div>
                <div class="cg-card">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Reports by Severity</h3>
                    <canvas id="severityChart"></canvas>
                </div>
            </div>

            <div class="cg-card">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Reports Over Last 14 Days</h3>
                <canvas id="trendChart"></canvas>
            </div>

            <div id="ai-insights-section" class="cg-card">
                <div id="ai-insights-prompt" class="text-center py-6">
                    <svg class="h-8 w-8 text-maroon-700 dark:text-maroon-400 mx-auto mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Get AI-generated patterns, risks, and recommendations based on this data.</p>
                    <button id="load-insights-btn" class="cg-btn">
                        <span id="load-insights-label">Load AI Insights</span>
                        <svg id="load-insights-spinner" class="hidden animate-spin h-4 w-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </button>
                </div>

                <div id="ai-insights-results" class="hidden space-y-6">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">AI Summary</h3>
                        <p id="ai-summary-text" class="text-sm text-gray-700 dark:text-gray-300"></p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Recommended Actions</h3>
                            <ul id="ai-recommendations-list" class="space-y-2 text-sm text-gray-700 dark:text-gray-300"></ul>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Observed Patterns</h3>
                            <ul id="ai-patterns-list" class="space-y-2 text-sm text-gray-700 dark:text-gray-300"></ul>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Risk Flags</h3>
                            <ul id="ai-risks-list" class="space-y-2 text-sm text-gray-700 dark:text-gray-300"></ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script>
        const isDark = () => document.documentElement.classList.contains('dark');
        const accent = () => '#7f1d1d';
        const accentFill = () => isDark() ? 'rgba(127,29,29,0.24)' : 'rgba(127,29,29,0.1)';

        const categoryLabels = @json($byCategory->pluck('name'));
        const categoryData = @json($byCategory->pluck('total'));
        const severityMap = @json($bySeverity);
        const severityLabels = ['low', 'moderate', 'high', 'critical'];
        const severityData = severityLabels.map(s => severityMap[s]?.total ?? 0);
        const trendLabels = @json($last14Days->pluck('day'));
        const trendData = @json($last14Days->pluck('total'));

        const categoryChart = new Chart(document.getElementById('categoryChart'), {
            type: 'bar',
            data: {
                labels: categoryLabels,
                datasets: [{ label: 'Reports', data: categoryData, backgroundColor: accent() }]
            },
            options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
        });

        const severityChart = new Chart(document.getElementById('severityChart'), {
            type: 'doughnut',
            data: {
                labels: severityLabels.map(s => s.charAt(0).toUpperCase() + s.slice(1)),
                datasets: [{ data: severityData, backgroundColor: ['#9ca3af', '#eab308', '#f97316', '#dc2626'], borderWidth: 0 }]
            }
        });

        const trendChart = new Chart(document.getElementById('trendChart'), {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: [{ label: 'Reports Filed', data: trendData, borderColor: accent(), backgroundColor: accentFill(), fill: true, tension: 0.3 }]
            },
            options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
        });

        function applyTheme() {
            Chart.defaults.color = isDark() ? '#d1d5db' : '#4b5563';
            Chart.defaults.borderColor = isDark() ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)';
            categoryChart.data.datasets[0].backgroundColor = accent();
            trendChart.data.datasets[0].borderColor = accent();
            trendChart.data.datasets[0].backgroundColor = accentFill();
            [categoryChart, severityChart, trendChart].forEach(c => c.update());
        }

        applyTheme();
        new MutationObserver(applyTheme).observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });

        function fillList(id, items, bulletColor) {
            const list = document.getElementById(id);
            list.innerHTML = '';
            if (!items || items.length === 0) {
                list.innerHTML = '<li class="text-gray-500 dark:text-gray-400">None detected.</li>';
                return;
            }
            items.forEach(text => {
                const li = document.createElement('li');
                li.className = 'flex gap-2';
                li.innerHTML = `<span class="${bulletColor}">&bull;</span><span></span>`;
                li.querySelector('span:last-child').textContent = text;
                list.appendChild(li);
            });
        }

        document.getElementById('load-insights-btn')?.addEventListener('click', async () => {
            const btn = document.getElementById('load-insights-btn');
            btn.disabled = true;
            document.getElementById('load-insights-label').textContent = 'Analyzing Data...';
            document.getElementById('load-insights-spinner').classList.remove('hidden');

            try {
                const response = await fetch("{{ route('admin.analytics.insights') }}");
                const data = await response.json();

                document.getElementById('ai-summary-text').textContent = data.analysisSummary;
                fillList('ai-recommendations-list', data.recommendations, 'text-maroon-700');
                fillList('ai-patterns-list', data.patterns, 'text-maroon-700');
                fillList('ai-risks-list', data.riskFlags, 'text-red-600');

                document.getElementById('ai-insights-prompt').classList.add('hidden');
                document.getElementById('ai-insights-results').classList.remove('hidden');
            } catch (err) {
                document.getElementById('load-insights-label').textContent = 'Failed \u2014 Try Again';
                document.getElementById('load-insights-spinner').classList.add('hidden');
                btn.disabled = false;
            }
        });
    </script>
</x-app-layout>