@extends('layouts.app')

@section('title', 'Genba Team - QMS')

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
                <h1 class="text-2xl font-bold text-slate-800">Genba Team</h1>
                <p class="text-slate-500 mt-1">Manage team-based genba audit sessions</p>
            </div>

            <!-- Main Card -->
            <div class="bg-white rounded-lg border border-slate-200">
                <!-- Filter Section -->
                <div class="p-6 border-b border-slate-200 bg-slate-50/50">
                    <div class="flex flex-wrap items-center gap-3">
                        <!-- Search -->
                        <div class="flex-1 min-w-[200px]">
                            <div class="relative">
                                <input type="text" id="searchInput" placeholder="Search team sessions..."
                                    class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                                <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            </div>
                        </div>

                        <!-- Spacer -->
                        <div class="flex-1"></div>

                        <!-- Create & Room Buttons -->
                        <div class="flex items-center gap-2">
                            <a href="{{ route('room_team') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-white text-slate-700 border border-slate-200 rounded-lg hover:bg-slate-50 text-sm font-medium transition-all">
                                <i class="fa-solid fa-users text-sm"></i>
                                Room Team
                            </a>
                            <button type="button" onclick="showCreateForm()"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition-colors">
                                <i class="fa-solid fa-users-medical text-sm"></i>
                                Create Team Genba
                            </button>
                        </div>
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
                <div class="overflow-x-auto p-6">
                    <table id="genbaFormTable" class="qms-table w-full">
                        <thead>
                            <tr>
                                <th class="w-[4%] text-center">No</th>
                                <th class="w-[12%]">Genba Date</th>
                                <th class="w-[15%]">Process</th>
                                <th class="w-[12%]">Line Checked</th>
                                <th class="w-[25%]">Auditor Team</th>
                                <th class="w-[12%]">Category</th>
                                <th class="w-[12%]">Action</th>
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
                        class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all">
                        <i class="fa-solid fa-arrow-left text-sm"></i>
                    </button>
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800">Team Genba Setup</h1>
                        <p class="text-slate-500 text-sm">Invite members and start a collaborative audit</p>
                    </div>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50">
                    <h2 class="text-lg font-bold text-slate-800">Audit Configuration</h2>
                    <p class="text-slate-500 text-sm mt-1">Configure your team and audit parameters.</p>
                </div>

                <div class="p-8">
                    <form id="createGenbaForm" action="{{ route('genba.header.add') }}" method="POST">
                        @csrf
                        <input type="hidden" id="formTrcUnixId" name="trc_unix_id">

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-8 gap-y-6">
                            <!-- Date -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Date <span class="text-red-500">*</span></label>
                                <input type="date" id="formDate" name="date" required
                                    class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm outline-none transition-all hover:border-blue-300">
                            </div>

                            <!-- Process -->
                            <div>
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
                            <div>
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
                            <div>
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

                            <!-- Team Collaboration Section -->
                            <div class="col-span-1 lg:col-span-2 mt-4 pt-6 border-t border-slate-100">
                                <label class="block text-sm font-bold text-slate-800 mb-4">Auditor Team Collaboration</label>
                                
                                <div x-data="teamManager()" x-init="init()" @update-team.window="members = $event.detail.members">
                                    <div class="space-y-4">
                                        <!-- Add Member Input -->
                                        <div class="flex flex-col sm:flex-row gap-3">
                                            <div class="flex-1">
                                                <x-searchable-select
                                                    name="temp_auditor"
                                                    id="memberSelect"
                                                    label="Auditor"
                                                    apiUrl="{{ route('genba.get_user_data') }}"
                                                    hideLabel="true"
                                                    updateEvent="update-member-select"
                                                    changeEvent="member-selected"
                                                    placeholder="Search auditor name to invite..." />
                                            </div>
                                        </div>

                                        <!-- Team List -->
                                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                                            <div class="text-sm font-semibold text-slate-400 mb-3">Active Team Members</div>
                                            <div class="flex flex-wrap gap-2">
                                                <template x-for="member in members" :key="member.id">
                                                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-white border border-slate-200 text-sm text-slate-700 group hover:border-blue-300 transition-all">
                                                        <span x-text="member.name" class="font-medium uppercase"></span>
                                                        <button type="button" @click="removeMember(member.id, member.name)" 
                                                             x-show="String(member.id) !== '{{ Auth::user()->id }}'"
                                                             class="text-red-400 hover:text-red-600 transition-colors ml-1">
                                                             <i class="fa-solid fa-xmark text-sm"></i>
                                                         </button>
                                                         <span x-show="String(member.id) === '{{ Auth::user()->id }}'" class="text-[10px] bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded ml-1">You</span>
                                                    </div>
                                                </template>
                                            </div>
                                            <p x-show="members.length === 1" class="text-xs text-slate-400 mt-3 italic">You are currently alone. Invite others to work together!</p>
                                        </div>

                                         <!-- Final Auditor String for DB -->
                                         <input type="hidden" name="auditor" :value="auditorString">
                                         <input type="hidden" name="is_team" :value="teamIds">
                                     </div>
                                </div>
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
                                <span>Start Team Genba</span>
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
    <div class="fixed inset-0 bg-slate-900/60 transition-opacity"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-md transform transition-all">
            <div class="p-6 text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                    <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Confirm Delete</h3>
                <p class="text-slate-500">Are you sure you want to delete this team genba session?</p>
            </div>
            <div class="flex gap-3 p-6 pt-0">
                <button type="button" id="btnCancelDelete"
                    class="flex-1 px-4 py-3 bg-slate-100 text-slate-700 rounded-xl font-semibold hover:bg-slate-200 transition-colors">No</button>
                <button type="button" id="btnConfirmDelete"
                    class="flex-1 px-4 py-3 bg-red-600 text-white rounded-xl font-semibold hover:bg-red-700 transition-colors">Yes, Delete</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    var currentStatusId = 0;
    var table;

    // Alpine component for team management
    function teamManager() {
        return {
            members: [{ id: '{{ Auth::user()->id }}', name: '{{ Auth::user()->full_name }}' }],
            auditorString: '{{ Auth::user()->full_name }}',
            teamIds: JSON.stringify(['{{ Auth::user()->id }}']),
            
            isInviting: false,

            init() {
                this.$watch('members', (value) => {
                    this.auditorString = value.map(m => m.name).join(', ');
                    this.teamIds = JSON.stringify(value.map(m => m.id));
                });
                window.addEventListener('member-selected', (e) => {
                    this.inviteMember(e.detail.id, e.detail.name);
                });
            },

            inviteMember(selId, selName) {
                if (this.isInviting) return;
                this.isInviting = true;
                
                // Set timeout to reset the flag
                setTimeout(() => { this.isInviting = false; }, 300);

                if (!selId || !selName) {
                    selId = selId || $('#memberSelect').val();
                    selName = selName || $('#memberSelect').parent().find('input[type=text]').val();
                }
                
                if (selId && !this.members.find(m => m.id.toString() === selId.toString())) {
                    this.members.push({ id: selId, name: selName });
                    showToast('Auditor invited: ' + selName, 'success');
                } else if (selId) {
                    showToast('Auditor already in team', 'warning');
                }

                // Reset the select component
                window.dispatchEvent(new CustomEvent('update-member-select', { 
                    detail: { id: '', name: '' } 
                }));
            },

            removeMember(id, name) {
                if (String(id) === '{{ Auth::user()->id }}') {
                    showToast('You cannot remove yourself from the team', 'warning');
                    return;
                }
                this.members = this.members.filter(m => m.id !== id && m.name !== name);
            }
        };
    }

    $(document).ready(function() {
        table = $('#genbaFormTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('genba.header.table') }}",
                type: 'POST',
                data: function(d) {
                    d._token = "{{ csrf_token() }}";
                    d.front_table_search = $('#searchInput').val();
                    d.status_id = currentStatusId;
                    d.is_room_team = 'false';
                }
            },
            columns: [
                { data: 'no', orderable: false, className: 'text-center font-base text-slate-700' },
                { data: 'date', className: 'text-slate-700' },
                { data: 'process', className: 'text-slate-700' },
                { data: 'line_checked', className: 'text-slate-700' },
                { 
                    data: 'auditor', 
                    className: 'text-blue-600 font-medium',
                    render: function(data) {
                        return data || '';
                    }
                },
                { data: 'category', className: 'text-slate-700' },
                { 
                    data: 'action', 
                    orderable: false, 
                    render: function(data) {
                        return '<div class="flex items-center gap-2">' + data + '</div>';
                    }
                }
            ],
            order: [[1, 'desc']],
            pageLength: 10,
            language: {
                emptyTable: '<div class="text-center py-8 text-slate-500">No team genba found</div>',
                info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                paginate: {
                    previous: '<i class="fa-solid fa-chevron-left"></i>',
                    next: '<i class="fa-solid fa-chevron-right"></i>'
                }
            }
        });

        // Search timer
        var searchTimer;
        $('#searchInput').on('keyup', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function() { table.ajax.reload(); }, 500);
        });

        // Form Submit
        $('#createGenbaForm').on('submit', function(e) {
            // Basic validation
            if (!$('#formDate').val() || !$('#formProcess').val() || !$('#formLineChecked').val()) {
                e.preventDefault();
                showToast('Please fill all required fields', 'error');
                return false;
            }
        });
    });

    function showCreateForm() {
        var timestamp = new Date().getTime();
        loadGenbaActivity('0_' + timestamp);
    }

    function loadGenbaActivity(trcUnixId) {
        $('#page-loader').removeClass('hidden');
        $('#formTrcUnixId').val(trcUnixId);

        $.ajax({
            url: "{{ route('genba.header.activity') }}",
            type: 'POST',
            data: { _token: "{{ csrf_token() }}", trc_unix_id: trcUnixId },
            success: function(response) {
                // Update basic fields
                $('#formDate').val(response.date ? response.date.split(' ')[0] : new Date().toISOString().split('T')[0]);
                
                window.dispatchEvent(new CustomEvent('update-process-options', { detail: { options: response.process_options || [] } }));
                window.dispatchEvent(new CustomEvent('update-process-value', { detail: { value: response.process || '' } }));
                window.dispatchEvent(new CustomEvent('update-line-value', { detail: { value: response.area_checked || '' } }));
                window.dispatchEvent(new CustomEvent('update-category-value', { detail: { id: response.category_id || '', name: response.category || '' } }));
                
                // Update Team Members via Alpine
                let members = [{ id: '{{ Auth::user()->id }}', name: '{{ Auth::user()->full_name }}' }];
                
                if (response.is_team && response.auditor) {
                    let ids = [];
                    try {
                        ids = JSON.parse(response.is_team);
                        if (!Array.isArray(ids)) ids = [ids];
                    } catch (e) {
                        ids = response.is_team.split(', ');
                    }
                    
                    // Split names by comma or ampersand
                    const names = response.auditor.split(/\s*[,&]\s*/);
                    members = ids.map((id, index) => {
                        let memberId = String(id).trim();
                        // If it's the first member (creator) and it looks like a name instead of an ID,
                        // use current user ID as a fix for legacy data consistency.
                        if (index === 0 && (isNaN(memberId) || memberId.includes('-'))) {
                            memberId = '{{ Auth::user()->id }}';
                        }
                        return { 
                            id: memberId, 
                            name: (names[index] || memberId).trim() 
                        };
                    });
                } else if (response.auditor) {
                    const names = response.auditor.split(/\s*[,&]\s*/);
                    members = names.map((n, index) => {
                        let name = n.trim();
                        let memberId = name;
                        
                        // Fix for legacy data: if it's the creator and it's a name, use ID
                        if (index === 0 && (isNaN(memberId) || memberId.includes('-'))) {
                            memberId = '{{ Auth::user()->id }}';
                        }
                        
                        return { id: memberId, name: name };
                    });
                }
                window.dispatchEvent(new CustomEvent('update-team', { detail: { members: members } }));

                // Switch views
                $('#listView').addClass('hidden');
                $('#createView').removeClass('hidden');
                $('#page-loader').addClass('hidden');
            },
            error: function() {
                $('#page-loader').addClass('hidden');
                showToast('Error loading session data', 'error');
            }
        });
    }

    function hideCreateForm() {
        $('#createView').addClass('hidden');
        $('#listView').removeClass('hidden');
        $('#createGenbaForm')[0].reset();
    }

    function document_view(id, no) {
        loadGenbaActivity(id);
    }

    function filterByStatus(statusId) {
        currentStatusId = statusId;
        $('.status-tab').removeClass('text-blue-600 border-blue-600').addClass('text-slate-600 border-transparent');
        $(event.target).addClass('text-blue-600 border-blue-600').removeClass('text-slate-600 border-transparent');
        table.ajax.reload();
    }

    // Delete handlers
    var deleteTargetSysId = null;
    var deleteTargetNo = null;
    function f_genba_header_delete(sysId, no) {
        deleteTargetSysId = sysId;
        deleteTargetNo = no;
        $('#deleteConfirmModal').removeClass('hidden');
    }
    $('#btnCancelDelete').click(() => $('#deleteConfirmModal').addClass('hidden'));
    $('#btnConfirmDelete').click(executeDelete);

    function executeDelete() {
        if (!deleteTargetSysId) return;
        $('#deleteConfirmModal').addClass('hidden');
        $.ajax({
            url: "{{ route('genba.header.delete') }}",
            type: 'POST',
            data: { _token: "{{ csrf_token() }}", sys_id: deleteTargetSysId },
            success: function(response) {
                if (response.success) {
                    showToast('Deleted successfully', 'success');
                    table.ajax.reload();
                }
            }
        });
    }
</script>
@endpush
