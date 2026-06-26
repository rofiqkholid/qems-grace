@extends('layouts.app')

@section('title', 'Internal Audit')

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
                <h1 class="text-2xl font-bold text-slate-800">Internal Audit</h1>
                <p class="text-slate-500 mt-1">Plan agendas, conduct standard compliance audits, and manage CAR workflows</p>
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
                    <button type="button" id="btnAll" onclick="filterByStatus('All')"
                        class="status-tab py-4 px-6 text-sm font-medium text-blue-600 hover:text-blue-600 hover:bg-slate-50 transition-colors border-b-2 border-blue-600">
                        All
                    </button>
                    <button type="button" id="btnScheduled" onclick="filterByStatus('Scheduled')"
                        class="status-tab py-4 px-6 text-sm font-medium text-slate-600 hover:text-blue-600 hover:bg-slate-50 transition-colors border-b-2 border-transparent">
                        Scheduled
                    </button>
                    <button type="button" id="btnDone" onclick="filterByStatus('Done')"
                        class="status-tab py-4 px-6 text-sm font-medium text-slate-600 hover:text-blue-600 hover:bg-slate-50 transition-colors border-b-2 border-transparent">
                        Done
                    </button>
                </div>

                <!-- Table Section -->
                <div class="p-6">
                    <table id="genbaFormTable" class="qms-table w-full min-w-[1200px]">
                        <thead>
                            <tr>
                                <th class="w-[5%] text-center">No</th>
                                <th class="w-[10%]">Audit Date</th>
                                <th class="w-[30%]">Auditee</th>
                                <th class="w-[20%]">Auditor</th>
                                <th class="w-[15%]">Departemen Auditee</th>
                                <th class="w-[10%]">Status</th>
                                <th class="w-[10%]">Action</th>
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
                        <h1 class="text-lg sm:text-2xl font-bold text-slate-800">Internal Audit</h1>
                        <p class="text-slate-500 text-sm">Create or edit Internal Audit audit details</p>
                    </div>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-xl border border-slate-200">
                <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50 rounded-t-xl">
                    <h2 class="text-lg font-bold text-slate-800">Schedule Information</h2>
                    <p class="text-slate-500 text-sm mt-1">Please fill in the required audit schedule details below.</p>
                </div>

                <div class="p-8">
                    <form id="createGenbaForm" action="{{ route('internal_audit.schedules.store') }}" method="POST">
                        @csrf
                        <input type="hidden" id="formScheduleId" name="schedule_id">

                        <div class="grid grid-cols-2 gap-x-4 sm:gap-x-8 gap-y-6">
                            <!-- Target Auditee Department -->
                            <div class="col-span-2 lg:col-span-1">
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Departemen Auditee <span class="text-red-500">*</span></label>
                                <x-searchable-select-multi
                                    name="auditee_dept"
                                    id="formAuditeeDept"
                                    label="Department"
                                    required="true"
                                    apiUrl="{{ route('genba.get_section') }}"
                                    updateEvent="update-auditee-dept"
                                    changeEvent="auditee-dept-changed"
                                    hideLabel="true"
                                    multiple="true"
                                    maxItems="5" />
                            </div>

                            <!-- Auditee -->
                            <div class="col-span-2 lg:col-span-1">
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Auditee <span class="text-red-500">*</span></label>
                                <x-searchable-select-multi
                                    name="auditee"
                                    id="formAuditee"
                                    label="Auditee"
                                    required="true"
                                    apiUrl="{{ route('internal_audit.get_users') }}"
                                    updateEvent="update-auditee"
                                    changeEvent="auditee-changed"
                                    hideLabel="true"
                                    multiple="true"
                                    maxItems="5" />
                            </div>

                            <!-- Auditor -->
                            <div class="col-span-2 lg:col-span-1">
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Auditor <span class="text-red-500">*</span></label>
                                <input type="text" id="formAuditorNiks" name="auditor_niks" required
                                    value="{{ Auth::user()->full_name ?? '' }}"
                                    class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm outline-none transition-all hover:border-blue-300">
                            </div>

                            <!-- Audit Date -->
                            <div class="col-span-2 lg:col-span-1">
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Tanggal Audit <span class="text-red-500">*</span></label>
                                <input type="date" id="formScheduleDate" name="schedule_date" required
                                    class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm outline-none transition-all hover:border-blue-300">
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
                                <span>Next</span>
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
                <p class="text-slate-500">Are you sure you want to delete this schedule? This action cannot be undone.</p>
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
    var currentStatusFilter = 'All';

    $(document).ready(function() {
        var table = $('#genbaFormTable').DataTable({
            dom: '<"overflow-x-auto"t>ip',
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('internal_audit.schedules') }}",
                type: 'POST',
                data: function(d) {
                    d._token = "{{ csrf_token() }}";
                    d.search = { value: $('#searchInput').val() };
                    d.status_filter = currentStatusFilter;
                }
            },
            columns: [
                { data: 'no', orderable: false, className: 'text-center font-base text-slate-700' },
                { data: 'schedule_date', className: 'text-slate-700' },
                { data: 'agenda_name', className: 'text-slate-700 font-medium' },
                { data: 'auditor_niks', className: 'text-slate-700' },
                { data: 'auditee_dept', className: 'text-slate-700' },
                { data: 'status', className: 'text-left' },
                { data: 'action', orderable: false, className: 'text-left' }
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

        // Form submit - AJAX save schedule and redirect to conduct page
        $('#createGenbaForm').on('submit', function(e) {
            e.preventDefault();
            
            let isValid = true;
            let errors = [];

            const dateVal = $('#formScheduleDate').val();
            if (!dateVal) {
                isValid = false;
                errors.push('Tanggal Audit is required');
                $('#formScheduleDate').addClass('border-red-500');
            } else {
                $('#formScheduleDate').removeClass('border-red-500');
            }

            const auditeeVal = $('#formAuditee').val();
            if (!auditeeVal) {
                isValid = false;
                errors.push('Auditee is required');
            }

            const auditorVal = $('#formAuditorNiks').val();
            if (!auditorVal) {
                isValid = false;
                errors.push('Auditor is required');
                $('#formAuditorNiks').addClass('border-red-500');
            } else {
                $('#formAuditorNiks').removeClass('border-red-500');
            }

            const deptVal = $('#formAuditeeDept').val();
            if (!deptVal) {
                isValid = false;
                errors.push('Departemen Auditee is required');
            }

            if (!isValid) {
                showToast(errors.join(', '), 'error');
                return false;
            }

            $('body').addClass('data-loading');
            $('#page-loader').removeClass('hidden');

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    schedule_id: $('#formScheduleId').val(),
                    agenda_name: auditeeVal,
                    schedule_date: dateVal,
                    auditor_niks: auditorVal,
                    auditee_dept: deptVal
                },
                success: function(response) {
                    $('body').removeClass('data-loading');
                    $('#page-loader').addClass('hidden');
                    
                    if (response.success) {
                        var redirectUrl = "{{ route('internal_audit.conduct', ':id') }}".replace(':id', response.schedule_id);
                        window.location.href = redirectUrl;
                    } else {
                        showToast(response.message, 'error');
                    }
                },
                error: function() {
                    $('body').removeClass('data-loading');
                    $('#page-loader').addClass('hidden');
                    showToast('Failed to save schedule.', 'error');
                }
            });
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

    window.editAuditSchedule = function(id) {
        $('#page-loader').removeClass('hidden');
        
        $.ajax({
            url: "{{ url('/internal-audit/schedules/detail') }}/" + id,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    var sched = response.schedule;
                    
                    $('#formScheduleId').val(sched.id);
                    
                    // Format date to YYYY-MM-DD
                    var dateVal = sched.audit_date ? sched.audit_date.split(' ')[0].split('T')[0] : '';
                    $('#formScheduleDate').val(dateVal);
                    
                    $('#formAuditorNiks').val(sched.auditor_names || '');
                    
                    // Dispatch searchable-select updates
                    window.dispatchEvent(new CustomEvent('update-auditee-dept', { 
                        detail: { 
                            id: sched.auditee_dept, 
                            name: sched.auditee_dept_name 
                        } 
                    }));
                    window.dispatchEvent(new CustomEvent('update-auditee', { 
                        detail: { 
                            id: sched.auditee, 
                            name: sched.auditee 
                        } 
                    }));
                    
                    // Transition views
                    $('#listView').addClass('opacity-0 transition-opacity duration-300');
                    setTimeout(function() {
                        $('#listView').addClass('hidden').removeClass('opacity-0');
                        $('#createView').removeClass('hidden').addClass('opacity-0');

                        setTimeout(function() {
                            $('#createView').removeClass('opacity-0').addClass('transition-opacity duration-300');
                            $('#page-loader').addClass('hidden');
                        }, 50);
                    }, 300);
                } else {
                    $('#page-loader').addClass('hidden');
                    showToast(response.message, 'error');
                }
            },
            error: function() {
                $('#page-loader').addClass('hidden');
                showToast('Failed to load schedule details.', 'error');
            }
        });
    };

    function showCreateForm() {
        // Reset inputs
        $('#createGenbaForm')[0].reset();
        $('#formScheduleId').val('');
        
        // Default Auditor and Date
        $('#formAuditorNiks').val('{{ Auth::user()->full_name ?? "" }}');
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const day = String(today.getDate()).padStart(2, '0');
        $('#formScheduleDate').val(`${year}-${month}-${day}`);

        // Reset searchable selects
        window.dispatchEvent(new CustomEvent('update-auditee-dept', { detail: { id: '', name: '' } }));
        window.dispatchEvent(new CustomEvent('update-auditee', { detail: { id: '', name: '' } }));

        // Transition views
        $('#listView').addClass('opacity-0 transition-opacity duration-300');
        setTimeout(function() {
            $('#listView').addClass('hidden').removeClass('opacity-0');
            $('#createView').removeClass('hidden').addClass('opacity-0');

            setTimeout(function() {
                $('#createView').removeClass('opacity-0').addClass('transition-opacity duration-300');
            }, 50);
        }, 300);
    }

    function hideCreateForm() {
        $('#createView').addClass('opacity-0 transition-opacity duration-300');

        setTimeout(function() {
            $('#createView').addClass('hidden').removeClass('opacity-0');
            $('#listView').removeClass('hidden').addClass('opacity-0');

            setTimeout(function() {
                $('#listView').removeClass('opacity-0').addClass('transition-opacity duration-300');
            }, 50);

            $('#createGenbaForm')[0].reset();
        }, 300);
    }

    function filterByStatus(statusFilter) {
        currentStatusFilter = statusFilter;

        $('.status-tab').removeClass('text-blue-600 border-blue-600').addClass('text-slate-600 border-transparent');

        if (statusFilter === 'All') {
            $('#btnAll').addClass('text-blue-600 border-blue-600').removeClass('text-slate-600 border-transparent');
        } else if (statusFilter === 'Scheduled') {
            $('#btnScheduled').addClass('text-blue-600 border-blue-600').removeClass('text-slate-600 border-transparent');
        } else {
            $('#btnDone').addClass('text-blue-600 border-blue-600').removeClass('text-slate-600 border-transparent');
        }

        $('#genbaFormTable').DataTable().ajax.reload();
    }

    var deleteTargetSysId = null;

    // Map global function referenced by datatable generated HTML delete button
    window.deleteAuditSchedule = function(id) {
        deleteTargetSysId = id;
        $('#deleteConfirmModal').removeClass('hidden');
    };

    function closeDeleteModal() {
        $('#deleteConfirmModal').addClass('hidden');
        deleteTargetSysId = null;
    }

    function executeDelete() {
        if (!deleteTargetSysId) return;

        var id = deleteTargetSysId;
        closeDeleteModal();
        
        $('body').addClass('data-loading');
        $('#page-loader').removeClass('hidden');

        $.ajax({
            url: "{{ url('/internal-audit/schedules/delete') }}/" + id,
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                $('body').removeClass('data-loading');
                $('#page-loader').addClass('hidden');

                if (response.success) {
                    showToast(response.message, 'success');
                    $('#genbaFormTable').DataTable().ajax.reload();
                } else {
                    showToast(response.message, 'error');
                }
            },
            error: function() {
                $('body').removeClass('data-loading');
                $('#page-loader').addClass('hidden');
                showToast('Failed to delete schedule.', 'error');
            }
        });
    }
</script>
@endpush