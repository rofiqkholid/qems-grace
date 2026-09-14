@php
    $hideCentralToast = true;
@endphp
@extends('layouts.app')

@section('title', 'KPI Master List')

@section('content')
@include('layouts.sidebar')
@include('components.toast')

<div class="lg:ml-20 min-h-screen flex flex-col bg-slate-50">
    @include('layouts.header')

    <!-- Page Content -->
    <main class="flex-1 p-6">
        <!-- Page Title -->
        <div class="mb-6">
            <h1 class="text-lg sm:text-2xl font-bold text-slate-800">KPI Master List</h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">Manage definitions, targets, objectives, and categories of KPIs.</p>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <!-- Filter Section -->
            <div class="p-4 sm:p-6 border-b border-slate-200 bg-slate-50/50">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:flex lg:flex-row lg:items-center gap-3 w-full">
                    <!-- Search -->
                    <div class="col-span-1 sm:col-span-2 lg:flex-1 lg:min-w-[200px]">
                        <div class="relative">
                            <input type="text" id="searchInput" placeholder="Search No. KPI, Objective, or Definition..."
                                class="w-full pl-10 pr-4 py-[9px] border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm outline-none bg-white transition-all font-normal text-slate-700">
                            <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        </div>
                    </div>

                    <!-- Pillar Filter -->
                    <div class="col-span-1 lg:w-44">
                        <x-searchable-select
                            name="filter_pillar"
                            id="filter_pillar"
                            label="Pillar"
                            :initialOptions="($pillars ?? collect([]))->map(fn($item) => ['id' => $item, 'name' => $item])->toArray()"
                            updateEvent="set-filter-pillar"
                            changeEvent="filter-pillar-changed"
                            hideLabel="true" />
                    </div>

                    <!-- Category Filter -->
                    <div class="col-span-1 lg:w-44">
                        <x-searchable-select
                            name="filter_category"
                            id="filter_category"
                            label="Category"
                            :initialOptions="($categories ?? collect([]))->map(fn($item) => ['id' => $item, 'name' => $item])->toArray()"
                            updateEvent="set-filter-category"
                            changeEvent="filter-category-changed"
                            hideLabel="true" />
                    </div>

                    <!-- Calculation Method Filter -->
                    <div class="col-span-1 sm:col-span-2 lg:w-56">
                        <x-searchable-select
                            name="filter_calculation_method"
                            id="filter_calculation_method"
                            label="Calculation Method"
                            :initialOptions="$calculationMethods->map(fn($item) => ['id' => $item, 'name' => $item])->toArray()"
                            updateEvent="set-filter-calculation-method"
                            changeEvent="filter-calculation-method-changed"
                            hideLabel="true" />
                    </div>

                    <!-- Reset Filters -->
                    <div class="col-span-1 lg:w-auto">
                        <button type="button" id="resetFilters"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-[9px] bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 text-sm font-medium transition-colors whitespace-nowrap">
                            <i class="fa-solid fa-rotate-right text-sm"></i>
                            <span>Reset</span>
                        </button>
                    </div>

                    <!-- Add Button -->
                    <div class="col-span-1 sm:col-span-2 lg:w-auto">
                        <button onclick="openCreateModal()"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-[9px] bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors shadow-sm shadow-blue-200 whitespace-nowrap">
                            <i class="fa-solid fa-plus text-xs"></i>
                            <span>Add KPI</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="p-6">
                <table id="kpiTable" class="qms-table w-full min-w-[900px]">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="w-[5%] text-center">No</th>
                            <th class="w-[10%]">No. KPI</th>
                            <th class="w-[20%]">KPI Name</th>
                            <th class="w-[30%]">Definition</th>
                            <th class="w-[10%]">Pillar</th>
                            <th class="w-[10%]">Category</th>
                            <th class="w-[10%]">Target</th>
                            <th class="w-[10%]">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                    </tbody>
                </table>
            </div>
            
            <!-- Data Count Component -->
            <x-data-table tableId="kpiTable" />
        </div>
    </main>
</div>

<!-- Create Modal -->
<div id="createModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-slate-900/50 transition-opacity" onclick="closeCreateModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl w-full h-[92vh] flex flex-col transform transition-all overflow-hidden shadow-2xl" style="max-width: 90%;">
            <!-- Modal Header (Sticky) -->
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-white shrink-0">
                <h3 class="text-lg font-bold text-slate-800">Add KPI</h3>
                <button onclick="closeCreateModal()" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <!-- Modal Body (Scrollable with slate bg) -->
            <form id="createForm" action="{{ route('master.kpi_list.store') }}" method="POST" class="flex flex-col flex-1 min-h-0 bg-slate-100">
                @csrf
                <div class="p-6 space-y-4 overflow-y-auto flex-1 min-h-0">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">No. KPI <span class="text-red-500">*</span></label>
                            <input type="text" name="no_kpi" required class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all uppercase text-sm" placeholder="Enter KPI number">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Parent Objective</label>
                            <x-searchable-select
                                name="parent_objective_id"
                                id="create_parent_objective_id"
                                label="Parent Objective"
                                apiUrl="{{ route('master.kpi_list.options') }}"
                                :initialOptions="$kpiList->map(fn($item) => ['id' => $item->id, 'name' => $item->no_kpi . ' - ' . $item->objective])->toArray()"
                                valueField="id"
                                updateEvent="set-create-parent-objective"
                                hideLabel="true" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">KPI Name <span class="text-red-500">*</span></label>
                            <input type="text" name="objective" required class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-sm" placeholder="Enter objective">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Category <span class="text-red-500">*</span></label>
                            <x-searchable-select
                                name="category"
                                id="create_category_kpilist"
                                label="Category"
                                required="true"
                                :initialOptions="[
                                    ['id' => 'Company KPI', 'name' => 'Company KPI'],
                                    ['id' => 'KPI Department', 'name' => 'KPI Department'],
                                    ['id' => 'Activity Plan', 'name' => 'Activity Plan']
                                ]"
                                updateEvent="set-create-category-kpilist"
                                hideLabel="true" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Definition <span class="text-red-500">*</span></label>
                        <textarea name="definition" id="create_definition" rows="3" required class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all resize-none overflow-y-auto max-h-40 text-sm" placeholder="Enter definition"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Pillar <span class="text-red-500">*</span></label>
                            <x-searchable-select
                                name="pillar"
                                id="create_pillar_kpilist"
                                label="Pillar"
                                required="true"
                                :initialOptions="[
                                    ['id' => 'Safety', 'name' => 'Safety'],
                                    ['id' => 'Quality', 'name' => 'Quality'],
                                    ['id' => 'People', 'name' => 'People'],
                                    ['id' => 'Cost', 'name' => 'Cost'],
                                    ['id' => 'Responsiveness', 'name' => 'Responsiveness'],
                                    ['id' => 'Delivery', 'name' => 'Delivery'],
                                    ['id' => 'Environment & Energy', 'name' => 'Environment & Energy']
                                ]"
                                updateEvent="set-create-pillar-kpilist"
                                hideLabel="true" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Arrow Target <span class="text-red-500">*</span></label>
                            <x-searchable-select
                                name="arrow_target"
                                id="create_arrow_target_kpilist"
                                label="Arrow Target"
                                required="true"
                                :initialOptions="[
                                    ['id' => 'Up', 'name' => 'Up'],
                                    ['id' => 'Down', 'name' => 'Down']
                                ]"
                                updateEvent="set-create-arrow-target-kpilist"
                                hideLabel="true" />
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Target <span class="text-red-500">*</span></label>
                            <input type="text" name="target" required class="w-full px-4 py-[9px] border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-sm" placeholder="Enter target">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Unit <span class="text-red-500">*</span></label>
                            <x-searchable-select
                                name="unit"
                                id="create_unit_kpilist"
                                label="Unit"
                                required="true"
                                apiUrl="{{ route('master.kpi_unit.options') }}"
                                updateEvent="set-create-unit-kpilist"
                                hideLabel="true" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Result <span class="text-red-500">*</span></label>
                            <x-searchable-select
                                name="result"
                                id="create_result_kpilist"
                                label="Result"
                                required="true"
                                :initialOptions="[
                                    ['id' => 'Total', 'name' => 'Total'],
                                    ['id' => 'Average', 'name' => 'Average']
                                ]"
                                updateEvent="set-create-result-kpilist"
                                hideLabel="true" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Operator <span class="text-red-500">*</span></label>
                            <x-searchable-select
                                name="operator"
                                id="create_operator_kpilist"
                                label="Operator"
                                required="true"
                                :initialOptions="[
                                    ['id' => '>=', 'name' => '>= (Greater than or equal to)'],
                                    ['id' => '<=', 'name' => '<= (Less than or equal to)'],
                                    ['id' => '=', 'name' => '= (Equal to)'],
                                    ['id' => '>', 'name' => '> (Greater than)'],
                                    ['id' => '<', 'name' => '< (Less than)']
                                ]"
                                updateEvent="set-create-operator-kpilist"
                                hideLabel="true" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Calculation Method <span class="text-red-500">*</span></label>
                            <x-searchable-select
                                name="calculation_method"
                                id="create_calculation_method_kpilist"
                                label="Calculation Method"
                                required="true"
                                :initialOptions="[
                                    ['id' => 'Direct Actual (Input Actual Data)', 'name' => 'Direct Actual (Input Actual Data)'],
                                    ['id' => 'Custom (Using Formula Components)', 'name' => 'Custom (Using Formula Components)']
                                ]"
                                updateEvent="set-create-calculation-method-kpilist"
                                hideLabel="true" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Achievement History (Last 5 Years)</label>
                        <div id="create_history_container" class="grid grid-cols-5 gap-3">
                            @php
                                $currentYear = date('Y');
                            @endphp
                            @for ($i = 5; $i >= 1; $i--)
                                @php $yr = $currentYear - $i; @endphp
                                <div class="border border-slate-200 rounded-lg p-3 bg-white space-y-2.5">
                                    <div class="text-sm font-bold text-slate-700 text-center">{{ $yr }}</div>
                                    <div class="grid grid-cols-[60%_40%] gap-2 items-end">
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-500 mb-1">Achievement</label>
                                            <input type="text" id="create_ach_{{ $yr }}" name="history[{{ $yr }}][achievement]" class="w-full px-2 py-[9px] border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-sm" placeholder="Achievement" oninput="onHistoryAchievementInput(this, '{{ $yr }}', 'create')">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-500 mb-1">Unit</label>
                                            <x-searchable-select
                                                name="history[{{ $yr }}][unit]"
                                                id="create_history_unit_{{ $yr }}"
                                                label="Unit"
                                                apiUrl="{{ route('master.kpi_unit.options') }}"
                                                updateEvent="set-create-history-unit-{{ $yr }}"
                                                position="up"
                                                hideLabel="true" />
                                        </div>
                                    </div>
                                    <div class="space-y-1.5 mt-2.5">
                                        <label class="block text-xs font-semibold text-slate-600">Status Achievement</label>
                                        <div class="flex items-center gap-1 bg-slate-100 rounded-lg p-1">
                                            <label class="flex-1 cursor-pointer">
                                                <input type="radio" name="history[{{ $yr }}][status_achievement]" value="Achieved" class="peer sr-only">
                                                <div class="py-2 rounded-md flex items-center justify-center text-sm font-bold text-slate-400 hover:bg-white/50 hover:text-slate-600 transition-all peer-checked:bg-green-100 peer-checked:text-green-700 peer-checked:shadow-sm">
                                                    <span>Achieve</span>
                                                </div>
                                            </label>
                                            <label class="flex-1 cursor-pointer">
                                                <input type="radio" name="history[{{ $yr }}][status_achievement]" value="Not Achieved" class="peer sr-only">
                                                <div class="py-2 rounded-md flex items-center justify-center text-sm font-bold text-slate-400 hover:bg-white/50 hover:text-slate-600 transition-all peer-checked:bg-red-100 peer-checked:text-red-700 peer-checked:shadow-sm">
                                                    <span>Not Achieve</span>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
                <!-- Modal Footer (Sticky) -->
                <div class="p-6 border-t border-slate-100 bg-white flex justify-end gap-3 rounded-b-xl shrink-0">
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
        <div class="bg-white rounded-xl w-full h-[92vh] flex flex-col transform transition-all overflow-hidden shadow-2xl" style="max-width: 90%;">
            <!-- Modal Header (Sticky) -->
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-white shrink-0">
                <h3 class="text-lg font-bold text-slate-800">Edit KPI</h3>
                <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <!-- Modal Body (Scrollable with slate bg) -->
            <form id="editForm" action="{{ route('master.kpi_list.update') }}" method="POST" class="flex flex-col flex-1 min-h-0 bg-slate-100">
                @csrf
                <input type="hidden" name="id" id="edit_id">
                <div class="p-6 space-y-4 overflow-y-auto flex-1 min-h-0">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">No. KPI <span class="text-red-500">*</span></label>
                            <input type="text" name="no_kpi" id="edit_no_kpi" required class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all uppercase text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Parent Objective</label>
                            <x-searchable-select
                                name="parent_objective_id"
                                id="edit_parent_objective_id"
                                label="Parent Objective"
                                apiUrl="{{ route('master.kpi_list.options') }}"
                                :initialOptions="$kpiList->map(fn($item) => ['id' => $item->id, 'name' => $item->no_kpi . ' - ' . $item->objective])->toArray()"
                                valueField="id"
                                updateEvent="set-edit-parent-objective"
                                hideLabel="true" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Objective <span class="text-red-500">*</span></label>
                            <input type="text" name="objective" id="edit_objective" required class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Category <span class="text-red-500">*</span></label>
                            <x-searchable-select
                                name="category"
                                id="edit_category_kpilist"
                                label="Category"
                                required="true"
                                :initialOptions="[
                                    ['id' => 'Company KPI', 'name' => 'Company KPI'],
                                    ['id' => 'KPI Department', 'name' => 'KPI Department'],
                                    ['id' => 'Activity Plan', 'name' => 'Activity Plan']
                                ]"
                                updateEvent="set-edit-category-kpilist"
                                hideLabel="true" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Definition <span class="text-red-500">*</span></label>
                        <textarea name="definition" id="edit_definition" rows="3" required class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all resize-none overflow-y-auto max-h-40 text-sm"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Pillar <span class="text-red-500">*</span></label>
                            <x-searchable-select
                                name="pillar"
                                id="edit_pillar_kpilist"
                                label="Pillar"
                                required="true"
                                :initialOptions="[
                                    ['id' => 'Safety', 'name' => 'Safety'],
                                    ['id' => 'Quality', 'name' => 'Quality'],
                                    ['id' => 'People', 'name' => 'People'],
                                    ['id' => 'Cost', 'name' => 'Cost'],
                                    ['id' => 'Responsiveness', 'name' => 'Responsiveness'],
                                    ['id' => 'Delivery', 'name' => 'Delivery'],
                                    ['id' => 'Environment & Energy', 'name' => 'Environment & Energy']
                                ]"
                                updateEvent="set-edit-pillar-kpilist"
                                hideLabel="true" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Arrow Target <span class="text-red-500">*</span></label>
                            <x-searchable-select
                                name="arrow_target"
                                id="edit_arrow_target_kpilist"
                                label="Arrow Target"
                                required="true"
                                :initialOptions="[
                                    ['id' => 'Up', 'name' => 'Up'],
                                    ['id' => 'Down', 'name' => 'Down']
                                ]"
                                updateEvent="set-edit-arrow-target-kpilist"
                                hideLabel="true" />
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Target <span class="text-red-500">*</span></label>
                            <input type="text" name="target" id="edit_target" required class="w-full px-4 py-[9px] border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Unit <span class="text-red-500">*</span></label>
                            <x-searchable-select
                                name="unit"
                                id="edit_unit_kpilist"
                                label="Unit"
                                required="true"
                                apiUrl="{{ route('master.kpi_unit.options') }}"
                                updateEvent="set-edit-unit-kpilist"
                                hideLabel="true" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Result <span class="text-red-500">*</span></label>
                            <x-searchable-select
                                name="result"
                                id="edit_result_kpilist"
                                label="Result"
                                required="true"
                                :initialOptions="[
                                    ['id' => 'Total', 'name' => 'Total'],
                                    ['id' => 'Average', 'name' => 'Average']
                                ]"
                                updateEvent="set-edit-result-kpilist"
                                hideLabel="true" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Operator <span class="text-red-500">*</span></label>
                            <x-searchable-select
                                name="operator"
                                id="edit_operator_kpilist"
                                label="Operator"
                                required="true"
                                :initialOptions="[
                                    ['id' => '>=', 'name' => '>= (Greater than or equal to)'],
                                    ['id' => '<=', 'name' => '<= (Less than or equal to)'],
                                    ['id' => '=', 'name' => '= (Equal to)'],
                                    ['id' => '>', 'name' => '> (Greater than)'],
                                    ['id' => '<', 'name' => '< (Less than)']
                                ]"
                                updateEvent="set-edit-operator-kpilist"
                                hideLabel="true" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Calculation Method <span class="text-red-500">*</span></label>
                            <x-searchable-select
                                name="calculation_method"
                                id="edit_calculation_method_kpilist"
                                label="Calculation Method"
                                required="true"
                                :initialOptions="[
                                    ['id' => 'Direct Actual (Input Actual Data)', 'name' => 'Direct Actual (Input Actual Data)'],
                                    ['id' => 'Custom (Using Formula Components)', 'name' => 'Custom (Using Formula Components)']
                                ]"
                                updateEvent="set-edit-calculation-method-kpilist"
                                hideLabel="true" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Achievement History (Last 5 Years)</label>
                        <div id="edit_history_container" class="grid grid-cols-5 gap-3">
                            @php
                                $currentYear = date('Y');
                            @endphp
                            @for ($i = 5; $i >= 1; $i--)
                                @php $yr = $currentYear - $i; @endphp
                                <div class="border border-slate-200 rounded-lg p-3 bg-white space-y-2.5">
                                    <div class="text-sm font-bold text-slate-700 text-center">{{ $yr }}</div>
                                    <div class="grid grid-cols-[60%_40%] gap-2 items-end">
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-500 mb-1">Achievement</label>
                                            <input type="text" id="edit_ach_{{ $yr }}" name="history[{{ $yr }}][achievement]" class="w-full px-2 py-[9px] border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-sm" placeholder="Achievement">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-500 mb-1">Unit</label>
                                            <x-searchable-select
                                                name="history[{{ $yr }}][unit]"
                                                id="edit_history_unit_{{ $yr }}"
                                                label="Unit"
                                                apiUrl="{{ route('master.kpi_unit.options') }}"
                                                updateEvent="set-edit-history-unit-{{ $yr }}"
                                                position="up"
                                                hideLabel="true" />
                                        </div>
                                    </div>
                                    <div class="space-y-1.5 mt-2.5">
                                        <label class="block text-xs font-semibold text-slate-600">Status Achievement</label>
                                        <div class="flex items-center gap-1 bg-slate-100 rounded-lg p-1">
                                            <label class="flex-1 cursor-pointer">
                                                <input type="radio" id="edit_status_achieved_{{ $yr }}" name="history[{{ $yr }}][status_achievement]" value="Achieved" class="peer sr-only">
                                                <div class="py-2 rounded-md flex items-center justify-center text-sm font-bold text-slate-400 hover:bg-white/50 hover:text-slate-600 transition-all peer-checked:bg-green-100 peer-checked:text-green-700 peer-checked:shadow-sm">
                                                    <span>Achieve</span>
                                                </div>
                                            </label>
                                            <label class="flex-1 cursor-pointer">
                                                <input type="radio" id="edit_status_not_achieved_{{ $yr }}" name="history[{{ $yr }}][status_achievement]" value="Not Achieved" class="peer sr-only">
                                                <div class="py-2 rounded-md flex items-center justify-center text-sm font-bold text-slate-400 hover:bg-white/50 hover:text-slate-600 transition-all peer-checked:bg-red-100 peer-checked:text-red-700 peer-checked:shadow-sm">
                                                    <span>Not Achieve</span>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
                <!-- Modal Footer (Sticky) -->
                <div class="p-6 border-t border-slate-100 bg-white flex justify-end gap-3 rounded-b-xl shrink-0">
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
                <p class="text-slate-500 text-sm">Are you sure you want to delete this specific KPI? This action cannot be undone.</p>
            </div>
            <div class="p-6 pt-0 flex gap-3">
                <button type="button" onclick="closeDeleteModal()" class="flex-1 px-4 py-2 bg-slate-100 text-slate-700 font-medium rounded-lg hover:bg-slate-200 transition-colors">Cancel</button>
                <button type="button" onclick="executeDelete()" class="flex-1 px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Formula Modal -->
<div id="formulaModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-slate-900/50 transition-opacity" onclick="closeFormulaModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl w-full h-[92vh] flex flex-col transform transition-all overflow-hidden shadow-2xl" style="max-width: 90%;">
            <!-- Modal Header -->
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-white shrink-0">
                <h3 class="text-lg font-bold text-slate-800" id="formulaModalTitle">Manage Formula</h3>
                <button onclick="closeFormulaModal()" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <!-- Modal Body -->
            <form id="formulaForm" action="{{ route('master.kpi_list.formula.save') }}" method="POST" class="flex flex-col flex-1 min-h-0 bg-slate-100"
                x-data="{
                    vals: ['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''],
                    isLocked: false,
                    calcOperator: '',
                    appendToken(index) {
                        this.calcOperator += ' C' + index + ' ';
                    },
                    appendOperator(op) {
                        let cur = this.calcOperator.trim();
                        if (!cur) {
                            this.calcOperator += ' ' + op + ' ';
                            return;
                        }
                        if (op === '*' || op === '/') {
                            // If the current expression has + or - and isn't already fully enclosed in (), wrap the entire expression in ()
                            if ((cur.includes('+') || cur.includes('-')) && !(cur.startsWith('(') && cur.endsWith(')'))) {
                                this.calcOperator = '(' + cur + ') ' + op + ' ';
                                return;
                            }
                        }
                        this.calcOperator += ' ' + op + ' ';
                    }
                }">
                @csrf
                <input type="hidden" name="kpi_list_id" id="formula_kpi_list_id">

                <div class="p-6 overflow-y-auto flex-1 min-h-0">
                    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-6">
                        <!-- 20 Varchar Input Fields -->
                        <div>
                            <label class="block text-base font-bold text-slate-800 mb-2.5">Value Inputs (20 Columns)</label>
                            <div class="grid grid-cols-5 gap-2.5 transition-all">
                                @for($i = 1; $i <= 20; $i++)
                                    <div class="relative">
                                        <input type="text" name="comp_{{ $i }}" x-model="vals[{{ $i - 1 }}]" :readonly="isLocked" 
                                            :class="isLocked 
                                                ? (vals[{{ $i - 1 }}] && vals[{{ $i - 1 }}].trim() 
                                                    ? 'bg-white text-slate-800 cursor-pointer font-semibold hover:bg-slate-50 focus:outline-none focus:ring-0 border-slate-200' 
                                                    : 'bg-slate-50 text-slate-300 cursor-not-allowed border-slate-100 opacity-40 focus:outline-none focus:ring-0')
                                                : 'bg-white focus:ring-1 focus:ring-blue-500 focus:border-blue-500'" 
                                            placeholder="Comp {{ $i }}" class="w-full pl-10 pr-3 py-2.5 border border-slate-200 rounded-xl text-center text-sm outline-none font-medium transition-all"
                                            @click="if (isLocked && vals[{{ $i - 1 }}] && vals[{{ $i - 1 }}].trim()) appendToken({{ $i }})">
                                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 px-1.5 py-0.5 rounded-none bg-slate-700/70 text-white text-[11px] font-semibold tracking-wider pointer-events-none select-none">
                                            C{{ $i }}
                                        </span>
                                    </div>
                                @endfor
                            </div>
                        </div>

                        <!-- Notice & Lock Button Row -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <!-- Left: Info Notice (shown only when locked) -->
                            <div x-show="isLocked" class="text-xs text-blue-700 font-semibold bg-blue-50/50 border border-blue-100 rounded-xl p-3 flex items-start gap-2.5 flex-1">
                                <i class="fa-solid fa-circle-info text-sm mt-0.5 text-blue-500"></i>
                                <div>
                                    <span class="font-bold">Locked Mode Active:</span>
                                    <span class="font-normal text-slate-600">Click on the Component boxes above to insert them into the formula.</span>
                                </div>
                            </div>
                            <div x-show="!isLocked" class="flex-1"></div> <!-- Spacer when unlocked -->

                            <!-- Right: Lock Button -->
                            <div class="shrink-0 flex justify-end">
                                <button type="button" @click="isLocked = !isLocked" class="px-5 py-2.5 rounded-xl text-sm font-semibold transition-all flex items-center gap-2 border shadow-sm"
                                    :class="isLocked 
                                        ? 'bg-blue-600 border-blue-600 text-white hover:bg-blue-700 hover:border-blue-700' 
                                        : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50'">
                                    <span x-show="isLocked" class="flex items-center gap-2">
                                        <i class="fa-solid fa-lock"></i> Unlock Component
                                    </span>
                                    <span x-show="!isLocked" class="flex items-center gap-2">
                                        <i class="fa-solid fa-lock-open"></i> Lock Component
                                    </span>
                                </button>
                            </div>
                        </div>

                        <!-- Formula Editor Section (shown when locked) -->
                        <div x-show="isLocked" class="pt-4 border-t border-slate-100 space-y-4">
                            <div>
                                <label class="block text-base font-bold text-slate-800 mb-2">Formula Editor</label>
                                <div class="flex gap-3 items-stretch">
                                    <textarea id="custom_formula_input" name="calc_operator" x-model="calcOperator" rows="2" class="flex-1 rounded-xl border border-slate-200 p-3.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 bg-slate-50/50 font-normal placeholder-slate-400" placeholder="Click active components above or use the calculator buttons below to build your formula. E.g. C2 * C3 + C4"></textarea>
                                    <button type="button" @click="calcOperator = ''" class="px-5 bg-white hover:bg-red-50 hover:text-red-600 hover:border-red-200 text-slate-500 border border-slate-200 rounded-xl text-sm font-semibold transition-all shrink-0 flex items-center justify-center">Clear</button>
                                </div>
                            </div>

                            <!-- Calculator buttons -->
                            <div class="flex flex-wrap gap-2.5 items-center">
                                <button type="button" @click="appendOperator('+')" class="w-12 h-12 flex items-center justify-center bg-slate-50 hover:bg-blue-600 hover:text-white hover:border-blue-600 border border-slate-200 text-slate-700 rounded-xl text-lg font-bold transition-all duration-150 shadow-sm">+</button>
                                <button type="button" @click="appendOperator('-')" class="w-12 h-12 flex items-center justify-center bg-slate-50 hover:bg-blue-600 hover:text-white hover:border-blue-600 border border-slate-200 text-slate-700 rounded-xl text-lg font-bold transition-all duration-150 shadow-sm">-</button>
                                <button type="button" @click="appendOperator('*')" class="w-12 h-12 flex items-center justify-center bg-slate-50 hover:bg-blue-600 hover:text-white hover:border-blue-600 border border-slate-200 text-slate-700 rounded-xl text-lg font-bold transition-all duration-150 shadow-sm">*</button>
                                <button type="button" @click="appendOperator('/')" class="w-12 h-12 flex items-center justify-center bg-slate-50 hover:bg-blue-600 hover:text-white hover:border-blue-600 border border-slate-200 text-slate-700 rounded-xl text-lg font-bold transition-all duration-150 shadow-sm">/</button>
                                <button type="button" @click="appendOperator('(')" class="w-12 h-12 flex items-center justify-center bg-slate-50 hover:bg-blue-600 hover:text-white hover:border-blue-600 border border-slate-200 text-slate-700 rounded-xl text-lg font-bold transition-all duration-150 shadow-sm">(</button>
                                <button type="button" @click="appendOperator(')')" class="w-12 h-12 flex items-center justify-center bg-slate-50 hover:bg-blue-600 hover:text-white hover:border-blue-600 border border-slate-200 text-slate-700 rounded-xl text-lg font-bold transition-all duration-150 shadow-sm">)</button>
                                
                                <!-- Backspace button -->
                                <button type="button" @click="calcOperator = calcOperator.trim().includes(' ') ? calcOperator.trim().substring(0, calcOperator.trim().lastIndexOf(' ')) + ' ' : ''" class="px-4 h-12 flex items-center justify-center bg-slate-50 hover:bg-amber-600 hover:text-white hover:border-amber-600 border border-slate-200 text-slate-600 rounded-xl text-sm font-semibold transition-all duration-150 shadow-sm">
                                    <i class="fa-solid fa-delete-left mr-1.5 text-base"></i> Backspace
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="p-6 border-t border-slate-100 bg-white flex justify-end gap-3 rounded-b-xl shrink-0">
                    <button type="button" onclick="closeFormulaModal()" class="px-5 py-2.5 border border-slate-200 text-slate-700 rounded-xl hover:bg-slate-50 transition-colors text-sm font-semibold">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-colors text-sm font-semibold shadow-sm shadow-blue-200">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        var table = $('#kpiTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('master.kpi_list.table') }}",
                type: 'POST',
                data: function(d) {
                    d._token = "{{ csrf_token() }}";
                    d.search.value = $('#searchInput').val();
                    d.filter_pillar = $('#filter_pillar').val();
                    d.filter_category = $('#filter_category').val();
                    d.filter_calculation_method = $('#filter_calculation_method').val();
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
                    data: 'no_kpi',
                    name: 'no_kpi',
                    className: 'text-slate-700'
                },
                {
                    data: 'objective',
                    name: 'objective',
                    className: 'text-slate-700'
                },
                {
                    data: 'definition',
                    name: 'definition',
                    className: 'text-slate-700'
                },
                {
                    data: 'pillar',
                    name: 'pillar',
                    className: 'text-slate-700'
                },
                {
                    data: 'category',
                    name: 'category',
                    className: 'text-slate-700'
                },
                {
                    data: 'target',
                    name: 'target',
                    className: 'text-slate-700'
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

        // Search trigger
        var searchTimer;
        $('#searchInput').on('keyup input', function() {
            var self = this;
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function() {
                table.search($(self).val()).draw();
            }, 500);
        });

        // Redraw on filter changes
        window.addEventListener('filter-pillar-changed', function() {
            table.draw();
        });
        window.addEventListener('filter-category-changed', function() {
            table.draw();
        });
        window.addEventListener('filter-calculation-method-changed', function() {
            table.draw();
        });

        // Reset Filters
        $('#resetFilters').on('click', function() {
            $('#searchInput').val('');
            window.dispatchEvent(new CustomEvent('set-filter-pillar', { detail: { id: '', name: '' } }));
            window.dispatchEvent(new CustomEvent('set-filter-category', { detail: { id: '', name: '' } }));
            window.dispatchEvent(new CustomEvent('set-filter-calculation-method', { detail: { id: '', name: '' } }));
            table.search('').draw();
        });

        // Parent Objective is optional, so remove required attribute from searchable-select hidden inputs
        $('#create_parent_objective_id').removeAttr('required');
        $('#edit_parent_objective_id').removeAttr('required');

        // History units are optional, remove required validation from historical inputs
        const currentYear = new Date().getFullYear();
        for (let i = 5; i >= 1; i--) {
            const yr = currentYear - i;
            $('#create_history_unit_' + yr).removeAttr('required');
            $('#edit_history_unit_' + yr).removeAttr('required');
        }

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

        $('#create_definition, #edit_definition').on('input', function() {
            autoResizeTextarea(this);
        });

        window.autoResizeTextarea = autoResizeTextarea;

        // Submit Create Form
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
                    if (response.success) {
                        showToast('KPI added successfully', 'success');
                        closeCreateModal();
                        table.ajax.reload();
                    } else {
                        showToast(response.message || 'Failed to add KPI', 'error');
                    }
                },
                error: function(xhr) {
                    showToast(xhr.responseJSON?.message || 'Failed to save data.', 'error');
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });

        // Submit Edit Form
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
                    if (response.success) {
                        showToast('KPI updated successfully', 'success');
                        closeEditModal();
                        table.ajax.reload();
                    } else {
                        showToast(response.message || 'Failed to update KPI', 'error');
                    }
                },
                error: function(xhr) {
                    showToast(xhr.responseJSON?.message || 'Failed to update data.', 'error');
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });

        // Submit Formula Form
        $('#formulaForm').on('submit', function(e) {
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
                    if (response.success) {
                        showToast('Formula saved successfully', 'success');
                        closeFormulaModal();
                    } else {
                        showToast(response.message || 'Failed to save formula', 'error');
                    }
                },
                error: function(xhr) {
                    showToast(xhr.responseJSON?.message || 'Failed to save formula.', 'error');
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });
    });

    function openCreateModal() {
        window.dispatchEvent(new CustomEvent('set-create-parent-objective', {
            detail: { id: "", name: "" }
        }));
        window.dispatchEvent(new CustomEvent('set-create-pillar-kpilist', {
            detail: { id: "", name: "" }
        }));
        window.dispatchEvent(new CustomEvent('set-create-category-kpilist', {
            detail: { id: "", name: "" }
        }));
        window.dispatchEvent(new CustomEvent('set-create-unit-kpilist', {
            detail: { id: "", name: "" }
        }));
        window.dispatchEvent(new CustomEvent('set-create-operator-kpilist', {
            detail: { id: "", name: "" }
        }));
        window.dispatchEvent(new CustomEvent('set-create-calculation-method-kpilist', {
            detail: { id: "", name: "" }
        }));
        window.dispatchEvent(new CustomEvent('set-create-arrow-target-kpilist', {
            detail: { id: "", name: "" }
        }));
        window.dispatchEvent(new CustomEvent('set-create-result-kpilist', {
            detail: { id: "", name: "" }
        }));

        // Reset inputs and values in history container
        const currentYear = new Date().getFullYear();
        for (let i = 5; i >= 1; i--) {
            const yr = currentYear - i;
            $(`input[name="history[${yr}][achievement]"]`).val('');
            window.dispatchEvent(new CustomEvent(`set-create-history-unit-${yr}`, {
                detail: { id: "", name: "" }
            }));
            $('#create_history_unit_' + yr).removeAttr('required');
            $(`input[name="history[${yr}][status_achievement]"]`).prop('checked', false);
            toggleHistoryStatus(yr, 'create', false);
        }

        $('#createModal').removeClass('hidden');
        setTimeout(() => {
            if (window.autoResizeTextarea) {
                window.autoResizeTextarea(document.getElementById('create_definition'));
            }
        }, 50);
    }

    function closeCreateModal() {
        $('#createModal').addClass('hidden');
    }

    function handleEdit(btn) {
        const id = btn.getAttribute('data-id');
        const parent_objective_id = btn.getAttribute('data-parent_objective_id');
        const no_kpi = btn.getAttribute('data-no_kpi');
        const objective = btn.getAttribute('data-objective');
        const definition = btn.getAttribute('data-definition');
        const pillar = btn.getAttribute('data-pillar');
        const category = btn.getAttribute('data-category');
        const target = btn.getAttribute('data-target');

        const unit = btn.getAttribute('data-unit') || '';
        const operator_val = btn.getAttribute('data-operator') || '';
        const calculation_method = btn.getAttribute('data-calculation_method') || '';
        const arrow_target = btn.getAttribute('data-arrow_target') || '';
        const result = btn.getAttribute('data-result') || '';

        const parent_objective_name = btn.getAttribute('data-parent_objective_name') || "";

        $('#edit_id').val(id);
        
        // Dispatch events to Alpine components of searchable select
        window.dispatchEvent(new CustomEvent('set-edit-parent-objective', {
            detail: { id: parent_objective_id || "", name: parent_objective_id ? parent_objective_name : "" }
        }));

        $('#edit_no_kpi').val(no_kpi);
        $('#edit_objective').val(objective);
        $('#edit_definition').val(definition);
        
        window.dispatchEvent(new CustomEvent('set-edit-pillar-kpilist', {
            detail: { id: pillar || "", name: pillar || "" }
        }));
        
        window.dispatchEvent(new CustomEvent('set-edit-category-kpilist', {
            detail: { id: category || "", name: category || "" }
        }));
        
        $('#edit_target').val(target);

        // Operator name helper
        const opLabels = {
            '>=': '>= (Greater than or equal to)',
            '<=': '<= (Less than or equal to)',
            '=': '= (Equal to)',
            '>': '> (Greater than)',
            '<': '< (Less than)'
        };

        window.dispatchEvent(new CustomEvent('set-edit-unit-kpilist', {
            detail: { id: unit || "", name: unit || "" }
        }));
        window.dispatchEvent(new CustomEvent('set-edit-operator-kpilist', {
            detail: { id: operator_val || "", name: operator_val ? (opLabels[operator_val] || operator_val) : "" }
        }));
        window.dispatchEvent(new CustomEvent('set-edit-calculation-method-kpilist', {
            detail: { id: calculation_method || "", name: calculation_method || "" }
        }));
        window.dispatchEvent(new CustomEvent('set-edit-arrow-target-kpilist', {
            detail: { id: arrow_target || "", name: arrow_target || "" }
        }));
        window.dispatchEvent(new CustomEvent('set-edit-result-kpilist', {
            detail: { id: result || "", name: result || "" }
        }));

        // Reset edit achievements and load data
        const currentYear = new Date().getFullYear();
        for (let i = 5; i >= 1; i--) {
            const yr = currentYear - i;
            $('#edit_ach_' + yr).val('');
            window.dispatchEvent(new CustomEvent(`set-edit-history-unit-${yr}`, {
                detail: { id: "", name: "" }
            }));
            $('#edit_history_unit_' + yr).removeAttr('required');
            $(`input[name="history[${yr}][status_achievement]"]`).prop('checked', false);
            toggleHistoryStatus(yr, 'edit', false);
        }

        // Fetch history data and update input fields
        $.ajax({
            url: `{{ route('master.kpi_list.history', '') }}/${id}`,
            type: 'GET',
            success: function(response) {
                const historyMap = response.history || {};
                for (let i = 5; i >= 1; i--) {
                    const yr = currentYear - i;
                    const record = historyMap[yr] || { achievement: '', unit: '', status_achievement: '' };
                    const ach = record.achievement !== null && record.achievement !== '' ? record.achievement : '';
                    $('#edit_ach_' + yr).val(ach);

                    // Only set unit if achievement has a value
                    const unitVal = ach !== '' ? (record.unit || '') : '';
                    window.dispatchEvent(new CustomEvent(`set-edit-history-unit-${yr}`, {
                        detail: { id: unitVal, name: unitVal }
                    }));

                    const statusAch = record.status_achievement || '';
                    if (statusAch === 'Achieved') {
                        $(`input[name="history[${yr}][status_achievement]"][value="Achieved"]`).prop('checked', true);
                    } else if (statusAch === 'Not Achieved') {
                        $(`input[name="history[${yr}][status_achievement]"][value="Not Achieved"]`).prop('checked', true);
                    }

                    toggleHistoryStatus(yr, 'edit', ach !== '');
                }
            }
        });

        // Auto-resize Definition textarea on load
        setTimeout(() => {
            if (window.autoResizeTextarea) {
                window.autoResizeTextarea(document.getElementById('edit_definition'));
            }
        }, 50);

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

    // Modal dismiss logic
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
            url: "{{ route('master.kpi_list.delete') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: id
            },
            success: function(response) {
                $('#icon_delete_' + no).removeClass('hidden');
                $('#loader_delete_' + no).addClass('hidden');

                if (response.success) {
                    showToast('Data deleted successfully', 'success');
                    $('#kpiTable').DataTable().ajax.reload();
                } else {
                    showToast('Failed to delete item', 'error');
                }
            },
            error: function() {
                $('#icon_delete_' + no).removeClass('hidden');
                $('#loader_delete_' + no).addClass('hidden');
                showToast('An error occurred', 'error');
            }
        });
    }

    function toggleHistoryStatus(yr, mode, hasValue) {
        const radios = $(`input[name="history[${yr}][status_achievement]"]`);
        if (hasValue) {
            radios.prop('disabled', false);
            radios.closest('.bg-slate-100').removeClass('opacity-50 pointer-events-none');
        } else {
            radios.prop('disabled', true).prop('checked', false);
            radios.closest('.bg-slate-100').addClass('opacity-50 pointer-events-none');
        }
    }

    // When achievement is cleared, automatically clear the unit for that year
    function onHistoryAchievementInput(input, yr, mode) {
        const hasValue = input.value.trim() !== '';
        toggleHistoryStatus(yr, mode, hasValue);
        if (!hasValue) {
            const event = `set-${mode}-history-unit-${yr}`;
            window.dispatchEvent(new CustomEvent(event, {
                detail: { id: '', name: '' }
            }));
        }
    }

    // Wire up edit achievement inputs on document ready
    $(document).ready(function() {
        const currentYear = new Date().getFullYear();
        for (let i = 5; i >= 1; i--) {
            const yr = currentYear - i;
            $('#edit_ach_' + yr).on('input', function() {
                onHistoryAchievementInput(this, yr, 'edit');
            });
        }
    });
    function openFormulaModal() {
        $('#formulaModal').removeClass('hidden');
    }

    function closeFormulaModal() {
        $('#formulaModal').addClass('hidden');
    }

    function handleFormula(btn) {
        const kpiId = btn.getAttribute('data-id');
        const kpiName = btn.getAttribute('data-kpi_name');
        $('#formulaModalTitle').text('Manage Formula: ' + kpiName);
        $('#formula_kpi_list_id').val(kpiId);

        $.ajax({
            url: `{{ route('master.kpi_list.formula', '') }}/${kpiId}`,
            type: 'GET',
            success: function(response) {
                const formula = response.formula || {};
                
                const formEl = document.getElementById('formulaForm');
                if (formEl && window.Alpine) {
                    const alpineData = window.Alpine.$data(formEl);
                    if (alpineData) {
                        const hasFormula = formula.calc_operator !== null && formula.calc_operator !== undefined && formula.calc_operator.trim() !== '';
                        let rawOp = hasFormula ? formula.calc_operator : '';
                        if (rawOp) {
                            rawOp = rawOp.replace(/\[?comp_(\d+)\]?/g, 'C$1');
                        }
                        alpineData.calcOperator = rawOp;
                        alpineData.isLocked = hasFormula;
                        for (let i = 1; i <= 20; i++) {
                            alpineData.vals[i - 1] = formula['comp_' + i] !== null && formula['comp_' + i] !== undefined ? formula['comp_' + i] : '';
                        }
                    }
                }
                
                openFormulaModal();
            },
            error: function() {
                showToast('Failed to fetch formula data.', 'error');
            }
        });
    }
</script>
@endpush
@endsection