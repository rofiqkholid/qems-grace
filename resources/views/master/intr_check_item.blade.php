@php
    $hideCentralToast = true;
@endphp
@extends('layouts.app')

@section('title', 'Internal Audit Check Item Master')

@section('content')
@include('layouts.sidebar')
@include('components.toast')

<div class="lg:ml-20 min-h-screen flex flex-col bg-slate-50">
    @include('layouts.header')

    <!-- Page Content -->
    <main class="flex-1 p-6">
        <!-- Page Title -->
        <div class="mb-6">
            <h1 class="text-lg sm:text-2xl font-bold text-slate-800">Internal Audit Check Item Master</h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">Manage check items, departments, and scopes for Internal Audits</p>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
            <!-- Filter Section -->
            <div class="p-4 sm:p-6 border-b border-slate-200 bg-slate-50/50">
                <div class="flex items-center gap-2 sm:gap-3">
                    <!-- Search -->
                    <div class="flex-1">
                        <div class="relative">
                            <input type="text" id="searchInput" placeholder="Search Check Item, Department, Scope..."
                                class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                            <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        </div>
                    </div>
                    <!-- Add Button -->
                    <button onclick="openCreateModal()" class="shrink-0 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg flex items-center gap-1.5 sm:gap-2 transition-colors text-xs sm:text-sm font-medium">
                        <i class="fa-solid fa-plus text-[10px] sm:text-xs"></i>
                        <span>Add Check Item</span>
                    </button>
                </div>
            </div>

            <div class="p-6">
                <table id="checkItemTable" class="qms-table w-full min-w-[1000px]">
                    <thead>
                        <tr>
                            <th class="w-[5%] text-center">No</th>
                            <th class="w-[15%] text-left">Scope Item</th>
                            <th class="w-[15%] text-left">Department</th>
                            <th class="w-[25%] text-left">Check Item (IDN)</th>
                            <th class="w-[25%] text-left">Check Item (EN)</th>
                            <th class="w-[15%] text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                    </tbody>
                </table>
            </div>
            <!-- Data Count Component -->
            <x-data-table tableId="checkItemTable" />
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
        <div class="bg-white rounded-xl w-full max-w-2xl transform transition-all">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-slate-800">Add Internal Audit Check Item</h3>
                <button onclick="closeCreateModal()" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form action="{{ route('master.intr_check_item.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-4">
                    <input type="hidden" name="is_active" value="1">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Scope Item</label>
                        <input type="text" name="scope_item" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all" placeholder="e.g. Equipment calibration">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Department <span class="text-red-500">*</span></label>
                        <x-searchable-select-multi
                            id="create_department"
                            name="department"
                            label="Department"
                            required="true"
                            hideLabel="true"
                            apiUrl="{{ route('genba.get_section') }}"
                            updateEvent="create-department-event"
                            multiple="true"
                            maxItems="0" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Check Item (IDN) <span class="text-red-500">*</span></label>
                        <textarea name="check_item_idn" rows="3" required class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all" placeholder="Deskripsi dalam Bahasa Indonesia..."></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Check Item (EN)</label>
                        <textarea name="check_item_en" rows="3" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all" placeholder="Description in English..."></textarea>
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
        <div class="bg-white rounded-xl w-full max-w-2xl transform transition-all">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-slate-800">Edit Internal Audit Check Item</h3>
                <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form action="{{ route('master.intr_check_item.update') }}" method="POST">
                @csrf
                <input type="hidden" name="id" id="edit_id">
                <input type="hidden" name="is_active" id="edit_is_active" value="1">
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Scope Item</label>
                        <input type="text" name="scope_item" id="edit_scope_item" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Department <span class="text-red-500">*</span></label>
                        <x-searchable-select-multi
                            id="edit_department"
                            name="department"
                            label="Department"
                            required="true"
                            hideLabel="true"
                            apiUrl="{{ route('genba.get_section') }}"
                            updateEvent="edit-department-event"
                            multiple="true"
                            maxItems="0" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Check Item (IDN) <span class="text-red-500">*</span></label>
                        <textarea name="check_item_idn" id="edit_check_item_idn" rows="3" required class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Check Item (EN)</label>
                        <textarea name="check_item_en" id="edit_check_item_en" rows="3" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all"></textarea>
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
                    <i class="fa-solid fa-triangle-exclamation text-2xl text-red-600"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">Confirm Delete</h3>
                <p class="text-slate-500 text-sm">Are you sure you want to delete this check item? This action cannot be undone.</p>
            </div>
            <div class="p-6 pt-0 flex gap-3">
                <button type="button" onclick="closeDeleteModal()" class="flex-1 px-4 py-2 bg-slate-100 text-slate-700 font-medium rounded-lg hover:bg-slate-200 transition-colors">Cancel</button>
                <button type="button" onclick="executeDelete()" class="flex-1 px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors">Delete</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        var table = $('#checkItemTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('master.intr_check_item.table') }}",
                type: 'POST',
                data: function(d) {
                    d._token = "{{ csrf_token() }}";
                    d.search.value = $('#searchInput').val();
                }
            },
            columns: [
                {
                    data: 'no',
                    name: 'no',
                    orderable: false,
                    searchable: false,
                    className: 'text-center font-base text-slate-700'
                },
                {
                    data: 'scope_item',
                    name: 'scope_item',
                    className: 'text-slate-700 text-left'
                },
                {
                    data: 'department',
                    name: 'department',
                    className: 'text-slate-700 text-left font-semibold'
                },
                {
                    data: 'check_item_idn',
                    name: 'check_item_idn',
                    className: 'text-slate-700 text-left whitespace-normal break-words'
                },
                {
                    data: 'check_item_en',
                    name: 'check_item_en',
                    className: 'text-slate-700 text-left whitespace-normal break-words'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    className: 'text-left'
                }
            ],
            language: {
                emptyTable: '<div class="flex flex-col items-center justify-center py-8 text-slate-500"><i class="fa-regular fa-folder-open text-4xl mb-3 text-slate-300"></i><p>No data available</p></div>',
            },
            dom: 'r<"overflow-x-auto"t><"flex flex-col sm:flex-row items-center justify-between p-4 border-t border-slate-200 gap-4"ip>',
            pagingType: "simple_numbers"
        });

        // Search on keyup (debounce)
        var searchTimer;
        $('#searchInput').on('keyup', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function() {
                table.search($('#searchInput').val()).draw();
            }, 500);
        });
    });

    function openCreateModal() {
        $('#createModal').removeClass('hidden');
        
        // Reset department select in create modal
        window.dispatchEvent(new CustomEvent('create-department-event', {
            detail: {
                id: '',
                name: ''
            }
        }));
    }

    function closeCreateModal() {
        $('#createModal').addClass('hidden');
    }

    function handleEdit(btn) {
        const id = btn.getAttribute('data-id');
        const checkItemIdn = btn.getAttribute('data-check_item_idn');
        const checkItemEn = btn.getAttribute('data-check_item_en');
        const department = btn.getAttribute('data-department');
        const scopeItem = btn.getAttribute('data-scope_item');
        const isActive = btn.getAttribute('data-is_active');

        $('#edit_id').val(id);
        $('#edit_check_item_idn').val(checkItemIdn);
        $('#edit_check_item_en').val(checkItemEn);
        
        const depts = {!! json_encode($departments->keyBy('Key1')->map(fn($d) => $d->Desc)->toArray()) !!};
        let deptNameArr = [];
        if (department) {
            const deptKeys = department.split(',').map(s => s.trim()).filter(Boolean);
            deptNameArr = deptKeys.map(k => depts[k] || k);
        }
        const deptNames = deptNameArr.join(', ');

        window.dispatchEvent(new CustomEvent('edit-department-event', {
            detail: {
                id: department,
                name: deptNames
            }
        }));

        $('#edit_scope_item').val(scopeItem);
        $('#edit_is_active').val(isActive);
        $('#editModal').removeClass('hidden');
    }

    function closeEditModal() {
        $('#editModal').addClass('hidden');
    }

    let deleteId = null;
    let deleteNo = null;

    function handleDelete(id, no) {
        deleteId = id;
        deleteNo = no;
        $('#deleteModal').removeClass('hidden');
    }

    function closeDeleteModal() {
        $('#deleteModal').addClass('hidden');
        deleteId = null;
        deleteNo = null;
    }

    function executeDelete() {
        if (!deleteId) return;
        const id = deleteId;
        const no = deleteNo;

        // Show loader on button
        $('#icon_delete_' + no).addClass('hidden');
        $('#loader_delete_' + no).removeClass('hidden');

        closeDeleteModal();

        $.ajax({
            url: "{{ route('master.intr_check_item.delete') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: id
            },
            success: function(response) {
                // Hide loader
                $('#icon_delete_' + no).removeClass('hidden');
                $('#loader_delete_' + no).addClass('hidden');

                if (response.success) {
                    showToast('Data deleted successfully', 'success');
                    $('#checkItemTable').DataTable().ajax.reload();
                } else {
                    showToast('Failed to delete item', 'error');
                }
            },
            error: function() {
                // Hide loader
                $('#icon_delete_' + no).removeClass('hidden');
                $('#loader_delete_' + no).addClass('hidden');

                showToast('An error occurred', 'error');
            }
        });
    }
</script>
@endpush
@endsection