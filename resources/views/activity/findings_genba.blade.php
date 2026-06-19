@extends('layouts.app')

@section('title', 'Findings Genba - QMS')

@section('content')
@include('layouts.sidebar')
@include('components.toast')

<!-- Main Content -->
<div class="lg:ml-20 min-h-screen flex flex-col bg-slate-50">
    @include('layouts.header')

    <!-- Page Content -->
    <main class="flex-1 p-6">
        <!-- Page Title -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Findings Genba</h1>
            <p class="text-slate-500 mt-1">Manage and track genba findings</p>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-lg border border-slate-200">
            <!-- Filter Section -->
            <div class="p-6 border-b border-slate-200 bg-slate-50/50">
                <div class="grid grid-cols-2 lg:flex lg:flex-row lg:flex-wrap lg:items-center gap-3">
                    <!-- Search -->
                    <div class="col-span-2 lg:col-span-auto lg:flex-1 lg:min-w-[200px]">
                        <div class="relative">
                            <input type="text" id="searchInput" placeholder="Search Findings and DocNum..."
                                class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                            <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        </div>
                    </div>

                    <!-- Date From -->
                    <div class="col-span-1 lg:col-span-auto w-full lg:w-auto">
                        <div class="date-input-container w-full lg:w-auto">
                            <input type="text" id="dateFrom" placeholder="dd/mm/yyyy" onfocus="(this.type='date')" onblur="if(!this.value) this.type='text'"
                                class="w-full lg:w-[150px] pl-4 pr-10 py-2 border border-slate-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                <i class="fa-regular fa-calendar text-sm"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Date To -->
                    <div class="col-span-1 lg:col-span-auto w-full lg:w-auto">
                        <div class="date-input-container w-full lg:w-auto">
                            <input type="text" id="dateTo" placeholder="dd/mm/yyyy" onfocus="(this.type='date')" onblur="if(!this.value) this.type='text'"
                                class="w-full lg:w-[150px] pl-4 pr-10 py-2 border border-slate-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
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
                            updateEvent="reset-dept"
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
                            updateEvent="reset-detail-area"
                            hideLabel="true" />
                    </div>

                    <!-- Status Filter -->
                    <div class="col-span-1 lg:col-span-auto w-full lg:w-auto min-w-0 lg:min-w-[150px]">
                        @php
                        $statusOptions = [
                        ['id' => 'OPEN', 'name' => 'Open'],
                        ['id' => 'NEED_VERIF', 'name' => 'Need Verif'],
                        ['id' => 'CLOSE', 'name' => 'Close']
                        ];
                        @endphp
                        <x-searchable-select
                            name="status"
                            id="statusFilter"
                            label="Status"
                            :initialOptions="$statusOptions"
                            updateEvent="reset-status"
                            hideLabel="true" />
                    </div>

                    <!-- Reset Button -->
                    <button type="button" id="btnReset"
                        class="col-span-1 lg:col-span-auto w-full lg:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 text-sm font-base transition-colors">
                        <i class="fa-solid fa-rotate-right text-sm"></i>
                        Reset
                    </button>
                </div>
            </div>

            <!-- Table Section -->
            <div class="p-6">
                <table id="findingsTable" class="qms-table w-full min-w-[1500px]">
                    <thead>
                        <tr>
                            <th class="w-[3%] text-center">No</th>
                            <th class="w-[8%]">DocNum</th>
                            <th class="w-[5%]">Picture</th>
                            <th class="w-[8%]">Genba Date</th>
                            <th class="w-[10%]">Detail Area</th>
                            <th class="w-[8%]">Line Checked</th>
                            <th class="w-[6%]">Dept</th>
                            <th class="w-[15%]">Findings</th>
                            <th class="w-[8%]">Auditor</th>
                            <th class="w-[20%]">
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
                            <th class="w-[9%]">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                    </tbody>
                </table>
            </div>
            <!-- Data Count Component -->
            <x-data-table tableId="findingsTable" />
        </div>
    </main>

    @include('layouts.footer')
</div>

<!-- Mobile Sidebar Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/50 z-30 hidden lg:hidden"></div>

<!-- Delete Confirmation Modal -->
<div id="deleteConfirmModal" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-900/60 transition-opacity"></div>

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
                <h3 class="text-xl font-bold text-slate-800 mb-2">Konfirmasi Hapus</h3>
                <p class="text-slate-500">Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.</p>
            </div>

            <!-- Footer -->
            <div class="flex gap-3 p-6 pt-0">
                <button type="button" id="btnCancelDelete"
                    class="flex-1 px-4 py-3 bg-slate-100 text-slate-700 rounded-xl font-semibold hover:bg-slate-200 transition-colors">
                    Tidak
                </button>
                <button type="button" id="btnConfirmDelete"
                    class="flex-1 px-4 py-3 bg-red-600 text-white rounded-xl font-semibold hover:bg-red-700 transition-colors">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Image Preview Modal -->
<div id="imagePreviewModal" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-900/60 transition-opacity" onclick="closeImageModal()"></div>

    <!-- Modal -->
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-2xl transform transition-all">
            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b border-slate-200">
                <h3 class="text-lg font-semibold text-slate-800">Findings</h3>
                <button type="button" onclick="closeImageModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div class="p-6 overflow-y-auto max-h-[80vh]">
                <div id="imageContainer" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Images will be inserted here dynamically -->
                </div>

                <!-- Fallback for no images -->
                <div id="noImageContainer" class="hidden flex-col items-center justify-center min-h-[300px] text-slate-400">
                    <svg class="w-24 h-24 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3l18 18" />
                    </svg>
                    <p class="text-sm">Image not available</p>
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
<script>
    $(document).ready(function() {
        var table = $('#findingsTable').DataTable({
            dom: '<"overflow-x-auto"t>ip',
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('genba.table') }}",
                type: 'POST',
                data: function(d) {
                    d._token = "{{ csrf_token() }}";
                    d.front_table_search = $('#searchInput').val();
                    d.date_from = $('#dateFrom').val();
                    d.date_to = $('#dateTo').val();
                    d.dept = $('#deptFilter').val();
                    d.status = $('#statusFilter').val();
                    d.detail_area = $('#detailAreaFilter').val();
                },
                error: function(xhr, error, code) {
                    console.error('DataTables AJAX error:', error, code);
                    console.error('Response:', xhr.responseText);
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
                        if (data) {
                            return '<button class="w-9 h-9 flex items-center justify-center text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors" onclick="viewImage(\'' + data + '\')" title="View Image"><svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg></button>';
                        }
                        return '<button class="w-9 h-9 flex items-center justify-center text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors" onclick="viewImage(\'' + data + '\')" title="View Image"><svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg></button>';
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
                    data: 'station',
                    className: 'text-slate-700',
                    render: function(data, type, row) {
                        return '<span class="text-sm">' + (data || '') + '</span>';
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
                    className: 'text-slate-700',
                    render: function(data, type, row) {
                        return '<span class="text-sm">' + data + '</span>';
                    }
                },
                {
                    data: 'findings',
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
        $('#dateFrom, #dateTo, #deptFilter, #statusFilter, #detailAreaFilter').on('change', function() {
            table.ajax.reload();
        });





        // Reset button
        $('#btnReset').click(function() {
            $('#searchInput').val('');
            $('#dateFrom').val('').attr('type', 'text');
            $('#dateTo').val('').attr('type', 'text');
            
            // Reset searchable-select components via their updateEvent triggers
            window.dispatchEvent(new CustomEvent('reset-dept', { detail: '' }));
            window.dispatchEvent(new CustomEvent('reset-status', { detail: '' }));
            window.dispatchEvent(new CustomEvent('reset-detail-area', { detail: '' }));
            
            table.ajax.reload();
        });

        // Handle initial date values (if any)
        if ($('#dateFrom').val()) $('#dateFrom').attr('type', 'date');
        if ($('#dateTo').val()) $('#dateTo').attr('type', 'date');

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

    function viewImage(path) {
        // Reset state
        $('#imageContainer').empty().removeClass('hidden');
        $('#noImageContainer').addClass('hidden').removeClass('flex');

        if (!path) {
            $('#imageContainer').addClass('hidden');
            $('#noImageContainer').removeClass('hidden').addClass('flex');
            $('#imagePreviewModal').removeClass('hidden');
            return;
        }

        // Split path by comma to handle multiple images
        var paths = path.split(',');

        paths.forEach(function(imgName) {
            imgName = imgName.trim();
            if (imgName) {
                var imagePath = findingPhotoBaseUrl + '/' + imgName;
                var imgHtml = `
                    <div class="relative group cursor-pointer">
                        <img src="${imagePath}" 
                             class="w-full h-auto rounded-lg object-contain border border-slate-200 hover:opacity-90 transition-opacity" 
                             alt="Finding Image"
                             onerror="this.parentElement.style.display='none'">
                    </div>
                `;
                $('#imageContainer').append(imgHtml);
            }
        });

        // Initialize Viewer.js
        if (galleryViewer) {
            galleryViewer.destroy();
        }

        var container = document.getElementById('imageContainer');
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

        // Show modal
        $('#imagePreviewModal').removeClass('hidden');
    }

    function closeImageModal() {
        $('#imagePreviewModal').addClass('hidden');
        $('#imageContainer').empty();

        if (galleryViewer) {
            galleryViewer.destroy();
            galleryViewer = null;
        }
    }

    function showBrokenImage() {
        $('#imageContainer').addClass('hidden');
        $('#noImageContainer').removeClass('hidden').addClass('flex');
    }

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
            url: "{{ route('genba.delete') }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                sys_id: sysId
            },
            success: function(response) {
                $('#icon_f_genba_conform_delete_' + no).removeClass('hidden');
                $('#loader_f_genba_conform_delete_' + no).addClass('hidden');

                if (response.success) {
                    showToast('Data berhasil dihapus', 'success');
                    $('#findingsTable').DataTable().ajax.reload();
                } else {
                    showToast('Gagal menghapus data', 'error');
                }
            },
            error: function() {
                $('#icon_f_genba_conform_delete_' + no).removeClass('hidden');
                $('#loader_f_genba_conform_delete_' + no).addClass('hidden');
                showToast('Terjadi kesalahan', 'error');
            }
        });
    }

    // Modal button handlers
    $(document).ready(function() {
        $('#btnCancelDelete').click(function() {
            closeDeleteModal();
        });

        $('#btnConfirmDelete').click(function() {
            executeDelete();
        });

        // Close modal on backdrop click
        $('#deleteConfirmModal').click(function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });
    });
</script>
@endpush