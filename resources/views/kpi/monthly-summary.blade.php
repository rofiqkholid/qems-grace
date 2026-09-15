@extends('layouts.app')

@php
    $hideCentralToast = true;
@endphp

@section('title', 'Monthly Summary')

@section('content')
@include('layouts.sidebar')
@include('components.toast')

<div class="lg:ml-20 min-h-screen flex flex-col bg-slate-50">
    @include('layouts.header')

    <!-- Page Content -->
    <main class="flex-1 p-6">
        <!-- Page Title -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-lg sm:text-2xl font-bold text-slate-800">Monthly Summary</h1>
                <p class="text-slate-500 text-xs sm:text-sm mt-1">Detailed track record of quality metrics and KPI progress across months</p>
            </div>
            <div class="shrink-0">
                <button type="button" id="btnExportPdf" onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold text-xs sm:text-sm rounded-lg transition-colors duration-200">
                    <i class="fa-solid fa-file-pdf text-sm"></i>
                    <span>Export PDF</span>
                </button>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
            <!-- Total KPI Card -->
            <div class="bg-white p-4 rounded-xl border border-slate-200 flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-list-check text-lg"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-medium">Total KPI</p>
                    <h3 id="card_total_kpi" class="text-lg font-bold text-slate-800">0</h3>
                </div>
            </div>

            <!-- Company KPI Card -->
            <div class="bg-white p-4 rounded-xl border border-slate-200 flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-building text-lg"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-medium">Company KPI</p>
                    <h3 id="card_company_kpi" class="text-lg font-bold text-slate-800">0</h3>
                </div>
            </div>

            <!-- Dept KPI Card -->
            <div class="bg-white p-4 rounded-xl border border-slate-200 flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-users-gear text-lg"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-medium">Dept KPI</p>
                    <h3 id="card_dept_kpi" class="text-lg font-bold text-slate-800">0</h3>
                </div>
            </div>

            <!-- Achieved Card -->
            <div class="bg-white p-4 rounded-xl border border-slate-200 flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-medium">Achieved</p>
                    <h3 id="card_achieved" class="text-lg font-bold text-slate-800">0</h3>
                </div>
            </div>

            <!-- Not Achieved Card -->
            <div class="bg-white p-4 rounded-xl border border-slate-200 flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-circle-xmark text-lg"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-medium">Not Achieved</p>
                    <h3 id="card_not_achieved" class="text-lg font-bold text-slate-800">0</h3>
                </div>
            </div>

            <!-- Waiting Data Card -->
            <div class="bg-white p-4 rounded-xl border border-slate-200 flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-clock text-lg"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-medium">Waiting Data</p>
                    <h3 id="card_waiting_data" class="text-lg font-bold text-slate-800">0</h3>
                </div>
            </div>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
            <!-- Filter Section -->
            <div class="p-4 sm:p-6 border-b border-slate-200 bg-slate-50/50 relative z-30">
                <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 sm:gap-4">
                    <!-- Search Input -->
                    <div class="flex-1">
                        <div class="relative">
                            <input type="text" id="searchInput" placeholder="Search metric name, dept, pillar..."
                                class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-xs sm:text-sm outline-none bg-white">
                            <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        </div>
                    </div>

                    <!-- Dropdown Filters -->
                    <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                        <!-- Department Filter -->
                        <div class="min-w-[160px]">
                            <x-searchable-select
                                name="department"
                                id="filter_department"
                                label="Department"
                                apiUrl="{{ route('kpi.company.departments') }}"
                                updateEvent="set-filter-department"
                                :initialOptions="$departments->map(fn($dept) => ['id' => $dept->Key1, 'name' => $dept->Key1])->toArray()"
                                hideLabel="true" />
                        </div>

                        <!-- Pillar Filter -->
                        <div class="min-w-[140px]">
                            <x-searchable-select
                                name="pillar"
                                id="filter_pillar"
                                label="Pillar"
                                apiUrl="{{ route('kpi.monthly-summary.pillars') }}"
                                updateEvent="set-filter-pillar"
                                :initialOptions="$pillars->map(fn($p) => ['id' => $p, 'name' => $p])->toArray()"
                                hideLabel="true" />
                        </div>

                        <!-- Year Filter -->
                        <div class="min-w-[130px]">
                            <x-searchable-select
                                name="year"
                                id="filter_year"
                                label="Year"
                                updateEvent="set-filter-year"
                                :initialOptions="collect($years)->map(fn($y) => ['id' => $y, 'name' => (string)$y])->toArray()"
                                hideLabel="true" />
                        </div>

                        <!-- Reset Filter Button -->
                        <div>
                            <button type="button" id="btnResetFilter" class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg text-sm font-medium transition-colors flex items-center gap-1.5 border border-slate-200">
                                <i class="fa-solid fa-rotate-left text-xs"></i>
                                <span>Reset Filter</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="p-6 overflow-x-auto">
                <table id="monthlySummaryTable" class="qms-table w-full min-w-[1740px]">
                    <thead>
                        <tr>
                            <th class="w-[50px] min-w-[50px] max-w-[50px] text-left whitespace-nowrap pl-3">No</th>
                            <th class="w-[320px] min-w-[320px] max-w-[320px] text-left whitespace-nowrap">KPI Name</th>
                            <th class="w-[110px] min-w-[110px] max-w-[110px] text-left whitespace-nowrap">Pillar</th>
                            <th class="w-[120px] min-w-[120px] max-w-[120px] text-left whitespace-nowrap">Target</th>
                            @foreach($months as $m)
                                <th class="text-left whitespace-nowrap min-w-[85px] w-[85px] px-3">{{ $m }}</th>
                            @endforeach
                            <th class="text-left whitespace-nowrap min-w-[100px] w-[100px]">YTD Avg</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                    </tbody>
                </table>
            </div>

            <!-- Data Count Component -->
            <x-data-table tableId="monthlySummaryTable" />
        </div>
    </main>
    @include('layouts.footer')
</div>

<!-- Mobile Sidebar Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/50 z-30 hidden lg:hidden"></div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Dispatch initial default filter events
        @php
            $deptName = 'All Departments';
            if (!empty($selectedDept)) {
                $foundDept = $departments->firstWhere('Key1', $selectedDept);
                if ($foundDept) {
                    $deptName = $foundDept->Key1;
                }
            }
            $pillarName = !empty($selectedPillar) ? $selectedPillar : 'All Pillars';
            $yearName = $selectedYear;
        @endphp

        window.dispatchEvent(new CustomEvent('set-filter-department', { detail: { id: "{{ $selectedDept ?? '' }}", name: "{{ !empty($selectedDept) ? $deptName : '' }}" } }));
        window.dispatchEvent(new CustomEvent('set-filter-pillar', { detail: { id: "{{ $selectedPillar ?? '' }}", name: "{{ !empty($selectedPillar) ? $selectedPillar : '' }}" } }));
        window.dispatchEvent(new CustomEvent('set-filter-year', { detail: { id: "{{ $selectedYear }}", name: "{{ $yearName }}" } }));

        var table = $('#monthlySummaryTable').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: "{{ route('kpi.monthly-summary.table') }}",
                type: 'POST',
                data: function(d) {
                    d._token = "{{ csrf_token() }}";
                    d.department = $('#filter_department').val();
                    d.pillar = $('#filter_pillar').val();
                    d.year = $('#filter_year').val();
                    d.search.value = $('#searchInput').val();
                },
                dataSrc: function(json) {
                    if (json.summary) {
                        $('#card_total_kpi').text(json.summary.total_kpi || 0);
                        $('#card_company_kpi').text(json.summary.company_kpi || 0);
                        $('#card_dept_kpi').text(json.summary.dept_kpi || 0);
                        $('#card_achieved').text(json.summary.achieved || 0);
                        $('#card_not_achieved').text(json.summary.not_achieved || 0);
                        $('#card_waiting_data').text(json.summary.waiting_data || 0);
                    }
                    return json.data;
                }
            },
            columns: [
                { data: 'no', name: 'no', orderable: false, searchable: false, className: 'text-left font-base text-slate-700 w-[50px] min-w-[50px] max-w-[50px] pl-3' },
                { data: 'objective', name: 'kl.objective', className: 'font-normal text-slate-800 w-[320px] min-w-[320px] max-w-[320px] text-left leading-snug' },
                { data: 'pillar', name: 'kl.pillar', className: 'text-xs font-normal text-slate-500 whitespace-nowrap w-[110px] min-w-[110px] max-w-[110px] text-left' },
                { data: 'target', name: 'target', className: 'text-slate-600 font-normal whitespace-nowrap w-[120px] min-w-[120px] max-w-[120px] text-left' },
                @foreach($months as $m)
                    { data: '{{ $m }}', name: '{{ $m }}', className: 'text-left whitespace-nowrap px-3 w-[85px] min-w-[85px]', orderable: false, searchable: false },
                @endforeach
                { data: 'ytd_avg', name: 'ytd_avg', className: 'text-left font-medium text-slate-700 whitespace-nowrap w-[100px] min-w-[100px]', orderable: false, searchable: false }
            ],
            language: {
                emptyTable: '<div class="flex flex-col items-center justify-center py-8 text-slate-500"><i class="fa-regular fa-folder-open text-4xl mb-3 text-slate-300"></i><p>No KPI records found</p></div>',
            },
            dom: 'r<"overflow-x-auto"t><"flex flex-col sm:flex-row items-center justify-between p-4 border-t border-slate-200 gap-4"ip>',
            pagingType: "simple_numbers"
        });

        $('#filter_department, #filter_pillar, #filter_year').on('change', function() {
            table.ajax.reload();
        });

        $('#btnResetFilter').on('click', function() {
            $('#searchInput').val('');
            $('#filter_department').val('');
            $('#filter_pillar').val('');
            $('#filter_year').val('{{ date("Y") }}');

            window.dispatchEvent(new CustomEvent('set-filter-department', { detail: { id: '', name: '' } }));
            window.dispatchEvent(new CustomEvent('set-filter-pillar', { detail: { id: '', name: '' } }));
            window.dispatchEvent(new CustomEvent('set-filter-year', { detail: { id: '{{ date("Y") }}', name: '{{ date("Y") }}' } }));

            table.search('').draw();
        });

        var searchTimer;
        $('#searchInput').on('keyup', function() {
            var value = this.value;
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function() {
                table.search(value).draw();
            }, 300);
        });
    });
</script>
@endpush
@endsection