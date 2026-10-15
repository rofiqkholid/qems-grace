@extends('layouts.app')

@php
    $hideCentralToast = true;
@endphp

@section('title', 'KPI Timeline: ' . ($kpi->objective ?? 'Activity Plan'))

@section('content')
@include('layouts.sidebar')
@include('components.toast')

<!-- Main Content -->
<div class="lg:ml-20 min-h-screen flex flex-col bg-slate-50">
    @include('layouts.header')

    <!-- Page Content -->
    <main class="flex-1 p-6">
        <!-- Back Button & Page Title -->
        <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('kpi.company.detail', \App\Http\Controllers\KPICompanyController::encodeId($kpi->id)) }}" class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl shrink-0 flex items-center justify-center bg-white border border-slate-200 hover:bg-slate-50 transition-colors">
                    <i class="fa-solid fa-arrow-left text-[11px] sm:text-sm text-slate-600"></i>
                </a>
                <div>
                    <h1 class="text-lg sm:text-2xl font-bold text-slate-800">
                        KPI Timeline: {{ $kpi->objective ?? 'Engagement & Corporate Culture' }}
                    </h1>
                    <p class="text-slate-500 text-sm">Detailed monthly operational planning and activity tracking.</p>
                </div>
            </div>

            <!-- Action Buttons Header -->
            <div class="flex flex-wrap items-center gap-2">
                <!-- Print Activity Plan Button -->
                <button type="button" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition-colors shadow-sm">
                    <i class="fa-solid fa-file-pdf text-sm"></i>
                    Print Activity Plan
                </button>

                <!-- Add Activity Button -->
                <button type="button" onclick="openAddActivityModal()" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-colors shadow-sm shadow-blue-200">
                    <i class="fa-solid fa-plus text-xs"></i>
                    Add Activity
                </button>
            </div>
        </div>

        <!-- Main Card Section -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-6">
            
            <!-- Table Container -->
            <div class="overflow-x-auto border border-slate-200 rounded-xl">
                <table class="w-full text-sm text-center border-collapse">
                    <thead>
                        <!-- Row 1 Header -->
                        <tr class="bg-slate-100 text-slate-700 font-bold">
                            <th rowspan="2" class="p-3 whitespace-nowrap min-w-[50px] border border-slate-200">No</th>
                            <th rowspan="2" class="p-3 whitespace-nowrap min-w-[140px] text-left border border-slate-200">Support Topic</th>
                            <th rowspan="2" class="p-3 whitespace-nowrap min-w-[180px] text-left border border-slate-200">Activity Plan</th>
                            <th rowspan="2" class="p-3 whitespace-nowrap min-w-[100px] border border-slate-200">PIC</th>
                            <th rowspan="2" class="p-3 whitespace-nowrap min-w-[120px] border border-slate-200">Supporting</th>
                            <th rowspan="2" class="p-3 whitespace-nowrap min-w-[100px] border border-slate-200">Quick Plan</th>
                            <th colspan="12" class="py-2.5 px-3 border border-slate-200 text-center font-bold">
                                Annual Operational Planning
                            </th>
                            <th rowspan="2" class="p-3 whitespace-nowrap min-w-[90px] border border-slate-200">% Success Rate</th>
                            <th rowspan="2" class="p-3 whitespace-nowrap min-w-[90px] border border-slate-200">Status</th>
                            <th rowspan="2" class="p-3 whitespace-nowrap min-w-[130px] text-left border border-slate-200">Remark</th>
                            <th rowspan="2" class="p-3 whitespace-nowrap min-w-[90px] border border-slate-200">Action</th>
                        </tr>
                        <!-- Row 2 Header (Months) -->
                        <tr class="bg-slate-100 text-slate-700 text-xs font-normal">
                            <th class="py-2 px-2 min-w-[36px] border border-slate-200 font-normal">Jan</th>
                            <th class="py-2 px-2 min-w-[36px] border border-slate-200 font-normal">Feb</th>
                            <th class="py-2 px-2 min-w-[36px] border border-slate-200 font-normal">Mar</th>
                            <th class="py-2 px-2 min-w-[36px] border border-slate-200 font-normal">Apr</th>
                            <th class="py-2 px-2 min-w-[36px] border border-slate-200 font-normal">May</th>
                            <th class="py-2 px-2 min-w-[36px] border border-slate-200 font-normal">Jun</th>
                            <th class="py-2 px-2 min-w-[36px] border border-slate-200 font-normal">Jul</th>
                            <th class="py-2 px-2 min-w-[36px] border border-slate-200 font-normal">Aug</th>
                            <th class="py-2 px-2 min-w-[36px] border border-slate-200 font-normal">Sep</th>
                            <th class="py-2 px-2 min-w-[36px] border border-slate-200 font-normal">Oct</th>
                            <th class="py-2 px-2 min-w-[36px] border border-slate-200 font-normal">Nov</th>
                            <th class="py-2 px-2 min-w-[36px] border border-slate-200 font-normal">Dec</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white text-slate-700">
                        @forelse($activityPlans as $index => $plan)
                            @php
                                $mArr = is_string($plan->months_data) ? json_decode($plan->months_data, true) : (array)$plan->months_data;
                            @endphp
                            <!-- Sub-Row 1: Main Info + Planned Months (Top Row) -->
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td rowspan="2" class="p-3 font-medium text-slate-600 align-middle border border-slate-200">{{ $index + 1 }}</td>
                                <td rowspan="2" class="p-3 text-left font-medium text-slate-800 align-middle border border-slate-200">{{ $plan->support_topic ?: '-' }}</td>
                                <td rowspan="2" class="p-3 text-left text-slate-700 font-normal align-middle border border-slate-200">{{ $plan->activity_plan }}</td>
                                <td rowspan="2" class="p-3 font-semibold text-slate-700 align-middle border border-slate-200">{{ $plan->pic ?: '-' }}</td>
                                <td rowspan="2" class="p-3 text-slate-600 align-middle border border-slate-200">{{ $plan->supporting ?: '-' }}</td>
                                <td rowspan="2" class="p-3 font-semibold text-slate-600 align-middle border border-slate-200">{{ $plan->quick_plan ?: '-' }}</td>
                                
                                <!-- Top Month Cells: Plan (Hatch Pattern if Active) -->
                                @for($m = 1; $m <= 12; $m++)
                                    @php
                                        $isActive = isset($mArr[$m]) && $mArr[$m];
                                    @endphp
                                    <td class="p-1 h-8 align-middle border border-slate-200 min-w-[36px]">
                                        @if($isActive)
                                            <div class="w-full h-full rounded-xs bg-slate-200/90 [background-image:repeating-linear-gradient(45deg,#cbd5e1_0,#cbd5e1_2px,transparent_0,transparent_6px)]" title="Planned for Month {{ $m }}"></div>
                                        @else
                                            <div class="w-full h-full bg-white"></div>
                                        @endif
                                    </td>
                                @endfor

                                <td rowspan="2" class="p-3 font-bold text-slate-700 align-middle border border-slate-200">{{ number_format($plan->success_rate, 0) }}%</td>

                                @php
                                    $rate = (float) ($plan->success_rate ?? 0);
                                    if ($rate >= 100) {
                                        $statusBg = 'bg-emerald-500 text-white';
                                        $defaultStatus = 'Completed';
                                    } elseif ($rate > 0) {
                                        $statusBg = 'bg-amber-400 text-slate-900';
                                        $defaultStatus = 'On Progress';
                                    } else {
                                        $statusBg = 'bg-rose-600 text-white';
                                        $defaultStatus = 'Not Started';
                                    }
                                @endphp

                                <td rowspan="2" class="p-0 align-middle border border-slate-200 {{ $statusBg }}">
                                    <div class="w-full h-full min-h-[64px]" title="Success Rate: {{ number_format($rate, 0) }}%"></div>
                                </td>
                                <td rowspan="2" class="p-3 text-center align-middle border border-slate-200">
                                    @php
                                        $rateVal = (float) ($plan->success_rate ?? 0);
                                        $remarkVal = ($rateVal >= 100) ? 'Closed' : 'Open';
                                        $isClosed = ($remarkVal === 'Closed');
                                        $remarkBg = $isClosed ? 'bg-emerald-500 text-white' : 'bg-amber-400 text-slate-900';
                                    @endphp
                                    <span class="inline-block px-3 py-1 text-xs font-bold rounded-none select-none {{ $remarkBg }}">
                                        {{ $remarkVal }}
                                    </span>
                                </td>
                                <td rowspan="2" class="p-3 align-middle border border-slate-200">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button type="button" class="btn-edit-plan w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-500 hover:bg-blue-100 hover:text-blue-600 transition-all duration-200" data-plan='@json($plan)' title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-500" viewBox="0 0 24 24" fill="none">
                                                <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="currentColor"></path>
                                                <path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="currentColor"></path>
                                                <path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="currentColor"></path>
                                            </svg>
                                        </button>
                                        <button type="button" class="btn-delete-plan w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-500 hover:bg-red-100 hover:text-red-600 transition-all duration-200" data-id="{{ $plan->id }}" title="Delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-red-600" viewBox="0 0 24 24" fill="none">
                                                <path opacity="0.3" d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"/>
                                                <path d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V7H5V5Z" fill="currentColor"/>
                                                <path d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Sub-Row 2: Actual Months (Bottom Row) -->
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                @php
                                    $eArr = is_string($plan->evidences_data) ? json_decode($plan->evidences_data, true) : (array)($plan->evidences_data ?? []);
                                @endphp
                                @for($m = 1; $m <= 12; $m++)
                                    @php
                                        $hasFile = isset($eArr[$m]) && !empty($eArr[$m]);
                                    @endphp
                                    <td class="btn-upload-evidence p-1 h-8 align-middle border border-slate-200 min-w-[36px] bg-white hover:bg-slate-100 cursor-pointer transition-colors" data-plan-id="{{ $plan->id }}" data-month="{{ $m }}" data-file="{{ $hasFile ? asset($eArr[$m]) : '' }}" title="{{ $hasFile ? 'Uploaded Evidence Month ' . $m . ' (Click to View/Edit)' : 'Upload Evidence Month ' . $m }}">
                                        @if($hasFile)
                                            <div class="w-full h-full rounded-xs bg-emerald-500 hover:bg-emerald-600 transition-colors shadow-2xs" title="Uploaded Evidence Month {{ $m }}"></div>
                                        @else
                                            <div class="w-full h-full bg-white"></div>
                                        @endif
                                    </td>
                                @endfor
                            </tr>
                        @empty
                            <!-- Empty Data State -->
                            <tr>
                                <td colspan="23" class="py-12 px-4 text-slate-400 text-center border border-slate-200">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <i class="fa-regular fa-folder-open text-4xl text-slate-300"></i>
                                        <span class="text-sm font-medium text-slate-500">No Activity Plan data available</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </main>

    @include('layouts.footer')
</div>

<!-- Modal Container: Add Activity Plan -->
<div id="addActivityModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-slate-900/50 transition-opacity" onclick="closeAddActivityModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4 sm:p-6 overflow-hidden">
        <div class="bg-white rounded-2xl w-full max-w-7xl h-[85vh] shadow-2xl relative my-auto flex flex-col overflow-hidden">
            
            <!-- Modal Header -->
            <div class="flex justify-between items-center px-6 sm:px-8 py-4 border-b border-slate-200/80 bg-slate-50/80 shrink-0">
                <h3 id="addActivityModalTitle" class="text-lg sm:text-xl font-bold text-slate-800">Add Activity Plan</h3>
                <button onclick="closeAddActivityModal()" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-200/60 transition-colors">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <!-- Form -->
            <form id="addActivityForm" action="{{ route('kpi.company.activity_plan.store') }}" method="POST" class="flex-1 flex flex-col justify-between overflow-hidden">
                @csrf
                <input type="hidden" name="kpi_company_id" value="{{ \App\Http\Controllers\KPICompanyController::encodeId($kpi->id) }}">
                <input type="hidden" name="quick_plan" id="quick_plan_hidden" value="Custom">

                <!-- Scrollable Body Content -->
                <div class="p-6 sm:p-8 space-y-6 overflow-y-auto flex-1">
                    <!-- Support Topic -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Support Topic</label>
                        <div class="flex flex-wrap items-center gap-5 text-sm font-medium text-slate-700">
                            @foreach(['Safety', 'Quality', 'People', 'Cost', 'Responsiveness', 'Delivery', 'Environment & Energy'] as $topic)
                            <label class="relative inline-flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox" name="support_topic[]" value="{{ $topic }}" class="sr-only peer">
                                <div class="w-5 h-5 rounded-md border border-slate-300 flex items-center justify-center peer-checked:border-sky-400 peer-checked:bg-sky-50 peer-checked:[&_svg]:scale-100 transition-all shrink-0">
                                    <svg class="w-3.5 h-3.5 text-sky-500 scale-0 transition-transform" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <span class="text-sm font-medium text-slate-700">{{ $topic }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Activity Plan Textarea & Quick Select Row -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-stretch">
                        <!-- Activity Plan -->
                        <div class="lg:col-span-2 flex flex-col">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Activity Plan <span class="text-red-500">*</span></label>
                            <textarea name="activity_plan" required class="flex-1 w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm outline-none transition-all placeholder-slate-400 resize-none min-h-[140px]" placeholder="Describe the activity plan..."></textarea>
                        </div>

                        <!-- Quick Select & Month Range -->
                        <div class="flex flex-col">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Quick Select</label>
                            <div class="flex-1 bg-slate-50/60 p-4 rounded-2xl border border-slate-200/80 flex flex-col justify-between space-y-4">
                                <div>
                                     <div class="flex flex-wrap gap-1.5">
                                        <button type="button" id="btn_qp_Q1" onclick="setQuickPlan('Q1')" class="quick-plan-btn px-3 py-1.5 rounded-none text-xs font-semibold border border-slate-200 bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all">Q1</button>
                                        <button type="button" id="btn_qp_Q2" onclick="setQuickPlan('Q2')" class="quick-plan-btn px-3 py-1.5 rounded-none text-xs font-semibold border border-slate-200 bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all">Q2</button>
                                        <button type="button" id="btn_qp_Q3" onclick="setQuickPlan('Q3')" class="quick-plan-btn px-3 py-1.5 rounded-none text-xs font-semibold border border-slate-200 bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all">Q3</button>
                                        <button type="button" id="btn_qp_Q4" onclick="setQuickPlan('Q4')" class="quick-plan-btn px-3 py-1.5 rounded-none text-xs font-semibold border border-slate-200 bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all">Q4</button>
                                        <button type="button" id="btn_qp_Full" onclick="setQuickPlan('Full')" class="quick-plan-btn px-3 py-1.5 rounded-none text-xs font-semibold border border-slate-200 bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all">Full</button>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-600 mb-1">Start Month</label>
                                        <x-searchable-select
                                            name="start_month"
                                            id="create_start_month"
                                            label="Start Month"
                                            :initialOptions="[
                                                ['id' => '1', 'name' => '1 (Jan)'],
                                                ['id' => '2', 'name' => '2 (Feb)'],
                                                ['id' => '3', 'name' => '3 (Mar)'],
                                                ['id' => '4', 'name' => '4 (Apr)'],
                                                ['id' => '5', 'name' => '5 (May)'],
                                                ['id' => '6', 'name' => '6 (Jun)'],
                                                ['id' => '7', 'name' => '7 (Jul)'],
                                                ['id' => '8', 'name' => '8 (Aug)'],
                                                ['id' => '9', 'name' => '9 (Sep)'],
                                                ['id' => '10', 'name' => '10 (Oct)'],
                                                ['id' => '11', 'name' => '11 (Nov)'],
                                                ['id' => '12', 'name' => '12 (Dec)']
                                            ]"
                                            updateEvent="set-start-month"
                                            changeEvent="start-month-changed"
                                            hideLabel="true" />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-600 mb-1">End Month</label>
                                        <x-searchable-select
                                            name="end_month"
                                            id="create_end_month"
                                            label="End Month"
                                            :initialOptions="[
                                                ['id' => '1', 'name' => '1 (Jan)'],
                                                ['id' => '2', 'name' => '2 (Feb)'],
                                                ['id' => '3', 'name' => '3 (Mar)'],
                                                ['id' => '4', 'name' => '4 (Apr)'],
                                                ['id' => '5', 'name' => '5 (May)'],
                                                ['id' => '6', 'name' => '6 (Jun)'],
                                                ['id' => '7', 'name' => '7 (Jul)'],
                                                ['id' => '8', 'name' => '8 (Aug)'],
                                                ['id' => '9', 'name' => '9 (Sep)'],
                                                ['id' => '10', 'name' => '10 (Oct)'],
                                                ['id' => '11', 'name' => '11 (Nov)'],
                                                ['id' => '12', 'name' => '12 (Dec)']
                                            ]"
                                            updateEvent="set-end-month"
                                            changeEvent="end-month-changed"
                                            hideLabel="true" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- PIC & Supporting Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- PIC -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">PIC</label>
                            <x-searchable-select
                                name="pic"
                                id="create_pic"
                                label="PIC"
                                apiUrl="{{ route('kpi.company.departments') }}"
                                updateEvent="set-create-pic"
                                hideLabel="true" />
                        </div>

                        <!-- Supporting -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Supporting</label>
                            <x-searchable-select-multi
                                name="supporting"
                                id="create_supporting"
                                label="Supporting"
                                apiUrl="{{ route('kpi.company.departments') }}"
                                updateEvent="set-create-supporting"
                                hideLabel="true"
                                maxItems="0" />
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 sm:px-8 py-4 border-t border-slate-200/80 bg-slate-50/80 shrink-0 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeAddActivityModal()" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-sm transition-colors border border-slate-200/80">
                        Cancel
                    </button>
                    <button type="submit" id="saveActivityBtn" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-sm transition-all shadow-md shadow-blue-200 flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Save</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Upload Evidence Modal -->
<div id="uploadEvidenceModal" class="fixed inset-0 z-[1000] hidden">
    <div class="fixed inset-0 bg-slate-900/50 transition-opacity" onclick="closeUploadEvidenceModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl w-full max-w-lg shadow-2xl overflow-hidden flex flex-col my-auto">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center bg-white shrink-0">
                <h3 class="text-base font-bold text-slate-800">Upload Evidence / Document</h3>
                <button type="button" onclick="closeUploadEvidenceModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Upload Form -->
            <form id="uploadEvidenceForm" action="#" method="POST" enctype="multipart/form-data" class="flex flex-col" onsubmit="event.preventDefault(); submitEvidenceForm();">
                @csrf
                <input type="hidden" name="activity_plan_id" id="modal_activity_plan_id">
                <input type="hidden" name="month_num" id="modal_month_num">

                <!-- Modal Body -->
                <div class="p-6 space-y-4 text-left">
                    <p class="text-xs text-slate-500">
                        Please upload an evidence file (Image/PDF/etc.) before saving the data.
                    </p>

                    <div id="modal_current_file_wrapper" class="hidden p-2.5 bg-blue-50 border border-blue-200 rounded-lg text-xs flex items-center justify-between text-blue-700">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-paperclip"></i>
                            <span class="font-medium">Current File:</span>
                            <a id="modal_current_file_link" href="#" target="_blank" class="font-bold underline hover:text-blue-900">View Document</a>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Evidence</label>
                        <input type="file" name="evidence" required class="w-full px-3 py-2 border border-slate-200 text-sm file:mr-4 file:py-1.5 file:px-3.5 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 outline-none">
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-3 border-t border-slate-200 flex justify-end gap-3 bg-slate-50 shrink-0">
                    <button type="button" onclick="closeUploadEvidenceModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-lg transition-colors border border-slate-200">
                        Cancel
                    </button>
                    <button type="submit" id="saveEvidenceBtn" class="px-4 py-2 text-xs bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition-all shadow-sm flex items-center gap-1.5">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Save</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-[1000] hidden">
    <div class="fixed inset-0 bg-slate-900/50 transition-opacity" onclick="closeDeleteModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl w-full max-w-sm transform transition-all shadow-2xl overflow-hidden my-auto">
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-triangle-exclamation text-2xl text-red-600"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">Confirm Delete</h3>
                <p class="text-slate-500 text-sm">Are you sure you want to delete this Activity Plan? This action cannot be undone.</p>
            </div>
            <div class="p-6 pt-0 flex gap-3">
                <button type="button" onclick="closeDeleteModal()" class="flex-1 px-4 py-2 bg-slate-100 text-slate-700 font-medium rounded-lg hover:bg-slate-200 transition-colors text-sm">Cancel</button>
                <button type="button" onclick="confirmExecuteDelete()" id="confirmDeleteBtn" class="flex-1 px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors text-sm">Delete</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let supportingList = [];

    function openUploadEvidenceModal(planId, monthNum, currentFilePath) {
        $('#modal_activity_plan_id').val(planId);
        $('#modal_month_num').val(monthNum);
        
        if (currentFilePath && currentFilePath !== '') {
            $('#modal_current_file_link').attr('href', currentFilePath);
            $('#modal_current_file_wrapper').removeClass('hidden');
        } else {
            $('#modal_current_file_wrapper').addClass('hidden');
        }

        document.getElementById('uploadEvidenceModal').classList.remove('hidden');
    }

    function closeUploadEvidenceModal() {
        document.getElementById('uploadEvidenceModal').classList.add('hidden');
    }

    function submitEvidenceForm() {
        const form = $('#uploadEvidenceForm')[0];
        const formData = new FormData(form);
        const submitBtn = $('#saveEvidenceBtn');
        const originalText = submitBtn.html();

        submitBtn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-1"></i> Saving...');

        $.ajax({
            url: "{{ route('kpi.company.activity_plan.upload_evidence') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                closeUploadEvidenceModal();
                if (typeof showToast === 'function') {
                    showToast(response.message || 'Dokumen berhasil diupload.', 'success');
                }
                setTimeout(function() {
                    window.location.reload();
                }, 500);
            },
            error: function(xhr) {
                if (typeof showToast === 'function') {
                    showToast(xhr.responseJSON?.message || 'Failed to upload evidence.', 'error');
                } else {
                    alert(xhr.responseJSON?.message || 'Failed to upload evidence.');
                }
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    }

    function openAddActivityModal() {
        $('#addActivityModalTitle').text('Add Activity Plan');
        $('#addActivityForm')[0].reset();
        $('#plan_id_hidden').remove();
        $('input[name="support_topic[]"]').prop('checked', false);
        document.getElementById('addActivityModal').classList.remove('hidden');

        window.dispatchEvent(new CustomEvent('set-start-month', { detail: { id: '1', name: '1 (Jan)' } }));
        window.dispatchEvent(new CustomEvent('set-end-month', { detail: { id: '1', name: '1 (Jan)' } }));
        window.dispatchEvent(new CustomEvent('set-create-pic', { detail: { id: '', name: '' } }));
        window.dispatchEvent(new CustomEvent('set-create-supporting', { detail: { id: '', name: '' } }));
        clearQuickPlanSelection();
    }

    function editActivityPlan(plan) {
        $('#addActivityModalTitle').text('Edit Activity Plan');
        $('#plan_id_hidden').remove();
        $('#addActivityForm').append('<input type="hidden" name="id" id="plan_id_hidden" value="' + plan.id + '">');
        
        $('textarea[name="activity_plan"]').val(plan.activity_plan || '');

        const topics = plan.support_topic ? plan.support_topic.split(',').map(s => s.trim()) : [];
        $('input[name="support_topic[]"]').prop('checked', false);
        topics.forEach(t => {
            $('input[name="support_topic[]"][value="' + t + '"]').prop('checked', true);
        });

        const startM = plan.start_month || 1;
        const endM = plan.end_month || 1;
        const monthNames = ['', '1 (Jan)', '2 (Feb)', '3 (Mar)', '4 (Apr)', '5 (May)', '6 (Jun)', '7 (Jul)', '8 (Aug)', '9 (Sep)', '10 (Oct)', '11 (Nov)', '12 (Dec)'];
        
        window.dispatchEvent(new CustomEvent('set-start-month', { detail: { id: String(startM), name: monthNames[startM] || (startM + ' (Jan)') } }));
        window.dispatchEvent(new CustomEvent('set-end-month', { detail: { id: String(endM), name: monthNames[endM] || (endM + ' (Jan)') } }));

        if (plan.quick_plan && plan.quick_plan !== 'Custom') {
            setQuickPlan(plan.quick_plan);
        } else {
            clearQuickPlanSelection();
        }

        if (plan.pic) {
            window.dispatchEvent(new CustomEvent('set-create-pic', { detail: { id: plan.pic, name: plan.pic } }));
        }
        if (plan.supporting) {
            window.dispatchEvent(new CustomEvent('set-create-supporting', { detail: { id: plan.supporting, name: plan.supporting } }));
        }

        document.getElementById('addActivityModal').classList.remove('hidden');
    }

    function closeAddActivityModal() {
        document.getElementById('addActivityModal').classList.add('hidden');
    }

    let isProgrammaticChange = false;

    function setQuickPlan(type) {
        isProgrammaticChange = true;
        let startId = '1', startName = '1 (Jan)', endId = '1', endName = '1 (Jan)';
        if (type === 'Q1') { startId = '1'; startName = '1 (Jan)'; endId = '3'; endName = '3 (Mar)'; }
        else if (type === 'Q2') { startId = '4'; startName = '4 (Apr)'; endId = '6'; endName = '6 (Jun)'; }
        else if (type === 'Q3') { startId = '7'; startName = '7 (Jul)'; endId = '9'; endName = '9 (Sep)'; }
        else if (type === 'Q4') { startId = '10'; startName = '10 (Oct)'; endId = '12'; endName = '12 (Dec)'; }
        else if (type === 'Full') { startId = '1'; startName = '1 (Jan)'; endId = '12'; endName = '12 (Dec)'; }

        window.dispatchEvent(new CustomEvent('set-start-month', { detail: { id: startId, name: startName } }));
        window.dispatchEvent(new CustomEvent('set-end-month', { detail: { id: endId, name: endName } }));
        $('#quick_plan_hidden').val(type);
        
        $('.quick-plan-btn').removeClass('bg-sky-100 text-sky-700 border-sky-400 font-bold').addClass('bg-slate-100 text-slate-600 border-slate-200 hover:bg-slate-200 font-semibold');
        $('#btn_qp_' + type).removeClass('bg-slate-100 text-slate-600 border-slate-200 hover:bg-slate-200 font-semibold').addClass('bg-sky-100 text-sky-700 border-sky-400 font-bold');

        setTimeout(() => { isProgrammaticChange = false; }, 300);
    }

    function clearQuickPlanSelection() {
        if (isProgrammaticChange) return;
        $('.quick-plan-btn').removeClass('bg-sky-100 text-sky-700 border-sky-400 font-bold').addClass('bg-slate-100 text-slate-600 border-slate-200 hover:bg-slate-200 font-semibold');
        $('#quick_plan_hidden').val('Custom');
    }

    function toggleRemark(planId, btnEl) {
        const currentRemark = $(btnEl).attr('data-remark') || 'Closed';
        const newRemark = (currentRemark === 'Open') ? 'Closed' : 'Open';

        if (newRemark === 'Closed') {
            $(btnEl).removeClass('bg-amber-400 text-slate-900 hover:bg-amber-500')
                    .addClass('bg-emerald-500 text-white hover:bg-emerald-600')
                    .text('Closed')
                    .attr('data-remark', 'Closed');
        } else {
            $(btnEl).removeClass('bg-emerald-500 text-white hover:bg-emerald-600')
                    .addClass('bg-amber-400 text-slate-900 hover:bg-amber-500')
                    .text('Open')
                    .attr('data-remark', 'Open');
        }

        $.ajax({
            url: "{{ route('kpi.company.activity_plan.toggle_remark') }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                id: planId
            },
            error: function() {
                if (currentRemark === 'Closed') {
                    $(btnEl).removeClass('bg-amber-400 text-slate-900 hover:bg-amber-500')
                            .addClass('bg-emerald-500 text-white hover:bg-emerald-600')
                            .text('Closed')
                            .attr('data-remark', 'Closed');
                } else {
                    $(btnEl).removeClass('bg-emerald-500 text-white hover:bg-emerald-600')
                            .addClass('bg-amber-400 text-slate-900 hover:bg-amber-500')
                            .text('Open')
                            .attr('data-remark', 'Open');
                }
                if (typeof showToast === 'function') {
                    showToast('Failed to update Remark state.', 'error');
                }
            }
        });
    }

    window.addEventListener('start-month-changed', clearQuickPlanSelection);
    window.addEventListener('end-month-changed', clearQuickPlanSelection);

    $(document).ready(function() {
        $(document).on('click', '.btn-toggle-remark', function() {
            const planId = $(this).data('id');
            toggleRemark(planId, this);
        });

        $(document).on('click', '.btn-edit-plan', function() {
            const planData = $(this).data('plan');
            editActivityPlan(planData);
        });

        $(document).on('click', '.btn-delete-plan', function() {
            const planId = $(this).data('id');
            deleteActivityPlan(planId);
        });

        $(document).on('click', '.btn-upload-evidence', function() {
            const planId = $(this).data('plan-id');
            const month = $(this).data('month');
            const file = $(this).data('file');
            openUploadEvidenceModal(planId, month, file);
        });

        $('#create_start_month, #create_end_month').on('change', clearQuickPlanSelection);
        // Handle AJAX form submission for Add Activity Plan
        $('#addActivityForm').on('submit', function(e) {
            e.preventDefault();
            const form = this;
            const submitBtn = $('#saveActivityBtn');
            const originalText = submitBtn.html();

            submitBtn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-2"></i> Saving...');

            $.ajax({
                url: form.action,
                type: 'POST',
                data: $(form).serialize(),
                success: function(response) {
                    closeAddActivityModal();
                    if (typeof showToast === 'function') {
                        showToast(response.message || 'Activity Plan saved successfully.', 'success');
                    }
                    setTimeout(function() {
                        window.location.reload();
                    }, 500);
                },
                error: function(xhr) {
                    if (typeof showToast === 'function') {
                        showToast(xhr.responseJSON?.message || 'Failed to save Activity Plan.', 'error');
                    } else {
                        alert(xhr.responseJSON?.message || 'Failed to save Activity Plan.');
                    }
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });
    });

    let deleteTargetId = null;

    function deleteActivityPlan(id) {
        deleteTargetId = id;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        deleteTargetId = null;
        document.getElementById('deleteModal').classList.add('hidden');
    }

    function confirmExecuteDelete() {
        if (!deleteTargetId) return;

        const btn = $('#confirmDeleteBtn');
        const originalText = btn.html();
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-1"></i> Deleting...');

        $.ajax({
            url: "{{ route('kpi.company.activity_plan.delete') }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                id: deleteTargetId
            },
            success: function(response) {
                closeDeleteModal();
                if (typeof showToast === 'function') {
                    showToast(response.message || 'Activity Plan deleted successfully.', 'success');
                }
                setTimeout(function() {
                    window.location.reload();
                }, 500);
            },
            error: function(xhr) {
                if (typeof showToast === 'function') {
                    showToast(xhr.responseJSON?.message || 'Failed to delete item.', 'error');
                } else {
                    alert('Failed to delete item.');
                }
            },
            complete: function() {
                btn.prop('disabled', false).html(originalText);
            }
        });
    }
</script>
@endpush
@endsection
