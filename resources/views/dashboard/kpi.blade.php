@extends('layouts.app')

@section('title', 'KPI Dashboard')

@section('content')
@include('layouts.sidebar')
@include('components.toast')

<!-- Main Content -->
<div class="lg:ml-20 min-h-screen flex flex-col bg-slate-50">
    @include('layouts.header')

    <!-- Page Content -->
    <main class="flex-1 px-4 py-2 lg:px-6 lg:py-3">
        <!-- Page Title -->
        <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-bold text-slate-800">Key Performance Indicators</h1>
                <p class="text-xs md:text-sm text-slate-500 mt-1">Monitor KPI achievements per department in real-time.</p>
            </div>
            <div class="flex-shrink-0 flex items-center justify-end w-full sm:w-auto self-end sm:self-auto gap-1.5 sm:gap-3">
                <button type="button" onclick="exportToExcel()" class="inline-flex items-center gap-1.5 px-2 py-1.5 sm:px-4 sm:py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] sm:text-sm font-semibold rounded-lg shadow-sm transition-colors">
                    <i class="fa-solid fa-download text-xs sm:text-sm"></i>
                    <span>Export to Excel</span>
                </button>
                <button type="button" onclick="exportToPdf()" class="inline-flex items-center gap-1.5 px-2 py-1.5 sm:px-4 sm:py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-[10px] sm:text-sm font-semibold rounded-lg shadow-sm transition-colors">
                    <i class="fa-solid fa-download text-xs sm:text-sm"></i>
                    <span>Export PDF</span>
                </button>
            </div>
        </div>

        <!-- Department Performance & Overview Grid -->
        <div class="bg-white p-5 border border-gray-200 rounded-none mb-8 lg:overflow-x-hidden">
            <div class="grid grid-cols-1 xl:grid-cols-5 gap-4">
                <!-- Left Column: Chart & Table (80%) -->
                <div class="xl:col-span-4 border-b border-gray-100 pb-8 xl:pb-0 xl:border-b-0 xl:border-r pr-0 xl:pr-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                        <div>
                            <h3 class="text-base sm:text-lg font-bold text-slate-800">KPI Performance per Department</h3>
                            <p class="text-[10px] sm:text-sm text-slate-500">KPI achievement status per department</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            @php
                                $currentYear = (int)date('Y');
                                $yearsOptions = [];
                                for ($y = $currentYear; $y >= $currentYear - 7; $y--) {
                                    $yearsOptions[] = ['id' => (string)$y, 'name' => (string)$y];
                                }
                                $auditTypesOptions = [
                                    ['id' => '', 'name' => 'All Audit Types'],
                                    ['id' => 'Product', 'name' => 'Audit Quality - Product'],
                                    ['id' => 'Process', 'name' => 'Audit Quality - Process'],
                                    ['id' => 'System', 'name' => 'Audit Quality - System'],
                                    ['id' => 'Environment', 'name' => 'Audit Lingkungan - Environment']
                                ];
                            @endphp
                            <div class="w-[200px] sm:w-[250px] text-left">
                                <x-searchable-select
                                    name="auditTypeFilter"
                                    id="auditTypeFilter"
                                    label="Internal Audit"
                                    hideLabel="true"
                                    updateEvent="update-audit-type-filter"
                                    changeEvent="audit-type-filter-changed"
                                    :initialOptions="$auditTypesOptions" />
                            </div>
                            <div class="w-[110px] sm:w-[130px] text-left">
                                <x-searchable-select
                                    name="chartFilterDate"
                                    id="chartFilterDate"
                                    label="Year"
                                    hideLabel="true"
                                    updateEvent="update-year-filter"
                                    changeEvent="year-filter-changed"
                                    :initialOptions="$yearsOptions" />
                            </div>
                            <!-- Chart Pagination (Visible on Mobile only) -->
                            <div id="chartPagination" class="hidden items-center gap-1.5">
                                <span id="chartPageIndicator" class="text-xs sm:text-sm text-slate-600 font-medium mr-1 text-nowrap">1/2</span>
                                <button type="button" id="btnChartPrev" class="w-8 h-8 flex items-center justify-center border border-slate-300 bg-white text-slate-600 hover:bg-slate-50 rounded-none disabled:opacity-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>
                                <button type="button" id="btnChartNext" class="w-8 h-8 flex items-center justify-center border border-slate-300 bg-white text-slate-600 hover:bg-slate-50 rounded-none disabled:opacity-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="relative h-[280px] w-full">
                        <canvas id="deptChart"></canvas>
                    </div>
                </div>

                <!-- Right Column: Findings Overview (20%) -->
                <div class="xl:col-span-1 pt-8 xl:pt-0">
                    <h3 class="text-lg font-bold text-slate-800 mb-3">Overview</h3>
                    <div class="relative h-52 w-full flex justify-center mb-3">
                        <canvas id="statsPieChart"></canvas>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-sm text-slate-600">
                        <div class="flex items-center justify-between p-3 rounded-none bg-green-50/50 border border-green-100">
                            <div class="flex items-center gap-3">
                                <span class="w-3 h-3 rounded-none bg-[#22c55e] -green-200"></span>
                                <span class="font-semibold text-slate-700 text-xs">Achieved</span>
                            </div>
                            <span id="val_ok" class="font-bold text-slate-800 text-xs">...</span>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-none bg-amber-50/50 border border-amber-100">
                            <div class="flex items-center gap-3">
                                <span class="w-3 h-3 rounded-none bg-[#FEB019] -amber-200"></span>
                                <span class="font-semibold text-slate-700 text-xs text-nowrap">Not Achieved</span>
                            </div>
                            <span id="val_minor" class="font-bold text-slate-800 text-xs">...</span>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-none bg-blue-50/50 border border-blue-100 col-span-2">
                            <div class="flex items-center gap-3">
                                <span class="w-3 h-3 rounded-none bg-[#008FFB] -blue-200"></span>
                                <span class="font-semibold text-slate-700 text-xs">Total KPI</span>
                            </div>
                            <span id="val_ofi" class="font-bold text-slate-800 text-xs">...</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Closed Findings Chart -->
            <div class="mt-4 border-t border-slate-200 pt-4">
                <div class="grid grid-cols-1 xl:grid-cols-5 gap-4">
                    <!-- Left Column: Closed Findings Chart (80%) -->
                    <div class="xl:col-span-4 border-b border-gray-100 pb-8 xl:pb-0 xl:border-b-0 xl:border-r pr-0 xl:pr-4">
                        <div class="flex items-center justify-between gap-4 mb-3">
                            <div>
                                <h3 class="text-base sm:text-lg font-bold text-slate-800">KPI Performance Per Pillar</h3>
                                <p class="text-[10px] sm:text-sm text-slate-500">Summary of KPI achievements per strategic pillar</p>
                            </div>
                            <div class="flex flex-col items-end gap-2">
                                <!-- Chart Pagination (Visible on Mobile only) -->
                                <div id="closedChartPagination" class="hidden items-center gap-1.5">
                                    <span id="closedChartPageIndicator" class="text-xs sm:text-sm text-slate-600 font-medium mr-1 text-nowrap">1/2</span>
                                    <button type="button" id="btnClosedChartPrev" class="w-8 h-8 flex items-center justify-center border border-slate-300 bg-white text-slate-600 hover:bg-slate-50 rounded-none disabled:opacity-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>
                                    <button type="button" id="btnClosedChartNext" class="w-8 h-8 flex items-center justify-center border border-slate-300 bg-white text-slate-600 hover:bg-slate-50 rounded-none disabled:opacity-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="relative h-[280px] w-full">
                            <canvas id="closedDeptChart"></canvas>
                        </div>
                    </div>

                    <!-- Right Column: Closed Findings Overview Pie Chart (20%) -->
                    <div class="xl:col-span-1 pt-8 xl:pt-0">
                        <h3 class="text-lg font-bold text-slate-800 mb-3">Overview</h3>
                        <div class="relative h-52 w-full flex justify-center mb-3">
                            <canvas id="closedStatsPieChart"></canvas>
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-sm text-slate-600">
                            <!-- Achieved -->
                            <div class="flex items-center justify-between p-3 rounded-none bg-green-50/50 border border-green-100">
                                <div class="flex items-center gap-3">
                                    <span class="w-3 h-3 rounded-none bg-[#22c55e] -green-200"></span>
                                    <span class="font-semibold text-slate-700 text-xs text-nowrap">Achieved</span>
                                </div>
                                <span id="val_minor_close" class="font-bold text-slate-800 text-xs">...</span>
                            </div>
                            <!-- Not Achieved -->
                            <div class="flex items-center justify-between p-3 rounded-none bg-amber-50/50 border border-amber-100">
                                <div class="flex items-center gap-3">
                                    <span class="w-3 h-3 rounded-none bg-[#FEB019] -amber-200"></span>
                                    <span class="font-semibold text-slate-700 text-xs text-nowrap">Not Achieved</span>
                                </div>
                                <span id="val_major_close" class="font-bold text-slate-800 text-xs">...</span>
                            </div>
                            <!-- Total KPI -->
                            <div class="flex items-center justify-between p-3 rounded-none bg-blue-50/50 border border-blue-100 col-span-2">
                                <div class="flex items-center gap-3">
                                    <span class="w-3 h-3 rounded-none bg-[#008FFB] -blue-200"></span>
                                    <span class="font-semibold text-slate-700 text-xs text-nowrap">Total KPI</span>
                                </div>
                                <span id="val_need_verif" class="font-bold text-slate-800 text-xs">...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>



    </main>
    @include('layouts.footer')
</div>

<!-- Mobile Sidebar Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/50 z-30 hidden lg:hidden"></div>



<!-- Image Preview Modal (Before/After) -->
<div id="imagePreviewModal" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-900/60 transition-opacity" onclick="closeImageModal()"></div>

    <!-- Modal -->
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-none w-full max-w-5xl transform transition-all h-[90vh] flex flex-col">
            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b border-slate-200">
                <h3 class="text-lg font-semibold text-slate-800">Findings & Evidence Preview</h3>
                <button type="button" onclick="closeImageModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div class="p-6 overflow-y-auto flex-1">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 h-full">
                    <!-- Before Section -->
                    <div class="bg-slate-50/50 rounded-none p-5 border border-slate-100 h-full flex flex-col">
                        <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-200/60">
                            <div>
                                <h4 class="text-sm font-bold text-slate-800">Before Condition</h4>
                            </div>
                        </div>

                        <!-- Findings Text -->
                        <div class="mb-4">
                            <div class="relative bg-white p-3.5 rounded-none border border-slate-200">
                                <p id="modalCaptionBefore" class="text-slate-600 font-medium text-sm leading-relaxed"></p>
                            </div>
                        </div>

                        <!-- Images -->
                        <div id="imageContainerBefore" class="grid grid-cols-2 gap-3 content-start"></div>

                        <!-- Empty State -->
                        <div id="noImageBefore" class="hidden flex-1 flex flex-col items-center justify-center min-h-[140px] bg-slate-100/50 rounded-none border border-dashed border-slate-300/60 mt-auto">
                            <div class="w-10 h-10 bg-white rounded-none flex items-center justify-center mb-2 border border-slate-100">
                                <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-slate-400">No finding images</span>
                        </div>
                    </div>

                    <!-- After Section -->
                    <div class="bg-slate-50/50 rounded-none p-5 border border-slate-100 h-full flex flex-col">
                        <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-200/60">
                            <div>
                                <h4 class="text-sm font-bold text-slate-800">After Condition</h4>
                            </div>
                        </div>

                        <!-- Evidence Text -->
                        <div class="mb-4">
                            <div class="relative bg-white p-3.5 rounded-none border border-slate-200">
                                <p id="modalCaptionAfter" class="text-slate-600 font-medium text-sm leading-relaxed"></p>
                            </div>
                        </div>

                        <!-- Images -->
                        <div id="imageContainerAfter" class="grid grid-cols-2 gap-3 content-start"></div>

                        <!-- Empty State -->
                        <div id="noImageAfter" class="hidden flex-1 flex flex-col items-center justify-center min-h-[140px] bg-slate-100/50 rounded-none border border-dashed border-slate-300/60 mt-auto">
                            <div class="w-10 h-10 bg-white rounded-none flex items-center justify-center mb-2 border border-slate-100">
                                <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-slate-400">No evidence images</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="flex justify-end p-4 border-t border-slate-200">
                <button type="button" onclick="closeImageModal()"
                    class="px-6 py-2.5 bg-slate-100 text-slate-700 rounded-none font-medium hover:bg-slate-200 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
<!-- Delete Confirmation Modal -->
<div id="deleteConfirmModal" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-900/60 transition-opacity" onclick="closeDeleteModal()"></div>

    <!-- Modal -->
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-md transform transition-all">
            <!-- Header -->
            <div class="p-6 text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                    <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Confirm Delete</h3>
                <p class="text-slate-500">Are you sure you want to delete this item? This action cannot be undone.</p>
            </div>

            <!-- Footer -->
            <div class="flex gap-3 p-6 pt-0">
                <button type="button" id="btnCancelDelete" onclick="closeDeleteModal()"
                    class="flex-1 px-4 py-3 bg-slate-100 text-slate-700 rounded-xl font-semibold hover:bg-slate-200 transition-colors">
                    Cancel
                </button>
                <button type="button" id="btnConfirmDelete" onclick="executeDelete()"
                    class="flex-1 px-4 py-3 bg-red-600 text-white rounded-xl font-semibold hover:bg-red-700 transition-colors">
                    Yes, Delete
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let statsPieChart = null;
    let closedStatsPieChart = null;
    let lastPieData = null;

    function renderPieChart() {
        if (!lastPieData) return;

        if (statsPieChart) {
            statsPieChart.destroy();
            statsPieChart = null;
        }

        const canvas = document.getElementById('statsPieChart');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        statsPieChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['OK', 'Minor', 'Major', 'OFI'],
                datasets: [{
                    data: lastPieData,
                    backgroundColor: ['#22c55e', '#FEB019', '#FF4560', '#008FFB'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                resizeDelay: 1500,
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart',
                    onComplete: function() {
                        this.options.resizeDelay = 0;
                        clearTimeout(this._resizeDelay);
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }

    function loadDataCards(yearMonth) {
        let year = yearMonth ? yearMonth.split('-')[0] : "{{ date('Y') }}";
        $.ajax({
            url: "{{ route('dashboard.kpi.summary_cards', ':year') }}".replace(':year', year),
            type: "GET",
            dataType: "json",
            success: function(response) {
                // Update text values untuk overview KPI
                $('#val_ok').text(new Intl.NumberFormat().format(response.achieved || 0));
                $('#val_minor').text(new Intl.NumberFormat().format(response.notAchieved || 0));
                $('#val_major').text(new Intl.NumberFormat().format(response.noData || 0));
                $('#val_ofi').text(new Intl.NumberFormat().format(response.totalKpi || 0));

                lastPieData = [
                    response.achieved || 0,
                    response.notAchieved || 0,
                    response.noData || 0,
                    response.totalKpi || 0
                ];

                if (deptChart) {
                    renderPieChart();
                }
            },
            error: function(xhr, status, error) {
                console.error(error);
                $('#val_ok').text('0');
                $('#val_minor').text('0');
                $('#val_major').text('0');
                $('#val_ofi').text('0');
            }
        });
    }

    // --- Department Chart Logic ---
    let deptChart = null;
    let table = null; 
    let selectedStatus = ''; 
    let selectedMonthYear = ''; 
    let selectedClause = '';
    let selectedAuditType = '';

    // Pagination state
    let rawChartData = null;
    let currentChartPage = 1;
    let chartPageSize = 5;
    let isMobileMode = null;

    function loadDeptChart(yearMonth) {
        let year = yearMonth ? yearMonth.split('-')[0] : "{{ date('Y') }}";
        $.ajax({
            url: "{{ route('dashboard.kpi.chart_data', ':year') }}".replace(':year', year),
            type: "GET",
            dataType: "json",
            success: function(response) {
                // Mengubah format response dari KPIController ke struktur chart bawaan template
                let depts = response.departments || [];
                let okList = [];
                let notAchievedList = [];
                let totalList = [];

                depts.forEach(function(d, idx) {
                    let totalAch = 0;
                    let totalNot = 0;
                    Object.keys(response.data || {}).forEach(function(pilarKey) {
                        let pData = response.data[pilarKey];
                        if (pData && pData.achieved && pData.achieved[idx] !== undefined) {
                            totalAch += pData.achieved[idx];
                        }
                        if (pData && pData.not_achieved && pData.not_achieved[idx] !== undefined) {
                            totalNot += pData.not_achieved[idx];
                        }
                    });
                    okList.push(totalAch);
                    notAchievedList.push(totalNot);
                    totalList.push(totalAch + totalNot);
                });

                rawChartData = {
                    data_name_dept: depts,
                    data_total_ok: okList,
                    data_total_minor: notAchievedList,
                    data_total_major: [],
                    data_total_ofi: totalList
                };
                currentChartPage = 1;
                renderDeptChart();
            },
            error: function(xhr) {
                console.error("Failed to load KPI chart data:", xhr);
            }
        });
    }

    function renderDeptChart() {
        if (!rawChartData) return;

        const ctx = document.getElementById('deptChart').getContext('2d');

        if (deptChart) {
            deptChart.destroy();
        }

        const isMobile = window.innerWidth < 1280;
        isMobileMode = isMobile;

        // Dynamic page size based on screen width
        const width = window.innerWidth;
        if (width < 380) chartPageSize = 3;
        else if (width < 480) chartPageSize = 4;
        else if (width < 640) chartPageSize = 5;
        else if (width < 768) chartPageSize = 6;
        else if (width < 1024) chartPageSize = 7;
        else chartPageSize = 9;

        let labels = rawChartData.data_name_dept;
        let okData = rawChartData.data_total_ok;
        let minorData = rawChartData.data_total_minor;
        let majorData = rawChartData.data_total_major;
        let ofiData = rawChartData.data_total_ofi;

        if (isMobile) {
            // Zip and sort by major descending, then minor descending
            let zipped = [];
            for (let i = 0; i < labels.length; i++) {
                zipped.push({
                    name: labels[i],
                    ok: okData[i] || 0,
                    minor: minorData[i] || 0,
                    major: majorData[i] || 0,
                    ofi: ofiData[i] || 0
                });
            }

            zipped.sort((a, b) => {
                if (b.major !== a.major) {
                    return b.major - a.major;
                }
                return b.minor - a.minor;
            });

            labels = zipped.map(item => item.name);
            okData = zipped.map(item => item.ok);
            minorData = zipped.map(item => item.minor);
            majorData = zipped.map(item => item.major);
            ofiData = zipped.map(item => item.ofi);

            const totalItems = labels.length;
            const totalPages = Math.ceil(totalItems / chartPageSize) || 1;
            
            // Boundary checks
            if (currentChartPage < 1) currentChartPage = 1;
            if (currentChartPage > totalPages) currentChartPage = totalPages;

            const startIndex = (currentChartPage - 1) * chartPageSize;
            const endIndex = startIndex + chartPageSize;

            labels = labels.slice(startIndex, endIndex);
            okData = okData.slice(startIndex, endIndex);
            minorData = minorData.slice(startIndex, endIndex);
            majorData = majorData.slice(startIndex, endIndex);
            ofiData = ofiData.slice(startIndex, endIndex);

            $('#chartPageIndicator').text(currentChartPage + '/' + totalPages);
            $('#btnChartPrev').prop('disabled', currentChartPage === 1);
            $('#btnChartNext').prop('disabled', currentChartPage === totalPages);
            $('#chartPagination').removeClass('hidden').addClass('flex');
        } else {
            $('#chartPagination').removeClass('flex').addClass('hidden');
        }

        // Calculate max value for y-axis scaling
        const allValues = [
            ...okData,
            ...minorData,
            ...majorData,
            ...ofiData
        ];
        const maxValue = Math.max(...allValues, 0);
        const suggestedMax = maxValue + 1;

        let delayed;

        deptChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                        label: 'Achieved',
                        data: okData,
                        backgroundColor: '#22c55e', // Green
                    },
                    {
                        label: 'Not Achieved',
                        data: minorData,
                        backgroundColor: '#FEB019', // Yellow
                    },
                    {
                        label: 'Total KPI',
                        data: ofiData,
                        backgroundColor: '#008FFB', // Blue
                    }
                ]
            },
            plugins: [{
                id: 'customLabels',
                afterDatasetsDraw: (chart) => {
                    const {
                        ctx
                    } = chart;
                    chart.data.datasets.forEach((dataset, i) => {
                        const meta = chart.getDatasetMeta(i);
                        if (!meta.hidden) {
                            meta.data.forEach((element, index) => {
                                const data = dataset.data[index];
                                if (data > 0) {
                                    ctx.fillStyle = '#334155'; // slate-700
                                    ctx.font = 'bold 11px sans-serif';
                                    ctx.textAlign = 'center';
                                    ctx.textBaseline = 'bottom';

                                    // Adjust position based on bar
                                    const xPos = element.x;
                                    const yPos = element.y - 3;

                                    ctx.fillText(data, xPos, yPos);
                                }
                            });
                        }
                    });
                }
            }],
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animations: {
                    y: {
                        duration: 1000,
                        easing: 'easeOutQuart',
                        delay: context => {
                            let delay = 0;
                            if (context.type === 'data' && context.mode === 'default' && !delayed) {
                                delay = context.dataIndex * 0 + 100; // Simultaneous 500ms delay on start
                            }
                            return delay;
                        },
                        from: (context) => {
                            if (context.type === 'data' && context.mode === 'default' && !delayed) {
                                const scale = context.chart.scales.y;
                                if (scale) return scale.getPixelForValue(0);
                            }
                            return undefined; // Default behavior for updates (hide/show)
                        },
                        loop: false
                    }
                },
                onClick: (e, elements, chart) => {
                    const points = chart.getElementsAtEventForMode(e, 'nearest', {
                        intersect: true
                    }, true);

                    if (points.length) {
                        const firstPoint = points[0];
                        const label = chart.data.labels[firstPoint.index];
                        const datasetLabel = chart.data.datasets[firstPoint.datasetIndex].label;

                        selectedStatus = ''; // Reset status
                        selectedClause = ''; // Reset clause
                        selectedMonthYear = $('#chartFilterDate').val(); // Filter by the chart's current month

                        // 1. Update Department Filter
                        window.dispatchEvent(new CustomEvent('updateDeptFilter', {
                            detail: {
                                id: label,
                                name: label
                            }
                        }));

                        // 2. Update Finding Category Filter
                        window.dispatchEvent(new CustomEvent('updateCategoryFilter', {
                            detail: {
                                id: datasetLabel,
                                name: datasetLabel
                            }
                        }));

                        // 3. Reload Table
                        if (table) {
                            table.ajax.reload();
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                scales: {
                    x: {
                        grid: {
                            display: true,
                            drawOnChartArea: true,
                            drawTicks: false,
                            color: 'rgba(203, 213, 225, 0.4)', // slate-300 with opacity
                        },
                        ticks: {
                            maxRotation: 0,
                            minRotation: 0,
                            autoSkip: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        suggestedMax: Math.max(suggestedMax, 5),
                        grid: {
                            borderDash: [2, 2]
                        },
                        ticks: {
                            precision: 0,
                            stepSize: 1,
                            maxTicksLimit: 6
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y;
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });

        // Mark initial animation as done
        setTimeout(() => {
            delayed = true;
        }, 1500);

        // Always create pie chart AFTER bar chart so grid layout is stable
        requestAnimationFrame(function() {
            renderPieChart();
        });
    }

    // --- Closed Department Chart Logic ---
    let closedDeptChart = null;
    let rawClosedChartData = null;
    let currentClosedChartPage = 1;
    let closedChartPageSize = 5;
 
    function loadClosedDeptChart(yearMonth) {
        let year = yearMonth ? yearMonth.split('-')[0] : "{{ date('Y') }}";
        $.ajax({
            url: "{{ route('dashboard.kpi.chart_data', ':year') }}".replace(':year', year),
            type: "GET",
            dataType: "json",
            success: function(response) {
                let pillars = response.pillars || [];
                let achList = [];
                let notAchList = [];

                pillars.forEach(function(pilarKey) {
                    let pData = response.data[pilarKey];
                    let sumAch = 0;
                    let sumNot = 0;
                    if (pData && pData.achieved) {
                        sumAch = pData.achieved.reduce((a, b) => a + b, 0);
                    }
                    if (pData && pData.not_achieved) {
                        sumNot = pData.not_achieved.reduce((a, b) => a + b, 0);
                    }
                    achList.push(sumAch);
                    notAchList.push(sumNot);
                });

                rawClosedChartData = {
                    data_name_dept: pillars,
                    data_total_minor: achList,
                    data_total_major: notAchList,
                    data_total_minor_overdue: [],
                    data_total_major_overdue: [],
                    data_total_need_verif: []
                };
                currentClosedChartPage = 1;
                renderClosedDeptChart();
            },
            error: function(xhr) {
                console.error("Failed to load closed pillar chart data:", xhr);
            }
        });
    }
 
    function renderClosedDeptChart() {
        if (!rawClosedChartData) return;
 
        const ctx = document.getElementById('closedDeptChart').getContext('2d');
 
        if (closedDeptChart) {
            closedDeptChart.destroy();
        }
 
        const isMobile = window.innerWidth < 1280;
 
        // Dynamic page size based on screen width
        const width = window.innerWidth;
        if (width < 380) closedChartPageSize = 3;
        else if (width < 480) closedChartPageSize = 4;
        else if (width < 640) closedChartPageSize = 5;
        else if (width < 768) closedChartPageSize = 6;
        else if (width < 1024) closedChartPageSize = 7;
        else closedChartPageSize = 9;
 
        let labels = rawClosedChartData.data_name_dept;
        let minorData = rawClosedChartData.data_total_minor;
        let majorData = rawClosedChartData.data_total_major;
        let minorOverdueData = rawClosedChartData.data_total_minor_overdue || [];
        let majorOverdueData = rawClosedChartData.data_total_major_overdue || [];
        let needVerifData = rawClosedChartData.data_total_need_verif || [];
 
        // Ke-6 Pilar selalu ditampilkan lengkap
        $('#closedChartPagination').removeClass('flex').addClass('hidden');
 
        const allValues = [
            ...minorData,
            ...majorData,
            ...minorOverdueData,
            ...majorOverdueData,
            ...needVerifData
        ];
        const maxValue = Math.max(...allValues, 0);
        const suggestedMax = maxValue + 1;
 
        let delayed;
 
        closedDeptChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Achieved',
                        data: minorData,
                        backgroundColor: '#22c55e', // Green
                    },
                    {
                        label: 'Not Achieved',
                        data: majorData,
                        backgroundColor: '#FEB019', // Yellow
                    }
                ]
            },
            plugins: [{
                id: 'customLabelsClosed',
                afterDatasetsDraw: (chart) => {
                    const { ctx } = chart;
                    chart.data.datasets.forEach((dataset, i) => {
                        const meta = chart.getDatasetMeta(i);
                        if (!meta.hidden) {
                            meta.data.forEach((element, index) => {
                                const data = dataset.data[index];
                                if (data > 0) {
                                    ctx.fillStyle = '#334155';
                                    ctx.font = 'bold 11px sans-serif';
                                    ctx.textAlign = 'center';
                                    ctx.textBaseline = 'bottom';
                                    const xPos = element.x;
                                    const yPos = element.y - 3;
                                    ctx.fillText(data, xPos, yPos);
                                }
                            });
                        }
                    });
                }
            }],
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animations: {
                    y: {
                        duration: 1000,
                        easing: 'easeOutQuart',
                        delay: context => {
                            let delay = 0;
                            if (context.type === 'data' && context.mode === 'default' && !delayed) {
                                delay = context.dataIndex * 0 + 100;
                            }
                            return delay;
                        },
                        from: (context) => {
                            if (context.type === 'data' && context.mode === 'default' && !delayed) {
                                const scale = context.chart.scales.y;
                                if (scale) return scale.getPixelForValue(0);
                            }
                            return undefined;
                        },
                        loop: false
                    }
                },
                onClick: (e, elements, chart) => {
                    const points = chart.getElementsAtEventForMode(e, 'nearest', {
                        intersect: true
                    }, true);
 
                    if (points.length) {
                        const firstPoint = points[0];
                        const label = chart.data.labels[firstPoint.index];
                        const datasetLabel = chart.data.datasets[firstPoint.datasetIndex].label;
 
                        // Determine status and category
                        let mappedCategory = '';
                        if (datasetLabel === 'Minor Close' || datasetLabel === 'Minor Overdue') {
                            mappedCategory = 'Minor';
                        } else if (datasetLabel === 'Major Close' || datasetLabel === 'Major Overdue') {
                            mappedCategory = 'Mayor';
                        } else if (datasetLabel === 'Need Verif') {
                            mappedCategory = ''; // Show both minor and major under Need Verif click
                        }
 
                        if (datasetLabel.includes('Close')) {
                            selectedStatus = 'Closed';
                        } else if (datasetLabel.includes('Overdue')) {
                            selectedStatus = 'Overdue';
                        } else if (datasetLabel === 'Need Verif') {
                            selectedStatus = 'Need Verif';
                        } else {
                            selectedStatus = '';
                        }
 
                        selectedMonthYear = $('#chartFilterDate').val(); // Filter by the chart's current month
                        selectedClause = ''; // Reset clause
 
                        window.dispatchEvent(new CustomEvent('updateDeptFilter', {
                            detail: {
                                id: label,
                                name: label
                            }
                        }));
 
                        window.dispatchEvent(new CustomEvent('updateCategoryFilter', {
                            detail: {
                                id: mappedCategory,
                                name: mappedCategory
                            }
                        }));
 
                        console.log("closedDeptChart Clicked - label:", label, "datasetLabel:", datasetLabel);
                        console.log("closedDeptChart Mapped values - selectedStatus:", selectedStatus, "mappedCategory:", mappedCategory);

                        if (table) {
                            table.ajax.reload();
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                scales: {
                    x: {
                        grid: {
                            display: true,
                            drawOnChartArea: true,
                            drawTicks: false,
                            color: 'rgba(203, 213, 225, 0.4)',
                        },
                        ticks: {
                            maxRotation: 0,
                            minRotation: 0,
                            autoSkip: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        max: suggestedMax,
                        grid: {
                            borderDash: [2, 2]
                        },
                        ticks: {
                            maxTicksLimit: 6
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y;
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
 
        setTimeout(() => {
            delayed = true;
        }, 1500);

        // Compute Overview Totals for Pie Chart
        const sumAchieved = rawClosedChartData.data_total_minor.reduce((a, b) => a + b, 0);
        const sumNotAchieved = rawClosedChartData.data_total_major.reduce((a, b) => a + b, 0);
        const sumTotal = sumAchieved + sumNotAchieved;

        $('#val_minor_close').text(new Intl.NumberFormat().format(sumAchieved));
        $('#val_major_close').text(new Intl.NumberFormat().format(sumNotAchieved));
        $('#val_need_verif').text(new Intl.NumberFormat().format(sumTotal));

        const closedPieData = [
            sumAchieved,
            sumNotAchieved,
            sumTotal
        ];

        if (closedStatsPieChart) {
            closedStatsPieChart.destroy();
        }

        setTimeout(function() {
            const canvas = document.getElementById('closedStatsPieChart');
            if (!canvas) return;
            const pieCtx = canvas.getContext('2d');
            closedStatsPieChart = new Chart(pieCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Achieved', 'Not Achieved', 'Total KPI'],
                    datasets: [{
                        data: [sumAchieved, sumNotAchieved, sumTotal],
                        backgroundColor: ['#22c55e', '#FEB019', '#008FFB'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    animations: {
                        circumference: {
                            duration: 1500,
                            easing: 'easeOutQuart',
                            from: 0
                        },
                        rotation: {
                            duration: 1500,
                            easing: 'easeOutQuart',
                            from: 0
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }, 100);
    }

    let clauseChart = null;
    let clauseStatsPieChart = null;
    let rawClauseChartData = null;
    let currentClauseChartPage = 1;
    let clauseChartPageSize = 9;

    function loadClauseChart(yearMonth) {
        $.ajax({
            url: "{{ route('dashboard.internal-audit.clause_chart_data', ':yearMonth') }}".replace(':yearMonth', yearMonth),
            type: "GET",
            data: {
                audit_type: selectedAuditType
            },
            dataType: "json",
            success: function(response) {
                rawClauseChartData = response;
                currentClauseChartPage = 1;
                renderClauseChart();
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }

    function renderClauseChart() {
        if (!rawClauseChartData) return;

        const ctx = document.getElementById('clauseChart').getContext('2d');

        if (clauseChart) {
            clauseChart.destroy();
        }

        const isMobile = window.innerWidth < 1280;

        // Display 5 items on mobile/tablet (to prevent squished bars), and 8 items on PC/laptop
        clauseChartPageSize = window.innerWidth < 1024 ? 5 : 8;

        let labels = rawClauseChartData.labels;
        let minorData = rawClauseChartData.minor;
        let majorData = rawClauseChartData.major;
        let ofiData = rawClauseChartData.ofi;

        let zipped = [];
        for (let i = 0; i < labels.length; i++) {
            zipped.push({
                name: labels[i],
                minor: minorData[i] || 0,
                major: majorData[i] || 0,
                ofi: ofiData[i] || 0
            });
        }

        zipped.sort((a, b) => {
            const bTotal = b.minor + b.major + b.ofi;
            const aTotal = a.minor + a.major + a.ofi;
            return bTotal - aTotal;
        });

        labels = zipped.map(item => item.name);
        minorData = zipped.map(item => item.minor);
        majorData = zipped.map(item => item.major);
        ofiData = zipped.map(item => item.ofi);

        const totalItems = labels.length;
        if (totalItems > clauseChartPageSize) {
            const totalPages = Math.ceil(totalItems / clauseChartPageSize) || 1;
            
            if (currentClauseChartPage < 1) currentClauseChartPage = 1;
            if (currentClauseChartPage > totalPages) currentClauseChartPage = totalPages;

            const startIndex = (currentClauseChartPage - 1) * clauseChartPageSize;
            const endIndex = startIndex + clauseChartPageSize;

            labels = labels.slice(startIndex, endIndex);
            minorData = minorData.slice(startIndex, endIndex);
            majorData = majorData.slice(startIndex, endIndex);
            ofiData = ofiData.slice(startIndex, endIndex);

            $('#clauseChartPageIndicator').text(currentClauseChartPage + '/' + totalPages);
            $('#btnClauseChartPrev').prop('disabled', currentClauseChartPage === 1);
            $('#btnClauseChartNext').prop('disabled', currentClauseChartPage === totalPages);
            $('#clauseChartPagination').removeClass('hidden').addClass('flex');
        } else {
            $('#clauseChartPagination').removeClass('flex').addClass('hidden');
        }

        const allValues = [
            ...minorData,
            ...majorData,
            ...ofiData
        ];
        const maxValue = Math.max(...allValues, 0);
        const suggestedMax = maxValue + 1;

        let delayed;

        clauseChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Minor',
                        data: minorData,
                        backgroundColor: '#FEB019',
                    },
                    {
                        label: 'Major',
                        data: majorData,
                        backgroundColor: '#FF4560',
                    },
                    {
                        label: 'OFI',
                        data: ofiData,
                        backgroundColor: '#008FFB',
                    }
                ]
            },
            plugins: [{
                id: 'customLabelsClause',
                afterDatasetsDraw: (chart) => {
                    const { ctx } = chart;
                    chart.data.datasets.forEach((dataset, i) => {
                        const meta = chart.getDatasetMeta(i);
                        if (!meta.hidden) {
                            meta.data.forEach((element, index) => {
                                const data = dataset.data[index];
                                if (data > 0) {
                                    ctx.fillStyle = '#334155';
                                    ctx.font = 'bold 11px sans-serif';
                                    ctx.textAlign = 'center';
                                    ctx.textBaseline = 'bottom';
                                    const xPos = element.x;
                                    const yPos = element.y - 3;
                                    ctx.fillText(data, xPos, yPos);
                                }
                            });
                        }
                    });
                }
            }],
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animations: {
                    y: {
                        duration: 1000,
                        easing: 'easeOutQuart',
                        delay: context => {
                            let delay = 0;
                            if (context.type === 'data' && context.mode === 'default' && !delayed) {
                                delay = context.dataIndex * 0 + 100;
                            }
                            return delay;
                        },
                        from: (context) => {
                            if (context.type === 'data' && context.mode === 'default' && !delayed) {
                                const scale = context.chart.scales.y;
                                if (scale) return scale.getPixelForValue(0);
                            }
                            return undefined;
                        },
                        loop: false
                    }
                },
                onClick: (e, elements, chart) => {
                    const points = chart.getElementsAtEventForMode(e, 'nearest', {
                        intersect: true
                    }, true);

                    if (points.length) {
                        const firstPoint = points[0];
                        const label = chart.data.labels[firstPoint.index];
                        const datasetLabel = chart.data.datasets[firstPoint.datasetIndex].label;

                        selectedStatus = ''; 
                        
                        let mappedCategory = datasetLabel;
                        if (datasetLabel === 'Major') {
                            mappedCategory = 'Mayor';
                        }

                        selectedMonthYear = $('#chartFilterDate').val();
                        selectedClause = label;

                        window.dispatchEvent(new CustomEvent('updateCategoryFilter', {
                            detail: {
                                id: mappedCategory,
                                name: mappedCategory
                            }
                        }));

                        window.dispatchEvent(new CustomEvent('updateDeptFilter', {
                            detail: {
                                id: '',
                                name: ''
                            }
                        }));

                        if (table) {
                            table.ajax.reload();
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                scales: {
                    x: {
                        grid: {
                            display: true,
                            drawOnChartArea: true,
                            drawTicks: false,
                            color: 'rgba(203, 213, 225, 0.4)',
                        },
                        ticks: {
                            maxRotation: 0,
                            minRotation: 0,
                            autoSkip: false,
                            callback: function(value, index, values) {
                                let label = this.getLabelForValue(value);
                                if (window.innerWidth < 1024) {
                                    return label ? label.split(' ')[0] : '';
                                }
                                if (label && label.length > 15) {
                                    return label.substring(0, 15) + '...';
                                }
                                return label;
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        max: suggestedMax,
                        grid: {
                            borderDash: [2, 2]
                        },
                        ticks: {
                            maxTicksLimit: 6
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y;
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });

        setTimeout(() => {
            delayed = true;
        }, 1500);

        // Compute Overview Totals for Clause Pie Chart
        const sumMinor = rawClauseChartData.minor.reduce((a, b) => a + b, 0);
        const sumMajor = rawClauseChartData.major.reduce((a, b) => a + b, 0);
        const sumOfi = rawClauseChartData.ofi.reduce((a, b) => a + b, 0);

        $('#val_clause_minor').text(new Intl.NumberFormat().format(sumMinor));
        $('#val_clause_major').text(new Intl.NumberFormat().format(sumMajor));
        $('#val_clause_ofi').text(new Intl.NumberFormat().format(sumOfi));

        const clausePieData = [sumMinor, sumMajor, sumOfi];

        if (clauseStatsPieChart) {
            clauseStatsPieChart.destroy();
        }

        setTimeout(function() {
            const canvas = document.getElementById('clauseStatsPieChart');
            if (!canvas) return;
            const pieCtx = canvas.getContext('2d');
            clauseStatsPieChart = new Chart(pieCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Minor', 'Major', 'OFI'],
                    datasets: [{
                        data: clausePieData,
                        backgroundColor: ['#FEB019', '#FF4560', '#008FFB'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    animations: {
                        circumference: {
                            duration: 1500,
                            easing: 'easeOutQuart',
                            from: 0
                        },
                        rotation: {
                            duration: 1500,
                            easing: 'easeOutQuart',
                            from: 0
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }, 100);
    }
 
    // Initialize Chart
    $(document).ready(function() {
        const initialDate = '{{ date("Y") }}';
        loadDeptChart(initialDate);
        loadClosedDeptChart(initialDate);
        loadClauseChart(initialDate);
        loadDataCards(initialDate);
 
        setTimeout(function() {
            window.dispatchEvent(new CustomEvent('update-year-filter', {
                detail: {
                    id: initialDate,
                    name: initialDate
                }
            }));
            window.dispatchEvent(new CustomEvent('update-audit-type-filter', {
                detail: {
                    id: '',
                    name: 'All Audit Types'
                }
            }));
        }, 100);

        window.addEventListener('year-filter-changed', function(e) {
            const val = e.detail.value;
            selectedMonthYear = val;
            loadDeptChart(val);
            loadClosedDeptChart(val);
            loadClauseChart(val);
            loadDataCards(val);
            if (table) {
                table.ajax.reload();
            }
        });

        window.addEventListener('audit-type-filter-changed', function(e) {
            const val = e.detail.value;
            selectedAuditType = val;
            const currentYear = selectedMonthYear || '{{ date("Y") }}';
            loadDeptChart(currentYear);
            loadClosedDeptChart(currentYear);
            loadClauseChart(currentYear);
            loadDataCards(currentYear);
            if (table) {
                table.ajax.reload();
            }
        });
 
        // Pagination buttons
        $('#btnChartPrev').click(function() {
            if (currentChartPage > 1) {
                currentChartPage--;
                renderDeptChart();
            }
        });
 
        $('#btnChartNext').click(function() {
            if (rawChartData) {
                const totalItems = rawChartData.data_name_dept.length;
                const totalPages = Math.ceil(totalItems / chartPageSize) || 1;
                if (currentChartPage < totalPages) {
                    currentChartPage++;
                    renderDeptChart();
                }
            }
        });
 
        // Closed Chart Pagination buttons
        $('#btnClosedChartPrev').click(function() {
            if (currentClosedChartPage > 1) {
                currentClosedChartPage--;
                renderClosedDeptChart();
            }
        });
 
        $('#btnClosedChartNext').click(function() {
            if (rawClosedChartData) {
                const totalItems = rawClosedChartData.data_name_dept.length;
                const totalPages = Math.ceil(totalItems / closedChartPageSize) || 1;
                if (currentClosedChartPage < totalPages) {
                    currentClosedChartPage++;
                    renderClosedDeptChart();
                }
            }
        });

        // Clause Chart Pagination buttons
        $('#btnClauseChartPrev').click(function() {
            if (currentClauseChartPage > 1) {
                currentClauseChartPage--;
                renderClauseChart();
            }
        });
 
        $('#btnClauseChartNext').click(function() {
            if (rawClauseChartData) {
                const totalItems = rawClauseChartData.labels.length;
                const totalPages = Math.ceil(totalItems / clauseChartPageSize) || 1;
                if (currentClauseChartPage < totalPages) {
                    currentClauseChartPage++;
                    renderClauseChart();
                }
            }
        });
 
        // Handle resize
        $(window).resize(function() {
            const currentMobile = window.innerWidth < 1280;
            if (currentMobile !== isMobileMode) {
                currentChartPage = 1;
                renderDeptChart();
                renderPieChart();
                currentClosedChartPage = 1;
                renderClosedDeptChart();
                currentClauseChartPage = 1;
                renderClauseChart();
            }
        });
    });

    $(document).ready(function() {
        table = $('#findingsTable').DataTable({
            dom: '<"overflow-x-auto"t>ip',
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('internal_audit.cars') }}",
                type: 'POST',
                data: function(d) {
                    d._token = "{{ csrf_token() }}";
                    d.search = { value: $('#searchInput').val() };
                    d.date_from = $('#dateFrom').val();
                    d.date_to = $('#dateTo').val();
                    d.dept = $('#deptFilter').val();
                    d.finding_category = $('#categoryFilter').val();
                    d.status = selectedStatus;
                    d.month_year = selectedMonthYear;
                    d.requirement_no = selectedClause;
                    d.audit_type = selectedAuditType;
                    d.is_dashboard = true;
                },
                error: function(xhr, error, code) {
                    console.error('DataTables AJAX error:', error, code);
                    console.error('Response:', xhr.responseText);
                }
            },
            columns: [{
                    data: 'no',
                    orderable: false,
                    className: 'text-center font-base text-slate-700'
                },
                {
                    data: 'req_number',
                    className: 'font-base text-slate-900'
                },
                {
                    data: 'audit_date',
                    className: 'text-slate-700'
                },
                {
                    data: 'department',
                    className: 'text-slate-700'
                },
                {
                    data: 'finding_category',
                    className: 'text-slate-700'
                },
                {
                    data: 'auditor',
                    className: 'text-slate-700',
                    render: function(data, type, row) {
                        return data || '';
                    }
                },
                {
                    data: 'auditee',
                    className: 'text-slate-700'
                },
                {
                    data: 'action',
                    orderable: false,
                    className: 'text-left'
                }
            ],
            order: [
                [1, 'desc']
            ],
            pageLength: 10,
            language: {
                emptyTable: '<div class="text-center py-8 text-slate-500">No data available</div>',
                info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                paginate: {
                    previous: '<i class="fa-solid fa-chevron-left"></i>',
                    next: '<i class="fa-solid fa-chevron-right"></i>'
                }
            }
        });

        // Show/hide page loader on DataTables AJAX
        table.on('preXhr.dt', function() {
            $('body').addClass('data-loading');
            $('#page-loader').removeClass('hidden');
        });

        table.on('xhr.dt', function() {
            $('body').removeClass('data-loading');
            $('#page-loader').addClass('hidden');
        });

        // Auto-filter on change
        $('#dateFrom, #dateTo, #deptFilter, #categoryFilter').on('change', function() {
            selectedStatus = '';
            selectedMonthYear = '';
            selectedClause = '';
            table.ajax.reload();
        });

        // Reset button
        $('#btnReset').click(function() {
            $('#searchInput').val('');
            $('#dateFrom').val('').removeAttr('data-has-value');
            $('#dateTo').val('').removeAttr('data-has-value');
            
            selectedStatus = '';
            selectedMonthYear = '';
            selectedClause = '';
            
            // Reset searchable-select components
            window.dispatchEvent(new CustomEvent('updateDeptFilter', { detail: '' }));
            window.dispatchEvent(new CustomEvent('updateCategoryFilter', { detail: '' }));
            
            table.ajax.reload();
        });

        if ($('#dateFrom').val()) $('#dateFrom').attr('data-has-value', 'true');
        if ($('#dateTo').val()) $('#dateTo').attr('data-has-value', 'true');

        // Search with debounce
        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                const context = this;
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(context, args), wait);
            };
        }

        $('#searchInput').on('keyup', debounce(function() {
            table.ajax.reload();
        }, 500));
    });

    // Delete confirmation variables
    var deleteTargetSysId = null;
    var deleteTargetNo = null;

    function f_genba_conform_delete(sysId, no) {
        deleteTargetSysId = sysId;
        deleteTargetNo = no;
        $('#deleteConfirmModal').removeClass('hidden');
    }

    function closeDeleteModal() {
        $('#deleteConfirmModal').addClass('hidden');
        deleteTargetSysId = null;
        deleteTargetNo = null;
    }

    function executeDelete() {
        if (!deleteTargetSysId) return;

        var sysId = deleteTargetSysId;
        var no = deleteTargetNo;

        // Show loader on button
        $('#icon_f_genba_conform_delete_' + no).addClass('hidden');
        $('#loader_f_genba_conform_delete_' + no).removeClass('hidden');

        closeDeleteModal();

        $.ajax({
            url: "{{ route('internal_audit.cars.delete') }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                sys_id: sysId
            },
            success: function(response) {
                $('#icon_f_genba_conform_delete_' + no).removeClass('hidden');
                $('#loader_f_genba_conform_delete_' + no).addClass('hidden');

                if (response.success) {
                    showToast('CAR Action Report deleted successfully.', 'success');
                    $('#findingsTable').DataTable().ajax.reload();
                } else {
                    showToast('Failed to delete CAR Action Report.', 'error');
                }
            },
            error: function() {
                $('#icon_f_genba_conform_delete_' + no).removeClass('hidden');
                $('#loader_f_genba_conform_delete_' + no).addClass('hidden');
                showToast('An error occurred.', 'error');
            }
        });
    }

    function document_preview(id, no) {
        window.location.href = "{{ route('internal_audit.action_report.preview', '') }}/" + id;
    }

    // Viewer instance
    var galleryViewer = null;

    const findingPhotoBaseUrl = "{{ asset('findings-photo') }}";
    const evidencePhotoBaseUrl = "{{ asset('evidence-photo') }}";

    function viewGenbaImages(pathBefore, pathAfter, captionBefore, captionAfter) {
        // Reset state
        $('#imageContainerBefore, #imageContainerAfter').empty();
        $('#noImageBefore, #noImageAfter').addClass('hidden');

        // Convert captions if needed (decodeURIComponent handles encoded strings from controller)
        $('#modalCaptionBefore').text(decodeURIComponent(captionBefore || ''));
        $('#modalCaptionAfter').text(decodeURIComponent(captionAfter || ''));

        // Logic to Populate BEFORE Images
        if (pathBefore && pathBefore.trim() !== '') {
            const paths = pathBefore.split(',');
            paths.forEach(imgName => {
                imgName = imgName.trim();
                if (imgName) {
                    const fullPath = findingPhotoBaseUrl + '/' + imgName;
                    const imgHtml = `
                        <div class="relative group cursor-zoom-in overflow-hidden rounded-none bg-slate-100 border border-slate-200 aspect-[4/3]">
                            <img src="${fullPath}" 
                                 class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" 
                                 alt="Before Image"
                                 onerror="this.parentElement.style.display='none'">
                        </div>
                     `;
                    $('#imageContainerBefore').append(imgHtml);
                }
            });
        } else {
            $('#noImageBefore').removeClass('hidden').addClass('flex');
        }

        // Logic to Populate AFTER Images
        if (pathAfter && pathAfter.trim() !== '') {
            const paths = pathAfter.split(',');
            paths.forEach(imgName => {
                imgName = imgName.trim();
                if (imgName) {
                    const fullPath = evidencePhotoBaseUrl + '/' + imgName;
                    const imgHtml = `
                        <div class="relative group cursor-zoom-in overflow-hidden rounded-none bg-slate-100 border border-slate-200 aspect-[4/3]">
                            <img src="${fullPath}" 
                                 class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" 
                                 alt="After Image"
                                 onerror="this.parentElement.style.display='none'">
                        </div>
                     `;
                    $('#imageContainerAfter').append(imgHtml);
                }
            });
        } else {
            $('#noImageAfter').removeClass('hidden').addClass('flex');
        }

        // Initialize Viewer
        if (galleryViewer) {
            galleryViewer.destroy();
        }

        // We can create a viewer for the whole modal content wrapper so it picks up all images
        var container = document.querySelector('#imagePreviewModal .p-6');

        // Check if Viewer is defined
        if (typeof Viewer !== 'undefined' && container) {
            galleryViewer = new Viewer(container, {
                toolbar: {
                    zoomIn: 1,
                    zoomOut: 1,
                    oneToOne: 1,
                    reset: 1,
                    prev: 1,
                    play: 1,
                    next: 1,
                    rotateLeft: 1,
                    rotateRight: 1,
                    flipHorizontal: 1,
                    flipVertical: 1,
                },
                title: false,
                transition: true,
            });
        }

        // Show modal
        $('#imagePreviewModal').removeClass('hidden');
    }

    // Keep existing viewImage for backward compatibility
    function viewImage(path) {
        // Call the new function with the path as 'pathBefore' (first arg)
        // and empty strings for the others.
        viewGenbaImages(path, '', '', '');
    }

    function closeImageModal() {
        $('#imagePreviewModal').addClass('hidden');
        $('#imageContainerBefore, #imageContainerAfter').empty();

        if (galleryViewer) {
            galleryViewer.destroy();
            galleryViewer = null;
        }
    }

    function exportToExcel() {
        const search = $('input[type="search"]').val() || $('#searchInput').val() || '';
        const dateFrom = $('#dateFrom').val() || '';
        const dateTo = $('#dateTo').val() || '';
        const dept = $('#deptFilter').val() || '';
        const category = $('#categoryFilter').val() || '';

        const url = new URL("{{ route('dashboard.internal-audit.export') }}");
        if (search) url.searchParams.append('search', search);
        if (dateFrom) url.searchParams.append('date_from', dateFrom);
        if (dateTo) url.searchParams.append('date_to', dateTo);
        if (dept) url.searchParams.append('dept', dept);
        if (category) url.searchParams.append('finding_category', category);

        // Use a hidden iframe to trigger the download so that the main window's beforeunload event is not fired
        let iframe = document.getElementById('download-iframe');
        if (!iframe) {
            iframe = document.createElement('iframe');
            iframe.id = 'download-iframe';
            iframe.style.display = 'none';
            document.body.appendChild(iframe);
        }
        iframe.src = url.toString();
    }

    function exportToPdf() {
        const search = $('input[type="search"]').val() || $('#searchInput').val() || '';
        const dateFrom = $('#dateFrom').val() || '';
        const dateTo = $('#dateTo').val() || '';
        const dept = $('#deptFilter').val() || '';
        const category = $('#categoryFilter').val() || '';

        const url = new URL("{{ route('dashboard.internal-audit.print') }}");
        if (search) url.searchParams.append('search', search);
        if (dateFrom) url.searchParams.append('date_from', dateFrom);
        if (dateTo) url.searchParams.append('date_to', dateTo);
        if (dept) url.searchParams.append('dept', dept);
        if (category) url.searchParams.append('finding_category', category);

        // Open in new tab so user can see preview and print to PDF
        window.open(url.toString(), '_blank');
    }
</script>
@endpush