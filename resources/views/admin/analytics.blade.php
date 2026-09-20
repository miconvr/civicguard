<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Analytics
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

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

            <div class="cg-card">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Recommended Follow-up Actions</h3>
                <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                    @forelse ($recommendations as $recommendation)
                        <li class="flex gap-2"><span class="text-maroon-700">&bull;</span><span>{{ $recommendation }}</span></li>
                    @empty
                        <li class="text-gray-500 dark:text-gray-400">No recommendations available yet.</li>
                    @endforelse
                </ul>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="cg-card">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">AI Summary</h3>
                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $analysisSummary }}</p>
                </div>
                <div class="cg-card">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Observed Patterns</h3>
                    <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                        @forelse ($patterns as $pattern)
                            <li class="flex gap-2"><span class="text-maroon-700">&bull;</span><span>{{ $pattern }}</span></li>
                        @empty
                            <li class="text-gray-500 dark:text-gray-400">No patterns detected yet.</li>
                        @endforelse
                    </ul>
                </div>
                <div class="cg-card">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Risk Flags</h3>
                    <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                        @forelse ($riskFlags as $riskFlag)
                            <li class="flex gap-2"><span class="text-red-600">&bull;</span><span>{{ $riskFlag }}</span></li>
                        @empty
                            <li class="text-gray-500 dark:text-gray-400">No risk flags detected.</li>
                        @endforelse
                    </ul>
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

        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script>
        const isDark = () => document.documentElement.classList.contains('dark');
        const accent = () => isDark() ? '#7f1d1d' : '#7f1d1d';
        const accentFill = () => isDark() ? 'rgba(127,29,29,0.15)' : 'rgba(127,29,29,0.1)';

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
        // Re-color the charts whenever the dark mode toggle flips the class on <html>
        new MutationObserver(applyTheme).observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
    </script>
</x-app-layout>
