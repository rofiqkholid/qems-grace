@extends('layouts.app')

@section('title', 'Etc Genba Form')

@section('content')
@include('layouts.sidebar')

<x-toast />
<div class="lg:ml-20 min-h-screen flex flex-col bg-slate-50"
    x-data="genbaForm()"
    x-init="initForm()">

    @include('layouts.header')

    <!-- Loading State -->
    <div x-show="isLoading" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/30">
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
                <h1 class="text-2xl font-bold text-slate-800">No Checksheet Genba Form</h1>
            </div>
        </div>

        <input type="hidden" name="activity_id" value="{{ $id_activity }}" id="activity_id">

        @php
            $firstScope = array_key_first($scopes);
            $firstItem = $scopes[$firstScope][0] ?? null;
            $itemId = $firstItem ? $firstItem['check_item_id'] : 64;
            $scopeId = $firstItem ? $firstItem['scope_id'] : 13;
        @endphp

        <!-- Form Card -->
        <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden">
            <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50">
                <h2 class="text-xl font-bold text-slate-800">Input Finding Etc.</h2>
                <p class="text-slate-500 text-sm mt-1">Upload photo finding and fill in the details below.</p>
            </div>

            <div class="p-4 sm:p-8">
                <!-- Two Column Layout -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-8">
                    <!-- Left Column -->
                    <div class="flex flex-col h-full">
                        <!-- Box: Evidence Photos -->
                        <div class="bg-white p-4 sm:p-6 rounded-xl border border-slate-200 h-full flex-1">
                            <h4 class="font-bold text-slate-800 mb-4 flex items-center gap-2 text-base">
                                <i class="fas fa-image text-blue-500"></i> Finding Captured <span class="text-red-500">*</span>
                            </h4>

                            <div class="grid grid-cols-2 gap-3 sm:gap-4">
                                <div class="relative group">
                                    <input type="file" id="cameraInput_{{ $itemId }}" accept="image/*" capture="environment"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                        @change="handleFileUpload($event, {{ $itemId }})">
                                    <div class="flex flex-col items-center justify-center p-4 sm:p-5 border-2 border-dashed border-blue-200 rounded-xl bg-blue-50/50 group-hover:bg-blue-50 group-hover:border-blue-300 transition-all">
                                        <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                                            <i class="fas fa-camera text-lg"></i>
                                        </div>
                                        <span class="text-sm font-semibold text-blue-600">Take Photo</span>
                                    </div>
                                </div>

                                <div class="relative group">
                                    <input type="file" id="uploadInput_{{ $itemId }}" multiple accept="image/*"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                        @change="handleFileUpload($event, {{ $itemId }})">
                                    <div class="flex flex-col items-center justify-center p-4 sm:p-5 border-2 border-dashed border-slate-200 rounded-xl bg-slate-50/50 group-hover:bg-slate-50 transition-all">
                                        <div class="w-12 h-12 bg-slate-100 text-slate-500 rounded-full flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                                            <i class="fas fa-images text-lg"></i>
                                        </div>
                                        <span class="text-sm font-semibold text-slate-600">From Gallery</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 grid grid-cols-4 gap-2" id="preview_container_{{ $itemId }}"></div>
                            <input type="hidden" name="photo_names[]" id="photoname_{{ $itemId }}">
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="flex flex-col h-full">
                        <!-- Box: Detail Temuan -->
                        <div class="bg-white p-4 sm:p-6 rounded-xl border border-slate-200 space-y-3 sm:space-y-4 h-full flex-1">
                            <h4 class="font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2 text-base">
                                <i class="fas fa-list-alt text-blue-500"></i> Detail Temuan
                            </h4>

                            <div class="grid grid-cols-2 gap-4">
                                <!-- Area / Process -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 mb-1">Area / Process</label>
                                    <input type="text" value="{{ $process }}" disabled
                                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-500 cursor-not-allowed outline-none h-[38px]">
                                </div>

                                <!-- Related -->
                                <div class="relative" x-data="{
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
                                    <label class="block text-xs font-semibold text-slate-500 mb-1">Related <span class="text-red-500">*</span></label>
                                    <input type="hidden" id="type_{{ $itemId }}" name="type" required>

                                    <div class="relative">
                                        <input type="text" x-model="search" @click="toggle" @click.outside="closeDropdown()"
                                            placeholder="Select Related..."
                                            class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none text-slate-700 h-[38px]">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-2.5 pointer-events-none text-slate-400">
                                            <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                                        </div>
                                    </div>

                                    <div x-show="open"
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="opacity-0 scale-95"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        class="absolute z-10 w-full mt-1 bg-white border border-slate-200 rounded-lg max-h-60 overflow-y-auto">

                                        <template x-if="items.length === 0">
                                            <div class="px-3 py-2 text-xs text-slate-500 text-center">No options found</div>
                                        </template>

                                        <template x-for="item in items" :key="item.id">
                                            <div x-show="!search || search === selectedName || item.name.toLowerCase().includes(search.toLowerCase())"
                                                @click="select(item)"
                                                class="px-3 py-2 text-xs cursor-pointer transition-colors hover:bg-slate-50"
                                                :class="selectedId === item.id ? 'text-blue-600 bg-blue-50' : 'text-slate-700'">
                                                <span x-text="item.name"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- Assign To -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 mb-1">Assign to <span class="text-red-500">*</span></label>
                                    <x-searchable-select
                                        id="asign_to_dept_{{ $itemId }}"
                                        name="asign_to_dept_{{ $itemId }}"
                                        label="Assign to"
                                        :apiUrl="route('genba.get_section')"
                                        required="true"
                                        hideLabel="true" />
                                </div>

                                <!-- Station -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 mb-1">Station / Mech <span class="text-red-500">*</span></label>
                                    <x-searchable-select 
                                        id="area_detail_{{ $itemId }}" 
                                        name="area_detail_{{ $itemId }}" 
                                        label="Station" 
                                        :apiUrl="route('genba.get_stations')"
                                        required="true"
                                        :hideLabel="true" />
                                </div>
                            </div>

                            <!-- Finding / Comments -->
                            <div class="pt-2">
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Finding / Comments <span class="text-red-500">*</span></label>
                                <textarea id="findings_{{ $itemId }}" name="findings" rows="4" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm outline-none p-3" placeholder="Describe the issue..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="scope_id_{{ $itemId }}" value="{{ $scopeId }}">

                <!-- Submit Button inside Card -->
                <div class="mt-6 pt-6 border-t border-slate-100 flex justify-end">
                    <button @click="submitForm()"
                        class="flex items-center gap-1.5 bg-green-50 hover:bg-green-100 text-green-600 border border-green-400 px-5 py-2 rounded-lg font-semibold text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        <span>Submit Audit</span>
                    </button>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Mobile Sidebar Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/50 z-30 hidden lg:hidden"></div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('genbaForm', () => ({
            isLoading: false,
            activeFindingIndex: 1,

            initForm() {
                const sidebar = document.getElementById('sidebar');
                const sidebarToggle = document.getElementById('sidebar-toggle');
                const sidebarOverlay = document.getElementById('sidebar-overlay');

                if (sidebarToggle) {
                    sidebarToggle.addEventListener('click', () => {
                        sidebar.classList.toggle('-translate-x-full');
                        sidebarOverlay.classList.toggle('hidden');
                    });
                }

                if (sidebarOverlay) {
                    sidebarOverlay.addEventListener('click', () => {
                        sidebar.classList.add('-translate-x-full');
                        sidebarOverlay.classList.add('hidden');
                    });
                }

                // Load existing evidence data
                this.loadEvidence({{ $itemId }}, {{ $scopeId }}, 1);
            },

            loadEvidence(itemId, scopeId, findingIndex = 1) {
                this.activeFindingIndex = findingIndex;
                this.isLoading = true;

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
                            finding_index: findingIndex,
                            _token: token
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.isLoading = false;
                        
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
                            container.innerHTML = '';
                            data.photo.forEach(photoPath => {
                                if (photoPath && photoPath.trim() !== '') {
                                    const div = document.createElement('div');
                                    div.className = "relative group rounded-lg overflow-hidden aspect-square bg-slate-100 border border-slate-200";
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
                                $(`#asign_to_dept_${itemId}`).val(data.asign_to_dept);

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
                        }

                        // Populate type/related if exists
                        if (data.type) {
                            const hiddenInput = document.getElementById(`type_${itemId}`);
                            if (hiddenInput) {
                                $(`#type_${itemId}`).val(data.type);

                                const alpineContainer = hiddenInput.closest('[x-data]');
                                if (alpineContainer && alpineContainer._x_dataStack) {
                                    const alpineData = alpineContainer._x_dataStack[0];
                                    if (alpineData) {
                                        alpineData.selectedId = data.type;
                                        alpineData.selectedName = data.type;
                                        alpineData.search = data.type;
                                    }
                                }
                            }
                        }
                    })
                    .catch(err => {
                        this.isLoading = false;
                        console.error('Error fetching evidence data:', err);
                    });
            },

            handleFileUpload(event, itemId) {
                const container = document.getElementById(`preview_container_${itemId}`);
                const currentCount = container.querySelectorAll('img').length;
                const files = event.target.files;

                if (currentCount + files.length > 5) {
                    showToast('Max 5 photos allowed', 'error');
                    event.target.value = '';
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
                div.className = "relative group rounded-lg overflow-hidden aspect-square bg-slate-100 border border-slate-200";
                div.innerHTML = `
                    <img src="${url}" class="w-full h-full object-cover">
                    <button onclick="this.parentElement.remove()" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 w-6 h-6 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                `;
                container.appendChild(div);
            },

            submitForm() {
                const itemId = {{ $itemId }};
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

                // Validation
                if (dataphoto.length + existing_photos.length === 0) {
                    showToast('Finding photo is required', 'error');
                    return;
                }
                if (!asignToDept) {
                    showToast('Assign to is required', 'error');
                    return;
                }
                if (!detailArea) {
                    showToast('Station / Mech. Num is required', 'error');
                    return;
                }
                if (!typeValue) {
                    showToast('Finding Related is required', 'error');
                    return;
                }
                if (!findings || findings.trim() === '') {
                    showToast('Finding / Comments is required', 'error');
                    return;
                }

                this.isLoading = true;

                // 1. Save finding details
                fetch("{{ route('genba.post_photo_spv') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        activity_id: activityId,
                        scope_id: scopeId,
                        check_item_id: itemId,
                        finding_index: 1,
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
                .then(async res => {
                    const contentType = res.headers.get('content-type');
                    const text = await res.text();
                    let data = null;
                    try {
                        data = JSON.parse(text);
                    } catch (e) {}

                    if (!res.ok) {
                        if (data) return Promise.reject(data);
                        return Promise.reject({ message: text });
                    }
                    if (data) return data;
                    return Promise.reject({ message: 'Invalid JSON response: ' + text });
                })
                .then(data => {
                    // 2. Submit the audit
                    return fetch("{{ route('genba.submit_form_genba') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify({
                            genba_id: activityId,
                            _token: token
                        })
                    });
                })
                .then(async res => {
                    const contentType = res.headers.get('content-type');
                    const text = await res.text();
                    let data = null;
                    try {
                        data = JSON.parse(text);
                    } catch (e) {}

                    if (!res.ok) {
                        if (data) return Promise.reject(data);
                        return Promise.reject({ message: text });
                    }
                    if (data) return data;
                    return Promise.reject({ message: 'Invalid JSON response: ' + text });
                })
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
                    console.error('Error saving or submitting:', err);
                    if (err && err.message) {
                        // If it's a long HTML error from php/laravel, show a cleaner message but log it
                        if (err.message.includes('<!DOCTYPE html>') || err.message.includes('<html')) {
                            showToast('Gagal submit audit (Server Error)', 'error');
                        } else {
                            showToast(err.message, 'error');
                        }
                    } else {
                        showToast('Gagal submit audit', 'error');
                    }
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