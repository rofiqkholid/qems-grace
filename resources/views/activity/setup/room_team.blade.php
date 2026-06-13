@extends('layouts.app')

@section('title', 'Room Team Collaboration - QMS')

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
            <div class="mb-6 flex items-center gap-4">
                <a href="{{ route('genba_team') }}" 
                    class="inline-flex items-center justify-center w-10 h-10 bg-white text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-50 hover:text-blue-600 transition-all shadow-sm"
                    title="Back to My Genba">
                    <i class="fa-solid fa-arrow-left text-sm"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Room Team Collaboration</h1>
                    <p class="text-slate-500 text-sm mt-1">Checksheets where you are invited as a team member</p>
                </div>
            </div>

            <!-- Main Card -->
            <div class="bg-white rounded-lg border border-slate-200">
                <!-- Filter Section -->
                <div class="p-6 border-b border-slate-200 bg-slate-50/50">
                    <div class="flex flex-wrap items-center gap-3">
                        <!-- Search -->
                        <div class="flex-1 min-w-[200px]">
                            <div class="relative">
                                <input type="text" id="searchInput" placeholder="Search invited sessions..."
                                    class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none">
                                <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            </div>
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
                    <table id="genbaRoomTable" class="qms-table w-full">
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
    </main>

    @include('layouts.footer')
</div>
@endsection

@push('scripts')
<script>
    var currentStatusId = 0;
    var table;

    $(document).ready(function() {
        table = $('#genbaRoomTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('genba.header.table') }}",
                type: 'POST',
                data: function(d) {
                    d._token = "{{ csrf_token() }}";
                    d.front_table_search = $('#searchInput').val();
                    d.status_id = currentStatusId;
                    d.is_room_team = 'true'; // Forced to true for this page
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
                        // For room team, we might want to hide delete actions, but keep view/edit
                        return '<div class="flex items-center gap-2">' + data + '</div>';
                    }
                }
            ],
            order: [[1, 'desc']],
            pageLength: 10,
            language: {
                emptyTable: '<div class="text-center py-8 text-slate-500">No invited genba sessions found</div>',
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
    });

    function document_view(id, no) {
        // Redirect or open checksheet logic from original qms
        // For now, assuming document_view is globally available or handled similarly
        // In this app, it usually calls loadGenbaActivity, but since we are in a simple list here:
        window.location.href = "{{ url('genba_management') }}?id=" + id;
    }

    function filterByStatus(statusId) {
        currentStatusId = statusId;
        $('.status-tab').removeClass('text-blue-600 border-blue-600').addClass('text-slate-600 border-transparent');
        $(event.target).addClass('text-blue-600 border-blue-600').removeClass('text-slate-600 border-transparent');
        table.ajax.reload();
    }
</script>
@endpush
