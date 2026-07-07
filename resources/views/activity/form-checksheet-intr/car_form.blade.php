@extends('layouts.app')

@section('title', 'Corrective Action Report')

@section('content')
@include('layouts.sidebar')

<div class="lg:ml-20 min-h-screen flex flex-col bg-slate-50">
    @include('layouts.header')

    <main class="flex-1 p-6">
        <!-- Back Button & Page Title -->
        <div class="mb-6 flex items-center gap-3">
            <a href="{{ route('internal_audit.conduct', $schedule->hash_id) }}" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Corrective Action Report (CAR)</h1>
                <p class="text-slate-500 text-xs sm:text-sm mt-0.5">Please document the audit finding and define the action plans below.</p>
            </div>
        </div>

        <form id="car-form" action="{{ route('internal_audit.car_form.send_draft', ['schedule_id' => $schedule->hash_id, 'item_id' => $item->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- Audit Metadata Card -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 mb-6">
                <h2 class="text-lg font-bold text-slate-800 mb-4 pb-3 border-b border-slate-100">
                    Audit Information
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Left: Metadata -->
                    <div class="col-span-1 md:border-r md:border-slate-100 md:pr-6 grid grid-cols-1 sm:grid-cols-2 gap-4 items-start">
                        <!-- Left sub-column: CAR Req. Number, Audit Date & Due Date -->
                        <div class="grid grid-cols-2 gap-x-4 gap-y-4">
                            <div class="col-span-2">
                                <span class="block text-[12px]  text-slate-400 tracking-wider">Req. Number</span>
                                <span class="text-sm font-bold text-slate-800 mt-1 block" id="car-req-number-display">{{ $car->req_number ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="block text-[12px]  text-slate-400 tracking-wider">Audit Date</span>
                                <span class="text-sm  text-slate-700 mt-1 block">{{ $schedule->formatted_date ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="block text-[12px] text-slate-400 tracking-wider">Due Date</span>
                                @php $carDueDate = $car->due_date ? \Carbon\Carbon::parse($car->due_date)->format('d M Y') : '-'; @endphp
                                <span class="text-sm text-slate-700 mt-1 block">{{ $carDueDate }}</span>
                            </div>
                        </div>
                        <!-- Right sub-column: Auditee -->
                        <div class="h-full flex flex-col justify-start">
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 flex flex-col justify-start h-full">
                                <span class="block text-sm font-semibold text-slate-400">Auditee</span>
                                <span class="text-sm text-slate-800 mt-1 block break-words leading-relaxed">{{ $schedule->auditee ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right: Klausul / Check Item -->
                    <div class="col-span-1 md:col-span-2 flex flex-col justify-center">
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 flex flex-col justify-center h-full">
                            <span class="block text-sm font-semibold text-slate-400">Check Item</span>
                            <p class="text-sm text-slate-800 mt-1">{{ $item->check_item_idn }}</p>
                            <p class="text-xs text-slate-500 mt-0.5 italic">{{ $item->check_item_en }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CAR Fillable Form -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 mb-6 space-y-6">
                <h2 class="text-lg font-bold text-slate-800 pb-3 border-b border-slate-100">
                    CAR Findings & Actions
                </h2>

                <!-- Audit Source Selection (Surveillance, External, Internal Audit) -->
                <div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @php
                            $surveillanceText = old('audit_source_surveillance_text', $car->surveillance ?? '');
                            $isSurveillanceChecked = old('audit_source') ? in_array('Surveillance', old('audit_source')) : !is_null($car->surveillance ?? null);
                            
                            $externalText = old('audit_source_external_text', $car->external ?? '');
                            $isExternalChecked = old('audit_source') ? in_array('External', old('audit_source')) : !is_null($car->external ?? null);
                        @endphp

                        <!-- Column 1: Surveillance -->
                        <div class="space-y-2">
                            <label class="relative flex items-center gap-3 p-1.5 bg-transparent hover:bg-slate-50 cursor-pointer rounded-lg transition-all">
                                <input type="checkbox" name="audit_source[]" value="Surveillance" class="peer sr-only audit-source-checkbox" {{ $isSurveillanceChecked ? 'checked' : '' }} onchange="toggleAuditSourceInputs()">
                                <div class="w-5 h-5 rounded-md border border-slate-300 flex items-center justify-center peer-checked:border-sky-400 peer-checked:bg-sky-50 peer-checked:[&_svg]:scale-100 transition-all shrink-0">
                                    <svg class="w-3 h-3 text-sky-400 scale-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <span class="text-sm font-bold text-slate-700">Surveillance</span>
                            </label>
                            <div class="pt-1 pl-1.5">
                                <input type="text" name="audit_source_surveillance_text" id="surveillance_input" value="{{ old('audit_source_surveillance_text', $surveillanceText) }}" class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-sm disabled:bg-slate-50 disabled:text-slate-400" placeholder="Enter surveillance body name..." {{ $isSurveillanceChecked ? '' : 'disabled' }}>
                            </div>
                        </div>

                        <!-- Column 2: External -->
                        <div class="space-y-2">
                            <label class="relative flex items-center gap-3 p-1.5 bg-transparent hover:bg-slate-50 cursor-pointer rounded-lg transition-all">
                                <input type="checkbox" name="audit_source[]" value="External" class="peer sr-only audit-source-checkbox" {{ $isExternalChecked ? 'checked' : '' }} onchange="toggleAuditSourceInputs()">
                                <div class="w-5 h-5 rounded-md border border-slate-300 flex items-center justify-center peer-checked:border-sky-400 peer-checked:bg-sky-50 peer-checked:[&_svg]:scale-100 transition-all shrink-0">
                                    <svg class="w-3 h-3 text-sky-400 scale-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <span class="text-sm font-bold text-slate-700">External</span>
                            </label>
                            <div class="pt-1 pl-1.5">
                                <input type="text" name="audit_source_external_text" id="external_input" value="{{ old('audit_source_external_text', $externalText) }}" class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-sm disabled:bg-slate-50 disabled:text-slate-400" placeholder="" {{ $isExternalChecked ? '' : 'disabled' }}>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Department, Requirement No. & Clause Title -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Department</label>
                        <x-searchable-select
                            id="car_department"
                            name="department"
                            label="Department"
                            required="false"
                            hideLabel="true"
                            apiUrl="{{ route('genba.get_section') }}"
                            updateEvent="update-car-department" />
                    </div>

                     <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">IATF/ISO Requirement No. <span class="text-red-500">*</span></label>
                        <x-searchable-select
                            id="car_requirement_no"
                            name="requirement_no"
                            label="IATF/ISO Requirement No."
                            required="false"
                            hideLabel="true"
                            apiUrl="{{ route('internal_audit.get_requirements') }}"
                            :initialOptions="$requirements->toArray()"
                            updateEvent="update-car-requirement"
                            changeEvent="car-requirement-changed"
                            dependencyParam="requirement_no" />
                        <p id="err_requirement_no" class="hidden mt-1 text-xs text-red-500 font-medium"><i class="fa-solid fa-circle-exclamation mr-1"></i>This field is required</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Clause Title <span class="text-red-500">*</span></label>
                        <x-searchable-select
                            id="car_clause_title"
                            name="clause_title"
                            label="Clause Title"
                            required="false"
                            hideLabel="true"
                            apiUrl="{{ route('internal_audit.get_clause_titles') }}"
                            :initialOptions="$clauseTitles->toArray()"
                            updateEvent="update-car-clause-title"
                            changeEvent="car-clause-changed"
                            dependencyEvent="car-requirement-changed"
                            dependencyParam="requirement_no" />
                        <p id="err_clause_title" class="hidden mt-1 text-xs text-red-500 font-medium"><i class="fa-solid fa-circle-exclamation mr-1"></i>This field is required</p>
                    </div>
                </div>

                <!-- Klausul & Finding Category -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4">
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Klausul</label>
                        <textarea name="clause_text" id="clause_text" readonly rows="1" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-slate-500 cursor-not-allowed outline-none transition-all text-sm resize-none overflow-hidden autogrow-textarea" placeholder="Clause text...">{{ old('clause_text', $car->clause_text ?? '') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Finding Category</label>
                        <div class="grid grid-cols-2 gap-2 -ml-1.5">
                            @php
                                $currentJudgment = old('judgment', $detail->judgment ?? 'OFI');
                            @endphp
                             @foreach([
                                'OFI' => 'OFI',
                                'Minor' => 'Minor',
                                'Mayor' => 'Mayor',
                                'Observation' => 'Observation'
                            ] as $val => $label)
                            <label class="relative flex items-center gap-3 p-1.5 bg-transparent hover:bg-slate-50 cursor-pointer rounded-lg transition-all">
                                <input type="radio" name="judgment" value="{{ $val }}" class="peer sr-only" {{ $currentJudgment === $val ? 'checked' : '' }}>
                                <div class="w-4 h-4 rounded-md border border-slate-300 flex items-center justify-center peer-checked:border-sky-400 peer-checked:bg-sky-50 peer-checked:[&_svg]:scale-100 transition-all shrink-0">
                                    <svg class="w-2.5 h-2.5 text-sky-400 scale-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <span class="text-sm text-slate-700">{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <!-- Finding & Auditor -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t border-slate-100">
                    <div class="col-span-1 md:col-span-2">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="col-span-1 sm:col-span-2">
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Finding <span class="text-red-500">*</span></label>
                                <textarea name="finding" id="finding_textarea" rows="1" style="min-height: 70px;" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-sm resize-none overflow-hidden autogrow-textarea" placeholder="Enter finding details...">{{ old('finding', $car->finding ?? '') }}</textarea>
                                <p id="err_finding" class="hidden mt-1 text-xs text-red-500 font-medium"><i class="fa-solid fa-circle-exclamation mr-1"></i>This field is required</p>
                            </div>
                            <div class="col-span-1">
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Finding Photo Evidence</label>

                                <div class="grid grid-cols-2 gap-2">
                                    <div class="relative group">
                                        <input type="file" id="camera_input" accept="image/*" capture="environment"
                                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                        <div class="flex flex-row items-center justify-center gap-2 h-[70px] border-2 border-dashed border-blue-200 rounded-xl bg-blue-50/50 group-hover:bg-blue-50 group-hover:border-blue-300 transition-all text-center px-2">
                                            <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center group-hover:scale-105 transition-transform shrink-0">
                                                <i class="fas fa-camera text-[14px]"></i>
                                            </div>
                                            <span class="text-[14px] font-medium text-blue-600 whitespace-nowrap">Take Photo</span>
                                        </div>
                                    </div>

                                    <div class="relative group">
                                        <input type="file" id="gallery_input" multiple accept="image/*,application/pdf"
                                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                        <div class="flex flex-row items-center justify-center gap-2 h-[70px] border-2 border-dashed border-blue-200 rounded-xl bg-blue-50/50 group-hover:bg-blue-50 group-hover:border-blue-300 transition-all text-center px-2">
                                            <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center group-hover:scale-105 transition-transform shrink-0">
                                                <i class="fas fa-folder-open text-[14px]"></i>
                                            </div>
                                            <span class="text-[14px] font-medium text-blue-600 whitespace-nowrap">File</span>
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name="existing_photos" id="existing_photos" value="{{ $car->finding_file_path ?? '' }}">
                                <input type="file" id="hidden_photos_input" name="finding_photo[]" multiple class="hidden">
                                
                                <div class="mt-2 flex flex-wrap gap-2 items-center">
                                    <div id="photo_preview_container" class="flex flex-wrap gap-2"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Auditor</label>
                        <input type="text" name="auditor" value="{{ old('auditor', $car->auditor ?? $schedule->auditor_names ?? '') }}" readonly class="w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-slate-500 cursor-not-allowed outline-none transition-all text-sm" placeholder="Enter auditor...">
                    </div>
                    
                    <input type="hidden" name="auditee" value="{{ old('auditee', $car->auditee ?? $schedule->auditee ?? '') }}">
                    @php $carDueDateRaw = $car->due_date ? \Carbon\Carbon::parse($car->due_date)->format('Y-m-d') : ''; @endphp
                    <input type="hidden" name="due_date" id="hidden_due_date" value="{{ $carDueDateRaw }}">
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('internal_audit.conduct', $schedule->hash_id) }}" class="px-5 py-2.5 bg-white text-slate-700 border border-slate-300 rounded-xl hover:bg-slate-50 font-bold transition-all text-sm">
                    Cancel
                </a>
                <button type="button" id="btn_save_car" onclick="validateAndSubmitCar()" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-all text-sm">
                    Save CAR Details
                </button>
            </div>
        </form>
    </main>

    <!-- Image Preview Modal -->
    <div id="imagePreviewModal" class="fixed inset-0 z-50 hidden">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60 transition-opacity" onclick="closeImageModal()"></div>

        <!-- Modal -->
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl w-full max-w-3xl transform transition-all max-h-[85vh] flex flex-col">
                <!-- Header -->
                <div class="flex items-center justify-between p-4 border-b border-slate-200">
                    <h3 class="text-lg font-semibold text-slate-800">Finding Photo Preview</h3>
                    <button type="button" onclick="closeImageModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="p-6 overflow-y-auto flex-1 flex items-center justify-center bg-slate-50/50">
                    <div id="imageContainer" class="max-w-full max-h-[60vh] rounded-lg overflow-hidden border border-slate-200 bg-white">
                        <img id="modalPreviewImg" src="" class="max-w-full max-h-[60vh] object-contain">
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex justify-end p-4 border-t border-slate-200 bg-slate-50 rounded-b-2xl">
                    <button type="button" onclick="closeImageModal()"
                        class="px-6 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-xl font-medium hover:bg-slate-50 transition-colors">
                        Close Preview
                    </button>
                </div>
            </div>
        </div>
    </div>
    @include('layouts.footer')
</div>

<script>
    const baseUrl = "{{ asset('') }}";

    function autoGrow(element) {
        element.style.height = "auto";
        element.style.height = (element.scrollHeight) + "px";
    }

    function validateAndSubmitCar() {
        let isValid = true;

        // Helper: show/hide error and highlight border
        function setError(inputEl, errEl, hasError) {
            if (hasError) {
                errEl.classList.remove('hidden');
                if (inputEl) {
                    inputEl.classList.add('!border-red-400');
                    inputEl.classList.remove('border-slate-200');
                }
            } else {
                errEl.classList.add('hidden');
                if (inputEl) {
                    inputEl.classList.remove('!border-red-400');
                    inputEl.classList.add('border-slate-200');
                }
            }
        }

        // Validate IATF/ISO Requirement No.
        const reqNoInput = document.getElementById('car_requirement_no');
        const reqNoErr   = document.getElementById('err_requirement_no');
        const reqNoVal   = reqNoInput ? reqNoInput.value.trim() : '';
        if (!reqNoVal) {
            setError(reqNoInput ? reqNoInput.closest('.relative')?.querySelector('input[type="text"]') : null, reqNoErr, true);
            isValid = false;
        } else {
            setError(null, reqNoErr, false);
        }

        // Validate Clause Title
        const clauseInput = document.getElementById('car_clause_title');
        const clauseErr   = document.getElementById('err_clause_title');
        const clauseVal   = clauseInput ? clauseInput.value.trim() : '';
        if (!clauseVal) {
            setError(clauseInput ? clauseInput.closest('.relative')?.querySelector('input[type="text"]') : null, clauseErr, true);
            isValid = false;
        } else {
            setError(null, clauseErr, false);
        }

        // Validate Finding
        const findingTextarea = document.getElementById('finding_textarea');
        const findingErr      = document.getElementById('err_finding');
        const findingVal      = findingTextarea ? findingTextarea.value.trim() : '';
        if (!findingVal) {
            setError(findingTextarea, findingErr, true);
            isValid = false;
        } else {
            setError(findingTextarea, findingErr, false);
        }

        if (!isValid) {
            // Scroll to first error
            const firstErr = document.querySelector('[id^="err_"]:not(.hidden)');
            if (firstErr) firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        // All valid — submit
        document.getElementById('car-form').submit();
    }

    function toggleAuditSourceInputs() {
        const surveillanceCheckbox = document.querySelector('.audit-source-checkbox[value="Surveillance"]');
        const externalCheckbox = document.querySelector('.audit-source-checkbox[value="External"]');
        const internalCheckbox = document.querySelector('.audit-source-checkbox[value="Internal Audit"]');
        
        const surveillanceInput = document.getElementById('surveillance_input');
        const externalInput = document.getElementById('external_input');
        const categoryCheckboxes = document.querySelectorAll('.category-checkbox');

        // Toggle Surveillance input
        if (surveillanceCheckbox && surveillanceCheckbox.checked) {
            surveillanceInput.removeAttribute('disabled');
        } else {
            surveillanceInput.setAttribute('disabled', 'true');
        }

        // Toggle External input
        if (externalCheckbox && externalCheckbox.checked) {
            externalInput.removeAttribute('disabled');
        } else {
            externalInput.setAttribute('disabled', 'true');
        }

        // Toggle Internal Audit categories
        if (internalCheckbox && internalCheckbox.checked) {
            categoryCheckboxes.forEach(cb => cb.removeAttribute('disabled'));
        } else {
            categoryCheckboxes.forEach(cb => {
                cb.setAttribute('disabled', 'true');
                cb.checked = false;
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        toggleAuditSourceInputs();

        // Dispatch initial department values
        @php
            $initialDeptId = old('department', $car->department ?? '');
            $initialDept = $initialDeptId ? $departments->firstWhere('Key1', $initialDeptId) : null;
            $initialDeptName = $initialDept ? $initialDept->Desc : $initialDeptId;
        @endphp
        window.dispatchEvent(new CustomEvent('update-car-department', {
            detail: {
                id: "{{ $initialDeptId }}",
                name: "{{ $initialDeptName }}"
            }
        }));

        // Dispatch initial requirement values
        @php
            $initialReq = old('requirement_no', $car->requirement_no ?? '');
            $reqObj = $initialReq ? $requirements->firstWhere('id', $initialReq) : null;
            $initialReqName = $reqObj ? $reqObj['name'] : '';
        @endphp
        window.dispatchEvent(new CustomEvent('update-car-requirement', {
            detail: {
                id: "{{ $initialReq }}",
                name: "{{ $initialReqName }}"
            }
        }));

        // Dispatch initial clause title values
        @php
            $initialClauseTitle = old('clause_title', $car->clause_title ?? '');
            $clauseObj = $initialClauseTitle ? $clauseTitles->firstWhere('id', $initialClauseTitle) : null;
            $initialClauseTitleName = $clauseObj ? $clauseObj['name'] : '';
        @endphp
        window.dispatchEvent(new CustomEvent('update-car-clause-title', {
            detail: {
                id: "{{ $initialClauseTitle }}",
                name: "{{ $initialClauseTitleName }}"
            }
        }));

        // Auto-grow textareas on load and input
        const textareas = document.querySelectorAll('.autogrow-textarea');
        textareas.forEach(textarea => {
            setTimeout(() => {
                autoGrow(textarea);
            }, 10);
            textarea.addEventListener('input', function() {
                autoGrow(this);
            });
        });

        // Listen to requirement changed event
        window.addEventListener('car-requirement-changed', function(e) {
            const clauseTextarea = document.querySelector('textarea[name="clause_text"]');
            if (clauseTextarea) {
                clauseTextarea.value = '';
                clauseTextarea.dispatchEvent(new Event('input', { bubbles: true }));
            }
        });

        // Listen to clause changed event
        window.addEventListener('car-clause-changed', function(e) {
            const clauseTitle = e.detail.id;
            const requirementNo = document.getElementById('car_requirement_no').value;
            const clauses = {!! json_encode(DB::table('CsKlausul')->select('clause_no', 'clause_title', 'clauses')->get()->toArray()) !!};
            const matchedClause = clauses.find(c => String(c.clause_title || '').trim() === String(clauseTitle || '').trim() && String(c.clause_no || '').trim() === String(requirementNo || '').trim());
            const clauseTextarea = document.querySelector('textarea[name="clause_text"]');
            if (clauseTextarea) {
                clauseTextarea.value = matchedClause ? (matchedClause.clauses || '') : '';
                clauseTextarea.dispatchEvent(new Event('input', { bubbles: true }));
            }
        });
        // Auto-save draft logic
        const form = document.getElementById('car-form');
        let autoSaveTimeout = null;

        function saveDraft() {
            const formData = new FormData(form);
            formData.append('draft', '1');

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('Draft auto-saved:', data);
                // Update req_number display if returned
                if (data.req_number) {
                    const reqDisplay = document.getElementById('car-req-number-display');
                    if (reqDisplay) reqDisplay.textContent = data.req_number;
                }
            })
            .catch(error => {
                console.error('Error saving draft:', error);
            });
        }

        form.addEventListener('input', function(e) {
            if (e.target.tagName === 'TEXTAREA' || e.target.type === 'text') {
                clearTimeout(autoSaveTimeout);
                autoSaveTimeout = setTimeout(saveDraft, 1000);
            }
        });

        form.addEventListener('change', function(e) {
            if (e.target.type === 'checkbox' || e.target.type === 'radio' || e.target.type === 'hidden' || e.target.type === 'date') {
                saveDraft();
            }
        });

        // Trigger change for searchable selects
        ['car_department', 'car_requirement_no', 'car_clause_title'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('change', saveDraft);
            }
        });

        // Photo Upload Handlers & Preview
        let existingPhotos = {!! json_encode(!empty($car->finding_file_path) ? explode(',', $car->finding_file_path) : []) !!};
        let uploadedFiles = [];
        const cameraInput = document.getElementById('camera_input');
        const galleryInput = document.getElementById('gallery_input');
        const hiddenPhotosInput = document.getElementById('hidden_photos_input');
        const existingPhotosInput = document.getElementById('existing_photos');
        const previewContainer = document.getElementById('photo_preview_container');

        function syncPhotos() {
            if (existingPhotosInput) {
                existingPhotosInput.value = existingPhotos.join(',');
            }
            if (hiddenPhotosInput) {
                const dt = new DataTransfer();
                uploadedFiles.forEach(file => dt.items.add(file));
                hiddenPhotosInput.files = dt.files;
            }
        }

        function renderPreviews() {
            if (!previewContainer) return;
            previewContainer.innerHTML = '';

            // Render existing photos/PDFs with red 'x' cancel button
            existingPhotos.forEach((path, index) => {
                const wrapper = document.createElement('div');
                wrapper.className = "relative w-16 h-16 bg-slate-100 border border-slate-200 rounded-lg group flex items-center justify-center";
                
                const isPdf = path.toLowerCase().endsWith('.pdf');
                
                if (isPdf) {
                    const pdfDiv = document.createElement('div');
                    pdfDiv.className = "w-full h-full flex flex-col items-center justify-center rounded-lg cursor-pointer bg-red-50 text-red-500 hover:bg-red-100 transition-colors";
                    pdfDiv.innerHTML = '<i class="fa-solid fa-file-pdf text-xl"></i><span class="text-[9px] font-bold mt-1">PDF</span>';
                    pdfDiv.onclick = function() { window.open(baseUrl + path, '_blank'); };
                    wrapper.appendChild(pdfDiv);
                } else {
                    const img = document.createElement('img');
                    img.src = baseUrl + path;
                    img.className = "w-full h-full object-cover rounded-lg cursor-pointer";
                    img.onclick = function() { viewPhoto(img.src); };
                    wrapper.appendChild(img);
                }
                
                const btn = document.createElement('button');
                btn.type = "button";
                btn.className = "absolute -top-1.5 -right-1.5 bg-red-500 text-white rounded-full w-4 h-4 flex items-center justify-center text-[10px] font-bold hover:bg-red-600 transition-colors z-20";
                btn.innerHTML = "×";
                btn.onclick = function(evt) {
                    evt.stopPropagation();
                    removeExistingPhoto(index);
                };
                
                wrapper.appendChild(btn);
                previewContainer.appendChild(wrapper);
            });

            // Render newly uploaded files
            uploadedFiles.forEach((file, index) => {
                const wrapper = document.createElement('div');
                wrapper.className = "relative w-16 h-16 bg-slate-100 border border-slate-200 rounded-lg group flex items-center justify-center";
                
                const isPdf = file.name.toLowerCase().endsWith('.pdf');
                
                if (isPdf) {
                    const pdfDiv = document.createElement('div');
                    pdfDiv.className = "w-full h-full flex flex-col items-center justify-center rounded-lg cursor-pointer bg-red-50 text-red-500 hover:bg-red-100 transition-colors";
                    pdfDiv.innerHTML = '<i class="fa-solid fa-file-pdf text-xl"></i><span class="text-[9px] font-bold mt-1">PDF</span>';
                    pdfDiv.onclick = function() {
                        const fileURL = URL.createObjectURL(file);
                        window.open(fileURL, '_blank');
                    };
                    wrapper.appendChild(pdfDiv);
                    
                    const btn = document.createElement('button');
                    btn.type = "button";
                    btn.className = "absolute -top-1.5 -right-1.5 bg-red-500 text-white rounded-full w-4 h-4 flex items-center justify-center text-[10px] font-bold hover:bg-red-600 transition-colors z-20";
                    btn.innerHTML = "×";
                    btn.onclick = function(evt) {
                        evt.stopPropagation();
                        removeNewPhoto(index);
                    };
                    wrapper.appendChild(btn);
                    previewContainer.appendChild(wrapper);
                } else {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = "w-full h-full object-cover rounded-lg cursor-pointer";
                        img.onclick = function() { viewPhoto(e.target.result); };
                        wrapper.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                    
                    const btn = document.createElement('button');
                    btn.type = "button";
                    btn.className = "absolute -top-1.5 -right-1.5 bg-red-500 text-white rounded-full w-4 h-4 flex items-center justify-center text-[10px] font-bold hover:bg-red-600 transition-colors z-20";
                    btn.innerHTML = "×";
                    btn.onclick = function(evt) {
                        evt.stopPropagation();
                        removeNewPhoto(index);
                    };
                    wrapper.appendChild(btn);
                    previewContainer.appendChild(wrapper);
                }
            });
        }

        window.removeExistingPhoto = function(index) {
            existingPhotos.splice(index, 1);
            syncPhotos();
            renderPreviews();
            saveDraft();
        };

        window.removeNewPhoto = function(index) {
            uploadedFiles.splice(index, 1);
            syncPhotos();
            renderPreviews();
            saveDraft();
        };

        function handleFileSelection(files) {
            const arr = Array.from(files);
            if (existingPhotos.length + uploadedFiles.length + arr.length > 3) {
                showToast('Maximum 3 photos allowed.', 'error');
                return;
            }
            uploadedFiles.push(...arr);
            syncPhotos();
            renderPreviews();
            saveDraft();
        }

        if (cameraInput) {
            cameraInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    handleFileSelection(this.files);
                    this.value = '';
                }
            });
        }

        if (galleryInput) {
            galleryInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    handleFileSelection(this.files);
                    this.value = '';
                }
            });
        }

        // Initial preview render
        renderPreviews();
    });

    window.viewPhoto = function(src) {
        if (!src) return;
        const img = document.getElementById('modalPreviewImg');
        if (img) img.src = src;
        
        const modal = document.getElementById('imagePreviewModal');
        if (modal) modal.classList.remove('hidden');
    };

    window.closeImageModal = function() {
        const modal = document.getElementById('imagePreviewModal');
        if (modal) modal.classList.add('hidden');
    };
</script>
@endsection
