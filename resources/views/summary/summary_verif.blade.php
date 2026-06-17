@extends('layouts.app')

@section('title', 'Verification Summary')

@section('content')
@include('layouts.sidebar')

<!-- Main Content -->
<div class="lg:ml-20 min-h-screen flex flex-col bg-slate-50">
    @include('layouts.header')

    <!-- Page Content -->
    <main class="flex-1 p-6">
        <!-- Page Title -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Findings Summary</h1>
            <p class="text-slate-500 mt-1">Summary finding Genba before, after, and verification evidence</p>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-lg border border-slate-200">
            <!-- Filter Section -->
            <div class="p-6 border-b border-slate-200 bg-slate-50/50">
                <div class="grid grid-cols-2 lg:flex lg:flex-row lg:flex-wrap lg:items-center gap-3">
                    <!-- Search -->
                    <div class="col-span-2 lg:col-span-auto lg:flex-1 lg:min-w-[200px]">
                        <div class="relative">
                            <input type="text" id="searchInput" placeholder="Search findings..."
                                class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                            <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        </div>
                    </div>

                    <!-- Date From -->
                    <div class="col-span-1 lg:col-span-auto w-full lg:w-auto">
                        <input type="date" id="dateFrom"
                            class="w-full lg:w-auto px-4 py-2 border border-slate-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                    </div>

                    <!-- Date To -->
                    <div class="col-span-1 lg:col-span-auto w-full lg:w-auto">
                        <input type="date" id="dateTo"
                            class="w-full lg:w-auto px-4 py-2 border border-slate-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                    </div>

                    <!-- Department Filter -->
                    <div class="col-span-2 sm:col-span-1 lg:col-span-auto w-full lg:w-auto min-w-0 lg:min-w-[200px]">
                        <x-searchable-select
                            name="dept"
                            id="deptFilter"
                            label="Department"
                            :initialOptions="collect($departments)->map(fn($d) => ['id' => $d, 'name' => $d])->values()->toArray()"
                            valueField="name"
                            hideLabel="true" />
                    </div>

                    <!-- Reset Button -->
                    <div class="col-span-2 sm:col-span-1 lg:col-span-auto">
                        <button type="button" id="btnReset"
                            class="w-full lg:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 text-sm font-base transition-colors">
                            <i class="fa-solid fa-rotate-right text-sm"></i>
                            Reset
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table Section -->
            <div class="overflow-x-auto p-6">
                <div id="summaryTableLoadingInfo" class="hidden mb-3 text-sm text-slate-500">
                    Data sedang dimuat...
                </div>
                <div id="summaryTableContent" class="opacity-0 pointer-events-none transition-opacity duration-200">
                    <table id="summaryVerificationTable" class="qms-table w-full min-w-[1500px]">
                        <thead>
                            <tr>
                                <th class="w-[4%] text-center">No</th>
                                <th class="w-[18%]">Findings</th>
                                <th class="w-[8%] text-center">Before</th>
                                <th class="w-[16%]">Execution Comment</th>
                                <th class="w-[16%]">Preventive Action</th>
                                <th class="w-[8%] text-center">After</th>
                                <th class="w-[10%] text-center">Verification</th>
                                <th class="w-[8%]">Dept</th>
                                <th class="w-[12%]">Auditor</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Data Count Component -->
            <x-data-table tableId="summaryVerificationTable" />
        </div>
    </main>
    @include('layouts.footer')
</div>

<!-- Mobile Sidebar Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/50 z-30 hidden lg:hidden"></div>

<!-- Image Preview Modal -->
<div id="summaryImagePreviewModal" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-900/60 transition-opacity" onclick="closeSummaryImageModal()"></div>

    <!-- Modal -->
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-5xl transform transition-all h-[90vh] flex flex-col">
            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b border-slate-200">
                <h3 id="summaryModalTitle" class="text-lg font-semibold text-slate-800">Image Preview</h3>
                <button type="button" onclick="closeSummaryImageModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div class="p-6 overflow-y-auto flex-1">
                <div class="bg-slate-50/50 rounded-2xl p-5 border border-slate-100 h-full flex flex-col">
                    <div class="mb-4 pb-3 border-b border-slate-200/60">
                        <p id="summaryModalContext" class="text-xs text-slate-500 mt-1"></p>
                    </div>
                    <div id="summarySingleImageContainer" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 content-start"></div>
                    <div id="summaryNoSingleImage" class="hidden flex-1 flex flex-col items-center justify-center min-h-[220px] bg-slate-100/50 rounded-xl border border-dashed border-slate-300/60 mt-auto">
                        <span class="text-xs font-medium text-slate-400">No images available</span>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="flex justify-end p-4 border-t border-slate-200 bg-slate-50 rounded-b-2xl">
                <button type="button" onclick="closeSummaryImageModal()"
                    class="px-6 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-xl font-medium hover:bg-slate-50 transition-colors">
                    Close Preview
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Ensure global loader from layout stays active during first paint on this page.
    document.body.classList.add('data-loading');
    const initialLoader = document.getElementById('page-loader');
    if (initialLoader) {
        initialLoader.classList.remove('hidden');
    }

    $(document).ready(function() {
        const tableElement = $('#summaryVerificationTable');
        const tableContent = $('#summaryTableContent');
        let loadCycle = 0;
        let activeCycle = 0;
        let waitingForAjaxDraw = false;

        function showGlobalLoader() {
            $('body').addClass('data-loading');
            $('#page-loader').removeClass('hidden');
            $('#summaryTableLoadingInfo').removeClass('hidden');
            tableContent.removeClass('opacity-100 pointer-events-auto');
            tableContent.addClass('opacity-0 pointer-events-none');
        }

        function hideGlobalLoader() {
            $('body').removeClass('data-loading');
            $('#page-loader').addClass('hidden');
            $('#summaryTableLoadingInfo').addClass('hidden');
            tableContent.removeClass('opacity-0 pointer-events-none');
            tableContent.addClass('opacity-100 pointer-events-auto');
        }

        function beginLoadCycle() {
            loadCycle += 1;
            showGlobalLoader();
            return loadCycle;
        }

        function finishLoadCycle(cycle) {
            if (cycle !== loadCycle) return;
            hideGlobalLoader();
        }

        function waitForVisibleTableImages(cycle) {
            const tableBody = document.querySelector('#summaryVerificationTable tbody');
            const images = tableBody ? Array.from(tableBody.querySelectorAll('img')) : [];

            if (images.length === 0) {
                finishLoadCycle(cycle);
                return;
            }

            let settled = 0;
            const markDone = () => {
                settled += 1;
                if (settled >= images.length) {
                    finishLoadCycle(cycle);
                }
            };

            images.forEach((img) => {
                if (img.complete) {
                    markDone();
                } else {
                    img.addEventListener('load', markDone, {
                        once: true
                    });
                    img.addEventListener('error', markDone, {
                        once: true
                    });
                }
            });

            // Fallback to prevent loader stuck on edge browser cases.
            setTimeout(() => finishLoadCycle(cycle), 12000);
        }

        tableElement.on('preXhr.dt', function() {
            activeCycle = beginLoadCycle();
            waitingForAjaxDraw = false;
        });

        tableElement.on('xhr.dt', function() {
            // Mark that the next draw is a real AJAX result draw.
            waitingForAjaxDraw = true;
        });

        tableElement.on('draw.dt', function() {
            if (!waitingForAjaxDraw) {
                return;
            }
            waitingForAjaxDraw = false;
            waitForVisibleTableImages(activeCycle);
        });

        tableElement.on('error.dt', function() {
            waitingForAjaxDraw = false;
            finishLoadCycle(activeCycle);
        });

        beginLoadCycle();

        var table = tableElement.DataTable({
            dom: '<"overflow-x-auto"t>ip',
            processing: true,
            serverSide: true,
            deferRender: true,
            autoWidth: false,
            ajax: {
                url: "{{ route('spv_verification.table') }}",
                type: 'POST',
                data: function(d) {
                    d._token = "{{ csrf_token() }}";
                    d.search = $('#searchInput').val();
                    d.date_from = $('#dateFrom').val();
                    d.date_to = $('#dateTo').val();
                    d.dept = $('#deptFilter').val();
                }
            },
            columns: [{
                    data: 'no',
                    orderable: false,
                    className: 'text-center text-slate-700',
                },
                {
                    data: 'findings',
                    className: 'text-slate-700',
                    render: function(data) {
                        return '<span class="text-sm">' + (data || '-') + '</span>';
                    }
                },
                {
                    data: 'path',
                    orderable: false,
                    className: 'text-center',
                    render: function(data, type, row) {
                        const firstImage = getFirstImagePath(data);
                        if (!firstImage) return '<span class="text-slate-300">-</span>';
 
                        const encodedImages = encodeURIComponent(data || '').replace(/'/g, '%27');
                        const encodedContext = encodeURIComponent(row.findings || '').replace(/'/g, '%27');
                        const imageSrc = `${summaryBeforeBaseUrl}/${firstImage}`;
 
                        return `<div class="flex items-center justify-center">
                                    <img src="${imageSrc}" alt="Before Image"
                                        decoding="async"
                                        onerror="this.onerror=null;this.src='${noImageThumbSrc}';"
                                        class="w-16 h-16 object-cover rounded-lg border border-slate-200 cursor-pointer hover:opacity-90 transition"
                                        onclick="openSummaryImageModal('before', '${encodedImages}', '${encodedContext}')">
                                </div>`;
                    }
                },
                {
                    data: 'execution_comment',
                    className: 'text-slate-700',
                    render: function(data) {
                        return '<span class="text-sm">' + (data || '-') + '</span>';
                    }
                },
                {
                    data: 'preventive_action',
                    className: 'text-slate-700',
                    render: function(data) {
                        return '<span class="text-sm">' + (data || '') + '</span>';
                    }
                },
                {
                    data: 'execution_path',
                    orderable: false,
                    className: 'text-center',
                    render: function(data, type, row) {
                        const firstImage = getFirstImagePath(data);
                        if (!firstImage) return '<span class="text-slate-300">-</span>';
 
                        const encodedImages = encodeURIComponent(data || '').replace(/'/g, '%27');
                        const encodedContext = encodeURIComponent(row.execution_comment || '').replace(/'/g, '%27');
                        const imageSrc = `${summaryAfterBaseUrl}/${firstImage}`;
 
                        return `<div class="flex items-center justify-center">
                                    <img src="${imageSrc}" alt="After Image"
                                        decoding="async"
                                        onerror="this.onerror=null;this.src='${noImageThumbSrc}';"
                                        class="w-16 h-16 object-cover rounded-lg border border-slate-200 cursor-pointer hover:opacity-90 transition"
                                        onclick="openSummaryImageModal('after', '${encodedImages}', '${encodedContext}')">
                                </div>`;
                    }
                },
                {
                    data: 'verif_img',
                    orderable: false,
                    className: 'text-center',
                    render: function(data, type, row) {
                        const firstImage = getFirstImagePath(data);
                        if (!firstImage) {
                            return `<div class="flex items-center justify-center">
                                        <div class="w-16 h-16 p-2 rounded-lg border border-slate-300 bg-slate-100 flex flex-col items-center justify-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                                <path d="M21 15l-5-5L5 21"></path>
                                                <line x1="3" y1="3" x2="21" y2="21"></line>
                                            </svg>
                                            <span class="text-center font-medium text-slate-600" style="font-size:10px; line-height:1;">No Picture</span>
                                        </div>
                                    </div>`;
                        }
 
                        const encodedImages = encodeURIComponent(data || '').replace(/'/g, '%27');
                        const encodedContext = encodeURIComponent(row.preventive_action || '').replace(/'/g, '%27');
                        const imageSrc = `${summaryVerificationBaseUrl}/${firstImage}`;
 
                        return `<div class="flex items-center justify-center">
                                    <img src="${imageSrc}" alt="Verification Image"
                                        decoding="async"
                                        onerror="this.onerror=null;this.src='${noImageThumbSrc}';"
                                        class="w-16 h-16 object-cover rounded-lg border border-emerald-200 cursor-pointer hover:opacity-90 transition"
                                        onclick="openSummaryImageModal('verification', '${encodedImages}', '${encodedContext}')">
                                </div>`;
                    }
                },
                {
                    data: 'asign_to_dept',
                    className: 'text-slate-700',
                    render: function(data) {
                        return '<span class="text-sm">' + (data || '-') + '</span>';
                    }
                },
                {
                    data: 'auditor',
                    className: 'text-slate-700',
                    render: function(data) {
                        return data || '-';
                    }
                }
            ],
            order: [
                [0, 'desc']
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
 
        // Debounce search
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

    var summaryGalleryViewer = null;
    const summaryBeforeBaseUrl = "{{ asset('findings-photo') }}";
    const summaryAfterBaseUrl = "{{ asset('evidence-photo') }}";
    const summaryVerificationBaseUrl = "{{ asset('verif-photo') }}";
    const noImageThumbSrc = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='3' width='18' height='18' rx='2' ry='2'/%3E%3Ccircle cx='8.5' cy='8.5' r='1.5'/%3E%3Cpath d='M21 15l-5-5L5 21'/%3E%3Cline x1='3' y1='3' x2='21' y2='21'/%3E%3C/svg%3E";

    function getFirstImagePath(images) {
        if (!images || images.trim() === '') return '';
        const paths = images.split(',').map(item => item.trim()).filter(item => item !== '');
        return paths.length > 0 ? paths[0] : '';
    }

    function appendImageList(containerSelector, images, baseUrl, emptySelector, altText) {
        const container = $(containerSelector);
        container.empty();
        $(emptySelector).addClass('hidden');

        if (!images || images.trim() === '') {
            $(emptySelector).removeClass('hidden').addClass('flex');
            return;
        }

        images.split(',').forEach(imgName => {
            const cleaned = imgName.trim();
            if (!cleaned) return;

            const fullPath = baseUrl + '/' + cleaned;
            const imageHtml = `
                <div class="relative group cursor-zoom-in overflow-hidden rounded-lg bg-slate-100 border border-slate-200 aspect-[4/3]">
                    <img src="${fullPath}"
                         class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                         alt="${altText}"
                         onerror="this.parentElement.style.display='none'">
                </div>
            `;
            container.append(imageHtml);
        });

        if (!container.children().length) {
            $(emptySelector).removeClass('hidden').addClass('flex');
        }
    }

    function openSummaryImageModal(imageType, encodedImages, encodedContext = '') {
        const decodedImages = decodeURIComponent(encodedImages || '');
        const decodedContext = decodeURIComponent(encodedContext || '');

        let modalTitle = 'Image Preview';
        let imageBaseUrl = summaryBeforeBaseUrl;
        let altText = 'Image';

        if (imageType === 'before') {
            modalTitle = 'Before Images';
            imageBaseUrl = summaryBeforeBaseUrl;
            altText = 'Before Image';
        } else if (imageType === 'after') {
            modalTitle = 'After Images';
            imageBaseUrl = summaryAfterBaseUrl;
            altText = 'After Image';
        } else if (imageType === 'verification') {
            modalTitle = 'Verification Images';
            imageBaseUrl = summaryVerificationBaseUrl;
            altText = 'Verification Image';
        }

        $('#summaryModalTitle').text(modalTitle);
        $('#summaryModalContext').text(decodedContext);
        appendImageList('#summarySingleImageContainer', decodedImages, imageBaseUrl, '#summaryNoSingleImage', altText);

        if (summaryGalleryViewer) {
            summaryGalleryViewer.destroy();
        }

        const container = document.querySelector('#summaryImagePreviewModal .p-6');
        if (typeof Viewer !== 'undefined' && container) {
            summaryGalleryViewer = new Viewer(container, {
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

        $('#summaryImagePreviewModal').removeClass('hidden');
    }

    function closeSummaryImageModal() {
        $('#summaryImagePreviewModal').addClass('hidden');
        if (summaryGalleryViewer) {
            summaryGalleryViewer.destroy();
            summaryGalleryViewer = null;
        }
    }
</script>
@endpush
