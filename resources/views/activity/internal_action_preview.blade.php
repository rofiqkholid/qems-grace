@extends('layouts.app')

@php
    $hideCentralToast = true;
    $isSaved = isset($car) && in_array($car->status, ['Under Review', 'Closed']);
    $isAuditeeSuperior = Auth::check() && isset($action) && strcasecmp(trim(Auth::user()->full_name), trim($action->analyzed_by ?? '')) === 0;
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
                            {{ $car->internal_audit ?? '' }}
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
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
                        <!-- Causal Factor -->
                        <div class="flex flex-col gap-1.5 sm:col-span-2">
                            <label class="text-slate-700 font-semibold text-sm tracking-wider">Causal Factor (Why-why Analysis, Fish bone) :</label>
                            <textarea name="causal_factor" rows="1" class="w-full px-4 py-[9px] border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm resize-none overflow-hidden {{ $isSaved ? 'bg-slate-50 text-slate-500 cursor-not-allowed' : '' }}" {{ $isSaved ? 'disabled' : '' }} placeholder="Enter causal factors or analysis...">{{ old('causal_factor', $action->causal_factor ?? '') }}</textarea>
                        </div>
                        
                        <!-- Analized by: Auditee Superior -->
                        <div class="flex flex-col gap-1.5 sm:col-span-1">
                            <label class="text-slate-700 font-semibold text-sm tracking-wider">Analized by Auditee Superior</label>
                            @if($isSaved)
                                <input type="text" value="{{ $action->analyzed_by ?? '' }}" readonly class="w-full pl-4 pr-8 py-[9px] border border-slate-200 rounded-lg bg-slate-50 text-slate-500 cursor-not-allowed text-sm outline-none truncate">
                            @else
                                <x-searchable-select
                                    id="analyzed_by"
                                    name="analyzed_by"
                                    label="Analized by: Auditee Superior"
                                    required="false"
                                    hideLabel="true"
                                    apiUrl="{{ route('internal_audit.get_users') }}"
                                    updateEvent="update-analyzed-by"
                                    changeEvent="analyzed-by-changed" />
                            @endif
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
                            <textarea name="corrective_action" rows="4" class="w-full px-4 py-[9px] border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm mt-1 resize-none overflow-hidden {{ $isSaved ? 'bg-slate-50 text-slate-500 cursor-not-allowed' : '' }}" {{ $isSaved ? 'disabled' : '' }} placeholder="Enter corrective actions...">{{ old('corrective_action', $action->corrective_action ?? '') }}</textarea>
                        </div>
                        
                        <!-- B. Preventive Action -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-slate-700 font-semibold text-sm tracking-wider">B. Preventive Action</label>
                            <span class="text-slate-400 text-[10px] -mt-1 block italic">(Perbaikan yang harus segera dilakukan untuk menghilangkan akar penyebab)</span>
                            <textarea name="preventive_action" rows="4" class="w-full px-4 py-[9px] border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm mt-1 resize-none overflow-hidden {{ $isSaved ? 'bg-slate-50 text-slate-500 cursor-not-allowed' : '' }}" {{ $isSaved ? 'disabled' : '' }} placeholder="Enter preventive actions...">{{ old('preventive_action', $action->preventive_action ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Notes & Signatures -->
                <div class="border-t border-slate-100 pt-6">
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 sm:gap-6">
                        <!-- Notes for A & B -->
                        <div class="flex flex-col gap-1.5 sm:col-span-2">
                            <label class="text-slate-700 font-semibold text-sm tracking-wider">Notes for A & B</label>
                            <textarea name="notes" rows="1" class="w-full px-4 py-[9px] border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm resize-none overflow-hidden {{ $isSaved ? 'bg-slate-50 text-slate-500 cursor-not-allowed' : '' }}" {{ $isSaved ? 'disabled' : '' }} placeholder="Enter notes...">{{ old('notes', $action->notes ?? '') }}</textarea>
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

                <!-- Submit / Rollback / Verify / Reject Button -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-t border-slate-100 pt-6">
                    <div>
                        @if($isSaved)
                            @if($car->status === 'Under Review')
                                <div class="text-left">
                                    <span class="text-xs font-semibold text-emerald-600 block">
                                        <i class="fa-solid fa-circle-info mr-1"></i> CAR has been saved, waiting for Auditee Superior to analyze
                                    </span>
                                    <span class="text-[10px] text-slate-400 block italic mt-0.5">
                                        CAR telah disimpan, menunggu Auditee Superior untuk melakukan analisis
                                    </span>
                                </div>
                            @elseif($car->status === 'Closed')
                                <div class="text-left">
                                    <span class="text-xs font-semibold text-emerald-600 block">
                                        <i class="fa-solid fa-circle-check mr-1"></i> Action Plan has been verified and closed
                                    </span>
                                    <span class="text-[10px] text-slate-400 block italic mt-0.5">
                                        Action Plan telah diverifikasi dan selesai (Closed)
                                    </span>
                                </div>
                            @endif
                        @endif
                    </div>
                    <div class="flex gap-3 w-full sm:w-auto justify-end">
                        @if($isSaved)
                            @if($isAuditeeSuperior)
                                @if($car->status === 'Under Review')
                                    <button type="button" id="btnVerify" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition-all text-sm flex items-center gap-2">
                                        <i class="fa-solid fa-check"></i> Verify Action Plan
                                    </button>
                                    <button type="button" id="btnRollback" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold transition-all text-sm flex items-center gap-2">
                                        <i class="fa-solid fa-xmark"></i> Reject Action Plan
                                    </button>
                                @endif
                            @else
                                @if($car->status === 'Under Review')
                                    <button type="button" id="btnRollback" class="px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white rounded-xl font-bold transition-all text-sm flex items-center gap-2">
                                        <i class="fa-solid fa-rotate-left"></i> Rollback Action Plan
                                    </button>
                                @endif
                            @endif
                        @else
                            <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-all text-sm">
                                Save Action Plan
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </main>

    @include('layouts.footer')
</div>

<!-- Generic Confirmation Modal -->
<div id="confirmationModal" class="fixed inset-0 z-[60] hidden">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-900/60 transition-opacity" onclick="closeConfirmationModal()"></div>

    <!-- Modal -->
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-md transform transition-all p-6 text-center border border-slate-100">
            <div id="modalIcon" class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-5">
                <!-- Icon injected by JS -->
            </div>

            <h3 id="modalTitle" class="text-xl font-bold text-slate-800 mb-2"></h3>
            <p id="modalMessage" class="text-base text-slate-600 mb-6 leading-relaxed"></p>

            <div class="flex gap-3 justify-center">
                <button type="button" onclick="closeConfirmationModal()"
                    class="px-5 py-2.5 bg-slate-100 text-slate-700 font-medium rounded-xl hover:bg-slate-200 transition-colors text-sm">
                    Cancel
                </button>
                <button type="button" id="confirmBtn"
                    class="px-5 py-2.5 text-white font-medium rounded-xl transition-colors text-sm">
                    Yes
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-resize textareas
        const textareas = document.querySelectorAll('#actionPlanForm textarea');
        textareas.forEach(textarea => {
            const isSingleLine = textarea.name === 'causal_factor' || textarea.name === 'notes';
            const minHeight = isSingleLine ? 40 : 110;
            
            function resize() {
                textarea.style.height = 'auto';
                textarea.style.height = (textarea.scrollHeight > minHeight ? textarea.scrollHeight : minHeight) + 'px';
            }
            
            // Initial resize on load
            resize();
            
            // Resize on input
            textarea.addEventListener('input', resize);
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
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
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

        let activeAction = null; // 'verify' or 'rollback'

        window.closeConfirmationModal = function() {
            document.getElementById('confirmationModal').classList.add('hidden');
            activeAction = null;
        }

        // Rollback / Reject AJAX Action
        const rollbackBtn = document.getElementById('btnRollback');
        if (rollbackBtn) {
            rollbackBtn.addEventListener('click', function() {
                const isReject = rollbackBtn.classList.contains('bg-red-600');
                activeAction = 'rollback';
                
                const title = isReject ? 'Reject Action Plan' : 'Rollback Action Plan';
                const message = isReject 
                    ? 'Are you sure you want to reject this action plan? This will allow the auditee to edit it again.' 
                    : 'Are you sure you want to rollback this action plan? This will allow editing again.';
                
                document.getElementById('modalTitle').innerText = title;
                document.getElementById('modalMessage').innerText = message;
                
                const modalIcon = document.getElementById('modalIcon');
                const confirmBtn = document.getElementById('confirmBtn');
                
                if (isReject) {
                    modalIcon.className = 'w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-5';
                    modalIcon.innerHTML = '<i class="fa-solid fa-xmark text-2xl"></i>';
                    confirmBtn.className = 'px-5 py-2.5 bg-red-600 text-white font-medium rounded-xl hover:bg-red-700 transition-colors text-sm';
                    confirmBtn.innerText = 'Yes, Reject';
                } else {
                    modalIcon.className = 'w-16 h-16 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center mx-auto mb-5';
                    modalIcon.innerHTML = '<i class="fa-solid fa-rotate-left text-2xl"></i>';
                    confirmBtn.className = 'px-5 py-2.5 bg-orange-500 text-white font-medium rounded-xl hover:bg-orange-600 transition-colors text-sm';
                    confirmBtn.innerText = 'Yes, Rollback';
                }
                
                document.getElementById('confirmationModal').classList.remove('hidden');
            });
        }

        // Verify AJAX Action
        const verifyBtn = document.getElementById('btnVerify');
        if (verifyBtn) {
            verifyBtn.addEventListener('click', function() {
                activeAction = 'verify';
                
                document.getElementById('modalTitle').innerText = 'Verify Action Plan';
                document.getElementById('modalMessage').innerText = 'Are you sure you want to verify and close this action plan?';
                
                const modalIcon = document.getElementById('modalIcon');
                const confirmBtn = document.getElementById('confirmBtn');
                
                modalIcon.className = 'w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-5';
                modalIcon.innerHTML = '<i class="fa-solid fa-check text-2xl"></i>';
                confirmBtn.className = 'px-5 py-2.5 bg-emerald-600 text-white font-medium rounded-xl hover:bg-emerald-700 transition-colors text-sm';
                confirmBtn.innerText = 'Yes, Verify';
                
                document.getElementById('confirmationModal').classList.remove('hidden');
            });
        }

        // Confirm Button click inside modal
        document.getElementById('confirmBtn').addEventListener('click', function() {
            if (activeAction === 'rollback') {
                closeConfirmationModal();
                executeRollback();
            } else if (activeAction === 'verify') {
                closeConfirmationModal();
                executeVerification();
            }
        });

        function executeRollback() {
            const isReject = rollbackBtn.classList.contains('bg-red-600');
            const originalText = rollbackBtn.innerHTML;
            const loadingText = isReject ? 'Rejecting...' : 'Rolling back...';
            
            rollbackBtn.disabled = true;
            rollbackBtn.innerHTML = `<i class="fa-solid fa-spinner fa-spin mr-2"></i> ${loadingText}`;
            
            fetch('{{ route("internal_audit.action_report.rollback", request()->route("id")) }}', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showToast(data.message || 'An error occurred.', 'error');
                    rollbackBtn.disabled = false;
                    rollbackBtn.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const errorMsg = isReject ? 'An error occurred while rejecting.' : 'An error occurred while performing rollback.';
                showToast(errorMsg, 'error');
                rollbackBtn.disabled = false;
                rollbackBtn.innerHTML = originalText;
            });
        }

        function executeVerification() {
            const originalText = verifyBtn.innerHTML;
            verifyBtn.disabled = true;
            verifyBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Verifying...';
            
            fetch('{{ route("internal_audit.cars.approve") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({
                    car_id: {{ $car->id }},
                    role: 'dept'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showToast(data.message || 'An error occurred.', 'error');
                    verifyBtn.disabled = false;
                    verifyBtn.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('An error occurred while performing verification.', 'error');
                verifyBtn.disabled = false;
                verifyBtn.innerHTML = originalText;
            });
        }
    });
</script>

@endsection
