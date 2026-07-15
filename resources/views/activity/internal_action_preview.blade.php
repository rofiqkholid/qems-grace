@extends('layouts.app')

@php
    $hideCentralToast = true;
    $approve = $approve ?? null;
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
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
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
                                            @php
                                                $pathTrimmed = trim($path);
                                                $ext = strtolower(pathinfo($pathTrimmed, PATHINFO_EXTENSION));
                                            @endphp
                                            @if($ext === 'pdf')
                                                <button type="button" onclick="openActionFileModal('{{ asset($pathTrimmed) }}', 'pdf')" class="w-16 h-16 flex flex-col items-center justify-center rounded-lg border border-slate-200 bg-red-50 text-red-500 hover:bg-red-100 transition-colors">
                                                    <i class="fa-solid fa-file-pdf text-xl"></i>
                                                    <span class="text-[9px] font-bold mt-1">PDF</span>
                                                </button>
                                            @else
                                                <img src="{{ asset($pathTrimmed) }}" class="w-16 h-16 object-cover rounded-lg border border-slate-200 cursor-pointer hover:opacity-90 transition">
                                            @endif
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
            @php 
                $isComplete = isset($action) && in_array($action->action_status, ['open_verif', 'approve_superior', 'verified']); 
                
                $isSuperiorUser = isset($action) && strcasecmp(Auth::user()->full_name, $action->auditee_superior_name ?? '') === 0;
                
                $isAuditorUser = false;
                if (!empty($car->auditor)) {
                    $auditors = array_map('trim', explode(',', $car->auditor));
                    foreach ($auditors as $auditorName) {
                        if (strcasecmp(Auth::user()->full_name, $auditorName) === 0) {
                            $isAuditorUser = true;
                            break;
                        }
                    }
                }

                $isQmr = in_array(Auth::user()->username, ['031114-001', '260422-001', '121020-002']);

                $isReviewing = false;
                if ($isSuperiorUser && ($car->status ?? '') === 'Under Review') {
                    $isReviewing = true;
                } elseif ($isAuditorUser && ($car->status ?? '') === 'Need Verification') {
                    $isReviewing = true;
                } elseif ($isQmr && ($car->status ?? '') === 'Closed' && empty($car->qmr_approved_at)) {
                    $isReviewing = true;
                }

                $isAuditorReviewing = $isAuditorUser && ($car->status ?? '') === 'Need Verification';
            @endphp
            <div class="bg-white rounded-lg border border-slate-200 p-4 sm:p-8 space-y-8">
                <div>
                    <h2 class="text-lg font-bold text-slate-800 mb-5 pb-2 border-b border-slate-100">
                        Action Plan & Analysis
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column: Why 1 to Why 5 -->
                        <div class="flex flex-col gap-4">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-slate-700 font-semibold text-xs tracking-wider">Why 1 <span class="text-red-500">*</span></label>
                                <textarea name="why_one" required rows="1" {{ $isComplete ? 'readonly' : '' }} class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none text-slate-700 resize-none overflow-hidden autogrow-textarea {{ $isComplete ? 'bg-slate-50 text-slate-500 cursor-not-allowed' : '' }}" placeholder="Enter Why 1...">{{ old('why_one', $action->why_one ?? '') }}</textarea>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-slate-700 font-semibold text-xs tracking-wider">Why 2 <span class="text-red-500">*</span></label>
                                <textarea name="why_two" required rows="1" {{ $isComplete ? 'readonly' : '' }} class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none text-slate-700 resize-none overflow-hidden autogrow-textarea {{ $isComplete ? 'bg-slate-50 text-slate-500 cursor-not-allowed' : '' }}" placeholder="Enter Why 2...">{{ old('why_two', $action->why_two ?? '') }}</textarea>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-slate-700 font-semibold text-xs tracking-wider">Why 3 <span class="text-red-500">*</span></label>
                                <textarea name="why_three" required rows="1" {{ $isComplete ? 'readonly' : '' }} class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none text-slate-700 resize-none overflow-hidden autogrow-textarea {{ $isComplete ? 'bg-slate-50 text-slate-500 cursor-not-allowed' : '' }}" placeholder="Enter Why 3...">{{ old('why_three', $action->why_three ?? '') }}</textarea>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-slate-700 font-semibold text-xs tracking-wider">Why 4</label>
                                <textarea name="why_four" rows="1" {{ $isComplete ? 'readonly' : '' }} class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none text-slate-700 resize-none overflow-hidden autogrow-textarea {{ $isComplete ? 'bg-slate-50 text-slate-500 cursor-not-allowed' : '' }}" placeholder="Enter Why 4 (Optional)...">{{ old('why_four', $action->why_four ?? '') }}</textarea>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-slate-700 font-semibold text-xs tracking-wider">Why 5</label>
                                <textarea name="why_five" rows="1" {{ $isComplete ? 'readonly' : '' }} class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none text-slate-700 resize-none overflow-hidden autogrow-textarea {{ $isComplete ? 'bg-slate-50 text-slate-500 cursor-not-allowed' : '' }}" placeholder="Enter Why 5 (Optional)...">{{ old('why_five', $action->why_five ?? '') }}</textarea>
                            </div>
                        </div>

                        <!-- Right Column: Root Cause & Analyzed By -->
                        <div class="flex flex-col gap-4 justify-start">
                            <!-- Root Cause -->
                            <div class="flex flex-col gap-1.5">
                                <div class="flex items-center justify-between">
                                    <label class="text-slate-700 font-semibold text-xs tracking-wider">Root Cause <span class="text-red-500">*</span></label>
                                </div>
                                <div class="flex items-start gap-2 w-full">
                                    <div class="flex flex-col gap-1.5 w-full">
                                        <textarea name="root_cause" required rows="5" style="min-height: 120px;" {{ $isComplete ? 'readonly' : '' }} class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none text-slate-700 resize-none overflow-hidden autogrow-textarea {{ $isComplete ? 'bg-slate-50 text-slate-500 cursor-not-allowed' : '' }}" placeholder="Enter Root Cause...">{{ old('root_cause', $action->root_cause ?? '') }}</textarea>
                                        @if(!$isReviewing && !empty($approve->root_cause_verif ?? ''))
                                            <div class="flex">
                                                @if(($approve->root_cause_verif ?? '') === 'approve')
                                                    @if(($car->status ?? '') === 'Need Verification')
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-blue-700 bg-blue-50 rounded-lg border border-blue-200"><i class="fa-solid fa-circle-check"></i> Approved by Superior</span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-green-700 bg-green-50 rounded-lg border border-green-200"><i class="fa-solid fa-circle-check"></i> Approved</span>
                                                    @endif
                                                @elseif(($approve->root_cause_verif ?? '') === 'reject')
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-red-700 bg-red-50 rounded-lg border border-red-200"><i class="fa-solid fa-circle-xmark text-red-500"></i> Rejected</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    @if($isAuditorReviewing)
                                        <input type="hidden" name="root_cause_verif" id="root_cause_verif" value="">
                                        <div class="flex flex-col gap-1 shrink-0 ml-2">
                                            <button type="button" onclick="setFieldVerif('root_cause', 'approve')" id="btn_approve_root_cause" class="w-9 h-9 rounded-lg flex items-center justify-center border transition-all bg-green-50 border-green-200 text-green-600 hover:bg-green-100" title="Approve Root Cause">
                                                <i class="fa-solid fa-check text-xs"></i>
                                            </button>
                                            <button type="button" onclick="setFieldVerif('root_cause', 'reject')" id="btn_reject_root_cause" class="w-9 h-9 rounded-lg flex items-center justify-center border transition-all bg-red-50 border-red-200 text-red-600 hover:bg-red-100" title="Reject Root Cause">
                                                <i class="fa-solid fa-xmark text-xs"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="flex items-center justify-between mt-1">
                                    <div id="root_cause_preview" class="flex flex-wrap gap-2 items-center">
                                        @if($isComplete && !empty($action->root_cause_path))
                                            @foreach(explode(',', $action->root_cause_path) as $idx => $path)
                                                @php $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION)); @endphp
                                                <button type="button" onclick="openActionFileModal('{{ asset(trim($path)) }}', '{{ $ext }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg border border-slate-200 transition-colors">
                                                    <i class="fa-solid {{ $ext === 'pdf' ? 'fa-file-pdf text-red-500' : 'fa-image text-blue-500' }}"></i>
                                                    Show File {{ $idx + 1 }}
                                                </button>
                                            @endforeach
                                        @endif
                                    </div>
                                    @if(!$isComplete)
                                        <div class="shrink-0">
                                            <input type="file" id="root_cause_file" name="root_cause_photo[]" multiple accept="image/*,application/pdf" class="hidden" onchange="handleActionFiles(this, 'root_cause')">
                                            <button type="button" onclick="document.getElementById('root_cause_file').click()" class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-dashed border-blue-300 bg-blue-50/50 hover:bg-blue-50 text-blue-600 rounded-lg text-xs font-semibold transition-all relative" title="Take / Upload Photo or PDF">
                                                <i class="fas fa-camera text-xs"></i> Upload File <span class="text-red-500 font-bold ml-0.5">*</span>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                <input type="hidden" name="existing_root_cause_photo" id="existing_root_cause" value="{{ $action->root_cause_path ?? '' }}">
                            </div>

                            <!-- Analyzed by -->
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

                <!-- Corrective & Preventive Action Row-by-Row Grid Alignment -->
                <div class="border-t border-slate-100 pt-6">
                    @php
                        $isCorrOneApproved = ($approve->corrective_action_one_verif ?? '') === 'approve';
                        $isCorrOneReadonly = $isComplete || (!$isComplete && $isCorrOneApproved);
                        
                        $isPrevOneApproved = ($approve->preventive_action_one_verif ?? '') === 'approve';
                        $isPrevOneReadonly = $isComplete || (!$isComplete && $isPrevOneApproved);

                        $isCorrTwoApproved = ($approve->corrective_action_two_verif ?? '') === 'approve';
                        $isCorrTwoReadonly = $isComplete || (!$isComplete && $isCorrTwoApproved);
                        
                        $isPrevTwoApproved = ($approve->preventive_action_two_verif ?? '') === 'approve';
                        $isPrevTwoReadonly = $isComplete || (!$isComplete && $isPrevTwoApproved);

                        $isCorrThreeApproved = ($approve->corrective_action_three_verif ?? '') === 'approve';
                        $isCorrThreeReadonly = $isComplete || (!$isComplete && $isCorrThreeApproved);
                        
                        $isPrevThreeApproved = ($approve->preventive_action_three_verif ?? '') === 'approve';
                        $isPrevThreeReadonly = $isComplete || (!$isComplete && $isPrevThreeApproved);
                    @endphp

                    <!-- Column Headers -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 border-b border-slate-100 pb-2 mb-4">
                        <!-- A. Corrective Action Header -->
                        <div class="flex flex-col gap-1">
                            <label class="text-slate-700 font-semibold text-sm tracking-wider">A. Corrective Action <span class="text-red-500">*</span></label>
                            <span class="text-slate-400 text-[10px] -mt-1 block italic">(Tindakan Darurat untuk mengatasi masalah)</span>
                        </div>
                        <!-- B. Preventive Action Header -->
                        <div class="flex flex-col gap-1">
                            <label class="text-slate-700 font-semibold text-sm tracking-wider">B. Preventive Action <span class="text-red-500">*</span></label>
                            <span class="text-slate-400 text-[10px] -mt-1 block italic">(Perbaikan yang harus segera dilakukan untuk menghilangkan akar penyebab)</span>
                        </div>
                    </div>

                    <!-- Row-by-Row Content -->
                    <div class="flex flex-col gap-6">
                        <!-- Row 1 -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 items-start">
                            <!-- Corrective Row 1 -->
                            <div class="flex flex-col gap-1 w-full">
                                <div class="flex items-center gap-2">
                                    <textarea name="corrective_action_one" required rows="1" {{ $isCorrOneReadonly ? 'readonly' : '' }} class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm resize-none overflow-hidden autogrow-textarea text-slate-700 {{ $isCorrOneReadonly ? 'bg-slate-50 text-slate-500 cursor-not-allowed' : '' }}" placeholder="Corrective Action 1...">{{ old('corrective_action_one', $action->corrective_action_one ?? '') }}</textarea>
                                    @if(!$isComplete && !$isCorrOneApproved)
                                        <div class="shrink-0">
                                            <input type="file" id="corr_one_file" name="corrective_photo_one[]" multiple accept="image/*,application/pdf" class="hidden" onchange="handleActionFiles(this, 'corr_one')">
                                            <button type="button" onclick="document.getElementById('corr_one_file').click()" class="w-10 h-10 border border-dashed border-blue-300 bg-blue-50/50 hover:bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center transition-all relative" title="Take / Upload Photo or PDF">
                                                <i class="fas fa-camera text-xs"></i>
                                                <span class="text-red-500 absolute -top-2 -right-2 text-[10px] font-bold">*</span>
                                            </button>
                                        </div>
                                    @endif
                                    @if($isAuditorReviewing)
                                        <input type="hidden" name="corrective_action_one_verif" id="corrective_action_one_verif" value="">
                                        <div class="flex items-center gap-1 shrink-0 ml-2">
                                            <button type="button" onclick="setFieldVerif('corrective_action_one', 'approve')" id="btn_approve_corrective_action_one" class="w-9 h-9 rounded-lg flex items-center justify-center border transition-all bg-green-50 border-green-200 text-green-600 hover:bg-green-100" title="Approve this row">
                                                <i class="fa-solid fa-check text-xs"></i>
                                            </button>
                                            <button type="button" onclick="setFieldVerif('corrective_action_one', 'reject')" id="btn_reject_corrective_action_one" class="w-9 h-9 rounded-lg flex items-center justify-center border transition-all bg-red-50 border-red-200 text-red-600 hover:bg-red-100" title="Reject this row">
                                                <i class="fa-solid fa-xmark text-xs"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                <div id="corr_one_preview" class="flex flex-wrap gap-2 items-center mt-1.5">
                                    @if(!$isReviewing && !empty($approve->corrective_action_one_verif ?? ''))
                                        @if(($approve->corrective_action_one_verif ?? '') === 'approve')
                                            @if(($car->status ?? '') === 'Need Verification')
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-blue-700 bg-blue-50 rounded-lg border border-blue-200"><i class="fa-solid fa-circle-check"></i> Approved by Superior</span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-green-700 bg-green-50 rounded-lg border border-green-200"><i class="fa-solid fa-circle-check"></i> Approved</span>
                                            @endif
                                        @elseif(($approve->corrective_action_one_verif ?? '') === 'reject')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-red-700 bg-red-50 rounded-lg border border-red-200"><i class="fa-solid fa-circle-xmark text-red-500"></i> Rejected</span>
                                        @endif
                                        @if($isComplete && !empty($action->corrective_path_one))
                                            <div class="h-6 w-[1px] bg-slate-200 mx-1 self-center shrink-0"></div>
                                        @endif
                                    @endif
                                    @if($isComplete && !empty($action->corrective_path_one))
                                        @foreach(explode(',', $action->corrective_path_one) as $idx => $path)
                                            @php $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION)); @endphp
                                            <button type="button" onclick="openActionFileModal('{{ asset(trim($path)) }}', '{{ $ext }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg border border-slate-200 transition-colors">
                                                <i class="fa-solid {{ $ext === 'pdf' ? 'fa-file-pdf text-red-500' : 'fa-image text-blue-500' }}"></i>
                                                Show File {{ $idx + 1 }}
                                            </button>
                                        @endforeach
                                    @endif
                                </div>
                                <input type="hidden" name="existing_corrective_photo_one" id="existing_corr_one" value="{{ $action->corrective_path_one ?? '' }}">
                            </div>

                            <!-- Preventive Row 1 -->
                            <div class="flex flex-col gap-1 w-full">
                                <div class="flex items-center gap-2">
                                    <textarea name="preventive_action_one" required rows="1" {{ $isPrevOneReadonly ? 'readonly' : '' }} class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm resize-none overflow-hidden autogrow-textarea text-slate-700 {{ $isPrevOneReadonly ? 'bg-slate-50 text-slate-500 cursor-not-allowed' : '' }}" placeholder="Preventive Action 1...">{{ old('preventive_action_one', $action->preventive_action_one ?? '') }}</textarea>
                                    @if(!$isComplete && !$isPrevOneApproved)
                                        <div class="shrink-0">
                                            <input type="file" id="prev_one_file" name="preventive_photo_one[]" multiple accept="image/*,application/pdf" class="hidden" onchange="handleActionFiles(this, 'prev_one')">
                                            <button type="button" onclick="document.getElementById('prev_one_file').click()" class="w-10 h-10 border border-dashed border-blue-300 bg-blue-50/50 hover:bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center transition-all relative" title="Take / Upload Photo or PDF">
                                                <i class="fas fa-camera text-xs"></i>
                                                <span class="text-red-500 absolute -top-2 -right-2 text-[10px] font-bold">*</span>
                                            </button>
                                        </div>
                                    @endif
                                    @if($isAuditorReviewing)
                                        <input type="hidden" name="preventive_action_one_verif" id="preventive_action_one_verif" value="">
                                        <div class="flex items-center gap-1 shrink-0 ml-2">
                                            <button type="button" onclick="setFieldVerif('preventive_action_one', 'approve')" id="btn_approve_preventive_action_one" class="w-9 h-9 rounded-lg flex items-center justify-center border transition-all bg-green-50 border-green-200 text-green-600 hover:bg-green-100" title="Approve this row">
                                                <i class="fa-solid fa-check text-xs"></i>
                                            </button>
                                            <button type="button" onclick="setFieldVerif('preventive_action_one', 'reject')" id="btn_reject_preventive_action_one" class="w-9 h-9 rounded-lg flex items-center justify-center border transition-all bg-red-50 border-red-200 text-red-600 hover:bg-red-100" title="Reject this row">
                                                <i class="fa-solid fa-xmark text-xs"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                <div id="prev_one_preview" class="flex flex-wrap gap-2 items-center mt-1.5">
                                    @if(!$isReviewing && !empty($approve->preventive_action_one_verif ?? ''))
                                        @if(($approve->preventive_action_one_verif ?? '') === 'approve')
                                            @if(($car->status ?? '') === 'Need Verification')
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-blue-700 bg-blue-50 rounded-lg border border-blue-200"><i class="fa-solid fa-circle-check"></i> Approved by Superior</span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-green-700 bg-green-50 rounded-lg border border-green-200"><i class="fa-solid fa-circle-check"></i> Approved</span>
                                            @endif
                                        @elseif(($approve->preventive_action_one_verif ?? '') === 'reject')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-red-700 bg-red-50 rounded-lg border border-red-200"><i class="fa-solid fa-circle-xmark"></i> Rejected</span>
                                        @endif
                                        @if($isComplete && !empty($action->preventive_path_one))
                                            <div class="h-6 w-[1px] bg-slate-200 mx-1 self-center shrink-0"></div>
                                        @endif
                                    @endif
                                    @if($isComplete && !empty($action->preventive_path_one))
                                        @foreach(explode(',', $action->preventive_path_one) as $idx => $path)
                                            @php $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION)); @endphp
                                            <button type="button" onclick="openActionFileModal('{{ asset(trim($path)) }}', '{{ $ext }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg border border-slate-200 transition-colors">
                                                <i class="fa-solid {{ $ext === 'pdf' ? 'fa-file-pdf text-red-500' : 'fa-image text-blue-500' }}"></i>
                                                Show File {{ $idx + 1 }}
                                            </button>
                                        @endforeach
                                    @endif
                                </div>
                                <input type="hidden" name="existing_preventive_photo_one" id="existing_prev_one" value="{{ $action->preventive_path_one ?? '' }}">
                            </div>
                        </div>

                        <!-- Row 2 -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 items-start">
                            <!-- Corrective Row 2 -->
                            <div class="flex flex-col gap-1 w-full">
                                <div class="flex items-center gap-2">
                                    <textarea name="corrective_action_two" required rows="1" {{ $isCorrTwoReadonly ? 'readonly' : '' }} class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm resize-none overflow-hidden autogrow-textarea text-slate-700 {{ $isCorrTwoReadonly ? 'bg-slate-50 text-slate-500 cursor-not-allowed' : '' }}" placeholder="Corrective Action 2...">{{ old('corrective_action_two', $action->corrective_action_two ?? '') }}</textarea>
                                    @if(!$isComplete && !$isCorrTwoApproved)
                                        <div class="shrink-0">
                                            <input type="file" id="corr_two_file" name="corrective_photo_two[]" multiple accept="image/*,application/pdf" class="hidden" onchange="handleActionFiles(this, 'corr_two')">
                                            <button type="button" onclick="document.getElementById('corr_two_file').click()" class="w-10 h-10 border border-dashed border-blue-300 bg-blue-50/50 hover:bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center transition-all relative" title="Take / Upload Photo or PDF">
                                                <i class="fas fa-camera text-xs"></i>
                                                <span class="text-red-500 absolute -top-2 -right-2 text-[10px] font-bold">*</span>
                                            </button>
                                        </div>
                                    @endif
                                    @if($isAuditorReviewing)
                                        <input type="hidden" name="corrective_action_two_verif" id="corrective_action_two_verif" value="">
                                        <div class="flex items-center gap-1 shrink-0 ml-2">
                                            <button type="button" onclick="setFieldVerif('corrective_action_two', 'approve')" id="btn_approve_corrective_action_two" class="w-9 h-9 rounded-lg flex items-center justify-center border transition-all bg-green-50 border-green-200 text-green-600 hover:bg-green-100" title="Approve this row">
                                                <i class="fa-solid fa-check text-xs"></i>
                                            </button>
                                            <button type="button" onclick="setFieldVerif('corrective_action_two', 'reject')" id="btn_reject_corrective_action_two" class="w-9 h-9 rounded-lg flex items-center justify-center border transition-all bg-red-50 border-red-200 text-red-600 hover:bg-red-100" title="Reject this row">
                                                <i class="fa-solid fa-xmark text-xs"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                <div id="corr_two_preview" class="flex flex-wrap gap-2 items-center mt-1.5">
                                    @if(!$isReviewing && !empty($approve->corrective_action_two_verif ?? ''))
                                        @if(($approve->corrective_action_two_verif ?? '') === 'approve')
                                            @if(($car->status ?? '') === 'Need Verification')
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-blue-700 bg-blue-50 rounded-lg border border-blue-200"><i class="fa-solid fa-circle-check"></i> Approved by Superior</span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-green-700 bg-green-50 rounded-lg border border-green-200"><i class="fa-solid fa-circle-check"></i> Approved</span>
                                            @endif
                                        @elseif(($approve->corrective_action_two_verif ?? '') === 'reject')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-red-700 bg-red-50 rounded-lg border border-red-200"><i class="fa-solid fa-circle-xmark"></i> Rejected</span>
                                        @endif
                                        @if($isComplete && !empty($action->corrective_path_two))
                                            <div class="h-6 w-[1px] bg-slate-200 mx-1 self-center shrink-0"></div>
                                        @endif
                                    @endif
                                    @if($isComplete && !empty($action->corrective_path_two))
                                        @foreach(explode(',', $action->corrective_path_two) as $idx => $path)
                                            @php $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION)); @endphp
                                            <button type="button" onclick="openActionFileModal('{{ asset(trim($path)) }}', '{{ $ext }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg border border-slate-200 transition-colors">
                                                <i class="fa-solid {{ $ext === 'pdf' ? 'fa-file-pdf text-red-500' : 'fa-image text-blue-500' }}"></i>
                                                Show File {{ $idx + 1 }}
                                            </button>
                                        @endforeach
                                    @endif
                                </div>
                                <input type="hidden" name="existing_corrective_photo_two" id="existing_corr_two" value="{{ $action->corrective_path_two ?? '' }}">
                            </div>

                            <!-- Preventive Row 2 -->
                            <div class="flex flex-col gap-1 w-full">
                                <div class="flex items-center gap-2">
                                    <textarea name="preventive_action_two" required rows="1" {{ $isPrevTwoReadonly ? 'readonly' : '' }} class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm resize-none overflow-hidden autogrow-textarea text-slate-700 {{ $isPrevTwoReadonly ? 'bg-slate-50 text-slate-500 cursor-not-allowed' : '' }}" placeholder="Preventive Action 2...">{{ old('preventive_action_two', $action->preventive_action_two ?? '') }}</textarea>
                                    @if(!$isComplete && !$isPrevTwoApproved)
                                        <div class="shrink-0">
                                            <input type="file" id="prev_two_file" name="preventive_photo_two[]" multiple accept="image/*,application/pdf" class="hidden" onchange="handleActionFiles(this, 'prev_two')">
                                            <button type="button" onclick="document.getElementById('prev_two_file').click()" class="w-10 h-10 border border-dashed border-blue-300 bg-blue-50/50 hover:bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center transition-all relative" title="Take / Upload Photo or PDF">
                                                <i class="fas fa-camera text-xs"></i>
                                                <span class="text-red-500 absolute -top-2 -right-2 text-[10px] font-bold">*</span>
                                            </button>
                                        </div>
                                    @endif
                                    @if($isAuditorReviewing)
                                        <input type="hidden" name="preventive_action_two_verif" id="preventive_action_two_verif" value="">
                                        <div class="flex items-center gap-1 shrink-0 ml-2">
                                            <button type="button" onclick="setFieldVerif('preventive_action_two', 'approve')" id="btn_approve_preventive_action_two" class="w-9 h-9 rounded-lg flex items-center justify-center border transition-all bg-green-50 border-green-200 text-green-600 hover:bg-green-100" title="Approve this row">
                                                <i class="fa-solid fa-check text-xs"></i>
                                            </button>
                                            <button type="button" onclick="setFieldVerif('preventive_action_two', 'reject')" id="btn_reject_preventive_action_two" class="w-9 h-9 rounded-lg flex items-center justify-center border transition-all bg-red-50 border-red-200 text-red-600 hover:bg-red-100" title="Reject this row">
                                                <i class="fa-solid fa-xmark text-xs"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                <div id="prev_two_preview" class="flex flex-wrap gap-2 items-center mt-1.5">
                                    @if(!$isReviewing && !empty($approve->preventive_action_two_verif ?? ''))
                                        @if(($approve->preventive_action_two_verif ?? '') === 'approve')
                                            @if(($car->status ?? '') === 'Need Verification')
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-blue-700 bg-blue-50 rounded-lg border border-blue-200"><i class="fa-solid fa-circle-check"></i> Approved by Superior</span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-green-700 bg-green-50 rounded-lg border border-green-200"><i class="fa-solid fa-circle-check"></i> Approved</span>
                                            @endif
                                        @elseif(($approve->preventive_action_two_verif ?? '') === 'reject')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-red-700 bg-red-50 rounded-lg border border-red-200"><i class="fa-solid fa-circle-xmark"></i> Rejected</span>
                                        @endif
                                        @if($isComplete && !empty($action->preventive_path_two))
                                            <div class="h-6 w-[1px] bg-slate-200 mx-1 self-center shrink-0"></div>
                                        @endif
                                    @endif
                                    @if($isComplete && !empty($action->preventive_path_two))
                                        @foreach(explode(',', $action->preventive_path_two) as $idx => $path)
                                            @php $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION)); @endphp
                                            <button type="button" onclick="openActionFileModal('{{ asset(trim($path)) }}', '{{ $ext }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg border border-slate-200 transition-colors">
                                                <i class="fa-solid {{ $ext === 'pdf' ? 'fa-file-pdf text-red-500' : 'fa-image text-blue-500' }}"></i>
                                                Show File {{ $idx + 1 }}
                                            </button>
                                        @endforeach
                                    @endif
                                </div>
                                <input type="hidden" name="existing_preventive_photo_two" id="existing_prev_two" value="{{ $action->preventive_path_two ?? '' }}">
                            </div>
                        </div>

                        <!-- Row 3 -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 items-start">
                            <!-- Corrective Row 3 -->
                            <div class="flex flex-col gap-1 w-full">
                                <div class="flex items-center gap-2">
                                    <textarea name="corrective_action_three" required rows="1" {{ $isCorrThreeReadonly ? 'readonly' : '' }} class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm resize-none overflow-hidden autogrow-textarea text-slate-700 {{ $isCorrThreeReadonly ? 'bg-slate-50 text-slate-500 cursor-not-allowed' : '' }}" placeholder="Corrective Action 3...">{{ old('corrective_action_three', $action->corrective_action_three ?? '') }}</textarea>
                                    @if(!$isComplete && !$isCorrThreeApproved)
                                        <div class="shrink-0">
                                            <input type="file" id="corr_three_file" name="corrective_photo_three[]" multiple accept="image/*,application/pdf" class="hidden" onchange="handleActionFiles(this, 'corr_three')">
                                            <button type="button" onclick="document.getElementById('corr_three_file').click()" class="w-10 h-10 border border-dashed border-blue-300 bg-blue-50/50 hover:bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center transition-all relative" title="Take / Upload Photo or PDF">
                                                <i class="fas fa-camera text-xs"></i>
                                                <span class="text-red-500 absolute -top-2 -right-2 text-[10px] font-bold">*</span>
                                            </button>
                                        </div>
                                    @endif
                                    @if($isAuditorReviewing)
                                        <input type="hidden" name="corrective_action_three_verif" id="corrective_action_three_verif" value="">
                                        <div class="flex items-center gap-1 shrink-0 ml-2">
                                            <button type="button" onclick="setFieldVerif('corrective_action_three', 'approve')" id="btn_approve_corrective_action_three" class="w-9 h-9 rounded-lg flex items-center justify-center border transition-all bg-green-50 border-green-200 text-green-600 hover:bg-green-100" title="Approve this row">
                                                <i class="fa-solid fa-check text-xs"></i>
                                            </button>
                                            <button type="button" onclick="setFieldVerif('corrective_action_three', 'reject')" id="btn_reject_corrective_action_three" class="w-9 h-9 rounded-lg flex items-center justify-center border transition-all bg-red-50 border-red-200 text-red-600 hover:bg-red-100" title="Reject this row">
                                                <i class="fa-solid fa-xmark text-xs"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                <div id="corr_three_preview" class="flex flex-wrap gap-2 items-center mt-1.5">
                                    @if(!$isReviewing && !empty($approve->corrective_action_three_verif ?? ''))
                                        @if(($approve->corrective_action_three_verif ?? '') === 'approve')
                                            @if(($car->status ?? '') === 'Need Verification')
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-blue-700 bg-blue-50 rounded-lg border border-blue-200"><i class="fa-solid fa-circle-check"></i> Approved by Superior</span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-green-700 bg-green-50 rounded-lg border border-green-200"><i class="fa-solid fa-circle-check"></i> Approved</span>
                                            @endif
                                        @elseif(($approve->corrective_action_three_verif ?? '') === 'reject')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-red-700 bg-red-50 rounded-lg border border-red-200"><i class="fa-solid fa-circle-xmark"></i> Rejected</span>
                                        @endif
                                        @if($isComplete && !empty($action->corrective_path_three))
                                            <div class="h-6 w-[1px] bg-slate-200 mx-1 self-center shrink-0"></div>
                                        @endif
                                    @endif
                                    @if($isComplete && !empty($action->corrective_path_three))
                                        @foreach(explode(',', $action->corrective_path_three) as $idx => $path)
                                            @php $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION)); @endphp
                                            <button type="button" onclick="openActionFileModal('{{ asset(trim($path)) }}', '{{ $ext }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg border border-slate-200 transition-colors">
                                                <i class="fa-solid {{ $ext === 'pdf' ? 'fa-file-pdf text-red-500' : 'fa-image text-blue-500' }}"></i>
                                                Show File {{ $idx + 1 }}
                                            </button>
                                        @endforeach
                                    @endif
                                </div>
                                <input type="hidden" name="existing_corrective_photo_three" id="existing_corr_three" value="{{ $action->corrective_path_three ?? '' }}">
                            </div>

                            <!-- Preventive Row 3 -->
                            <div class="flex flex-col gap-1 w-full">
                                <div class="flex items-center gap-2">
                                    <textarea name="preventive_action_three" required rows="1" {{ $isPrevThreeReadonly ? 'readonly' : '' }} class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm resize-none overflow-hidden autogrow-textarea text-slate-700 {{ $isPrevThreeReadonly ? 'bg-slate-50 text-slate-500 cursor-not-allowed' : '' }}" placeholder="Preventive Action 3...">{{ old('preventive_action_three', $action->preventive_action_three ?? '') }}</textarea>
                                    @if(!$isComplete && !$isPrevThreeApproved)
                                        <div class="shrink-0">
                                            <input type="file" id="prev_three_file" name="preventive_photo_three[]" multiple accept="image/*,application/pdf" class="hidden" onchange="handleActionFiles(this, 'prev_three')">
                                            <button type="button" onclick="document.getElementById('prev_three_file').click()" class="w-10 h-10 border border-dashed border-blue-300 bg-blue-50/50 hover:bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center transition-all relative" title="Take / Upload Photo or PDF">
                                                <i class="fas fa-camera text-xs"></i>
                                                <span class="text-red-500 absolute -top-2 -right-2 text-[10px] font-bold">*</span>
                                            </button>
                                        </div>
                                    @endif
                                    @if($isAuditorReviewing)
                                        <input type="hidden" name="preventive_action_three_verif" id="preventive_action_three_verif" value="">
                                        <div class="flex items-center gap-1 shrink-0 ml-2">
                                            <button type="button" onclick="setFieldVerif('preventive_action_three', 'approve')" id="btn_approve_preventive_action_three" class="w-9 h-9 rounded-lg flex items-center justify-center border transition-all bg-green-50 border-green-200 text-green-600 hover:bg-green-100" title="Approve this row">
                                                <i class="fa-solid fa-check text-xs"></i>
                                            </button>
                                            <button type="button" onclick="setFieldVerif('preventive_action_three', 'reject')" id="btn_reject_preventive_action_three" class="w-9 h-9 rounded-lg flex items-center justify-center border transition-all bg-red-50 border-red-200 text-red-600 hover:bg-red-100" title="Reject this row">
                                                <i class="fa-solid fa-xmark text-xs"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                <div id="prev_three_preview" class="flex flex-wrap gap-2 items-center mt-1.5">
                                    @if(!$isReviewing && !empty($approve->preventive_action_three_verif ?? ''))
                                        @if(($approve->preventive_action_three_verif ?? '') === 'approve')
                                            @if(($car->status ?? '') === 'Need Verification')
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-blue-700 bg-blue-50 rounded-lg border border-blue-200"><i class="fa-solid fa-circle-check"></i> Approved by Superior</span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-green-700 bg-green-50 rounded-lg border border-green-200"><i class="fa-solid fa-circle-check"></i> Approved</span>
                                            @endif
                                        @elseif(($approve->preventive_action_three_verif ?? '') === 'reject')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-red-700 bg-red-50 rounded-lg border border-red-200"><i class="fa-solid fa-circle-xmark"></i> Rejected</span>
                                        @endif
                                        @if($isComplete && !empty($action->preventive_path_three))
                                            <div class="h-6 w-[1px] bg-slate-200 mx-1 self-center shrink-0"></div>
                                        @endif
                                    @endif
                                    @if($isComplete && !empty($action->preventive_path_three))
                                        @foreach(explode(',', $action->preventive_path_three) as $idx => $path)
                                            @php $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION)); @endphp
                                            <button type="button" onclick="openActionFileModal('{{ asset(trim($path)) }}', '{{ $ext }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg border border-slate-200 transition-colors">
                                                <i class="fa-solid {{ $ext === 'pdf' ? 'fa-file-pdf text-red-500' : 'fa-image text-blue-500' }}"></i>
                                                Show File {{ $idx + 1 }}
                                            </button>
                                        @endforeach
                                    @endif
                                </div>
                                <input type="hidden" name="existing_preventive_photo_three" id="existing_prev_three" value="{{ $action->preventive_path_three ?? '' }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes & Signatures -->
                <div class="border-t border-slate-100 pt-6">
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 sm:gap-6">
                        @php
                            $isAuditorUser = false;
                            if (!empty($car->auditor)) {
                                $auditors = array_map('trim', explode(',', $car->auditor));
                                foreach ($auditors as $auditorName) {
                                    if (strcasecmp(Auth::user()->full_name, $auditorName) === 0) {
                                        $isAuditorUser = true;
                                        break;
                                    }
                                }
                            }
                            $isQmr = in_array(Auth::user()->username, ['031114-001', '260422-001', '121020-002']);
                            // Notes is editable only if:
                            // 1. Action is completed (i.e. status is no longer Draft)
                            // 2. CAR status is 'Need Verification' (meaning superior has already verified it) and user is Auditor
                            // 3. CAR status is 'Closed' (waiting for final verif) and user is QMR
                            $isNotesEditable = ($isComplete && (($car->status ?? '') === 'Need Verification') && $isAuditorUser)
                                || ($isComplete && (($car->status ?? '') === 'Closed' && empty($car->qmr_approved_at)) && $isQmr);
                        @endphp
                        <!-- Notes for A & B -->
                        <div class="flex flex-col gap-1.5 sm:col-span-2">
                            <label class="text-slate-700 font-semibold text-sm tracking-wider">Notes for A & B @if($isNotesEditable)<span class="text-red-500">*</span>@endif</label>
                            <textarea name="notes" rows="1" {{ $isNotesEditable ? '' : 'readonly' }} class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none text-slate-700 resize-none overflow-hidden autogrow-textarea {{ !$isNotesEditable ? 'bg-slate-50 text-slate-500 cursor-not-allowed' : '' }}" placeholder="{{ $isNotesEditable ? 'Enter verification notes...' : 'Notes can only be filled by the Auditor during verification...' }}">{{ old('notes', $action->notes ?? '') }}</textarea>
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

                    <!-- Signature Stamps Status Block -->
                    <div class="mt-8 grid grid-cols-4 gap-1.5 sm:gap-4 border-t border-slate-100 pt-6">
                        <!-- Prepare by -->
                        <div class="flex flex-col items-center justify-between p-1 sm:p-4 rounded-lg sm:rounded-xl border border-slate-200 bg-slate-50/50 text-center min-h-[90px] sm:min-h-[140px]">
                            <span class="text-[8px] sm:text-xs font-semibold text-slate-500 tracking-wider">Prepare by</span>
                            @if(isset($action) && !empty($action->auditee_name) && $isComplete)
                                <div class="my-0.5 sm:my-2 select-none">
                                    <div class="inline-flex items-center border-[0.5px] sm:border-2 border-red-500 font-bold uppercase tracking-widest text-[6px] sm:text-sm bg-white overflow-hidden">
                                        <div class="px-0.5 sm:px-2 py-0.5 border-r-[0.5px] sm:border-r-2 border-red-500 text-red-500">
                                            <i class="fa-solid fa-check text-[5px] sm:text-xs" style="-webkit-text-stroke: 0.5px currentColor;"></i>
                                        </div>
                                        <div class="px-0.5 sm:px-2 py-0.5 text-red-500">
                                            PREPARED
                                        </div>
                                    </div>
                                </div>
                                <div class="text-[8px] sm:text-xs">
                                    <p class="font-bold text-slate-700 truncate max-w-[55px] sm:max-w-none" title="{{ $action->auditee_name }}">{{ $action->auditee_name }}</p>
                                    <p class="text-slate-400 text-[7px] sm:text-[10px] mt-0.5">{{ \Carbon\Carbon::parse($action->created_at)->format('d/m/Y') }}</p>
                                </div>
                            @else
                                <div class="my-0.5 sm:my-2 select-none">
                                    <div class="inline-flex items-center border-[0.5px] sm:border-2 border-slate-300 font-bold uppercase tracking-widest text-[6px] sm:text-sm bg-white overflow-hidden">
                                        <div class="px-0.5 sm:px-2 py-0.5 border-r-[0.5px] sm:border-r-2 border-slate-300 text-slate-400">
                                            <i class="fa-solid fa-clock text-[5px] sm:text-[11px]"></i>
                                        </div>
                                        <div class="px-0.5 sm:px-2 py-0.5 text-slate-400">
                                            PENDING
                                        </div>
                                    </div>
                                </div>
                                <div class="text-[8px] sm:text-xs">
                                    <p class="text-slate-400 font-medium truncate max-w-[55px] sm:max-w-none">{{ $action->auditee_name ?? $car->header_auditee ?? $car->auditee ?? '-' }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- Checked by -->
                        <div class="flex flex-col items-center justify-between p-1 sm:p-4 rounded-lg sm:rounded-xl border border-slate-200 bg-slate-50/50 text-center min-h-[90px] sm:min-h-[140px]">
                            <span class="text-[8px] sm:text-xs font-semibold text-slate-500 tracking-wider">Checked by</span>
                            @php
                                $isVerifiedBySuperior = isset($action) && (in_array($action->action_status, ['approve_superior', 'verified']) || !empty($approve->superior_approved_at ?? ''));
                            @endphp
                            @if($isVerifiedBySuperior && !empty($action->auditee_superior_name))
                                <div class="my-0.5 sm:my-2 select-none">
                                    <div class="inline-flex items-center border-[0.5px] sm:border-2 border-red-500 font-bold uppercase tracking-widest text-[6px] sm:text-sm bg-white overflow-hidden">
                                        <div class="px-0.5 sm:px-2 py-0.5 border-r-[0.5px] sm:border-r-2 border-red-500 text-red-500">
                                            <i class="fa-solid fa-check text-[5px] sm:text-xs" style="-webkit-text-stroke: 0.5px currentColor;"></i>
                                        </div>
                                        <div class="px-0.5 sm:px-2 py-0.5 text-red-500">
                                            CHECKED
                                        </div>
                                    </div>
                                </div>
                                <div class="text-[8px] sm:text-xs">
                                    <p class="font-bold text-slate-700 truncate max-w-[55px] sm:max-w-none" title="{{ $action->auditee_superior_name }}">{{ $action->auditee_superior_name }}</p>
                                    <p class="text-slate-400 text-[7px] sm:text-[10px] mt-0.5">
                                        {{ !empty($approve->superior_approved_at ?? '') ? \Carbon\Carbon::parse($approve->superior_approved_at)->format('d/m/Y') : '' }}
                                    </p>
                                </div>
                            @else
                                <div class="my-0.5 sm:my-2 select-none">
                                    <div class="inline-flex items-center border-[0.5px] sm:border-2 border-slate-300 font-bold uppercase tracking-widest text-[6px] sm:text-sm bg-white overflow-hidden">
                                        <div class="px-0.5 sm:px-2 py-0.5 border-r-[0.5px] sm:border-r-2 border-slate-300 text-slate-400">
                                            <i class="fa-solid fa-clock text-[5px] sm:text-[11px]"></i>
                                        </div>
                                        <div class="px-0.5 sm:px-2 py-0.5 text-slate-400">
                                            PENDING
                                        </div>
                                    </div>
                                </div>
                                <div class="text-[8px] sm:text-xs">
                                    <p class="text-slate-400 font-medium truncate max-w-[55px] sm:max-w-none">{{ !empty($action->auditee_superior_name) ? $action->auditee_superior_name : 'Superior' }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- Confirm by -->
                        <div class="flex flex-col items-center justify-between p-1 sm:p-4 rounded-lg sm:rounded-xl border border-slate-200 bg-slate-50/50 text-center min-h-[90px] sm:min-h-[140px]">
                            <span class="text-[8px] sm:text-xs font-semibold text-slate-500 tracking-wider">Confirm by</span>
                            @php
                                $isConfirmedByAuditor = isset($action) && ($action->action_status === 'verified' || !empty($approve->auditor_approved_at ?? '') || ($car->status ?? '') === 'Closed');
                            @endphp
                            @if($isConfirmedByAuditor && !empty($car->auditor))
                                <div class="my-0.5 sm:my-2 select-none">
                                    <div class="inline-flex items-center border-[0.5px] sm:border-2 border-red-500 font-bold uppercase tracking-widest text-[6px] sm:text-sm bg-white overflow-hidden">
                                        <div class="px-0.5 sm:px-2 py-0.5 border-r-[0.5px] sm:border-r-2 border-red-500 text-red-500">
                                            <i class="fa-solid fa-check text-[5px] sm:text-xs" style="-webkit-text-stroke: 0.5px currentColor;"></i>
                                        </div>
                                        <div class="px-0.5 sm:px-2 py-0.5 text-red-500">
                                            CONFIRMED
                                        </div>
                                    </div>
                                </div>
                                <div class="text-[8px] sm:text-xs">
                                    <p class="font-bold text-slate-700 truncate max-w-[55px] sm:max-w-none" title="{{ $car->auditor }}">{{ $car->auditor }}</p>
                                    <p class="text-slate-400 text-[7px] sm:text-[10px] mt-0.5">
                                        {{ !empty($approve->auditor_approved_at ?? '') ? \Carbon\Carbon::parse($approve->auditor_approved_at)->format('d/m/Y') : '' }}
                                    </p>
                                </div>
                            @else
                                <div class="my-0.5 sm:my-2 select-none">
                                    <div class="inline-flex items-center border-[0.5px] sm:border-2 border-slate-300 font-bold uppercase tracking-widest text-[6px] sm:text-sm bg-white overflow-hidden">
                                        <div class="px-0.5 sm:px-2 py-0.5 border-r-[0.5px] sm:border-r-2 border-slate-300 text-slate-400">
                                            <i class="fa-solid fa-clock text-[5px] sm:text-[11px]"></i>
                                        </div>
                                        <div class="px-0.5 sm:px-2 py-0.5 text-slate-400">
                                            PENDING
                                        </div>
                                    </div>
                                </div>
                                <div class="text-[8px] sm:text-xs">
                                    <p class="text-slate-400 font-medium truncate max-w-[55px] sm:max-w-none">{{ $car->auditor ?? '-' }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- Known by -->
                        <div class="flex flex-col items-center justify-between p-1 sm:p-4 rounded-lg sm:rounded-xl border border-slate-200 bg-slate-50/50 text-center min-h-[90px] sm:min-h-[140px]">
                            <span class="text-[8px] sm:text-xs font-semibold text-slate-500 tracking-wider">Known by</span>
                            @php
                                $isApprovedByQmr = (!empty($approve) && !empty($approve->qmr_approved_at)) || !empty($car->qmr_approved_at);
                            @endphp
                            @if($isApprovedByQmr)
                                <div class="my-0.5 sm:my-2 select-none">
                                    <div class="inline-flex items-center border-[0.5px] sm:border-2 border-red-500 font-bold uppercase tracking-widest text-[6px] sm:text-sm bg-white overflow-hidden">
                                        <div class="px-0.5 sm:px-2 py-0.5 border-r-[0.5px] sm:border-r-2 border-red-500 text-red-500">
                                            <i class="fa-solid fa-check text-[5px] sm:text-xs" style="-webkit-text-stroke: 0.5px currentColor;"></i>
                                        </div>
                                        <div class="px-0.5 sm:px-2 py-0.5 text-red-500">
                                            APPROVED
                                        </div>
                                    </div>
                                </div>
                                <div class="text-[8px] sm:text-xs">
                                    <p class="font-bold text-slate-700 truncate max-w-[55px] sm:max-w-none" title="{{ $qmrUser->full_name ?? 'PAK ARIF' }}">{{ $qmrUser->full_name ?? 'PAK ARIF' }}</p>
                                    <p class="text-slate-400 text-[7px] sm:text-[10px] mt-0.5">{{ !empty($approve->qmr_approved_at ?? '') ? \Carbon\Carbon::parse($approve->qmr_approved_at)->format('d/m/Y') : (!empty($car->qmr_approved_at) ? \Carbon\Carbon::parse($car->qmr_approved_at)->format('d/m/Y') : '') }}</p>
                                </div>
                            @else
                                <div class="my-0.5 sm:my-2 select-none">
                                    <div class="inline-flex items-center border-[0.5px] sm:border-2 border-slate-300 font-bold uppercase tracking-widest text-[6px] sm:text-sm bg-white overflow-hidden">
                                        <div class="px-0.5 sm:px-2 py-0.5 border-r-[0.5px] sm:border-r-2 border-slate-300 text-slate-400">
                                            <i class="fa-solid fa-clock text-[5px] sm:text-[11px]"></i>
                                        </div>
                                        <div class="px-0.5 sm:px-2 py-0.5 text-slate-400">
                                            PENDING
                                        </div>
                                    </div>
                                </div>
                                <div class="text-[8px] sm:text-xs">
                                    <p class="text-slate-400 font-medium">QMR</p>
                                </div>
                            @endif
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
                            $isQmr = in_array(Auth::user()->username, ['031114-001', '260422-001', '121020-002']);
                            $isSuperior = isset($action) && strcasecmp(Auth::user()->full_name, $action->auditee_superior_name ?? '') === 0;
                            
                            $showActionButtons = false;
                            if ($isSuperior && ($car->status ?? '') === 'Under Review') {
                                $showActionButtons = true;
                            } elseif ($isAuditor && ($car->status ?? '') === 'Need Verification') {
                                $showActionButtons = true;
                            } elseif ($isQmr && ($car->status ?? '') === 'Closed' && empty($car->qmr_approved_at)) {
                                $showActionButtons = true;
                            }
                        @endphp
                        @if($showActionButtons)
                            @if(($car->status ?? '') === 'Need Verification' || ($car->status ?? '') === 'Closed')
                                @if(($car->status ?? '') === 'Need Verification')
                                    <button type="button" id="btnSaveVerification" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-all text-sm flex items-center gap-2">
                                        <i class="fa-solid fa-floppy-disk text-base"></i> Save
                                    </button>
                                @endif
                                <button type="button" id="btnApproveAction" class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold transition-all text-sm flex items-center gap-2">
                                    <i class="fa-solid fa-check text-base"></i> Approve
                                </button>
                            @else
                                <button type="button" id="btnApproveAction" class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold transition-all text-sm flex items-center gap-2">
                                    <i class="fa-solid fa-check text-base"></i> Approve
                                </button>
                            @endif
                            @if(($car->status ?? '') === 'Under Review' || ($car->status ?? '') === 'Closed')
                                <button type="button" id="btnRejectAction" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold transition-all text-sm flex items-center gap-2">
                                    <i class="fa-solid fa-xmark text-base"></i> Reject
                                </button>
                            @endif
                        @endif
                        @php
                            $showRollback = false;
                            if (($car->status ?? '') === 'Closed' && !empty($car->qmr_approved_at) && ($isAuditor || $isQmr)) {
                                $showRollback = true;
                            }
                        @endphp
                        @if($showRollback)
                            <button type="button" id="btnRollback" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-bold transition-all text-sm flex items-center gap-2">
                                <i class="fa-solid fa-rotate-left text-base"></i> Rollback
                            </button>
                        @endif
                        @php
                            $isAuditee = false;
                            if (!empty($car->auditee)) {
                                $auditees = array_map('trim', explode(',', $car->auditee));
                                foreach ($auditees as $auditeeName) {
                                    if (strcasecmp(Auth::user()->full_name, $auditeeName) === 0) {
                                        $isAuditee = true;
                                        break;
                                    }
                                }
                            }
                            $showAuditeeRollback = false;
                            if ($isAuditee && ($car->status ?? '') === 'Under Review') {
                                $showAuditeeRollback = true;
                            }
                        @endphp
                        @if($showAuditeeRollback)
                            <button type="button" id="btnAuditeeRollback" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-bold transition-all text-sm flex items-center gap-2">
                                <i class="fa-solid fa-rotate-left text-base"></i> Cancel submission & edit
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
    const assetUrl = "{{ asset('') }}";

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
            const isNeedVerification = "{{ ($car->status ?? '') === 'Need Verification' }}" === "1";
            if (isNeedVerification) {
                const hasReject = ['corrective_action_one', 'corrective_action_two', 'corrective_action_three', 'preventive_action_one', 'preventive_action_two', 'preventive_action_three']
                    .some(field => document.getElementById(field + '_verif')?.value === 'reject');
                if (hasReject) {
                    showToast('Cannot approve and close because some items are rejected. Please click the Save button to submit rejections.', 'warning');
                    return;
                }
            }
            modalTitle.innerText = 'Confirm Approval';
            modalMessage.innerHTML = 'Are you sure you want to approve this Action Plan?<br>This will mark the CAR as Closed.';
            modalIcon.className = 'w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-5';
            modalIcon.innerHTML = `<svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>`;
            confirmBtn.className = 'px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-xl transition-colors text-sm';
            confirmBtn.innerText = 'Yes, Approve';
        } else if (action === 'save_verif') {
            modalTitle.innerText = 'Confirm Verification';
            modalMessage.innerHTML = 'Are you sure you want to save this verification?<br>Rejected items will be returned to draft for Auditee correction, and approved items will remain locked.';
            modalIcon.className = 'w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-5';
            modalIcon.innerHTML = `<svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>`;
            confirmBtn.className = 'px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition-colors text-sm';
            confirmBtn.innerText = 'Yes, Save';
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
        } else if (action === 'auditee_rollback') {
            modalTitle.innerText = 'Edit Action Plan';
            modalMessage.innerHTML = 'Are you sure you want to cancel submission and edit this action plan?<br>This will return the status to Draft.';
            modalIcon.className = 'w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-5';
            modalIcon.innerHTML = `<svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 0 1 8 8v2M3 10l6 6m-6-6l6-6"></path></svg>`;
            confirmBtn.className = 'px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-medium rounded-xl transition-colors text-sm';
            confirmBtn.innerText = 'Yes, Edit';
        }

        modal.classList.remove('hidden');
    }

    function submitConfirmation() {
        if (!currentAction) return;

        if (currentAction === 'approve' || currentAction === 'save_verif') {
            const notesInput = document.querySelector('textarea[name="notes"]');
            if (notesInput && !notesInput.hasAttribute('readonly') && (!notesInput.value || notesInput.value.trim() === '')) {
                showToast('Notes for A & B is required.', 'warning');
                return;
            }

            const role = "{{ ($car->status ?? '') === 'Need Verification' ? 'auditor' : (($car->status ?? '') === 'Closed' ? 'closed' : 'superior') }}";
            if (role === 'auditor') {
                const fields = [
                    { id: 'corrective_action_one_verif', name: 'Corrective Action 1' },
                    { id: 'corrective_action_two_verif', name: 'Corrective Action 2' },
                    { id: 'corrective_action_three_verif', name: 'Corrective Action 3' },
                    { id: 'preventive_action_one_verif', name: 'Preventive Action 1' },
                    { id: 'preventive_action_two_verif', name: 'Preventive Action 2' },
                    { id: 'preventive_action_three_verif', name: 'Preventive Action 3' },
                    { id: 'root_cause_verif', name: 'Root Cause' }
                ];
                for (const field of fields) {
                    const el = document.getElementById(field.id);
                    if (el && (!el.value || el.value.trim() === '')) {
                        showToast(`Please verify: ${field.name} has not been verified yet.`, 'warning');
                        return;
                    }
                }
            }
        }

        const confirmBtn = document.getElementById('confirmBtn');
        const originalText = confirmBtn.innerText;
        confirmBtn.disabled = true;
        confirmBtn.innerText = 'Processing...';

        let url = '';
        let payload = {};

        if (currentAction === 'approve' || currentAction === 'save_verif') {
            url = "{{ route('internal_audit.cars.approve') }}";
            payload = {
                _token: "{{ csrf_token() }}",
                car_id: {{ $car->id }},
                role: "{{ ($car->status ?? '') === 'Need Verification' ? 'auditor' : (($car->status ?? '') === 'Closed' ? 'closed' : 'superior') }}",
                notes: document.querySelector('textarea[name="notes"]').value,
                corrective_action_one_verif: document.getElementById('corrective_action_one_verif')?.value || null,
                corrective_action_two_verif: document.getElementById('corrective_action_two_verif')?.value || null,
                corrective_action_three_verif: document.getElementById('corrective_action_three_verif')?.value || null,
                preventive_action_one_verif: document.getElementById('preventive_action_one_verif')?.value || null,
                preventive_action_two_verif: document.getElementById('preventive_action_two_verif')?.value || null,
                preventive_action_three_verif: document.getElementById('preventive_action_three_verif')?.value || null,
                root_cause_verif: document.getElementById('root_cause_verif')?.value || null,
            };
        } else if (currentAction === 'reject') {
            url = "{{ route('internal_audit.cars.reject') }}";
            payload = {
                _token: "{{ csrf_token() }}",
                car_id: {{ $car->id }},
                notes: document.querySelector('textarea[name="notes"]').value
            };
        } else if (currentAction === 'rollback' || currentAction === 'auditee_rollback') {
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

        // Auditee Rollback Handler
        const btnAuditeeRollback = document.getElementById('btnAuditeeRollback');
        if (btnAuditeeRollback) {
            btnAuditeeRollback.addEventListener('click', function() {
                openConfirmationModal('auditee_rollback');
            });
        }

        // Save Verification Handler
        const btnSaveVerif = document.getElementById('btnSaveVerification');
        if (btnSaveVerif) {
            btnSaveVerif.addEventListener('click', function() {
                openConfirmationModal('save_verif');
            });
        }

        // Approve Action Plan Handler
        const btnApprove = document.getElementById('btnApproveAction');
        if (btnApprove) {
            btnApprove.addEventListener('click', function() {
                // Auto-approve all fields!
                const fields = [
                    'corrective_action_one',
                    'corrective_action_two',
                    'corrective_action_three',
                    'preventive_action_one',
                    'preventive_action_two',
                    'preventive_action_three',
                    'root_cause'
                ];
                fields.forEach(field => {
                    setFieldVerif(field, 'approve');
                });
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

                // Validate mandatory file uploads
                const requiredFiles = [
                    { key: 'root_cause', name: 'Root Cause' },
                    { key: 'corr_one', name: 'Corrective Action 1' },
                    { key: 'corr_two', name: 'Corrective Action 2' },
                    { key: 'corr_three', name: 'Corrective Action 3' },
                    { key: 'prev_one', name: 'Preventive Action 1' },
                    { key: 'prev_two', name: 'Preventive Action 2' },
                    { key: 'prev_three', name: 'Preventive Action 3' }
                ];

                for (const item of requiredFiles) {
                    const state = actionFilesState[item.key];
                    if (!state || (state.files.length === 0 && state.existing.length === 0)) {
                        showToast(`Upload file untuk ${item.name} wajib diisi.`, 'error');
                        return;
                    }
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

        window.setFieldVerif = function(fieldName, status) {
            const input = document.getElementById(fieldName + '_verif');
            if (!input) return;
            
            const approveBtn = document.getElementById('btn_approve_' + fieldName);
            const rejectBtn = document.getElementById('btn_reject_' + fieldName);
            
            if (status === 'approve') {
                input.value = 'approve';
                if (approveBtn) approveBtn.className = "w-9 h-9 rounded-lg flex items-center justify-center border transition-all bg-green-600 border-green-600 text-white shadow-sm";
                if (rejectBtn) rejectBtn.className = "w-9 h-9 rounded-lg flex items-center justify-center border transition-all bg-red-50 border-red-200 text-red-600 hover:bg-red-100";
            } else {
                input.value = 'reject';
                if (rejectBtn) rejectBtn.className = "w-9 h-9 rounded-lg flex items-center justify-center border transition-all bg-red-600 border-red-600 text-white shadow-sm";
                if (approveBtn) approveBtn.className = "w-9 h-9 rounded-lg flex items-center justify-center border transition-all bg-green-50 border-green-200 text-green-600 hover:bg-green-100";
            }
        };
        const actionFilesState = {
            corr_one: { files: [], existing: {!! json_encode(array_filter(explode(',', $action->corrective_path_one ?? ''))) !!} },
            corr_two: { files: [], existing: {!! json_encode(array_filter(explode(',', $action->corrective_path_two ?? ''))) !!} },
            corr_three: { files: [], existing: {!! json_encode(array_filter(explode(',', $action->corrective_path_three ?? ''))) !!} },
            prev_one: { files: [], existing: {!! json_encode(array_filter(explode(',', $action->preventive_path_one ?? ''))) !!} },
            prev_two: { files: [], existing: {!! json_encode(array_filter(explode(',', $action->preventive_path_two ?? ''))) !!} },
            prev_three: { files: [], existing: {!! json_encode(array_filter(explode(',', $action->preventive_path_three ?? ''))) !!} },
            root_cause: { files: [], existing: {!! json_encode(array_filter(explode(',', $action->root_cause_path ?? ''))) !!} }
        };

        window.handleActionFiles = function(input, key) {
            if (input.files && input.files.length > 0) {
                const newFiles = Array.from(input.files);
                actionFilesState[key].files = actionFilesState[key].files.concat(newFiles);
                syncActionFiles(key);
                renderActionPreviews(key);
            }
        };

        function syncActionFiles(key) {
            const fileInput = document.getElementById(key + '_file');
            if (!fileInput) return;
            const dataTransfer = new DataTransfer();
            actionFilesState[key].files.forEach(file => dataTransfer.items.add(file));
            fileInput.files = dataTransfer.files;
        }

        window.removeActionFile = function(key, index, isExisting) {
            if (isExisting) {
                actionFilesState[key].existing.splice(index, 1);
                const hiddenInputMap = {
                    corr_one: 'existing_corr_one',
                    corr_two: 'existing_corr_two',
                    corr_three: 'existing_corr_three',
                    prev_one: 'existing_prev_one',
                    prev_two: 'existing_prev_two',
                    prev_three: 'existing_prev_three',
                    root_cause: 'existing_root_cause'
                };
                const hiddenId = hiddenInputMap[key];
                if (hiddenId) {
                    document.getElementById(hiddenId).value = actionFilesState[key].existing.join(',');
                }
            } else {
                actionFilesState[key].files.splice(index, 1);
                syncActionFiles(key);
            }
            renderActionPreviews(key);
        };

        function renderActionPreviews(key) {
            const container = document.getElementById(key + '_preview');
            if (!container) return;
            container.innerHTML = '';

            const state = actionFilesState[key];

            // Render existing photos
            state.existing.forEach((path, idx) => {
                const ext = path.split('.').pop().toLowerCase();
                const wrapper = document.createElement('div');
                wrapper.className = "relative w-12 h-12 bg-slate-100 border border-slate-200 rounded-lg group cursor-pointer flex items-center justify-center";
                wrapper.onclick = function() {
                    openActionFileModal(assetUrl + path, ext);
                };
                
                if (ext === 'pdf') {
                    const icon = document.createElement('i');
                    icon.className = "fa-solid fa-file-pdf text-red-500 text-lg";
                    wrapper.appendChild(icon);
                } else {
                    const img = document.createElement('img');
                    img.src = assetUrl + path;
                    img.className = "w-full h-full object-cover rounded-lg";
                    wrapper.appendChild(img);
                }
                
                const btn = document.createElement('button');
                btn.type = "button";
                btn.className = "absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-3.5 h-3.5 flex items-center justify-center text-[9px] font-bold hover:bg-red-600 transition-colors z-20";
                btn.innerHTML = "×";
                btn.onclick = function(e) {
                    e.stopPropagation();
                    removeActionFile(key, idx, true);
                };
                
                wrapper.appendChild(btn);
                container.appendChild(wrapper);
            });

            // Render new files
            state.files.forEach((file, idx) => {
                const ext = file.name.split('.').pop().toLowerCase();
                const wrapper = document.createElement('div');
                wrapper.className = "relative w-12 h-12 bg-slate-100 border border-slate-200 rounded-lg group cursor-pointer flex items-center justify-center";
                
                if (ext === 'pdf') {
                    const icon = document.createElement('i');
                    icon.className = "fa-solid fa-file-pdf text-red-500 text-lg";
                    wrapper.appendChild(icon);
                    
                    wrapper.onclick = function() {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            openActionFileModal(e.target.result, 'pdf');
                        };
                        reader.readAsDataURL(file);
                    };
                } else {
                    const img = document.createElement('img');
                    img.className = "w-full h-full object-cover rounded-lg";
                    wrapper.appendChild(img);
                    
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        img.src = e.target.result;
                        wrapper.onclick = function() {
                            openActionFileModal(e.target.result, ext);
                        };
                    };
                    reader.readAsDataURL(file);
                }
                
                const btn = document.createElement('button');
                btn.type = "button";
                btn.className = "absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-3.5 h-3.5 flex items-center justify-center text-[9px] font-bold hover:bg-red-600 transition-colors z-20";
                btn.innerHTML = "×";
                btn.onclick = function(evt) {
                    evt.stopPropagation();
                    removeActionFile(key, idx, false);
                };
                
                wrapper.appendChild(btn);
                container.appendChild(wrapper);
            });
        }

        // Initial render for edit mode
        @if(!$isComplete)
            Object.keys(actionFilesState).forEach(key => {
                renderActionPreviews(key);
            });
        @endif

        let currentFileUrl = '';
        let currentFileType = '';

        // File Viewer Modal Handlers
        window.openActionFileModal = function(url, ext) {
            currentFileUrl = url;
            currentFileType = ext;

            const modal = document.getElementById('actionFileModal');
            const img = document.getElementById('actionFileImg');
            const iframe = document.getElementById('actionFileIframe');
            
            img.classList.add('hidden');
            iframe.classList.add('hidden');
            img.src = '';
            iframe.src = '';
            
            if (ext === 'pdf') {
                iframe.src = url;
                iframe.classList.remove('hidden');
            } else {
                img.src = url;
                img.classList.remove('hidden');
            }
            
            modal.classList.remove('hidden');
        };

        window.closeActionFileModal = function() {
            const modal = document.getElementById('actionFileModal');
            modal.classList.add('hidden');
            document.getElementById('actionFileImg').src = '';
            document.getElementById('actionFileIframe').src = '';
            currentFileUrl = '';
            currentFileType = '';
        };

        window.handleActionFullscreen = function() {
            if (!currentFileUrl) return;
            
            if (currentFileType === 'pdf') {
                window.open(currentFileUrl, '_blank');
            } else {
                const img = document.getElementById('actionFileImg');
                if (img) {
                    if (img.viewer) {
                        img.viewer.destroy();
                    }
                    img.viewer = new Viewer(img, {
                        title: false,
                        navbar: false,
                        toolbar: {
                            zoomIn: 1, zoomOut: 1, oneToOne: 1, reset: 1,
                            prev: 0, play: 0, next: 0, rotateLeft: 1, rotateRight: 1,
                            flipHorizontal: 1, flipVertical: 1
                        }
                    });
                    img.viewer.show();
                }
            }
        };

        // Initialize ViewerJS on the images container
        const container = document.getElementById('finding_images_container');
        if (typeof Viewer !== 'undefined' && container) {
            new Viewer(container, {
                title: false,
                navbar: false,
                toolbar: {
                    zoomIn: 1, zoomOut: 1, oneToOne: 1, reset: 1,
                    prev: 0, play: 0, next: 0, rotateLeft: 1, rotateRight: 1,
                    flipHorizontal: 1, flipVertical: 1,
                }
            });
        }
    });
</script>

<!-- Action File Viewer Modal -->
<div id="actionFileModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-slate-900 bg-opacity-70" onclick="closeActionFileModal()"></div>

        <!-- Centering trick -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-slate-100 flex justify-between items-center">
                <h3 class="text-base font-semibold leading-6 text-slate-900" id="actionFileModalTitle">
                    File Preview
                </h3>
                <button type="button" onclick="closeActionFileModal()" class="text-slate-400 hover:text-slate-500 text-3xl font-light leading-none">
                    &times;
                </button>
            </div>
            <div class="bg-slate-50 px-4 py-4 sm:p-6 flex items-center justify-center min-h-[400px] max-h-[600px] overflow-auto">
                <img id="actionFileImg" src="" class="hidden max-w-full max-h-[500px] object-contain rounded-lg shadow-sm">
                <iframe id="actionFileIframe" src="" class="hidden w-full h-[500px] rounded-lg border border-slate-200 bg-white" frameborder="0"></iframe>
            </div>
            <!-- Modal Footer -->
            <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2 border-t border-slate-100">
                <button type="button" onclick="closeActionFileModal()" class="w-full inline-flex justify-center rounded-lg border border-slate-200 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 focus:outline-none sm:w-auto sm:text-sm">
                    Close
                </button>
                <button type="button" id="actionFileFullscreenBtn" onclick="handleActionFullscreen()" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:w-auto sm:text-sm">
                    <i class="fa-solid fa-expand mr-1.5 mt-0.5"></i> Full Screen
                </button>
            </div>
        </div>
    </div>
</div>

@endsection
