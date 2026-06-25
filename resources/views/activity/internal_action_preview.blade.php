@extends('layouts.app')

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
                class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-blue-50 border border-blue-200 text-blue-600 hover:bg-blue-100 hover:text-blue-700 transition-all duration-200 shadow-sm">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-700">Action Report Preview</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Detailed overview of CAR Action Report finding</p>
            </div>
        </div>

        <!-- Combined Details Card -->
        <div class="bg-white rounded-lg border border-slate-200 p-4 sm:p-8 shadow-sm space-y-8">
            
            <!-- Section 1: Finding General Information -->
            <div>
                <h2 class="text-base font-bold text-slate-800 mb-5 pb-2 border-b border-slate-100">
                    Finding Information
                </h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
                    <!-- CAR Number -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-slate-500 text-xs tracking-wider">CAR / Request Number</label>
                        <div class="bg-slate-50 border border-slate-100 rounded-lg px-4 py-3 text-slate-600 font-semibold text-sm">
                            {{ $car->req_number ?? '-' }}
                        </div>
                    </div>

                    <!-- Finding Category -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-slate-500 text-xs tracking-wider">Finding Category</label>
                        <div class="bg-slate-50 border border-slate-100 rounded-lg px-4 py-3 text-slate-600 font-semibold text-sm">
                            {{ $car->finding_category ?? 'OFI' }}
                        </div>
                    </div>

                    <!-- Department -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-slate-500 text-xs tracking-wider">Auditee Department</label>
                        <div class="bg-slate-50 border border-slate-100 rounded-lg px-4 py-3 text-slate-600 font-semibold text-sm">
                            {{ $car->department ?? '-' }}
                        </div>
                    </div>

                    <!-- Date Created -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-slate-500 text-xs tracking-wider">Report Date</label>
                        <div class="bg-slate-50 border border-slate-100 rounded-lg px-4 py-3 text-slate-600 font-semibold text-sm">
                            {{ $car->formatted_date }}
                        </div>
                    </div>

                    <!-- Auditor -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-slate-500 text-xs tracking-wider">Auditor</label>
                        <div class="bg-slate-50 border border-slate-100 rounded-lg px-4 py-3 text-slate-600 font-semibold text-sm">
                            {{ $car->auditor ?? '-' }}
                        </div>
                    </div>

                    <!-- Auditee -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-slate-500 text-xs tracking-wider">Auditee</label>
                        <div class="bg-slate-50 border border-slate-100 rounded-lg px-4 py-3 text-slate-600 font-semibold text-sm">
                            {{ $car->auditee ?? '-' }}
                        </div>
                    </div>

                    <!-- Surveillance -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-slate-500 text-xs tracking-wider">Surveillance</label>
                        <div class="bg-slate-50 border border-slate-100 rounded-lg px-4 py-3 text-slate-600 font-semibold text-sm min-h-[46px]">
                            {{ $car->surveillance ?? '' }}
                        </div>
                    </div>

                    <!-- External -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-slate-500 text-xs tracking-wider">External</label>
                        <div class="bg-slate-50 border border-slate-100 rounded-lg px-4 py-3 text-slate-600 font-semibold text-sm min-h-[46px]">
                            {{ $car->external ?? '' }}
                        </div>
                    </div>

                    <!-- Internal Audit -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-slate-500 text-xs tracking-wider">Internal Audit</label>
                        <div class="bg-slate-50 border border-slate-100 rounded-lg px-4 py-3 text-slate-600 font-semibold text-sm min-h-[46px]">
                            {{ $car->internal_audit ?? '' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Requirement & Clause Standards -->
            <div class="border-t border-slate-100 pt-6">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
                    <div class="flex flex-col gap-1.5">
                        <label class="text-slate-500 font-semibold text-xs tracking-wider">Requirement No.</label>
                        <div class="bg-slate-50 border border-slate-100 rounded-lg px-4 py-3 text-slate-800 text-sm">
                            {{ $car->requirement_no ?? '-' }}
                        </div>
                    </div>
                    <div class="flex flex-col gap-1.5 sm:col-span-2">
                        <label class="text-slate-500 font-semibold text-xs tracking-wider">Clause Title</label>
                        <div class="bg-slate-50 border border-slate-100 rounded-lg px-4 py-3 text-slate-800 text-sm">
                            {{ $car->clause_title ?? '-' }}
                        </div>
                    </div>
                    <div class="flex flex-col gap-1.5 sm:col-span-3">
                        <label class="text-slate-500 font-semibold text-xs tracking-wider">Check Item</label>
                        <div class="bg-slate-50 border border-slate-100 rounded-lg px-4 py-3 text-slate-800 text-sm leading-relaxed">
                            {{ $car->check_item ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Clause Description & Finding Evidence Details side-by-side -->
            <div class="border-t border-slate-100 pt-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                    <div class="flex flex-col gap-1.5">
                        <label class="text-slate-500 font-semibold text-xs tracking-wider">Clause Description / Klausul</label>
                        <div class="bg-slate-50 border border-slate-100 rounded-lg px-4 py-3 text-slate-800 text-sm leading-relaxed h-full">
                            {{ $car->clause_text ?? '-' }}
                        </div>
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-slate-500 font-semibold text-xs tracking-wider">Finding Evidence Details</label>
                        <div class="bg-slate-50 border border-slate-100 rounded-lg px-4 py-3 text-slate-800 text-sm leading-relaxed h-full">
                            {{ $car->evidence ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    @include('layouts.footer')
</div>

@endsection
