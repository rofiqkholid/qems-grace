@extends('layouts.app')

@section('title', 'Internal Audit Verification')

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
            <h1 class="text-2xl font-bold text-slate-800">Internal Audit Verification</h1>
            <p class="text-slate-500 mt-1">Verifikasi Audit Internal (Approval)</p>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-lg border border-slate-200">
            <!-- Filter Section -->
            <div class="p-6 border-b border-slate-200 bg-slate-50/50">
                <div class="grid grid-cols-2 lg:flex lg:flex-row lg:flex-wrap lg:items-center gap-3">
                    <!-- Search -->
                    <div class="col-span-2 lg:col-span-auto lg:flex-1 lg:min-w-[200px]">
                        <div class="relative">
                            <input type="text" id="searchInput" placeholder="Search CAR Number, Department, Auditor, Category..."
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
                    <div class="col-span-2 lg:col-span-auto w-full lg:w-auto min-w-0 lg:min-w-[200px]">
                        <x-searchable-select
                            name="dept"
                            id="deptFilter"
                            label="Department"
                            :initialOptions="collect($departments)->map(fn($d) => ['id' => $d, 'name' => $d])->values()->toArray()"
                            valueField="name"
                            hideLabel="true" />
                    </div>

                    <!-- Reset Button -->
                    <div class="col-span-2 lg:col-span-auto">
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
                <table id="findingsTable" class="qms-table w-full min-w-[1500px]">
                    <thead>
                        <tr>
                            <th class="w-[4%] text-center">No</th>
                            <th class="w-[15%]">CAR Number</th>
                            <th class="w-[6%] text-left">Preview</th>
                            <th class="w-[12%]">Audit Date</th>
                            <th class="w-[13%]">Department</th>
                            <th class="w-[15%]">Clause</th>
                            <th class="w-[14%]">Auditor</th>
                            <th class="w-[14%]">Auditee</th>
                            <th class="w-[7%]">Approve</th>
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
                <button type="button" onclick="closeConfirmationModal()"
                    class="px-5 py-2.5 bg-slate-100 text-slate-700 font-medium rounded-xl hover:bg-slate-200 transition-colors">
                    Cancel
                </button>
                <button type="button" id="confirmBtn" onclick="submitConfirmation()"
                    class="px-5 py-2.5 bg-green-600 text-white font-medium rounded-xl hover:bg-green-700 transition-colors">
                    Yes, Approve
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
                url: "{{ route('internal_audit.verification.table') }}",
                type: 'POST',
                data: function(d) {
                    d._token = "{{ csrf_token() }}";
                    d.search = $('#searchInput').val();
                    d.date_from = $('#dateFrom').val();
                    d.date_to = $('#dateTo').val();
                    d.dept = $('#deptFilter').val();
                }
            },
            columns: [
                {
                    data: 'no',
                    orderable: false,
                    className: 'text-center font-base text-slate-700'
                },
                {
                    data: 'req_number',
                    className: 'font-base text-slate-900 text-sm',
                    render: function(data) {
                        return data || '-';
                    }
                },
                {
                    data: 'id',
                    orderable: false,
                    className: 'text-left',
                    render: function(data) {
                        return `
                            <div class="flex items-center justify-start w-full">
                                <button onclick="document_preview('${data}')" class="w-9 h-9 inline-flex items-center justify-center text-blue-500 bg-blue-50 hover:bg-blue-100 hover:text-blue-600 transition-colors ring-1 ring-blue-100" title="Preview CAR Report">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500" viewBox="0 0 24 24" fill="none">
                                        <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="currentColor"></path>
                                        <path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="currentColor"></path>
                                        <path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="currentColor"></path>
                                    </svg>
                                </button>
                            </div>
                        `;
                    }
                },
                {
                    data: 'audit_date',
                    className: 'text-slate-700',
                    render: function(data) {
                        return '<span class="text-sm">' + data + '</span>';
                    }
                },
                {
                    data: 'department',
                    className: 'text-slate-700',
                    render: function(data) {
                        return '<span class="text-sm">' + data + '</span>';
                    }
                },
                {
                    data: 'clause_title',
                    className: 'text-slate-700',
                    render: function(data) {
                        return '<span class="text-sm">' + (data || '-') + '</span>';
                    }
                },

                {
                    data: 'auditor',
                    className: 'text-slate-700',
                    render: function(data) {
                        return '<span class="text-sm">' + (data || '-') + '</span>';
                    }
                },
                {
                    data: 'auditee',
                    className: 'text-slate-700',
                    render: function(data) {
                        return '<span class="text-sm">' + (data || '-') + '</span>';
                    }
                },

                {
                    data: 'action',
                    orderable: false,
                    className: 'text-left'
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
            $('#dateFrom').val('').removeAttr('data-has-value');
            $('#dateTo').val('').removeAttr('data-has-value');
            $('#deptFilter').val('');
            table.ajax.reload();
        });

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

        if ($('#dateFrom').val()) $('#dateFrom').attr('data-has-value', 'true');
        if ($('#dateTo').val()) $('#dateTo').attr('data-has-value', 'true');
    });

    function document_preview(id) {
        window.location.href = "{{ route('internal_audit.action_report.preview', '') }}/" + id;
    }

    let currentAction = ''; // 'approve' or 'rollback'

    function openApproveModal(id) {
        currentAction = 'approve';
        document.getElementById('confirmationId').value = id;

        const modal = document.getElementById('confirmationModal');
        modal.querySelector('#modalTitle').innerText = 'Auditee Superior Verification';
        modal.querySelector('#modalMessage').innerHTML = 'Are you sure you want to verify this corrective action report as Auditee Superior?<br>This action will sign off the report.';

        // Icon
        const iconContainer = modal.querySelector('#modalIcon');
        iconContainer.className = 'w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-5';
        iconContainer.innerHTML = `<svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>`;

        const confirmBtn = document.getElementById('confirmBtn');
        confirmBtn.className = 'px-5 py-2.5 bg-emerald-600 text-white font-medium rounded-xl hover:bg-emerald-700 transition-colors';
        confirmBtn.innerText = 'Yes, Verify';
        confirmBtn.disabled = false;

        modal.classList.remove('hidden');
    }

    function rollbackCar(id, isReject = false) {
        currentAction = 'rollback';
        document.getElementById('confirmationId').value = id;

        const modal = document.getElementById('confirmationModal');
        const iconContainer = modal.querySelector('#modalIcon');
        const confirmBtn = document.getElementById('confirmBtn');

        if (isReject) {
            modal.querySelector('#modalTitle').innerText = 'Reject CAR Action Plan?';
            modal.querySelector('#modalMessage').innerHTML = 'Are you sure you want to reject this CAR Action Plan?<br>Status will be set back to Open so the auditee can edit it again.';
            
            iconContainer.className = 'w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-5';
            iconContainer.innerHTML = `<svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>`;
            
            confirmBtn.className = 'px-5 py-2.5 bg-red-600 text-white font-medium rounded-xl hover:bg-red-700 transition-colors';
            confirmBtn.innerText = 'Yes, Reject';
        } else {
            modal.querySelector('#modalTitle').innerText = 'Rollback CAR Approval?';
            modal.querySelector('#modalMessage').innerHTML = 'Are you sure you want to rollback all approvals for this CAR?<br>All approval signatures will be cleared and status set back to Under Review.';
            
            iconContainer.className = 'w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-5';
            iconContainer.innerHTML = `<svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 0 1 8 8v2M3 10l6 6m-6-6l6-6"></path></svg>`;
            
            confirmBtn.className = 'px-5 py-2.5 bg-amber-600 text-white font-medium rounded-xl hover:bg-amber-700 transition-colors';
            confirmBtn.innerText = 'Yes, Rollback';
        }
        confirmBtn.disabled = false;

        modal.classList.remove('hidden');
    }

    function closeConfirmationModal() {
        document.getElementById('confirmationModal').classList.add('hidden');
        document.getElementById('confirmationId').value = '';
    }

    function submitConfirmation() {
        const id = document.getElementById('confirmationId').value;
        const confirmBtn = document.getElementById('confirmBtn');
        const originalConfirmText = confirmBtn.innerText;

        let url = '';
        let data = {
            _token: "{{ csrf_token() }}",
            car_id: id
        };

        if (currentAction === 'approve') {
            url = "{{ route('internal_audit.cars.approve') }}";
            data.role = 'dept'; // Always approve as Auditee Superior (dept)
        } else if (currentAction === 'rollback') {
            url = "{{ route('internal_audit.cars.rollback') }}";
        }

        // Show loading state
        confirmBtn.disabled = true;
        confirmBtn.innerText = 'Processing...';

        $.ajax({
            url: url,
            type: "POST",
            data: data,
            success: function(response) {
                closeConfirmationModal();
                if (response.success) {
                    showToast(response.message, 'success');
                    $('#findingsTable').DataTable().ajax.reload();
                } else {
                    showToast(response.message || 'Verification failed.', 'error');
                }
            },
            error: function(xhr) {
                closeConfirmationModal();
                showToast('Something went wrong. Please try again.', 'error');
            },
            complete: function() {
                confirmBtn.disabled = false;
                confirmBtn.innerText = originalConfirmText;
            }
        });
    }
</script>
@endpush
