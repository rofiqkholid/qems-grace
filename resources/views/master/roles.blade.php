@extends('layouts.app')

@php
    $hideCentralToast = true;
@endphp

@section('title', 'Roles Master')

@section('content')
@include('layouts.sidebar')
@include('components.toast')

<div class="lg:ml-20 min-h-screen flex flex-col bg-slate-50">
    @include('layouts.header')

    <!-- Page Content -->
    <main class="flex-1 p-6">
        <!-- Page Title -->
        <div class="mb-6">
            <h1 class="text-lg sm:text-2xl font-bold text-slate-800">Roles Master</h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">Manage user roles definitions</p>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
            <!-- Filter Section -->
            <div class="p-4 sm:p-6 border-b border-slate-200 bg-slate-50/50">
                <div class="flex items-center gap-2 sm:gap-3">
                    <!-- Search -->
                    <div class="flex-1">
                        <div class="relative">
                            <input type="text" id="searchInput" placeholder="Search Role Name..."
                                class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                            <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        </div>
                    </div>
                    <!-- Add Button -->
                    <button onclick="openCreateModal()" class="shrink-0 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg flex items-center gap-1.5 sm:gap-2 transition-colors text-xs sm:text-sm font-medium">
                        <i class="fa-solid fa-plus text-[10px] sm:text-xs"></i>
                        <span>Add Role</span>
                    </button>
                </div>
            </div>

            <div class="p-6">
                <table id="rolesTable" class="qms-table w-full min-w-[800px]">
                    <thead>
                        <tr>
                            <th class="w-[10%] text-center">No</th>
                            <th class="w-[75%]">Role Name</th>
                            <th class="w-[15%]">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                    </tbody>
                </table>
            </div>
            <!-- Data Count Component -->
            <x-data-table tableId="rolesTable" />
        </div>
    </main>
    @include('layouts.footer')
</div>

<!-- Mobile Sidebar Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/50 z-30 hidden lg:hidden"></div>

<!-- Create Modal -->
<div id="createModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-slate-900/50 transition-opacity" onclick="closeCreateModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl w-full max-w-md transform transition-all animate-fade-in">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-slate-800">Add Role</h3>
                <button onclick="closeCreateModal()" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form id="createForm" action="{{ route('master.roles.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Role Name</label>
                        <input type="text" name="role_name" required class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" placeholder="Enter role name (e.g. MANAGER)">
                    </div>
                </div>
                <div class="p-6 border-t border-slate-100 bg-slate-50/50 flex justify-end gap-3 rounded-b-xl">
                    <button type="button" onclick="closeCreateModal()" class="px-4 py-2 text-slate-700 font-medium hover:bg-slate-200 rounded-lg transition-colors">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-slate-900/50 transition-opacity" onclick="closeEditModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl w-full max-w-md transform transition-all animate-fade-in">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-slate-800">Edit Role</h3>
                <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form id="editForm" action="{{ route('master.roles.update') }}" method="POST">
                @csrf
                <input type="hidden" name="id" id="edit_id">
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Role Name</label>
                        <input type="text" name="role_name" id="edit_role_name" required class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    </div>
                </div>
                <div class="p-6 border-t border-slate-100 bg-slate-50/50 flex justify-end gap-3 rounded-b-xl">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-slate-700 font-medium hover:bg-slate-200 rounded-lg transition-colors">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-slate-900/50 transition-opacity" onclick="closeDeleteModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl w-full max-w-sm transform transition-all">
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">Delete Role</h3>
                <p class="text-sm text-slate-500 mb-6">Are you sure you want to delete this role? This action cannot be undone.</p>
                <div class="flex gap-3 justify-center">
                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-lg transition-colors min-w-[80px]">No</button>
                    <button type="button" id="confirmDeleteBtn" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors min-w-[80px]">Yes</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let table;
    let deleteId = null;
    let deleteRowNo = null;

    $(document).ready(function() {
        table = $('#rolesTable').DataTable({
            processing: true,
            serverSide: true,
            dom: '<"overflow-x-auto"t>',
            ajax: {
                url: "{{ route('master.roles.table') }}",
                type: 'POST',
                data: function(d) {
                    d._token = "{{ csrf_token() }}";
                    d.search = { value: $('#searchInput').val() };
                }
            },
            columns: [
                { data: 'no', className: 'text-center' },
                { data: 'role_name' },
                { data: 'action', orderable: false }
            ],
            order: [[1, 'asc']],
            pageLength: 10,
            language: {
                emptyTable: '<div class="text-center py-8 text-slate-500">No roles available</div>'
            }
        });

        // Delay search keyup
        let searchTimer;
        $('#searchInput').on('keyup', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function() {
                table.draw();
            }, 500);
        });

        // AJAX Create Form
        $('#createForm').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function(res) {
                    if (res.success) {
                        showToast(res.message, 'success');
                        closeCreateModal();
                        table.draw();
                    } else {
                        showToast(res.message, 'error');
                    }
                },
                error: function(xhr) {
                    const errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred';
                    showToast(errorMsg, 'error');
                }
            });
        });

        // AJAX Edit Form
        $('#editForm').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function(res) {
                    if (res.success) {
                        showToast(res.message, 'success');
                        closeEditModal();
                        table.draw();
                    } else {
                        showToast(res.message, 'error');
                    }
                },
                error: function(xhr) {
                    const errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred';
                    showToast(errorMsg, 'error');
                }
            });
        });

        // Confirm Delete Click
        $('#confirmDeleteBtn').on('click', function() {
            if (!deleteId) return;

            $(`#icon_delete_${deleteRowNo}`).addClass('hidden');
            $(`#loader_delete_${deleteRowNo}`).removeClass('hidden');
            closeDeleteModal();

            $.ajax({
                url: "{{ route('master.roles.delete') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    id: deleteId
                },
                success: function(res) {
                    if (res.success) {
                        showToast('Data deleted successfully.', 'success');
                        table.draw();
                    } else {
                        showToast(res.message, 'error');
                        table.draw();
                    }
                },
                error: function() {
                    showToast('Failed to delete data.', 'error');
                    table.draw();
                }
            });
        });
    });

    function openCreateModal() {
        $('#createForm')[0].reset();
        $('#createModal').removeClass('hidden');
    }

    function closeCreateModal() {
        $('#createModal').addClass('hidden');
    }

    function handleEdit(btn) {
        const id = $(btn).data('id');
        const role_name = $(btn).data('role_name');

        $('#edit_id').val(id);
        $('#edit_role_name').val(role_name);

        $('#editModal').removeClass('hidden');
    }

    function closeEditModal() {
        $('#editModal').addClass('hidden');
    }

    function handleDelete(id, rowNo) {
        deleteId = id;
        deleteRowNo = rowNo;
        $('#deleteModal').removeClass('hidden');
    }

    function closeDeleteModal() {
        $('#deleteModal').addClass('hidden');
    }
</script>
@endpush
