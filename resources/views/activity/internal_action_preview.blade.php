@extends('layouts.app')

@php
    $hideCentralToast = true;
@endphp

@section('title', 'Action Report Preview')

@section('content')
@include('layouts.sidebar')
@include('components.toast')

<div class="lg:ml-20 min-h-screen flex flex-col bg-slate-50">
    @include('layouts.header')

    <main class="flex-1 p-3 sm:p-6">
        <!-- Page Title & Back Button -->
        <div class="mb-4 sm:mb-6 flex items-center gap-3 sm:gap-4">
            @php
                $backUrl = route('internal_audit.action_report');
                $previousUrl = url()->previous();
                if ($previousUrl && (str_contains($previousUrl, 'verification') || str_contains($previousUrl, 'verifkasi') || str_contains($previousUrl, 'verifikasi'))) {
                    $backUrl = route('internal_audit.verification');
                }
            @endphp
            <a href="{{ $backUrl }}"
                class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-blue-50 border border-blue-200 text-blue-600 hover:bg-blue-100 hover:text-blue-700 transition-all duration-200">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-700">Action Report Preview</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Detailed overview of CAR Action Report finding</p>
            </div>
        </div>

        <!-- Combined Details Card -->
        <div class="bg-white rounded-lg border border-slate-200 p-4 sm:p-8 space-y-8">
            
            <!-- Section 1: Finding General Information -->
            <div>
                <h2 class="text-lg font-bold text-slate-800 mb-5 pb-2 border-b border-slate-100">
                    Finding Information
                </h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-6">
                    <!-- CAR Number -->
                    <div class="flex flex-col gap-1 sm:gap-1.5">
                        <label class="text-slate-500 text-[10px] sm:text-xs tracking-wider">CAR / Request Number</label>
                        <div class="bg-slate-50 border border-slate-200 rounded-lg px-2 sm:px-4 py-1.5 sm:py-[9px] text-slate-600 font-semibold text-[11px] sm:text-sm truncate">
                            {{ $car->req_number ?? '-' }}
                        </div>
                    </div>

                    <!-- Finding Category -->
                    <div class="flex flex-col gap-1 sm:gap-1.5">
                        <label class="text-slate-500 text-[10px] sm:text-xs tracking-wider">Finding Category</label>
                        <div class="bg-slate-50 border border-slate-200 rounded-lg px-2 sm:px-4 py-1.5 sm:py-[9px] text-slate-600 font-semibold text-[11px] sm:text-sm truncate">
                            {{ $car->finding_category ?? 'OFI' }}
                        </div>
                    </div>

                    <!-- Department -->
                    <div class="flex flex-col gap-1 sm:gap-1.5">
                        <label class="text-slate-500 text-[10px] sm:text-xs tracking-wider">Auditee Department</label>
                        <div class="bg-slate-50 border border-slate-200 rounded-lg px-2 sm:px-4 py-1.5 sm:py-[9px] text-slate-600 font-semibold text-[11px] sm:text-sm truncate">
                            {{ $car->department ?? '-' }}
                        </div>
                    </div>

                    <!-- Date Created -->
                    <div class="flex flex-col gap-1 sm:gap-1.5">
                        <label class="text-slate-500 text-[10px] sm:text-xs tracking-wider">Report Date</label>
                        <div class="bg-slate-50 border border-slate-200 rounded-lg px-2 sm:px-4 py-1.5 sm:py-[9px] text-slate-600 font-semibold text-[11px] sm:text-sm truncate">
                            {{ $car->formatted_date }}
                        </div>
                    </div>

                    <!-- Auditor -->
                    <div class="flex flex-col gap-1 sm:gap-1.5">
                        <label class="text-slate-500 text-[10px] sm:text-xs tracking-wider">Auditor</label>
                        <div class="bg-slate-50 border border-slate-200 rounded-lg px-2 sm:px-4 py-1.5 sm:py-[9px] text-slate-600 font-semibold text-[11px] sm:text-sm truncate">
                            {{ $car->auditor ?? '-' }}
                        </div>
                    </div>

                    <!-- Due Date -->
                    <div class="flex flex-col gap-1 sm:gap-1.5">
                        <label class="text-slate-500 text-[10px] sm:text-xs tracking-wider">Due Date</label>
                        <div class="bg-slate-50 border border-slate-200 rounded-lg px-2 sm:px-4 py-1.5 sm:py-[9px] text-slate-600 font-semibold text-[11px] sm:text-sm">
                            {{ $car->due_date ? \Carbon\Carbon::parse($car->due_date)->format('d M Y') : '-' }}
                        </div>
                    </div>

                    <!-- Surveillance -->
                    <div class="flex flex-col gap-1 sm:gap-1.5">
                        <label class="text-slate-500 text-[10px] sm:text-xs tracking-wider">Surveillance</label>
                        <div class="bg-slate-50 border border-slate-200 rounded-lg px-2.5 sm:px-4 py-1.5 sm:py-[9px] text-slate-600 font-semibold text-[11px] sm:text-sm truncate min-h-[32px] sm:min-h-[40px]">
                            {{ $car->surveillance ?? '' }}
                        </div>
                    </div>

                    <!-- External -->
                    <div class="flex flex-col gap-1 sm:gap-1.5">
                        <label class="text-slate-500 text-[10px] sm:text-xs tracking-wider">External</label>
                        <div class="bg-slate-50 border border-slate-200 rounded-lg px-2.5 sm:px-4 py-1.5 sm:py-[9px] text-slate-600 font-semibold text-[11px] sm:text-sm truncate min-h-[32px] sm:min-h-[40px]">
                            {{ $car->external ?? '' }}
                        </div>
                    </div>

                    <!-- Internal Audit -->
                    <div class="flex flex-col gap-1 sm:gap-1.5">
                        <label class="text-slate-500 text-[10px] sm:text-xs tracking-wider">Internal Audit</label>
                        <div class="bg-slate-50 border border-slate-200 rounded-lg px-2.5 sm:px-4 py-1.5 sm:py-[9px] text-slate-600 font-semibold text-[11px] sm:text-sm truncate min-h-[32px] sm:min-h-[40px]">
                            {{ !empty($car->internal_audit) ? $car->internal_audit : (empty($car->schedule_hash_id) ? 'Header Deleted' : ($car->audit_type ?? '')) }}
                        </div>
                    </div>
                </div>
            </div>
            <!-- Section 2: Requirement & Clause Standards -->
            <div class="border-t border-slate-100 pt-6">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-6">
                    <!-- Requirement No -->
                    <div class="flex flex-col gap-1 sm:gap-1.5">
                        <label class="text-slate-500 text-[10px] sm:text-xs tracking-wider">Requirement No.</label>
                        <div class="bg-slate-50 border border-slate-200 rounded-lg px-2 sm:px-4 py-1.5 sm:py-[9px] text-slate-800 text-[11px] sm:text-sm">
                            {{ $car->requirement_no ?? '-' }}
                        </div>
                    </div>
                    <!-- Clause Title -->
                    <div class="flex flex-col gap-1 sm:gap-1.5">
                        <label class="text-slate-500 text-[10px] sm:text-xs tracking-wider">Clause Title</label>
                        <div class="bg-slate-50 border border-slate-200 rounded-lg px-2 sm:px-4 py-1.5 sm:py-[9px] text-slate-800 text-[11px] sm:text-sm">
                            {{ $car->clause_title ?? '-' }}
                        </div>
                    </div>
                    <!-- Auditee -->
                    <div class="flex flex-col gap-1 sm:gap-1.5">
                        <label class="text-slate-500 text-[10px] sm:text-xs tracking-wider">Auditee</label>
                        <div class="bg-slate-50 border border-slate-200 rounded-lg px-2 sm:px-4 py-1.5 sm:py-[9px] text-slate-800 text-[11px] sm:text-sm break-words whitespace-normal" title="{{ $car->header_auditee ?? $car->auditee ?? '-' }}">
                            {{ $car->header_auditee ?? $car->auditee ?? '-' }}
                        </div>
                    </div>
                    <!-- Check Item -->
                    <div class="flex flex-col gap-1 sm:gap-1.5 col-span-2 sm:col-span-3">
                        <label class="text-slate-500 text-[10px] sm:text-xs tracking-wider">Check Item</label>
                        <div class="bg-slate-50 border border-slate-200 rounded-lg px-2 sm:px-4 py-1.5 sm:py-[9px] text-slate-800 text-[11px] sm:text-sm leading-relaxed">
                            {{ $car->check_item ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Clause Description & Finding Evidence Details side-by-side -->
            <div class="border-t border-slate-100 pt-6">
                <div class="grid grid-cols-2 gap-3 sm:gap-6">
                    <!-- Clause Description -->
                    <div class="flex flex-col gap-1 sm:gap-1.5">
                        <label class="text-slate-500 text-[10px] sm:text-xs tracking-wider">Clause Description / Klausul</label>
                        <div class="bg-slate-50 border border-slate-200 rounded-lg px-2 sm:px-4 py-1.5 sm:py-[9px] text-slate-800 text-[11px] sm:text-sm leading-relaxed h-full">
                            {{ $car->clause_text ?? '-' }}
                        </div>
                    </div>
                    <!-- Finding Evidence Details -->
                    <div class="flex flex-col gap-1 sm:gap-1.5">
                        <label class="text-slate-500 text-[10px] sm:text-xs tracking-wider">Finding Evidence Details</label>
                        <div class="bg-slate-50 border border-slate-200 rounded-lg px-2 sm:px-4 py-1.5 sm:py-[9px] text-slate-800 text-[11px] sm:text-sm leading-relaxed h-full">
                            {{ $car->finding ?? '-' }}
                        </div>
                        @if(!empty($car->finding_file_path))
                            <div class="mt-2 flex flex-wrap gap-3">
                                <div id="finding_images_container" class="flex flex-wrap gap-2">
                                    @foreach(explode(',', $car->finding_file_path) as $path)
                                        @if(!empty(trim($path)))
                                            <img src="{{ asset(trim($path)) }}" class="w-16 h-16 object-cover rounded-lg border border-slate-200 cursor-pointer hover:opacity-90 transition">
                                        @endif
                                    @endforeach
                                </div>
                                <span class="text-[10px] sm:text-xs text-slate-400 italic whitespace-nowrap mt-2"><i class="fa-solid fa-magnifying-glass-plus mr-1"></i>Click to zoom / preview</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Plan Form Card -->
        <form id="actionPlanForm" action="{{ route('internal_audit.action_report.save_action', request()->route('id')) }}" method="POST" enctype="multipart/form-data" class="mt-6">
            @csrf
            @php $isComplete = isset($action) && $action->action_status === 'complete'; @endphp
            <div class="bg-white rounded-lg border border-slate-200 p-4 sm:p-8 space-y-8">
                <div>
                    <h2 class="text-lg font-bold text-slate-800 mb-5 pb-2 border-b border-slate-100">
                        Action Plan & Analysis
                    </h2>
                    
                    <div class="space-y-6">
                        <!-- Row 1: Why 1 & Why 5 -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-slate-700 font-semibold text-xs tracking-wider">Why 1 <span class="text-red-500">*</span></label>
                                <textarea name="why_one" required rows="1" {{ $isComplete ? 'readonly' : '' }} class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none text-slate-700 resize-none overflow-hidden autogrow-textarea {{ $isComplete ? 'bg-slate-50 text-slate-500 cursor-not-allowed' : '' }}" placeholder="Enter Why 1...">{{ old('why_one', $action->why_one ?? '') }}</textarea>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-slate-700 font-semibold text-xs tracking-wider">Why 5</label>
                                <textarea name="why_five" rows="1" {{ $isComplete ? 'readonly' : '' }} class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none text-slate-700 resize-none overflow-hidden autogrow-textarea {{ $isComplete ? 'bg-slate-50 text-slate-500 cursor-not-allowed' : '' }}" placeholder="Enter Why 5 (Optional)...">{{ old('why_five', $action->why_five ?? '') }}</textarea>
                            </div>
                        </div>

                        <!-- Row 2: Why 2 & 3 (Left) and Root Cause (Right) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Left: Why 2 & Why 3 stacked -->
                            <div class="space-y-4 flex flex-col justify-between">
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-slate-700 font-semibold text-xs tracking-wider">Why 2 <span class="text-red-500">*</span></label>
                                    <textarea name="why_two" required rows="1" {{ $isComplete ? 'readonly' : '' }} class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none text-slate-700 resize-none overflow-hidden autogrow-textarea {{ $isComplete ? 'bg-slate-50 text-slate-500 cursor-not-allowed' : '' }}" placeholder="Enter Why 2...">{{ old('why_two', $action->why_two ?? '') }}</textarea>
                                </div>
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-slate-700 font-semibold text-xs tracking-wider">Why 3 <span class="text-red-500">*</span></label>
                                    <textarea name="why_three" required rows="1" {{ $isComplete ? 'readonly' : '' }} class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none text-slate-700 resize-none overflow-hidden autogrow-textarea {{ $isComplete ? 'bg-slate-50 text-slate-500 cursor-not-allowed' : '' }}" placeholder="Enter Why 3...">{{ old('why_three', $action->why_three ?? '') }}</textarea>
                                </div>
                            </div>
                            <!-- Right: Root Cause -->
                            <div class="flex flex-col gap-1.5 justify-between">
                                <label class="text-slate-700 font-semibold text-xs tracking-wider">Root Cause <span class="text-red-500">*</span></label>
                                <textarea name="root_cause" required rows="5" style="min-height: 120px;" {{ $isComplete ? 'readonly' : '' }} class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none text-slate-700 resize-none overflow-hidden autogrow-textarea flex-grow {{ $isComplete ? 'bg-slate-50 text-slate-500 cursor-not-allowed' : '' }}" placeholder="Enter Root Cause...">{{ old('root_cause', $action->root_cause ?? '') }}</textarea>
                            </div>
                        </div>

                        <!-- Row 3: Why 4 (Left) and Analyzed by (Right) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-slate-700 font-semibold text-xs tracking-wider">Why 4</label>
                                <textarea name="why_four" rows="1" {{ $isComplete ? 'readonly' : '' }} class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none text-slate-700 resize-none overflow-hidden autogrow-textarea {{ $isComplete ? 'bg-slate-50 text-slate-500 cursor-not-allowed' : '' }}" placeholder="Enter Why 4 (Optional)...">{{ old('why_four', $action->why_four ?? '') }}</textarea>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-slate-700 font-semibold text-xs tracking-wider">Analized by Auditee Superior <span class="text-red-500">*</span></label>
                                <x-searchable-select
                                    id="analyzed_by"
                                    name="analyzed_by"
                                    label="Analized by: Auditee Superior"
                                    required="true"
                                    hideLabel="true"
                                    disabled="{{ $isComplete ? 1 : 0 }}"
                                    apiUrl="{{ route('internal_audit.get_users') }}"
                                    updateEvent="update-analyzed-by"
                                    changeEvent="analyzed-by-changed" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Corrective & Preventive Action Side-by-Side -->
                <div class="border-t border-slate-100 pt-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <!-- A. Corrective Action -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-slate-700 font-semibold text-sm tracking-wider">A. Corrective Action <span class="text-red-500">*</span></label>
                            <span class="text-slate-400 text-[10px] -mt-1 block italic">(Tindakan Darurat untuk mengatasi masalah)</span>
                             <textarea name="corrective_action" required rows="3" {{ $isComplete ? 'readonly' : '' }} class="w-full px-4 py-[9px] border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm mt-1 resize-none overflow-hidden autogrow-textarea {{ $isComplete ? 'bg-slate-50 text-slate-500 cursor-not-allowed' : '' }}" placeholder="Enter corrective actions...">{{ old('corrective_action', $action->corrective_action ?? '') }}</textarea>
                             
                              @if(!$isComplete)
                                <div class="mt-3">
                                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Evidence Photos (Max 3)</label>
                                    
                                    <div class="flex flex-wrap items-center gap-3">
                                        <div class="grid grid-cols-2 gap-2 w-full max-w-[280px] shrink-0">
                                            <div class="relative group">
                                                <input type="file" id="corr_camera_input" accept="image/*" capture="environment" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                                <div class="flex flex-row items-center justify-center gap-2 h-16 border border-dashed border-blue-200 rounded-lg bg-blue-50/50 hover:bg-blue-50 hover:border-blue-300 transition-all text-center px-2">
                                                    <div class="w-7 h-7 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center shrink-0">
                                                        <i class="fas fa-camera text-xs"></i>
                                                    </div>
                                                    <span class="text-xs font-medium text-blue-600">Take Photo</span>
                                                </div>
                                            </div>
                                            <div class="relative group">
                                                <input type="file" id="corr_gallery_input" multiple accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                                <div class="flex flex-row items-center justify-center gap-2 h-16 border border-dashed border-slate-200 rounded-lg bg-slate-50/50 hover:bg-slate-50 transition-all text-center px-2">
                                                    <div class="w-7 h-7 bg-slate-100 text-slate-500 rounded-full flex items-center justify-center shrink-0">
                                                        <i class="fas fa-images text-xs"></i>
                                                    </div>
                                                    <span class="text-xs font-medium text-slate-600">From Gallery</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Previews positioned to the right of buttons -->
                                        <div id="corr_preview_container" class="flex flex-wrap gap-2 items-center"></div>
                                    </div>
                                    <input type="hidden" name="existing_corrective_photos" id="existing_corrective_photos" value="{{ $action->corrective_path ?? '' }}">
                                    <input type="file" id="hidden_corr_input" name="corrective_photos[]" multiple class="hidden">
                                </div>
                            @endif

                            @if($isComplete && !empty($action->corrective_path))
                                <div class="mt-2 flex flex-wrap gap-3">
                                    <div id="corr_readonly_container" class="flex flex-wrap gap-2">
                                        @foreach(explode(',', $action->corrective_path) as $path)
                                            @if(!empty(trim($path)))
                                                <img src="{{ asset(trim($path)) }}" class="w-16 h-16 object-cover rounded-lg border border-slate-200 cursor-pointer hover:opacity-90 transition">
                                            @endif
                                        @endforeach
                                    </div>
                                    <span class="text-[10px] sm:text-xs text-slate-400 italic whitespace-nowrap mt-2"><i class="fa-solid fa-magnifying-glass-plus mr-1"></i>Click to zoom / preview</span>
                                </div>
                            @endif
                        </div>
                        
                        <!-- B. Preventive Action -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-slate-700 font-semibold text-sm tracking-wider">B. Preventive Action <span class="text-red-500">*</span></label>
                            <span class="text-slate-400 text-[10px] -mt-1 block italic">(Perbaikan yang harus segera dilakukan untuk menghilangkan akar penyebab)</span>
                            <textarea name="preventive_action" required rows="3" {{ $isComplete ? 'readonly' : '' }} class="w-full px-4 py-[9px] border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm mt-1 resize-none overflow-hidden autogrow-textarea {{ $isComplete ? 'bg-slate-50 text-slate-500 cursor-not-allowed' : '' }}" placeholder="Enter preventive actions...">{{ old('preventive_action', $action->preventive_action ?? '') }}</textarea>

                            @if(!$isComplete)
                                <div class="mt-3">
                                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Evidence Photos (Max 3)</label>
                                    
                                    <div class="flex flex-wrap items-center gap-3">
                                        <div class="grid grid-cols-2 gap-2 w-full max-w-[280px] shrink-0">
                                            <div class="relative group">
                                                <input type="file" id="prev_camera_input" accept="image/*" capture="environment" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                                <div class="flex flex-row items-center justify-center gap-2 h-16 border border-dashed border-blue-200 rounded-lg bg-blue-50/50 hover:bg-blue-50 hover:border-blue-300 transition-all text-center px-2">
                                                    <div class="w-7 h-7 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center shrink-0">
                                                        <i class="fas fa-camera text-xs"></i>
                                                    </div>
                                                    <span class="text-xs font-medium text-blue-600">Take Photo</span>
                                                </div>
                                            </div>
                                            <div class="relative group">
                                                <input type="file" id="prev_gallery_input" multiple accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                                <div class="flex flex-row items-center justify-center gap-2 h-16 border border-dashed border-slate-200 rounded-lg bg-slate-50/50 hover:bg-slate-50 transition-all text-center px-2">
                                                    <div class="w-7 h-7 bg-slate-100 text-slate-500 rounded-full flex items-center justify-center shrink-0">
                                                        <i class="fas fa-images text-xs"></i>
                                                    </div>
                                                    <span class="text-xs font-medium text-slate-600">From Gallery</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Previews positioned to the right of buttons -->
                                        <div id="prev_preview_container" class="flex flex-wrap gap-2 items-center"></div>
                                    </div>
                                    <input type="hidden" name="existing_preventive_photos" id="existing_preventive_photos" value="{{ $action->preventive_path ?? '' }}">
                                    <input type="file" id="hidden_prev_input" name="preventive_photos[]" multiple class="hidden">
                                </div>
                            @endif

                            @if($isComplete && !empty($action->preventive_path))
                                <div class="mt-2 flex flex-wrap gap-3">
                                    <div id="prev_readonly_container" class="flex flex-wrap gap-2">
                                        @foreach(explode(',', $action->preventive_path) as $path)
                                            @if(!empty(trim($path)))
                                                <img src="{{ asset(trim($path)) }}" class="w-16 h-16 object-cover rounded-lg border border-slate-200 cursor-pointer hover:opacity-90 transition">
                                            @endif
                                        @endforeach
                                    </div>
                                    <span class="text-[10px] sm:text-xs text-slate-400 italic whitespace-nowrap mt-2"><i class="fa-solid fa-magnifying-glass-plus mr-1"></i>Click to zoom / preview</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Notes & Signatures -->
                <div class="border-t border-slate-100 pt-6">
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 sm:gap-6">
                        <!-- Notes for A & B -->
                        <div class="flex flex-col gap-1.5 sm:col-span-2">
                            <label class="text-slate-700 font-semibold text-sm tracking-wider">Notes for A & B</label>
                            <textarea name="notes" rows="1" {{ $isComplete ? 'readonly' : '' }} class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none text-slate-700 resize-none overflow-hidden autogrow-textarea {{ $isComplete ? 'bg-slate-50 text-slate-500 cursor-not-allowed' : '' }}" placeholder="Enter notes...">{{ old('notes', $action->notes ?? '') }}</textarea>
                        </div>
                        
                        <!-- Auditee -->
                        <div class="flex flex-col gap-1.5 sm:col-span-1">
                            <label class="text-slate-700 font-semibold text-sm tracking-wider">Auditee</label>
                            <div class="w-full px-4 py-[9px] border border-slate-200 rounded-lg bg-slate-50 text-slate-500 text-sm break-words whitespace-normal min-h-[38px]" title="{{ old('auditee_name', $action->auditee_name ?? $car->header_auditee ?? $car->auditee ?? '') }}">
                                {{ old('auditee_name', $action->auditee_name ?? $car->header_auditee ?? $car->auditee ?? '-') }}
                            </div>
                            <input type="hidden" name="auditee_name" value="{{ old('auditee_name', $action->auditee_name ?? $car->header_auditee ?? $car->auditee ?? '') }}">
                        </div>

                        <!-- Auditee Superior -->
                        <div class="flex flex-col gap-1.5 sm:col-span-1">
                            <label class="text-slate-700 font-semibold text-sm tracking-wider">Auditee Superior</label>
                            <input type="text" name="auditee_superior_name" id="auditee_superior_name" value="{{ old('auditee_superior_name', $action->auditee_superior_name ?? '') }}" readonly class="w-full pl-4 pr-8 py-[9px] border border-slate-200 rounded-lg bg-slate-50 text-slate-500 cursor-not-allowed text-sm outline-none truncate" placeholder="Name of Auditee Superior...">
                        </div>
                    </div>
                </div>

                <!-- Submit Button / Rollback -->
                <div class="flex justify-end gap-3 border-t border-slate-100 pt-6">
                    @if($isComplete)
                        @php
                            $isAuditor = false;
                            if (!empty($car->auditor)) {
                                $auditors = array_map('trim', explode(',', $car->auditor));
                                foreach ($auditors as $auditorName) {
                                    if (strcasecmp(Auth::user()->full_name, $auditorName) === 0) {
                                        $isAuditor = true;
                                        break;
                                    }
                                }
                            }
                            $isSuperior = isset($action) && strcasecmp(Auth::user()->full_name, $action->auditee_superior_name ?? '') === 0;
                            
                            $showActionButtons = false;
                            if ($isSuperior && ($car->status ?? '') === 'Under Review') {
                                $showActionButtons = true;
                            } elseif ($isAuditor && ($car->status ?? '') === 'Need Verification') {
                                $showActionButtons = true;
                            }
                        @endphp
                        @if($showActionButtons)
                            <button type="button" id="btnApproveAction" class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold transition-all text-sm flex items-center gap-2">
                                <i class="fa-solid fa-check text-base"></i> Approve
                            </button>
                            <button type="button" id="btnRejectAction" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold transition-all text-sm flex items-center gap-2">
                                <i class="fa-solid fa-xmark text-base"></i> Reject
                            </button>
                        @endif
                        @php
                            $showRollback = true;
                            if (($car->status ?? '') === 'Closed') {
                                if (!$isAuditor) {
                                    $showRollback = false;
                                }
                            }
                        @endphp
                        @if($showRollback)
                            <button type="button" id="btnRollback" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-bold transition-all text-sm flex items-center gap-2">
                                <i class="fa-solid fa-rotate-left text-base"></i> Rollback
                            </button>
                        @endif
                    @else
                        <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-all text-sm flex items-center gap-2">
                            <i class="fa-solid fa-floppy-disk text-base"></i> Save Action Plan
                        </button>
                    @endif
                </div>
            </div>
        </form>
    </main>

    <!-- Confirmation Modal -->
    <div id="confirmationModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-slate-900/50 transition-opacity" onclick="closeConfirmationModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl w-full max-w-md transform transition-all shadow-2xl p-6">
                <div class="text-center">
                    <div id="modalIcon" class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-5">
                        <!-- Icon will be injected via JS -->
                    </div>
                    <h3 id="modalTitle" class="text-xl font-bold text-slate-800 mb-2">Confirm Action</h3>
                    <p id="modalMessage" class="text-slate-500 text-sm mb-6">Are you sure you want to proceed?</p>
                    <div class="flex justify-center gap-3">
                        <button type="button" onclick="closeConfirmationModal()" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-xl transition-colors text-sm">
                            Cancel
                        </button>
                        <button type="button" id="confirmBtn" onclick="submitConfirmation()" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition-colors text-sm">
                            Confirm
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.footer')
</div>


<script>
    function autoGrow(element) {
        element.style.height = "auto";
        element.style.height = (element.scrollHeight) + "px";
    }

    let currentAction = '';

    function closeConfirmationModal() {
        document.getElementById('confirmationModal').classList.add('hidden');
        currentAction = '';
    }

    function openConfirmationModal(action) {
        currentAction = action;
        const modal = document.getElementById('confirmationModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalMessage = document.getElementById('modalMessage');
        const modalIcon = document.getElementById('modalIcon');
        const confirmBtn = document.getElementById('confirmBtn');

        if (action === 'approve') {
            modalTitle.innerText = 'Confirm Approval';
            modalMessage.innerHTML = 'Are you sure you want to approve this Action Plan?<br>This will mark the CAR as Closed.';
            modalIcon.className = 'w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-5';
            modalIcon.innerHTML = `<svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>`;
            confirmBtn.className = 'px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-xl transition-colors text-sm';
            confirmBtn.innerText = 'Yes, Approve';
        } else if (action === 'reject') {
            modalTitle.innerText = 'Confirm Rejection';
            modalMessage.innerHTML = 'Are you sure you want to reject this Action Plan?<br>This will return the action plan to draft status for correction.';
            modalIcon.className = 'w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-5';
            modalIcon.innerHTML = `<svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>`;
            confirmBtn.className = 'px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-xl transition-colors text-sm';
            confirmBtn.innerText = 'Yes, Reject';
        } else if (action === 'rollback') {
            modalTitle.innerText = 'Confirm Rollback';
            modalMessage.innerHTML = 'Are you sure you want to rollback this action plan to draft?<br>This will make all fields editable again.';
            modalIcon.className = 'w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-5';
            modalIcon.innerHTML = `<svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 0 1 8 8v2M3 10l6 6m-6-6l6-6"></path></svg>`;
            confirmBtn.className = 'px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-medium rounded-xl transition-colors text-sm';
            confirmBtn.innerText = 'Yes, Rollback';
        }

        modal.classList.remove('hidden');
    }

    function submitConfirmation() {
        if (!currentAction) return;

        const confirmBtn = document.getElementById('confirmBtn');
        const originalText = confirmBtn.innerText;
        confirmBtn.disabled = true;
        confirmBtn.innerText = 'Processing...';

        let url = '';
        let payload = {};

        if (currentAction === 'approve') {
            url = "{{ route('internal_audit.cars.approve') }}";
            payload = {
                _token: "{{ csrf_token() }}",
                car_id: {{ $car->id }},
                role: "{{ ($car->status ?? '') === 'Need Verification' ? 'auditor' : 'superior' }}"
            };
        } else if (currentAction === 'reject') {
            url = "{{ route('internal_audit.cars.reject') }}";
            payload = {
                _token: "{{ csrf_token() }}",
                car_id: {{ $car->id }}
            };
        } else if (currentAction === 'rollback') {
            url = "{{ route('internal_audit.action_report.rollback', request()->route('id')) }}";
            payload = {
                _token: "{{ csrf_token() }}"
            };
        }

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            closeConfirmationModal();
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showToast(data.message || 'Action failed.', 'warning');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            closeConfirmationModal();
            showToast('Something went wrong. Please try again.', 'warning');
        })
        .finally(() => {
            confirmBtn.disabled = false;
            confirmBtn.innerText = originalText;
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Auto-grow textareas on load and input
        const textareas = document.querySelectorAll('.autogrow-textarea');
        textareas.forEach(textarea => {
            // Initial call to set size based on loaded content
            setTimeout(() => {
                autoGrow(textarea);
            }, 10);
            
            textarea.addEventListener('input', function() {
                autoGrow(this);
            });
        });
        @if(isset($action) && !empty($action->analyzed_by))
            window.dispatchEvent(new CustomEvent('update-analyzed-by', { 
                detail: { 
                    id: '{{ $action->analyzed_by }}', 
                    name: '{{ $action->analyzed_by }}' 
                } 
            }));
            const superiorInput = document.getElementById('auditee_superior_name');
            if (superiorInput && !superiorInput.value) {
                superiorInput.value = '{{ $action->analyzed_by }}';
            }
        @endif

        // Rollback Action Plan Handler
        const btnRollback = document.getElementById('btnRollback');
        if (btnRollback) {
            btnRollback.addEventListener('click', function() {
                openConfirmationModal('rollback');
            });
        }

        // Approve Action Plan Handler
        const btnApprove = document.getElementById('btnApproveAction');
        if (btnApprove) {
            btnApprove.addEventListener('click', function() {
                openConfirmationModal('approve');
            });
        }

        // Reject Action Plan Handler
        const btnReject = document.getElementById('btnRejectAction');
        if (btnReject) {
            btnReject.addEventListener('click', function() {
                openConfirmationModal('reject');
            });
        }

        window.addEventListener('analyzed-by-changed', function(e) {
            const name = e.detail.name || '';
            const superiorInput = document.getElementById('auditee_superior_name');
            if (superiorInput) {
                superiorInput.value = name;
            }
        });

        // AJAX Form Submission
        const form = document.getElementById('actionPlanForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Validate searchable-select (analyzed_by) manually since it is a hidden input
                const analyzedBy = document.getElementById('analyzed_by');
                if (!analyzedBy || !analyzedBy.value || analyzedBy.value.trim() === '') {
                    showToast('Please select Analized by Auditee Superior.', 'error');
                    return;
                }
                
                // Get submit button and show loading state
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Saving...';
                
                const formData = new FormData(form);
                
                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message, 'success');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showToast(data.message || 'An error occurred.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('An error occurred while saving.', 'error');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
            });
        }

        // Corrective Photos state
        let correctiveFiles = [];
        let existingCorrective = {!! json_encode(array_filter(explode(',', $action->corrective_path ?? ''))) !!};
        const corrPreviewContainer = document.getElementById('corr_preview_container');
        const hiddenCorrInput = document.getElementById('hidden_corr_input');

        // Preventive Photos state
        let preventiveFiles = [];
        let existingPreventive = {!! json_encode(array_filter(explode(',', $action->preventive_path ?? ''))) !!};
        const prevPreviewContainer = document.getElementById('prev_preview_container');
        const hiddenPrevInput = document.getElementById('hidden_prev_input');

        function renderCorrectivePreviews() {
            if (!corrPreviewContainer) return;
            corrPreviewContainer.innerHTML = '';

            // Render existing
            existingCorrective.forEach((path, index) => {
                const wrapper = document.createElement('div');
                wrapper.className = "relative w-16 h-16 bg-slate-100 border border-slate-200 rounded-lg group";
                
                const img = document.createElement('img');
                img.src = '/' + path;
                img.className = "w-full h-full object-cover rounded-lg cursor-pointer";
                
                const btn = document.createElement('button');
                btn.type = "button";
                btn.className = "absolute -top-1.5 -right-1.5 bg-red-500 text-white rounded-full w-4 h-4 flex items-center justify-center text-[10px] font-bold hover:bg-red-600 transition-colors z-20";
                btn.innerHTML = "×";
                btn.onclick = function(evt) {
                    evt.stopPropagation();
                    existingCorrective.splice(index, 1);
                    document.getElementById('existing_corrective_photos').value = existingCorrective.join(',');
                    renderCorrectivePreviews();
                };
                
                wrapper.appendChild(img);
                wrapper.appendChild(btn);
                corrPreviewContainer.appendChild(wrapper);
            });

            // Render newly uploaded files
            correctiveFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const wrapper = document.createElement('div');
                    wrapper.className = "relative w-16 h-16 bg-slate-100 border border-slate-200 rounded-lg group";
                    
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = "w-full h-full object-cover rounded-lg cursor-pointer";
                    
                    const btn = document.createElement('button');
                    btn.type = "button";
                    btn.className = "absolute -top-1.5 -right-1.5 bg-red-500 text-white rounded-full w-4 h-4 flex items-center justify-center text-[10px] font-bold hover:bg-red-600 transition-colors z-20";
                    btn.innerHTML = "×";
                    btn.onclick = function(evt) {
                        evt.stopPropagation();
                        correctiveFiles.splice(index, 1);
                        syncCorrectiveFiles();
                        renderCorrectivePreviews();
                    };
                    
                    wrapper.appendChild(img);
                    wrapper.appendChild(btn);
                    corrPreviewContainer.appendChild(wrapper);
                };
                reader.readAsDataURL(file);
            });

            // Set up ViewerJS on the container
            setTimeout(() => {
                if (typeof Viewer !== 'undefined' && corrPreviewContainer.children.length > 0) {
                    if (corrPreviewContainer.viewer) {
                        corrPreviewContainer.viewer.destroy();
                    }
                    corrPreviewContainer.viewer = new Viewer(corrPreviewContainer, {
                        title: false,
                        navbar: false,
                        toolbar: {
                            zoomIn: 1, zoomOut: 1, oneToOne: 1, reset: 1,
                            prev: 0, play: 0, next: 0, rotateLeft: 1, rotateRight: 1,
                            flipHorizontal: 1, flipVertical: 1
                        }
                    });
                }
            }, 100);
        }

        function syncCorrectiveFiles() {
            if (!hiddenCorrInput) return;
            const dataTransfer = new DataTransfer();
            correctiveFiles.forEach(file => dataTransfer.items.add(file));
            hiddenCorrInput.files = dataTransfer.files;
        }

        function handleCorrectiveFileSelection(files) {
            const totalCount = existingCorrective.length + correctiveFiles.length;
            const remainingCount = 3 - totalCount;
            if (remainingCount <= 0) {
                showToast("You can only upload up to 3 photos max.", "error");
                return;
            }
            
            const filesToAppend = Array.from(files).slice(0, remainingCount);
            if (filesToAppend.length < files.length) {
                showToast("Limit exceeded. Only " + remainingCount + " photos added.", "warning");
            }
            
            correctiveFiles = correctiveFiles.concat(filesToAppend);
            syncCorrectiveFiles();
            renderCorrectivePreviews();
        }

        function renderPreventivePreviews() {
            if (!prevPreviewContainer) return;
            prevPreviewContainer.innerHTML = '';

            // Render existing
            existingPreventive.forEach((path, index) => {
                const wrapper = document.createElement('div');
                wrapper.className = "relative w-16 h-16 bg-slate-100 border border-slate-200 rounded-lg group";
                
                const img = document.createElement('img');
                img.src = '/' + path;
                img.className = "w-full h-full object-cover rounded-lg cursor-pointer";
                
                const btn = document.createElement('button');
                btn.type = "button";
                btn.className = "absolute -top-1.5 -right-1.5 bg-red-500 text-white rounded-full w-4 h-4 flex items-center justify-center text-[10px] font-bold hover:bg-red-600 transition-colors z-20";
                btn.innerHTML = "×";
                btn.onclick = function(evt) {
                    evt.stopPropagation();
                    existingPreventive.splice(index, 1);
                    document.getElementById('existing_preventive_photos').value = existingPreventive.join(',');
                    renderPreventivePreviews();
                };
                
                wrapper.appendChild(img);
                wrapper.appendChild(btn);
                prevPreviewContainer.appendChild(wrapper);
            });

            // Render newly uploaded files
            preventiveFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const wrapper = document.createElement('div');
                    wrapper.className = "relative w-16 h-16 bg-slate-100 border border-slate-200 rounded-lg group";
                    
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = "w-full h-full object-cover rounded-lg cursor-pointer";
                    
                    const btn = document.createElement('button');
                    btn.type = "button";
                    btn.className = "absolute -top-1.5 -right-1.5 bg-red-500 text-white rounded-full w-4 h-4 flex items-center justify-center text-[10px] font-bold hover:bg-red-600 transition-colors z-20";
                    btn.innerHTML = "×";
                    btn.onclick = function(evt) {
                        evt.stopPropagation();
                        preventiveFiles.splice(index, 1);
                        syncPreventiveFiles();
                        renderPreventivePreviews();
                    };
                    
                    wrapper.appendChild(img);
                    wrapper.appendChild(btn);
                    prevPreviewContainer.appendChild(wrapper);
                };
                reader.readAsDataURL(file);
            });

            // Set up ViewerJS on the container
            setTimeout(() => {
                if (typeof Viewer !== 'undefined' && prevPreviewContainer.children.length > 0) {
                    if (prevPreviewContainer.viewer) {
                        prevPreviewContainer.viewer.destroy();
                    }
                    prevPreviewContainer.viewer = new Viewer(prevPreviewContainer, {
                        title: false,
                        navbar: false,
                        toolbar: {
                            zoomIn: 1, zoomOut: 1, oneToOne: 1, reset: 1,
                            prev: 0, play: 0, next: 0, rotateLeft: 1, rotateRight: 1,
                            flipHorizontal: 1, flipVertical: 1
                        }
                    });
                }
            }, 100);
        }

        function syncPreventiveFiles() {
            if (!hiddenPrevInput) return;
            const dataTransfer = new DataTransfer();
            preventiveFiles.forEach(file => dataTransfer.items.add(file));
            hiddenPrevInput.files = dataTransfer.files;
        }

        function handlePreventiveFileSelection(files) {
            const totalCount = existingPreventive.length + preventiveFiles.length;
            const remainingCount = 3 - totalCount;
            if (remainingCount <= 0) {
                showToast("You can only upload up to 3 photos max.", "error");
                return;
            }
            
            const filesToAppend = Array.from(files).slice(0, remainingCount);
            if (filesToAppend.length < files.length) {
                showToast("Limit exceeded. Only " + remainingCount + " photos added.", "warning");
            }
            
            preventiveFiles = preventiveFiles.concat(filesToAppend);
            syncPreventiveFiles();
            renderPreventivePreviews();
        }

        // Corrective input hooks
        const corrCameraInput = document.getElementById('corr_camera_input');
        const corrGalleryInput = document.getElementById('corr_gallery_input');
        if (corrCameraInput) {
            corrCameraInput.addEventListener('change', function() {
                if (this.files && this.files.length > 0) {
                    handleCorrectiveFileSelection(this.files);
                    this.value = '';
                }
            });
        }
        if (corrGalleryInput) {
            corrGalleryInput.addEventListener('change', function() {
                if (this.files && this.files.length > 0) {
                    handleCorrectiveFileSelection(this.files);
                    this.value = '';
                }
            });
        }

        // Preventive input hooks
        const prevCameraInput = document.getElementById('prev_camera_input');
        const prevGalleryInput = document.getElementById('prev_gallery_input');
        if (prevCameraInput) {
            prevCameraInput.addEventListener('change', function() {
                if (this.files && this.files.length > 0) {
                    handlePreventiveFileSelection(this.files);
                    this.value = '';
                }
            });
        }
        if (prevGalleryInput) {
            prevGalleryInput.addEventListener('change', function() {
                if (this.files && this.files.length > 0) {
                    handlePreventiveFileSelection(this.files);
                    this.value = '';
                }
            });
        }

        // Initial render
        renderCorrectivePreviews();
        renderPreventivePreviews();

        // Readonly Viewers
        const corrReadonly = document.getElementById('corr_readonly_container');
        if (typeof Viewer !== 'undefined' && corrReadonly) {
            new Viewer(corrReadonly, {
                title: false,
                navbar: false,
                toolbar: {
                    zoomIn: 1, zoomOut: 1, oneToOne: 1, reset: 1,
                    prev: 0, play: 0, next: 0, rotateLeft: 1, rotateRight: 1,
                    flipHorizontal: 1, flipVertical: 1
                }
            });
        }

        const prevReadonly = document.getElementById('prev_readonly_container');
        if (typeof Viewer !== 'undefined' && prevReadonly) {
            new Viewer(prevReadonly, {
                title: false,
                navbar: false,
                toolbar: {
                    zoomIn: 1, zoomOut: 1, oneToOne: 1, reset: 1,
                    prev: 0, play: 0, next: 0, rotateLeft: 1, rotateRight: 1,
                    flipHorizontal: 1, flipVertical: 1
                }
            });
        }

        // Initialize ViewerJS on the images container
        const container = document.getElementById('finding_images_container');
        if (typeof Viewer !== 'undefined' && container) {
            new Viewer(container, {
                title: false,
                navbar: false,
                toolbar: {
                    zoomIn: 1,
                    zoomOut: 1,
                    oneToOne: 1,
                    reset: 1,
                    prev: 0,
                    play: 0,
                    next: 0,
                    rotateLeft: 1,
                    rotateRight: 1,
                    flipHorizontal: 1,
                    flipVertical: 1,
                }
            });
        }
    });
</script>

@endsection
