@extends('layouts.app')

@section('title', 'Execution Genba - QMS')

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
            <h1 class="text-2xl font-bold text-slate-800">Execution Genba</h1>
            <p class="text-slate-500 mt-1">Verifikasi Genba (Approval)</p>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-lg border border-slate-200">
            <!-- Filter Section -->
            <div class="p-6 border-b border-slate-200 bg-slate-50/50">
                <div class="flex flex-wrap items-center gap-3">
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
            </div>

            <!-- Table Section -->
            <div class="overflow-x-auto p-6">
                <table id="findingsTable" class="qms-table w-full">
                    <thead>
                        <tr>
                            <th class="w-[4%] text-center">No</th>
                            <th class="w-[8%]">DocNum</th>
                            <th class="w-[10%]">Genba Date</th>
                            <th class="w-[15%]">Area Detail</th>
                            <th class="w-[5%]">Pict</th>
                            <th class="w-[9%]">Asign to Dept</th>
                            <th class="w-[12%]">Auditor</th>
                            <th class="w-[14%]">
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
                            <th class="w-[8%]">Approve</th>
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

<!-- Image Preview Modal -->
<div id="imagePreviewModal" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-900/60 transition-opacity" onclick="closeImageModal()"></div>

    <!-- Modal -->
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-4xl transform transition-all h-[90vh] flex flex-col">
            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b border-slate-200">
                <h3 id="modalTitle" class="text-lg font-semibold text-slate-800">Findings & Evidence</h3>
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
                                <!-- Simple arrow decoration -->
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
            <div class="flex justify-end p-4 border-t border-slate-200 bg-slate-50 rounded-b-2xl">
                <button type="button" onclick="closeImageModal()"
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
    $(document).ready(function() {
        var table = $('#findingsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('execution_genba.table') }}",
                type: 'POST',
                data: function(d) {
                    d._token = "{{ csrf_token() }}";
                    d.search = $('#searchInput').val(); // Corrected param name for controller
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
                    data: 'date',
                    className: 'text-slate-700',
                    render: function(data, type, row) {
                        return '<span class="text-sm">' + data + '</span>';
                    }
                },
                {
                    data: 'area_detail',
                    className: 'text-slate-700',
                    render: function(data, type, row) {
                        return '<span class="text-sm">' + (data || '-') + '</span>';
                    }
                },
                {
                    data: 'path', // Using path as the data source, but accessing other fields in render
                    orderable: false,
                    className: 'text-left',
                    render: function(data, type, row) {
                        const hasBefore = row.path ? true : false;
                        const hasAfter = row.execution_path ? true : false;

                        if (hasBefore || hasAfter) {
                            const findings = encodeURIComponent(row.findings || '').replace(/'/g, "%27");
                            const comment = encodeURIComponent(row.execution_comment || '').replace(/'/g, "%27");
                            const pathBefore = row.path || '';
                            const pathAfter = row.execution_path || '';

                            return `
                                <div class="flex items-center justify-start w-full">
                                    <button class="w-9 h-9 inline-flex items-center justify-center text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors ring-1 ring-slate-200" 
                                        onclick="viewGenbaImages('${pathBefore}', '${pathAfter}', '${findings}', '${comment}')" 
                                        title="View Images">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                            <polyline points="21 15 16 10 5 21"></polyline>
                                        </svg>
                                    </button>
                                </div>
                            `;
                        }
                        return '<span class="text-slate-300">-</span>';
                    }
                },
                {
                    data: 'asign_to_dept',
                    className: 'text-slate-700',
                    render: function(data, type, row) {
                        return '<span class="text-sm">' + data + '</span>';
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
                    orderable: true,
                    className: 'text-center',
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
                [2, 'desc']
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

        // Mobile sidebar toggle
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
    });

    function document_preview(id, no) {
        // Redirect to preview page - Reusing existing preview
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

        // Convert captions
        $('#modalCaptionBefore').text(decodeURIComponent(captionBefore));
        $('#modalCaptionAfter').text(decodeURIComponent(captionAfter));

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
        // Or we can just create one for the whole .p-6 container
        var container = document.querySelector('#imagePreviewModal .p-6');

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
            title: false, // Hide title to avoid clutter
            transition: true,
        });

        // Show modal
        $('#imagePreviewModal').removeClass('hidden');
    }

    function closeImageModal() {
        $('#imagePreviewModal').addClass('hidden');
        // Clear logic if needed
        if (galleryViewer) {
            galleryViewer.destroy();
            galleryViewer = null;
        }
    }
</script>
<script>
    let currentAction = ''; // 'approve' or 'rollback'

    function approveGenba(id) {
        currentAction = 'approve';
        document.getElementById('confirmationId').value = id;

        // Update Modal UI for Approval
        const modal = document.getElementById('confirmationModal');
        modal.querySelector('#modalTitle').innerText = 'Approve Finding?';
        modal.querySelector('#modalMessage').innerHTML = 'Are you sure you want to approve this finding?<br>This action cannot be undone.';

        // Icon
        const iconContainer = modal.querySelector('#modalIcon');
        iconContainer.className = 'w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-5';

        iconContainer.innerHTML = `<svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>`;

        // Confirm Button (Green)
        const confirmBtn = document.getElementById('confirmBtn');
        confirmBtn.className = 'px-5 py-2.5 bg-emerald-50 text-emerald-600 font-medium rounded-xl hover:bg-emerald-700 transition-colors border border-emerald-200';
        confirmBtn.innerText = 'Yes, Approve';

        modal.classList.remove('hidden');
    }

    function rollbackGenba(id) {
        currentAction = 'rollback';
        document.getElementById('confirmationId').value = id;

        // Update Modal UI for Rollback
        const modal = document.getElementById('confirmationModal');
        modal.querySelector('#modalTitle').innerText = 'Rollback Finding?';
        modal.querySelector('#modalMessage').innerHTML = 'Are you sure you want to rollback this finding?<br>The status will be reset.';

        // Icon (Amber Undo/Refresh)
        const iconContainer = modal.querySelector('#modalIcon');
        iconContainer.className = 'w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-5';
        iconContainer.innerHTML = `<svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 0 1 8 8v2M3 10l6 6m-6-6l6-6"></path></svg>`;

        // Confirm Button (Amber)
        const confirmBtn = document.getElementById('confirmBtn');
        confirmBtn.className = 'px-5 py-2.5 bg-amber-50 text-amber-600 font-medium rounded-xl hover:bg-amber-700 transition-colors border border-amber-200';
        confirmBtn.innerText = 'Yes, Rollback';

        modal.classList.remove('hidden');
    }

    function closeConfirmationModal() {
        document.getElementById('confirmationModal').classList.add('hidden');
        document.getElementById('confirmationId').value = '';
    }

    function submitConfirmation() {
        const id = document.getElementById('confirmationId').value;
        const confirmBtn = document.getElementById('confirmBtn');
        const originalText = confirmBtn.innerText;

        let url = '';
        if (currentAction === 'approve') {
            url = "{{ route('execution_genba.approve') }}";
        } else if (currentAction === 'rollback') {
            url = "{{ route('execution_genba.rollback') }}";
        }

        // Show loading state
        confirmBtn.disabled = true;
        confirmBtn.innerText = 'Processing...';

        $.ajax({
            url: url,
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: id
            },
            success: function(response) {
                closeConfirmationModal();
                if (response.status === 'success') {
                    showToast(response.message, 'success');
                    $('#findingsTable').DataTable().ajax.reload();
                } else {
                    showToast(response.message, 'error');
                }
            },
            error: function(xhr) {
                closeConfirmationModal();
                showToast('Something went wrong. Please try again.', 'error');
            },
            complete: function() {
                confirmBtn.disabled = false;
                confirmBtn.innerText = originalText;
            }
        });
    }
</script>
<!-- Generic Confirmation Modal -->
<div id="confirmationModal" class="fixed inset-0 z-[60] hidden">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-900/60 transition-opacity" onclick="closeConfirmationModal()"></div>

    <!-- Modal -->
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-md transform transition-all p-6 text-center border border-slate-100">

            <div id="modalIcon" class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-5">
                <!-- Icon injected by JS -->
            </div>

            <h3 id="modalTitle" class="text-xl font-bold text-slate-800 mb-2"></h3>
            <p id="modalMessage" class="text-base text-slate-600 mb-6 leading-relaxed"></p>

            <input type="hidden" id="confirmationId">

            <div class="flex gap-3 justify-center">
                <button onclick="closeConfirmationModal()"
                    class="px-5 py-2.5 bg-slate-100 text-slate-700 font-medium rounded-xl hover:bg-slate-200 transition-colors">
                    Cancel
                </button>
                <button id="confirmBtn" onclick="submitConfirmation()"
                    class="px-5 py-2.5 bg-green-600 text-white font-medium rounded-xl hover:bg-green-700 transition-colors">
                    Yes
                </button>
            </div>
        </div>
    </div>
</div>
@endpush