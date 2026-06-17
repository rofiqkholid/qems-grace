@extends('layouts.app')

@section('content')
@include('layouts.sidebar')
@include('components.toast')

<div class="lg:ml-20 min-h-screen flex flex-col bg-slate-50">
    @include('layouts.header')

    <!-- Page Content -->
    <main class="flex-1 p-6">
        <!-- Page Title -->
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Department Master</h1>
                <p class="text-slate-500 mt-1">Manage department definitions and their associated details</p>
            </div>

        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
            <!-- Filter Section -->
            <div class="p-6 border-b border-slate-200 bg-slate-50/50">
                <div class="flex flex-wrap items-center gap-3">
                    <!-- Search -->
                    <div class="flex-1 min-w-[200px]">
                        <div class="relative">
                            <input type="text" id="searchInput" placeholder="Search Code or Name..."
                                class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                            <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        </div>
                    </div>
                    <!-- Add Button -->
                    <button onclick="openCreateModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
                        <i class="fa-solid fa-plus"></i>
                        <span>Add Department</span>
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto p-6">
                <table id="departmentTable" class="qms-table w-full">
                    <thead>
                        <tr>
                            <th class="w-[5%] text-center">No</th>
                            <th class="w-[20%]">Code</th>
                            <th class="w-[60%]">Department Name</th>
                            <th class="w-[15%] text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                    </tbody>
                </table>
            </div>
            <!-- Data Count Component -->
            <x-data-table tableId="departmentTable" />
        </div>
    </main>
    @include('layouts.footer')
</div>

<!-- Create Modal -->
<div id="createModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-slate-900/50 transition-opacity" onclick="closeCreateModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl w-full max-w-md transform transition-all">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-slate-800">Add Department</h3>
                <button onclick="closeCreateModal()" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form action="{{ route('master.department.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Code</label>
                        <input type="text" name="key1" required class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all uppercase" placeholder="Enter department code">
                        <p class="text-xs text-slate-500 mt-1">Code must be unique</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Department Name</label>
                        <input type="text" name="desc" required class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all" placeholder="Enter department name">
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
                <h3 class="text-lg font-bold text-slate-800">Edit Department</h3>
                <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form action="{{ route('master.department.update') }}" method="POST">
                @csrf
                <!-- Key1 is the primary key/identifier -->
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Code</label>
                        <input type="text" name="key1" id="edit_key1" readonly class="w-full px-4 py-2 border border-slate-200 bg-slate-100 text-slate-500 rounded-lg outline-none cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Department Name</label>
                        <input type="text" name="desc" id="edit_desc" required class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">
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
                <p class="text-slate-500 text-sm">Are you sure you want to delete this specific Department? This action cannot be undone.</p>
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
        var table = $('#departmentTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('master.department.table') }}",
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
                    data: 'Key1',
                    name: 'Key1',
                    className: 'text-slate-700 font-semibold'
                },
                {
                    data: 'Desc',
                    name: 'Desc',
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
            dom: 'rt<"flex items-center justify-between p-4 border-t border-slate-200"ip>',
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
        const key1 = btn.getAttribute('data-key1');
        const desc = btn.getAttribute('data-desc');

        $('#edit_key1').val(key1);
        $('#edit_desc').val(desc);
        $('#editModal').removeClass('hidden');
    }

    function closeEditModal() {
        $('#editModal').addClass('hidden');
    }

    let deleteKey1 = null;

    function handleDelete(key1, no) {
        deleteKey1 = key1;
        deleteNo = no;
        $('#deleteModal').removeClass('hidden');
    }

    function closeDeleteModal() {
        $('#deleteModal').addClass('hidden');
        deleteKey1 = null;
        deleteNo = null;
    }

    let deleteNo = null;

    function executeDelete() {
        if (!deleteKey1) return;
        const id = deleteKey1;
        const no = deleteNo;

        // Show loader on button
        $('#icon_delete_' + no).addClass('hidden');
        $('#loader_delete_' + no).removeClass('hidden');

        closeDeleteModal();

        $.ajax({
            url: "{{ route('master.department.delete') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                key1: id
            },
            success: function(response) {
                // Hide loader
                $('#icon_delete_' + no).removeClass('hidden');
                $('#loader_delete_' + no).addClass('hidden');

                if (response.success) {
                    showToast('Data deleted successfully', 'success');
                    $('#departmentTable').DataTable().ajax.reload();
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