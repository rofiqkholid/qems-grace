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
            <a href="{{ route('internal_audit.action_report') }}"
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

                    <!-- Auditee -->
                    <div class="flex flex-col gap-1 sm:gap-1.5">
                        <label class="text-slate-500 text-[10px] sm:text-xs tracking-wider">Auditee</label>
                        <div class="bg-slate-50 border border-slate-200 rounded-lg px-2 sm:px-4 py-1.5 sm:py-[9px] text-slate-600 font-semibold text-[11px] sm:text-sm truncate">
                            {{ $car->auditee ?? '-' }}
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
                    <!-- Due Date -->
                    <div class="flex flex-col gap-1 sm:gap-1.5">
                        <label class="text-slate-500 text-[10px] sm:text-xs tracking-wider">Due Date</label>
                        <div class="bg-slate-50 border border-slate-200 rounded-lg px-2 sm:px-4 py-1.5 sm:py-[9px] text-slate-800 text-[11px] sm:text-sm">
                            {{ $car->due_date ? \Carbon\Carbon::parse($car->due_date)->format('d M Y') : '-' }}
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
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Plan Form Card -->
        <form id="actionPlanForm" action="{{ route('internal_audit.action_report.save_action', request()->route('id')) }}" method="POST" class="mt-6">
            @csrf
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
                                <textarea name="why_one" required rows="1" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none text-slate-700 resize-none overflow-hidden autogrow-textarea" placeholder="Enter Why 1...">{{ old('why_one', $action->why_one ?? '') }}</textarea>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-slate-700 font-semibold text-xs tracking-wider">Why 5</label>
                                <textarea name="why_five" rows="1" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none text-slate-700 resize-none overflow-hidden autogrow-textarea" placeholder="Enter Why 5 (Optional)...">{{ old('why_five', $action->why_five ?? '') }}</textarea>
                            </div>
                        </div>

                        <!-- Row 2: Why 2 & 3 (Left) and Root Cause (Right) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Left: Why 2 & Why 3 stacked -->
                            <div class="space-y-4 flex flex-col justify-between">
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-slate-700 font-semibold text-xs tracking-wider">Why 2 <span class="text-red-500">*</span></label>
                                    <textarea name="why_two" required rows="1" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none text-slate-700 resize-none overflow-hidden autogrow-textarea" placeholder="Enter Why 2...">{{ old('why_two', $action->why_two ?? '') }}</textarea>
                                </div>
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-slate-700 font-semibold text-xs tracking-wider">Why 3 <span class="text-red-500">*</span></label>
                                    <textarea name="why_three" required rows="1" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none text-slate-700 resize-none overflow-hidden autogrow-textarea" placeholder="Enter Why 3...">{{ old('why_three', $action->why_three ?? '') }}</textarea>
                                </div>
                            </div>
                            <!-- Right: Root Cause -->
                            <div class="flex flex-col gap-1.5 justify-between">
                                <label class="text-slate-700 font-semibold text-xs tracking-wider">Root Cause <span class="text-red-500">*</span></label>
                                <textarea name="root_cause" required rows="5" style="min-height: 120px;" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none text-slate-700 resize-none overflow-hidden autogrow-textarea flex-grow" placeholder="Enter Root Cause...">{{ old('root_cause', $action->root_cause ?? '') }}</textarea>
                            </div>
                        </div>

                        <!-- Row 3: Why 4 (Left) and Analyzed by (Right) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-slate-700 font-semibold text-xs tracking-wider">Why 4</label>
                                <textarea name="why_four" rows="1" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none text-slate-700 resize-none overflow-hidden autogrow-textarea" placeholder="Enter Why 4 (Optional)...">{{ old('why_four', $action->why_four ?? '') }}</textarea>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-slate-700 font-semibold text-xs tracking-wider">Analized by Auditee Superior</label>
                                <x-searchable-select
                                    id="analyzed_by"
                                    name="analyzed_by"
                                    label="Analized by: Auditee Superior"
                                    required="false"
                                    hideLabel="true"
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
                            <label class="text-slate-700 font-semibold text-sm tracking-wider">A. Corrective Action</label>
                            <span class="text-slate-400 text-[10px] -mt-1 block italic">(Tindakan Darurat untuk mengatasi masalah)</span>
                             <textarea name="corrective_action" rows="3" class="w-full px-4 py-[9px] border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm mt-1 resize-none overflow-hidden autogrow-textarea" placeholder="Enter corrective actions...">{{ old('corrective_action', $action->corrective_action ?? '') }}</textarea>
                        </div>
                        
                        <!-- B. Preventive Action -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-slate-700 font-semibold text-sm tracking-wider">B. Preventive Action</label>
                            <span class="text-slate-400 text-[10px] -mt-1 block italic">(Perbaikan yang harus segera dilakukan untuk menghilangkan akar penyebab)</span>
                            <textarea name="preventive_action" rows="3" class="w-full px-4 py-[9px] border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm mt-1 resize-none overflow-hidden autogrow-textarea" placeholder="Enter preventive actions...">{{ old('preventive_action', $action->preventive_action ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Notes & Signatures -->
                <div class="border-t border-slate-100 pt-6">
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 sm:gap-6">
                        <!-- Notes for A & B -->
                        <div class="flex flex-col gap-1.5 sm:col-span-2">
                            <label class="text-slate-700 font-semibold text-sm tracking-wider">Notes for A & B</label>
                            <textarea name="notes" rows="1" class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none text-slate-700 resize-none overflow-hidden autogrow-textarea" placeholder="Enter notes...">{{ old('notes', $action->notes ?? '') }}</textarea>
                        </div>
                        
                        <!-- Auditee -->
                        <div class="flex flex-col gap-1.5 sm:col-span-1">
                            <label class="text-slate-700 font-semibold text-sm tracking-wider">Auditee</label>
                            <input type="text" name="auditee_name" value="{{ old('auditee_name', $action->auditee_name ?? $car->auditee ?? '') }}" readonly class="w-full pl-4 pr-8 py-[9px] border border-slate-200 rounded-lg bg-slate-50 text-slate-500 cursor-not-allowed text-sm outline-none truncate" placeholder="Name of Auditee...">
                        </div>

                        <!-- Auditee Superior -->
                        <div class="flex flex-col gap-1.5 sm:col-span-1">
                            <label class="text-slate-700 font-semibold text-sm tracking-wider">Auditee Superior</label>
                            <input type="text" name="auditee_superior_name" id="auditee_superior_name" value="{{ old('auditee_superior_name', $action->auditee_superior_name ?? '') }}" readonly class="w-full pl-4 pr-8 py-[9px] border border-slate-200 rounded-lg bg-slate-50 text-slate-500 cursor-not-allowed text-sm outline-none truncate" placeholder="Name of Auditee Superior...">
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end gap-3 border-t border-slate-100 pt-6">
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-all text-sm">
                        Save Action Plan
                    </button>
                </div>
            </div>
        </form>
    </main>

    @include('layouts.footer')
</div>

<script>
    function autoGrow(element) {
        element.style.height = "auto";
        element.style.height = (element.scrollHeight) + "px";
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
    });
</script>

@endsection
