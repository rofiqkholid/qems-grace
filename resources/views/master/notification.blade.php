@php
    $hideCentralToast = true;
    $targetOptions = [
        ['id' => 'all', 'name' => 'All Registered Users'],
        ['id' => 'department', 'name' => 'Specific Department'],
        ['id' => 'user', 'name' => 'Specific User(s)']
    ];
    $deptOptions = [];
    foreach($departments as $dept) {
        $deptOptions[] = [
            'id' => $dept->Key1,
            'name' => $dept->Key1 . ' - ' . ($dept->Desc ?? $dept->Key1)
        ];
    }
    $userOptions = [];
    foreach($users as $usr) {
        $userOptions[] = [
            'id' => (string)$usr->id,
            'name' => ($usr->full_name ?: $usr->username)
        ];
    }
@endphp
@extends('layouts.app')

@section('title', 'Master Notification - ICT')

@section('content')
@include('layouts.sidebar')
@include('components.toast')

<div class="lg:ml-20 min-h-screen flex flex-col bg-slate-50">
    @include('layouts.header')

    <!-- Page Content -->
    <main class="flex-1 p-6">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-lg sm:text-2xl font-bold text-slate-800">Master Notification</h1>
                </div>
                <p class="text-slate-500 text-xs sm:text-sm mt-1">Broadcast update & info notifications to all users, department, or specific user</p>
            </div>
            
            <button onclick="openSendModal()" class="shrink-0 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg flex items-center gap-2 transition-colors text-xs sm:text-sm font-semibold shadow-sm">
                <i class="fa-solid fa-paper-plane text-xs"></i>
                <span>Send Notification</span>
            </button>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold shrink-0">
                    <i class="fa-solid fa-bell"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-slate-800">{{ number_format($stats['total_sent'] ?? 0) }}</div>
                    <div class="text-xs text-slate-500 font-medium">Total Notifications Sent</div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold shrink-0">
                    <i class="fa-solid fa-envelope-open text-lg"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-slate-800">{{ number_format($stats['total_unread'] ?? 0) }}</div>
                    <div class="text-xs text-slate-500 font-medium">Total Unread Notifications</div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold shrink-0">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-slate-800">{{ number_format($stats['total_users'] ?? 0) }}</div>
                    <div class="text-xs text-slate-500 font-medium">Active Registered Users</div>
                </div>
            </div>
        </div>

        <!-- Main Table Card -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <!-- Filter Section -->
            <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="relative w-full sm:w-80">
                    <input type="text" id="searchInput" placeholder="Search notification title, message, user..."
                        class="w-full pl-10 pr-4 py-2 text-xs sm:text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">
                    <i class="fa-solid fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs sm:text-sm"></i>
                </div>
            </div>

            <div class="p-6">
                <table id="notificationTable" class="qms-table w-full min-w-[900px]">
                    <thead>
                        <tr>
                            <th class="w-[5%] text-center">No</th>
                            <th class="w-[25%]">Title</th>
                            <th class="w-[20%]">Recipient</th>
                            <th class="w-[10%]">Type</th>
                            <th class="w-[25%]">Message</th>
                            <th class="w-[10%]">Sent At</th>
                            <th class="w-[5%] text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                    </tbody>
                </table>
            </div>
            <!-- Data Count Component -->
            <x-data-table tableId="notificationTable" />
        </div>
    </main>
    @include('layouts.footer')
</div>

<!-- Mobile Sidebar Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/50 z-30 hidden lg:hidden"></div>

<!-- Send Broadcast Modal -->
<div id="sendModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-slate-900/50 transition-opacity" onclick="closeSendModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl w-full max-w-6xl shadow-2xl transform transition-all flex flex-col max-h-[90vh] z-10">
            <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50 rounded-t-xl">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-paper-plane"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Send Notification Broadcast</h3>
                </div>
                <button onclick="closeSendModal()" class="text-slate-400 hover:text-slate-600 p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form id="sendForm" onsubmit="handleSendSubmit(event)" class="flex flex-col flex-1 overflow-y-visible">
                @csrf
                <div class="p-6 space-y-4 flex-1">
                    <!-- Title -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Notification Title <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" required class="w-full px-3.5 py-2 text-xs sm:text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all" placeholder="e.g. System Maintenance Update">
                    </div>

                    <!-- Type -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Notification Type <span class="text-rose-500">*</span></label>
                        <div class="grid grid-cols-4 gap-2">
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="info" checked class="peer sr-only">
                                <div class="p-2.5 flex items-center justify-center gap-2 text-xs font-medium rounded-lg border border-slate-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-700 transition-all">
                                    <i class="fa-solid fa-circle-info text-sm text-blue-500"></i>
                                    <span>Info</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="success" class="peer sr-only">
                                <div class="p-2.5 flex items-center justify-center gap-2 text-xs font-medium rounded-lg border border-slate-200 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-700 transition-all">
                                    <i class="fa-solid fa-circle-check text-sm text-emerald-500"></i>
                                    <span>Success</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="warning" class="peer sr-only">
                                <div class="p-2.5 flex items-center justify-center gap-2 text-xs font-medium rounded-lg border border-slate-200 peer-checked:border-amber-500 peer-checked:bg-amber-50 peer-checked:text-amber-700 transition-all">
                                    <i class="fa-solid fa-triangle-exclamation text-sm text-amber-500"></i>
                                    <span>Warning</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="danger" class="peer sr-only">
                                <div class="p-2.5 flex items-center justify-center gap-2 text-xs font-medium rounded-lg border border-slate-200 peer-checked:border-rose-500 peer-checked:bg-rose-50 peer-checked:text-rose-700 transition-all">
                                    <i class="fa-solid fa-circle-exclamation text-sm text-rose-500"></i>
                                    <span>Danger</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Target Recipient (Searchable Select Component) -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Target Recipient <span class="text-rose-500">*</span></label>
                        <x-searchable-select 
                            name="target" 
                            id="targetSelect" 
                            label="Target Recipient" 
                            :required="true" 
                            :hideLabel="true" 
                            :initialOptions="$targetOptions" 
                            updateEvent="set-target-value"
                        />
                    </div>

                    <!-- Department Selector (Conditional Searchable Select) -->
                    <div id="deptSelectWrapper" class="hidden">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Select Department <span class="text-rose-500">*</span></label>
                        <x-searchable-select 
                            name="department" 
                            id="departmentInput" 
                            label="Select Department" 
                            :required="false" 
                            :hideLabel="true" 
                            :initialOptions="$deptOptions" 
                            updateEvent="set-dept-value"
                        />
                    </div>

                    <!-- User Selector (Conditional Searchable Select Multi) -->
                    <div id="userSelectWrapper" class="hidden">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Select User(s) <span class="text-rose-500">*</span></label>
                        <x-searchable-select-multi 
                            name="target_user_id" 
                            id="userInput" 
                            label="Select User(s)" 
                            :required="false" 
                            :hideLabel="true" 
                            :multiple="true" 
                            :maxItems="50" 
                            :initialOptions="$userOptions" 
                            updateEvent="set-user-value"
                        />
                    </div>

                    <!-- URL / Target Link -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Target Link / URL <span class="text-slate-400 font-normal">(Optional)</span></label>
                        <input type="text" name="url" class="w-full px-3.5 py-2 text-xs sm:text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all" placeholder="e.g. /dashboard or https://...">
                        <p class="text-[11px] text-slate-400 mt-1">Users clicking this notification will be redirected to this URL.</p>
                    </div>

                    <!-- Message Body -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Notification Message <span class="text-rose-500">*</span></label>
                        <textarea name="message" rows="3" required class="w-full px-3.5 py-2 text-xs sm:text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all" placeholder="Type the broadcast message here..."></textarea>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="p-4 border-t border-slate-100 bg-slate-50 flex justify-end gap-3 shrink-0 rounded-b-xl">
                    <button type="button" onclick="closeSendModal()" class="px-4 py-2 text-xs sm:text-sm text-slate-600 font-semibold hover:bg-slate-200/60 rounded-lg transition-colors">Cancel</button>
                    <button type="submit" id="btnSendSubmit" class="px-5 py-2 bg-blue-600 text-white text-xs sm:text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">Send Broadcast</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Notification Modal -->
<div id="editModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-slate-900/50 transition-opacity" onclick="closeEditModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl w-full max-w-2xl shadow-2xl transform transition-all flex flex-col max-h-[90vh] z-10">
            <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50 rounded-t-xl">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Edit Notification</h3>
                </div>
                <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form id="editForm" onsubmit="handleEditSubmit(event)" class="flex flex-col flex-1 overflow-y-visible">
                @csrf
                <input type="hidden" name="id" id="edit_notif_id">
                <div class="p-6 space-y-4 flex-1">
                    <!-- Title -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Notification Title <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" id="edit_title" required class="w-full px-3.5 py-2 text-xs sm:text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all" placeholder="e.g. System Maintenance Update">
                    </div>

                    <!-- Type -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Notification Type <span class="text-rose-500">*</span></label>
                        <div class="grid grid-cols-4 gap-2">
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="info" id="edit_type_info" class="peer sr-only">
                                <div class="p-2.5 flex items-center justify-center gap-2 text-xs font-medium rounded-lg border border-slate-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-700 transition-all">
                                    <i class="fa-solid fa-circle-info text-sm text-blue-500"></i>
                                    <span>Info</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="success" id="edit_type_success" class="peer sr-only">
                                <div class="p-2.5 flex items-center justify-center gap-2 text-xs font-medium rounded-lg border border-slate-200 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-700 transition-all">
                                    <i class="fa-solid fa-circle-check text-sm text-emerald-500"></i>
                                    <span>Success</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="warning" id="edit_type_warning" class="peer sr-only">
                                <div class="p-2.5 flex items-center justify-center gap-2 text-xs font-medium rounded-lg border border-slate-200 peer-checked:border-amber-500 peer-checked:bg-amber-50 peer-checked:text-amber-700 transition-all">
                                    <i class="fa-solid fa-triangle-exclamation text-sm text-amber-500"></i>
                                    <span>Warning</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="danger" id="edit_type_danger" class="peer sr-only">
                                <div class="p-2.5 flex items-center justify-center gap-2 text-xs font-medium rounded-lg border border-slate-200 peer-checked:border-rose-500 peer-checked:bg-rose-50 peer-checked:text-rose-700 transition-all">
                                    <i class="fa-solid fa-circle-exclamation text-sm text-rose-500"></i>
                                    <span>Danger</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- URL / Target Link -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Target Link / URL <span class="text-slate-400 font-normal">(Optional)</span></label>
                        <input type="text" name="url" id="edit_url" class="w-full px-3.5 py-2 text-xs sm:text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all" placeholder="e.g. /dashboard or https://...">
                    </div>

                    <!-- Message Body -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Notification Message <span class="text-rose-500">*</span></label>
                        <textarea name="message" id="edit_message" rows="3" required class="w-full px-3.5 py-2 text-xs sm:text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all" placeholder="Type the message here..."></textarea>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="p-4 border-t border-slate-100 bg-slate-50 flex justify-end gap-3 shrink-0 rounded-b-xl">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-xs sm:text-sm text-slate-600 font-semibold hover:bg-slate-200/60 rounded-lg transition-colors">Cancel</button>
                    <button type="submit" id="btnEditSubmit" class="px-5 py-2 bg-blue-600 text-white text-xs sm:text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-slate-900/50 transition-opacity" onclick="closeDeleteModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl w-full max-w-sm p-6 shadow-2xl text-center transform transition-all">
            <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-4 text-xl">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-1">Delete Notification</h3>
            <p class="text-xs text-slate-500 mb-6">Are you sure you want to delete this notification? This action cannot be undone.</p>
            <div class="flex justify-center gap-3">
                <button onclick="closeDeleteModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs sm:text-sm font-semibold rounded-lg transition-colors">Cancel</button>
                <button onclick="executeDelete()" id="btnDeleteSubmit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs sm:text-sm font-semibold rounded-lg transition-colors">Yes, Delete</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let notifTable;
let targetDeleteId = null;

$(document).ready(function() {
    notifTable = $('#notificationTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('master.notification.table') }}",
            type: "POST",
            data: function(d) {
                d._token = "{{ csrf_token() }}";
            }
        },
        columns: [
            { data: 'no', name: 'no', className: 'text-center' },
            { data: 'title', name: 'title' },
            { data: 'target', name: 'target' },
            { data: 'type', name: 'type' },
            { data: 'message', name: 'message' },
            { data: 'created_at', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[0, 'asc']],
        dom: 'r<"overflow-x-auto"t><"flex flex-col sm:flex-row items-center justify-between p-4 border-t border-slate-200 gap-4"ip>',
        pagingType: "simple_numbers",
        language: {
            zeroRecords: 'No notification records found',
            emptyTable: '<div class="flex flex-col items-center justify-center py-8 text-slate-500"><i class="fa-regular fa-folder-open text-4xl mb-3 text-slate-300"></i><p>No data available</p></div>'
        }
    });

    $('#searchInput').on('keyup', function() {
        notifTable.search(this.value).draw();
    });

    $('#targetSelect').on('change', function() {
        toggleTargetFields();
    });
});

function toggleTargetFields() {
    const val = $('#targetSelect').val();
    if (val === 'department') {
        $('#deptSelectWrapper').removeClass('hidden');
        $('#userSelectWrapper').addClass('hidden');
    } else if (val === 'user') {
        $('#userSelectWrapper').removeClass('hidden');
        $('#deptSelectWrapper').addClass('hidden');
    } else {
        $('#deptSelectWrapper').addClass('hidden');
        $('#userSelectWrapper').addClass('hidden');
    }
}

function openSendModal() {
    $('#sendForm')[0].reset();
    $('#btnSendSubmit').prop('disabled', false);
    window.dispatchEvent(new CustomEvent('set-target-value', { detail: { id: 'all', name: 'All Registered Users' } }));
    window.dispatchEvent(new CustomEvent('set-dept-value', { detail: { id: '', name: '' } }));
    window.dispatchEvent(new CustomEvent('set-user-value', { detail: '' }));
    toggleTargetFields();
    $('#sendModal').removeClass('hidden');
}

function closeSendModal() {
    $('#sendModal').addClass('hidden');
}

function handleSendSubmit(e) {
    e.preventDefault();
    const $btn = $('#btnSendSubmit');

    const targetVal = $('#targetSelect').val() || 'all';
    if (targetVal === 'department' && !$('#departmentInput').val()) {
        showToast('Warning', 'Please select a department.', 'warning');
        return;
    }
    if (targetVal === 'user' && !$('#userInput').val()) {
        showToast('Warning', 'Please select at least one target user.', 'warning');
        return;
    }

    $btn.prop('disabled', true);

    $.ajax({
        url: "{{ route('master.notification.send') }}",
        type: "POST",
        data: $('#sendForm').serialize(),
        success: function(res) {
            $btn.prop('disabled', false);
            if (res.success) {
                closeSendModal();
                showToast('Success', res.message, 'success');
                notifTable.ajax.reload(null, false);
                if (typeof fetchHeaderNotifications === 'function') {
                    fetchHeaderNotifications();
                }
            } else {
                showToast('Error', res.message || 'Failed to send broadcast.', 'error');
            }
        },
        error: function(xhr) {
            $btn.prop('disabled', false);
            let errMsg = 'Failed to send notification.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errMsg = xhr.responseJSON.message;
            }
            showToast('Error', errMsg, 'error');
        }
    });
}

function openEditModal(id, notif) {
    $('#edit_notif_id').val(id);
    $('#edit_title').val(notif.title || '');
    $('#edit_message').val(notif.message || '');
    $('#edit_url').val(notif.url || '');
    const type = notif.type || 'info';
    $(`input[name="type"][value="${type}"]`).prop('checked', true);

    $('#btnEditSubmit').prop('disabled', false);
    $('#editModal').removeClass('hidden');
}

function closeEditModal() {
    $('#editModal').addClass('hidden');
}

function handleEditSubmit(e) {
    e.preventDefault();
    const $btn = $('#btnEditSubmit');
    $btn.prop('disabled', true);

    $.ajax({
        url: "{{ route('master.notification.update') }}",
        type: "POST",
        data: $('#editForm').serialize(),
        success: function(res) {
            $btn.prop('disabled', false);
            if (res.success) {
                closeEditModal();
                showToast('Success', res.message, 'success');
                notifTable.ajax.reload(null, false);
                if (typeof fetchHeaderNotifications === 'function') {
                    fetchHeaderNotifications();
                }
            } else {
                showToast('Error', res.message || 'Failed to update notification.', 'error');
            }
        },
        error: function(xhr) {
            $btn.prop('disabled', false);
            let errMsg = 'Failed to update notification.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errMsg = xhr.responseJSON.message;
            }
            showToast('Error', errMsg, 'error');
        }
    });
}

function confirmDeleteNotif(id) {
    targetDeleteId = id;
    $('#deleteModal').removeClass('hidden');
}

function closeDeleteModal() {
    targetDeleteId = null;
    $('#deleteModal').addClass('hidden');
}

function executeDelete() {
    if (!targetDeleteId) return;

    const $btn = $('#btnDeleteSubmit');

    $btn.prop('disabled', true);

    $.ajax({
        url: "{{ route('master.notification.delete') }}",
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            id: targetDeleteId
        },
        success: function(res) {
            $btn.prop('disabled', false);
            closeDeleteModal();
            if (res.success) {
                showToast('Success', res.message, 'success');
                notifTable.ajax.reload(null, false);
                if (typeof fetchHeaderNotifications === 'function') {
                    fetchHeaderNotifications();
                }
            } else {
                showToast('Error', res.message || 'Failed to delete notification.', 'error');
            }
        },
        error: function(xhr) {
            $btn.prop('disabled', false);
            closeDeleteModal();
            showToast('Error', 'Failed to delete notification.', 'error');
        }
    });
}
</script>
@endpush
@endsection
