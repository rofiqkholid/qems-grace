@extends('layouts.app')

@section('title', 'KPI Company Activity Input')

@section('content')
@include('layouts.sidebar')

<!-- Main Content -->
<div class="lg:ml-20 min-h-screen flex flex-col bg-slate-50">
    @include('layouts.header')

    <!-- Page Content -->
    <main class="flex-1 p-6">
        @php
            $isViewMode = request('mode') === 'view';
        @endphp
        <style>
            #closed_status ~ div[x-show="open"],
            #pic_dept ~ div[x-show="open"],
            #follow_up_by ~ div[x-show="open"] {
                bottom: 100% !important;
                top: auto !important;
                margin-bottom: 4px !important;
                margin-top: 0 !important;
            }
        </style>
        <!-- Back Button & Page Title -->
        <div class="mb-6 flex items-center gap-3">
            <a href="javascript:history.back()" class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl shrink-0 flex items-center justify-center bg-white border border-slate-200 hover:bg-slate-50 transition-colors">
                <i class="fa-solid fa-arrow-left text-[11px] sm:text-sm text-slate-600"></i>
            </a>
            <div>
                <h1 class="text-lg sm:text-2xl font-bold text-slate-800">{{ $isViewMode ? 'KPI Company Activity Details' : 'KPI Company Activity Input' }}</h1>
                <p class="text-slate-500 text-sm">{{ $isViewMode ? 'View actual performance, status, and corrective action for this month.' : 'Insert actual performance, status, and corrective action for this month.' }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-6">
            <form action="{{ route('kpi.company.activity.update', $activity->hash_id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Department -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Department</label>
                        <input type="text" value="{{ $activity->department_code }}" disabled class="w-full px-4 py-2.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-500 text-sm outline-none cursor-not-allowed">
                    </div>
                    <!-- KPI Objective -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">KPI Objective</label>
                        <input type="text" value="{{ $activity->objective }}" disabled class="w-full px-4 py-2.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-500 text-sm outline-none cursor-not-allowed">
                    </div>

                    <!-- Operator -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Operator</label>
                        <input type="text" value="{{ $activity->operator }}" disabled class="w-full px-4 py-2.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-500 text-sm outline-none cursor-not-allowed">
                    </div>
                    <!-- Unit -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Unit</label>
                        <input type="text" value="{{ $activity->unit ?? '' }}" disabled class="w-full px-4 py-2.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-500 text-sm outline-none cursor-not-allowed">
                    </div>

                    <!-- Tahun -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Tahun</label>
                        <input type="text" value="{{ $activity->tahun }}" disabled class="w-full px-4 py-2.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-500 text-sm outline-none cursor-not-allowed">
                    </div>
                    <!-- Bulan -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Bulan</label>
                        <input type="text" value="{{ $activity->bulan }}" disabled class="w-full px-4 py-2.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-500 text-sm outline-none cursor-not-allowed">
                    </div>

                    <!-- Target -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Target</label>
                        <input type="text" value="{{ ($activity->operator ?? '') }} {{ ($activity->master_target ?? '') }} {{ ($activity->unit ?? '') }}" disabled class="w-full px-4 py-2.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-500 text-sm outline-none cursor-not-allowed">
                    </div>

                    <!-- Components Inputs -->
                    @if(!empty($components))
                        @foreach($components as $index => $name)
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-slate-700 mb-1.5"><span class="text-slate-400 font-normal">[Component {{ $index }}]</span> {{ $name }} <span class="text-red-500">*</span></label>
                                <input type="text" name="comp_{{ $index }}" value="{{ old('comp_' . $index, $activity->{'comp_' . $index}) }}" required placeholder="Enter {{ $name }}" class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm outline-none transition-all hover:border-blue-300" {{ $isViewMode ? 'disabled' : '' }}>
                            </div>
                        @endforeach
                    @endif

                    @if(!empty($components))
                    <div class="md:col-span-2 flex justify-start pt-2">
                        <button type="button" id="check_result_btn" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                            <i class="fa-solid fa-calculator mr-1.5"></i> Check Result
                        </button>
                    </div>
                    <div class="md:col-span-2 p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <span class="block text-md font-bold text-slate-700">Calculated Actual Result</span>
                            <span class="block text-xs text-slate-500 mt-1">
                                Calculated based on formula: <span class="font-mono px-2 py-0.5 text-slate-600">{{ $activity->calc_operator ?: ($formula->calc_operator ?? 'Sum of all components') }}</span> 
                                Result: <span id="actual_preview_value" class="font-mono bg-white px-2 py-0.5 text-slate-600 font-bold"></span>
                            </span>
                        </div>
                    </div>
                    @endif

                    @if(empty($components))
                    <!-- Actual -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Actual <span class="text-red-500">*</span></label>
                        <input type="text" name="actual" id="actual_input" value="{{ old('actual', $activity->actual) }}" required placeholder="Enter actual value" class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm outline-none transition-all hover:border-blue-300" {{ $isViewMode ? 'disabled' : '' }}>
                    </div>
                    @endif
                </div>

                @php
                    $showProblemSolving = ($activity->status === 'Not Achieved');
                    
                    $unitData = (old('unit') || ($problem && isset($problem->unit))) 
                        ? ['id' => old('unit', $problem->unit ?? ''), 'name' => old('unit', $problem->unit ?? '')]
                        : ['id' => 'Case', 'name' => 'Case'];

                    $closedStatusData = (old('closed_status') || ($problem && isset($problem->closed_status)))
                        ? ['id' => old('closed_status', $problem->closed_status ?? ''), 'name' => old('closed_status', $problem->closed_status ?? '')]
                        : ['id' => 'Open', 'name' => 'Open'];

                    $picId = old('pic_dept', $problem->pic_dept ?? '');
                    $picObj = $picId ? $departments->firstWhere('Key1', $picId) : null;
                    $picName = $picObj ? $picObj->Key1 : $picId;
                    $picData = $picId ? ['id' => $picId, 'name' => $picName] : null;

                    $fupId = old('follow_up_by', $problem->follow_up_by ?? '');
                    $fupObj = $fupId ? $departments->firstWhere('Key1', $fupId) : null;
                    $fupName = $fupObj ? $fupObj->Key1 : $fupId;
                    $fupData = $fupId ? ['id' => $fupId, 'name' => $fupName] : null;

                    $initialSelects = [
                        'unit' => $unitData,
                        'closed_status' => $closedStatusData,
                        'pic_dept' => $picData,
                        'follow_up_by' => $fupData,
                    ];
                @endphp
                <div id="initial-selects-data" class="hidden" data-selects='{!! json_encode($initialSelects) !!}'></div>
                <!-- Prevention and Problem Solving Process Section -->
                <div id="problem-solving-section" class="{{ $showProblemSolving ? '' : 'hidden' }} mt-6 pt-6 border-t border-slate-100 space-y-6">
                    <h2 class="text-lg font-bold text-slate-800">
                        Prevention and Problem Solving Process
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- 1. Problem Description -->
                        <div class="min-w-0">
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">1. Problem Description <span class="text-red-500">*</span></label>
                            <div class="flex flex-col md:flex-row md:items-stretch gap-2 w-full md:max-w-[90%]">
                                <div class="flex-1 min-w-0 flex">
                                    <textarea name="problem_description" placeholder="Describe the problem..." rows="4" class="ps-required w-full block px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm outline-none transition-all resize-none" {{ $isViewMode ? 'disabled' : '' }}>{{ old('problem_description', $problem?->problem_description ?? '') }}</textarea>
                                </div>
                                @if(!$isViewMode)
                                    <div class="shrink-0 flex items-stretch">
                                        <input type="file" id="problem_image" name="problem_image" accept="image/*" class="hidden" onchange="handleFileSelect(this, 'problem_image_preview')">
                                        <button type="button" onclick="document.getElementById('problem_image').click()" class="w-full md:w-auto md:h-full md:aspect-square py-2.5 md:py-0 border border-dashed border-blue-300 bg-blue-50/50 hover:bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center gap-2 md:gap-0 transition-all relative" title="Upload Problem Image">
                                            <i class="fas fa-camera text-sm"></i>
                                            <span class="md:hidden text-xs font-medium">Upload Image</span>
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <div id="problem_image_preview" class="mt-2">
                                @if($problem && $problem?->problem_image)
                                    <div class="relative inline-block group">
                                        <div class="cursor-pointer" onclick="showViewer(this)">
                                            <img src="{{ asset($problem?->problem_image) }}" class="w-16 h-16 object-cover rounded-lg border border-slate-200 hover:border-blue-400 transition-all" alt="Problem Image">
                                        </div>
                                        @if(!$isViewMode)
                                            <button type="button" onclick="clearFileSelect(event, 'problem_image', 'problem_image_preview', 'delete_problem_image')" class="absolute -top-1.5 -right-1.5 bg-red-500 hover:bg-red-600 text-white rounded-full w-5 h-5 flex items-center justify-center shadow-md transition-all z-10" title="Delete Image">
                                                <i class="fas fa-times text-[10px]"></i>
                                            </button>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- 2. Root Cause -->
                        <div class="min-w-0">
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">2. Root Cause <span class="text-red-500">*</span></label>
                            <div class="flex flex-col md:flex-row md:items-stretch gap-2 w-full md:max-w-[90%]">
                                <div class="flex-1 min-w-0 flex">
                                    <textarea name="root_cause" placeholder="Identify the root cause..." rows="4" class="ps-required w-full block px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm outline-none transition-all resize-none" {{ $isViewMode ? 'disabled' : '' }}>{{ old('root_cause', $problem?->root_cause ?? '') }}</textarea>
                                </div>
                                @if(!$isViewMode)
                                    <div class="shrink-0 flex items-stretch">
                                        <input type="file" id="root_cause_image" name="root_cause_image" accept="image/*" class="hidden" onchange="handleFileSelect(this, 'root_cause_image_preview')">
                                        <button type="button" onclick="document.getElementById('root_cause_image').click()" class="w-full md:w-auto md:h-full md:aspect-square py-2.5 md:py-0 border border-dashed border-blue-300 bg-blue-50/50 hover:bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center gap-2 md:gap-0 transition-all relative" title="Upload Root Cause Image">
                                            <i class="fas fa-camera text-sm"></i>
                                            <span class="md:hidden text-xs font-medium">Upload Image</span>
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <div id="root_cause_image_preview" class="mt-2">
                                @if($problem && $problem?->root_cause_image)
                                    <div class="relative inline-block group">
                                        <div class="cursor-pointer" onclick="showViewer(this)">
                                            <img src="{{ asset($problem?->root_cause_image) }}" class="w-16 h-16 object-cover rounded-lg border border-slate-200 hover:border-blue-400 transition-all" alt="Root Cause Image">
                                        </div>
                                        @if(!$isViewMode)
                                            <button type="button" onclick="clearFileSelect(event, 'root_cause_image', 'root_cause_image_preview', 'delete_root_cause_image')" class="absolute -top-1.5 -right-1.5 bg-red-500 hover:bg-red-600 text-white rounded-full w-5 h-5 flex items-center justify-center shadow-md transition-all z-10" title="Delete Image">
                                                <i class="fas fa-times text-[10px]"></i>
                                            </button>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Left Card: Problem Cause 5M + E -->
                        <div class="p-5 border border-slate-200 bg-slate-50/50 rounded-xl space-y-4">
                            <h3 class="text-sm font-semibold text-slate-800 flex items-center gap-1.5">
                                <i class="fa-solid fa-diagram-project"></i> Problem Cause 5M + E
                            </h3>
                            
                            <div class="relative z-40">
                                <label class="block text-xs font-medium text-slate-500 mb-1">Unit</label>
                                @php
                                    $unitOptions = [
                                        ['id' => 'Case', 'name' => 'Case'],
                                        ['id' => 'Percent', 'name' => 'Percent']
                                    ];
                                @endphp
                                <x-searchable-select
                                    name="unit"
                                    id="unit"
                                    label="Unit"
                                    :initialOptions="$unitOptions"
                                    updateEvent="update-unit"
                                    hideLabel="true"
                                    :disabled="$isViewMode"
                                />
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 mb-1">Machine</label>
                                    <input type="text" name="machine" value="{{ old('machine', $problem?->machine ?? '') }}" placeholder="Machine cause" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm outline-none focus:border-blue-500" {{ $isViewMode ? 'disabled' : '' }}>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 mb-1">Material</label>
                                    <input type="text" name="material" value="{{ old('material', $problem?->material ?? '') }}" placeholder="Material cause" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm outline-none focus:border-blue-500" {{ $isViewMode ? 'disabled' : '' }}>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 mb-1">Man</label>
                                    <input type="text" name="man" value="{{ old('man', $problem?->man ?? '') }}" placeholder="Man cause" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm outline-none focus:border-blue-500" {{ $isViewMode ? 'disabled' : '' }}>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 mb-1">Method</label>
                                    <input type="text" name="method" value="{{ old('method', $problem?->method ?? '') }}" placeholder="Method cause" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm outline-none focus:border-blue-500" {{ $isViewMode ? 'disabled' : '' }}>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 mb-1">Money</label>
                                    <input type="text" name="money" value="{{ old('money', $problem?->money ?? '') }}" placeholder="Money cause" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm outline-none focus:border-blue-500" {{ $isViewMode ? 'disabled' : '' }}>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 mb-1">Environment</label>
                                    <input type="text" name="environment" value="{{ old('environment', $problem?->environment ?? '') }}" placeholder="Environment cause" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm outline-none focus:border-blue-500" {{ $isViewMode ? 'disabled' : '' }}>
                                </div>
                            </div>
                        </div>

                        <!-- Right Side Action steps -->
                        <div class="space-y-4">
                            <!-- 3. Temporary Action -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">3. Temporary Action <span class="text-red-500">*</span></label>
                                <div class="flex flex-col md:flex-row md:items-stretch gap-2 w-full md:max-w-[90%]">
                                    <div class="flex-1 min-w-0 flex">
                                        <textarea name="temporary_action" placeholder="Temporary fix..." rows="4" class="ps-required w-full block px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm outline-none transition-all resize-none" {{ $isViewMode ? 'disabled' : '' }}>{{ old('temporary_action', $problem?->temporary_action ?? '') }}</textarea>
                                    </div>
                                    @if(!$isViewMode)
                                        <div class="shrink-0 flex items-stretch">
                                            <input type="file" id="temporary_action_image" name="temporary_action_image" accept="image/*" class="hidden" onchange="handleFileSelect(this, 'temporary_action_image_preview')">
                                            <button type="button" onclick="document.getElementById('temporary_action_image').click()" class="w-full md:w-auto md:h-full md:aspect-square py-2.5 md:py-0 border border-dashed border-blue-300 bg-blue-50/50 hover:bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center gap-2 md:gap-0 transition-all relative" title="Upload Temporary Action Image">
                                                <i class="fas fa-camera text-sm"></i>
                                                <span class="md:hidden text-xs font-medium">Upload Image</span>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                <div id="temporary_action_image_preview" class="mt-2">
                                    @if($problem && $problem?->temporary_action_image)
                                        <div class="relative inline-block group">
                                            <div class="cursor-pointer" onclick="showViewer(this)">
                                                <img src="{{ asset($problem?->temporary_action_image) }}" class="w-16 h-16 object-cover rounded-lg border border-slate-200 hover:border-blue-400 transition-all" alt="Temporary Action Image">
                                            </div>
                                            @if(!$isViewMode)
                                                <button type="button" onclick="clearFileSelect(event, 'temporary_action_image', 'temporary_action_image_preview', 'delete_temporary_action_image')" class="absolute -top-1.5 -right-1.5 bg-red-500 hover:bg-red-600 text-white rounded-full w-5 h-5 flex items-center justify-center shadow-md transition-all z-10" title="Delete Image">
                                                    <i class="fas fa-times text-[10px]"></i>
                                                </button>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- 4. Permanent Action -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">4. Permanent Action <span class="text-red-500">*</span></label>
                                <div class="flex flex-col md:flex-row md:items-stretch gap-2 w-full md:max-w-[90%]">
                                    <div class="flex-1 min-w-0 flex">
                                        <textarea name="permanent_action" placeholder="Permanent solution..." rows="4" class="ps-required w-full block px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm outline-none transition-all resize-none" {{ $isViewMode ? 'disabled' : '' }}>{{ old('permanent_action', $problem?->permanent_action ?? '') }}</textarea>
                                    </div>
                                    @if(!$isViewMode)
                                        <div class="shrink-0 flex items-stretch">
                                            <input type="file" id="permanent_action_image" name="permanent_action_image" accept="image/*" class="hidden" onchange="handleFileSelect(this, 'permanent_action_image_preview')">
                                            <button type="button" onclick="document.getElementById('permanent_action_image').click()" class="w-full md:w-auto md:h-full md:aspect-square py-2.5 md:py-0 border border-dashed border-blue-300 bg-blue-50/50 hover:bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center gap-2 md:gap-0 transition-all relative" title="Upload Permanent Action Image">
                                                <i class="fas fa-camera text-sm"></i>
                                                <span class="md:hidden text-xs font-medium">Upload Image</span>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                <div id="permanent_action_image_preview" class="mt-2">
                                    @if($problem && $problem?->permanent_action_image)
                                        <div class="relative inline-block group">
                                            <div class="cursor-pointer" onclick="showViewer(this)">
                                                <img src="{{ asset($problem?->permanent_action_image) }}" class="w-16 h-16 object-cover rounded-lg border border-slate-200 hover:border-blue-400 transition-all" alt="Permanent Action Image">
                                            </div>
                                            @if(!$isViewMode)
                                                <button type="button" onclick="clearFileSelect(event, 'permanent_action_image', 'permanent_action_image_preview', 'delete_permanent_action_image')" class="absolute -top-1.5 -right-1.5 bg-red-500 hover:bg-red-600 text-white rounded-full w-5 h-5 flex items-center justify-center shadow-md transition-all z-10" title="Delete Image">
                                                    <i class="fas fa-times text-[10px]"></i>
                                                </button>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Date & Dropdowns Section (1 Column width span-2) -->
                        <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t border-slate-100">
                            <!-- Start Date -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Start Date <span class="text-red-500">*</span></label>
                                <input type="date" name="start_date" value="{{ old('start_date', $problem?->start_date ?? '') }}" class="ps-required w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm outline-none transition-all" {{ $isViewMode ? 'disabled' : '' }}>
                            </div>
                            <!-- Finish Date -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Finish Date <span class="text-red-500">*</span></label>
                                <input type="date" name="finish_date" value="{{ old('finish_date', $problem?->finish_date ?? '') }}" class="ps-required w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm outline-none transition-all" {{ $isViewMode ? 'disabled' : '' }}>
                            </div>
                            <!-- Closed Status -->
                            <div class="relative z-30">
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Closed Status <span class="text-red-500">*</span></label>
                                @php
                                    $statusOptions = [
                                        ['id' => 'Open', 'name' => 'Open'],
                                        ['id' => 'Closed', 'name' => 'Closed']
                                    ];
                                @endphp
                                <x-searchable-select
                                    name="closed_status"
                                    id="closed_status"
                                    label="Closed Status"
                                    :initialOptions="$statusOptions"
                                    updateEvent="update-closed-status"
                                    hideLabel="true"
                                    :disabled="$isViewMode"
                                />
                            </div>

                            <!-- PIC Dept -->
                            <div class="relative z-20">
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">PIC Dept <span class="text-red-500">*</span></label>
                                @php
                                    $deptOptions = $departments->map(fn($d) => ['id' => $d->Key1, 'name' => $d->Key1])->toArray();
                                @endphp
                                <x-searchable-select
                                    name="pic_dept"
                                    id="pic_dept"
                                    label="PIC Dept"
                                    apiUrl="{{ route('kpi.company.departments') }}"
                                    :initialOptions="$deptOptions"
                                    updateEvent="update-pic-dept"
                                    hideLabel="true"
                                    :disabled="$isViewMode"
                                />
                            </div>
                            <!-- Follow Up By -->
                            <div class="relative z-10">
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Follow Up By <span class="text-red-500">*</span></label>
                                <x-searchable-select
                                    name="follow_up_by"
                                    id="follow_up_by"
                                    label="Follow Up By"
                                    apiUrl="{{ route('kpi.company.departments') }}"
                                    :initialOptions="$deptOptions"
                                    updateEvent="update-follow-up-by"
                                    hideLabel="true"
                                    :disabled="$isViewMode"
                                />
                            </div>
                            <!-- Evidence -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Evidence</label>
                                <input type="file" id="evidence" name="evidence" onchange="handleFileSelect(this, 'evidence_preview')" class="w-full px-4 py-2 border border-slate-200 rounded-lg text-sm file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200" {{ $isViewMode ? 'disabled' : '' }}>
                                <div id="evidence_preview" class="mt-2">
                                    @if($problem && $problem?->evidence)
                                        @php
                                            $ext = strtolower(pathinfo($problem->evidence, PATHINFO_EXTENSION));
                                            $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
                                        @endphp
                                        @if($isImage)
                                            <div class="relative inline-block group">
                                                <div class="cursor-pointer" onclick="showViewer(this)">
                                                    <img src="{{ asset($problem->evidence) }}" class="w-16 h-16 object-cover rounded-lg border border-slate-200 hover:border-blue-400 transition-all" alt="Evidence Image">
                                                </div>
                                                @if(!$isViewMode)
                                                    <button type="button" onclick="clearFileSelect(event, 'evidence', 'evidence_preview', 'delete_evidence')" class="absolute -top-1.5 -right-1.5 bg-red-500 hover:bg-red-600 text-white rounded-full w-5 h-5 flex items-center justify-center shadow-md transition-all z-10" title="Delete Image">
                                                        <i class="fas fa-times text-[10px]"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        @else
                                            <div class="relative inline-flex items-center gap-1.5 text-xs text-slate-500 mt-1">
                                                <i class="fa-solid fa-circle-check text-green-500"></i> Current Evidence: <a href="{{ asset($problem->evidence) }}" target="_blank" class="text-blue-500 hover:underline">View Uploaded File</a>
                                                @if(!$isViewMode)
                                                    <button type="button" onclick="clearFileSelect(event, 'evidence', 'evidence_preview', 'delete_evidence')" class="text-red-500 hover:text-red-700 ml-1" title="Delete File">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                    @if($isViewMode)
                        <a href="{{ route('kpi.company.detail', $activity->kpi_company_hash_id) }}" class="px-5 py-2.5 bg-white text-slate-700 border border-slate-300 rounded-lg hover:bg-slate-50 font-medium text-sm transition-colors">
                            Back
                        </a>
                    @else
                        <a href="{{ route('kpi.company.activity.cancel', $activity->hash_id) }}" class="px-5 py-2.5 bg-white text-slate-700 border border-slate-300 rounded-lg hover:bg-slate-50 font-medium text-sm transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm transition-colors shadow-sm shadow-blue-200">
                            Save Changes
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </main>

    @include('layouts.footer')
</div>
@endsection

@push('scripts')
<script>
    function handleFileSelect(input, previewId) {
        const previewDiv = document.getElementById(previewId);
        if (previewDiv) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const fileUrl = URL.createObjectURL(file);
                const deleteInputName = 'delete_' + input.id;
                
                // Clear delete flag if user re-selects a file
                const existingDeleteInput = document.querySelector(`input[name="${deleteInputName}"]`);
                if (existingDeleteInput) {
                    existingDeleteInput.remove();
                }
                
                const isImage = file.type.startsWith('image/');
                if (isImage) {
                    previewDiv.innerHTML = `
                        <div class="relative inline-block group">
                            <div class="cursor-pointer" onclick="showViewer(this)">
                                <img src="${fileUrl}" class="w-16 h-16 object-cover rounded-lg border border-slate-200 hover:border-blue-400 transition-all" alt="Preview Image">
                            </div>
                            <button type="button" onclick="clearFileSelect(event, '${input.id}', '${previewId}', '${deleteInputName}')" class="absolute -top-1.5 -right-1.5 bg-red-500 hover:bg-red-600 text-white rounded-full w-5 h-5 flex items-center justify-center shadow-md transition-all z-10" title="Cancel Selection">
                                <i class="fas fa-times text-[10px]"></i>
                            </button>
                        </div>
                    `;
                } else {
                    previewDiv.innerHTML = `
                        <div class="relative inline-flex items-center gap-1.5 text-xs text-slate-500 mt-1">
                            <i class="fa-solid fa-circle-check text-green-500"></i> Selected: <span class="font-medium text-slate-700">${file.name}</span>
                            <button type="button" onclick="clearFileSelect(event, '${input.id}', '${previewId}', '${deleteInputName}')" class="text-red-500 hover:text-red-700 ml-1" title="Cancel Selection">
                                <i class="fas fa-trash-alt text-[10px]"></i>
                            </button>
                        </div>
                    `;
                }
            } else {
                previewDiv.innerHTML = '';
            }
        }
    }

    function clearFileSelect(event, inputId, previewId, deleteInputName = '') {
        if (event) event.stopPropagation();
        
        const input = document.getElementById(inputId);
        if (input) {
            input.value = '';
        }
        
        const previewDiv = document.getElementById(previewId);
        if (previewDiv) {
            previewDiv.innerHTML = '';
        }
        
        if (deleteInputName) {
            const form = document.querySelector('form');
            if (form) {
                const existingDeleteInput = form.querySelector(`input[name="${deleteInputName}"]`);
                if (existingDeleteInput) {
                    existingDeleteInput.remove();
                }
                const deleteInput = document.createElement('input');
                deleteInput.type = 'hidden';
                deleteInput.name = deleteInputName;
                deleteInput.value = '1';
                form.appendChild(deleteInput);
            }
        }
    }

    function showViewer(el) {
        const img = el.querySelector('img');
        if (img && typeof Viewer !== 'undefined') {
            if (img.viewer) {
                img.viewer.show();
            } else {
                img.viewer = new Viewer(img, {
                    navbar: false,
                    title: false,
                    toolbar: {
                        zoomIn: 4,
                        zoomOut: 4,
                        oneToOne: 4,
                        reset: 4,
                        prev: false,
                        play: false,
                        next: false,
                        rotateLeft: 4,
                        rotateRight: 4,
                        flipHorizontal: 4,
                        flipVertical: 4,
                    },
                });
                img.viewer.show();
            }
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        const problemSolvingSection = document.getElementById('problem-solving-section');
        const actualInput = document.getElementById('actual_input');

        function updateProblemSolvingVisibility() {
            if (!problemSolvingSection) return;
            
            function parseLocalNumber(valStr) {
                if (!valStr) return 0;
                let clean = valStr.replace(/\./g, '');
                clean = clean.replace(/,/g, '.');
                return parseFloat(clean) || 0;
            }
            
            const operator = '{!! html_entity_decode($activity->operator) !!}';
            const targetVal = parseFloat('{{ filter_var($activity->master_target, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) }}') || 0;
            const calcOperator = '{{ $activity->calc_operator ?: ($formula->calc_operator ?? "") }}';
            const bulan = '{{ $activity->bulan }}';
            const rawUnit = '{{ $activity->unit ?? "" }}'.trim();
            const isNumberUnit = ['number', 'num'].includes(rawUnit.toLowerCase());
            const unitStr = isNumberUnit ? '' : rawUnit;

            let actualVal = 0;
            let hasActual = false;

            if (actualInput) {
                const valStr = actualInput.value.trim();
                if (valStr !== '') {
                    actualVal = parseLocalNumber(valStr);
                    hasActual = true;
                }
            } else if (calcOperator) {
                if (calcOperator.includes('[')) {
                    let expr = calcOperator;
                    // Auto-prefix master style [comp_X] with current month name
                    expr = expr.replace(/\[(comp_\d+)\]/g, '[' + bulan + '.$1]');
                    
                    const regex = /\[([A-Za-z]{3})\.(comp_\d+)\]/g;
                    let match;
                    let missingComponent = false;
                    let countComponentsInExpr = 0;

                    const tempExpr = expr;
                    regex.lastIndex = 0;
                    while ((match = regex.exec(tempExpr)) !== null) {
                        const mName = match[1];
                        const cCol = match[2];
                        
                        if (mName === bulan) {
                            const inputEl = document.querySelector(`input[name="${cCol}"]`);
                            const valStr = inputEl ? inputEl.value.trim() : '';
                            if (valStr !== '') {
                                const val = parseLocalNumber(valStr);
                                expr = expr.replace(match[0], isNaN(val) ? 0 : val);
                                countComponentsInExpr++;
                            } else {
                                missingComponent = true;
                            }
                        } else {
                            expr = expr.replace(match[0], 0);
                        }
                    }
                    
                    if (!missingComponent) {
                        expr = expr.replace(/x/gi, '*');
                        const exprClean = expr.replace(/[^0-9\+\-\*\/\(\)\.\s]/g, '');
                        try {
                            actualVal = eval(exprClean);
                            const isPercentUnit = (unitStr === '%' || unitStr === 'Percent' || unitStr === 'persen' || unitStr === 'Persen');
                            if (isPercentUnit && exprClean.includes('*') && !exprClean.includes('/') && countComponentsInExpr > 1) {
                                actualVal = actualVal / Math.pow(100, countComponentsInExpr - 1);
                            }
                            hasActual = true;
                        } catch (e) {
                            hasActual = false;
                        }
                    }
                } else {
                    const compInputs = document.querySelectorAll('input[name^="comp_"]');
                    let vals = [];
                    compInputs.forEach(input => {
                        const valStr = input.value.trim();
                        if (valStr !== '') {
                            vals.push(parseLocalNumber(valStr));
                        }
                    });
                    if (vals.length > 0) {
                        const op = calcOperator.trim();
                        const isPercentUnit = (unitStr === '%' || unitStr === 'Percent' || unitStr === 'persen' || unitStr === 'Persen');
                        if (op === '+') {
                            actualVal = vals.reduce((a, b) => a + b, 0);
                        } else if (op === '-') {
                            actualVal = vals.slice(1).reduce((a, b) => a - b, vals[0]);
                        } else if (op === 'x' || op === '*') {
                            actualVal = vals.reduce((a, b) => a * b, 1);
                            if (isPercentUnit && vals.length > 1) {
                                actualVal = actualVal / Math.pow(100, vals.length - 1);
                            }
                        } else if (op === '/') {
                            actualVal = vals.slice(1).reduce((a, b) => b !== 0 ? a / b : 0, vals[0]);
                        } else if (op === 'Average') {
                            actualVal = vals.reduce((a, b) => a + b, 0) / vals.length;
                        } else {
                            actualVal = vals.reduce((a, b) => a + b, 0);
                        }
                        hasActual = true;
                    }
                }
            } else if (!actualInput) {
                // Fallback: sum of all components
                const compInputs = document.querySelectorAll('input[name^="comp_"]');
                let sum = 0;
                let hasAny = false;
                compInputs.forEach(input => {
                    const valStr = input.value.trim();
                    if (valStr !== '') {
                        sum += parseLocalNumber(valStr);
                        hasAny = true;
                    }
                });
                if (hasAny) {
                    actualVal = sum;
                    hasActual = true;
                }
            }

            const previewValEl = document.getElementById('actual_preview_value');
            if (previewValEl) {
                if (hasActual) {
                    const formatted = (actualVal % 1 === 0) 
                        ? actualVal.toLocaleString('id-ID') 
                        : actualVal.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    previewValEl.textContent = unitStr ? `${formatted} ${unitStr}` : formatted;
                } else {
                    previewValEl.textContent = '';
                }
            }

            let isAchieved = false;
            if (hasActual) {
                switch (operator) {
                    case '>=': isAchieved = (actualVal >= targetVal); break;
                    case '<=': isAchieved = (actualVal <= targetVal); break;
                    case '>':  isAchieved = (actualVal > targetVal); break;
                    case '<':  isAchieved = (actualVal < targetVal); break;
                    case '=':
                    default:   isAchieved = (actualVal == targetVal); break;
                }
            }

            const show = hasActual && !isAchieved;

            if (show) {
                problemSolvingSection.classList.remove('hidden');
                problemSolvingSection.querySelectorAll('.ps-required').forEach(el => {
                    el.setAttribute('required', 'required');
                });
                problemSolvingSection.querySelectorAll('input[type="text"]').forEach(el => {
                    if (el.placeholder && el.placeholder.includes('Select')) {
                        el.setAttribute('required', 'required');
                    }
                });
            } else {
                problemSolvingSection.classList.add('hidden');
                problemSolvingSection.querySelectorAll('.ps-required').forEach(el => {
                    el.removeAttribute('required');
                });
                problemSolvingSection.querySelectorAll('input[type="text"]').forEach(el => {
                    if (el.placeholder && el.placeholder.includes('Select')) {
                        el.removeAttribute('required');
                    }
                });
            }
        }

        if (actualInput) {
            actualInput.addEventListener('input', updateProblemSolvingVisibility);
            actualInput.addEventListener('change', updateProblemSolvingVisibility);
        } else {
            const checkBtn = document.getElementById('check_result_btn');
            if (checkBtn) {
                checkBtn.addEventListener('click', updateProblemSolvingVisibility);
            }
            const compInputs = document.querySelectorAll('input[name^="comp_"]');
            compInputs.forEach(input => {
                input.addEventListener('input', () => {
                    const previewValEl = document.getElementById('actual_preview_value');
                    if (previewValEl) {
                        previewValEl.textContent = '-';
                    }
                    if (problemSolvingSection) {
                        problemSolvingSection.classList.add('hidden');
                        problemSolvingSection.querySelectorAll('.ps-required').forEach(el => {
                            el.removeAttribute('required');
                        });
                        problemSolvingSection.querySelectorAll('input[type="text"]').forEach(el => {
                            if (el.placeholder && el.placeholder.includes('Select')) {
                                el.removeAttribute('required');
                            }
                        });
                    }
                });
            });
        }

        // Initialize state on page load
        updateProblemSolvingVisibility();

        // Initialize searchable-select components values
        const selectsContainer = document.getElementById('initial-selects-data');
        if (selectsContainer) {
            const data = JSON.parse(selectsContainer.dataset.selects || '{}');
            
            if (data.unit) {
                window.dispatchEvent(new CustomEvent('update-unit', { detail: data.unit }));
            }
            if (data.closed_status) {
                window.dispatchEvent(new CustomEvent('update-closed-status', { detail: data.closed_status }));
            }
            if (data.pic_dept) {
                window.dispatchEvent(new CustomEvent('update-pic-dept', { detail: data.pic_dept }));
            }
            if (data.follow_up_by) {
                window.dispatchEvent(new CustomEvent('update-follow-up-by', { detail: data.follow_up_by }));
            }
        }
    });
</script>
@endpush
