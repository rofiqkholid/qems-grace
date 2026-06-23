@extends('layouts.app')

@section('title', 'Conduct Internal Audit')

@section('content')
@include('layouts.sidebar')

<x-toast />
<div class="lg:ml-20 min-h-screen flex flex-col bg-slate-50"
    x-data="genbaForm()"
    x-init="initForm()">

    @include('layouts.header')

    <!-- Loading State -->
    <div x-show="isLoading" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/20 backdrop-blur-[2px]">
        <div class="flex flex-col items-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
        </div>
    </div>

    <!-- Page Content -->
    <main class="flex-1 p-6">
        <!-- Simple Header -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('internal_audit') }}" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all shadow-sm">
                    <i class="fa-solid fa-arrow-left text-sm"></i>
                </a>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Conduct Audit</h1>
                    <p class="text-slate-500 text-xs sm:text-sm mt-0.5">Evaluate compliance clauses and submit findings</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:h-[calc(100vh-190px)]">
            <!-- Left Info Panel -->
            <div class="lg:col-span-3">
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden h-full flex flex-col">
                    <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                        <h2 class="text-lg font-bold text-slate-800">Audit Header Information</h2>
                        <p class="text-slate-500 text-sm mt-0.5">View details of the scheduled internal audit session.</p>
                    </div>
                    <div class="p-6 grid grid-cols-2 gap-x-6 gap-y-4 content-start flex-1 overflow-y-auto">
                        <div>
                            <div class="text-[12px] text-slate-400">Request Number</div>
                            <div class="text-sm font-semibold text-slate-800 mt-1">{{ $schedule->req_number ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-[12px] text-slate-400">Audit Date</div>
                            <div class="text-sm font-semibold text-slate-800 mt-1">{{\Carbon\Carbon::parse($schedule->schedule_date)->format('d M Y')}}</div>
                        </div>
                        
                        <div class="col-span-2 border-t border-slate-100 my-1"></div>
                        
                        <div>
                            <div class="text-[12px] text-slate-400">Auditee</div>
                            <div class="text-sm font-semibold text-slate-800 mt-1">{{ $schedule->auditee }}</div>
                        </div>
                        <div>
                            <div class="text-[12px] text-slate-400">Auditor</div>
                            <div class="text-sm font-semibold text-slate-800 mt-1">{{ $schedule->auditor_niks }}</div>
                        </div>
                        
                        <div class="col-span-2 border-t border-slate-100 my-1"></div>
                        
                        <div class="col-span-2">
                            <div class="text-[12px] text-slate-400">Auditee Department</div>
                            <div class="text-sm font-semibold text-slate-800 mt-1">{{ $schedule->auditee_dept_name }}</div>
                        </div>
                    </div>
                    <div class="p-6 border-t border-slate-100 bg-slate-50/50 space-y-3" x-show="!isReadOnly">
                        <button @click="submitForm()"
                            class="w-full flex items-center justify-center gap-2 bg-green-50 hover:bg-green-100 text-green-600 border border-green-400 py-3 rounded-xl font-bold text-base transition-all active:scale-95 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            <span>Finish Audit</span>
                        </button>
                        <p class="text-xs text-slate-400 text-center font-medium leading-normal">
                            Please remember to submit after completing all checksheets.
                        </p>
                    </div>
                    <div class="p-6 border-t border-slate-100 bg-slate-50/50 text-center" x-show="isReadOnly">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <i class="fa-solid fa-circle-check"></i> Audit Completed
                        </span>
                    </div>
                </div>
            </div>

            <!-- Right Checksheet Items Form -->
            <div class="lg:col-span-9 flex flex-col h-full overflow-hidden">
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden h-full flex flex-col">
                    <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex-shrink-0">
                        <h2 class="text-lg font-bold text-slate-800">Audit Checksheet Clauses</h2>
                        <p class="text-slate-500 text-sm mt-0.5">Please review each clause and input the judgment, evidence details, and photos.</p>
                    </div>

                    <div class="p-6 space-y-6 flex-1 overflow-y-auto">
                        @foreach ($items as $item)
                        @php $itemId = $item->id; @endphp
                        <div class="group relative rounded-xl border bg-white p-5 transition-all duration-300"
                            :style="answers[{{ $itemId }}] !== 'OK' ? 'border-color: #fecaca; background-color: rgba(254, 242, 242, 0.1);' : 'border-color: #f1f5f9;'">

                            <div class="flex flex-col lg:flex-row gap-6">
                                <!-- Question -->
                                <div class="flex-1">
                                    <div class="flex items-start gap-3">
                                        <span class="flex-shrink-0 w-6 h-6 rounded-full bg-slate-100 text-slate-500 text-xs font-bold flex items-center justify-center mt-0.5">
                                            {{ $loop->iteration }}
                                        </span>
                                        <div>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100 mb-2">
                                                {{ $item->clause_number }}
                                            </span>
                                            <p class="text-slate-800 font-medium text-base leading-relaxed">{{ $item->requirement_desc }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Area -->
                                <div class="flex-shrink-0 flex flex-col sm:flex-row gap-4 lg:w-[480px]">
                                    <!-- Radio Options -->
                                    <div class="grid grid-cols-4 sm:flex sm:items-center sm:justify-center gap-2 bg-slate-50 rounded-lg p-1.5 w-full sm:w-auto self-start">
                                        @foreach(['OK' => ['icon' => 'fa-circle', 'textColor' => '#22c55e', 'bgColor' => '#f0fdf4'],
                                                  'OFI' => ['icon' => 'fa-info-circle', 'textColor' => '#3b82f6', 'bgColor' => '#eff6ff'],
                                                  'Minor' => ['icon' => 'fa-exclamation-triangle', 'textColor' => '#f97316', 'bgColor' => '#fff7ed'],
                                                  'Mayor' => ['icon' => 'fa-times', 'textColor' => '#ef4444', 'bgColor' => '#fef2f2']] as $val => $style)
                                        <label class="cursor-pointer relative w-full sm:w-auto flex justify-center">
                                            <input type="radio"
                                                name="answers[{{ $itemId }}]"
                                                value="{{ $val }}"
                                                class="peer sr-only"
                                                @click="updateAnswer({{ $itemId }}, '{{ $val }}')"
                                                :checked="answers[{{ $itemId }}] === '{{ $val }}'"
                                                :disabled="isReadOnly">
                                            <div class="w-full sm:px-3 h-10 rounded-md flex items-center justify-center text-slate-300 hover:bg-white hover:text-slate-400 transition-all peer-checked:ring-1 peer-checked:ring-offset-1 peer-checked:ring-slate-200"
                                                :style="answers[{{ $itemId }}] === '{{ $val }}' ? 'background-color: {{ $style['bgColor'] }}; color: {{ $style['textColor'] }};' : ''"
                                                title="{{ $val }}">
                                                <i class="fas {{ $style['icon'] }} text-base mr-1"></i>
                                                <span class="text-xs font-bold">{{ $val }}</span>
                                            </div>
                                        </label>
                                        @endforeach
                                    </div>

                                    <!-- Camera/Evidences Trigger -->
                                    <div x-show="answers[{{ $itemId }}] !== 'OK'"
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 translate-x-4"
                                        x-transition:enter-end="opacity-100 translate-x-0"
                                        class="flex-1">
                                        <button @click="openModal({{ $itemId }})"
                                            class="w-full flex items-center justify-center gap-2 px-3 py-3 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors border border-blue-100 group-hover:border-blue-200"
                                            :class="hasFinding({{ $itemId }}) ? 'bg-green-50 text-green-600 border-green-200 hover:bg-green-100' : ''">
                                            <i class="fas" :class="isReadOnly ? 'fa-eye' : (hasFinding({{ $itemId }}) ? 'fa-check-circle' : 'fa-camera')"></i>
                                            <span class="font-medium text-xs whitespace-nowrap" x-text="isReadOnly ? 'View Finding Details' : (hasFinding({{ $itemId }}) ? 'Evidence Added' : 'Add Finding Evidence')"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal for Item {{ $itemId }} -->
                        <div x-show="activeModal === {{ $itemId }}"
                            style="display: none;"
                            class="fixed inset-0 z-[60] overflow-hidden"
                            aria-labelledby="modal-title" role="dialog" aria-modal="true">
                            <div class="fixed inset-0 flex items-center justify-center p-0 md:p-4">
                                <div x-show="activeModal === {{ $itemId }}"
                                    x-transition:enter="ease-out duration-300"
                                    x-transition:enter-start="opacity-0"
                                    x-transition:enter-end="opacity-100"
                                    x-transition:leave="ease-in duration-200"
                                    x-transition:leave-start="opacity-100"
                                    x-transition:leave-end="opacity-0"
                                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                                    @click="closeModal()"></div>

                                <div x-show="activeModal === {{ $itemId }}"
                                    x-transition:enter="ease-out duration-300"
                                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                    x-transition:leave="ease-in duration-200"
                                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                    class="relative bg-white text-left overflow-hidden transform transition-all flex flex-col w-full h-full rounded-none md:w-[95vw] md:max-w-4xl md:max-h-[85vh] md:rounded-2xl shadow-xl">

                                    <!-- Modal Header -->
                                    <div class="bg-white border-b border-slate-200 px-8 py-5 flex justify-between items-center flex-shrink-0">
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-800">Finding & Evidence Details</h3>
                                            <p class="text-slate-500 text-xs mt-0.5">Clause: {{ $item->clause_number }}</p>
                                        </div>
                                        <button @click="closeModal()" class="text-slate-400 hover:text-slate-600 transition-colors p-1">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Modal Body - 2 Column Layout -->
                                    <div class="px-8 py-6 bg-slate-50 flex-1 overflow-y-auto">
                                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                            <!-- Left Column -->
                                            <div class="space-y-5">
                                                <!-- Box: Evidence Photos -->
                                                <div class="bg-white p-5 rounded-xl border border-slate-200">
                                                    <h4 class="font-semibold text-slate-800 mb-4 flex items-center gap-2 text-sm uppercase tracking-wider">
                                                        <i class="fas fa-image text-blue-500"></i> Finding Attachment
                                                    </h4>

                                                    <div class="grid grid-cols-2 gap-3" x-show="!isReadOnly">
                                                        <div class="relative group">
                                                            <input type="file" id="cameraInput_{{ $itemId }}" accept="image/*" capture="environment"
                                                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                                                @change="handleFileUpload($event, {{ $itemId }})">
                                                            <div class="flex flex-col items-center justify-center p-4 border-2 border-dashed border-blue-200 rounded-lg bg-blue-50/50 group-hover:bg-blue-50 group-hover:border-blue-300 transition-all">
                                                                <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                                                                    <i class="fas fa-camera text-sm"></i>
                                                                </div>
                                                                <span class="text-xs font-semibold text-blue-600">Take Photo</span>
                                                            </div>
                                                        </div>

                                                        <div class="relative group">
                                                            <input type="file" id="uploadInput_{{ $itemId }}" accept="image/*"
                                                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                                                @change="handleFileUpload($event, {{ $itemId }})">
                                                            <div class="flex flex-col items-center justify-center p-4 border-2 border-dashed border-slate-200 rounded-lg bg-slate-50/50 group-hover:bg-slate-50 transition-all">
                                                                <div class="w-8 h-8 bg-slate-100 text-slate-500 rounded-full flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                                                                    <i class="fas fa-images text-sm"></i>
                                                                </div>
                                                                <span class="text-xs font-semibold text-slate-600">Upload Photo</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="mt-4 flex justify-center" id="preview_container_{{ $itemId }}"></div>
                                                </div>
                                            </div>

                                            <!-- Right Column -->
                                            <div class="space-y-5">
                                                <!-- Box: Finding / Comments -->
                                                <div class="bg-white p-5 rounded-xl border border-slate-200">
                                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Findings / Observation Comments <span class="text-red-500">*</span></label>
                                                    <textarea id="findings_{{ $itemId }}" rows="5" 
                                                        class="w-full border border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm outline-none p-3 rounded-lg" 
                                                        placeholder="Describe the issue..."
                                                        :disabled="isReadOnly"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal Footer -->
                                    <div class="bg-white border-t border-slate-200 px-8 py-4 flex justify-end gap-3 flex-shrink-0">
                                        <button @click="closeModal()" type="button" class="px-5 py-2.5 bg-white text-slate-700 border border-slate-300 rounded-lg hover:bg-slate-50 font-medium" x-text="isReadOnly ? 'Close' : 'Cancel'">
                                        </button>
                                        <button @click="saveEvidence({{ $itemId }})" type="button" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium" x-show="!isReadOnly">
                                            Save Details
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>


    </main>

    @include('layouts.footer')
    <div class="h-20 sm:hidden"></div>
</div>

<!-- Mobile Sidebar Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/50 z-30 hidden lg:hidden"></div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('genbaForm', () => ({
            isReadOnly: {{ $schedule->status === 'Done' ? 'true' : 'false' }},
            isLoading: false,
            answers: {},
            activeModal: null,
            evidenceData: {},

            initForm() {
                @foreach ($items as $item)
                @php
                    $detail = $details[$item->id] ?? null;
                    $judgment = $detail ? $detail->judgment : 'OK';
                    $evidence = $detail && $detail->evidence ? $detail->evidence : '';
                    $photo = $detail && $detail->finding_photo_path ? asset($detail->finding_photo_path) : null;
                @endphp
                this.answers[{{ $item->id }}] = '{{ $judgment }}';
                this.evidenceData[{{ $item->id }}] = {
                    evidence: {!! json_encode($evidence) !!},
                    photo: {!! json_encode($photo) !!}
                };
                @endforeach
            },

            hasFinding(itemId) {
                const data = this.evidenceData[itemId];
                return data && ((data.evidence && data.evidence.trim() !== '') || data.photo !== null);
            },

            updateAnswer(itemId, val) {
                this.answers[itemId] = val;
            },

            openModal(itemId) {
                this.activeModal = itemId;
                document.body.style.overflow = 'hidden';

                const data = this.evidenceData[itemId];
                document.getElementById(`findings_${itemId}`).value = data.evidence;

                const container = document.getElementById(`preview_container_${itemId}`);
                container.innerHTML = '';
                if (data.photo) {
                    this.addThumbnail(itemId, data.photo);
                }
            },

            closeModal() {
                this.activeModal = null;
                document.body.style.overflow = '';
            },

            handleFileUpload(event, itemId) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const container = document.getElementById(`preview_container_${itemId}`);
                        container.innerHTML = '';
                        this.addThumbnail(itemId, e.target.result);
                    };
                    reader.readAsDataURL(file);
                }
            },

            addThumbnail(itemId, url) {
                const container = document.getElementById(`preview_container_${itemId}`);
                const div = document.createElement('div');
                div.className = "relative group rounded-lg overflow-hidden w-40 h-40 bg-slate-100 border border-slate-200";
                let btnHtml = '';
                if (!this.isReadOnly) {
                    btnHtml = `
                    <button type="button" onclick="window.removePhoto(${itemId})" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 w-6 h-6 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <i class="fas fa-times text-xs"></i>
                    </button>`;
                }
                div.innerHTML = `
                    <img src="${url}" class="w-full h-full object-cover">
                    ${btnHtml}
                `;
                container.appendChild(div);
            },

            saveEvidence(itemId) {
                const findingsVal = document.getElementById(`findings_${itemId}`).value;
                const container = document.getElementById(`preview_container_${itemId}`);
                const img = container.querySelector('img');
                const photoSrc = img ? img.src : null;

                if (this.answers[itemId] !== 'OK' && findingsVal.trim() === '') {
                    showToast('Finding / Observation comments are required.', 'error');
                    return;
                }

                this.evidenceData[itemId].evidence = findingsVal;
                this.evidenceData[itemId].photo = photoSrc;

                this.closeModal();
                showToast('Evidence details saved to draft.', 'success');
            },

            submitForm() {
                let isValid = true;
                let errorMsgs = [];
                for (const itemId in this.answers) {
                    if (this.answers[itemId] !== 'OK') {
                        const data = this.evidenceData[itemId];
                        if (!data || !data.evidence || data.evidence.trim() === '') {
                            isValid = false;
                            errorMsgs.push(`Clause #${itemId} requires finding comments.`);
                        }
                    }
                }

                if (!isValid) {
                    showToast(errorMsgs.join(' '), 'error');
                    return;
                }

                this.isLoading = true;

                const results = {};
                for (const itemId in this.answers) {
                    results[itemId] = {
                        judgment: this.answers[itemId],
                        evidence: this.evidenceData[itemId].evidence,
                        photo: this.evidenceData[itemId].photo
                    };
                }

                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                fetch("{{ route('internal_audit.submit') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        _token: token,
                        schedule_id: {{ $schedule->id }},
                        audit_date: '{{ $schedule->schedule_date }}',
                        auditor_names: '{{ $schedule->auditor_niks }}',
                        auditee_dept: '{{ $schedule->auditee_dept }}',
                        results: results
                    })
                })
                .then(res => res.json())
                .then(data => {
                    this.isLoading = false;
                    if (data.success) {
                        showToast(data.message, 'success');
                        setTimeout(() => {
                            window.location.href = "{{ route('internal_audit') }}";
                        }, 1500);
                    } else {
                        showToast(data.message, 'error');
                    }
                })
                .catch(err => {
                    this.isLoading = false;
                    console.error('Error submitting audit:', err);
                    showToast('Failed to submit audit results.', 'error');
                });
            }
        }));
    });

    window.removePhoto = function(itemId) {
        const container = document.getElementById(`preview_container_${itemId}`);
        if (container) container.innerHTML = '';
        const cam = document.getElementById(`cameraInput_${itemId}`);
        if (cam) cam.value = '';
        const upl = document.getElementById(`uploadInput_${itemId}`);
        if (upl) upl.value = '';
    };
</script>
@endpush
@endsection