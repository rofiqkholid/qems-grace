@extends('layouts.app')

@section('title', 'Verifikasi CAR Audit Internal')

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
                <h1 class="text-2xl font-bold text-slate-800">Verifikasi CAR Audit Internal</h1>
                <p class="text-slate-500 mt-1">Verifikasi CAR Audit Internal (Superior and Auditor Approval)</p>
            </div>
            
            <!-- Selector Tabs -->
            <div class="flex border-b border-slate-200 md:mr-24">
                <button type="button" onclick="setRoleTab('superior')" id="tab-superior" class="px-5 py-2.5 text-sm font-semibold border-b-2 border-blue-500 text-blue-600 transition-all duration-200 outline-none flex items-center">
                    Verif by Superior
                    <span id="count-superior" class="ml-2 px-2 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-600">{{ $superiorCount ?? 0 }}</span>
                </button>
                <button type="button" onclick="setRoleTab('auditor')" id="tab-auditor" class="px-5 py-2.5 text-sm font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition-all duration-200 outline-none flex items-center">
                    Verif by Auditor
                    <span id="count-auditor" class="ml-2 px-2 py-0.5 text-xs font-semibold rounded-full bg-slate-100 text-slate-600">{{ $auditorCount ?? 0 }}</span>
                </button>
                <button type="button" onclick="setRoleTab('closed')" id="tab-closed" class="px-5 py-2.5 text-sm font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition-all duration-200 outline-none flex items-center">
                    Verif by QMR
                    <span id="count-closed" class="ml-2 px-2 py-0.5 text-xs font-semibold rounded-full bg-slate-100 text-slate-600">{{ $closedCount ?? 0 }}</span>
                </button>
                <button type="button" onclick="setRoleTab('all')" id="tab-all" class="px-5 py-2.5 text-sm font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition-all duration-200 outline-none flex items-center">
                    All Data Audit
                    <span id="count-all" class="ml-2 px-2 py-0.5 text-xs font-semibold rounded-full bg-slate-100 text-slate-600">{{ $allCount ?? 0 }}</span>
                </button>
            </div>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-lg border border-slate-200">
            <!-- Filter Section -->
            <div class="p-6 border-b border-slate-200 bg-slate-50/50">
                <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                    <!-- Search -->
                    <div class="flex-1 min-w-[200px]">
                        <div class="relative">
                            <input type="text" id="searchInput" placeholder="Search by DocNum, finding, dept..."
                                class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
                            <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        </div>
                    </div>

                    <!-- Date From -->
                    <div class="w-full lg:w-auto">
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
                    <div class="w-full lg:w-auto">
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
                    <div class="w-full lg:w-[200px]">
                        <x-searchable-select
                            name="dept"
                            id="deptFilter"
                            label="Department"
                            :initialOptions="collect($departments)->map(fn($d) => ['id' => $d, 'name' => $d])->values()->toArray()"
                            valueField="name"
                            hideLabel="true"
                            placeholder="Select Department..." />
                    </div>

                    <!-- Reset Button -->
                    <div class="w-full lg:w-auto">
                        <button type="button" id="btnReset"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 text-sm font-medium transition-colors h-[38px]">
                            <i class="fa-solid fa-rotate-right text-sm"></i>
                            Reset
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table Section -->
            <div class="overflow-x-auto p-6">
                <table id="findingsTable" class="qms-table w-full min-w-[1200px]">
                    <thead>
                        <tr>
                            <th class="w-[5%] text-center">No</th>
                            <th class="w-[12%] text-left">Req Number</th>
                            <th class="w-[10%] text-left">Department</th>
                            <th class="w-[12%] text-left">Finding Category</th>
                            <th class="w-[12%] text-left">Auditor</th>
                            <th class="w-[12%] text-left">Auditee</th>
                            <th class="w-[12%] text-left">Superior</th>
                            <th class="w-[15%] text-left">Status</th>
                            <th class="w-[10%] text-left">Action</th>
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

<!-- Generic Confirmation Modal -->
<div id="confirmationModal" class="fixed inset-0 z-[60] hidden">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-900/60 transition-opacity" onclick="closeConfirmationModal()"></div>

    <!-- Modal -->
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-md transform transition-all p-6 text-center border border-slate-100 shadow-xl">
            <div id="modalIcon" class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-5">
                <!-- Icon injected by JS -->
            </div>

            <h3 id="modalTitle" class="text-xl font-bold text-slate-800 mb-2"></h3>
            <p id="modalMessage" class="text-base text-slate-600 mb-6 leading-relaxed"></p>

            <input type="hidden" id="confirmationId">

            <div class="flex gap-3 justify-center">
                <button type="button" onclick="closeConfirmationModal()"
                    class="px-5 py-2.5 bg-slate-100 text-slate-700 font-medium rounded-xl hover:bg-slate-200 transition-colors text-sm">
                    Cancel
                </button>
                <button type="button" id="confirmBtn" onclick="submitConfirmation()"
                    class="px-5 py-2.5 text-white font-medium rounded-xl transition-colors text-sm">
                    Confirm
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentAction = ''; 
    let currentCarId = null;
    let currentRole = sessionStorage.getItem('activeVerificationTab') || 'superior';

    function setRoleTab(role) {
        currentRole = role;
        sessionStorage.setItem('activeVerificationTab', role);
        
        // Reset all tabs
        $('#tab-superior, #tab-auditor, #tab-closed, #tab-all').removeClass('border-blue-500 text-blue-600 border-emerald-600 text-emerald-600').addClass('border-transparent text-slate-500 hover:text-slate-800');
        $('#count-superior, #count-auditor, #count-closed, #count-all').removeClass('bg-blue-100 text-blue-600 bg-emerald-100 text-emerald-600').addClass('bg-slate-100 text-slate-600');

        // Set active tab (always blue)
        $('#tab-' + role).removeClass('border-transparent text-slate-500 hover:text-slate-800').addClass('border-blue-500 text-blue-600');
        $('#count-' + role).removeClass('bg-slate-100 text-slate-600').addClass('bg-blue-100 text-blue-600');

        // Reload table if initialized
        if ($.fn.DataTable.isDataTable('#findingsTable')) {
            $('#findingsTable').DataTable().ajax.reload();
        }
    }

    $(document).ready(function() {
        // Initialize tab styles
        setRoleTab(currentRole);

        var table = $('#findingsTable').DataTable({
            dom: '<"overflow-x-auto"t>ip',
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('internal_audit.verification.table') }}",
                type: 'POST',
                data: function(d) {
                    d._token = "{{ csrf_token() }}";
                    d.search.value = $('#searchInput').val();
                    d.date_from = $('#dateFrom').val();
                    d.date_to = $('#dateTo').val();
                    d.dept = $('#deptFilter').val();
                    d.role = currentRole;
                },
                dataSrc: function(json) {
                    $('#count-superior').text(json.superiorCount || 0);
                    $('#count-auditor').text(json.auditorCount || 0);
                    $('#count-closed').text(json.closedCount || 0);
                    $('#count-all').text(json.allCount || 0);
                    return json.data;
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
                    className: 'font-base text-slate-900',
                    render: function(data) {
                        return data ? `<span class="font-semibold text-slate-800">${data}</span>` : '-';
                    }
                },

                {
                    data: 'department',
                    className: 'text-slate-700 font-semibold',
                },
                {
                    data: 'finding_category',
                    className: 'text-slate-700',
                    render: function(data) {
                        return data || '-';
                    }
                },
                {
                    data: 'auditor',
                    className: 'text-slate-700',
                },
                {
                    data: 'auditee',
                    className: 'text-slate-700',
                },
                {
                    data: 'superior',
                    className: 'text-slate-700',
                },
                {
                    data: 'status_badge',
                    orderable: false,
                    className: 'text-left',
                },
                {
                    data: 'action',
                    orderable: false,
                    className: 'text-left',
                }
            ],
            order: [
                [1, 'desc']
            ],
            pageLength: 10,
            language: {
                emptyTable: '<div class="text-center py-8 text-slate-500">No internal audit data ready for verification</div>',
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
            window.dispatchEvent(new CustomEvent('update-dept', {
                detail: { id: '', name: '' }
            }));
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

        // Handle initial date values (if any)
        if ($('#dateFrom').val()) $('#dateFrom').attr('data-has-value', 'true');
        if ($('#dateTo').val()) $('#dateTo').attr('data-has-value', 'true');
    });

    function previewCar(encryptedId) {
        window.location.href = "{{ route('internal_audit.action_report.preview', '') }}/" + encryptedId;
    }

    let approvalRole = '';

    function approveCarAction(id, role) {
        currentAction = 'approve';
        currentCarId = id;
        approvalRole = role || currentRole;

        const modal = document.getElementById('confirmationModal');
        modal.querySelector('#modalTitle').innerText = 'Verify & Approve CAR';
        modal.querySelector('#modalMessage').innerHTML = 'Are you sure you want to verify and sign off this CAR?<br>This will lock the action plan and mark it as approved.';

        const iconContainer = modal.querySelector('#modalIcon');
        iconContainer.className = 'w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-5';
        iconContainer.innerHTML = `<svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>`;

        const confirmBtn = document.getElementById('confirmBtn');
        confirmBtn.className = 'px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-xl transition-colors text-sm';
        confirmBtn.innerText = 'Yes, Approve';

        modal.classList.remove('hidden');
    }

    function rollbackCarAction(id) {
        currentAction = 'rollback';
        currentCarId = id;

        const modal = document.getElementById('confirmationModal');
        modal.querySelector('#modalTitle').innerText = 'Rollback CAR Approval';
        modal.querySelector('#modalMessage').innerHTML = 'Are you sure you want to rollback the approval for this CAR?<br>This will change the status back to Under Review.';

        const iconContainer = modal.querySelector('#modalIcon');
        iconContainer.className = 'w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-5';
        iconContainer.innerHTML = `<svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 0 1 8 8v2M3 10l6 6m-6-6l6-6"></path></svg>`;

        const confirmBtn = document.getElementById('confirmBtn');
        confirmBtn.className = 'px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-medium rounded-xl transition-colors text-sm';
        confirmBtn.innerText = 'Yes, Rollback';

        modal.classList.remove('hidden');
    }

    function rejectCarAction(id, role) {
        currentAction = 'reject';
        currentCarId = id;
        approvalRole = role || currentRole;

        const modal = document.getElementById('confirmationModal');
        modal.querySelector('#modalTitle').innerText = 'Reject & Rollback CAR';
        modal.querySelector('#modalMessage').innerHTML = 'Are you sure you want to reject this CAR Action Plan?<br>This will return the action plan to draft status for correction.';

        const iconContainer = modal.querySelector('#modalIcon');
        iconContainer.className = 'w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-5';
        iconContainer.innerHTML = `<svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>`;

        const confirmBtn = document.getElementById('confirmBtn');
        confirmBtn.className = 'px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-xl transition-colors text-sm';
        confirmBtn.innerText = 'Yes, Reject';

        modal.classList.remove('hidden');
    }

    function closeConfirmationModal() {
        document.getElementById('confirmationModal').classList.add('hidden');
        currentCarId = null;
        currentAction = '';
    }

    function submitConfirmation() {
        if (!currentCarId) return;

        const confirmBtn = document.getElementById('confirmBtn');
        const originalText = confirmBtn.innerText;
        confirmBtn.disabled = true;
        confirmBtn.innerText = 'Processing...';

        let url = '';
        let payload = {};

        if (currentAction === 'approve') {
            url = "{{ route('internal_audit.cars.approve') }}";
            payload = {
                _token: "{{ csrf_token() }}",
                car_id: currentCarId,
                role: approvalRole
            };
        } else if (currentAction === 'rollback') {
            url = "{{ route('internal_audit.cars.rollback') }}";
            payload = {
                _token: "{{ csrf_token() }}",
                car_id: currentCarId
            };
        } else if (currentAction === 'reject') {
            url = "{{ route('internal_audit.cars.reject') }}";
            payload = {
                _token: "{{ csrf_token() }}",
                car_id: currentCarId
            };
        }

        $.ajax({
            url: url,
            type: "POST",
            data: payload,
            success: function(response) {
                closeConfirmationModal();
                if (response.success) {
                    showToast(response.message, 'success');
                    $('#findingsTable').DataTable().ajax.reload(null, false);
                } else {
                    showToast(response.message || 'Verification failed.', 'warning');
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
@endpush
@endsection
