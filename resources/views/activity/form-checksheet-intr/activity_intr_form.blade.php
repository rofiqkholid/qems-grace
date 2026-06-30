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
    <div x-show="isLoading" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/20">
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
                            <div class="text-[12px] text-slate-400">Audit Date</div>
                            <div class="text-sm font-semibold text-slate-800 mt-1">{{\Carbon\Carbon::parse($schedule->schedule_date)->format('d M Y')}}</div>
                        </div>
                        <div>
                            <div class="text-[12px] text-slate-400">Auditor</div>
                            <div class="text-sm font-semibold text-slate-800 mt-1">{{ $schedule->auditor_niks }}</div>
                        </div>
                        
                        <div class="col-span-2 border-t border-slate-100 my-1"></div>
                        
                        <div class="col-span-2">
                            <div class="text-[12px] text-slate-400">Auditee</div>
                            <div class="text-sm font-semibold text-slate-800 mt-1">{{ $schedule->auditee }}</div>
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
                        @if($items->isEmpty())
                            <div class="flex flex-col items-center justify-center py-16 px-4 rounded-2xl border border-dashed border-slate-200 bg-slate-50/50">
                                <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mb-3">
                                    <i class="fa-solid fa-clipboard-question text-slate-400 text-xl"></i>
                                </div>
                                <h3 class="text-sm font-semibold text-slate-800">Tidak ada item checksheet</h3>
                                <p class="text-xs text-slate-500 mt-1.5 text-center leading-relaxed">
                                    Belum ada item checksheet untuk departemen ini.<br>
                                    <span class="text-slate-400 italic">No checksheet items available for this department.</span>
                                </p>
                            </div>
                        @else
                            @php
                                $groupedItems = $items->groupBy(function($item) {
                                    return $item->scope_item ?: 'General';
                                });
                                $globalIteration = 1;
                            @endphp

                            @foreach ($groupedItems as $scopeName => $scopeGroup)
                        <div class="mb-8 last:mb-0">
                            <div class="flex items-center justify-between mb-4 px-1">
                                <h3 class="text-base sm:text-lg font-bold text-slate-800 flex items-center gap-2">
                                    {{ $scopeName }}
                                </h3>
                            </div>
                            <div class="space-y-4">
                                @foreach ($scopeGroup as $item)
                                @php 
                                    $itemId = $item->id; 
                                    $currentNo = $globalIteration++;
                                @endphp
                                <div class="group relative rounded-xl border bg-white p-5 transition-all duration-300"
                                    :style="answers[{{ $itemId }}] !== 'OK' && answers[{{ $itemId }}] !== '' ? 'border-color: #fecaca; background-color: rgba(254, 242, 242, 0.1);' : 'border-color: #f1f5f9;'">

                                    <div class="flex flex-col lg:flex-row gap-6">
                                        <!-- Question -->
                                        <div class="flex-1">
                                            <div class="flex items-start gap-3">
                                                <span class="flex-shrink-0 w-6 h-6 rounded-full bg-slate-100 text-slate-500 text-xs font-bold flex items-center justify-center mt-0.5">
                                                    {{ $currentNo }}
                                                </span>
                                                <div class="flex-1">
                                                    
                                                    <p class="text-slate-800 font-medium text-base leading-relaxed">{{ $item->check_item_idn }}</p>
                                                    <p class="text-slate-500 text-sm mt-1 leading-relaxed">{{ $item->check_item_en }}</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Action Area -->
                                        <div class="flex flex-col sm:flex-row items-center gap-4">
                                            <!-- Radio Options -->
                                            <div class="grid grid-cols-4 sm:flex sm:items-center sm:justify-center gap-2 bg-slate-50 rounded-lg p-1.5 w-full sm:w-auto">
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

                                            <!-- Camera/Evidences/Note Trigger -->
                                            <div :class="answers[{{ $itemId }}] !== '' ? 'opacity-100' : 'opacity-0 pointer-events-none'"
                                                class="w-full sm:w-44 transition-all duration-200">
                                                
                                                <!-- For Minor and Mayor: Link to CAR Form -->
                                                <template x-if="answers[{{ $itemId }}] === 'Minor' || answers[{{ $itemId }}] === 'Mayor'">
                                                    <a :href="'{{ route('internal_audit.car_form', ['schedule_id' => $schedule->hash_id, 'item_id' => $itemId]) }}?judgment=' + answers[{{ $itemId }}]"
                                                        class="w-full flex items-center justify-center gap-2 px-3 h-[52px] rounded-lg transition-colors border"
                                                        :class="hasFinding({{ $itemId }}) ? 'bg-green-50 text-green-600 border-green-200 hover:!bg-green-100 hover:!border-green-300 hover:!text-green-700' : 'bg-blue-50 text-blue-600 border-blue-100 hover:!bg-blue-100 hover:!border-blue-300 hover:!text-blue-700'">
                                                        <i class="fas" :class="isReadOnly ? 'fa-eye' : (hasFinding({{ $itemId }}) ? 'fa-check-circle' : 'fa-camera')"></i>
                                                        <span class="font-medium text-xs whitespace-nowrap" x-text="isReadOnly ? 'View Finding Details' : (hasFinding({{ $itemId }}) ? 'Report Added' : 'Add Report')"></span>
                                                    </a>
                                                </template>

                                                <!-- For OK and OFI: Open Note/Evidence Modal -->
                                                <template x-if="answers[{{ $itemId }}] === 'OK' || answers[{{ $itemId }}] === 'OFI'">
                                                    <button type="button" @click="openNoteModal({{ $itemId }})"
                                                        class="w-full flex items-center justify-center gap-2 px-3 h-[52px] rounded-lg transition-colors border"
                                                        :class="hasNote({{ $itemId }}) ? 'bg-green-50 text-green-600 border-green-200 hover:!bg-green-100 hover:!border-green-300 hover:!text-green-700' : 'bg-blue-50 text-blue-600 border-blue-100 hover:!bg-blue-100 hover:!border-blue-300 hover:!text-blue-700'">
                                                        <i class="fas" :class="hasNote({{ $itemId }}) ? 'fa-check-circle' : 'fa-sticky-note'"></i>
                                                        <span class="font-medium text-xs whitespace-nowrap" x-text="hasNote({{ $itemId }}) ? 'Evidence Added' : 'Add Evidence'"></span>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>


    </main>

    @include('layouts.footer')
    <div class="h-20 sm:hidden"></div>

    <!-- Note Modal -->
    <div x-show="noteModalOpen" 
         style="display: none;" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         role="dialog" 
         aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background Overlay -->
            <div x-show="noteModalOpen"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 transition-opacity" 
                 @click="closeNoteModal()"></div>

            <!-- Trick the browser into centering the modal contents. -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal Panel -->
            <div x-show="noteModalOpen"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
                
                <div class="bg-white px-6 pt-6 pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-50 text-blue-600 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fa-solid fa-sticky-note text-lg"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg font-bold text-slate-800 leading-6" id="modal-title">
                                Add Evidence
                            </h3>
                            <p class="text-xs text-slate-400 mt-1">
                                Sebagai menu untuk input saran dan dokumen yang sudah berjalan.
                            </p>
                            
                            <div class="mt-4">
                                <textarea x-model="noteModalText" 
                                          rows="4" 
                                          class="w-full border border-slate-300 rounded-lg p-3 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none resize-none"
                                          placeholder="Tulis evidence atau catatan hasil audit (baik nama, nomor dokumen, PIC yang diperiksa dan deskripsi implementasinya)."
                                          :disabled="isReadOnly"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-slate-50 px-6 py-4 flex flex-row-reverse gap-2">
                    <button type="button" 
                            @click="saveNote()" 
                            x-show="!isReadOnly"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium shadow-sm transition-colors">
                        Save Evidence
                    </button>
                    <button type="button" 
                            @click="closeNoteModal()" 
                            class="px-4 py-2 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 rounded-lg text-sm font-medium transition-colors">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
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
                    $judgment = $detail ? $detail->judgment : '';
                    $evidence = $detail ? ($detail->note ?: ($detail->evidence ?? '')) : '';
                    $car_finding = $detail ? ($detail->car_finding ?? '') : '';
                    $photo = $detail && $detail->finding_photo_path ? asset($detail->finding_photo_path) : null;
                @endphp
                this.answers[{{ $item->id }}] = '{{ $judgment }}';
                this.evidenceData[{{ $item->id }}] = {
                    evidence: {!! json_encode($evidence) !!},
                    car_finding: {!! json_encode($car_finding) !!},
                    photo: {!! json_encode($photo) !!}
                };
                @endforeach
            },

            noteModalOpen: false,
            noteModalItemId: null,
            noteModalText: '',

            hasFinding(itemId) {
                const data = this.evidenceData[itemId];
                return data && ((data.car_finding && data.car_finding.trim() !== '') || data.photo !== null);
            },

            hasNote(itemId) {
                const data = this.evidenceData[itemId];
                return data && data.evidence && data.evidence.trim() !== '';
            },

            openNoteModal(itemId) {
                this.noteModalItemId = itemId;
                const data = this.evidenceData[itemId];
                this.noteModalText = data ? (data.evidence || '') : '';
                this.noteModalOpen = true;
                document.body.style.overflow = 'hidden';
            },

            closeNoteModal() {
                this.noteModalOpen = false;
                this.noteModalItemId = null;
                this.noteModalText = '';
                document.body.style.overflow = '';
            },

            saveNote() {
                const itemId = this.noteModalItemId;
                if (!itemId) return;

                if (!this.evidenceData[itemId]) {
                    this.evidenceData[itemId] = { evidence: '', photo: null };
                }
                this.evidenceData[itemId].evidence = this.noteModalText;

                if (!this.isReadOnly) {
                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    fetch("{{ route('internal_audit.save_judgment') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify({
                            _token: token,
                            schedule_id: {{ $schedule->id }},
                            checksheet_item_id: itemId,
                            judgment: this.answers[itemId],
                            note: this.noteModalText
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (!data.success) {
                            showToast(data.message, 'error');
                        }
                    })
                    .catch(err => {
                        console.error('Error saving note:', err);
                        showToast('Failed to auto-save evidence.', 'error');
                    });
                }

                this.closeNoteModal();
                showToast('Evidence saved successfully.', 'success');
            },

            updateAnswer(itemId, val) {
                this.answers[itemId] = val;

                if (this.isReadOnly) return;

                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                fetch("{{ route('internal_audit.save_judgment') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        _token: token,
                        schedule_id: {{ $schedule->id }},
                        checksheet_item_id: itemId,
                        judgment: val
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        showToast(data.message, 'error');
                    }
                })
                .catch(err => {
                    console.error('Error saving judgment:', err);
                    showToast('Failed to auto-save judgment.', 'error');
                });
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
                @php $globalIteration = 1; @endphp
                @foreach ($items as $item)
                {
                    const itemId = {{ $item->id }};
                    const ans = this.answers[itemId];
                    if (!ans || ans === '') {
                        isValid = false;
                        errorMsgs.push("Item {{ $globalIteration }} must be judged.");
                    } else if (ans === 'OFI') {
                        const data = this.evidenceData[itemId];
                        if (!data || !data.evidence || data.evidence.trim() === '') {
                            isValid = false;
                            errorMsgs.push("Item {{ $globalIteration }} requires finding comments.");
                        }
                    } else if (ans === 'Minor' || ans === 'Mayor') {
                        const data = this.evidenceData[itemId];
                        if (!data || !data.car_finding || data.car_finding.trim() === '') {
                            isValid = false;
                            errorMsgs.push("Item {{ $globalIteration }} requires finding comments.");
                        }
                    }
                }
                @php $globalIteration++; @endphp
                @endforeach

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