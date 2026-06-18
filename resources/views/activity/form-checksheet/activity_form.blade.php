@extends('layouts.app')

@section('title', 'Genba Form')

@section('content')
@include('layouts.sidebar')

<x-toast />
<div class="lg:ml-20 min-h-screen flex flex-col bg-slate-50"
    x-data="genbaForm()"
    x-init="initForm()">

    @include('layouts.header')

    <!-- Loading State -->
    <div x-show="isLoading" class="fixed inset-0 z-[100] flex items-center justify-center">
        <div class="flex flex-col items-center">
            <div class="animate-spin rounded-[50%] h-12 w-12 border-b-2 border-blue-600"></div>
        </div>
    </div>
    <!-- Page Content -->
    <main class="flex-1 p-6">

        <!-- Simple Header -->
        <div class="mb-6">
            <div class="flex items-center gap-3">
                <button onclick="backHome()" class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-blue-50 border border-blue-200 text-blue-600 hover:bg-blue-50 hover:text-blue-900 transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
                <h1 class="text-base md:text-2xl font-bold text-slate-800">Genba Form</h1>
            </div>
        </div>

        <input type="hidden" name="activity_id" value="{{ $id_activity }}" id="activity_id">

        @php
        $initialAnswers = [];
        foreach ($scopes as $scope => $items) {
        foreach ($items as $item) {
        $initialAnswers[$item['check_item_id']] = $item['result'] > 0 ? (int)$item['result'] : null;
        }
        }
        @endphp
        <input type="hidden" id="initial_answers_data" value="{{ json_encode($initialAnswers) }}">
        <input type="hidden" id="initial_finding_status" value="{{ json_encode($finding_status ?? []) }}">

        <!-- Scopes & Items -->
        <div class="space-y-8">
            @php $no = 0; @endphp
            @foreach ($scopes as $scope => $items)
            <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden" id="kt_card_{{ $no }}">
                <div class="px-8 py-6 flex justify-between items-center">
                    <h2 class="text-xl font-bold text-slate-800 flex items-center gap-3">
                        {{ $scope }}
                    </h2>
                    <div class="relative" x-data="{ showPopover: false }">
                        <button @click="showPopover = !showPopover" @click.outside="showPopover = false" class="text-slate-400 hover:text-primary-600 transition-colors">
                            <i class="fa fa-info-circle text-xl"></i>
                        </button>
                        <div x-show="showPopover"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-2"
                            style="display: none;"
                            class="absolute right-0 top-full mt-2 w-72 p-4 bg-white rounded-xl border border-slate-200 z-[100]">
                            <h3 class="text-sm font-semibold text-slate-700 mb-2">Process</h3>
                            <div class='text-sm space-y-3 text-slate-600'>
                                <div class='flex items-start gap-2'>
                                    <i class='fa fa-circle text-blue-500 mt-1 text-xs'></i>
                                    <span>Jika telah sesuai dengan persyaratan/ poin cek</span>
                                </div>
                                <div class='flex items-start gap-2'>
                                    <i class='fa fa-exclamation-triangle text-yellow-500 mt-0.5'></i>
                                    <span>Jika persyaratan/item check sudah dilakukan namun tidak maksimal / tidak konsisten / masih perlu dilakukan improvement</span>
                                </div>
                                <div class='flex items-start gap-2'>
                                    <i class='fa fa-times text-red-500 mt-0.5'></i>
                                    <span>Jika tidak sesuai dengan persyaratan / poin cek</span>
                                </div>
                            </div>
                            <!-- Arrow -->
                            <div class="absolute -top-1.5 right-3 w-3 h-3 bg-white border-t border-l border-slate-200 transform rotate-45"></div>
                        </div>
                    </div>
                </div>

                <div class="px-8 pb-8 space-y-6">
                    @foreach ($items as $item)
                    @php $itemId = $item['check_item_id']; @endphp
                    <div class="group relative rounded-xl border bg-white p-5 transition-all duration-300"
                        :style="answers[{{ $itemId }}] == 3 ? 'border-color: #fecaca; background-color: rgba(254, 242, 242, 0.1);' : (answers[{{ $itemId }}] == 2 ? 'border-color: #fef08a; background-color: rgba(254, 252, 232, 0.1);' : 'border-color: #f1f5f9;')">

                        <div class="flex flex-col lg:flex-row gap-6">
                            <!-- Question -->
                            <div class="flex-1">
                                <div class="flex items-start gap-3">
                                    <span class="flex-shrink-0 w-6 h-6 rounded-full bg-slate-100 text-slate-500 text-xs font-bold flex items-center justify-center mt-0.5">
                                        {{ $loop->iteration }}
                                    </span>
                                    <div>
                                        <p class="text-slate-800 font-medium text-base leading-relaxed">{{ $item['check_item'] }}</p>
                                        <p class="text-slate-500 text-sm mt-1">{{ $item['check_item_eng'] }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Area -->
                            <div class="flex-shrink-0 flex flex-col sm:flex-row gap-4 lg:w-[550px]">
                                <!-- Radio Options -->
                                <div class="grid grid-cols-3 sm:flex sm:items-center sm:justify-center gap-2 bg-slate-50 rounded-lg p-1.5 w-full sm:w-auto self-start">
                                    <input type="hidden" id="scope_id_{{ $itemId }}" value="{{ $item['scope_id'] }}">

                                    @foreach([1 => ['icon' => 'fa-circle', 'textColor' => '#22c55e', 'bgColor' => '#f0fdf4'],
                                    2 => ['icon' => 'fa-exclamation-triangle', 'textColor' => '#eab308', 'bgColor' => '#fefce8'],
                                    3 => ['icon' => 'fa-times', 'textColor' => '#ef4444', 'bgColor' => '#fef2f2']] as $val => $style)
                                    <label class="cursor-pointer relative w-full sm:w-auto flex justify-center">
                                        <input type="radio"
                                            name="answers[{{ $itemId }}]"
                                            value="{{ $val }}"
                                            class="peer sr-only"
                                            @click="updateAnswer({{ $itemId }}, {{ $val }})"
                                            {{ $item['result'] == $val ? 'checked' : '' }}>
                                        <div class="w-full sm:w-10 h-10 rounded-md flex items-center justify-center text-slate-300 hover:bg-white hover:text-slate-400 transition-all peer-checked:ring-1 peer-checked:ring-offset-1 peer-checked:ring-slate-200"
                                            style="--checked-bg: {{ $style['bgColor'] }}; --checked-text: {{ $style['textColor'] }};"
                                            :style="answers[{{ $itemId }}] == {{ $val }} ? 'background-color: {{ $style['bgColor'] }}; color: {{ $style['textColor'] }};' : ''">
                                            <i class="fas {{ $style['icon'] }} text-lg"></i>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>

                                <!-- Camera/Evidences Trigger -->
                                <div x-show="answers[{{ $itemId }}] > 1"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-x-4"
                                    x-transition:enter-end="opacity-100 translate-x-0"
                                    class="flex-1">
                                    <div class="grid grid-cols-3 gap-2">
                                        @for ($i = 1; $i <= 3; $i++)
                                            <button @click="openModal({{ $itemId }}, '{{ $item['scope_id'] }}', {{ $i }})"
                                            class="w-full flex flex-col sm:flex-row items-center justify-center gap-1 sm:gap-2 px-1 sm:px-2 py-2 sm:py-3 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors border border-blue-100 group-hover:border-blue-200"
                                            :class="hasFinding({{ $itemId }}, {{ $i }}) ? 'bg-green-50 text-green-600 border-green-200 hover:bg-green-100' : ''">
                                            <i class="fas text-sm sm:text-base" :class="hasFinding({{ $itemId }}, {{ $i }}) ? 'fa-check-circle' : 'fa-camera'"></i>
                                            <span class="font-medium text-[10px] sm:text-xs whitespace-nowrap">Finding {{ $i }}</span>
                                            </button>
                                            @endfor
                                    </div>
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
                                class="relative bg-white text-left overflow-hidden transform transition-all flex flex-col w-full h-full rounded-none md:w-[95vw] md:max-w-6xl md:max-h-[90vh] md:rounded-2xl">

                                <!-- Modal Header -->
                                <div class="bg-white border-b border-slate-200 px-8 py-5 flex justify-between items-center flex-shrink-0">
                                    <h3 class="text-xl font-bold text-gray-800">Finding Photo</h3>
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
                                                <h4 class="font-semibold text-slate-800 mb-4 flex items-center gap-2 text-base">
                                                    <i class="fas fa-image text-blue-500"></i> Finding Captured
                                                </h4>

                                                <div class="grid grid-cols-2 gap-3" x-data>
                                                    <div class="relative group">
                                                        <input type="file" id="cameraInput_{{ $itemId }}" accept="image/*" capture="environment"
                                                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                                            @change="handleFileUpload($event, {{ $itemId }})">
                                                        <div class="flex flex-col items-center justify-center p-4 border-2 border-dashed border-blue-200 rounded-lg bg-blue-50/50 group-hover:bg-blue-50 group-hover:border-blue-300 transition-all">
                                                            <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                                                                <i class="fas fa-camera"></i>
                                                            </div>
                                                            <span class="text-sm font-medium text-blue-600">Take Photo</span>
                                                        </div>
                                                    </div>

                                                    <div class="relative group">
                                                        <input type="file" id="uploadInput_{{ $itemId }}" multiple accept="image/*"
                                                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                                            @change="handleFileUpload($event, {{ $itemId }})">
                                                        <div class="flex flex-col items-center justify-center p-4 border-2 border-dashed border-slate-200 rounded-lg bg-slate-50/50 group-hover:bg-slate-50 transition-all">
                                                            <div class="w-10 h-10 bg-slate-100 text-slate-500 rounded-full flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                                                                <i class="fas fa-images"></i>
                                                            </div>
                                                            <span class="text-sm font-medium text-slate-600">From Gallery</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mt-4 grid grid-cols-4 gap-2" id="preview_container_{{ $itemId }}"></div>
                                                <input type="hidden" name="photo_names[]" id="photoname_{{ $itemId }}">
                                            </div>

                                            <!-- Box: Detailed Area -->
                                            <div class="bg-white p-5 rounded-xl border border-slate-200">
                                                <div class="grid grid-cols-3 gap-4 items-center">
                                                    <label class="text-sm text-slate-600 font-semibold">Area / Process</label>
                                                    <div class="col-span-2">
                                                        <input type="text" value="{{ $process }}" disabled
                                                            class="w-full px-4 py-[9px] bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-500 cursor-not-allowed outline-none">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Right Column -->
                                        <div class="space-y-5">
                                            <!-- Box: Assign To -->
                                            <div class="bg-white p-5 rounded-xl border border-slate-200">
                                                <x-searchable-select
                                                    id="asign_to_dept_{{ $itemId }}"
                                                    name="asign_to_dept_{{ $itemId }}"
                                                    label="Assign to"
                                                    :apiUrl="route('genba.get_section')"
                                                    required="true" />
                                            </div>

                                            <!-- Box: Station -->
                                            <div class="bg-white p-5 rounded-xl border border-slate-200">
                                                <div class="grid grid-cols-3 gap-4 items-center">
                                                    <label class="text-sm text-slate-600 font-semibold">Station / Mech. Num <span class="text-red-500">*</span></label>
                                                    <div class="col-span-2">
                                                        <x-searchable-select 
                                                            id="area_detail_{{ $itemId }}" 
                                                            name="area_detail_{{ $itemId }}" 
                                                            label="Station" 
                                                            :apiUrl="route('genba.get_stations')"
                                                            required="true"
                                                            :hideLabel="true" />
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Box: Related -->
                                            <div class="bg-white p-5 rounded-xl border border-slate-200">
                                                <div class="grid grid-cols-3 gap-4 items-center">
                                                    <label class="text-sm text-slate-600 font-semibold">Related <span class="text-red-500">*</span></label>
                                                    <div class="col-span-2 relative" x-data="{
                                                        open: false,
                                                        search: '',
                                                        selectedName: '',
                                                        selectedId: '',
                                                        items: {{ json_encode($finding_types) }},
                                                        
                                                        init() {
                                                            this.items = this.items.map(opt => (typeof opt === 'object' ? opt : { id: opt, name: opt }));
                                                        },

                                                        closeDropdown() {
                                                            this.open = false;
                                                            this.search = this.selectedName || '';
                                                        },

                                                        toggle() {
                                                            if (this.open) {
                                                                this.closeDropdown();
                                                            } else {
                                                                this.open = true;
                                                            }
                                                        },

                                                        select(item) {
                                                            this.selectedName = item.name;
                                                            this.selectedId = item.id;
                                                            this.search = item.name;
                                                            this.open = false;
                                                            $('#type_{{ $itemId }}').val(item.id);
                                                            document.getElementById('type_{{ $itemId }}').dispatchEvent(new Event('change'));
                                                        }
                                                    }">
                                                        <input type="hidden" id="type_{{ $itemId }}" name="type" required>

                                                        <div class="relative">
                                                            <input type="text" x-model="search" @click="toggle" @click.outside="closeDropdown()"
                                                                placeholder="Select Related..."
                                                                class="w-full px-4 py-[9px] border border-slate-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none text-slate-700">
                                                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                                                <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                                                            </div>
                                                        </div>

                                                        <div x-show="open"
                                                            x-transition:enter="transition ease-out duration-100"
                                                            x-transition:enter-start="opacity-0 scale-95"
                                                            x-transition:enter-end="opacity-100 scale-100"
                                                            class="absolute z-10 w-full mt-1 bg-white border border-slate-200 rounded-lg max-h-60 overflow-y-auto">

                                                            <template x-if="items.length === 0">
                                                                <div class="px-4 py-3 text-sm text-slate-500 text-center">No options found</div>
                                                            </template>

                                                            <template x-for="item in items" :key="item.id">
                                                                <div x-show="!search || search === selectedName || item.name.toLowerCase().includes(search.toLowerCase())"
                                                                    @click="select(item)"
                                                                    class="pl-4 pr-8 py-2.5 text-sm cursor-pointer transition-colors hover:bg-slate-50 whitespace-normal break-words"
                                                                    :class="selectedId === item.id ? 'text-blue-600 bg-blue-50' : 'text-slate-700'">
                                                                    <span x-text="item.name"></span>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Box: Finding / Comments -->
                                            <div class="bg-white p-5 rounded-xl border border-slate-200">
                                                <label class="block text-sm font-semibold text-slate-700 mb-2">Finding / Comments <span class="text-red-500">*</span></label>
                                                <textarea id="findings_{{ $itemId }}" name="findings" rows="5" class="w-full rounded-none border border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm outline-none p-3" placeholder="Describe the issue..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Footer -->
                                <div class="bg-white border-t border-slate-200 px-8 py-4 flex justify-end gap-3 flex-shrink-0">
                                    <button @click="closeModal()" type="button" class="px-5 py-2.5 bg-white text-slate-700 border border-slate-300 rounded-lg hover:bg-slate-50 font-medium">
                                        Cancel
                                    </button>
                                    <button @click="saveEvidence({{ $itemId }})" type="button" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                                        Save Evidence
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @php $no++; @endphp
            @endforeach
        </div>

        <!-- Submit Button at Bottom -->
        <div class="fixed bottom-0 left-0 right-0 z-30 bg-white border-t border-slate-200 p-3 flex justify-between items-center sm:static sm:bg-transparent sm:border-0 sm:p-0 sm:mt-8 sm:mb-8 sm:justify-start sm:gap-4">
            <span class="text-[11px] sm:text-sm text-slate-400 font-medium max-w-[55%] sm:max-w-none leading-tight text-right sm:text-left order-1 sm:order-2">
                Please remember to submit after completing all checksheets.
            </span>
            <button @click="submitForm()"
                class="flex items-center gap-2 bg-green-50 hover:bg-green-100 text-green-600 border border-green-400 px-4 py-2 sm:px-8 sm:py-3 rounded-none font-bold text-sm sm:text-lg shrink-0 order-2 sm:order-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-6 sm:w-6" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                <span>Finish Audit</span>
            </button>
        </div>
    </main>
    @include('layouts.footer')
    <div class="h-20 sm:hidden"></div>
</div>

<!-- Mobile Sidebar Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/50 z-30 hidden lg:hidden"></div>

@push('scripts')
<script>
    const initialGenbaAnswers = JSON.parse(document.getElementById('initial_answers_data').value || '{}');
    const initialFindingStatus = JSON.parse(document.getElementById('initial_finding_status').value || '{}');

    document.addEventListener('alpine:init', () => {
        Alpine.data('genbaForm', () => ({
            isLoading: false,
            answers: initialGenbaAnswers,
            activeModal: null,
            activeFindingIndex: 1, // Track which finding we are editing (1, 2, or 3)
            findingStatus: initialFindingStatus, // Initialized from backend map
            cameraActive: false,
            stream: null,

            initForm() {
                console.log('Genba Form Initialized');



                // Initialize finding status (can be populated via API later if needed, 
                // but for now we rely on user interaction or fetching data)
                // We'll fetch status for all items on load if needed, or just lazy load.
                // For better UX, we might want to know immediately which findings exist.
                // For now, let's keep it simple and update on save.
            },

            hasFinding(itemId, index) {
                // Check if we have record of this finding being filled
                // This logic might need to be robustly hydrated from server on load
                const key = `${itemId}_${index}`;
                return this.findingStatus[key] === true;
            },

            updateAnswer(itemId, val) {
                this.answers[itemId] = val;

                // AJAX Sync to Backend
                let scopeId = document.getElementById(`scope_id_${itemId}`).value;
                let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                let activityId = document.getElementById('activity_id').value;

                // Optimistic update - no loader for radio clicks to feel snappy
                fetch("{{ route('genba.post_form_spv') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify({
                            activity_id: activityId,
                            scope_id: scopeId,
                            check_item_id: itemId,
                            answer: val,
                            _token: token
                        })
                    }).then(res => res.json())
                    .catch(err => console.error('Sync error:', err));
            },

            openModal(itemId, scopeId, findingIndex = 1) {
                this.activeModal = itemId;
                this.activeFindingIndex = findingIndex; // Set active index
                // this.cameraActive = false; // logic removed
                document.body.style.overflow = 'hidden';

                // Clear previous form data to avoid flickering old data
                document.getElementById(`findings_${itemId}`).value = '';
                const container = document.getElementById(`preview_container_${itemId}`);
                if (container) container.innerHTML = '';
                // Reset select if possible (might need more complex logic for component)

                // Fetch existing evidence data
                let activityId = document.getElementById('activity_id').value;
                let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                fetch("{{ route('genba.get_data_photo') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify({
                            activity_id: activityId,
                            scope_id: scopeId,
                            check_item_id: itemId,
                            finding_index: findingIndex, // Pass index
                            _token: token
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        // Populate findings
                        if (data.findings) {
                            document.getElementById(`findings_${itemId}`).value = data.findings;
                        }

                        // Populate station / area detail
                        const areaDetailInput = document.getElementById(`area_detail_${itemId}`);
                        if (areaDetailInput) {
                            $(`#area_detail_${itemId}`).val(data.area_detail || '');
                            
                            const alpineContainer = areaDetailInput.closest('[x-data]');
                            if (alpineContainer && alpineContainer._x_dataStack) {
                                const alpineData = alpineContainer._x_dataStack[0];
                                if (alpineData) {
                                    alpineData.selectedId = data.area_detail || '';
                                    alpineData.selectedName = data.area_detail || '';
                                    alpineData.search = data.area_detail || '';
                                }
                            }
                        }

                        // Populate photos if exist
                        if (data.photo && Array.isArray(data.photo) && data.photo.length > 0) {
                            const container = document.getElementById(`preview_container_${itemId}`);
                            container.innerHTML = ''; // Clear existing thumbnails
                            data.photo.forEach(photoPath => {
                                if (photoPath && photoPath.trim() !== '') {
                                    const div = document.createElement('div');
                                    div.className="relative group rounded-lg overflow-hidden aspect-square bg-slate-100 border border-slate-200";
                                    div.innerHTML = `
                                    <img src="{{ asset('findings-photo') }}/${photoPath}" class="w-full h-full object-cover">
                                    <button onclick="this.parentElement.remove()" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 w-6 h-6 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                `;
                                    container.appendChild(div);
                                }
                            });
                        }

                        // Populate assign_to_dept dropdown if exists
                        if (data.asign_to_dept) {
                            const hiddenInput = document.getElementById(`asign_to_dept_${itemId}`);
                            if (hiddenInput) {
                                // Set hidden input value with jQuery
                                $(`#asign_to_dept_${itemId}`).val(data.asign_to_dept);

                                // Find the Alpine.js container (parent div with x-data)
                                const alpineContainer = hiddenInput.closest('[x-data]');
                                if (alpineContainer && alpineContainer._x_dataStack) {
                                    const alpineData = alpineContainer._x_dataStack[0];
                                    if (alpineData) {
                                        alpineData.selectedId = data.asign_to_dept;
                                        alpineData.selectedName = data.asign_to_dept_name || data.asign_to_dept;
                                        alpineData.search = data.asign_to_dept_name || data.asign_to_dept;
                                    }
                                }
                            }
                        } else {
                            // Reset if no data
                            const typeHidden = document.getElementById(`type_${itemId}`);
                            if (typeHidden) {
                                $(`#type_${itemId}`).val('');
                                const typeAlpine = typeHidden.closest('[x-data]');
                                if (typeAlpine && typeAlpine._x_dataStack) {
                                    const typeData = typeAlpine._x_dataStack[0];
                                    if (typeData) {
                                        typeData.selectedId = '';
                                        typeData.selectedName = '';
                                        typeData.search = '';
                                    }
                                }
                            }
                            const hiddenInput = document.getElementById(`asign_to_dept_${itemId}`);
                            if (hiddenInput) {
                                $(`#asign_to_dept_${itemId}`).val('');
                                const alpineContainer = hiddenInput.closest('[x-data]');
                                if (alpineContainer && alpineContainer._x_dataStack) {
                                    const alpineData = alpineContainer._x_dataStack[0];
                                    if (alpineData) {
                                        alpineData.selectedId = '';
                                        alpineData.selectedName = '';
                                        alpineData.search = '';
                                    }
                                }
                            }
                        }
                    })
                    .catch(err => console.error('Error fetching evidence data:', err));
            },

            closeModal() {
                this.activeModal = null;
                document.body.style.overflow = '';
            },

            // Removed custom camera logic (startCamera, stopCamera, capturePhoto) relying on getUserMedia
            // Now using native file inputs with capture="environment"

            handleFileUpload(event, itemId) {
                const container = document.getElementById(`preview_container_${itemId}`);
                const currentCount = container.querySelectorAll('img').length;
                const files = event.target.files;

                if (currentCount + files.length > 5) {
                    showToast('Max 5 photos allowed', 'error');
                    event.target.value = ''; // Reset input
                    return;
                }

                if (files.length > 0) {
                    for (let i = 0; i < files.length; i++) {
                        const reader = new FileReader();
                        reader.onload = (e) => this.addThumbnail(itemId, e.target.result);
                        reader.readAsDataURL(files[i]);
                    }
                }
            },

            addThumbnail(itemId, url) {
                const container = document.getElementById(`preview_container_${itemId}`);
                const div = document.createElement('div');
                div.className="relative group rounded-lg overflow-hidden aspect-square bg-slate-100 border border-slate-200";
                div.innerHTML = `
                        <img src="${url}" class="w-full h-full object-cover">
                        <button onclick="this.parentElement.remove()" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 w-6 h-6 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    `;
                container.appendChild(div);
            },

            saveEvidence(itemId) {
                // Collect photos from preview container
                const container = document.getElementById(`preview_container_${itemId}`);
                const images = container.querySelectorAll('img');
                const dataphoto = [];
                const existing_photos = [];

                images.forEach(img => {
                    const src = img.src;
                    if (src.startsWith('data:image')) {
                        dataphoto.push(src);
                    } else if (src.includes('/findings-photo/')) {
                        const parts = src.split('/findings-photo/');
                        if (parts.length > 1) {
                            existing_photos.push(parts[1]);
                        }
                    }
                });

                if (dataphoto.length + existing_photos.length > 5) {
                    showToast('Max 5 photos allowed', 'error');
                    return;
                }

                // Get form values
                const findings = document.getElementById(`findings_${itemId}`).value;
                const scopeId = document.getElementById(`scope_id_${itemId}`).value;
                const activityId = document.getElementById('activity_id').value;
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                const hiddenInput = document.getElementById(`asign_to_dept_${itemId}`);
                let asignToDept = '';
                let asignToDeptName = '';
                if (hiddenInput) {
                    asignToDept = hiddenInput.value;
                    const textInput = hiddenInput.nextElementSibling?.querySelector('input[type="text"]');
                    if (textInput) asignToDeptName = textInput.value;
                }
                const typeValue = document.getElementById(`type_${itemId}`).value;
                const areaDetailInput = document.getElementById(`area_detail_${itemId}`);
                const detailArea = areaDetailInput ? areaDetailInput.value : '';

                if (!detailArea || detailArea.trim() === '') {
                    showToast('Station / Mech. Num is required', 'error');
                    this.isLoading = false;
                    return;
                }

                if (!typeValue) {
                    showToast('Finding Related is required', 'error');
                    this.isLoading = false;
                    return;
                }

                this.isLoading = true;

                fetch("{{ route('genba.post_photo_spv') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify({
                            activity_id: activityId,
                            scope_id: scopeId,
                            check_item_id: itemId,
                            finding_index: this.activeFindingIndex, // Pass active index
                            findings: findings,
                            dataphoto: dataphoto.length > 0 ? dataphoto : null,
                            existing_photos: existing_photos,
                            asign_to_dept: asignToDept,
                            asign_to_dept_name: asignToDeptName,
                            detail_area: detailArea,
                            type: typeValue,
                            _token: token
                        })
                    })
                    .then(res => {
                        if (!res.ok) {
                            return res.json().then(data => Promise.reject(data));
                        }
                        return res.json();
                    })
                    .then(data => {
                        this.isLoading = false;
                        if (data.message) {
                            showToast(data.message, 'success');
                        }

                        // Update local status to show checkmark
                        this.findingStatus[`${itemId}_${this.activeFindingIndex}`] = true;

                        this.closeModal();
                    })
                    .catch(err => {
                        this.isLoading = false;
                        console.error('Error saving evidence:', err);
                        if (err.message) {
                            showToast(err.message, 'error');
                        } else {
                            showToast('Gagal menyimpan evidence', 'error');
                        }
                    });
            },

            submitForm() {
                this.isLoading = true;

                const activityId = document.getElementById('activity_id').value;
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                fetch("{{ route('genba.submit_form_genba') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify({
                            genba_id: activityId,
                            _token: token
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.isLoading = false;
                        if (data.code === 200) {
                            showToast(data.message, 'success');
                            setTimeout(() => {
                                window.location.href = "{{ route('genba_management') }}";
                            }, 1500);
                        } else {
                            showToast(data.message, 'error');
                        }
                    })
                    .catch(err => {
                        this.isLoading = false;
                        console.error('Error submitting form:', err);
                        showToast('Gagal submit audit', 'error');
                    });
            }
        }))
    });

    function backHome() {
        window.history.back();
    }
</script>
@endpush
@endsection