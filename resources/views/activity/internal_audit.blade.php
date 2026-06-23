@extends('layouts.app')

@section('title', 'Internal Audit')

@section('content')
@include('layouts.sidebar')
@include('components.toast')

<div class="lg:ml-20 min-h-screen flex flex-col bg-slate-50" x-data="csAuditApp()">
    <style>
        [x-cloak] {
            display: none !important;
        }
        .qms-table th, .qms-table td {
            vertical-align: middle !important;
            padding: 0.5rem 0.75rem !important;
        }
    </style>
    @include('layouts.header')

    <!-- Page Content -->
    <main class="flex-1 p-6">
        <!-- Page Title & Stats -->
        <div x-show="tab === 'schedules'" class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Internal Audit</h1>
                <p class="text-slate-500 mt-1">Plan agendas, conduct standard compliance audits, and manage CAR workflows</p>
            </div>
        </div>

        <!-- Section 1: Agenda & Schedules -->
        <div x-show="tab === 'schedules'" class="space-y-6">
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                <!-- Filter Section -->
                <div class="p-6 border-b border-slate-200 bg-slate-50/50">
                    <div class="flex flex-wrap items-center gap-3">
                        <!-- Search -->
                        <div class="flex-1 min-w-[200px]">
                            <div class="relative">
                                <input type="text" id="scheduleSearch" placeholder="Search..."
                                    class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                                <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            </div>
                        </div>

                        <!-- Spacer -->
                        <div class="flex-1"></div>

                        <!-- Create Button -->
                        <button type="button" @click="openCreateSchedule()"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition-colors">
                            <i class="fa-solid fa-plus text-sm"></i>
                            New Schedule
                        </button>
                    </div>
                </div>

                <!-- Status Tabs -->
                <div class="grid grid-cols-3 border-b border-slate-200">
                    <button type="button" 
                        @click="statusFilter = 'All'; $('#scheduleTable').DataTable().ajax.reload();"
                        class="py-4 px-6 text-sm font-medium transition-all border-b-2"
                        :class="statusFilter === 'All' ? 'text-blue-600 border-blue-600 bg-slate-50/30 font-semibold' : 'text-slate-600 border-transparent hover:text-blue-600 hover:bg-slate-50'">
                        All
                    </button>
                    <button type="button" 
                        @click="statusFilter = 'Scheduled'; $('#scheduleTable').DataTable().ajax.reload();"
                        class="py-4 px-6 text-sm font-medium transition-all border-b-2"
                        :class="statusFilter === 'Scheduled' ? 'text-blue-600 border-blue-600 bg-slate-50/30 font-semibold' : 'text-slate-600 border-transparent hover:text-blue-600 hover:bg-slate-50'">
                        Scheduled
                    </button>
                    <button type="button" 
                        @click="statusFilter = 'Done'; $('#scheduleTable').DataTable().ajax.reload();"
                        class="py-4 px-6 text-sm font-medium transition-all border-b-2"
                        :class="statusFilter === 'Done' ? 'text-blue-600 border-blue-600 bg-slate-50/30 font-semibold' : 'text-slate-600 border-transparent hover:text-blue-600 hover:bg-slate-50'">
                        Done
                    </button>
                </div>

                <!-- Schedule Table -->
                <div class="p-6">
                    <div class="overflow-x-auto lg:overflow-x-visible">
                        <table id="scheduleTable" class="qms-table w-full min-w-[1000px]">
                            <thead>
                                <tr>
                                    <th class="w-[5%] text-center">No</th>
                                    <th class="w-[15%]">Genba Date</th>
                                    <th class="w-[25%]">Agenda Name</th>
                                    <th class="w-[20%]">Auditor</th>
                                    <th class="w-[15%]">Auditee Department</th>
                                    <th class="w-[10%]">Status</th>
                                    <th class="w-[10%]">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                            </tbody>
                        </table>
                    </div>
                    <!-- Data Count Component -->
                    <x-data-table tableId="scheduleTable" />
                </div>
            </div>
        </div>




        <!-- View: Create New Audit Schedule -->
        <div x-show="tab === 'create-schedule'" class="space-y-6" x-cloak>
            <!-- Page Title with Back button -->
            <div class="mb-6">
                <div class="flex items-center gap-3">
                    <button type="button" @click="closeCreateSchedule()"
                        class="inline-flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all">
                        <i class="fa-solid fa-arrow-left text-[11px] sm:text-sm"></i>
                    </button>
                    <div>
                        <h1 class="text-lg sm:text-2xl font-bold text-slate-800">Create New Audit Agenda</h1>
                        <p class="text-slate-500 text-sm">Plan and schedule a new compliance audit</p>
                    </div>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
                <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50 rounded-t-xl">
                    <h2 class="text-lg font-bold text-slate-800">Schedule Information</h2>
                    <p class="text-slate-500 text-sm mt-1">Please fill in the required audit schedule details below.</p>
                </div>

                <div class="p-8">
                    <form @submit.prevent="saveSchedule()">
                        <div class="grid grid-cols-2 gap-x-4 sm:gap-x-8 gap-y-6">
                            <!-- Agenda / Audit Name -->
                            <div class="col-span-2 lg:col-span-1">
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Agenda / Audit Name <span class="text-red-500">*</span></label>
                                <input type="text" x-model="newSchedule.agenda_name" required class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm outline-none transition-all hover:border-blue-300">
                            </div>

                            <!-- Audit Date -->
                            <div class="col-span-2 lg:col-span-1">
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Audit Date <span class="text-red-500">*</span></label>
                                <input type="date" x-model="newSchedule.schedule_date" required class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm outline-none transition-all hover:border-blue-300">
                            </div>

                            <!-- Auditor(s) -->
                            <div class="col-span-2 lg:col-span-1">
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Auditor(s) <span class="text-red-500">*</span></label>
                                <input type="text" placeholder="Auditor name(s)..." x-model="newSchedule.auditor_niks" required class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm outline-none transition-all hover:border-blue-300">
                            </div>

                            <!-- Target Auditee Department -->
                            <div class="col-span-2 lg:col-span-1" @auditee-dept-changed.window="newSchedule.auditee_dept = $event.detail.value">
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Target Auditee Department <span class="text-red-500">*</span></label>
                                <x-searchable-select
                                    name="auditee_dept"
                                    id="formAuditeeDept"
                                    label="Department"
                                    required="true"
                                    apiUrl="{{ route('genba.get_section') }}"
                                    updateEvent="update-auditee-dept"
                                    changeEvent="auditee-dept-changed"
                                    hideLabel="true" />
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end gap-3 pt-8 mt-8 border-t border-slate-100 bg-slate-50/50 -mx-8 -mb-8 p-6 rounded-b-xl">
                            <button type="button" @click="closeCreateSchedule()" class="px-5 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-lg text-sm font-semibold transition-colors">Cancel</button>
                            <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition-colors">Save Schedule</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </main>

    <!-- Modal 2: CAR Detail & Workflow Approvals -->
    <div x-show="carModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
        <div class="fixed inset-0 bg-slate-900/60" @click="carModalOpen = false"></div>
        <div class="bg-white rounded-2xl w-full max-w-2xl overflow-hidden shadow-2xl relative z-10 animate-fade-in">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-800" x-text="activeCar.num"></h3>
                    <p class="text-xs text-slate-400 mt-0.5">Auditee Department: <span x-text="activeCar.dept" class="font-medium text-slate-600"></span></p>
                </div>
                <button type="button" @click="carModalOpen = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            
            <div class="p-6 space-y-6 max-h-[70vh] overflow-y-auto no-scrollbar">
                <!-- Finding Details -->
                <div class="bg-slate-50 p-4 border border-slate-200 rounded-xl">
                    <div class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1.5">Finding Description</div>
                    <div class="text-sm text-slate-800 font-medium" x-text="activeCar.desc"></div>
                </div>

                <!-- CAR Filling Form (Auditee Actions) -->
                <div class="space-y-4">
                    <h4 class="text-sm font-bold text-slate-800 border-b pb-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-pen-to-square text-blue-500"></i> Step 2: Auditee Corrective Action Planning
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Corrective Action Plan</label>
                            <textarea rows="3" placeholder="Input specific actions..." x-model="carUpdate.corrective_action" class="w-full text-sm border border-slate-200 rounded-lg p-2.5 focus:ring-1 focus:ring-blue-500 outline-none" :disabled="activeCar.status === 'Closed'"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Preventive Action Plan</label>
                            <textarea rows="3" placeholder="Input steps to prevent recurrence..." x-model="carUpdate.preventive_action" class="w-full text-sm border border-slate-200 rounded-lg p-2.5 focus:ring-1 focus:ring-blue-500 outline-none" :disabled="activeCar.status === 'Closed'"></textarea>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Due Date</label>
                            <input type="date" x-model="carUpdate.due_date" class="w-full text-sm border border-slate-200 rounded-lg p-2.5 focus:ring-1 focus:ring-blue-500 outline-none" :disabled="activeCar.status === 'Closed'">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Upload Completion Evidence File</label>
                            <input type="file" id="evidenceFile" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer" :disabled="activeCar.status === 'Closed'">
                        </div>
                    </div>
                </div>

                <!-- Workflow Approval Logs (Step 2.5, 3 & 3.5) -->
                <div class="space-y-4 pt-4 border-t">
                    <h4 class="text-sm font-bold text-slate-800 border-b pb-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-signature text-emerald-500"></i> Workflow Approvals & Verification
                    </h4>
                    
                    <div class="grid grid-cols-3 gap-3">
                        <!-- Approval 1: Dept Head Auditee -->
                        <div class="p-3 border border-slate-200 rounded-xl flex flex-col justify-between h-28 relative overflow-hidden">
                            <div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase">1. Dept Head Auditee</div>
                                <div class="text-xs font-semibold text-slate-700 mt-1" x-text="activeCar.deptApproved"></div>
                            </div>
                            <button type="button" @click="approveSign('dept')" class="w-full py-1 text-center bg-blue-50 hover:bg-blue-100 text-blue-600 rounded text-xs font-bold transition-all" :disabled="activeCar.status === 'Closed' || activeCar.deptApproved === 'Approved'">
                                Approve Sign
                            </button>
                        </div>
                        
                        <!-- Approval 2: Auditor Verification -->
                        <div class="p-3 border border-slate-200 rounded-xl flex flex-col justify-between h-28 relative overflow-hidden">
                            <div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase">2. Auditor Verification</div>
                                <div class="text-xs font-semibold text-slate-700 mt-1" x-text="activeCar.auditorApproved"></div>
                            </div>
                            <button type="button" @click="approveSign('auditor')" class="w-full py-1 text-center bg-blue-50 hover:bg-blue-100 text-blue-600 rounded text-xs font-bold transition-all" :disabled="activeCar.status === 'Closed' || activeCar.auditorApproved === 'Verified'">
                                Verify Sign
                            </button>
                        </div>

                        <!-- Approval 3: QMR Sign Off -->
                        <div class="p-3 border border-slate-200 rounded-xl flex flex-col justify-between h-28 relative overflow-hidden">
                            <div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase">3. QMR Approval (Close)</div>
                                <div class="text-xs font-semibold text-slate-700 mt-1" x-text="activeCar.qmrApproved"></div>
                            </div>
                            <button type="button" @click="approveSign('qmr')" class="w-full py-1 text-center bg-blue-50 hover:bg-blue-100 text-blue-600 rounded text-xs font-bold transition-all" :disabled="activeCar.status === 'Closed' || activeCar.qmrApproved === 'Closed'">
                                Close Sign
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Action Buttons -->
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex justify-between">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded-full text-xs font-semibold" x-text="activeCar.status"></span>
                </div>
                <div class="flex gap-2">
                    <button type="button" @click="carModalOpen = false" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 rounded-lg text-sm font-semibold transition-colors">Close Details</button>
                    <button type="button" @click="saveCarPlan()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition-colors" x-show="activeCar.status !== 'Closed'">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.footer')
</div>

<!-- Mobile Sidebar Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/50 z-30 hidden lg:hidden"></div>
@endsection

@push('scripts')
<script>
    function csAuditApp() {
        return {
            tab: 'schedules',
            statusFilter: 'All',
            scheduleModalOpen: false,
            carModalOpen: false,
            
            checksheetItems: [],
            auditResults: {},
            
            newSchedule: {
                agenda_name: '',
                schedule_date: '',
                auditor_niks: '',
                auditee_dept: ''
            },
            
            activeSession: {
                id: null,
                name: '',
                dept: '',
                deptName: ''
            },
            
            activeCar: {
                id: null,
                num: '',
                desc: '',
                dept: '',
                status: '',
                deptApproved: 'Pending',
                auditorApproved: 'Pending',
                qmrApproved: 'Pending'
            },

            carUpdate: {
                corrective_action: '',
                preventive_action: '',
                due_date: ''
            },
            
            init() {
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.has('tab')) {
                    this.tab = urlParams.get('tab');
                }
                if (urlParams.has('success') && urlParams.has('msg')) {
                    setTimeout(() => {
                        showToast(urlParams.get('msg'), 'success');
                    }, 500);
                }
                this.initScheduleTable();
            },
            
            setTab(tabName) {
                this.tab = tabName;
            },

            initScheduleTable() {
                var self = this;
                var table = $('#scheduleTable').DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: false,
                    ajax: {
                        url: "{{ route('internal_audit.schedules') }}",
                        type: 'POST',
                        data: function(d) {
                            d._token = "{{ csrf_token() }}";
                            d.search.value = $('#scheduleSearch').val();
                            d.status_filter = self.statusFilter;
                        }
                    },
                    columns: [
                        { data: 'no', orderable: false, className: 'text-center font-base text-slate-700' },
                        { data: 'schedule_date', className: 'text-slate-600' },
                        { data: 'agenda_name', className: 'text-slate-800 font-medium' },
                        { data: 'auditor_niks', className: 'text-slate-600' },
                        { data: 'auditee_dept', className: 'text-slate-600' },
                        { data: 'status', className: 'text-left' },
                        { data: 'action', orderable: false, className: 'text-left' }
                    ],
                    dom: 'r<"overflow-x-auto"t><"flex flex-col sm:flex-row items-center justify-between p-4 border-t border-slate-200 gap-4"ip>',
                    pagingType: "simple_numbers"
                });

                window.deleteAuditSchedule = function(id) {
                    if (confirm('Are you sure you want to delete this schedule? This action cannot be undone.')) {
                        $.ajax({
                            url: "{{ url('/genba-internal/internal-audit/schedules/delete') }}/" + id,
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                if (response.success) {
                                    showToast(response.message, 'success');
                                    table.ajax.reload();
                                } else {
                                    showToast(response.message, 'error');
                                }
                            },
                            error: function(xhr) {
                                showToast('Failed to delete schedule.', 'error');
                            }
                        });
                    }
                };

                $('#scheduleSearch').on('keyup', function() {
                    table.search($(this).val()).draw();
                });
            },

            initCarTable() {
                var self = this;
                var table = $('#carTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('internal_audit.cars') }}",
                        type: 'POST',
                        data: function(d) {
                            d._token = "{{ csrf_token() }}";
                            d.search.value = $('#carSearch').val();
                        }
                    },
                    columns: [
                        { data: 'car_number', className: 'text-slate-800 font-bold' },
                        { data: 'finding_desc', className: 'text-slate-600' },
                        { data: 'auditee_dept', className: 'text-slate-600' },
                        { data: 'due_date', className: 'text-slate-600' },
                        { data: 'status', className: 'text-center' },
                        { data: 'action', orderable: false, className: 'text-center' }
                    ],
                    dom: 't<"flex flex-col sm:flex-row items-center justify-between p-4 border-t border-slate-200 gap-4"ip>',
                    pagingType: "simple_numbers"
                });

                window.viewCarDetail = function(id, num, desc, dept, status, corr, prev, due, deptApp, audApp, qmrApp) {
                    self.activeCar.id = id;
                    self.activeCar.num = num;
                    self.activeCar.desc = desc;
                    self.activeCar.dept = dept;
                    self.activeCar.status = status;
                    self.activeCar.deptApproved = deptApp;
                    self.activeCar.auditorApproved = audApp;
                    self.activeCar.qmrApproved = qmrApp;

                    self.carUpdate.corrective_action = corr || '';
                    self.carUpdate.preventive_action = prev || '';
                    self.carUpdate.due_date = due || '';

                    self.carModalOpen = true;
                };

                $('#carSearch').on('keyup', function() {
                    table.search($(this).val()).draw();
                });
            },



            openCreateSchedule() {
                var self = this;
                $('body').addClass('data-loading');
                $('#page-loader').removeClass('hidden');
                
                setTimeout(function() {
                    self.newSchedule = {
                        agenda_name: '',
                        schedule_date: '',
                        auditor_niks: '{{ Auth::user()->full_name }}',
                        auditee_dept: ''
                    };
                    window.dispatchEvent(new CustomEvent('update-auditee-dept', { detail: { id: '', name: '' } }));
                    self.tab = 'create-schedule';
                    $('body').removeClass('data-loading');
                    $('#page-loader').addClass('hidden');
                }, 400);
            },

            closeCreateSchedule() {
                var self = this;
                $('body').addClass('data-loading');
                $('#page-loader').removeClass('hidden');
                
                setTimeout(function() {
                    self.tab = 'schedules';
                    $('body').removeClass('data-loading');
                    $('#page-loader').addClass('hidden');
                }, 300);
            },
            
            saveSchedule() {
                var self = this;
                $('body').addClass('data-loading');
                $.ajax({
                    url: "{{ route('internal_audit.schedules.store') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        agenda_name: self.newSchedule.agenda_name,
                        schedule_date: self.newSchedule.schedule_date,
                        auditor_niks: self.newSchedule.auditor_niks,
                        auditee_dept: self.newSchedule.auditee_dept
                    },
                    success: function(response) {
                        $('body').removeClass('data-loading');
                        if (response.success) {
                            showToast(response.message, 'success');
                            self.tab = 'schedules';
                            $('#scheduleTable').DataTable().ajax.reload();
                        } else {
                            showToast(response.message, 'error');
                        }
                    },
                    error: function() {
                        $('body').removeClass('data-loading');
                        showToast('Failed to save schedule.', 'error');
                    }
                });
            },

            saveCarPlan() {
                var self = this;
                var formData = new FormData();
                formData.append('_token', "{{ csrf_token() }}");
                formData.append('car_id', self.activeCar.id);
                formData.append('corrective_action', self.carUpdate.corrective_action);
                formData.append('preventive_action', self.carUpdate.preventive_action);
                formData.append('due_date', self.carUpdate.due_date);
                
                var fileInput = document.getElementById('evidenceFile');
                if (fileInput && fileInput.files[0]) {
                    formData.append('evidence_file', fileInput.files[0]);
                }

                $.ajax({
                    url: "{{ route('internal_audit.cars.update') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            showToast(response.message, 'success');
                            self.carModalOpen = false;
                            $('#carTable').DataTable().ajax.reload();
                        } else {
                            showToast(response.message, 'error');
                        }
                    }
                });
            },
            
            approveSign(role) {
                var self = this;
                $.ajax({
                    url: "{{ route('internal_audit.cars.approve') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        car_id: self.activeCar.id,
                        role: role
                    },
                    success: function(response) {
                        if (response.success) {
                            showToast(response.message, 'success');
                            if (role === 'dept') self.activeCar.deptApproved = 'Approved';
                            if (role === 'auditor') self.activeCar.auditorApproved = 'Verified';
                            if (role === 'qmr') {
                                self.activeCar.qmrApproved = 'Closed';
                                self.activeCar.status = 'Closed';
                                self.carModalOpen = false;
                            }
                            $('#carTable').DataTable().ajax.reload();
                        } else {
                            showToast(response.message, 'error');
                        }
                    }
                });
            }
        }
    }
</script>
@endpush
