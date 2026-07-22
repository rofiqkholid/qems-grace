@extends('layouts.app')

@php
    $hideCentralToast = true;
@endphp

@section('title', 'User Auditor Master')

@section('content')
@include('layouts.sidebar')
@include('components.toast')

<div class="lg:ml-20 min-h-screen flex flex-col bg-slate-50">
    @include('layouts.header')

    <!-- Page Content -->
    <main class="flex-1 p-6">
        <!-- Page Title -->
        <div class="mb-6">
            <h1 class="text-lg sm:text-2xl font-bold text-slate-800">User Auditor Master</h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">Manage user auditor definitions</p>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
            <!-- Filter Section -->
            <div class="p-4 sm:p-6 border-b border-slate-200 bg-slate-50/50">
                <div class="flex items-center gap-2 sm:gap-3">
                    <!-- Search -->
                    <div class="flex-1">
                        <div class="relative">
                            <input type="text" id="searchInput" placeholder="Search NIK, Full Name..."
                                class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                            <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Section -->
            <div class="p-6">
                <table id="userAuditorTable" class="qms-table w-full min-w-[800px]">
                    <thead>
                        <tr>
                            <th class="w-[8%] text-center">No</th>
                            <th class="w-[20%]">NIK</th>
                            <th class="w-[57%]">Full Name</th>
                            <th class="w-[15%]">Is Auditor</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        <!-- DataTables dynamic content -->
                    </tbody>
                </table>
            </div>
            <!-- Data Count Component -->
            <x-data-table tableId="userAuditorTable" />
        </div>
    </main>
    @include('layouts.footer')
</div>
@endsection

@push('scripts')
<script>
    let table;

    $(document).ready(function() {
        table = $('#userAuditorTable').DataTable({
            processing: true,
            serverSide: true,
            dom: '<"overflow-x-auto"t>',
            ajax: {
                url: "{{ route('master.user_auditor.table') }}",
                type: 'POST',
                data: function(d) {
                    d._token = "{{ csrf_token() }}";
                    d.search = { value: $('#searchInput').val() };
                }
            },
            columns: [
                { data: 'no', className: 'text-center' },
                { data: 'username' },
                { data: 'full_name' },
                { data: 'is_auditor', orderable: false }
            ],
            order: [[1, 'asc']],
            pageLength: 25,
            language: {
                emptyTable: '<div class="text-center py-8 text-slate-500">No users available</div>'
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
    });

    function toggleAuditor(checkbox, userId) {
        const isChecked = checkbox.checked ? 1 : 0;
        
        // Disable checkbox during AJAX
        checkbox.disabled = true;

        $.ajax({
            url: "{{ route('master.user_auditor.toggle') }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                id_user: userId,
                is_auditor: isChecked
            },
            success: function(res) {
                checkbox.disabled = false;
                if (res.success) {
                    showToast(res.message, 'success');
                } else {
                    showToast(res.message, 'error');
                    // Revert checkbox state
                    checkbox.checked = !checkbox.checked;
                }
            },
            error: function(xhr) {
                checkbox.disabled = false;
                const errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred';
                showToast(errorMsg, 'error');
                // Revert checkbox state
                checkbox.checked = !checkbox.checked;
            }
        });
    }
</script>
@endpush
