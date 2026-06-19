@extends('layouts.app')

@section('title', 'Genba Form ')

@section('content')
@include('layouts.sidebar')
@include('components.toast')

<!-- Main Content -->
<div class="lg:ml-20 min-h-screen flex flex-col bg-slate-50">
    @include('layouts.header')

    <!-- Page Content -->
    <main class="flex-1 p-6">
        <!-- List View -->
        <div id="listView">
            <!-- Page Title -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-slate-800">Genba Form</h1>
                <p class="text-slate-500 mt-1">Manage genba audit forms</p>
            </div>

            <!-- Main Card -->
            <div class="bg-white rounded-lg border border-slate-200">
                <!-- Filter Section -->
                <div class="p-6 border-b border-slate-200 bg-slate-50/50">
                    <div class="flex flex-wrap items-center gap-3">
                        <!-- Search -->
                        <div class="flex-1 min-w-[200px]">
                            <div class="relative">
                                <input type="text" id="searchInput" placeholder="Search..."
                                    class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                                <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            </div>
                        </div>

                        <!-- Spacer -->
                        <div class="flex-1"></div>

                        <!-- Create Button -->
                        <button type="button" onclick="showCreateForm()"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition-colors">
                            <i class="fa-solid fa-plus text-sm"></i>
                            Create
                        </button>
                    </div>
                </div>

                <!-- Status Tabs -->
                <div class="grid grid-cols-3 border-b border-slate-200">
                    <button type="button" id="btnAll" onclick="filterByStatus(0)"
                        class="status-tab py-4 px-6 text-sm font-medium text-blue-600 hover:text-blue-600 hover:bg-slate-50 transition-colors border-b-2 border-blue-600">
                        All
                    </button>
                    <button type="button" id="btnDraft" onclick="filterByStatus(4)"
                        class="status-tab py-4 px-6 text-sm font-medium text-slate-600 hover:text-blue-600 hover:bg-slate-50 transition-colors border-b-2 border-transparent">
                        Draft
                    </button>
                    <button type="button" id="btnDone" onclick="filterByStatus(3)"
                        class="status-tab py-4 px-6 text-sm font-medium text-slate-600 hover:text-blue-600 hover:bg-slate-50 transition-colors border-b-2 border-transparent">
                        Done
                    </button>
                </div>

                <!-- Table Section -->
                <div class="p-6">
                    <table id="genbaFormTable" class="qms-table w-full min-w-[1200px]">
                        <thead>
                            <tr>
                                <th class="w-[4%] text-center">No</th>
                                <th class="w-[12%]">Genba Date</th>
                                <th class="w-[15%]">Process</th>
                                <th class="w-[12%]">Line Checked</th>
                                <th class="w-[18%]">Auditor</th>
                                <th class="w-[12%]">Category</th>
                                <th class="w-[12%]">View</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Create Form View -->
        <div id="createView" class="hidden">
            <!-- Page Title -->
            <div class="mb-6">
                <div class="flex items-center gap-3">
                    <button type="button" onclick="hideCreateForm()"
                        class="inline-flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all">
                        <i class="fa-solid fa-arrow-left text-[11px] sm:text-sm"></i>
                    </button>
                    <div>
                        <h1 class="text-lg sm:text-2xl font-bold text-slate-800">Genba Form</h1>
                        <p class="text-slate-500 text-sm">Create or edit genba audit details</p>
                    </div>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-xl border border-slate-200">
                <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50 rounded-t-xl">
                    <h2 class="text-lg font-bold text-slate-800">Header Information</h2>
                    <p class="text-slate-500 text-sm mt-1">Please fill in the required audit information below.</p>
                </div>

                <div class="p-8">
                    <form id="createGenbaForm" action="{{ route('genba.header.add') }}" method="POST">
                        @csrf
                        <input type="hidden" id="formTrcUnixId" name="trc_unix_id">

                        <div class="grid grid-cols-2 gap-x-4 sm:gap-x-8 gap-y-6">
                            <!-- Date -->
                            <div class="col-span-2 lg:col-span-1">
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Date <span class="text-red-500">*</span></label>
                                <input type="date" id="formDate" name="date" required
                                    class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm outline-none transition-all hover:border-blue-300">
                            </div>

                            <!-- Process -->
                            <div class="col-span-2 lg:col-span-1">
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Process <span class="text-red-500">*</span></label>
                                <x-searchable-select
                                    name="process"
                                    id="formProcess"
                                    label="Process"
                                    required="true"
                                    optionsEvent="update-process-options"
                                    updateEvent="update-process-value"
                                    changeEvent="process-changed"
                                    dependencyParam="process"
                                    hideLabel="true" />
                            </div>

                            <!-- Line Checked -->
                            <div class="col-span-2 lg:col-span-1">
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Line Checked <span class="text-red-500">*</span></label>
                                <x-searchable-select
                                    name="line_checked"
                                    id="formLineChecked"
                                    label="Line Checked"
                                    required="true"
                                    apiUrl="{{ route('genba.header.area') }}"
                                    dependencyEvent="process-changed"
                                    dependencyParam="process"
                                    valueField="name"
                                    updateEvent="update-line-value"
                                    hideLabel="true" />
                            </div>

                            <!-- Category -->
                            <div class="col-span-2 lg:col-span-1">
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Category <span class="text-red-500">*</span></label>
                                <x-searchable-select
                                    name="category"
                                    id="formCategory"
                                    label="Category"
                                    required="true"
                                    apiUrl="{{ route('genba.header.category') }}"
                                    updateEvent="update-category-value"
                                    hideLabel="true" />
                            </div>


                            <!-- Auditor -->
                            <div class="col-span-2 lg:col-span-1">
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Auditor <span class="text-red-500">*</span></label>
                                <input type="text" id="formAuditor" name="auditor" required
                                    value="{{ Auth::user()->full_name ?? '' }}"
                                    class="w-full px-4 py-2.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-500 text-sm outline-none">
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end gap-3 pt-8 mt-8 border-t border-slate-100">
                            <button type="button" onclick="hideCreateForm()"
                                class="px-6 py-2.5 bg-white text-slate-700 border border-slate-200 rounded-lg hover:bg-slate-50 text-sm font-medium transition-all">
                                Cancel
                            </button>
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition-all hover: active:scale-95">
                                <span>Next Step</span>
                                <i class="fa-solid fa-arrow-right text-sm"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
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
                <h3 class="text-xl font-bold text-slate-800 mb-2">Confirm Delete</h3>
                <p class="text-slate-500">Are you sure you want to delete this data? This action cannot be undone.</p>
            </div>

            <!-- Footer -->
            <div class="flex gap-3 p-6 pt-0">
                <button type="button" id="btnCancelDelete"
                    class="flex-1 px-4 py-3 bg-slate-100 text-slate-700 rounded-xl font-semibold hover:bg-slate-200 transition-colors">
                    No
                </button>
                <button type="button" id="btnConfirmDelete"
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
    var currentStatusId = 0; // Default: All

    $(document).ready(function() {
        var table = $('#genbaFormTable').DataTable({
            dom: '<"overflow-x-auto"t>ip',
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('genba.header.table') }}",
                type: 'POST',
                data: function(d) {
                    d._token = "{{ csrf_token() }}";
                    d.front_table_search = $('#searchInput').val();
                    d.status_id = currentStatusId;
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
                    data: 'date',
                    className: 'text-slate-700',
                    render: function(data, type, row) {
                        return '<span class="text-sm">' + data + '</span>';
                    }
                },
                {
                    data: 'process',
                    className: 'text-slate-700',
                    render: function(data, type, row) {
                        return '<span class="text-sm">' + (data || '') + '</span>';
                    }
                },
                {
                    data: 'line_checked',
                    className: 'text-slate-700',
                    render: function(data, type, row) {
                        return '<span class="text-sm">' + (data || '') + '</span>';
                    }
                },
                {
                    data: 'auditor',
                    className: 'text-blue-600',
                    render: function(data, type, row) {
                        return data || '';
                    }
                },
                {
                    data: 'category',
                    className: 'text-slate-700',
                    render: function(data, type, row) {
                        return '<span class="text-sm">' + (data || '') + '</span>';
                    }
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

        // Search on keyup (real-time search with delay)
        var searchTimer;
        $('#searchInput').on('keyup', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function() {
                table.ajax.reload();
            }, 500);
        });



        // Form validation on submit
        $('#createGenbaForm').on('submit', function(e) {
            let isValid = true;
            let errors = [];

            // Validate Date
            const dateVal = $('#formDate').val();
            if (!dateVal) {
                isValid = false;
                errors.push('Date is required');
                $('#formDate').addClass('border-red-500');
            } else {
                $('#formDate').removeClass('border-red-500');
            }

            // Validate Process
            const processVal = $('#formProcess').val();
            if (!processVal) {
                isValid = false;
                errors.push('Process is required');
                $('#formProcess').closest('.col-span-2').find('input[type="text"]').addClass('border-red-500');
            } else {
                $('#formProcess').closest('.col-span-2').find('input[type="text"]').removeClass('border-red-500');
            }

            // Validate Line Checked
            const lineVal = $('#formLineChecked').val();
            if (!lineVal) {
                isValid = false;
                errors.push('Line Checked is required');
                $('#formLineChecked').closest('.col-span-2').find('input[type="text"]').addClass('border-red-500');
            } else {
                $('#formLineChecked').closest('.col-span-2').find('input[type="text"]').removeClass('border-red-500');
            }

            // Validate Category
            const categoryVal = $('#formCategory').val();
            if (!categoryVal) {
                isValid = false;
                errors.push('Category is required');
                $('#formCategory').closest('.col-span-2').find('input[type="text"]').addClass('border-red-500');
            } else {
                $('#formCategory').closest('.col-span-2').find('input[type="text"]').removeClass('border-red-500');
            }

            // Station / Mech. Num is optional - no validation needed

            if (!isValid) {
                e.preventDefault();
                showToast(errors.join(', '), 'error');
                return false;
            }
        });

        // Modal button handlers
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

    var currentTrcUnixId = '';

    function document_view(id, no) {
        loadGenbaActivity(id);
    }

    function showCreateForm() {
        var timestamp = new Date().getTime();
        loadGenbaActivity('0_' + timestamp);
    }

    function loadGenbaActivity(trcUnixId) {
        // Show loading
        $('#page-loader').removeClass('hidden');
        currentTrcUnixId = trcUnixId;
        $('#formTrcUnixId').val(trcUnixId);


        $.ajax({
            url: "{{ route('genba.header.activity') }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                trc_unix_id: trcUnixId
            },
            success: function(response) {
                // Dispatch event to update options
                window.dispatchEvent(new CustomEvent('update-process-options', {
                    detail: {
                        options: response.process_options || []
                    }
                }));

                // Fill form with data - handle date format "YYYY-MM-DD HH:MM:SS" or "YYYY-MM-DDTHH:MM:SS"
                var dateVal = '';
                if (response.date) {
                    dateVal = response.date.split(' ')[0].split('T')[0];
                } else {
                    dateVal = new Date().toISOString().split('T')[0];
                }
                $('#formDate').val(dateVal);

                // Update process value via event and direct update
                var processVal = response.process || '';
                window.dispatchEvent(new CustomEvent('update-process-value', {
                    detail: {
                        value: processVal
                    }
                }));
                $('#formProcess').val(processVal);

                // Dispatch dependency event to enable/disable Line Checked
                window.dispatchEvent(new CustomEvent('process-changed', {
                    detail: {
                        process: processVal
                    }
                }));

                // Update line checked value via event
                var lineVal = response.area_checked || '';
                window.dispatchEvent(new CustomEvent('update-line-value', {
                    detail: {
                        value: lineVal
                    }
                }));
                $('#formLineChecked').val(lineVal);

                $('#formStation').val(response.station || '');
                $('#formAuditor').val(response.auditor || '');

                // Update category value via event
                var catId = response.category_id || '';
                var catName = response.category || '';
                window.dispatchEvent(new CustomEvent('update-category-value', {
                    detail: {
                        id: catId,
                        name: catName
                    }
                }));
                $('#formCategory').val(catId);

                // Fade out list view
                $('#listView').addClass('opacity-0 transition-opacity duration-300');

                setTimeout(function() {
                    $('#listView').addClass('hidden').removeClass('opacity-0');
                    $('#createView').removeClass('hidden').addClass('opacity-0');

                    // Fade in create view
                    setTimeout(function() {
                        $('#createView').removeClass('opacity-0').addClass('transition-opacity duration-300');
                        $('#page-loader').addClass('hidden');
                    }, 50);
                }, 300);
            },
            error: function(xhr) {
                $('#page-loader').addClass('hidden');
                showToast('Error loading data: ' + xhr.responseText, 'error');
            }
        });
    }

    function hideCreateForm() {
        // Show loading
        $('#page-loader').removeClass('hidden');

        // Fade out create view
        $('#createView').addClass('opacity-0 transition-opacity duration-300');

        setTimeout(function() {
            $('#createView').addClass('hidden').removeClass('opacity-0');
            $('#listView').removeClass('hidden').addClass('opacity-0');

            // Fade in list view
            setTimeout(function() {
                $('#listView').removeClass('opacity-0').addClass('transition-opacity duration-300');
                $('#page-loader').addClass('hidden');
            }, 50);

            // Reset form
            $('#createGenbaForm')[0].reset();
        }, 300);
    }

    function filterByStatus(statusId) {
        currentStatusId = statusId;

        // Update tab styles
        $('.status-tab').removeClass('text-blue-600 border-blue-600').addClass('text-slate-600 border-transparent');

        if (statusId == 0) {
            $('#btnAll').addClass('text-blue-600 border-blue-600').removeClass('text-slate-600 border-transparent');
        } else if (statusId == 4) {
            $('#btnDraft').addClass('text-blue-600 border-blue-600').removeClass('text-slate-600 border-transparent');
        } else {
            $('#btnDone').addClass('text-blue-600 border-blue-600').removeClass('text-slate-600 border-transparent');
        }

        // Reload table
        $('#genbaFormTable').DataTable().ajax.reload();
    }

    // Set initial active tab style
    $(document).ready(function() {
        $('#btnAll').addClass('text-blue-600 border-blue-600').removeClass('text-slate-600 border-transparent');
    });

    // Delete confirmation variables
    var deleteTargetSysId = null;
    var deleteTargetNo = null;

    function f_genba_header_delete(sysId, no) {
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
        $('#icon_f_genba_header_delete_' + no).addClass('hidden');
        $('#loader_f_genba_header_delete_' + no).removeClass('hidden');

        closeDeleteModal();

        $.ajax({
            url: "{{ route('genba.header.delete') }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                sys_id: sysId
            },
            success: function(response) {
                $('#icon_f_genba_header_delete_' + no).removeClass('hidden');
                $('#loader_f_genba_header_delete_' + no).addClass('hidden');

                if (response.success) {
                    showToast('Data berhasil dihapus', 'success');
                    $('#genbaFormTable').DataTable().ajax.reload();
                } else {
                    showToast('Gagal menghapus data', 'error');
                }
            },
            error: function() {
                $('#icon_f_genba_header_delete_' + no).removeClass('hidden');
                $('#loader_f_genba_header_delete_' + no).addClass('hidden');
                showToast('Terjadi kesalahan', 'error');
            }
        });
    }
</script>
@endpush