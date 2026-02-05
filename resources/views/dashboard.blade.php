@extends('layouts.app')

@section('title', 'Dashboard - QMS')

@section('content')
@include('layouts.sidebar')
@include('components.toast')

<!-- Main Content -->
<div class="lg:ml-20 min-h-screen flex flex-col bg-slate-50">
    @include('layouts.header')

    <!-- Page Content -->
    <main class="flex-1 p-6">
        <!-- Page Title -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-slate-800">Dashboard</h1>
            <p class="text-slate-500 mt-1">Monitor audit findings and performance in real-time.</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            <!-- Card 0: All Findings -->
            <div class="bg-white rounded-2xl p-4 border border-gray-200">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-slate-500 text-sm font-medium">All Findings</p>
                        <h3 class="text-2xl font-bold text-slate-800" id="val_allFindings">...</h3>
                    </div>
                </div>
            </div>

            <!-- Card 1: Findings Open -->
            <div class="bg-white rounded-2xl p-4 border border-gray-200">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-slate-500 text-sm font-medium">Findings Open</p>
                        <h3 class="text-2xl font-bold text-slate-800" id="val_findingsOpen">...</h3>
                    </div>
                </div>
            </div>

            <!-- Card 2: Need Approve -->
            <div class="bg-white rounded-2xl p-4 border border-gray-200">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-slate-500 text-sm font-medium">Need Approve</p>
                        <h3 class="text-2xl font-bold text-slate-800" id="val_needApprove">...</h3>
                    </div>
                </div>
            </div>

            <!-- Card 3: Due Date (Overdue) -->
            <div class="bg-white rounded-2xl p-4 border border-gray-200">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-slate-500 text-sm font-medium">Due Date</p>
                        <h3 class="text-2xl font-bold text-slate-800" id="val_dueDateCount">...</h3>
                    </div>
                </div>
            </div>

            <!-- Card 4: Closed -->
            <div class="bg-white rounded-2xl p-4 border border-gray-200">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-slate-500 text-sm font-medium">Closed</p>
                        <h3 class="text-2xl font-bold text-slate-800" id="val_findingsClose">...</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Department Chart Section -->
        <div class="bg-white rounded-2xl p-5 border border-gray-200 mb-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-6">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Department Performance</h3>
                    <p class="text-slate-500 text-sm">Findings status per department</p>
                </div>
                <div>
                    <input type="month" id="chartFilterDate" value="{{ date('Y-m') }}"
                        class="px-4 py-2 border border-slate-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-slate-50">
                </div>
            </div>
            <div class="relative h-96 w-full">
                <canvas id="deptChart"></canvas>
            </div>
            <div class="overflow-x-auto p-6">
                <div class="flex flex-wrap items-center gap-3 mb-5 border-t border-slate-200 pt-5">
                    <!-- Search -->
                    <div class="flex-1 min-w-[200px]">
                        <div class="relative">
                            <input type="text" id="searchInput" placeholder="Search findings..."
                                class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                            <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        </div>
                    </div>

                    <!-- Date From -->
                    <div>
                        <input type="date" id="dateFrom"
                            class="px-4 py-2 border border-slate-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                    </div>

                    <!-- Date To -->
                    <div>
                        <input type="date" id="dateTo"
                            class="px-4 py-2 border border-slate-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                    </div>

                    <!-- Department Filer -->
                    <div class="min-w-[200px]">
                        <x-searchable-select
                            name="dept"
                            id="deptFilter"
                            label="Department"
                            :initialOptions="collect($departments)->map(fn($d) => ['id' => $d, 'name' => $d])->values()->toArray()"
                            valueField="name"
                            hideLabel="true" />
                    </div>





                    <!-- Reset Button -->
                    <button type="button" id="btnReset"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 text-sm font-base transition-colors">
                        <i class="fa-solid fa-rotate-right text-sm"></i>
                        Reset
                    </button>
                </div>
                <table id="findingsTable" class="qms-table w-full">
                    <thead>
                        <tr>
                            <th class="w-[3%] text-center">No</th>
                            <th class="w-[6%]">DocNum</th>
                            <th class="w-[5%]">Picture</th>
                            <th class="w-[12%]">Genba Date</th>
                            <th class="w-[12%]">Area Checked</th>
                            <th class="w-[8%]">Dept</th>
                            <th class="w-[15%]">Auditor</th>
                            <th class="w-[18%]">
                                <div class="flex flex-col items-center gap-1.5">
                                    <span>Status</span>
                                    <div class="flex items-center gap-4 text-[10px] font-bold text-slate-400 tracking-wider leading-none normal-case">
                                        <span>Action</span>
                                        <span class="w-0.5 h-0.5 bg-slate-300 rounded-full shrink-0"></span>
                                        <span>Evidence</span>
                                        <span class="w-0.5 h-0.5 bg-slate-300 rounded-full shrink-0"></span>
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
        <div class="bg-white rounded-2xl w-full max-w-5xl transform transition-all h-[90vh] flex flex-col">
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
                    <div class="bg-slate-50/50 rounded-2xl p-5 border border-slate-100 h-full flex flex-col">
                        <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-200/60">
                            <div>
                                <h4 class="text-sm font-bold text-slate-800">Before Condition</h4>
                            </div>
                        </div>

                        <!-- Findings Text -->
                        <div class="mb-4">
                            <div class="relative bg-white p-3.5 rounded-xl border border-slate-200">
                                <p id="modalCaptionBefore" class="text-slate-600 font-medium text-sm leading-relaxed"></p>
                            </div>
                        </div>

                        <!-- Images -->
                        <div id="imageContainerBefore" class="grid grid-cols-2 gap-3 content-start"></div>

                        <!-- Empty State -->
                        <div id="noImageBefore" class="hidden flex-1 flex flex-col items-center justify-center min-h-[140px] bg-slate-100/50 rounded-xl border border-dashed border-slate-300/60 mt-auto">
                            <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center mb-2 border border-slate-100">
                                <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-slate-400">No finding images</span>
                        </div>
                    </div>

                    <!-- After Section -->
                    <div class="bg-slate-50/50 rounded-2xl p-5 border border-slate-100 h-full flex flex-col">
                        <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-200/60">
                            <div>
                                <h4 class="text-sm font-bold text-slate-800">After Condition</h4>
                            </div>
                        </div>

                        <!-- Evidence Text -->
                        <div class="mb-4">
                            <div class="relative bg-white p-3.5 rounded-xl border border-slate-200">
                                <p id="modalCaptionAfter" class="text-slate-600 font-medium text-sm leading-relaxed"></p>
                            </div>
                        </div>

                        <!-- Images -->
                        <div id="imageContainerAfter" class="grid grid-cols-2 gap-3 content-start"></div>

                        <!-- Empty State -->
                        <div id="noImageAfter" class="hidden flex-1 flex flex-col items-center justify-center min-h-[140px] bg-slate-100/50 rounded-xl border border-dashed border-slate-300/60 mt-auto">
                            <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center mb-2 border border-slate-100">
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
                    class="px-6 py-2.5 bg-slate-100 text-slate-700 rounded-xl font-medium hover:bg-slate-200 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Fetch Data
    $(document).ready(function() {
        $.ajax({
            url: "{{ route('dashboard.data_cards') }}",
            type: "GET",
            dataType: "json",
            success: function(response) {
                $('#val_allFindings').text(new Intl.NumberFormat().format(response.allFindings));
                $('#val_findingsOpen').text(new Intl.NumberFormat().format(response.findingsOpen));
                $('#val_needApprove').text(new Intl.NumberFormat().format(response.needApprove));
                $('#val_dueDateCount').text(new Intl.NumberFormat().format(response.dueDateCount));
                $('#val_findingsClose').text(new Intl.NumberFormat().format(response.findingsClose));
            },
            error: function(xhr, status, error) {
                console.error(error);
                $('#val_allFindings').text('Error');
                $('#val_findingsOpen').text('Error');
                $('#val_needApprove').text('Error');
                $('#val_dueDateCount').text('Error');
                $('#val_findingsClose').text('Error');
            }
        });
    });

    // --- Department Chart Logic ---
    let deptChart = null;

    function loadDeptChart(yearMonth) {
        $.ajax({
            url: "{{ route('dashboard.chart_data', ':yearMonth') }}".replace(':yearMonth', yearMonth),
            type: "GET",
            dataType: "json",
            success: function(response) {
                const ctx = document.getElementById('deptChart').getContext('2d');

                if (deptChart) {
                    deptChart.destroy();
                }

                // Calculate max value for y-axis scaling
                const allValues = [
                    ...response.data_total_open,
                    ...response.data_total_close,
                    ...response.data_total_overdue
                ];
                const maxValue = Math.max(...allValues, 0);
                const suggestedMax = maxValue + 2;

                deptChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: response.data_name_dept,
                        datasets: [{
                                label: 'Open',
                                data: response.data_total_open,
                                backgroundColor: '#f59e0b', // amber-500 (Yellow)
                            },
                            {
                                label: 'Close',
                                data: response.data_total_close,
                                backgroundColor: '#22c55e', // green-500
                            },
                            {
                                label: 'Overdue',
                                data: response.data_total_overdue,
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
                        interaction: {
                            intersect: false,
                            mode: 'index',
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
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
            },
            error: function(xhr) {
                console.error("Failed to load chart data:", xhr);
            }
        });
    }

    // Initialize Chart
    $(document).ready(function() {
        const initialDate = $('#chartFilterDate').val();
        loadDeptChart(initialDate);

        $('#chartFilterDate').change(function() {
            loadDeptChart($(this).val());
        });
    });

    // Mobile Sidebar Toggle
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebarOverlay = document.getElementById('sidebar-overlay');

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
            sidebarOverlay.classList.toggle('hidden');
        });
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            sidebarOverlay.classList.add('hidden');
        });
    }

    // --- Findings Table Logic (Ported) ---

    $(document).ready(function() {
        var table = $('#findingsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('dashboard.table') }}", // Uses DashboardController@table (No Delete Button)
                type: 'POST',
                data: function(d) {
                    d._token = "{{ csrf_token() }}";
                    d.front_table_search = $('#searchInput').val();
                    d.date_from = $('#dateFrom').val();
                    d.date_to = $('#dateTo').val();
                    d.dept = $('#deptFilter').val();
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
                        return '<span class="inline-flex items-center rounded-md text-sm font-base text-slate-800 font-mono">' + data + '</span>';
                    }
                },
                {
                    data: 'path',
                    orderable: false,
                    className: 'text-left',
                    render: function(data, type, row) {
                        // Pass findings and execution details to viewGenbaImages
                        // Escape quotes for JS string passing
                        const findings = encodeURIComponent(row.findings || '');
                        const execComment = encodeURIComponent(row.execution_comment || '');
                        const pathAfter = (row.execution_path || '');

                        return `<button class="w-9 h-9 flex items-center justify-center text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors" 
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
                    render: function(data, type, row) {
                        return '<div class="text-sm text-slate-600">' + (data || '') + '</div>';
                    }
                },
                {
                    data: 'auditor',
                    className: 'text-slate-700',
                    render: function(data, type, row) {
                        return '<span class="text-sm">' + (data || '') + '</span>';
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
        $('#dateFrom, #dateTo, #deptFilter').on('change', function() {
            table.ajax.reload();
        });

        // Reset button
        $('#btnReset').click(function() {
            $('#searchInput').val('');
            $('#dateFrom').val('');
            $('#dateTo').val('');
            $('#deptFilter').val('');
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
                        <div class="relative group cursor-zoom-in overflow-hidden rounded-lg bg-slate-100 border border-slate-200 aspect-[4/3]">
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
                        <div class="relative group cursor-zoom-in overflow-hidden rounded-lg bg-slate-100 border border-slate-200 aspect-[4/3]">
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
</script>
@endpush