@extends('layouts.app')

@section('title', 'Genba MNG Dashboard')

@section('content')
@include('layouts.sidebar')
@include('components.toast')

<!-- Main Content -->
<div class="lg:ml-20 min-h-screen flex flex-col bg-slate-50">
    @include('layouts.header')

    <!-- Page Content -->
    <main class="flex-1 p-4 lg:p-6">
        <!-- Page Title -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-bold text-slate-800">Genba Management Dashboard</h1>
                <p class="text-xs md:text-sm text-slate-500 mt-1">Monitor Genba Management audit findings and performance in real-time.</p>
            </div>
            <div class="flex-shrink-0 flex items-center gap-3">
                <button type="button" id="btnExport" onclick="exportToExcel()" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-none shadow-sm transition-colors disabled:opacity-50">
                    <i id="exportIcon" class="fa-solid fa-file-excel text-base"></i>
                    <span id="exportText">Export to Excel</span>
                </button>
            </div>
        </div>


        <div class="bg-white p-5 border border-gray-200 rounded-none mb-8 lg:overflow-x-hidden">
            <div class="grid grid-cols-1 xl:grid-cols-5 gap-8">
                <!-- Left Column: Chart & Table (80%) -->
                <div class="xl:col-span-4 border-b border-gray-100 pb-8 xl:pb-0 xl:border-b-0 xl:border-r pr-0 xl:pr-8">
                    <div class="flex items-center justify-between gap-4 mb-6">
                        <div>
                            <h3 class="text-base sm:text-lg font-bold text-slate-800">Department Performance</h3>
                            <p class="text-[10px] sm:text-sm text-slate-500">Findings status per department</p>
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            <input type="month" id="chartFilterDate" value="{{ date('Y-m') }}"
                                class="w-[95px] sm:w-auto px-2 py-1.5 sm:px-4 sm:py-2 border border-slate-300 rounded-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-xs sm:text-sm outline-none bg-slate-50">
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
                    <div class="relative h-[390px] w-full">
                        <canvas id="deptChart"></canvas>
                    </div>
                </div>

                <!-- Right Column: Findings Overview (20%) -->
                <div class="xl:col-span-1 pt-8 xl:pt-0">
                    <h3 class="text-lg font-bold text-slate-800 mb-6">Overview</h3>
                    <div class="relative h-64 w-full flex justify-center mb-6">
                        <canvas id="statsPieChart"></canvas>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-sm text-slate-600">
                        <div class="flex items-center justify-between p-3 rounded-none bg-amber-50/50 border border-amber-100">
                            <div class="flex items-center gap-3">
                                <span class="w-3 h-3 rounded-none bg-[#FEB019] -amber-200"></span>
                                <span class="font-semibold text-slate-700 text-xs">Open</span>
                            </div>
                            <span id="val_findingsOpen" class="font-bold text-slate-800 text-xs">...</span>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-none bg-blue-50/50 border border-blue-100">
                            <div class="flex items-center gap-3">
                                <span class="w-3 h-3 rounded-none bg-[#008FFB] -blue-200"></span>
                                <span class="font-semibold text-slate-700 text-xs text-nowrap">Need Verif</span>
                            </div>
                            <span id="val_needApprove" class="font-bold text-slate-800 text-xs">...</span>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-none bg-green-50/50 border border-green-100">
                            <div class="flex items-center gap-3">
                                <span class="w-3 h-3 rounded-none bg-[#00E396] -green-200"></span>
                                <span class="font-semibold text-slate-700 text-xs">Closed</span>
                            </div>
                            <span id="val_findingsClose" class="font-bold text-slate-800 text-xs">...</span>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-none bg-red-50/50 border border-red-100">
                            <div class="flex items-center gap-3">
                                <span class="w-3 h-3 rounded-none bg-[#FF4560] -red-200"></span>
                                <span class="font-semibold text-slate-700 text-xs">Overdue</span>
                            </div>
                            <span id="val_dueDateCount" class="font-bold text-slate-800 text-xs">...</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Full Width Findings Table -->
            <div class="mt-8 border-t border-slate-200 pt-8">
                <div class="grid grid-cols-2 lg:flex lg:flex-row lg:flex-wrap lg:items-center gap-3 mb-5">
                    <!-- Search -->
                    <div class="col-span-2 lg:col-span-auto lg:flex-1 lg:min-w-[200px]">
                        <div class="relative">
                            <input type="text" id="searchInput" placeholder="Search findings..."
                                class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                            <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        </div>
                    </div>

                    <!-- Date From -->
                    <div class="col-span-1 lg:col-span-auto w-full lg:w-auto">
                        <div class="date-input-container w-full lg:w-auto">
                            <input type="date" id="dateFrom" oninput="this.setAttribute('data-has-value', this.value ? 'true' : '')" onchange="this.setAttribute('data-has-value', this.value ? 'true' : '')" onfocus="try { this.showPicker(); } catch(e) {}" onclick="try { this.showPicker(); } catch(e) {}" onkeydown="return false;"
                                class="w-full lg:w-[150px] pl-4 pr-10 py-2 border border-slate-300 rounded-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
                            <span class="placeholder-overlay absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none">dd/mm/yyyy</span>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                <i class="fa-regular fa-calendar text-sm"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Date To -->
                    <div class="col-span-1 lg:col-span-auto w-full lg:w-auto">
                        <div class="date-input-container w-full lg:w-auto">
                            <input type="date" id="dateTo" oninput="this.setAttribute('data-has-value', this.value ? 'true' : '')" onchange="this.setAttribute('data-has-value', this.value ? 'true' : '')" onfocus="try { this.showPicker(); } catch(e) {}" onclick="try { this.showPicker(); } catch(e) {}" onkeydown="return false;"
                                class="w-full lg:w-[150px] pl-4 pr-10 py-2 border border-slate-300 rounded-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
                            <span class="placeholder-overlay absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none">dd/mm/yyyy</span>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                <i class="fa-regular fa-calendar text-sm"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Department Filter -->
                    <div class="col-span-1 lg:col-span-auto w-full lg:w-auto min-w-0 lg:min-w-[200px]">
                        <x-searchable-select
                            name="dept"
                            id="deptFilter"
                            label="Department"
                            :initialOptions="collect($departments)->map(fn($d) => ['id' => $d, 'name' => $d])->values()->toArray()"
                            valueField="name"
                            updateEvent="updateDeptFilter"
                            hideLabel="true" />
                    </div>

                    <!-- Detail Area Filter -->
                    <div class="col-span-1 lg:col-span-auto w-full lg:w-auto min-w-0 lg:min-w-[200px]">
                        <x-searchable-select
                            name="detail_area"
                            id="detailAreaFilter"
                            label="Detail Area"
                            :initialOptions="$detail_areas"
                            valueField="id"
                            updateEvent="updateDetailAreaFilter"
                            hideLabel="true"
                            placeholder="Select Detail Area..." />
                    </div>

                    <!-- Status Filter -->
                    <div class="col-span-1 lg:col-span-auto w-full lg:w-auto min-w-0 lg:min-w-[160px]">
                        <x-searchable-select
                            name="status"
                            id="statusFilter"
                            label="Status"
                            :initialOptions="[
                                ['id' => 'OPEN', 'name' => 'Open'],
                                ['id' => 'NEED_VERIF', 'name' => 'Need Verif'],
                                ['id' => 'CLOSE', 'name' => 'Close'],
                                ['id' => 'OVERDUE', 'name' => 'Overdue']
                            ]"
                            valueField="id"
                            updateEvent="updateStatusFilter"
                            hideLabel="true"
                            placeholder="Select Status..." />
                    </div>

                    <!-- Reset Button -->
                    <button type="button" id="btnReset"
                        class="col-span-1 lg:col-span-auto w-full lg:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 bg-slate-200 text-slate-700 rounded-none hover:bg-slate-300 text-sm font-base transition-colors">
                        <i class="fa-solid fa-rotate-right text-sm"></i>
                        Reset
                    </button>
                </div>
                <div>
                    <table id="findingsTable" class="qms-table w-full min-w-[1200px]">
                    <thead>
                        <tr>
                            <th class="w-[4%] text-center">No</th>
                            <th class="w-[10%]">DocNum</th>
                            <th class="w-[7%]">Picture</th>
                            <th class="w-[12%]">Genba Date</th>
                            <th class="w-[15%]">Area Checked</th>
                            <th class="w-[10%]">Dept</th>
                            <th class="w-[12%]">Auditor</th>
                            <th class="w-[22%]">
                                <div class="flex flex-col items-center gap-1.5">
                                    <span>Status</span>
                                    <div class="flex items-center gap-4 text-[10px] font-bold text-slate-400 tracking-wider leading-none normal-case">
                                        <span>Action</span>
                                        <span class="w-0.5 h-0.5 bg-slate-300 rounded-none shrink-0"></span>
                                        <span>Evidence</span>
                                        <span class="w-0.5 h-0.5 bg-slate-300 rounded-none shrink-0"></span>
                                        <span>Close</span>
                                    </div>
                                </div>
                            </th>
                            <th class="w-[8%]">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                    </tbody>
                    </table>
                </div>
            </div>
            <!-- Data Count Component -->
            <x-data-table tableId="findingsTable" />



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
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    let statsPieChart = null;

    function loadDataCards(yearMonth) {
        $.ajax({
            url: "{{ route('dashboard.data_cards') }}",
            data: {
                yearMonth: yearMonth
            },
            type: "GET",
            dataType: "json",
            success: function(response) {
                // Update text values
                $('#val_findingsOpen').text(new Intl.NumberFormat().format(response.findingsOpen));
                $('#val_needApprove').text(new Intl.NumberFormat().format(response.needApprove));
                $('#val_dueDateCount').text(new Intl.NumberFormat().format(response.dueDateCount));
                $('#val_findingsClose').text(new Intl.NumberFormat().format(response.findingsClose));

                const pieData = [
                    response.findingsOpen,
                    response.needApprove,
                    response.findingsClose,
                    response.dueDateCount
                ];

                if (statsPieChart) {
                    statsPieChart.data.datasets[0].data = pieData;
                    statsPieChart.update();
                } else {
                    const ctx = document.getElementById('statsPieChart').getContext('2d');
                    statsPieChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Open', 'Need Verif', 'Closed', 'Overdue'],
                            datasets: [{
                                data: pieData,
                                backgroundColor: [
                                    '#FEB019',
                                    '#008FFB',
                                    '#00E396',
                                    '#FF4560'
                                ],
                                borderWidth: 0,
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '70%',
                            animations: {
                                animateScale: {
                                    type: 'number',
                                    easing: 'easeOutQuart',
                                    duration: 2000,
                                    delay: 500,
                                    from: 0,
                                    to: 1,
                                    loop: false
                                },
                                animateRotate: {
                                    type: 'number',
                                    easing: 'easeOutQuart',
                                    duration: 2000,
                                    delay: 500,
                                    from: 0,
                                    to: 360, // Full rotation
                                    loop: false
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false // Use custom legend below
                                }
                            }
                        }
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error(error);
                $('#val_findingsOpen').text('Error');
                $('#val_needApprove').text('Error');
                $('#val_dueDateCount').text('Error');
                $('#val_findingsClose').text('Error');
            }
        });
    }

    // --- Department Chart Logic ---
    let deptChart = null;
    let table = null; // Global table variable
    let currentStatusFilter = ''; // Initial status filter

    // Pagination state
    let rawChartData = null;
    let currentChartPage = 1;
    let chartPageSize = 5;
    let isMobileMode = null;

    function loadDeptChart(yearMonth) {
        $.ajax({
            url: "{{ route('dashboard.chart_data', ':yearMonth') }}".replace(':yearMonth', yearMonth),
            type: "GET",
            dataType: "json",
            success: function(response) {
                rawChartData = response;
                currentChartPage = 1;
                renderDeptChart();
            },
            error: function(xhr) {
                console.error("Failed to load chart data:", xhr);
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
        let openData = rawChartData.data_total_open;
        let needApproveData = rawChartData.data_total_need_approve;
        let closeData = rawChartData.data_total_close;
        let overdueData = rawChartData.data_total_overdue;

        if (isMobile) {
            // Zip and sort by overdue descending, then open descending
            let zipped = [];
            for (let i = 0; i < labels.length; i++) {
                zipped.push({
                    name: labels[i],
                    open: openData[i] || 0,
                    needApprove: needApproveData[i] || 0,
                    close: closeData[i] || 0,
                    overdue: overdueData[i] || 0
                });
            }

            zipped.sort((a, b) => {
                if (b.overdue !== a.overdue) {
                    return b.overdue - a.overdue;
                }
                return b.open - a.open;
            });

            labels = zipped.map(item => item.name);
            openData = zipped.map(item => item.open);
            needApproveData = zipped.map(item => item.needApprove);
            closeData = zipped.map(item => item.close);
            overdueData = zipped.map(item => item.overdue);

            const totalItems = labels.length;
            const totalPages = Math.ceil(totalItems / chartPageSize) || 1;
            
            // Boundary checks
            if (currentChartPage < 1) currentChartPage = 1;
            if (currentChartPage > totalPages) currentChartPage = totalPages;

            const startIndex = (currentChartPage - 1) * chartPageSize;
            const endIndex = startIndex + chartPageSize;

            labels = labels.slice(startIndex, endIndex);
            openData = openData.slice(startIndex, endIndex);
            needApproveData = needApproveData.slice(startIndex, endIndex);
            closeData = closeData.slice(startIndex, endIndex);
            overdueData = overdueData.slice(startIndex, endIndex);

            $('#chartPageIndicator').text(currentChartPage + '/' + totalPages);
            $('#btnChartPrev').prop('disabled', currentChartPage === 1);
            $('#btnChartNext').prop('disabled', currentChartPage === totalPages);
            $('#chartPagination').removeClass('hidden').addClass('flex');
        } else {
            $('#chartPagination').removeClass('flex').addClass('hidden');
        }

        // Calculate max value for y-axis scaling
        const allValues = [
            ...openData,
            ...closeData,
            ...overdueData,
            ...needApproveData
        ];
        const maxValue = Math.max(...allValues, 0);
        const suggestedMax = maxValue + 1;

        let delayed;

        deptChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                        label: 'Open',
                        data: openData,
                        backgroundColor: '#f59e0b', // amber-500 (Yellow)
                    },
                    {
                        label: 'Need Verif',
                        data: needApproveData,
                        backgroundColor: '#008FFB', // blue-500 (matching pie)
                    },
                    {
                        label: 'Close',
                        data: closeData,
                        backgroundColor: '#22c55e', // green-500
                    },
                    {
                        label: 'Overdue',
                        data: overdueData,
                        backgroundColor: '#ef4444', // red-500
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
                onClick: (e) => {
                    const points = deptChart.getElementsAtEventForMode(e, 'nearest', {
                        intersect: true
                    }, true);

                    if (points.length) {
                        const firstPoint = points[0];
                        const label = deptChart.data.labels[firstPoint.index];
                        const datasetLabel = deptChart.data.datasets[firstPoint.datasetIndex].label;

                        // 1. Update Department Filter
                        window.dispatchEvent(new CustomEvent('updateDeptFilter', {
                            detail: {
                                id: label,
                                name: label
                            }
                        }));

                        // 2. Set Status Filter
                        // Map display label to backend code
                        let statusCode = '';
                        if (datasetLabel === 'Open') statusCode = 'OPEN';
                        else if (datasetLabel === 'Need Verif') statusCode = 'NEED_VERIF';
                        else if (datasetLabel === 'Close') statusCode = 'CLOSE';
                        else if (datasetLabel === 'Overdue') statusCode = 'OVERDUE';

                        currentStatusFilter = statusCode;

                        // 3. Update Status Dropdown UI
                        window.dispatchEvent(new CustomEvent('updateStatusFilter', {
                            detail: {
                                id: statusCode,
                                name: datasetLabel
                            }
                        }));

                        // 4. Reload Table
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
                        max: suggestedMax, // Dynamic max value
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

        // Mark initial animation as done
        setTimeout(() => {
            delayed = true;
        }, 1500);
    }

    // Initialize Chart
    $(document).ready(function() {
        const initialDate = $('#chartFilterDate').val();
        loadDeptChart(initialDate);
        loadDataCards(initialDate);

        $('#chartFilterDate').change(function() {
            loadDeptChart($(this).val());
            loadDataCards($(this).val());
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

        // Handle resize
        $(window).resize(function() {
            const currentMobile = window.innerWidth < 1280;
            if (currentMobile !== isMobileMode) {
                currentChartPage = 1;
                renderDeptChart();
            }
        });
    });



    $(document).ready(function() {
        table = $('#findingsTable').DataTable({
            dom: '<"overflow-x-auto"t>ip',
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('dashboard.table') }}", // Uses DashboardController table method (no delete button)
                type: 'POST',
                data: function(d) {
                    d._token = "{{ csrf_token() }}";
                    d.front_table_search = $('#searchInput').val();
                    d.date_from = $('#dateFrom').val();
                    d.date_to = $('#dateTo').val();
                    d.dept = $('#deptFilter').val();
                    d.detail_area = $('#detailAreaFilter').val();
                    d.status = currentStatusFilter;
                }
            },
            columns: [{
                    data: 'no',
                    orderable: false,
                    className: 'text-center font-base text-slate-700',
                    render: function(data, type, row) {
                        return data;
                    }
                },
                {
                    data: 'DocNum',
                    className: 'font-base text-slate-900',
                    render: function(data, type, row) {
                        return '<span class="inline-flex items-center rounded-none text-sm font-base text-slate-800 font-mono">' + data + '</span>';
                    }
                },
                {
                    data: 'path',
                    orderable: false,
                    className: 'text-left',
                    render: function(data, type, row) {
                       
                        const findings = encodeURIComponent(row.findings || '');
                        const execComment = encodeURIComponent(row.execution_comment || '');
                        const pathAfter = (row.execution_path || '');

                        return `<button class="w-9 h-9 flex items-center justify-center text-blue-600 border border-blue-200 bg-blue-50 hover:bg-blue-100 rounded-none transition-colors" 
                                onclick="viewGenbaImages('${data}', '${pathAfter}', '${findings}', '${execComment}')" title="View Image">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                </button>`;
                    }
                },
                {
                    data: 'date',
                    className: 'text-slate-700',
                    render: function(data, type, row) {
                        return '<span class="text-sm">' + data + '</span>';
                    }
                },
                {
                    data: 'area_checked',
                    className: 'text-slate-700',
                    render: function(data, type, row) {
                        return '<span class="text-sm">' + data + '</span>';
                    }
                },
                {
                    data: 'dept',
                    className: '',
                    render: function(data, type, row) {
                        return '<div class="text-sm text-slate-600">' + (data || '') + '</div>';
                    }
                },
                {
                    data: 'auditor',
                    className: 'text-slate-700',
                    render: function(data, type, row) {
                        return data || '';
                    }
                },
                {
                    data: 'status',
                    orderable: false,
                    className: 'text-left',
                },
                {
                    data: 'action',
                    orderable: false,
                    className: 'text-left',
                    render: function(data, type, row) {
                        return '<div class="flex items-center gap-2">' + data + '</div>';
                    }
                }
            ],
            order: [
                [3, 'desc']
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

        // Auto-filter on change
        $('#dateFrom, #dateTo, #deptFilter, #statusFilter, #detailAreaFilter').on('change', function() {
            if (this.id === 'statusFilter') {
                currentStatusFilter = $(this).val();
            }
            table.ajax.reload();
        });

        // Reset button
        $('#btnReset').click(function() {
            $('#searchInput').val('');
            $('#dateFrom').val('').removeAttr('data-has-value');
            $('#dateTo').val('').removeAttr('data-has-value');
            $('#deptFilter').val('');
            $('#statusFilter').val('');
            $('#detailAreaFilter').val('');
            currentStatusFilter = '';
            // Reset searchable select UI
            window.dispatchEvent(new CustomEvent('updateDeptFilter', {
                detail: {
                    id: '',
                    name: ''
                }
            }));
            window.dispatchEvent(new CustomEvent('updateStatusFilter', {
                detail: {
                    id: '',
                    name: ''
                }
            }));
            window.dispatchEvent(new CustomEvent('updateDetailAreaFilter', {
                detail: {
                    id: '',
                    name: ''
                }
            }));
            table.ajax.reload();
        });

        // Search on enter
        // Debounce function
        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                const context = this;
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(context, args), wait);
            };
        }

        // Auto-search with debounce
        $('#searchInput').on('keyup', debounce(function() {
            table.ajax.reload();
        }, 500));

        // Handle initial date values (if any)
        if ($('#dateFrom').val()) $('#dateFrom').attr('data-has-value', 'true');
        if ($('#dateTo').val()) $('#dateTo').attr('data-has-value', 'true');
    });

    function document_preview(id, no) {
        // Redirect to preview page
        window.location.href = "{{ route('genba.preview', '') }}/" + id;
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
        const btn = $('#btnExport');
        const icon = $('#exportIcon');
        const text = $('#exportText');

        // Set loading state
        btn.prop('disabled', true);
        icon.removeClass('fa-file-excel').addClass('fa-spinner fa-spin');
        text.text('Exporting...');

        const search = $('#searchInput').val() || '';
        const dateFrom = $('#dateFrom').val() || '';
        const dateTo = $('#dateTo').val() || '';
        const dept = $('#deptFilter').val() || '';
        const detail_area = $('#detailAreaFilter').val() || '';
        const status = currentStatusFilter || '';

        const url = new URL("{{ route('dashboard.export') }}");
        if (search) url.searchParams.append('search', search);
        if (dateFrom) url.searchParams.append('date_from', dateFrom);
        if (dateTo) url.searchParams.append('date_to', dateTo);
        if (dept) url.searchParams.append('dept', dept);
        if (detail_area) url.searchParams.append('detail_area', detail_area);
        if (status) url.searchParams.append('status', status);

        // Use a hidden iframe to trigger the download so that the main window's beforeunload event is not fired
        let iframe = document.getElementById('download-iframe');
        if (!iframe) {
            iframe = document.createElement('iframe');
            iframe.id = 'download-iframe';
            iframe.style.display = 'none';
            document.body.appendChild(iframe);
        }
        iframe.src = url.toString();

        // Restore button state after 3 seconds
        setTimeout(() => {
            btn.prop('disabled', false);
            icon.removeClass('fa-spinner fa-spin').addClass('fa-file-excel');
            text.text('Export to Excel');
        }, 3000);
    }
</script>
@endpush