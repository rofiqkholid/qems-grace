@extends('layouts.app')

@section('title', 'Company KPI')

@section('content')
@include('layouts.sidebar')
@include('components.toast')

@php
    $currentYear = date('Y');
    $years = [];
    for ($i = 5; $i >= 1; $i--) {
        $years[] = $currentYear - $i;
    }
@endphp

<!-- JSON Config Data for JS -->
<div id="kpi-config-data" class="hidden"
     data-pillars="{{ json_encode($kpiList->pluck('pillar', 'id')) }}"
     data-targets="{{ json_encode($kpiList->pluck('target', 'id')) }}"
     data-objectives="{{ json_encode($kpiList->mapWithKeys(function($item) { return [$item->id => $item->no_kpi . ' - ' . $item->objective]; })) }}"
     data-departments="{{ json_encode($departments->mapWithKeys(function($dept) { return [$dept->Key1 => $dept->Key1]; })) }}"
     data-years="{{ json_encode(array_values($years)) }}"
     data-units="{{ json_encode($kpiList->pluck('unit', 'id')) }}"
     data-operators="{{ json_encode($kpiList->pluck('operator', 'id')) }}"
     data-calculation-methods="{{ json_encode($kpiList->pluck('calculation_method', 'id')) }}">
</div>

<!-- Main Content -->
<div class="lg:ml-20 min-h-screen flex flex-col bg-slate-50">
    @include('layouts.header')

    <!-- Page Content -->
    <main class="flex-1 p-6">
        <!-- List View -->
        <div id="listView">
            <!-- Page Title -->
            <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Key Performance Indicators Form</h1>
                    <p class="text-slate-500 mt-1">Real-time overview of organization-wide strategic goals and target achievements.</p>
                </div>
            </div>

            <!-- Main Card -->
            <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
                <!-- Filter Section -->
                <div class="p-6 border-b border-slate-200 bg-slate-50/50">
                    <div class="grid grid-cols-2 md:flex md:flex-row md:flex-wrap md:items-center gap-3 w-full">
                        <!-- Search -->
                        <div class="col-span-1 md:flex-1 md:min-w-[150px]">
                            <div class="relative">
                                <input type="text" id="searchInput" placeholder="Search KPI..."
                                    class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none bg-white">
                                <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            </div>
                        </div>

                        <!-- Pillar Filter -->
                        <div class="col-span-1 md:flex-1 md:min-w-[120px]">
                            <x-searchable-select
                                name="filter_pillar"
                                id="filter_pillar"
                                label="Pilar"
                                :initialOptions="$pillars->map(fn($item) => ['id' => $item, 'name' => $item])->toArray()"
                                updateEvent="set-filter-pillar"
                                changeEvent="filter-pillar-changed"
                                hideLabel="true" />
                        </div>

                        <!-- Objective Filter -->
                        <div class="col-span-1 md:flex-1 md:min-w-[180px]">
                            <x-searchable-select
                                name="filter_objective"
                                id="filter_objective"
                                label="Objective"
                                :initialOptions="$kpiList->map(fn($item) => ['id' => $item->id, 'name' => $item->no_kpi . ' - ' . $item->objective])->toArray()"
                                updateEvent="set-filter-objective"
                                changeEvent="filter-objective-changed"
                                hideLabel="true" />
                        </div>

                        <!-- Period Filter -->
                        <div class="col-span-1 md:flex-1 md:min-w-[110px]">
                            <x-searchable-select
                                name="filter_periode"
                                id="filter_periode"
                                label="Period"
                                :initialOptions="$periods->map(fn($item) => ['id' => $item, 'name' => $item])->toArray()"
                                updateEvent="set-filter-periode"
                                changeEvent="filter-periode-changed"
                                hideLabel="true" />
                        </div>

                        <!-- Department Filter -->
                        <div class="col-span-1 md:flex-1 md:min-w-[150px]">
                            <x-searchable-select
                                name="filter_department"
                                id="filter_department"
                                label="Department"
                                :initialOptions="$departments->map(fn($item) => ['id' => $item->Key1, 'name' => $item->Key1])->toArray()"
                                updateEvent="set-filter-department"
                                changeEvent="filter-department-changed"
                                hideLabel="true" />
                        </div>

                        <!-- Reset Filters -->
                        <div class="col-span-1 md:w-auto md:flex-none">
                            <button type="button" id="resetFilters"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 text-sm font-base transition-colors whitespace-nowrap">
                                <i class="fa-solid fa-rotate-right text-sm"></i>
                                Reset
                            </button>
                        </div>

                        <!-- Create Button -->
                        <div class="col-span-2 md:w-auto md:flex-none">
                            <button type="button" onclick="showCreateForm()"
                                class="w-full inline-flex items-center justify-center gap-2 px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition-colors shadow-sm shadow-blue-200 whitespace-nowrap">
                                <i class="fa-solid fa-plus text-sm"></i>
                                <span>Add New Company KPI</span>
                            </button>
                        </div>
                    </div>
                </div>

@php
    $currentYear = date('Y');
    $years = [];
    for ($i = 5; $i >= 1; $i--) {
        $years[] = $currentYear - $i;
    }
@endphp

                <!-- Table Section -->
                <div class="p-6 overflow-x-auto">
                    <table id="companyKpiTable" class="qms-table w-full" style="width: 100% !important; table-layout: auto !important;">
                        <thead>
                            <tr>
                                <th class="w-[10%]">Department</th>
                                <th class="w-[10%]">Pilar</th>
                                <th class="w-[15%]">Objective</th>
                                <th class="w-[10%]">Target</th>
                                @foreach($years as $yr)
                                    <th class="w-[6%]" style="font-size: 0.7rem !important; font-weight: 600 !important; color: #475569 !important; text-transform: uppercase !important; letter-spacing: 0.05em !important;">{{ $yr }}</th>
                                @endforeach
                                <th class="w-[15%]">Periode</th>
                                <th class="w-[15%] min-w-[150px] text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Create Form View -->
        <div id="createView" class="hidden">
            <!-- Page Title -->
            <div class="mb-6">
                <div class="flex items-center gap-3">
                    <button type="button" onclick="hideCreateForm()"
                        class="inline-flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all">
                        <i class="fa-solid fa-arrow-left text-[11px] sm:text-sm"></i>
                    </button>
                    <div>
                        <h1 class="text-lg sm:text-2xl font-bold text-slate-800">Add Company KPI</h1>
                        <p class="text-slate-500 text-sm">Define and assign new strategic goals for the organization</p>
                    </div>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50">
                    <h2 class="text-lg font-bold text-slate-800">KPI Information</h2>
                    <p class="text-slate-500 text-sm mt-1">Please select the objective, target department, and enter target parameters.</p>
                </div>

                <div class="p-8">
                    <form id="createKpiForm" action="{{ route('kpi.company.store') }}" method="POST">
                        @csrf
                         <div class="grid grid-cols-2 gap-x-8 gap-y-6">
                             <!-- Department -->
                             <div class="col-span-2 lg:col-span-1">
                                 <label class="block text-sm font-medium text-slate-700 mb-1.5 tracking-wider">Department <span class="text-red-500">*</span></label>
                                 <div class="flex gap-2 items-start">
                                     <div class="flex-1">
                                         <x-searchable-select-multi
                                             name="department_code"
                                             id="create_department_code"
                                             label="Department"
                                             required="true"
                                             maxItems="0"
                                             apiUrl="{{ route('kpi.company.departments') }}"
                                             updateEvent="set-create-department"
                                             hideLabel="true" />
                                     </div>
                                     <div class="shrink-0 h-[40px] flex items-center">
                                         <button type="button" id="btn_dept_toggle" onclick="toggleDeptSelection()" class="w-10 h-[40px] flex items-center justify-center text-slate-500 hover:text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors" title="Select All">
                                             <i class="fa-solid fa-file-circle-check text-sm"></i>
                                         </button>
                                     </div>
                                 </div>
                             </div>

                            <!-- Objective -->
                            <div class="col-span-2 lg:col-span-1">
                                <label class="block text-sm font-medium text-slate-700 mb-1.5 tracking-wider">KPI Objective <span class="text-red-500">*</span></label>
                                <x-searchable-select
                                    name="kpi_list_id"
                                    id="create_kpi_list_id"
                                    label="Objective"
                                    required="true"
                                    apiUrl="{{ route('master.kpi_list.options') }}"
                                    updateEvent="set-create-kpi"
                                    changeEvent="create-kpi-changed"
                                    hideLabel="true" />
                            </div>

                            <!-- Target -->
                             <div class="col-span-2 lg:col-span-1">
                                 <label class="block text-sm font-medium text-slate-700 mb-1.5 tracking-wider">Target <span class="text-red-500">*</span></label>
                                 <input type="text" name="target" id="create_target" disabled placeholder="Target from KPI List"
                                     class="w-full px-4 py-2.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-500 text-sm outline-none cursor-not-allowed">
                             </div>

                             <!-- Operator -->
                             <div class="col-span-2 lg:col-span-1">
                                 <label class="block text-sm font-medium text-slate-700 mb-1.5">Operator <span class="text-red-500">*</span></label>
                                 <x-searchable-select
                                     name="operator"
                                     id="create_operator"
                                     label="Operator"
                                     required="true"
                                     disabled="true"
                                     :initialOptions="[
                                         ['id' => '>=', 'name' => '>= (Greater than or equal to)'],
                                         ['id' => '<=', 'name' => '<= (Less than or equal to)'],
                                         ['id' => '=', 'name' => '= (Equal to)'],
                                         ['id' => '>', 'name' => '> (Greater than)'],
                                         ['id' => '<', 'name' => '< (Less than)']
                                     ]"
                                     updateEvent="set-create-operator"
                                     hideLabel="true" />
                             </div>

                              <!-- Unit (Hidden) -->
                              <div class="hidden">
                                  <x-searchable-select
                                      name="unit"
                                      id="create_unit"
                                      label="Unit"
                                      required="false"
                                      disabled="true"
                                      apiUrl="{{ route('master.kpi_unit.options') }}"
                                      updateEvent="set-create-unit"
                                      hideLabel="true" />
                              </div>

                              <!-- Periode -->
                              <div class="col-span-2">
                                  <label class="block text-sm font-medium text-slate-700 mb-1.5 tracking-wider">Periode <span class="text-red-500">*</span></label>
                                  <input type="text"
                                      name="periode"
                                      id="create_periode"
                                      value="{{ date('Y') }}"
                                      readonly
                                      required
                                      class="w-full px-4 py-2.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-500 text-sm outline-none cursor-not-allowed focus:border-slate-200 focus:ring-0" />
                              </div>

                              <!-- Calculation Method -->
                              <div class="col-span-2">
                                  <label class="block text-sm font-medium text-slate-700 mb-1.5 tracking-wider">Calculation Method <span class="text-red-500">*</span></label>
                                  <x-searchable-select
                                      name="calculation_method"
                                      id="create_calculation_method"
                                      label="Calculation Method"
                                      required="true"
                                      disabled="true"
                                      :initialOptions="[
                                          ['id' => 'Direct Actual (Input Actual Data)', 'name' => 'Direct Actual (Input Actual Data)'],
                                          ['id' => 'Custom (Using Formula Components)', 'name' => 'Custom (Using Formula Components)']
                                      ]"
                                      updateEvent="set-create-calculation-method"
                                      hideLabel="true" />
                              </div></div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end gap-3 pt-8 mt-8 border-t border-slate-100">
                            <button type="button" onclick="hideCreateForm()"
                                class="px-6 py-2.5 bg-white text-slate-700 border border-slate-200 rounded-lg hover:bg-slate-50 text-sm font-medium transition-all shadow-sm">
                                Cancel
                            </button>
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition-all shadow-sm shadow-blue-200">
                                <span>Save KPI</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Form View -->
        <div id="editView" class="hidden">
            <!-- Page Title -->
            <div class="mb-6">
                <div class="flex items-center gap-3">
                    <button type="button" onclick="hideEditForm()"
                        class="inline-flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all">
                        <i class="fa-solid fa-arrow-left text-[11px] sm:text-sm"></i>
                    </button>
                    <div>
                        <h1 class="text-lg sm:text-2xl font-bold text-slate-800">Edit Company KPI</h1>
                        <p class="text-slate-500 text-sm">Modify strategic goals and target achievements for the organization</p>
                    </div>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50">
                    <h2 class="text-lg font-bold text-slate-800">KPI Information</h2>
                    <p class="text-slate-500 text-sm mt-1">Please update the objective, target department, and target parameters.</p>
                </div>

                <div class="p-8">
                    <form id="editForm" action="{{ route('kpi.company.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" id="edit_id">
                        <div class="grid grid-cols-2 gap-x-8 gap-y-6">
                            <!-- Department -->
                            <div class="col-span-2 lg:col-span-1">
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Department <span class="text-red-500">*</span></label>
                                <x-searchable-select
                                    name="department_code"
                                    id="edit_department_code"
                                    label="Department"
                                    required="true"
                                    apiUrl="{{ route('kpi.company.departments') }}"
                                    updateEvent="set-edit-department"
                                    hideLabel="true" />
                            </div>

                            <!-- KPI Objective -->
                            <div class="col-span-2 lg:col-span-1">
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">KPI Objective <span class="text-red-500">*</span></label>
                                <x-searchable-select
                                    name="kpi_list_id"
                                    id="edit_kpi_list_id"
                                    label="Objective"
                                    required="true"
                                    apiUrl="{{ route('master.kpi_list.options') }}"
                                    updateEvent="set-edit-kpi"
                                    changeEvent="edit-kpi-changed"
                                    hideLabel="true" />
                            </div>

                            <!-- Target -->
                             <div class="col-span-2 lg:col-span-1">
                                 <label class="block text-sm font-medium text-slate-700 mb-1.5">Target <span class="text-red-500">*</span></label>
                                 <input type="text" name="target" id="edit_target" disabled placeholder="Target from KPI List"
                                     class="w-full px-4 py-2.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-500 text-sm outline-none cursor-not-allowed">
                             </div>

                             <!-- Operator -->
                             <div class="col-span-2 lg:col-span-1">
                                 <label class="block text-sm font-medium text-slate-700 mb-1.5">Operator <span class="text-red-500">*</span></label>
                                 <x-searchable-select
                                     name="operator"
                                     id="edit_operator"
                                     label="Operator"
                                     required="true"
                                     disabled="true"
                                     :initialOptions="[
                                         ['id' => '>=', 'name' => '>= (Greater than or equal to)'],
                                         ['id' => '<=', 'name' => '<= (Less than or equal to)'],
                                         ['id' => '=', 'name' => '= (Equal to)'],
                                         ['id' => '>', 'name' => '> (Greater than)'],
                                         ['id' => '<', 'name' => '< (Less than)']
                                     ]"
                                     updateEvent="set-edit-operator"
                                     hideLabel="true" />
                             </div>

                              <!-- Unit (Hidden) -->
                             <div class="hidden">
                                 <x-searchable-select
                                     name="unit"
                                     id="edit_unit"
                                     label="Unit"
                                     required="false"
                                     disabled="true"
                                     apiUrl="{{ route('master.kpi_unit.options') }}"
                                     updateEvent="set-edit-unit"
                                     hideLabel="true" />
                             </div>

                             <!-- Periode -->
                             <div class="col-span-2">
                                 <label class="block text-sm font-medium text-slate-700 mb-1.5">Periode <span class="text-red-500">*</span></label>
                                 <input type="text"
                                     name="periode"
                                     id="edit_periode"
                                     readonly
                                     required
                                     class="w-full px-4 py-2.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-500 text-sm outline-none cursor-not-allowed focus:border-slate-200 focus:ring-0" />
                             </div>

                             <!-- Calculation Method (Hidden) -->
                             <div class="hidden">
                                 <x-searchable-select
                                     name="calculation_method"
                                     id="edit_calculation_method"
                                     label="Calculation Method"
                                     required="false"
                                     disabled="true"
                                     :initialOptions="[
                                         ['id' => 'Direct Actual (Input Actual Data)', 'name' => 'Direct Actual (Input Actual Data)'],
                                         ['id' => 'Custom (Using Formula Components)', 'name' => 'Custom (Using Formula Components)']
                                     ]"
                                     updateEvent="set-edit-calculation-method"
                                     hideLabel="true" />
                             </div></div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end gap-3 pt-8 mt-8 border-t border-slate-100">
                            <button type="button" onclick="hideEditForm()"
                                class="px-6 py-2.5 bg-white text-slate-700 border border-slate-200 rounded-lg hover:bg-slate-50 text-sm font-medium transition-all shadow-sm">
                                Cancel
                            </button>
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition-all shadow-sm shadow-blue-200">
                                <span>Update</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    @include('layouts.footer')
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-slate-900/50 transition-opacity" onclick="closeDeleteModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl w-full max-w-sm transform transition-all shadow-xl">
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-triangle-exclamation text-2xl text-red-600"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">Confirm Delete</h3>
                <p class="text-slate-500 text-sm">Are you sure you want to delete this specific Company KPI? This action cannot be undone.</p>
            </div>
            <div class="p-6 pt-0 flex gap-3">
                <button type="button" onclick="closeDeleteModal()" class="flex-1 px-4 py-2 bg-slate-100 text-slate-700 font-medium rounded-lg hover:bg-slate-200 transition-colors">Cancel</button>
                <button type="button" onclick="executeDelete()" class="flex-1 px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Sidebar Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/50 z-30 hidden lg:hidden"></div>

@push('scripts')
<script>
    const configEl = document.getElementById('kpi-config-data');
    const kpiPillars = JSON.parse(configEl.getAttribute('data-pillars') || '{}');
    const kpiTargets = JSON.parse(configEl.getAttribute('data-targets') || '{}');
    const kpiObjectives = JSON.parse(configEl.getAttribute('data-objectives') || '{}');

    const kpiUnits = JSON.parse(configEl.getAttribute('data-units') || '{}');
    const kpiOperators = JSON.parse(configEl.getAttribute('data-operators') || '{}');
    const kpiCalcMethods = JSON.parse(configEl.getAttribute('data-calculation-methods') || '{}');

    window.addEventListener('create-kpi-changed', function(e) {
        const kpiId = e.detail.id;
        const targetVal = kpiTargets[kpiId] || '';
        $('#create_target').val(targetVal);

        // Auto-fill Operator, Unit, Calculation Method from KPI List
        const unitVal = kpiUnits[kpiId] || '';
        const operatorVal = kpiOperators[kpiId] || '';
        const calcMethodVal = kpiCalcMethods[kpiId] || '';

        if (unitVal) {
            window.dispatchEvent(new CustomEvent('set-create-unit', { detail: { id: unitVal, name: unitVal } }));
        } else {
            window.dispatchEvent(new CustomEvent('set-create-unit', { detail: { id: '', name: '' } }));
        }
        if (operatorVal) {
            window.dispatchEvent(new CustomEvent('set-create-operator', { detail: { id: operatorVal, name: (operatorLabels[operatorVal] || operatorVal) } }));
        } else {
            window.dispatchEvent(new CustomEvent('set-create-operator', { detail: { id: '', name: '' } }));
        }
        if (calcMethodVal) {
            window.dispatchEvent(new CustomEvent('set-create-calculation-method', { detail: { id: calcMethodVal, name: calcMethodVal } }));
        } else {
            window.dispatchEvent(new CustomEvent('set-create-calculation-method', { detail: { id: '', name: '' } }));
        }
    });

    window.addEventListener('edit-kpi-changed', function(e) {
        const kpiId = e.detail.id;
        const targetVal = kpiTargets[kpiId] || '';
        $('#edit_target').val(targetVal);

        // Auto-fill Operator, Unit, Calculation Method from KPI List
        const unitVal = kpiUnits[kpiId] || '';
        const operatorVal = kpiOperators[kpiId] || '';
        const calcMethodVal = kpiCalcMethods[kpiId] || '';

        if (unitVal) {
            window.dispatchEvent(new CustomEvent('set-edit-unit', { detail: { id: unitVal, name: unitVal } }));
        } else {
            window.dispatchEvent(new CustomEvent('set-edit-unit', { detail: { id: '', name: '' } }));
        }
        if (operatorVal) {
            window.dispatchEvent(new CustomEvent('set-edit-operator', { detail: { id: operatorVal, name: (operatorLabels[operatorVal] || operatorVal) } }));
        } else {
            window.dispatchEvent(new CustomEvent('set-edit-operator', { detail: { id: '', name: '' } }));
        }
        if (calcMethodVal) {
            window.dispatchEvent(new CustomEvent('set-edit-calculation-method', { detail: { id: calcMethodVal, name: calcMethodVal } }));
        } else {
            window.dispatchEvent(new CustomEvent('set-edit-calculation-method', { detail: { id: '', name: '' } }));
        }
    });

    const departmentNames = JSON.parse(configEl.getAttribute('data-departments') || '{}');

    const operatorLabels = {
        '>=': '>= (Greater than or equal to)',
        '<=': '<= (Less than or equal to)',
        '=': '= (Equal to)',
        '>': '> (Greater than)',
        '<': '< (Less than)'
    };

    $(document).ready(function() {
        var table = $('#companyKpiTable').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: "{{ route('kpi.company.table') }}",
                type: 'POST',
                data: function(d) {
                    d._token = "{{ csrf_token() }}";
                    d.search.value = $('#searchInput').val();
                    d.pillar = $('#filter_pillar').val();
                    d.objective = $('#filter_objective').val();
                    d.periode = $('#filter_periode').val();
                    d.department = $('#filter_department').val();
                }
            },
            columns: (function() {
                var years = JSON.parse(configEl.getAttribute('data-years') || '[]');
                var cols = [
                    { data: 'department_code', name: 'department_code', className: 'text-slate-700 py-4' },
                    { data: 'pillar', name: 'pillar', className: 'text-slate-700 py-4' },
                    { data: 'objective', name: 'objective', className: 'text-slate-700 py-4' },
                    { data: 'target', name: 'target', className: 'text-slate-700 py-4' },
                ];
                years.forEach(function(yr) {
                    cols.push({ data: 'year_' + yr, name: 'year_' + yr, orderable: false, searchable: false, className: 'text-slate-700 py-4' });
                });
                cols.push({ data: 'periode', name: 'periode', className: 'text-slate-700 py-4' });
                cols.push({ data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-left py-4 min-w-[150px]' });
                return cols;
            }()),
            language: {
                emptyTable: '<div class="flex flex-col items-center justify-center py-8 text-slate-500"><i class="fa-regular fa-folder-open text-4xl mb-3 text-slate-300"></i><p>No data available</p></div>',
            },
            dom: 'r<"overflow-x-auto"t><"flex flex-col sm:flex-row items-center justify-between p-4 border-t border-slate-200 gap-4"ip>',
            pagingType: "simple_numbers"
        });

        // Search trigger
        $('#searchInput').on('keyup', function() {
            table.search(this.value).draw();
        });

        // Filters trigger
        $('#filter_pillar, #filter_objective, #filter_periode, #filter_department').on('change', function() {
            table.draw();
        });

        // Reset filters trigger
        $('#resetFilters').on('click', function() {
            window.dispatchEvent(new CustomEvent('set-filter-pillar', { detail: { id: "", name: "" } }));
            window.dispatchEvent(new CustomEvent('set-filter-objective', { detail: { id: "", name: "" } }));
            window.dispatchEvent(new CustomEvent('set-filter-periode', { detail: { id: "", name: "" } }));
            window.dispatchEvent(new CustomEvent('set-filter-department', { detail: { id: "", name: "" } }));
            $('#searchInput').val('');
            table.search('').draw();
        });

        $('#create_department_code').on('change', function() {
            const btn = $('#btn_dept_toggle');
            if (btn.prop('disabled')) return; // Skip updating HTML if currently loading/disabled

            const val = $(this).val();
            if (val && val.trim() !== '') {
                btn.html('<i class="fa-solid fa-trash-can text-sm"></i>')
                   .attr('title', 'Clear All')
                   .removeClass('text-slate-500 bg-slate-100 hover:bg-slate-200 hover:text-slate-600')
                   .addClass('text-red-500 bg-red-50 hover:bg-red-100 hover:text-red-600');
            } else {
                btn.html('<i class="fa-solid fa-file-circle-check text-sm"></i>')
                   .attr('title', 'Select All')
                   .removeClass('text-red-500 bg-red-50 hover:bg-red-100 hover:text-red-600')
                   .addClass('text-slate-500 bg-slate-100 hover:bg-slate-200 hover:text-slate-600');
            }
        });

        // Remove required attribute from searchable select hidden inputs to bypass html5 check
        $('#create_kpi_list_id').removeAttr('required');
        $('#edit_kpi_list_id').removeAttr('required');
        $('#create_department_code').removeAttr('required');
        $('#edit_department_code').removeAttr('required');
        $('#create_operator').removeAttr('required');
        $('#edit_operator').removeAttr('required');
        $('#create_periode').removeAttr('required');
        $('#edit_periode').removeAttr('required');
        $('#create_unit').removeAttr('required');
        $('#edit_unit').removeAttr('required');
        $('#create_calculation_method').removeAttr('required');
        $('#edit_calculation_method').removeAttr('required');
        $('#filter_pillar').removeAttr('required');
        $('#filter_objective').removeAttr('required');
        $('#filter_periode').removeAttr('required');
        $('#filter_department').removeAttr('required');

        // Auto-resize calculation method textareas
        function autoResizeTextarea(el) {
            if (!el) return;
            el.style.height = 'auto';
            let scrollHeight = el.scrollHeight;
            if (scrollHeight > 160) {
                el.style.height = '160px';
                el.style.overflowY = 'auto';
            } else {
                el.style.height = scrollHeight + 'px';
                el.style.overflowY = 'hidden';
            }
        }

        window.autoResizeTextarea = autoResizeTextarea;

        // Create form AJAX submit
        $('#createKpiForm').on('submit', function(e) {
            e.preventDefault();
            const form = this;
            const submitBtn = $(form).find('button[type="submit"]');
            
            submitBtn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-2"></i> Saving...');
            
            $.ajax({
                url: form.action,
                type: 'POST',
                data: $(form).serialize(),
                success: function(response) {
                    hideCreateForm();
                    showToast('Company KPI added successfully.', 'success');
                    table.ajax.reload();
                    form.reset();
                    // Reset searchable selects visually
                    window.dispatchEvent(new CustomEvent('set-create-department', { detail: { id: '', name: '' } }));
                    window.dispatchEvent(new CustomEvent('set-create-kpi', { detail: { id: '', name: '' } }));
                    window.dispatchEvent(new CustomEvent('set-create-operator', { detail: { id: '', name: '' } }));
                    $('#create_periode').val('{{ date("Y") }}');
                    window.dispatchEvent(new CustomEvent('set-create-unit', { detail: { id: '', name: '' } }));
                    window.dispatchEvent(new CustomEvent('set-create-calculation-method', { detail: { id: '', name: '' } }));
                },
                error: function(xhr) {
                    showToast(xhr.responseJSON?.message || 'Failed to add Company KPI.', 'error');
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html('<span>Save KPI</span>');
                }
            });
        });

        // Edit form AJAX submit
        $('#editForm').on('submit', function(e) {
            e.preventDefault();
            const form = this;
            const submitBtn = $(form).find('button[type="submit"]');
            
            submitBtn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-2"></i> Updating...');
            
            $.ajax({
                url: form.action,
                type: 'POST',
                data: $(form).serialize(),
                success: function(response) {
                    hideEditForm();
                    showToast('Company KPI updated successfully.', 'success');
                    table.ajax.reload(null, false);
                },
                error: function(xhr) {
                    showToast(xhr.responseJSON?.message || 'Failed to update Company KPI.', 'error');
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html('Update');
                }
            });
        });
    });

    function showCreateForm() {
        $('#page-loader').removeClass('hidden');
        setTimeout(function() {
            $('#listView').addClass('hidden');
            $('#createView').removeClass('hidden');
            
            // Reset selects
            window.dispatchEvent(new CustomEvent('set-create-department', { detail: { id: "", name: "" } }));
            window.dispatchEvent(new CustomEvent('set-create-kpi', { detail: { id: "", name: "" } }));
            window.dispatchEvent(new CustomEvent('set-create-operator', { detail: { id: "", name: "" } }));
            $('#create_periode').val('{{ date("Y") }}');
            window.dispatchEvent(new CustomEvent('set-create-unit', { detail: { id: "", name: "" } }));
            window.dispatchEvent(new CustomEvent('set-create-calculation-method', { detail: { id: "", name: "" } }));
            
            // Trigger change manual agar visual tombol update ke Select All abu-abu
            setTimeout(() => {
                $('#create_department_code').trigger('change');
            }, 10);
            
            setTimeout(() => {
                if (window.autoResizeTextarea) {
                    window.autoResizeTextarea(document.getElementById('create_calculation_method'));
                }
            }, 50);

            $('#page-loader').addClass('hidden');
        }, 350);
    }

    function hideCreateForm() {
        $('#page-loader').removeClass('hidden');
        setTimeout(function() {
            $('#createView').addClass('hidden');
            $('#listView').removeClass('hidden');
            $('#page-loader').addClass('hidden');
        }, 350);
    }

    function handleEdit(btn) {
        const id = btn.getAttribute('data-id');
        const kpi_list_id = btn.getAttribute('data-kpi_list_id');
        const department_code = btn.getAttribute('data-department_code');
        const target = btn.getAttribute('data-target');
        const periode = btn.getAttribute('data-periode');
        
        $('#edit_id').val(id);
        $('#edit_target').val(target);

        // KPI Objective selection
        let objectiveName = kpiObjectives[kpi_list_id] || "";
        window.dispatchEvent(new CustomEvent('set-edit-kpi', {
            detail: { id: kpi_list_id, name: objectiveName }
        }));

        // Trigger edit-kpi-changed to auto-fill Operator, Unit, and Calculation Method
        window.dispatchEvent(new CustomEvent('edit-kpi-changed', {
            detail: { id: kpi_list_id }
        }));

        // Department selection
        const deptLabel = departmentNames[department_code] || department_code;
        window.dispatchEvent(new CustomEvent('set-edit-department', {
            detail: { id: department_code, name: deptLabel }
        }));

        // Periode selection
        $('#edit_periode').val(periode);
        
        showEditForm();
    }

    function showEditForm() {
        $('#page-loader').removeClass('hidden');
        setTimeout(function() {
            $('#listView').addClass('hidden');
            $('#editView').removeClass('hidden');
            $('#page-loader').addClass('hidden');
        }, 350);
    }

    function hideEditForm() {
        $('#page-loader').removeClass('hidden');
        setTimeout(function() {
            $('#editView').addClass('hidden');
            $('#listView').removeClass('hidden');
            $('#page-loader').addClass('hidden');
        }, 350);
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

        $('#icon_delete_' + no).addClass('hidden');
        $('#loader_delete_' + no).removeClass('hidden');

        closeDeleteModal();

        $.ajax({
            url: "{{ route('kpi.company.delete') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: id
            },
            success: function(response) {
                $('#icon_delete_' + no).removeClass('hidden');
                $('#loader_delete_' + no).addClass('hidden');

                if (response.success) {
                    showToast('Company KPI deleted successfully', 'success');
                    $('#companyKpiTable').DataTable().ajax.reload();
                } else {
                    showToast('Failed to delete Company KPI', 'error');
                }
            },
            error: function() {
                $('#icon_delete_' + no).removeClass('hidden');
                $('#loader_delete_' + no).addClass('hidden');
                showToast('An error occurred', 'error');
            }
        });
    }

    function handleDetail(id) {
        window.location.href = "{{ route('kpi.company.detail', '') }}/" + id;
    }

    function selectAllDepartments() {
        const configEl = document.getElementById('kpi-config-data');
        const departmentsObj = JSON.parse(configEl.getAttribute('data-departments') || '{}');
        const deptKeys = Object.keys(departmentsObj);
        if (deptKeys.length > 0) {
            const ids = deptKeys.join(',');
            const names = deptKeys.join(',');
            window.dispatchEvent(new CustomEvent('set-create-department', {
                detail: { id: ids, name: names }
            }));
        }
    }

    function clearAllDepartments() {
        window.dispatchEvent(new CustomEvent('set-create-department', {
            detail: { id: "", name: "" }
        }));
    }

    function toggleDeptSelection() {
        const btn = $('#btn_dept_toggle');
        const originalHtml = btn.html();
        
        // Disable button and show spinner
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin text-sm"></i>');
        
        setTimeout(() => {
            const currentVal = $('#create_department_code').val();
            if (currentVal && currentVal.trim() !== '') {
                clearAllDepartments();
            } else {
                selectAllDepartments();
            }
            btn.prop('disabled', false);
            // Manually trigger change to update button icon after loading finishes
            $('#create_department_code').trigger('change');
        }, 1000);
    }
</script>
@endpush
@endsection
