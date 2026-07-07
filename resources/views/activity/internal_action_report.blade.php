@extends('layouts.app')

@section('title', 'Action Report')

@section('content')
@include('layouts.sidebar')
@include('components.toast')

<!-- Main Content -->
<div class="lg:ml-20 min-h-screen flex flex-col bg-slate-50">
    @include('layouts.header')

    <!-- Page Content -->
    <main class="flex-1 p-6">
        <!-- Page Title & Tabs -->
        <div class="mb-6 flex flex-col md:flex-row justify-between md:items-end gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Action Report</h1>
                <p class="text-slate-500 mt-1">Manage and track internal audit action reports (CAR)</p>
            </div>
            
            <!-- Selector Tabs -->
            <div class="flex border-b border-slate-200 md:mr-24">
                <button type="button" onclick="setCategoryTab('CAR')" id="tab-car" class="px-5 py-2.5 text-sm font-semibold border-b-2 border-blue-500 text-blue-600 transition-all duration-200 outline-none flex items-center">
                    Minor & Mayor
                    <span id="count-car" class="ml-2 px-2 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-600">{{ $carCount ?? 0 }}</span>
                </button>
                <button type="button" onclick="setCategoryTab('OKE')" id="tab-oke" class="px-5 py-2.5 text-sm font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition-all duration-200 outline-none flex items-center">
                    OKE
                    <span id="count-oke" class="ml-2 px-2 py-0.5 text-xs font-semibold rounded-full bg-slate-100 text-slate-600">{{ $okeCount ?? 0 }}</span>
                </button>
                <button type="button" onclick="setCategoryTab('OFI')" id="tab-ofi" class="px-5 py-2.5 text-sm font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition-all duration-200 outline-none flex items-center">
                    OFI
                    <span id="count-ofi" class="ml-2 px-2 py-0.5 text-xs font-semibold rounded-full bg-slate-100 text-slate-600">{{ $ofiCount ?? 0 }}</span>
                </button>
            </div>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-lg border border-slate-200">
            <!-- Filter Section -->
            <div class="p-6 border-b border-slate-200 bg-slate-50/50">
                <div class="grid grid-cols-2 lg:flex lg:flex-row lg:flex-wrap lg:items-center gap-3">
                    <!-- Search -->
                    <div class="col-span-2 lg:col-span-auto lg:flex-1 lg:min-w-[200px]">
                        <div class="relative">
                            <input type="text" id="searchInput" placeholder="Search CAR Number, Department..."
                                class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                            <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        </div>
                    </div>

                    <!-- Date From -->
                    <div class="col-span-1 lg:col-span-auto w-full lg:w-auto">
                        <div class="date-input-container w-full lg:w-auto">
                            <input type="date" id="dateFrom" oninput="this.setAttribute('data-has-value', this.value ? 'true' : '')" onchange="this.setAttribute('data-has-value', this.value ? 'true' : '')" onfocus="try { this.showPicker(); } catch(e) {}" onclick="try { this.showPicker(); } catch(e) {}" onkeydown="return false;"
                                class="w-full lg:w-[150px] pl-4 pr-10 py-2 border border-slate-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
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
                                class="w-full lg:w-[150px] pl-4 pr-10 py-2 border border-slate-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
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
                            updateEvent="reset-dept"
                            hideLabel="true" />
                    </div>

                    <!-- Finding Category Filter (Hidden input to store state from tabs) -->
                    <input type="hidden" id="categoryFilter" value="CAR">

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
                <table id="findingsTable" class="qms-table w-full min-w-[1000px]">
                    <thead>
                        <tr>
                            <th class="w-[5%] text-center">No</th>
                            <th class="w-[15%]">Req Number</th>
                            <th class="w-[10%]">Department</th>
                            <th class="w-[10%]">Finding Category</th>
                            <th class="w-[15%]">Auditor</th>
                            <th class="w-[35%]">Auditee</th>
                            <th class="w-[10%]">Action</th>
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

<!-- Saran Modal -->
<div id="saranModal" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-900/60 transition-opacity" onclick="closeSaranModal()"></div>

    <!-- Modal -->
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-lg transform transition-all shadow-xl">
            <!-- Header -->
            <div class="p-6 border-b border-slate-200 flex justify-between items-center">
                <h3 class="text-lg font-bold text-slate-800">Isi Saran / Note</h3>
                <button type="button" onclick="closeSaranModal()" class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-all duration-200 outline-none">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            
            <!-- Body -->
            <form id="saranForm" onsubmit="submitSaran(event)">
                <div class="p-6">
                    <input type="hidden" id="saranDetailId" name="detail_id">
                    <div class="mb-4">
                        <label for="saranText" class="block text-sm font-semibold text-slate-700 mb-2">Saran / Note</label>
                        <textarea id="saranText" name="note" rows="6" class="w-full p-3 border border-slate-300 rounded-xl focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none resize-none bg-white" placeholder="Masukkan saran atau catatan di sini..."></textarea>
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex gap-3 p-6 pt-0 border-t border-slate-100 mt-4">
                    <button type="button" onclick="closeSaranModal()"
                        class="flex-1 px-4 py-3 bg-slate-100 text-slate-700 rounded-xl font-semibold hover:bg-slate-200 transition-colors text-sm">
                        Batal
                    </button>
                    <button type="submit" id="btnSubmitSaran"
                        class="flex-1 px-4 py-3 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition-colors text-sm">
                        Simpan
                    </button>
                </div>
            </form>
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
    $(document).ready(function() {
        // Load persisted tab from localStorage
        const initialTab = localStorage.getItem('internal_action_report_active_tab') || 'CAR';
        $('#categoryFilter').val(initialTab);
        
        // Update tab styles based on persisted tab
        const tabs = ['CAR', 'OKE', 'OFI'];
        tabs.forEach(tab => {
            const btn = $(`#tab-${tab.toLowerCase()}`);
            const countSpan = $(`#count-${tab.toLowerCase()}`);
            if (tab === initialTab) {
                btn.removeClass('border-transparent text-slate-500 hover:text-slate-800')
                   .addClass('border-blue-500 text-blue-600');
                countSpan.removeClass('bg-slate-100 text-slate-600')
                         .addClass('bg-blue-100 text-blue-600');
            } else {
                btn.removeClass('border-blue-500 text-blue-600')
                   .addClass('border-transparent text-slate-500 hover:text-slate-800');
                countSpan.removeClass('bg-blue-100 text-blue-600')
                         .addClass('bg-slate-100 text-slate-600');
            }
        });
        
        // Update header label text based on persisted tab
        if (initialTab === 'OKE' || initialTab === 'OFI') {
            $('#findingsTable thead th').eq(1).text('Note');
        } else {
            $('#findingsTable thead th').eq(1).text('Req Number');
        }

        var table = $('#findingsTable').DataTable({
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
                    className: 'font-base text-slate-900',
                    render: function(data, type, row) {
                        if (row.finding_category === 'OKE' || row.finding_category === 'OK' || row.finding_category === 'OFI') {
                            return row.note || '-';
                        }
                        return data || '-';
                    }
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
            table.ajax.reload();
        });

        // Reset button
        $('#btnReset').click(function() {
            $('#searchInput').val('');
            $('#dateFrom').val('').removeAttr('data-has-value');
            $('#dateTo').val('').removeAttr('data-has-value');
            
            // Reset searchable-select components
            window.dispatchEvent(new CustomEvent('reset-dept', { detail: '' }));
            
            setCategoryTab('CAR');
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

    // Tab switching function
    function setCategoryTab(category) {
        $('#categoryFilter').val(category);
        localStorage.setItem('internal_action_report_active_tab', category);
        
        // Dynamically change table header
        if (category === 'OKE' || category === 'OFI') {
            $('#findingsTable thead th').eq(1).text('Note');
        } else {
            $('#findingsTable thead th').eq(1).text('Req Number');
        }
        
        const tabs = ['CAR', 'OKE', 'OFI'];
        tabs.forEach(tab => {
            const btn = $(`#tab-${tab.toLowerCase()}`);
            const countSpan = $(`#count-${tab.toLowerCase()}`);
            if (tab === category) {
                btn.removeClass('border-transparent text-slate-500 hover:text-slate-800')
                   .addClass('border-blue-500 text-blue-600');
                countSpan.removeClass('bg-slate-100 text-slate-600')
                         .addClass('bg-blue-100 text-blue-600');
            } else {
                btn.removeClass('border-blue-500 text-blue-600')
                   .addClass('border-transparent text-slate-500 hover:text-slate-800');
                countSpan.removeClass('bg-blue-100 text-blue-600')
                         .addClass('bg-slate-100 text-slate-600');
            }
        });
        
        $('#findingsTable').DataTable().ajax.reload();
    }

    // Saran modal logic
    function openSaranModal(detailId, currentNote) {
        $('#saranDetailId').val(detailId);
        $('#saranText').val(currentNote);
        $('#saranModal').removeClass('hidden');
    }

    function closeSaranModal() {
        $('#saranModal').addClass('hidden');
        $('#saranDetailId').val('');
        $('#saranText').val('');
    }

    function submitSaran(e) {
        e.preventDefault();
        
        var detailId = $('#saranDetailId').val();
        var note = $('#saranText').val();

        $('#btnSubmitSaran').prop('disabled', true).text('Menyimpan...');

        $.ajax({
            url: "{{ route('internal_audit.detail.save_note') }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                detail_id: detailId,
                note: note
            },
            success: function(response) {
                $('#btnSubmitSaran').prop('disabled', false).text('Simpan');
                closeSaranModal();

                if (response.success) {
                    showToast('Saran berhasil disimpan.', 'success');
                    $('#findingsTable').DataTable().ajax.reload();
                } else {
                    showToast(response.message || 'Gagal menyimpan saran.', 'error');
                }
            },
            error: function() {
                $('#btnSubmitSaran').prop('disabled', false).text('Simpan');
                showToast('Terjadi kesalahan saat menghubungi server.', 'error');
            }
        });
    }
</script>
@endpush