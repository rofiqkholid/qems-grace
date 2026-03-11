@extends('layouts.app')

@section('title', 'Genba Finding Preview - QMS')

@section('content')
@php
$isClosed = $genba->status === 'Close';
@endphp
@include('layouts.sidebar')
@include('components.toast')

<div class="lg:ml-20 min-h-screen flex flex-col bg-slate-50">
    @include('layouts.header')

    <main class="flex-1 p-3 sm:p-6">
        <div class="mb-4 sm:mb-6 flex items-center gap-3 sm:gap-4">
            <a href="{{ route('genba_mng_management') }}"
                class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-blue-50 border border-blue-200 text-blue-600 hover:bg-blue-50 hover:text-blue-900 transition-all duration-200">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-700">Genba Finding Preview</h1>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-slate-200 p-4 sm:p-8">
            <div class="grid grid-cols-2 gap-4 sm:gap-6 mb-6 sm:mb-8">
                <div class="flex flex-col gap-2">
                    <label class="text-slate-700 font-medium text-sm">Date</label>
                    <div class="bg-slate-100 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-slate-800 text-sm">
                        {{ \Carbon\Carbon::parse($genba->Date)->format('d/m/Y') }}
                    </div>
                </div>

                <!-- Due Date -->
                <div class="flex flex-col gap-2">
                    <label class="text-slate-700 font-medium text-sm">Due Date</label>
                    @php
                    $isOverdue = !$isClosed && $genba->due_date && \Carbon\Carbon::parse($genba->due_date)->startOfDay() < \Carbon\Carbon::now()->startOfDay();
                    @endphp
                    <div class="rounded-lg border border-slate-200 p-4 sm:p-8 text-sm flex items-center justify-between transition-all duration-300 {{ $isOverdue ? 'bg-red-50 border-red-200 text-red-700 shadow-sm' : 'bg-slate-100 border-transparent text-slate-800' }}">
                        <div class="flex items-center gap-2">
                            @if($isOverdue)
                            <i class="fa-solid fa-clock-rotate-left text-red-500 animate-pulse"></i>
                            @endif
                            <span>{{ $genba->due_date ? \Carbon\Carbon::parse($genba->due_date)->format('d/m/Y') : '-' }}</span>
                        </div>
                        @if($isOverdue)
                        <span class="text-[13px] text-red-500 ml-2 opacity-70">Overdue</span>
                        @endif
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-slate-700 font-medium text-sm">Document Number</label>
                    <div class="bg-slate-100 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-slate-800 font-mono text-sm">
                        {{ $genba->DocNum }}
                    </div>
                </div>

                <!-- Type -->
                <div class="flex flex-col gap-2">
                    <label class="text-slate-700 font-medium text-sm">Related</label>
                    <div class="bg-slate-100 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-slate-800 text-sm">
                        {{ $genba->type ?? '-' }}
                    </div>
                </div>

                <!-- Auditor -->
                <div class="flex flex-col gap-2">
                    <label class="text-slate-700 font-medium text-sm">Auditor</label>
                    <div class="bg-slate-100 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-slate-800 text-sm">
                        {{ $genba->Auditor ?? '-' }}
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-slate-700 font-medium text-sm">Station / Mech. Num</label>
                    <div class="bg-slate-100 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-slate-800 text-sm">
                        {{ $genba->Area_Checked }}
                    </div>
                </div>

                <!-- Department -->
                <div class="flex flex-col gap-2">
                    <label class="text-slate-700 font-medium text-sm">Department</label>

                    <!-- Department Display (Match Auditor Style) -->
                    <div id="dept-display-container" class="bg-slate-100 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-slate-800 text-sm flex items-center justify-between h-[46px]">
                        <span id="dept-display-text" class="truncate">
                            {{ $genba->asign_to_dept ?? '-' }}
                        </span>

                        @if(!$isClosed && $canEditDept)
                        <button type="button" onclick="toggleDeptEdit()" class="w-6 h-6 flex items-center justify-center rounded-full text-slate-400 hover:text-blue-600 hover:bg-white transition-all ml-2" title="Edit Department">
                            <i class="fa-solid fa-pencil text-xs"></i>
                        </button>
                        @endif
                    </div>

                    <!-- Department Edit (Hidden) -->
                    <div id="dept-edit-container" class="hidden flex items-center gap-2 w-full h-[46px]">
                        <div class="flex-1 h-full">
                            @php
                            $deptOptions = collect($departments)->map(function($d) {
                            return ['id' => $d->id, 'name' => $d->id];
                            })->values()->all();
                            @endphp
                            <x-searchable-select
                                name="dept"
                                id="dept-select"
                                label="Department"
                                :initialOptions="$deptOptions"
                                :hideLabel="true"
                                valueField="id"
                                updateEvent="set-dept-value" />
                        </div>
                        <div class="flex items-center gap-1 shrink-0">
                            <button type="button" onclick="saveDept()" class="mb-1 w-10 h-[42px] flex items-center justify-center rounded-lg bg-green-50 text-green-600 hover:bg-green-100 border border-green-200 transition-all" title="Save">
                                <i class="fa-solid fa-check text-xs"></i>
                            </button>
                            <button type="button" onclick="toggleDeptEdit()" class="mb-1 w-10 h-[42px] flex items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-100 border border-red-200 transition-all" title="Cancel">
                                <i class="fa-solid fa-times text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Close Date -->
                <div class="flex flex-col gap-2">
                    <label class="text-slate-700 font-medium text-sm">Close Date</label>
                    <div class="bg-slate-100 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-slate-800 text-sm h-[46px] flex items-center">
                        {{ $genba->complete_date ? \Carbon\Carbon::parse($genba->complete_date)->format('d/m/Y') : '-' }}
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <div class="border-t border-slate-200 my-8"></div>

            <!-- Findings -->
            <div class="flex flex-col sm:grid sm:grid-cols-[180px_1fr] gap-2 sm:gap-4 items-start mb-6">
                <label class="text-slate-700 font-medium text-sm sm:pt-3">Findings</label>
                <div class="w-full bg-slate-100 rounded-lg px-4 py-3 text-slate-800 min-h-[100px]">
                    <p class="whitespace-pre-line">{{ $genba->findings ?? 'No findings recorded' }}</p>
                </div>
            </div>

            <!-- Preventive Action -->


            <!-- Finding Image -->
            @if($genba->Path)
            <div class="flex flex-col sm:grid sm:grid-cols-[180px_1fr] gap-2 sm:gap-4 items-start mb-6">
                <label class="text-slate-700 font-medium text-sm sm:pt-3">Finding Image</label>
                <div class="w-full bg-slate-100 rounded-lg p-3">
                    @php
                    $paths = explode(',', $genba->Path);
                    @endphp
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3" id="findingImageContainer">
                        @foreach($paths as $path)
                        <div class="relative group aspect-square">
                            <img src="{{ asset('findings-photo/' . $path) }}"
                                alt="Finding Image"
                                class="w-full h-full rounded-lg object-cover cursor-pointer hover:opacity-80 transition-opacity"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="hidden flex-col items-center justify-center text-slate-400 gap-2 w-full h-full bg-slate-200 rounded-lg">
                                <i class="fa-regular fa-image text-2xl"></i>
                                <span class="text-xs">No image</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @else
            <div class="flex flex-col sm:grid sm:grid-cols-[180px_1fr] gap-2 sm:gap-4 items-start mb-6">
                <label class="text-slate-700 font-medium text-sm sm:pt-3">Finding Image</label>
                <div class="w-full bg-slate-100 rounded-lg p-3 flex items-start">
                    <div class="flex flex-col items-center justify-center text-slate-400 gap-2 w-24 h-24">
                        <i class="fa-regular fa-image text-2xl"></i>
                        <span class="text-xs">No image</span>
                    </div>
                </div>
            </div>
            @endif

            <!-- Divider -->
            <div class="border-t border-slate-200 my-8"></div>


            <!-- Hidden input for trc_unix_id -->
            <input type="hidden" id="trc_unix_id" value="{{ $genba->trc_unix_id ?? '' }}">

            <!-- Execution Comment -->
            <div class="flex flex-col sm:grid sm:grid-cols-[180px_1fr] gap-2 sm:gap-4 items-start mb-6">
                <label class="text-slate-700 font-medium text-sm sm:pt-3">Corrective Action <span class="text-red-500">*</span></label>
                <div class="w-full">
                    <textarea
                        id="actionPlanText"
                        class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none resize-none text-sm disabled:bg-slate-100 disabled:text-slate-500"
                        rows="5"
                        @if($isClosed) disabled @endif
                        placeholder="Enter corrective action...">{{ $genba->execution_comment }}</textarea>
                </div>
            </div>

            <!-- Preventive Action -->
            <div class="flex flex-col sm:grid sm:grid-cols-[180px_1fr] gap-2 sm:gap-4 items-start mb-6">
                <label class="text-slate-700 font-medium text-sm sm:pt-3">Preventive Action <span class="text-red-500">*</span></label>
                <div class="w-full">
                    <textarea
                        id="preventiveActionText"
                        class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none resize-none text-sm disabled:bg-slate-100 disabled:text-slate-500"
                        rows="3"
                        @if($isClosed) disabled @endif
                        placeholder="Enter preventive action...">{{ $genba->preventive_action ?? '' }}</textarea>
                </div>
            </div>

            <!-- Execution Evidence Upload -->
            <div class="flex flex-col sm:grid sm:grid-cols-[180px_1fr] gap-2 sm:gap-4 items-start mb-6">
                <label class="text-slate-700 font-medium text-sm sm:pt-3">Evidence</label>

                <div class="w-full space-y-3">

                    @if(!$isClosed)
                    <div class="flex gap-2 flex-wrap">
                        <label class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 cursor-pointer transition-colors text-sm">
                            <i class="fa-solid fa-upload mr-2"></i>
                            Choose Files
                            <input type="file" class="hidden" accept="image/*" id="evidenceFile" onchange="previewFromFile(event)">
                        </label>
                        <button type="button" id="btnOpenCamera" onclick="openCameraStream()" class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-600 border border-blue-200 rounded-lg hover:bg-blue-200 hover:border-blue-400 transition-colors text-sm">
                            <i class="fa-solid fa-camera mr-2"></i>
                            Open Camera
                        </button>
                        <button type="button" id="btnCloseCamera" onclick="closeCameraStream()" class="hidden inline-flex items-center px-4 py-2 bg-red-50 text-red-600 border border-red-200 rounded-lg hover:bg-red-200 hover:border-red-400 transition-colors text-sm">
                            <i class="fa-solid fa-times mr-2"></i>
                            Close Camera
                        </button>
                        <button type="button" id="btnCapture" onclick="capturePhotoFromStream()" class="hidden inline-flex items-center px-4 py-2 bg-blue-50 text-blue-600 border border-blue-200 rounded-lg hover:bg-blue-200 hover:border-blue-400 transition-colors text-sm">
                            <i class="fa-solid fa-camera mr-2"></i>
                            Capture
                        </button>
                    </div>
                    <p class="text-xs text-red-600 mt-2">
                        * Harap mengirimkan foto perbaikan yang mewakili temuan secara detail dan jelas! <br>
                        <span class="italic">* Please provide improvement photos that represent the findings in detail and clearly!</span>
                    </p>
                    @endif

                    <!-- Camera Preview Container -->
                    <div id="cameraContainer" class="hidden bg-slate-100 rounded-lg p-4">
                        <video id="cameraPreview" autoplay playsinline class="w-full h-auto rounded-lg max-h-96 object-contain bg-black"></video>
                        <canvas id="canvas" class="hidden"></canvas>
                    </div>

                    <!-- Image Preview Container -->
                    <!-- Image Preview Container -->
                    <div id="evidencePreviewContainer" class="grid grid-cols-2 sm:grid-cols-3 gap-3 @if(!$genba->execution_path) hidden @endif">
                        @if($genba->execution_path)
                        @foreach(explode(',', $genba->execution_path) as $path)
                        <div class="relative group aspect-square">
                            <img src="{{ asset('evidence-photo/' . trim($path)) }}" class="w-full h-full object-cover rounded-lg border border-slate-200">
                            @if(!$isClosed)
                            <button type="button" onclick="removeImage(this, '{{ trim($path) }}')" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-90 hover:opacity-100 transition-opacity w-6 h-6 flex items-center justify-center">
                                <i class="fa-solid fa-times text-xs"></i>
                            </button>
                            @endif
                        </div>
                        @endforeach
                        @endif
                    </div>

                    <div id="placeholderText" class="@if($genba->execution_path) hidden @endif flex flex-col items-center justify-center text-slate-400 gap-2 min-h-[150px] bg-slate-50 rounded-lg border-2 border-dashed border-slate-200">
                        <i class="fa-regular fa-image text-4xl"></i>
                        <span class="text-xs">No evidence images</span>
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            @if(!$isClosed)
            <div class="flex flex-col sm:grid sm:grid-cols-[180px_1fr] gap-2 sm:gap-4 items-start mb-6">
                <div></div>
                <div class="w-full sm:w-auto">
                    <button type="button" onclick="saveData()"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-green-50 text-green-600 border border-green-200 rounded-lg hover:bg-green-200 hover:border-green-400 font-medium transition-colors">
                        <i class="fa-solid fa-save text-sm"></i>
                        Save
                    </button>
                </div>
            </div>
            @else
            <div class="h-6"></div>
            @endif

        </div>
    </main>

    @include('layouts.footer')
</div>

<!-- Mobile Sidebar Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/50 z-30 hidden lg:hidden"></div>



@endsection

@push('scripts')
<script>
    const initialPaths = "{{ $genba->execution_path ?? '' }}";
    let stream = null;
    let newImages = [];
    let existingImages = []; // Array of existing paths

    document.addEventListener('DOMContentLoaded', function() {
        // Viewer.js initialization (kept as is)
        const container = document.getElementById('findingImageContainer');
        if (container) {
            new Viewer(container, {
                inline: false,
                navbar: false,
                title: false,
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
                },
                zoomRatio: 0.1,
                minZoomRatio: 0.1,
                maxZoomRatio: 10,
                movable: true,
                rotatable: true,
                scalable: true,
                transition: true,
                fullscreen: true,
                keyboard: true,
            });
        }

        // Initialize existingImages array
        if (initialPaths) {
            existingImages = initialPaths.split(',').map(p => p.trim());
        }

        // Set initial department if exists
        const currentDept = "{{ $genba->asign_to_dept }}";
        const currentDeptName = "{{ $genba->asign_to_dept_name }}";
        const allDepts = JSON.parse('@json($departments, JSON_HEX_APOS)');

        if (currentDept) {
            // Find name in master list to ensure consistency
            // For this specific case, we want to show the ID (Key1) as the name
            const displayName = currentDept;

            // Delay slightly to ensure Alpine is ready
            setTimeout(() => {
                window.dispatchEvent(new CustomEvent('set-dept-value', {
                    detail: {
                        id: currentDept,
                        name: displayName
                    }
                }));
            }, 100);
        }
    });

    // Mobile Sidebar Toggle
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


    // Preview from file upload
    function previewFromFile(event) {
        const files = event.target.files;
        if (!files.length) return;

        const totalImages = existingImages.length + newImages.length + files.length;
        if (totalImages > 5) {
            showToast('Maksimal 5 foto bukti allowed', 'error');
            event.target.value = ''; // Reset input
            return;
        }

        Array.from(files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const base64Info = e.target.result;
                newImages.push(base64Info);
                renderImage(base64Info, 'new');
            };
            reader.readAsDataURL(file);
        });

        event.target.value = ''; // Allow re-selecting same file
    }

    // Toggle Department Edit
    function toggleDeptEdit() {
        const displayContainer = document.getElementById('dept-display-container');
        const editContainer = document.getElementById('dept-edit-container');

        if (displayContainer.classList.contains('hidden')) {
            displayContainer.classList.remove('hidden');
            editContainer.classList.add('hidden');
        } else {
            displayContainer.classList.add('hidden');
            editContainer.classList.remove('hidden');
        }
    }

    // Save Department
    function saveDept() {
        const trcUnixId = document.getElementById('trc_unix_id').value;
        // Searchable select component updates the hidden input with the id provided
        const selectedDept = document.getElementById('dept-select').value;

        if (!selectedDept) {
            showToast('Please select a department', 'warning');
            return;
        }

        // Show loading state appropriately if desired, for now just simple fetch

        fetch("{{ route('genba.update_department') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    trc_unix_id: trcUnixId,
                    dept: selectedDept
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.code === 200) {
                    showToast(data.message, 'success');

                    // Update display text
                    const displayText = document.getElementById('dept-display-text');
                    displayText.textContent = selectedDept;
                    displayText.classList.remove('hidden'); // Ensure visible if it was empty before

                    // Toggle back
                    toggleDeptEdit();
                } else {
                    showToast(data.message || 'Failed to update department', 'error');
                }
            })
            .catch(err => {
                console.error('Error updating department:', err);
                showToast('An error occurred while updating', 'error');
            });
    }

    // Open camera stream (kept mostly same, just check limit)
    async function openCameraStream() {
        if (existingImages.length + newImages.length >= 5) {
            showToast('Maksimal 5 foto. Hapus beberapa untuk mengambil foto baru.', 'warning');
            return;
        }
        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: 'environment'
                },
                audio: false
            });
            const video = document.getElementById('cameraPreview');
            video.srcObject = stream;
            document.getElementById('cameraContainer').classList.remove('hidden');
            document.getElementById('evidencePreviewContainer').classList.add('hidden'); // Hide grid while camera open
            document.getElementById('placeholderText').classList.add('hidden');
            document.getElementById('btnOpenCamera').classList.add('hidden');
            document.getElementById('btnCloseCamera').classList.remove('hidden');
            document.getElementById('btnCapture').classList.remove('hidden');
        } catch (err) {
            console.error('Error accessing camera:', err);
            alert('Tidak dapat mengakses kamera.');
        }
    }

    function closeCameraStream() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
        document.getElementById('cameraContainer').classList.add('hidden');

        // Show grid if there are images, else show placeholder
        if (existingImages.length > 0 || newImages.length > 0) {
            document.getElementById('evidencePreviewContainer').classList.remove('hidden');
            document.getElementById('placeholderText').classList.add('hidden');
        } else {
            document.getElementById('evidencePreviewContainer').classList.add('hidden');
            document.getElementById('placeholderText').classList.remove('hidden');
        }

        document.getElementById('btnOpenCamera').classList.remove('hidden');
        document.getElementById('btnCloseCamera').classList.add('hidden');
        document.getElementById('btnCapture').classList.add('hidden');
    }

    function capturePhotoFromStream() {
        if (existingImages.length + newImages.length >= 5) {
            showToast('Maksimal 5 foto reached', 'error');
            return;
        }

        const video = document.getElementById('cameraPreview');
        const canvas = document.getElementById('canvas');
        const context = canvas.getContext('2d');

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        const imageDataUrl = canvas.toDataURL('image/jpeg');
        newImages.push(imageDataUrl);
        renderImage(imageDataUrl, 'new');

        closeCameraStream();
    }

    function renderImage(src, type, existingPath = null) {
        const container = document.getElementById('evidencePreviewContainer');
        const placeholder = document.getElementById('placeholderText');

        container.classList.remove('hidden');
        placeholder.classList.add('hidden');

        const div = document.createElement('div');
        div.className = 'relative group aspect-square';

        // If it's a new base64 image, src is the data. If existing, src might be full URL but we need path for remove logic

        div.innerHTML = `
            <img src="${src}" class="w-full h-full object-cover rounded-lg border border-slate-200">
            <button type="button" onclick="removeImage(this, '${type === 'existing' ? existingPath : ''}')" 
                class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-90 hover:opacity-100 transition-opacity w-6 h-6 flex items-center justify-center">
                <i class="fa-solid fa-times text-xs"></i>
            </button>
        `;

        container.appendChild(div);

    }

    function removeImage(btn, pathOrIndex) {
        const parentDiv = btn.closest('.group');
        const container = document.getElementById('evidencePreviewContainer');
        const placeholder = document.getElementById('placeholderText');

        const img = parentDiv.querySelector('img');
        const src = img.src;

        if (src.startsWith('data:image')) {
            // It's a new image
            const idx = newImages.indexOf(src);
            if (idx > -1) newImages.splice(idx, 1);
        } else {
            // It's an existing image (URL)
            // pathOrIndex should be the path passed from blade
            if (pathOrIndex) {
                const idx = existingImages.indexOf(pathOrIndex);
                if (idx > -1) existingImages.splice(idx, 1);
            }
        }

        parentDiv.remove();

        if (existingImages.length === 0 && newImages.length === 0) {
            container.classList.add('hidden');
            placeholder.classList.remove('hidden');
        }
    }

    // Save data function
    function saveData() {
        const trcUnixId = document.getElementById('trc_unix_id').value;
        const actionPlan = document.getElementById('actionPlanText').value;
        const preventiveAction = document.getElementById('preventiveActionText').value;

        // Validation
        if (!actionPlan.trim()) {
            showToast('Corrective Action is required', 'error');
            return;
        }

        if (!preventiveAction.trim()) {
            showToast('Preventive Action is required', 'error');
            return;
        }

        // Show loading
        const saveBtn = document.querySelector('button[onclick="saveData()"]');
        const originalBtnHtml = saveBtn.innerHTML;
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-sm"></i> Saving...';

        fetch("{{ route('genba.save_action_plan') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    trc_unix_id: trcUnixId,
                    action_plan: actionPlan,
                    preventive_action: preventiveAction,
                    dataphoto: newImages, // Array of new base64 strings
                    existing_photos: existingImages // Array of preserved paths
                })
            })
            .then(res => res.json())
            .then(data => {
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalBtnHtml;

                if (data.code === 200) {
                    showToast(data.message, 'success');
                    // Optional: reload page to refresh state or redirect
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showToast(data.message || 'Failed to save data', 'error');
                }
            })
            .catch(err => {
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalBtnHtml;
                console.error('Error saving data:', err);
                showToast('An error occurred while saving', 'error');
            });
    }
</script>
@endpush