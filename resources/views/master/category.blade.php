@extends('layouts.app')

@section('title', 'Category Master')

@section('content')
@include('layouts.sidebar')
@include('components.toast')

<div class="lg:ml-20 min-h-screen flex flex-col bg-slate-50">
    @include('layouts.header')

    <!-- Page Content -->
    <main class="flex-1 p-6">
        <!-- Page Title -->
        <div class="mb-6">
            <h1 class="text-lg sm:text-2xl font-bold text-slate-800">Category Master</h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">Manage category definitions and their associated details</p>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
            <!-- Filter Section -->
            <div class="p-4 sm:p-6 border-b border-slate-200 bg-slate-50/50">
                <div class="flex items-center gap-2 sm:gap-3">
                    <!-- Search -->
                    <div class="flex-1">
                        <div class="relative">
                            <input type="text" id="searchInput" placeholder="Search Category or Description..."
                                class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                            <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        </div>
                    </div>
                    <!-- Add Button -->
                    <button onclick="openCreateModal()" class="shrink-0 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg flex items-center gap-1.5 sm:gap-2 transition-colors text-xs sm:text-sm font-medium">
                        <i class="fa-solid fa-plus text-[10px] sm:text-xs"></i>
                        <span>Add Category</span>
                    </button>
                </div>
            </div>

            <div class="p-6">
                <table id="categoryTable" class="qms-table w-full min-w-[800px]">
                    <thead>
                        <tr>
                            <th class="w-[5%] text-center">No</th>
                            <th class="w-[40%]">Category</th>
                            <th class="w-[40%]">Description</th>
                            <th class="w-[15%] text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                    </tbody>
                </table>
            </div>
            <!-- Data Count Component -->
            <x-data-table tableId="categoryTable" />
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
        <div class="bg-white rounded-xl w-full max-w-md transform transition-all">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-slate-800">Add Category</h3>
                <button onclick="closeCreateModal()" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form action="{{ route('master.category.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Category</label>
                        <input type="text" name="category" required class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all" placeholder="Enter category">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                        <input type="text" name="description" required class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all" placeholder="Enter description">
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
        <div class="bg-white rounded-xl w-full max-w-md transform transition-all">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-slate-800">Edit Category</h3>
                <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form action="{{ route('master.category.update') }}" method="POST">
                @csrf
                <input type="hidden" name="sys_id" id="edit_sys_id">
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Category</label>
                        <input type="text" name="category" id="edit_category" required class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                        <input type="text" name="description" id="edit_description" required class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">
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
                <p class="text-slate-500 text-sm">Are you sure you want to delete this item? This action cannot be undone.</p>
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
        var table = $('#categoryTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('master.category.table') }}",
                type: 'POST',
                data: function(d) {
                    d._token = "{{ csrf_token() }}";
                    d.search.value = $('#searchInput').val();
                }
            },
            columns: [{
                    data: 'no',
                    name: 'no',
                    orderable: false,
                    searchable: false,
                    className: 'text-center font-base text-slate-700'
                },
                {
                    data: 'Category',
                    name: 'Category',
                    className: 'text-slate-700'
                },
                {
                    data: 'Description',
                    name: 'Description',
                    className: 'text-slate-700'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    className: 'text-center'
                }
            ],
            language: {
                emptyTable: '<div class="flex flex-col items-center justify-center py-8 text-slate-500"><i class="fa-regular fa-folder-open text-4xl mb-3 text-slate-300"></i><p>No data available</p></div>',
            },
            dom: 'r<"overflow-x-auto"t><"flex flex-col sm:flex-row items-center justify-between p-4 border-t border-slate-200 gap-4"ip>',
            pagingType: "simple_numbers",
            drawCallback: function(settings) {
                // Re-apply any needed generic styles or behaviors here
            }
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
    }

    function closeCreateModal() {
        $('#createModal').addClass('hidden');
    }

    function handleEdit(btn) {
        const sysId = btn.getAttribute('data-sysid');
        const category = btn.getAttribute('data-category');
        const description = btn.getAttribute('data-description');

        $('#edit_sys_id').val(sysId);
        $('#edit_category').val(category);
        $('#edit_description').val(description);
        $('#editModal').removeClass('hidden');
    }

    function closeEditModal() {
        $('#editModal').addClass('hidden');
    }

    let deleteId = null;

    function handleDelete(sysId, no) {
        deleteId = sysId;
        deleteNo = no;
        $('#deleteModal').removeClass('hidden');
    }

    function closeDeleteModal() {
        $('#deleteModal').addClass('hidden');
        deleteId = null;
        deleteNo = null;
    }

    let deleteNo = null;

    function executeDelete() {
        if (!deleteId) return;
        const id = deleteId;
        const no = deleteNo;

        // Show loader on button
        $('#icon_delete_' + no).addClass('hidden');
        $('#loader_delete_' + no).removeClass('hidden');

        closeDeleteModal();

        $.ajax({
            url: "{{ route('master.category.delete') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                sys_id: id
            },
            success: function(response) {
                // Hide loader
                $('#icon_delete_' + no).removeClass('hidden');
                $('#loader_delete_' + no).addClass('hidden');

                if (response.success) {
                    showToast('Data deleted successfully', 'success');
                    $('#categoryTable').DataTable().ajax.reload();
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