<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Analytics
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white p-6 shadow sm:rounded-lg text-center">
                    <p class="text-3xl font-bold text-maroon-700">{{ $totalReports }}</p>
                    <p class="text-sm text-gray-500 mt-1">Total Reports</p>
                </div>
                <div class="bg-white p-6 shadow sm:rounded-lg text-center">
                    <p class="text-3xl font-bold text-yellow-600">{{ $pendingCount }}</p>
                    <p class="text-sm text-gray-500 mt-1">Pending</p>
                </div>
                <div class="bg-white p-6 shadow sm:rounded-lg text-center">
                    <p class="text-3xl font-bold text-green-600">{{ $resolvedCount }}</p>
                    <p class="text-sm text-gray-500 mt-1">Resolved</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white p-6 shadow sm:rounded-lg">
                    <h3 class="text-sm font-medium text-gray-700 mb-4">Reports by Category</h3>
                    <canvas id="categoryChart"></canvas>
                </div>
                <div class="bg-white p-6 shadow sm:rounded-lg">
                    <h3 class="text-sm font-medium text-gray-700 mb-4">Reports by Severity</h3>
                    <canvas id="severityChart"></canvas>
                </div>
            </div>

            <div class="bg-white p-6 shadow sm:rounded-lg">
                <h3 class="text-sm font-medium text-gray-700 mb-4">Reports Over Last 14 Days</h3>
                <canvas id="trendChart"></canvas>
            </div>

        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script>
        const categoryLabels = @json($byCategory->pluck('name'));
        const categoryData = @json($byCategory->pluck('total'));
        new Chart(document.getElementById('categoryChart'), {
            type: 'bar',
            data: {
                labels: categoryLabels,
                datasets: [{ label: 'Reports', data: categoryData, backgroundColor: '#7f1d1d' }]
            },
            options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
        });

        const severityLabels = ['low', 'moderate', 'high', 'critical'];
        const severityData = severityLabels.map(s => {{ Illuminate\Support\Js::from($bySeverity) }}[s]?.total ?? 0);
        new Chart(document.getElementById('severityChart'), {
            type: 'doughnut',
            data: {
                labels: severityLabels.map(s => s.charAt(0).toUpperCase() + s.slice(1)),
                datasets: [{ data: severityData, backgroundColor: ['#9ca3af', '#eab308', '#f97316', '#dc2626'] }]
            }
        });

        const trendLabels = @json($last14Days->pluck('day'));
        const trendData = @json($last14Days->pluck('total'));
        new Chart(document.getElementById('trendChart'), {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: [{ label: 'Reports Filed', data: trendData, borderColor: '#7f1d1d', backgroundColor: 'rgba(127,29,29,0.1)', fill: true, tension: 0.3 }]
            },
            options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
        });
    </script>
</x-app-layout>
