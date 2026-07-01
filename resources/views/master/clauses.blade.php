@extends('layouts.app')

@php
    $hideCentralToast = true;
@endphp

@section('title', 'Clauses Master')

@section('content')
@include('layouts.sidebar')
@include('components.toast')

<div class="lg:ml-20 min-h-screen flex flex-col bg-slate-50">
    @include('layouts.header')

    <!-- Page Content -->
    <main class="flex-1 p-6">
        <!-- Page Title -->
        <div class="mb-6">
            <h1 class="text-lg sm:text-2xl font-bold text-slate-800">Clauses Master</h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">Manage clauses definitions and their associated details</p>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
            <!-- Filter Section -->
            <div class="p-4 sm:p-6 border-b border-slate-200 bg-slate-50/50">
                <div class="flex items-center gap-2 sm:gap-3">
                    <!-- Search -->
                    <div class="flex-1">
                        <div class="relative">
                            <input type="text" id="searchInput" placeholder="Search Clause No or Name..."
                                class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                            <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        </div>
                    </div>
                    <!-- Add Button -->
                    <button onclick="openCreateModal()" class="shrink-0 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg flex items-center gap-1.5 sm:gap-2 transition-colors text-xs sm:text-sm font-medium">
                        <i class="fa-solid fa-plus text-[10px] sm:text-xs"></i>
                        <span>Add Clause</span>
                    </button>
                </div>
            </div>

            <div class="p-6">
                <table id="clausesTable" class="qms-table w-full min-w-[800px]">
                    <thead>
                        <tr>
                            <th class="w-[5%] text-center">No</th>
                            <th class="w-[20%]">IATF/ISO Req</th>
                            <th class="w-[30%]">Clause Title</th>
                            <th class="w-[30%]">Clauses</th>
                            <th class="w-[15%] text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                    </tbody>
                </table>
            </div>
            <!-- Data Count Component -->
            <x-data-table tableId="clausesTable" />
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
        <div class="bg-white rounded-xl w-full max-w-3xl transform transition-all">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-slate-800">Add Clause</h3>
                <button onclick="closeCreateModal()" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form id="createForm" action="{{ route('master.clauses.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">IATF/ISO Req</label>
                        <input type="text" name="clause_no" required class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all uppercase" placeholder="Enter IATF/ISO Req (e.g. ISO 9001 - 8.5.2)">
                        <p class="text-xs text-slate-500 mt-1">IATF/ISO Req must be unique</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Clause Title</label>
                        <input type="text" name="clause_title" required class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" placeholder="Enter clause title">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Clauses</label>
                        <textarea name="clauses" style="min-height: 42px;" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all resize-none overflow-hidden autogrow-textarea" placeholder="Enter clauses description/text" rows="1"></textarea>
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
        <div class="bg-white rounded-xl w-full max-w-3xl transform transition-all">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-slate-800">Edit Clause</h3>
                <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form id="editForm" action="{{ route('master.clauses.update') }}" method="POST">
                @csrf
                <input type="hidden" name="id" id="edit_id">
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">IATF/ISO Req</label>
                        <input type="text" name="clause_no" id="edit_clause_no" required class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all uppercase" placeholder="Enter IATF/ISO Req">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Clause Title</label>
                        <input type="text" name="clause_title" id="edit_clause_title" required class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Clauses</label>
                        <textarea name="clauses" id="edit_clauses" style="min-height: 42px;" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all resize-none overflow-hidden autogrow-textarea" rows="1"></textarea>
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
                <p class="text-slate-500 text-sm">Are you sure you want to delete this specific Clause? This action cannot be undone.</p>
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
    function autoGrow(element) {
        if (!element || element.scrollHeight === 0) return;
        element.style.height = "auto";
        element.style.height = (element.scrollHeight) + "px";
    }

    $(document).ready(function() {
        // Auto-grow textareas on input
        $(document).on('input', '.autogrow-textarea', function() {
            autoGrow(this);
        });

        // Initialize autogrow on load
        $('.autogrow-textarea').each(function() {
            var self = this;
            setTimeout(function() {
                autoGrow(self);
            }, 10);
        });
        var table = $('#clausesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('master.clauses.table') }}",
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
                    data: 'clause_no',
                    name: 'clause_no',
                    className: 'text-slate-700 font-semibold'
                },
                {
                    data: 'clause_title',
                    name: 'clause_title',
                    className: 'text-slate-700'
                },
                {
                    data: 'clauses',
                    name: 'clauses',
                    className: 'text-slate-700 max-w-xs truncate'
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

        // AJAX Form Submission for Create
        $('#createForm').on('submit', function(e) {
            e.preventDefault();
            const form = this;
            const submitBtn = $(form).find('button[type="submit"]');
            const originalText = submitBtn.html();
            
            submitBtn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-2"></i> Saving...');
            
            $.ajax({
                url: form.action,
                type: 'POST',
                data: $(form).serialize(),
                success: function(response) {
                    closeCreateModal();
                    showToast('Data added successfully.', 'success');
                    table.ajax.reload();
                    form.reset();
                },
                error: function(xhr) {
                    showToast(xhr.responseJSON?.message || 'Failed to add data.', 'error');
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });

        // AJAX Form Submission for Edit
        $('#editForm').on('submit', function(e) {
            e.preventDefault();
            const form = this;
            const submitBtn = $(form).find('button[type="submit"]');
            const originalText = submitBtn.html();
            
            submitBtn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-2"></i> Updating...');
            
            $.ajax({
                url: form.action,
                type: 'POST',
                data: $(form).serialize(),
                success: function(response) {
                    closeEditModal();
                    showToast('Data updated successfully.', 'success');
                    table.ajax.reload(null, false); // Reload KEEPING page
                },
                error: function(xhr) {
                    showToast(xhr.responseJSON?.message || 'Failed to update data.', 'error');
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
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
        const ta = document.querySelector('#createModal .autogrow-textarea');
        if (ta) {
            setTimeout(function() {
                autoGrow(ta);
            }, 50);
        }
    }

    // Reset create modal fields
    function closeCreateModal() {
        $('#createModal').addClass('hidden');
        $('#createModal').find('form')[0].reset();
    }

    function handleEdit(btn) {
        const id = btn.getAttribute('data-id');
        const clause_no = btn.getAttribute('data-clause_no');
        const clause_title = btn.getAttribute('data-clause_title');
        const clauses = btn.getAttribute('data-clauses');

        $('#edit_id').val(id);
        $('#edit_clause_no').val(clause_no);
        $('#edit_clause_title').val(clause_title);
        $('#edit_clauses').val(clauses);
        $('#editModal').removeClass('hidden');
        
        // Trigger auto-grow for edit clauses textarea after value is loaded
        const editClauses = document.getElementById('edit_clauses');
        if (editClauses) {
            setTimeout(() => {
                autoGrow(editClauses);
            }, 50);
        }
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
            url: "{{ route('master.clauses.delete') }}",
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
                    $('#clausesTable').DataTable().ajax.reload();
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