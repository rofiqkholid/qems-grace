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

        <!-- Summary Cards Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-2.5 sm:gap-3.5 mb-6">
            <!-- 1. Total KPI -->
            <div class="relative overflow-hidden bg-white p-3 sm:p-4 rounded-xl border border-slate-200/80 transition-all duration-200 flex items-center gap-2.5 sm:gap-4 group">
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-blue-500/10 rounded-full blur-2xl pointer-events-none group-hover:bg-blue-500/20 transition-all"></div>
                <i class="fa-solid fa-list-check absolute -right-2 -bottom-3 text-6xl text-blue-600/[0.07] -rotate-12 pointer-events-none group-hover:scale-110 group-hover:rotate-0 transition-all duration-300"></i>
                <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-lg sm:rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0 border border-blue-100/60 relative z-10">
                    <i class="fa-solid fa-list-check text-base sm:text-lg"></i>
                </div>
                <div class="min-w-0 flex-1 relative z-10">
                    <p class="text-[11px] sm:text-xs font-semibold text-slate-500 whitespace-nowrap overflow-hidden text-ellipsis">Total KPI</p>
                    <p id="card_total_kpi" class="text-lg sm:text-2xl font-bold text-slate-800 leading-tight">0</p>
                </div>
            </div>

            <!-- 2. Company KPI -->
            <div class="relative overflow-hidden bg-white p-3 sm:p-4 rounded-xl border border-slate-200/80 transition-all duration-200 flex items-center gap-2.5 sm:gap-4 group">
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-indigo-500/10 rounded-full blur-2xl pointer-events-none group-hover:bg-indigo-500/20 transition-all"></div>
                <i class="fa-solid fa-building absolute -right-2 -bottom-3 text-6xl text-indigo-600/[0.07] -rotate-12 pointer-events-none group-hover:scale-110 group-hover:rotate-0 transition-all duration-300"></i>
                <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-lg sm:rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0 border border-indigo-100/60 relative z-10">
                    <i class="fa-solid fa-building text-base sm:text-lg"></i>
                </div>
                <div class="min-w-0 flex-1 relative z-10">
                    <p class="text-[11px] sm:text-xs font-semibold text-slate-500 whitespace-nowrap overflow-hidden text-ellipsis">Company KPI</p>
                    <p id="card_company_kpi" class="text-lg sm:text-2xl font-bold text-slate-800 leading-tight">0</p>
                </div>
            </div>

            <!-- 3. Dept KPI -->
            <div class="relative overflow-hidden bg-white p-3 sm:p-4 rounded-xl border border-slate-200/80 transition-all duration-200 flex items-center gap-2.5 sm:gap-4 group">
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-purple-500/10 rounded-full blur-2xl pointer-events-none group-hover:bg-purple-500/20 transition-all"></div>
                <i class="fa-solid fa-users-gear absolute -right-2 -bottom-3 text-6xl text-purple-600/[0.07] -rotate-12 pointer-events-none group-hover:scale-110 group-hover:rotate-0 transition-all duration-300"></i>
                <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-lg sm:rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center flex-shrink-0 border border-purple-100/60 relative z-10">
                    <i class="fa-solid fa-users-gear text-base sm:text-lg"></i>
                </div>
                <div class="min-w-0 flex-1 relative z-10">
                    <p class="text-[11px] sm:text-xs font-semibold text-slate-500 whitespace-nowrap overflow-hidden text-ellipsis">Dept KPI</p>
                    <p id="card_dept_kpi" class="text-lg sm:text-2xl font-bold text-slate-800 leading-tight">0</p>
                </div>
            </div>

            <!-- 4. Achieved -->
            <div class="relative overflow-hidden bg-white p-3 sm:p-4 rounded-xl border border-slate-200/80 transition-all duration-200 flex items-center gap-2.5 sm:gap-4 group">
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none group-hover:bg-emerald-500/20 transition-all"></div>
                <i class="fa-solid fa-circle-check absolute -right-2 -bottom-3 text-6xl text-emerald-600/[0.07] -rotate-12 pointer-events-none group-hover:scale-110 group-hover:rotate-0 transition-all duration-300"></i>
                <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-lg sm:rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0 border border-emerald-100/60 relative z-10">
                    <i class="fa-solid fa-circle-check text-base sm:text-lg"></i>
                </div>
                <div class="min-w-0 flex-1 relative z-10">
                    <p class="text-[11px] sm:text-xs font-semibold text-slate-500 whitespace-nowrap overflow-hidden text-ellipsis">Achieved</p>
                    <p id="card_achieved" class="text-lg sm:text-2xl font-bold text-slate-800 leading-tight">0</p>
                </div>
            </div>

            <!-- 5. Not Achieved -->
            <div class="relative overflow-hidden bg-white p-3 sm:p-4 rounded-xl border border-slate-200/80 transition-all duration-200 flex items-center gap-2.5 sm:gap-4 group">
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-rose-500/10 rounded-full blur-2xl pointer-events-none group-hover:bg-rose-500/20 transition-all"></div>
                <i class="fa-solid fa-circle-xmark absolute -right-2 -bottom-3 text-6xl text-rose-600/[0.07] -rotate-12 pointer-events-none group-hover:scale-110 group-hover:rotate-0 transition-all duration-300"></i>
                <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-lg sm:rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0 border border-rose-100/60 relative z-10">
                    <i class="fa-solid fa-circle-xmark text-base sm:text-lg"></i>
                </div>
                <div class="min-w-0 flex-1 relative z-10">
                    <p class="text-[11px] sm:text-xs font-semibold text-slate-500 whitespace-nowrap overflow-hidden text-ellipsis">Not Achieved</p>
                    <p id="card_not_achieved" class="text-lg sm:text-2xl font-bold text-slate-800 leading-tight">0</p>
                </div>
            </div>

            <!-- 6. Waiting Data -->
            <div class="relative overflow-hidden bg-white p-3 sm:p-4 rounded-xl border border-slate-200/80 transition-all duration-200 flex items-center gap-2.5 sm:gap-4 group">
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-amber-500/10 rounded-full blur-2xl pointer-events-none group-hover:bg-amber-500/20 transition-all"></div>
                <i class="fa-solid fa-clock absolute -right-2 -bottom-3 text-6xl text-amber-600/[0.07] -rotate-12 pointer-events-none group-hover:scale-110 group-hover:rotate-0 transition-all duration-300"></i>
                <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-lg sm:rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0 border border-amber-100/60 relative z-10">
                    <i class="fa-solid fa-clock text-base sm:text-lg"></i>
                </div>
                <div class="min-w-0 flex-1 relative z-10">
                    <p class="text-[11px] sm:text-xs font-semibold text-slate-500 whitespace-nowrap overflow-hidden text-ellipsis">Waiting Data</p>
                    <p id="card_waiting_data" class="text-lg sm:text-2xl font-bold text-slate-800 leading-tight">0</p>
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