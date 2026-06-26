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
                        <!-- Left sub-column: CAR Req. Number & Audit Date -->
                        <div class="space-y-4">
                            <div>
                                <span class="block text-[12px]  text-slate-400 tracking-wider">Req. Number</span>
                                <span class="text-sm font-bold text-slate-800 mt-1 block">{{ $car->req_number ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="block text-[12px]  text-slate-400 tracking-wider">Audit Date</span>
                                <span class="text-sm  text-slate-700 mt-1 block">{{ $schedule->formatted_date ?? '-' }}</span>
                            </div>
                        </div>
                        <!-- Right sub-column: Auditee -->
                        <div class="h-full flex flex-col justify-start">
                            <span class="block text-[12px] font-semibold text-slate-400 tracking-wider">Auditee</span>
                            <span class="text-sm font-semibold text-slate-700 mt-1 block break-words leading-relaxed">{{ $schedule->auditee ?? '-' }}</span>
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
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @php
                            $surveillanceText = old('audit_source_surveillance_text', $car->surveillance ?? '');
                            $isSurveillanceChecked = old('audit_source') ? in_array('Surveillance', old('audit_source')) : !is_null($car->surveillance ?? null);
                            
                            $externalText = old('audit_source_external_text', $car->external ?? '');
                            $isExternalChecked = old('audit_source') ? in_array('External', old('audit_source')) : !is_null($car->external ?? null);
                            
                            $isInternalChecked = old('audit_source') ? in_array('Internal Audit', old('audit_source')) : !is_null($car->internal_audit ?? null);
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

                        <!-- Column 3: Internal Audit -->
                        <div class="space-y-2">
                            <label class="relative flex items-center gap-3 p-1.5 bg-transparent hover:bg-slate-50 cursor-pointer rounded-lg transition-all">
                                <input type="checkbox" name="audit_source[]" value="Internal Audit" class="peer sr-only audit-source-checkbox" {{ $isInternalChecked ? 'checked' : '' }} onchange="toggleAuditSourceInputs()">
                                <div class="w-5 h-5 rounded-md border border-slate-300 flex items-center justify-center peer-checked:border-sky-400 peer-checked:bg-sky-50 peer-checked:[&_svg]:scale-100 transition-all shrink-0">
                                    <svg class="w-3 h-3 text-sky-400 scale-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <span class="text-sm font-bold text-slate-700">Internal Audit</span>
                            </label>
                            
                            <!-- Audit Category checkboxes nested here -->
                            <div class="pl-8 pt-1.5 flex flex-wrap gap-x-4 gap-y-1.5 border-t border-slate-100 min-h-[50px] items-center">
                                @php
                                    $rawCategory = old('audit_category', $car->internal_audit ?? '');
                                    $selectedCategories = is_array($rawCategory) ? $rawCategory : (empty($rawCategory) ? [] : explode(', ', $rawCategory));
                                @endphp
                                @foreach(['Product', 'Process', 'System', 'Environment'] as $catVal)
                                @php
                                    $isCatChecked = in_array($catVal, $selectedCategories);
                                @endphp
                                <label class="relative flex items-center gap-2 cursor-pointer transition-all">
                                    <input type="checkbox" name="audit_category[]" value="{{ $catVal }}" class="peer sr-only category-checkbox" {{ $isCatChecked ? 'checked' : '' }} {{ $isInternalChecked ? '' : 'disabled' }}>
                                    <div class="w-4 h-4 rounded-md border border-slate-300 flex items-center justify-center peer-checked:border-sky-400 peer-checked:bg-sky-50 peer-checked:[&_svg]:scale-100 transition-all shrink-0 peer-disabled:bg-slate-50 peer-disabled:opacity-50">
                                        <svg class="w-2.5 h-2.5 text-sky-400 scale-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <span class="text-xs font-semibold text-slate-600 peer-disabled:text-slate-400">{{ $catVal }}</span>
                                </label>
                                @endforeach
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
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">IATF/ISO Requirement No.</label>
                        <x-searchable-select
                            id="car_requirement_no"
                            name="requirement_no"
                            label="IATF/ISO Requirement No."
                            required="false"
                            hideLabel="true"
                            apiUrl="{{ route('internal_audit.get_requirements') }}"
                            :initialOptions="$requirements->toArray()"
                            updateEvent="update-car-requirement"
                            changeEvent="car-requirement-changed" />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Clause Title</label>
                        <x-searchable-select
                            id="car_clause_title"
                            name="clause_title"
                            label="Clause Title"
                            required="false"
                            hideLabel="true"
                            apiUrl="{{ route('internal_audit.get_clause_titles') }}"
                            :initialOptions="$clauseTitles->toArray()"
                            updateEvent="update-car-clause-title" />
                    </div>
                </div>

                <!-- Klausul & Finding Category -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4">
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Klausul</label>
                        <textarea name="clause_text" id="clause_text" readonly rows="3.5" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-slate-500 cursor-not-allowed outline-none transition-all text-sm" placeholder="Clause text...">{{ old('clause_text', $car->clause_text ?? '') }}</textarea>
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
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Finding</label>
                        <textarea name="finding" rows="3.5" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-sm" placeholder="Enter finding details...">{{ old('finding', $car->finding ?? '') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Auditor</label>
                        <input type="text" name="auditor" value="{{ old('auditor', $car->auditor ?? $schedule->auditor_names ?? '') }}" readonly class="w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-slate-500 cursor-not-allowed outline-none transition-all text-sm" placeholder="Enter auditor...">
                    </div>
                    
                    <input type="hidden" name="auditee" value="{{ old('auditee', $car->auditee ?? $schedule->auditee ?? '') }}">
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('internal_audit.conduct', $schedule->hash_id) }}" class="px-5 py-2.5 bg-white text-slate-700 border border-slate-300 rounded-xl hover:bg-slate-50 font-bold transition-all text-sm">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-all text-sm">
                    Save CAR Details
                </button>
            </div>
        </form>
    </main>

    @include('layouts.footer')
</div>

<script>
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

        // Listen to requirement changed event
        window.addEventListener('car-requirement-changed', function(e) {
            const clauseNo = e.detail.value;
            const clauses = {!! json_encode(DB::table('CsKlausul')->select('clause_no', 'clause_title', 'clauses')->get()->toArray()) !!};
            const matchedClause = clauses.find(c => c.clause_no === clauseNo);
            if (matchedClause) {
                window.dispatchEvent(new CustomEvent('update-car-clause-title', {
                    detail: {
                        id: matchedClause.clause_title,
                        name: matchedClause.clause_title
                    }
                }));
                const clauseTextarea = document.querySelector('textarea[name="clause_text"]');
                if (clauseTextarea) {
                    clauseTextarea.value = matchedClause.clauses || '';
                    // Trigger input event to fire the draft auto-save
                    clauseTextarea.dispatchEvent(new Event('input', { bubbles: true }));
                }
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
            if (e.target.type === 'checkbox' || e.target.type === 'radio' || e.target.type === 'hidden') {
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
    });
</script>
@endsection
