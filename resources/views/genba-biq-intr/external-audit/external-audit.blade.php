@extends('layouts.app')

@section('title', 'External Audit')

@section('content')
@include('layouts.sidebar')
@include('components.toast')

<div class="lg:ml-20 min-h-screen flex flex-col bg-slate-50">
    @include('layouts.header')

    <main class="flex-1 p-3 sm:p-6">
        <!-- Page Title & Header Actions -->
        <div class="mb-4 sm:mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
            <div class="flex items-center gap-3 sm:gap-4">
                <a href="{{ url('/') }}"
                    class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-blue-50 border border-blue-200 text-blue-600 hover:bg-blue-100 hover:text-blue-700 transition-all duration-200">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-700">External Audit</h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Corrective Action Report Preview & Submission for External Audit</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition-all text-sm cursor-pointer opacity-90 hover:opacity-100">
                    <i class="fa-solid fa-file-excel text-base"></i>
                    Export Excel
                </button>
            </div>
        </div>

        <form id="externalAuditForm" onsubmit="event.preventDefault(); alert('Data berhasil disubmit (Mode Standalone/Preview).');">
            @csrf
            <!-- Combined Finding Details Card -->
            <div class="bg-white rounded-lg border border-slate-200 p-4 sm:p-8 space-y-8">

                <!-- Section 1: Finding General Information -->
                <div>
                    <h2 class="text-lg font-bold text-slate-800 mb-5 pb-2 border-b border-slate-100">
                        Finding Information
                    </h2>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-6">
                        <!-- CAR Number -->
                        <div class="flex flex-col gap-1 sm:gap-1.5">
                            <label class="text-slate-600 font-semibold text-[11px] sm:text-xs tracking-wider">CAR / Request Number</label>
                            <input type="text" name="req_number" class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-xs sm:text-sm outline-none text-slate-700 bg-white" placeholder="EXT-CAR-2026-001">
                        </div>

                        <!-- Finding Category -->
                        <div class="flex flex-col gap-1 sm:gap-1.5">
                            <label class="text-slate-600 font-semibold text-[11px] sm:text-xs tracking-wider">Finding Category</label>
                            <input type="text" name="finding_category" class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-xs sm:text-sm outline-none text-slate-700 bg-white" placeholder="Major">
                        </div>

                        <!-- Department -->
                        <div class="flex flex-col gap-1 sm:gap-1.5">
                            <label class="text-slate-600 font-semibold text-[11px] sm:text-xs tracking-wider">Auditee Department</label>
                            <input type="text" name="department" class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-xs sm:text-sm outline-none text-slate-700 bg-white" placeholder="QA / Quality Assurance">
                        </div>

                        <!-- Date Created -->
                        <div class="flex flex-col gap-1 sm:gap-1.5">
                            <label class="text-slate-600 font-semibold text-[11px] sm:text-xs tracking-wider">Report Date</label>
                            <input type="date" name="report_date" class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-xs sm:text-sm outline-none text-slate-700 bg-white">
                        </div>

                        <!-- Auditor -->
                        <div class="flex flex-col gap-1 sm:gap-1.5">
                            <label class="text-slate-600 font-semibold text-[11px] sm:text-xs tracking-wider">Auditor</label>
                            <input type="text" name="auditor" class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-xs sm:text-sm outline-none text-slate-700 bg-white" placeholder="Auditor Eksternal (SGS / TUV)">
                        </div>

                        <!-- Due Date -->
                        <div class="flex flex-col gap-1 sm:gap-1.5">
                            <label class="text-slate-600 font-semibold text-[11px] sm:text-xs tracking-wider">Due Date</label>
                            <input type="date" name="due_date" class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-xs sm:text-sm outline-none text-slate-700 bg-white">
                        </div>

                        <!-- Surveillance -->
                        <div class="flex flex-col gap-1 sm:gap-1.5">
                            <label class="text-slate-600 font-semibold text-[11px] sm:text-xs tracking-wider">Surveillance</label>
                            <input type="text" name="surveillance" class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-xs sm:text-sm outline-none text-slate-700 bg-white" placeholder="Surveillance 1">
                        </div>

                        <!-- External -->
                        <div class="flex flex-col gap-1 sm:gap-1.5">
                            <label class="text-slate-600 font-semibold text-[11px] sm:text-xs tracking-wider">External</label>
                            <input type="text" name="external" class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-xs sm:text-sm outline-none text-slate-700 bg-white" placeholder="PT. Summit Adyawinsa Indonesia">
                        </div>

                        <!-- Internal Audit / Audit Source -->
                        <div class="flex flex-col gap-1 sm:gap-1.5">
                            <label class="text-slate-600 font-semibold text-[11px] sm:text-xs tracking-wider">Audit Source</label>
                            <input type="text" name="audit_source" class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-xs sm:text-sm outline-none text-slate-700 bg-white" placeholder="External Audit">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Requirement & Clause Standards -->
                <div class="border-t border-slate-100 pt-6">
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-6">
                        <!-- Requirement No -->
                        <div class="flex flex-col gap-1 sm:gap-1.5">
                            <label class="text-slate-600 font-semibold text-[11px] sm:text-xs tracking-wider">Requirement No.</label>
                            <input type="text" name="requirement_no" class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-xs sm:text-sm outline-none text-slate-700 bg-white" placeholder="Clause 8.5.1">
                        </div>
                        <!-- Clause Title -->
                        <div class="flex flex-col gap-1 sm:gap-1.5">
                            <label class="text-slate-600 font-semibold text-[11px] sm:text-xs tracking-wider">Clause Title</label>
                            <input type="text" name="clause_title" class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-xs sm:text-sm outline-none text-slate-700 bg-white" placeholder="Control of production and service provision">
                        </div>
                        <!-- Auditee -->
                        <div class="flex flex-col gap-1 sm:gap-1.5">
                            <label class="text-slate-600 font-semibold text-[11px] sm:text-xs tracking-wider">Auditee</label>
                            <input type="text" name="auditee" class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-xs sm:text-sm outline-none text-slate-700 bg-white" placeholder="Production / Quality Assurance Team">
                        </div>
                        <!-- Check Item -->
                        <div class="flex flex-col gap-1 sm:gap-1.5 col-span-2 sm:col-span-3">
                            <label class="text-slate-600 font-semibold text-[11px] sm:text-xs tracking-wider">Check Item</label>
                            <textarea name="check_item" rows="2" class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-xs sm:text-sm outline-none text-slate-700 bg-white resize-y" placeholder="Verifikasi kelengkapan dokumen kerja standar operasional dan kepatuhan monitoring di area produksi"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Clause Description & Finding Evidence Details -->
                <div class="border-t border-slate-100 pt-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <!-- Clause Description -->
                        <div class="flex flex-col gap-1 sm:gap-1.5">
                            <label class="text-slate-600 font-semibold text-[11px] sm:text-xs tracking-wider">Clause Description / Klausul</label>
                            <textarea name="clause_text" rows="4" class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-xs sm:text-sm outline-none text-slate-700 bg-white resize-y leading-relaxed" placeholder="The organization shall implement production and service provision under controlled conditions including documented information that defines characteristics of products and activities to be performed."></textarea>
                        </div>
                        <!-- Finding Evidence Details -->
                        <div class="flex flex-col gap-1 sm:gap-1.5">
                            <label class="text-slate-600 font-semibold text-[11px] sm:text-xs tracking-wider">Finding Evidence Details</label>
                            <textarea name="finding" rows="4" class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-xs sm:text-sm outline-none text-slate-700 bg-white resize-y leading-relaxed" placeholder="Ditemukan beberapa lembar instruksi kerja (IK) belum di-update ke revisi terbaru pada stasiun kerja Line 2."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Plan Form Card -->
            <div class="bg-white rounded-lg border border-slate-200 p-4 sm:p-8 space-y-8 mt-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-800 mb-5 pb-2 border-b border-slate-100">
                        Action Plan & Analysis
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column: Why 1 to Why 5 -->
                        <div class="flex flex-col gap-4">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-slate-700 font-semibold text-xs tracking-wider">Why 1 <span class="text-red-500">*</span></label>
                                <textarea name="why_one" rows="1" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none text-slate-700 resize-none overflow-hidden" placeholder="Enter Why 1..."></textarea>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-slate-700 font-semibold text-xs tracking-wider">Why 2 <span class="text-red-500">*</span></label>
                                <textarea name="why_two" rows="1" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none text-slate-700 resize-none overflow-hidden" placeholder="Enter Why 2..."></textarea>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-slate-700 font-semibold text-xs tracking-wider">Why 3 <span class="text-red-500">*</span></label>
                                <textarea name="why_three" rows="1" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none text-slate-700 resize-none overflow-hidden" placeholder="Enter Why 3..."></textarea>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-slate-700 font-semibold text-xs tracking-wider">Why 4</label>
                                <textarea name="why_four" rows="1" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none text-slate-700 resize-none overflow-hidden" placeholder="Enter Why 4 (Optional)..."></textarea>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-slate-700 font-semibold text-xs tracking-wider">Why 5</label>
                                <textarea name="why_five" rows="1" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none text-slate-700 resize-none overflow-hidden" placeholder="Enter Why 5 (Optional)..."></textarea>
                            </div>
                        </div>

                        <!-- Right Column: Root Cause & Analyzed By -->
                        <div class="flex flex-col gap-4 justify-start">
                            <!-- Root Cause -->
                            <div class="flex flex-col gap-1.5">
                                <div class="flex items-center justify-between">
                                    <label class="text-slate-700 font-semibold text-xs tracking-wider">Root Cause <span class="text-red-500">*</span></label>
                                </div>
                                <div class="flex items-start gap-2 w-full">
                                    <div class="flex flex-col gap-1.5 w-full">
                                        <textarea name="root_cause" rows="5" style="min-height: 120px;" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none text-slate-700 resize-none overflow-hidden" placeholder="Enter Root Cause..."></textarea>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between mt-1">
                                    <div id="root_cause_preview" class="flex flex-wrap gap-2 items-center"></div>
                                    <div class="shrink-0">
                                        <input type="file" id="root_cause_file" name="root_cause_photo[]" multiple accept="image/*,application/pdf" class="hidden">
                                        <button type="button" onclick="document.getElementById('root_cause_file').click()" class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-dashed border-blue-300 bg-blue-50/50 hover:bg-blue-50 text-blue-600 rounded-lg text-xs font-semibold transition-all" title="Take / Upload Photo or PDF">
                                            <i class="fas fa-camera text-xs"></i> Upload File
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Analyzed by -->
                            <div class="flex flex-col gap-1.5">
                                <label class="text-slate-700 font-semibold text-xs tracking-wider">Analyzed by Auditee Superior <span class="text-red-500">*</span></label>
                                <input type="text" name="analyzed_by" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none text-slate-700" placeholder="Auditee Superior">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Corrective & Preventive Action Row-by-Row Grid Alignment -->
                <div class="border-t border-slate-100 pt-6">
                    <!-- Column Headers -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 border-b border-slate-100 pb-2 mb-4">
                        <!-- A. Corrective Action Header -->
                        <div class="flex flex-col gap-1">
                            <label class="text-slate-700 font-semibold text-sm tracking-wider">A. Corrective Action <span class="text-red-500">*</span></label>
                            <span class="text-slate-400 text-[10px] -mt-1 block italic">(Tindakan Darurat untuk mengatasi masalah)</span>
                        </div>
                        <!-- B. Preventive Action Header -->
                        <div class="flex flex-col gap-1">
                            <label class="text-slate-700 font-semibold text-sm tracking-wider">B. Preventive Action <span class="text-red-500">*</span></label>
                            <span class="text-slate-400 text-[10px] -mt-1 block italic">(Perbaikan yang harus segera dilakukan untuk menghilangkan akar penyebab)</span>
                        </div>
                    </div>

                    <!-- Row-by-Row Content -->
                    <div class="flex flex-col gap-6">
                        <!-- Row 1 -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 items-start">
                            <!-- Corrective Row 1 -->
                            <div class="flex flex-col gap-1 w-full">
                                <div class="flex items-center gap-2">
                                    <textarea name="corrective_action_one" rows="1" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm resize-none overflow-hidden text-slate-700" placeholder="Corrective Action 1..."></textarea>
                                    <div class="shrink-0">
                                        <input type="file" id="corr_one_file" class="hidden">
                                        <button type="button" onclick="document.getElementById('corr_one_file').click()" class="w-10 h-10 border border-dashed border-blue-300 bg-blue-50/50 hover:bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center transition-all relative" title="Take / Upload Photo or PDF">
                                            <i class="fas fa-camera text-xs"></i>
                                            <span class="text-red-500 absolute -top-2 -right-2 text-[10px] font-bold">*</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Preventive Row 1 -->
                            <div class="flex flex-col gap-1 w-full">
                                <div class="flex items-center gap-2">
                                    <textarea name="preventive_action_one" rows="1" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm resize-none overflow-hidden text-slate-700" placeholder="Preventive Action 1..."></textarea>
                                    <div class="shrink-0">
                                        <input type="file" id="prev_one_file" class="hidden">
                                        <button type="button" onclick="document.getElementById('prev_one_file').click()" class="w-10 h-10 border border-dashed border-blue-300 bg-blue-50/50 hover:bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center transition-all relative" title="Take / Upload Photo or PDF">
                                            <i class="fas fa-camera text-xs"></i>
                                            <span class="text-red-500 absolute -top-2 -right-2 text-[10px] font-bold">*</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Row 2 -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 items-start">
                            <!-- Corrective Row 2 -->
                            <div class="flex flex-col gap-1 w-full">
                                <div class="flex items-center gap-2">
                                    <textarea name="corrective_action_two" rows="1" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm resize-none overflow-hidden text-slate-700" placeholder="Corrective Action 2..."></textarea>
                                    <div class="shrink-0">
                                        <input type="file" id="corr_two_file" class="hidden">
                                        <button type="button" onclick="document.getElementById('corr_two_file').click()" class="w-10 h-10 border border-dashed border-blue-300 bg-blue-50/50 hover:bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center transition-all" title="Take / Upload Photo or PDF">
                                            <i class="fas fa-camera text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Preventive Row 2 -->
                            <div class="flex flex-col gap-1 w-full">
                                <div class="flex items-center gap-2">
                                    <textarea name="preventive_action_two" rows="1" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm resize-none overflow-hidden text-slate-700" placeholder="Preventive Action 2..."></textarea>
                                    <div class="shrink-0">
                                        <input type="file" id="prev_two_file" class="hidden">
                                        <button type="button" onclick="document.getElementById('prev_two_file').click()" class="w-10 h-10 border border-dashed border-blue-300 bg-blue-50/50 hover:bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center transition-all" title="Take / Upload Photo or PDF">
                                            <i class="fas fa-camera text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Row 3 -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 items-start">
                            <!-- Corrective Row 3 -->
                            <div class="flex flex-col gap-1 w-full">
                                <div class="flex items-center gap-2">
                                    <textarea name="corrective_action_three" rows="1" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm resize-none overflow-hidden text-slate-700" placeholder="Corrective Action 3..."></textarea>
                                    <div class="shrink-0">
                                        <input type="file" id="corr_three_file" class="hidden">
                                        <button type="button" onclick="document.getElementById('corr_three_file').click()" class="w-10 h-10 border border-dashed border-blue-300 bg-blue-50/50 hover:bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center transition-all" title="Take / Upload Photo or PDF">
                                            <i class="fas fa-camera text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Preventive Row 3 -->
                            <div class="flex flex-col gap-1 w-full">
                                <div class="flex items-center gap-2">
                                    <textarea name="preventive_action_three" rows="1" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm resize-none overflow-hidden text-slate-700" placeholder="Preventive Action 3..."></textarea>
                                    <div class="shrink-0">
                                        <input type="file" id="prev_three_file" class="hidden">
                                        <button type="button" onclick="document.getElementById('prev_three_file').click()" class="w-10 h-10 border border-dashed border-blue-300 bg-blue-50/50 hover:bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center transition-all" title="Take / Upload Photo or PDF">
                                            <i class="fas fa-camera text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes & Signatures -->
                <div class="border-t border-slate-100 pt-6">
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 sm:gap-6">
                        <!-- Notes for A & B -->
                        <div class="flex flex-col gap-1.5 sm:col-span-2">
                            <label class="text-slate-700 font-semibold text-sm tracking-wider">Notes for A & B</label>
                            <textarea name="notes" rows="1" class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none text-slate-700 resize-none overflow-hidden" placeholder="Enter verification notes..."></textarea>
                        </div>

                        <!-- Auditee -->
                        <div class="flex flex-col gap-1.5 sm:col-span-1">
                            <label class="text-slate-700 font-semibold text-sm tracking-wider">Auditee</label>
                            <input type="text" name="auditee_name" class="w-full px-4 py-[9px] border border-slate-200 rounded-lg bg-white text-slate-700 text-sm outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="Auditee">
                        </div>

                        <!-- Auditee Superior -->
                        <div class="flex flex-col gap-1.5 sm:col-span-1">
                            <label class="text-slate-700 font-semibold text-sm tracking-wider">Auditee Superior</label>
                            <input type="text" name="auditee_superior_name" class="w-full px-4 py-[9px] border border-slate-200 rounded-lg bg-white text-slate-700 text-sm outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="Auditee Superior">
                        </div>
                    </div>

                    <!-- Signature Stamps Status Block -->
                    <div class="mt-8 grid grid-cols-3 gap-1.5 sm:gap-4 border-t border-slate-100 pt-6">
                        <!-- Prepare by -->
                        <div class="flex flex-col items-center justify-between p-1 sm:p-4 rounded-lg sm:rounded-xl border border-slate-200 bg-slate-50/50 text-center min-h-[90px] sm:min-h-[140px]">
                            <span class="text-[8px] sm:text-xs font-semibold text-slate-500 tracking-wider">Prepare by</span>
                            <div class="my-0.5 sm:my-2 select-none">
                                <div class="inline-flex items-center border-[0.5px] sm:border-2 border-slate-300 font-bold uppercase tracking-widest text-[6px] sm:text-sm bg-white overflow-hidden">
                                    <div class="px-0.5 sm:px-2 py-0.5 border-r-[0.5px] sm:border-r-2 border-slate-300 text-slate-400">
                                        <i class="fa-solid fa-clock text-[5px] sm:text-[11px]"></i>
                                    </div>
                                    <div class="px-0.5 sm:px-2 py-0.5 text-slate-400">
                                        PENDING
                                    </div>
                                </div>
                            </div>
                            <div class="text-[8px] sm:text-xs">
                                <p class="text-slate-400 font-medium truncate max-w-[55px] sm:max-w-none">Auditee</p>
                            </div>
                        </div>

                        <!-- Checked by -->
                        <div class="flex flex-col items-center justify-between p-1 sm:p-4 rounded-lg sm:rounded-xl border border-slate-200 bg-slate-50/50 text-center min-h-[90px] sm:min-h-[140px]">
                            <span class="text-[8px] sm:text-xs font-semibold text-slate-500 tracking-wider">Checked by</span>
                            <div class="my-0.5 sm:my-2 select-none">
                                <div class="inline-flex items-center border-[0.5px] sm:border-2 border-slate-300 font-bold uppercase tracking-widest text-[6px] sm:text-sm bg-white overflow-hidden">
                                    <div class="px-0.5 sm:px-2 py-0.5 border-r-[0.5px] sm:border-r-2 border-slate-300 text-slate-400">
                                        <i class="fa-solid fa-clock text-[5px] sm:text-[11px]"></i>
                                    </div>
                                    <div class="px-0.5 sm:px-2 py-0.5 text-slate-400">
                                        PENDING
                                    </div>
                                </div>
                            </div>
                            <div class="text-[8px] sm:text-xs">
                                <p class="text-slate-400 font-medium truncate max-w-[55px] sm:max-w-none">Superior</p>
                            </div>
                        </div>

                        <!-- Confirm by -->
                        <div class="flex flex-col items-center justify-between p-1 sm:p-4 rounded-lg sm:rounded-xl border border-slate-200 bg-slate-50/50 text-center min-h-[90px] sm:min-h-[140px]">
                            <span class="text-[8px] sm:text-xs font-semibold text-slate-500 tracking-wider">Confirm by</span>
                            <div class="my-0.5 sm:my-2 select-none">
                                <div class="inline-flex items-center border-[0.5px] sm:border-2 border-slate-300 font-bold uppercase tracking-widest text-[6px] sm:text-sm bg-white overflow-hidden">
                                    <div class="px-0.5 sm:px-2 py-0.5 border-r-[0.5px] sm:border-r-2 border-slate-300 text-slate-400">
                                        <i class="fa-solid fa-clock text-[5px] sm:text-[11px]"></i>
                                    </div>
                                    <div class="px-0.5 sm:px-2 py-0.5 text-slate-400">
                                        PENDING
                                    </div>
                                </div>
                            </div>
                            <div class="text-[8px] sm:text-xs">
                                <p class="text-slate-400 font-medium truncate max-w-[55px] sm:max-w-none">Auditor</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end gap-3 border-t border-slate-100 pt-6">
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-all text-sm flex items-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-paper-plane text-xs"></i>
                        <span>Submit Action Report</span>
                    </button>
                </div>
            </div>
        </form>
    </main>
</div>
@endsection
