@extends('layouts.app')

@section('title', 'Conduct Internal Audit')

@section('content')
@include('layouts.sidebar')
@include('components.toast')

<div class="lg:ml-20 min-h-screen flex flex-col bg-slate-50" x-data="conductAuditApp()">
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    @include('layouts.header')

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
                    <p class="text-slate-500 text-xs sm:text-sm mt-0.5">Evaluate clauses and submit audit findings</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Info Panel -->
            <div class="space-y-6 lg:col-span-1">
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
                    <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b pb-2">Audit Session Information</h2>
                    <div>
                        <div class="text-[11px] font-bold text-slate-400 uppercase">Agenda / Audit Name</div>
                        <div class="text-sm font-semibold text-slate-800 mt-1">{{ $schedule->agenda_name }}</div>
                    </div>
                    <div>
                        <div class="text-[11px] font-bold text-slate-400 uppercase">Auditee Department</div>
                        <div class="text-sm font-semibold text-slate-800 mt-1">{{ $schedule->auditee_dept_name }} ({{ $schedule->auditee_dept }})</div>
                    </div>
                    <div>
                        <div class="text-[11px] font-bold text-slate-400 uppercase">Auditor(s)</div>
                        <div class="text-sm font-semibold text-slate-800 mt-1">{{ $schedule->auditor_niks }}</div>
                    </div>
                    <div>
                        <div class="text-[11px] font-bold text-slate-400 uppercase">Audit Date</div>
                        <div class="text-sm font-semibold text-slate-800 mt-1">{{\Carbon\Carbon::parse($schedule->schedule_date)->format('d M Y')}}</div>
                    </div>
                </div>
            </div>

            <!-- Right Checksheet Items Form -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                        <h2 class="text-lg font-bold text-slate-800">Audit Checksheet Clauses</h2>
                        <p class="text-slate-500 text-sm mt-0.5">Please review each clause and input the judgment, evidence details, and photos.</p>
                    </div>

                    <div class="p-6 space-y-6">
                        @foreach ($items as $item)
                        <div class="p-5 border border-slate-200 rounded-xl bg-slate-50/30 space-y-4">
                            <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                                <div class="flex-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-slate-100 text-slate-800 border border-slate-200 mb-2">
                                        {{ $item->clause_number }}
                                    </span>
                                    <h4 class="font-semibold text-slate-800 text-sm sm:text-base leading-snug">
                                        {{ $item->requirement_desc }}
                                    </h4>
                                </div>
                                
                                <!-- Judgment Selectors -->
                                <div class="flex gap-2 flex-shrink-0">
                                    @foreach (['OK', 'OFI', 'Minor', 'Mayor'] as $jOpt)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="judgment_{{ $item->id }}" value="{{ $jOpt }}" 
                                            @change="setJudgment({{ $item->id }}, '{{ $jOpt }}')" 
                                            :checked="getJudgment({{ $item->id }}) === '{{ $jOpt }}'" class="peer sr-only">
                                        <span class="inline-flex px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-semibold text-slate-500 hover:border-slate-300 peer-checked:bg-blue-50 peer-checked:text-blue-700 peer-checked:border-blue-200 transition-all">
                                            {{ $jOpt }}
                                        </span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Evidence & Photo attachment -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Evidence / Findings Detail</label>
                                    <textarea rows="2" placeholder="Describe findings or observations..." 
                                        @input="setEvidence({{ $item->id }}, $event.target.value)" 
                                        class="w-full text-sm border border-slate-200 rounded-lg p-2.5 focus:ring-1 focus:ring-blue-500 outline-none"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Attachment (Photo)</label>
                                    <input type="file" @change="handlePhotoUpload({{ $item->id }}, $event)" 
                                        class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                                </div>
                            </div>
                        </div>
                        @endforeach

                        <!-- Submit Buttons -->
                        <div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
                            <a href="{{ route('internal_audit') }}" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 rounded-lg text-sm font-semibold transition-colors">
                                Cancel
                            </a>
                            <button type="button" @click="submitAudit()" class="px-5 py-2.5 bg-blue-600 text-white hover:bg-blue-700 rounded-lg text-sm font-semibold transition-colors shadow-sm animate-button">
                                Submit Audit Results
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

@push('scripts')
<script>
    function conductAuditApp() {
        return {
            scheduleId: {{ $schedule->id }},
            auditeeDept: '{{ $schedule->auditee_dept }}',
            auditResults: {},

            init() {
                // Initialize default judgments for checksheet items
                @foreach ($items as $item)
                this.auditResults[{{ $item->id }}] = {
                    judgment: 'OK',
                    evidence: '',
                    photo: null
                };
                @endforeach
            },

            setJudgment(itemId, val) {
                if (this.auditResults[itemId]) {
                    this.auditResults[itemId].judgment = val;
                }
            },

            getJudgment(itemId) {
                return this.auditResults[itemId] ? this.auditResults[itemId].judgment : 'OK';
            },

            setEvidence(itemId, val) {
                if (this.auditResults[itemId]) {
                    this.auditResults[itemId].evidence = val;
                }
            },

            handlePhotoUpload(itemId, event) {
                var self = this;
                var file = event.target.files[0];
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        if (self.auditResults[itemId]) {
                            self.auditResults[itemId].photo = e.target.result;
                        }
                    };
                    reader.readAsDataURL(file);
                }
            },

            submitAudit() {
                var self = this;
                $('body').addClass('data-loading');
                $('#page-loader').removeClass('hidden');
                
                $.ajax({
                    url: "{{ route('internal_audit.submit') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        schedule_id: self.scheduleId,
                        audit_date: new Date().toISOString().split('T')[0],
                        auditor_names: '{{ Auth::user()->full_name }}',
                        auditee_dept: self.auditeeDept,
                        results: self.auditResults
                    },
                    success: function(response) {
                        $('body').removeClass('data-loading');
                        $('#page-loader').addClass('hidden');
                        
                        if (response.success) {
                            window.location.href = "{{ route('internal_audit') }}?tab=schedules&success=1&msg=" + encodeURIComponent(response.message);
                        } else {
                            showToast(response.message, 'error');
                        }
                    },
                    error: function() {
                        $('body').removeClass('data-loading');
                        $('#page-loader').addClass('hidden');
                        showToast('Failed to submit audit results.', 'error');
                    }
                });
            }
        };
    }
</script>
@endpush
@endsection
