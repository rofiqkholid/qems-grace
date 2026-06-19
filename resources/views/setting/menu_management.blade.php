@extends('layouts.app')

@section('title', 'Menu Management')

@section('content')
@include('layouts.sidebar')
@include('components.toast')

<div class="lg:ml-20 min-h-screen flex flex-col bg-slate-50">
    @include('layouts.header')

    <!-- Page Content -->
    <main class="flex-1 p-6">
        <!-- Page Title -->
        <div class="mb-6">
            <h1 class="text-lg sm:text-2xl font-bold text-slate-800">Menu Management</h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">View registered system menus and their level structures</p>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
            <!-- Filter Section -->
            <div class="p-4 sm:p-6 border-b border-slate-200 bg-slate-50/50">
                <div class="flex items-center gap-2 sm:gap-3">
                    <!-- Search -->
                    <div class="flex-1">
                        <div class="relative">
                            <input type="text" id="searchInput" placeholder="Search Menu Name or Path..."
                                class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                            <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <table id="menuTable" class="qms-table w-full min-w-[800px]">
                    <thead>
                        <tr>
                            <th class="w-[5%] text-center">No</th>
                            <th class="w-[10%] text-center">Menu ID</th>
                            <th class="w-[35%]">Menu Name</th>
                            <th class="w-[30%]">URL Path</th>
                            <th class="w-[10%] text-center">Level ID</th>
                            <th class="w-[10%] text-center">Seq ID</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                    </tbody>
                </table>
            </div>
            <!-- Data Count Component -->
            <x-data-table tableId="menuTable" />
        </div>
    </main>
    @include('layouts.footer')
</div>

<!-- Mobile Sidebar Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/50 z-30 hidden lg:hidden"></div>

@push('scripts')
<script>
    $(document).ready(function() {
        var table = $('#menuTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('master.menu_management.table') }}",
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
                    data: 'id',
                    name: 'id',
                    className: 'text-center font-semibold text-slate-700'
                },
                {
                    data: 'menu_name',
                    name: 'menu_name',
                    className: 'text-slate-700 font-medium'
                },
                {
                    data: 'menu',
                    name: 'menu',
                    className: 'text-slate-600 font-mono text-xs'
                },
                {
                    data: 'level_menu_id',
                    name: 'level_menu_id',
                    className: 'text-center text-slate-700'
                },
                {
                    data: 'sequence_id',
                    name: 'sequence_id',
                    className: 'text-center text-slate-700'
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
            clearTimer(searchTimer);
            searchTimer = setTimeout(function() {
                table.search($('#searchInput').val()).draw();
            }, 500);
        });
    });
</script>
@endpush
@endsection
