@extends('layouts.app')

@section('title', 'KPI Dashboard')

@section('content')
@include('layouts.sidebar')
@include('components.toast')

<!-- Main Content -->
<div class="lg:ml-20 min-h-screen flex flex-col bg-slate-50">
    @include('layouts.header')

    <!-- Page Content -->
    <main class="flex-1 px-4 py-4 lg:px-6 lg:py-6">
        <!-- Page Title & Header -->
        <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">KPI Dashboard</h1>
                <p class="text-slate-500 text-sm mt-1">Overview of Key Performance Indicators, departmental achievements, and pillar targets.</p>
            </div>
            <div class="flex items-center gap-3">
                <select id="kpiYearFilter" class="text-sm font-semibold text-slate-700 border border-slate-300 rounded-xl px-4 py-2 bg-white outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="2026">Year 2026</option>
                    <option value="2025">Year 2025</option>
                </select>
            </div>
        </div>

        <!-- Metric Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Total KPIs -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-400 tracking-wider">Total KPIs</p>
                        <h4 class="text-2xl font-extrabold text-slate-800 mt-1">24</h4>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold">
                        <i class="fa-solid fa-list-check"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs text-slate-500">
                    <span class="text-emerald-600 font-semibold flex items-center gap-1 mr-1">
                        <i class="fa-solid fa-arrow-up"></i> 100%
                    </span>
                    <span>monitored parameters</span>
                </div>
            </div>

            <!-- Target Achieved -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-400 tracking-wider">Target Achieved</p>
                        <h4 class="text-2xl font-extrabold text-emerald-600 mt-1">19</h4>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs text-slate-500">
                    <span class="text-emerald-600 font-semibold flex items-center gap-1 mr-1">
                        79.2%
                    </span>
                    <span>achievement rate</span>
                </div>
            </div>

            <!-- In Progress / Need Focus -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-400 tracking-wider">Need Attention</p>
                        <h4 class="text-2xl font-extrabold text-amber-600 mt-1">3</h4>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs text-slate-500">
                    <span class="text-amber-600 font-semibold flex items-center gap-1 mr-1">
                        12.5%
                    </span>
                    <span>near target threshold</span>
                </div>
            </div>

            <!-- Critical / Off Target -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-400 tracking-wider">Off Target</p>
                        <h4 class="text-2xl font-extrabold text-rose-600 mt-1">2</h4>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl font-bold">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs text-slate-500">
                    <span class="text-rose-600 font-semibold flex items-center gap-1 mr-1">
                        8.3%
                    </span>
                    <span>action plan required</span>
                </div>
            </div>
        </div>

        <!-- Main Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Left Chart: Pillar Achievement (2 Columns) -->
            <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">KPI Performance by Pillar</h3>
                        <p class="text-xs text-slate-500">Target vs Actual achievement rate (%) per strategic pillar</p>
                    </div>
                </div>
                <div class="relative h-72 w-full">
                    <canvas id="kpiPillarChart"></canvas>
                </div>
            </div>

            <!-- Right Chart: Status Distribution (1 Column) -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col">
                <div class="mb-4">
                    <h3 class="text-lg font-bold text-slate-800">Status Distribution</h3>
                    <p class="text-xs text-slate-500">Overall KPI status breakdown</p>
                </div>
                <div class="relative h-56 w-full flex justify-center items-center my-auto">
                    <canvas id="kpiStatusPieChart"></canvas>
                </div>
                <div class="grid grid-cols-3 gap-2 text-center text-xs mt-4 pt-3 border-t border-slate-100">
                    <div class="p-2 rounded-xl bg-emerald-50 border border-emerald-100">
                        <p class="text-emerald-700 font-semibold">Met</p>
                        <p class="font-bold text-slate-800 text-sm mt-0.5">79%</p>
                    </div>
                    <div class="p-2 rounded-xl bg-amber-50 border border-amber-100">
                        <p class="text-amber-700 font-semibold">Warning</p>
                        <p class="font-bold text-slate-800 text-sm mt-0.5">13%</p>
                    </div>
                    <div class="p-2 rounded-xl bg-rose-50 border border-rose-100">
                        <p class="text-rose-700 font-semibold">Off Target</p>
                        <p class="font-bold text-slate-800 text-sm mt-0.5">8%</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Department KPI Overview Table -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-800 text-lg">Departmental KPI Highlights</h3>
                    <p class="text-xs text-slate-400">Current year key achievement status per department</p>
                </div>
                <a href="{{ route('kpi.monthly-summary') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                    <span>View Monthly Summary</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase">
                            <th class="p-4">Department</th>
                            <th class="p-4">Total KPIs</th>
                            <th class="p-4">Achieved</th>
                            <th class="p-4">On Track %</th>
                            <th class="p-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-4 font-semibold text-slate-800">Quality Management System (QMS)</td>
                            <td class="p-4 text-slate-600">6</td>
                            <td class="p-4 text-slate-600">6</td>
                            <td class="p-4 text-slate-700 font-medium">100.0%</td>
                            <td class="p-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold bg-emerald-100 text-emerald-800 rounded-full">
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>Excellent
                                </span>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-4 font-semibold text-slate-800">Production & Manufacturing</td>
                            <td class="p-4 text-slate-600">8</td>
                            <td class="p-4 text-slate-600">6</td>
                            <td class="p-4 text-slate-700 font-medium">75.0%</td>
                            <td class="p-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold bg-emerald-100 text-emerald-800 rounded-full">
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>Good
                                </span>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-4 font-semibold text-slate-800">Quality Control (QC)</td>
                            <td class="p-4 text-slate-600">5</td>
                            <td class="p-4 text-slate-600">4</td>
                            <td class="p-4 text-slate-700 font-medium">80.0%</td>
                            <td class="p-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold bg-emerald-100 text-emerald-800 rounded-full">
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>Good
                                </span>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-4 font-semibold text-slate-800">Maintenance & Facilities</td>
                            <td class="p-4 text-slate-600">5</td>
                            <td class="p-4 text-slate-600">3</td>
                            <td class="p-4 text-slate-700 font-medium">60.0%</td>
                            <td class="p-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold bg-amber-100 text-amber-800 rounded-full">
                                    <span class="w-1.5 h-1.5 bg-amber-500 rounded-full"></span>Needs Attention
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<!-- Chart.js Setup -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Pillar Performance Bar Chart
        const pillarCtx = document.getElementById('kpiPillarChart').getContext('2d');
        new Chart(pillarCtx, {
            type: 'bar',
            data: {
                labels: ['Quality', 'Delivery', 'Cost & Safety', 'People & Growth', 'Compliance'],
                datasets: [
                    {
                        label: 'Target (%)',
                        data: [95, 90, 85, 90, 100],
                        backgroundColor: '#e2e8f0',
                        borderRadius: 6
                    },
                    {
                        label: 'Actual (%)',
                        data: [96.5, 88.0, 87.2, 92.0, 98.0],
                        backgroundColor: '#2563eb',
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: { callback: value => value + '%' }
                    }
                }
            }
        });

        // Status Distribution Pie Chart
        const pieCtx = document.getElementById('kpiStatusPieChart').getContext('2d');
        new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: ['Met Target', 'Warning', 'Off Target'],
                datasets: [{
                    data: [19, 3, 2],
                    backgroundColor: ['#10b981', '#f59e0b', '#f43f5e'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                cutout: '70%'
            }
        });
    });
</script>
@endsection
