@extends('layouts.app')

@section('title', 'KPI Dashboard')

@section('content')
@include('layouts.sidebar')
@include('components.toast')

<!-- Main Content -->
<div class="lg:ml-20 min-h-screen flex flex-col bg-slate-50 overflow-x-hidden max-w-full">
    @include('layouts.header')

    <!-- Page Content -->
    <main class="flex-1 px-4 py-2 lg:px-6 lg:py-3">
        <!-- Page Title & Export Buttons -->
        <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-bold text-slate-800">Key Performance Indicators</h1>
                <p class="text-xs md:text-sm text-slate-500 mt-0.5 sm:mt-1">Monitor KPI achievements per department in real-time.</p>
            </div>
            <div class="flex items-center gap-2 sm:gap-3 w-full sm:w-auto">
                <button type="button" onclick="exportToExcel()" class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 px-3 py-2 sm:px-4 sm:py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-semibold rounded-lg shadow-sm transition-colors">
                    <i class="fa-solid fa-download text-xs sm:text-sm"></i>
                    <span>Export to Excel</span>
                </button>
                <button type="button" onclick="exportToPdf()" class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 px-3 py-2 sm:px-4 sm:py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs sm:text-sm font-semibold rounded-lg shadow-sm transition-colors">
                    <i class="fa-solid fa-download text-xs sm:text-sm"></i>
                    <span>Export PDF</span>
                </button>
            </div>
        </div>

        <!-- Summary Cards Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-2.5 sm:gap-3.5 mb-5">
            <!-- 1. Total KPI -->
            <div class="relative overflow-hidden bg-white p-3 sm:p-4 rounded-xl border border-slate-200/80 transition-all duration-200 flex items-center gap-2.5 sm:gap-4 group">
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-blue-500/10 rounded-full blur-2xl pointer-events-none group-hover:bg-blue-500/20 transition-all"></div>
                <i class="fa-solid fa-list-check absolute -right-2 -bottom-3 text-6xl text-blue-600/[0.07] -rotate-12 pointer-events-none group-hover:scale-110 group-hover:rotate-0 transition-all duration-300"></i>
                <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-lg sm:rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0 border border-blue-100/60 relative z-10">
                    <i class="fa-solid fa-list-check text-base sm:text-lg"></i>
                </div>
                <div class="min-w-0 flex-1 relative z-10">
                    <p class="text-[11px] sm:text-xs font-semibold text-slate-500 whitespace-nowrap overflow-hidden text-ellipsis">Total KPI</p>
                    <p id="card_total_kpi" class="text-lg sm:text-2xl font-bold text-slate-800 leading-tight">0</p>
                </div>
            </div>

            <!-- 2. Company KPI -->
            <div class="relative overflow-hidden bg-white p-3 sm:p-4 rounded-xl border border-slate-200/80 transition-all duration-200 flex items-center gap-2.5 sm:gap-4 group">
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-indigo-500/10 rounded-full blur-2xl pointer-events-none group-hover:bg-indigo-500/20 transition-all"></div>
                <i class="fa-solid fa-building absolute -right-2 -bottom-3 text-6xl text-indigo-600/[0.07] -rotate-12 pointer-events-none group-hover:scale-110 group-hover:rotate-0 transition-all duration-300"></i>
                <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-lg sm:rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0 border border-indigo-100/60 relative z-10">
                    <i class="fa-solid fa-building text-base sm:text-lg"></i>
                </div>
                <div class="min-w-0 flex-1 relative z-10">
                    <p class="text-[11px] sm:text-xs font-semibold text-slate-500 whitespace-nowrap overflow-hidden text-ellipsis">Company KPI</p>
                    <p id="card_company_kpi" class="text-lg sm:text-2xl font-bold text-slate-800 leading-tight">0</p>
                </div>
            </div>

            <!-- 3. Dept KPI -->
            <div class="relative overflow-hidden bg-white p-3 sm:p-4 rounded-xl border border-slate-200/80 transition-all duration-200 flex items-center gap-2.5 sm:gap-4 group">
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-purple-500/10 rounded-full blur-2xl pointer-events-none group-hover:bg-purple-500/20 transition-all"></div>
                <i class="fa-solid fa-users-gear absolute -right-2 -bottom-3 text-6xl text-purple-600/[0.07] -rotate-12 pointer-events-none group-hover:scale-110 group-hover:rotate-0 transition-all duration-300"></i>
                <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-lg sm:rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center flex-shrink-0 border border-purple-100/60 relative z-10">
                    <i class="fa-solid fa-users-gear text-base sm:text-lg"></i>
                </div>
                <div class="min-w-0 flex-1 relative z-10">
                    <p class="text-[11px] sm:text-xs font-semibold text-slate-500 whitespace-nowrap overflow-hidden text-ellipsis">Dept KPI</p>
                    <p id="card_dept_kpi" class="text-lg sm:text-2xl font-bold text-slate-800 leading-tight">0</p>
                </div>
            </div>

            <!-- 4. Achieved -->
            <div class="relative overflow-hidden bg-white p-3 sm:p-4 rounded-xl border border-slate-200/80 transition-all duration-200 flex items-center gap-2.5 sm:gap-4 group">
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none group-hover:bg-emerald-500/20 transition-all"></div>
                <i class="fa-solid fa-circle-check absolute -right-2 -bottom-3 text-6xl text-emerald-600/[0.07] -rotate-12 pointer-events-none group-hover:scale-110 group-hover:rotate-0 transition-all duration-300"></i>
                <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-lg sm:rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0 border border-emerald-100/60 relative z-10">
                    <i class="fa-solid fa-circle-check text-base sm:text-lg"></i>
                </div>
                <div class="min-w-0 flex-1 relative z-10">
                    <p class="text-[11px] sm:text-xs font-semibold text-slate-500 whitespace-nowrap overflow-hidden text-ellipsis">Achieved</p>
                    <p id="card_achieved" class="text-lg sm:text-2xl font-bold text-slate-800 leading-tight">0</p>
                </div>
            </div>

            <!-- 5. Not Achieved -->
            <div class="relative overflow-hidden bg-white p-3 sm:p-4 rounded-xl border border-slate-200/80 transition-all duration-200 flex items-center gap-2.5 sm:gap-4 group">
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-rose-500/10 rounded-full blur-2xl pointer-events-none group-hover:bg-rose-500/20 transition-all"></div>
                <i class="fa-solid fa-circle-xmark absolute -right-2 -bottom-3 text-6xl text-rose-600/[0.07] -rotate-12 pointer-events-none group-hover:scale-110 group-hover:rotate-0 transition-all duration-300"></i>
                <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-lg sm:rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0 border border-rose-100/60 relative z-10">
                    <i class="fa-solid fa-circle-xmark text-base sm:text-lg"></i>
                </div>
                <div class="min-w-0 flex-1 relative z-10">
                    <p class="text-[11px] sm:text-xs font-semibold text-slate-500 whitespace-nowrap overflow-hidden text-ellipsis">Not Achieved</p>
                    <p id="card_not_achieved" class="text-lg sm:text-2xl font-bold text-slate-800 leading-tight">0</p>
                </div>
            </div>

            <!-- 6. Waiting Data -->
            <div class="relative overflow-hidden bg-white p-3 sm:p-4 rounded-xl border border-slate-200/80 transition-all duration-200 flex items-center gap-2.5 sm:gap-4 group">
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-amber-500/10 rounded-full blur-2xl pointer-events-none group-hover:bg-amber-500/20 transition-all"></div>
                <i class="fa-solid fa-clock absolute -right-2 -bottom-3 text-6xl text-amber-600/[0.07] -rotate-12 pointer-events-none group-hover:scale-110 group-hover:rotate-0 transition-all duration-300"></i>
                <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-lg sm:rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0 border border-amber-100/60 relative z-10">
                    <i class="fa-solid fa-clock text-base sm:text-lg"></i>
                </div>
                <div class="min-w-0 flex-1 relative z-10">
                    <p class="text-[11px] sm:text-xs font-semibold text-slate-500 whitespace-nowrap overflow-hidden text-ellipsis">Waiting Data</p>
                    <p id="card_waiting_data" class="text-lg sm:text-2xl font-bold text-slate-800 leading-tight">0</p>
                </div>
            </div>
        </div>

        <!-- Department Performance & Overview Grid -->
        <div class="bg-white p-5 border border-gray-200 rounded-none mb-8">
            <!-- Stacked Bar Chart Section (At Very Top - Full Width) -->
            <div class="border-b border-slate-200 pb-8 mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-slate-800">KPI Performance Overview (Stacked)</h3>
                        <p class="text-[10px] sm:text-sm text-slate-500">Stacked achievement status per department</p>
                    </div>
                    <div class="flex items-center justify-between sm:justify-end gap-2 w-full sm:w-auto">
                        <x-month-input id="chartFilterDate" name="chartFilterDate" value="{{ date('Y-m') }}" />
                        <!-- Chart Pagination (Visible on Mobile only) -->
                        <div id="stackedChartPagination" class="hidden items-center gap-1.5">
                            <span id="stackedChartPageIndicator" class="text-xs sm:text-sm text-slate-600 font-medium mr-1 text-nowrap">1/2</span>
                            <button type="button" id="btnStackedChartPrev" class="w-8 h-8 flex items-center justify-center border border-slate-300 bg-white text-slate-600 hover:bg-slate-50 rounded-none disabled:opacity-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            <button type="button" id="btnStackedChartNext" class="w-8 h-8 flex items-center justify-center border border-slate-300 bg-white text-slate-600 hover:bg-slate-50 rounded-none disabled:opacity-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="relative h-[280px] w-full">
                    <canvas id="stackedDeptChart"></canvas>
                </div>
            </div>

            <!-- Department Performance Section (Side-by-side Bar Chart) -->
            <div class="grid grid-cols-1 xl:grid-cols-5 gap-4">
                <!-- Left Column: Chart & Table (80%) -->
                <div class="xl:col-span-4 border-b border-gray-100 pb-8 xl:pb-0 xl:border-b-0 xl:border-r pr-0 xl:pr-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                        <div>
                            <h3 class="text-base sm:text-lg font-bold text-slate-800">KPI Performance per Department</h3>
                            <p class="text-[10px] sm:text-sm text-slate-500">KPI achievement status per department</p>
                        </div>
                        <div class="flex items-center justify-between sm:justify-end gap-2 w-full sm:w-auto">
                            <!-- Chart Pagination (Visible on Mobile only) -->
                            <div id="chartPagination" class="hidden items-center gap-1.5">
                                <span id="chartPageIndicator" class="text-xs sm:text-sm text-slate-600 font-medium mr-1 text-nowrap">1/2</span>
                                <button type="button" id="btnChartPrev" class="w-8 h-8 flex items-center justify-center border border-slate-300 bg-white text-slate-600 hover:bg-slate-50 rounded-none disabled:opacity-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>
                                <button type="button" id="btnChartNext" class="w-8 h-8 flex items-center justify-center border border-slate-300 bg-white text-slate-600 hover:bg-slate-50 rounded-none disabled:opacity-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="relative h-[280px] w-full">
                        <canvas id="deptChart"></canvas>
                    </div>
                </div>

                <!-- Right Column: Findings Overview (20%) -->
                <div class="xl:col-span-1 pt-8 xl:pt-0 flex flex-col justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 mb-3">Overview</h3>
                        <div class="relative h-52 w-full flex justify-center mb-3">
                            <canvas id="statsPieChart"></canvas>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-sm text-slate-600 mt-auto">
                        <div class="flex items-center justify-between p-2.5 rounded-lg bg-emerald-50/60 border border-emerald-100">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-[#22c55e]"></span>
                                <span class="font-semibold text-slate-700 text-xs">Achieved</span>
                            </div>
                            <span id="val_ok" class="font-bold text-slate-800 text-xs">0</span>
                        </div>
                        <div class="flex items-center justify-between p-2.5 rounded-lg bg-rose-50/60 border border-rose-100">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-[#ef4444]"></span>
                                <span class="font-semibold text-slate-700 text-xs whitespace-nowrap">Not Achieved</span>
                            </div>
                            <span id="val_minor" class="font-bold text-slate-800 text-xs">0</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Closed Findings Chart -->
            <div class="mt-4 border-t border-slate-200 pt-4">
                <div class="grid grid-cols-1 xl:grid-cols-5 gap-4">
                    <!-- Left Column: Closed Findings Chart (80%) -->
                    <div class="xl:col-span-4 border-b border-gray-100 pb-8 xl:pb-0 xl:border-b-0 xl:border-r pr-0 xl:pr-4">
                        <div class="flex items-center justify-between gap-4 mb-3">
                            <div>
                                <h3 class="text-base sm:text-lg font-bold text-slate-800">KPI Performance Per Pillar</h3>
                                <p class="text-[10px] sm:text-sm text-slate-500">Summary of KPI achievements per strategic pillar</p>
                            </div>
                            <div class="flex flex-col items-end gap-2">
                                <!-- Chart Pagination (Visible on Mobile only) -->
                                <div id="closedChartPagination" class="hidden items-center gap-1.5">
                                    <span id="closedChartPageIndicator" class="text-xs sm:text-sm text-slate-600 font-medium mr-1 text-nowrap">1/2</span>
                                    <button type="button" id="btnClosedChartPrev" class="w-8 h-8 flex items-center justify-center border border-slate-300 bg-white text-slate-600 hover:bg-slate-50 rounded-none disabled:opacity-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>
                                    <button type="button" id="btnClosedChartNext" class="w-8 h-8 flex items-center justify-center border border-slate-300 bg-white text-slate-600 hover:bg-slate-50 rounded-none disabled:opacity-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="relative h-[280px] w-full">
                            <canvas id="closedDeptChart"></canvas>
                        </div>
                    </div>

                    <!-- Right Column: Closed Findings Overview Pie Chart (20%) -->
                    <div class="xl:col-span-1 pt-8 xl:pt-0 flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-slate-800 mb-3">Overview</h3>
                            <div class="relative h-52 w-full flex justify-center mb-3">
                                <canvas id="closedStatsPieChart"></canvas>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 text-sm text-slate-600 mt-auto">
                            <!-- Achieved -->
                            <div class="flex items-center justify-between p-2.5 rounded-lg bg-emerald-50/60 border border-emerald-100">
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full bg-[#22c55e]"></span>
                                    <span class="font-semibold text-slate-700 text-xs text-nowrap">Achieved</span>
                                </div>
                                <span id="val_minor_close" class="font-bold text-slate-800 text-xs">0</span>
                            </div>
                            <!-- Not Achieved -->
                            <div class="flex items-center justify-between p-2.5 rounded-lg bg-rose-50/60 border border-rose-100">
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full bg-[#ef4444]"></span>
                                    <span class="font-semibold text-slate-700 text-xs text-nowrap">Not Achieved</span>
                                </div>
                                <span id="val_major_close" class="font-bold text-slate-800 text-xs">0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>
    @include('layouts.footer')
</div>

<!-- Mobile Sidebar Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/50 z-30 hidden lg:hidden"></div>



<!-- Image Preview Modal (Before/After) -->
<div id="imagePreviewModal" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-900/60 transition-opacity" onclick="closeImageModal()"></div>

    <!-- Modal -->
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-none w-full max-w-5xl transform transition-all h-[90vh] flex flex-col">
            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b border-slate-200">
                <h3 class="text-lg font-semibold text-slate-800">Findings & Evidence Preview</h3>
                <button type="button" onclick="closeImageModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div class="p-6 overflow-y-auto flex-1">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 h-full">
                    <!-- Before Section -->
                    <div class="bg-slate-50/50 rounded-none p-5 border border-slate-100 h-full flex flex-col">
                        <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-200/60">
                            <div>
                                <h4 class="text-sm font-bold text-slate-800">Before Condition</h4>
                            </div>
                        </div>

                        <!-- Findings Text -->
                        <div class="mb-4">
                            <div class="relative bg-white p-3.5 rounded-none border border-slate-200">
                                <p id="modalCaptionBefore" class="text-slate-600 font-medium text-sm leading-relaxed"></p>
                            </div>
                        </div>

                        <!-- Images -->
                        <div id="imageContainerBefore" class="grid grid-cols-2 gap-3 content-start"></div>

                        <!-- Empty State -->
                        <div id="noImageBefore" class="hidden flex-1 flex flex-col items-center justify-center min-h-[140px] bg-slate-100/50 rounded-none border border-dashed border-slate-300/60 mt-auto">
                            <div class="w-10 h-10 bg-white rounded-none flex items-center justify-center mb-2 border border-slate-100">
                                <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-slate-400">No finding images</span>
                        </div>
                    </div>

                    <!-- After Section -->
                    <div class="bg-slate-50/50 rounded-none p-5 border border-slate-100 h-full flex flex-col">
                        <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-200/60">
                            <div>
                                <h4 class="text-sm font-bold text-slate-800">After Condition</h4>
                            </div>
                        </div>

                        <!-- Evidence Text -->
                        <div class="mb-4">
                            <div class="relative bg-white p-3.5 rounded-none border border-slate-200">
                                <p id="modalCaptionAfter" class="text-slate-600 font-medium text-sm leading-relaxed"></p>
                            </div>
                        </div>

                        <!-- Images -->
                        <div id="imageContainerAfter" class="grid grid-cols-2 gap-3 content-start"></div>

                        <!-- Empty State -->
                        <div id="noImageAfter" class="hidden flex-1 flex flex-col items-center justify-center min-h-[140px] bg-slate-100/50 rounded-none border border-dashed border-slate-300/60 mt-auto">
                            <div class="w-10 h-10 bg-white rounded-none flex items-center justify-center mb-2 border border-slate-100">
                                <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-slate-400">No evidence images</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="flex justify-end p-4 border-t border-slate-200">
                <button type="button" onclick="closeImageModal()"
                    class="px-6 py-2.5 bg-slate-100 text-slate-700 rounded-none font-medium hover:bg-slate-200 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
<!-- Delete Confirmation Modal -->
<div id="deleteConfirmModal" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-900/60 transition-opacity" onclick="closeDeleteModal()"></div>

    <!-- Modal -->
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-md transform transition-all">
            <!-- Header -->
            <div class="p-6 text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                    <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Confirm Delete</h3>
                <p class="text-slate-500">Are you sure you want to delete this item? This action cannot be undone.</p>
            </div>

            <!-- Footer -->
            <div class="flex gap-3 p-6 pt-0">
                <button type="button" id="btnCancelDelete" onclick="closeDeleteModal()"
                    class="flex-1 px-4 py-3 bg-slate-100 text-slate-700 rounded-xl font-semibold hover:bg-slate-200 transition-colors">
                    Cancel
                </button>
                <button type="button" id="btnConfirmDelete" onclick="executeDelete()"
                    class="flex-1 px-4 py-3 bg-red-600 text-white rounded-xl font-semibold hover:bg-red-700 transition-colors">
                    Yes, Delete
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let statsPieChart = null;
    let closedStatsPieChart = null;
    let lastPieData = null;

    function renderPieChart() {
        if (!lastPieData) return;

        const canvas = document.getElementById('statsPieChart');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');

        const totalSum = lastPieData.reduce((a, b) => a + b, 0);

        if (totalSum === 0) {
            if (statsPieChart) {
                statsPieChart.destroy();
                statsPieChart = null;
            }
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            return;
        }

        if (statsPieChart) {
            statsPieChart.data.labels = ['Achieved', 'Not Achieved'];
            statsPieChart.data.datasets[0].data = lastPieData;
            statsPieChart.data.datasets[0].backgroundColor = ['#22c55e', '#ef4444'];
            statsPieChart.update();
            return;
        }

        statsPieChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Achieved', 'Not Achieved'],
                datasets: [{
                    data: lastPieData,
                    backgroundColor: ['#22c55e', '#ef4444'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                animation: {
                    duration: 600,
                    easing: 'easeOutQuart'
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }

    function loadDataCards(yearMonth) {
        let param = yearMonth || "{{ date('Y-m') }}";
        $.ajax({
            url: "{{ route('dashboard.kpi.summary_cards', ':year') }}".replace(':year', param),
            type: "GET",
            dataType: "json",
            success: function(response) {
                // Update text values untuk summary cards atas
                $('#card_total_kpi').text(new Intl.NumberFormat().format(response.totalKpi || 0));
                $('#card_company_kpi').text(new Intl.NumberFormat().format(response.companyKpi || 0));
                $('#card_dept_kpi').text(new Intl.NumberFormat().format(response.deptKpi || 0));
                $('#card_achieved').text(new Intl.NumberFormat().format(response.achieved || 0));
                $('#card_not_achieved').text(new Intl.NumberFormat().format(response.notAchieved || 0));
                $('#card_waiting_data').text(new Intl.NumberFormat().format(response.waitingData || 0));

                // Update text values untuk overview KPI kanan
                $('#val_ok').text(new Intl.NumberFormat().format(response.achieved || 0));
                $('#val_minor').text(new Intl.NumberFormat().format(response.notAchieved || 0));
                $('#val_ofi').text(new Intl.NumberFormat().format(response.totalKpi || 0));

                lastPieData = [
                    response.achieved || 0,
                    response.notAchieved || 0
                ];

                renderPieChart();
            },
            error: function(xhr, status, error) {
                console.error(error);
                $('#card_total_kpi').text('0');
                $('#card_company_kpi').text('0');
                $('#card_dept_kpi').text('0');
                $('#card_achieved').text('0');
                $('#card_not_achieved').text('0');
                $('#card_waiting_data').text('0');

                $('#val_ok').text('0');
                $('#val_minor').text('0');
                $('#val_ofi').text('0');
            }
        });
    }

    // --- Department Chart Logic ---
    let deptChart = null;
    let stackedDeptChart = null;
    let stackedStatsPieChart = null;
    let table = null; 
    let selectedStatus = ''; 
    let selectedMonthYear = '';

    // Pagination state
    let rawChartData = null;
    let currentChartPage = 1;
    let chartPageSize = 5;
    let currentStackedChartPage = 1;
    let stackedChartPageSize = 5;
    let isMobileMode = null;

    function renderStackedPieChart() {
        if (!rawChartData) return;

        const sumOk = rawChartData.data_total_ok.reduce((a, b) => a + b, 0);
        const sumMinor = rawChartData.data_total_minor.reduce((a, b) => a + b, 0);
        const totalSum = sumOk + sumMinor;

        $('#val_ok_stacked').text(new Intl.NumberFormat().format(sumOk));
        $('#val_minor_stacked').text(new Intl.NumberFormat().format(sumMinor));

        const canvas = document.getElementById('stackedStatsPieChart');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');

        if (totalSum === 0) {
            if (stackedStatsPieChart) {
                stackedStatsPieChart.destroy();
                stackedStatsPieChart = null;
            }
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            return;
        }

        if (stackedStatsPieChart) {
            stackedStatsPieChart.data.labels = ['Achieved', 'Not Achieved'];
            stackedStatsPieChart.data.datasets[0].data = [sumOk, sumMinor];
            stackedStatsPieChart.data.datasets[0].backgroundColor = ['#22c55e', '#ef4444'];
            stackedStatsPieChart.update();
            return;
        }

        stackedStatsPieChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Achieved', 'Not Achieved'],
                datasets: [{
                    data: [sumOk, sumMinor],
                    backgroundColor: ['#22c55e', '#ef4444'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                animation: {
                    duration: 600,
                    easing: 'easeOutQuart'
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }

    function renderStackedDeptChart() {
        if (!rawChartData) return;

        const canvas = document.getElementById('stackedDeptChart');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');

        if (stackedDeptChart) {
            stackedDeptChart.destroy();
        }

        const isMobile = window.innerWidth < 1280;

        const width = window.innerWidth;
        if (width < 380) stackedChartPageSize = 3;
        else if (width < 480) stackedChartPageSize = 4;
        else if (width < 640) stackedChartPageSize = 5;
        else if (width < 768) stackedChartPageSize = 6;
        else if (width < 1024) stackedChartPageSize = 7;
        else stackedChartPageSize = 9;

        let labels = rawChartData.data_name_dept;
        let okData = rawChartData.data_total_ok;
        let minorData = rawChartData.data_total_minor;

        if (isMobile) {
            let zipped = [];
            for (let i = 0; i < labels.length; i++) {
                zipped.push({
                    name: labels[i],
                    ok: okData[i] || 0,
                    minor: minorData[i] || 0
                });
            }

            zipped.sort((a, b) => (b.ok + b.minor) - (a.ok + a.minor));

            labels = zipped.map(item => item.name);
            okData = zipped.map(item => item.ok);
            minorData = zipped.map(item => item.minor);

            const totalItems = labels.length;
            const totalPages = Math.ceil(totalItems / stackedChartPageSize) || 1;

            if (currentStackedChartPage < 1) currentStackedChartPage = 1;
            if (currentStackedChartPage > totalPages) currentStackedChartPage = totalPages;

            const startIndex = (currentStackedChartPage - 1) * stackedChartPageSize;
            const endIndex = startIndex + stackedChartPageSize;

            labels = labels.slice(startIndex, endIndex);
            okData = okData.slice(startIndex, endIndex);
            minorData = minorData.slice(startIndex, endIndex);

            $('#stackedChartPageIndicator').text(currentStackedChartPage + '/' + totalPages);
            $('#btnStackedChartPrev').prop('disabled', currentStackedChartPage === 1);
            $('#btnStackedChartNext').prop('disabled', currentStackedChartPage === totalPages);
            $('#stackedChartPagination').removeClass('hidden').addClass('flex');
        } else {
            $('#stackedChartPagination').removeClass('flex').addClass('hidden');
        }

        // Calculate 100% percentage datasets for stacked bar
        let rawOkList = okData;
        let rawMinorList = minorData;
        let pctOkData = [];
        let pctMinorData = [];

        for (let i = 0; i < labels.length; i++) {
            let ach = rawOkList[i] || 0;
            let notAch = rawMinorList[i] || 0;
            let total = ach + notAch;
            if (total > 0) {
                pctOkData.push((ach / total) * 100);
                pctMinorData.push((notAch / total) * 100);
            } else {
                pctOkData.push(0);
                pctMinorData.push(0);
            }
        }

        let delayed;

        stackedDeptChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Achieved',
                        data: pctOkData,
                        rawCounts: rawOkList,
                        backgroundColor: '#22c55e', // Green
                    },
                    {
                        label: 'Not Achieved',
                        data: pctMinorData,
                        rawCounts: rawMinorList,
                        backgroundColor: '#ef4444', // Red
                    }
                ]
            },
            plugins: [{
                id: 'customLabelsStacked',
                afterDatasetsDraw: (chart) => {
                    const { ctx } = chart;
                    chart.data.datasets.forEach((dataset, i) => {
                        const meta = chart.getDatasetMeta(i);
                        if (!meta.hidden) {
                            meta.data.forEach((element, index) => {
                                const pctVal = dataset.data[index];
                                if (pctVal > 0) {
                                    const pctRound = Math.round(pctVal);
                                    const labelText = `${pctRound}%`;
                                    const yPos = (element.y + element.base) / 2;
                                    
                                    ctx.fillStyle = '#ffffff';
                                    ctx.font = 'bold 11px sans-serif';
                                    ctx.textAlign = 'center';
                                    ctx.textBaseline = 'middle';
                                    ctx.fillText(labelText, element.x, yPos);
                                }
                            });
                        }
                    });
                }
            }],
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animations: {
                    y: {
                        duration: 1000,
                        easing: 'easeOutQuart',
                        delay: context => {
                            let delay = 0;
                            if (context.type === 'data' && context.mode === 'default' && !delayed) {
                                delay = context.dataIndex * 0 + 100;
                            }
                            return delay;
                        },
                        from: (context) => {
                            if (context.type === 'data' && context.mode === 'default' && !delayed) {
                                const scale = context.chart.scales.y;
                                if (scale) return scale.getPixelForValue(0);
                            }
                            return undefined;
                        },
                        loop: false
                    }
                },
                onClick: (e, elements, chart) => {
                    const points = chart.getElementsAtEventForMode(e, 'nearest', {
                        intersect: true
                    }, true);

                    if (points.length) {
                        const firstPoint = points[0];
                        const label = chart.data.labels[firstPoint.index];
                        const datasetLabel = chart.data.datasets[firstPoint.datasetIndex].label;

                        selectedStatus = '';
                        selectedClause = '';
                        selectedMonthYear = $('#chartFilterDate').val();

                        window.dispatchEvent(new CustomEvent('updateDeptFilter', {
                            detail: { id: label, name: label }
                        }));

                        window.dispatchEvent(new CustomEvent('updateCategoryFilter', {
                            detail: { id: datasetLabel, name: datasetLabel }
                        }));

                        if (table) {
                            table.ajax.reload();
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                scales: {
                    x: {
                        stacked: true,
                        grid: {
                            display: true,
                            drawOnChartArea: true,
                            drawTicks: false,
                            color: 'rgba(203, 213, 225, 0.4)',
                        },
                        ticks: {
                            maxRotation: 0,
                            minRotation: 0,
                            autoSkip: false
                        }
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        max: 100,
                        grid: {
                            borderDash: [2, 2]
                        },
                        ticks: {
                            precision: 0,
                            stepSize: 20,
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                const index = context.dataIndex;
                                const count = context.dataset.rawCounts ? context.dataset.rawCounts[index] : 0;
                                const pct = context.parsed.y !== null ? Math.round(context.parsed.y) : 0;

                                return `${label}${count} (${pct}%)`;
                            },
                            footer: function(tooltipItems) {
                                if (!tooltipItems.length) return '';
                                const index = tooltipItems[0].dataIndex;
                                const ach = tooltipItems[0].chart.data.datasets[0].rawCounts[index] || 0;
                                const notAch = tooltipItems[0].chart.data.datasets[1].rawCounts[index] || 0;
                                const total = ach + notAch;
                                return `Total KPI: ${total}`;
                            }
                        }
                    }
                }
            }
        });

        setTimeout(() => {
            delayed = true;
        }, 1500);

        requestAnimationFrame(function() {
            renderStackedPieChart();
        });
    }

    function loadDeptChart(yearMonth) {
        let param = yearMonth || "{{ date('Y-m') }}";
        $.ajax({
            url: "{{ route('dashboard.kpi.chart_data', ':year') }}".replace(':year', param),
            type: "GET",
            dataType: "json",
            success: function(response) {
                // Mengubah format response dari KPIController ke struktur chart bawaan template
                let depts = response.departments || [];
                let okList = [];
                let notAchievedList = [];
                let totalList = [];

                depts.forEach(function(d, idx) {
                    let totalAch = 0;
                    let totalNot = 0;
                    Object.keys(response.data || {}).forEach(function(pilarKey) {
                        let pData = response.data[pilarKey];
                        if (pData && pData.achieved && pData.achieved[idx] !== undefined) {
                            totalAch += pData.achieved[idx];
                        }
                        if (pData && pData.not_achieved && pData.not_achieved[idx] !== undefined) {
                            totalNot += pData.not_achieved[idx];
                        }
                    });
                    okList.push(totalAch);
                    notAchievedList.push(totalNot);
                    totalList.push(totalAch + totalNot);
                });

                rawChartData = {
                    data_name_dept: depts,
                    data_total_ok: okList,
                    data_total_minor: notAchievedList,
                    data_total_major: [],
                    data_total_ofi: totalList
                };
                currentChartPage = 1;
                currentStackedChartPage = 1;
                renderStackedDeptChart();
                renderDeptChart();
            },
            error: function(xhr) {
                console.error("Failed to load KPI chart data:", xhr);
            }
        });
    }

    function renderDeptChart() {
        if (!rawChartData) return;

        const canvas = document.getElementById('deptChart');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');

        if (deptChart) {
            deptChart.destroy();
        }

        const isMobile = window.innerWidth < 1280;
        isMobileMode = isMobile;

        // Dynamic page size based on screen width
        const width = window.innerWidth;
        if (width < 380) chartPageSize = 3;
        else if (width < 480) chartPageSize = 4;
        else if (width < 640) chartPageSize = 5;
        else if (width < 768) chartPageSize = 6;
        else if (width < 1024) chartPageSize = 7;
        else chartPageSize = 9;

        let labels = rawChartData.data_name_dept;
        let okData = rawChartData.data_total_ok;
        let minorData = rawChartData.data_total_minor;
        let majorData = rawChartData.data_total_major;
        let ofiData = rawChartData.data_total_ofi;

        if (isMobile) {
            // Zip and sort by major descending, then minor descending
            let zipped = [];
            for (let i = 0; i < labels.length; i++) {
                zipped.push({
                    name: labels[i],
                    ok: okData[i] || 0,
                    minor: minorData[i] || 0,
                    major: majorData[i] || 0,
                    ofi: ofiData[i] || 0
                });
            }

            zipped.sort((a, b) => {
                if (b.major !== a.major) {
                    return b.major - a.major;
                }
                return b.minor - a.minor;
            });

            labels = zipped.map(item => item.name);
            okData = zipped.map(item => item.ok);
            minorData = zipped.map(item => item.minor);
            majorData = zipped.map(item => item.major);
            ofiData = zipped.map(item => item.ofi);

            const totalItems = labels.length;
            const totalPages = Math.ceil(totalItems / chartPageSize) || 1;
            
            // Boundary checks
            if (currentChartPage < 1) currentChartPage = 1;
            if (currentChartPage > totalPages) currentChartPage = totalPages;

            const startIndex = (currentChartPage - 1) * chartPageSize;
            const endIndex = startIndex + chartPageSize;

            labels = labels.slice(startIndex, endIndex);
            okData = okData.slice(startIndex, endIndex);
            minorData = minorData.slice(startIndex, endIndex);
            majorData = majorData.slice(startIndex, endIndex);
            ofiData = ofiData.slice(startIndex, endIndex);

            $('#chartPageIndicator').text(currentChartPage + '/' + totalPages);
            $('#btnChartPrev').prop('disabled', currentChartPage === 1);
            $('#btnChartNext').prop('disabled', currentChartPage === totalPages);
            $('#chartPagination').removeClass('hidden').addClass('flex');
        } else {
            $('#chartPagination').removeClass('flex').addClass('hidden');
        }

        // Calculate max value for y-axis scaling
        const allValues = [
            ...okData,
            ...minorData,
            ...majorData
        ];
        const maxValue = Math.max(...allValues, 0);
        const suggestedMax = maxValue + 1;

        let delayed;

        deptChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                        label: 'Achieved',
                        data: okData,
                        backgroundColor: '#22c55e', // Green
                    },
                    {
                        label: 'Not Achieved',
                        data: minorData,
                        backgroundColor: '#ef4444', // Red
                    }
                ]
            },
            plugins: [{
                id: 'customLabels',
                afterDatasetsDraw: (chart) => {
                    const {
                        ctx
                    } = chart;
                    chart.data.datasets.forEach((dataset, i) => {
                        const meta = chart.getDatasetMeta(i);
                        if (!meta.hidden) {
                            meta.data.forEach((element, index) => {
                                const data = dataset.data[index];
                                if (data > 0) {
                                    ctx.fillStyle = '#334155'; // slate-700
                                    ctx.font = 'bold 11px sans-serif';
                                    ctx.textAlign = 'center';
                                    ctx.textBaseline = 'bottom';

                                    // Adjust position based on bar
                                    const xPos = element.x;
                                    const yPos = element.y - 3;

                                    ctx.fillText(data, xPos, yPos);
                                }
                            });
                        }
                    });
                }
            }],
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animations: {
                    y: {
                        duration: 1000,
                        easing: 'easeOutQuart',
                        delay: context => {
                            let delay = 0;
                            if (context.type === 'data' && context.mode === 'default' && !delayed) {
                                delay = context.dataIndex * 0 + 100; // Simultaneous 500ms delay on start
                            }
                            return delay;
                        },
                        from: (context) => {
                            if (context.type === 'data' && context.mode === 'default' && !delayed) {
                                const scale = context.chart.scales.y;
                                if (scale) return scale.getPixelForValue(0);
                            }
                            return undefined; // Default behavior for updates (hide/show)
                        },
                        loop: false
                    }
                },
                onClick: (e, elements, chart) => {
                    const points = chart.getElementsAtEventForMode(e, 'nearest', {
                        intersect: true
                    }, true);

                    if (points.length) {
                        const firstPoint = points[0];
                        const label = chart.data.labels[firstPoint.index];
                        const datasetLabel = chart.data.datasets[firstPoint.datasetIndex].label;

                        selectedStatus = ''; // Reset status
                        selectedClause = ''; // Reset clause
                        selectedMonthYear = $('#chartFilterDate').val(); // Filter by the chart's current month

                        // 1. Update Department Filter
                        window.dispatchEvent(new CustomEvent('updateDeptFilter', {
                            detail: {
                                id: label,
                                name: label
                            }
                        }));

                        // 2. Update Finding Category Filter
                        window.dispatchEvent(new CustomEvent('updateCategoryFilter', {
                            detail: {
                                id: datasetLabel,
                                name: datasetLabel
                            }
                        }));

                        // 3. Reload Table
                        if (table) {
                            table.ajax.reload();
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                scales: {
                    x: {
                        grid: {
                            display: true,
                            drawOnChartArea: true,
                            drawTicks: false,
                            color: 'rgba(203, 213, 225, 0.4)', // slate-300 with opacity
                        },
                        ticks: {
                            maxRotation: 0,
                            minRotation: 0,
                            autoSkip: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        suggestedMax: Math.max(suggestedMax, 5),
                        grid: {
                            borderDash: [2, 2]
                        },
                        ticks: {
                            precision: 0,
                            stepSize: 1,
                            maxTicksLimit: 6
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y;
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });

        // Mark initial animation as done
        setTimeout(() => {
            delayed = true;
        }, 1500);

        // Always create pie chart AFTER bar chart so grid layout is stable
        requestAnimationFrame(function() {
            renderPieChart();
        });
    }

    // --- Closed Department Chart Logic ---
    let closedDeptChart = null;
    let rawClosedChartData = null;
    let currentClosedChartPage = 1;
    let closedChartPageSize = 5;
 
    function loadClosedDeptChart(yearMonth) {
        let param = yearMonth || "{{ date('Y-m') }}";
        $.ajax({
            url: "{{ route('dashboard.kpi.chart_data', ':year') }}".replace(':year', param),
            type: "GET",
            dataType: "json",
            success: function(response) {
                let pillars = response.pillars || [];
                let achList = [];
                let notAchList = [];

                pillars.forEach(function(pilarKey) {
                    let pData = response.data[pilarKey];
                    let sumAch = 0;
                    let sumNot = 0;
                    if (pData && pData.achieved) {
                        sumAch = pData.achieved.reduce((a, b) => a + b, 0);
                    }
                    if (pData && pData.not_achieved) {
                        sumNot = pData.not_achieved.reduce((a, b) => a + b, 0);
                    }
                    achList.push(sumAch);
                    notAchList.push(sumNot);
                });

                rawClosedChartData = {
                    data_name_dept: pillars,
                    data_total_minor: achList,
                    data_total_major: notAchList,
                    data_total_minor_overdue: [],
                    data_total_major_overdue: [],
                    data_total_need_verif: []
                };
                currentClosedChartPage = 1;
                renderClosedDeptChart();
            },
            error: function(xhr) {
                console.error("Failed to load closed pillar chart data:", xhr);
            }
        });
    }
 
    function renderClosedDeptChart() {
        if (!rawClosedChartData) return;
 
        const canvas = document.getElementById('closedDeptChart');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
 
        if (closedDeptChart) {
            closedDeptChart.destroy();
        }
 
        const isMobile = window.innerWidth < 1280;

        // Dynamic page size based on screen width
        const width = window.innerWidth;
        if (width < 380) closedChartPageSize = 3;
        else if (width < 480) closedChartPageSize = 3;
        else if (width < 640) closedChartPageSize = 4;
        else if (width < 768) closedChartPageSize = 5;
        else if (width < 1024) closedChartPageSize = 6;
        else closedChartPageSize = 9;

        let labels = rawClosedChartData.data_name_dept;
        let minorData = rawClosedChartData.data_total_minor;
        let majorData = rawClosedChartData.data_total_major;

        if (isMobile) {
            const totalItems = labels.length;
            const totalPages = Math.ceil(totalItems / closedChartPageSize) || 1;

            if (currentClosedChartPage < 1) currentClosedChartPage = 1;
            if (currentClosedChartPage > totalPages) currentClosedChartPage = totalPages;

            const startIndex = (currentClosedChartPage - 1) * closedChartPageSize;
            const endIndex = startIndex + closedChartPageSize;

            labels = labels.slice(startIndex, endIndex);
            minorData = minorData.slice(startIndex, endIndex);
            majorData = majorData.slice(startIndex, endIndex);

            $('#closedChartPageIndicator').text(currentClosedChartPage + '/' + totalPages);
            $('#btnClosedChartPrev').prop('disabled', currentClosedChartPage === 1);
            $('#btnClosedChartNext').prop('disabled', currentClosedChartPage === totalPages);
            $('#closedChartPagination').removeClass('hidden').addClass('flex');
        } else {
            $('#closedChartPagination').removeClass('flex').addClass('hidden');
        }

        const allValues = [
            ...minorData,
            ...majorData
        ];
        const maxValue = Math.max(...allValues, 0);
        const suggestedMax = maxValue + 1;
 
        let delayed;
 
        closedDeptChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Achieved',
                        data: minorData,
                        backgroundColor: '#22c55e', // Green
                    },
                    {
                        label: 'Not Achieved',
                        data: majorData,
                        backgroundColor: '#ef4444', // Red
                    }
                ]
            },
            plugins: [{
                id: 'customLabelsClosed',
                afterDatasetsDraw: (chart) => {
                    const { ctx } = chart;
                    chart.data.datasets.forEach((dataset, i) => {
                        const meta = chart.getDatasetMeta(i);
                        if (!meta.hidden) {
                            meta.data.forEach((element, index) => {
                                const data = dataset.data[index];
                                if (data > 0) {
                                    ctx.fillStyle = '#334155';
                                    ctx.font = 'bold 11px sans-serif';
                                    ctx.textAlign = 'center';
                                    ctx.textBaseline = 'bottom';
                                    const xPos = element.x;
                                    const yPos = element.y - 3;
                                    ctx.fillText(data, xPos, yPos);
                                }
                            });
                        }
                    });
                }
            }],
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animations: {
                    y: {
                        duration: 1000,
                        easing: 'easeOutQuart',
                        delay: context => {
                            let delay = 0;
                            if (context.type === 'data' && context.mode === 'default' && !delayed) {
                                delay = context.dataIndex * 0 + 100;
                            }
                            return delay;
                        },
                        from: (context) => {
                            if (context.type === 'data' && context.mode === 'default' && !delayed) {
                                const scale = context.chart.scales.y;
                                if (scale) return scale.getPixelForValue(0);
                            }
                            return undefined;
                        },
                        loop: false
                    }
                },
                onClick: (e, elements, chart) => {
                    const points = chart.getElementsAtEventForMode(e, 'nearest', {
                        intersect: true
                    }, true);
 
                    if (points.length) {
                        const firstPoint = points[0];
                        const label = chart.data.labels[firstPoint.index];
                        const datasetLabel = chart.data.datasets[firstPoint.datasetIndex].label;
 
                        // Determine status and category
                        let mappedCategory = '';
                        if (datasetLabel === 'Minor Close' || datasetLabel === 'Minor Overdue') {
                            mappedCategory = 'Minor';
                        } else if (datasetLabel === 'Major Close' || datasetLabel === 'Major Overdue') {
                            mappedCategory = 'Mayor';
                        } else if (datasetLabel === 'Need Verif') {
                            mappedCategory = ''; // Show both minor and major under Need Verif click
                        }
 
                        if (datasetLabel.includes('Close')) {
                            selectedStatus = 'Closed';
                        } else if (datasetLabel.includes('Overdue')) {
                            selectedStatus = 'Overdue';
                        } else if (datasetLabel === 'Need Verif') {
                            selectedStatus = 'Need Verif';
                        } else {
                            selectedStatus = '';
                        }
 
                        selectedMonthYear = $('#chartFilterDate').val(); // Filter by the chart's current month
                        selectedClause = ''; // Reset clause
 
                        window.dispatchEvent(new CustomEvent('updateDeptFilter', {
                            detail: {
                                id: label,
                                name: label
                            }
                        }));
 
                        window.dispatchEvent(new CustomEvent('updateCategoryFilter', {
                            detail: {
                                id: mappedCategory,
                                name: mappedCategory
                            }
                        }));
 
                        console.log("closedDeptChart Clicked - label:", label, "datasetLabel:", datasetLabel);
                        console.log("closedDeptChart Mapped values - selectedStatus:", selectedStatus, "mappedCategory:", mappedCategory);

                        if (table) {
                            table.ajax.reload();
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                scales: {
                    x: {
                        grid: {
                            display: true,
                            drawOnChartArea: true,
                            drawTicks: false,
                            color: 'rgba(203, 213, 225, 0.4)',
                        },
                        ticks: {
                            maxRotation: 0,
                            minRotation: 0,
                            autoSkip: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        max: suggestedMax,
                        grid: {
                            borderDash: [2, 2]
                        },
                        ticks: {
                            maxTicksLimit: 6
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y;
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
 
        setTimeout(() => {
            delayed = true;
        }, 1500);

        // Compute Overview Totals for Pie Chart
        const sumAchieved = rawClosedChartData.data_total_minor.reduce((a, b) => a + b, 0);
        const sumNotAchieved = rawClosedChartData.data_total_major.reduce((a, b) => a + b, 0);
        const sumTotal = sumAchieved + sumNotAchieved;

        $('#val_minor_close').text(new Intl.NumberFormat().format(sumAchieved));
        $('#val_major_close').text(new Intl.NumberFormat().format(sumNotAchieved));
        $('#val_need_verif').text(new Intl.NumberFormat().format(sumTotal));

        const pieCanvas = document.getElementById('closedStatsPieChart');
        if (pieCanvas) {
            const pieCtx = pieCanvas.getContext('2d');
            if (sumTotal === 0) {
                if (closedStatsPieChart) {
                    closedStatsPieChart.destroy();
                    closedStatsPieChart = null;
                }
                pieCtx.clearRect(0, 0, pieCanvas.width, pieCanvas.height);
            } else if (closedStatsPieChart) {
                closedStatsPieChart.data.labels = ['Achieved', 'Not Achieved'];
                closedStatsPieChart.data.datasets[0].data = [sumAchieved, sumNotAchieved];
                closedStatsPieChart.data.datasets[0].backgroundColor = ['#22c55e', '#ef4444'];
                closedStatsPieChart.update();
            } else {
                closedStatsPieChart = new Chart(pieCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Achieved', 'Not Achieved'],
                        datasets: [{
                            data: [sumAchieved, sumNotAchieved],
                            backgroundColor: ['#22c55e', '#ef4444'],
                            borderWidth: 0,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        animation: {
                            duration: 600,
                            easing: 'easeOutQuart'
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }
        }
    }

    // Initialize Chart
    $(document).ready(function() {
        const initialDate = $('#chartFilterDate').val() || '{{ date("Y-m") }}';
        selectedMonthYear = initialDate;
        loadDeptChart(initialDate);
        loadClosedDeptChart(initialDate);
        loadDataCards(initialDate);

        $(document).on('change', '#chartFilterDate', function() {
            const val = $(this).val();
            selectedMonthYear = val;
            loadDeptChart(val);
            loadClosedDeptChart(val);
            loadDataCards(val);
        });

        // Stacked Chart Pagination buttons
        $('#btnStackedChartPrev').click(function() {
            if (currentStackedChartPage > 1) {
                currentStackedChartPage--;
                renderStackedDeptChart();
            }
        });

        $('#btnStackedChartNext').click(function() {
            if (rawChartData) {
                const totalItems = rawChartData.data_name_dept.length;
                const totalPages = Math.ceil(totalItems / stackedChartPageSize) || 1;
                if (currentStackedChartPage < totalPages) {
                    currentStackedChartPage++;
                    renderStackedDeptChart();
                }
            }
        });

        // Pagination buttons
        $('#btnChartPrev').click(function() {
            if (currentChartPage > 1) {
                currentChartPage--;
                renderDeptChart();
            }
        });
 
        $('#btnChartNext').click(function() {
            if (rawChartData) {
                const totalItems = rawChartData.data_name_dept.length;
                const totalPages = Math.ceil(totalItems / chartPageSize) || 1;
                if (currentChartPage < totalPages) {
                    currentChartPage++;
                    renderDeptChart();
                }
            }
        });
 
        // Closed Chart Pagination buttons
        $('#btnClosedChartPrev').click(function() {
            if (currentClosedChartPage > 1) {
                currentClosedChartPage--;
                renderClosedDeptChart();
            }
        });
 
        $('#btnClosedChartNext').click(function() {
            if (rawClosedChartData) {
                const totalItems = rawClosedChartData.data_name_dept.length;
                const totalPages = Math.ceil(totalItems / closedChartPageSize) || 1;
                if (currentClosedChartPage < totalPages) {
                    currentClosedChartPage++;
                    renderClosedDeptChart();
                }
            }
        });

        // Handle resize
        $(window).resize(function() {
            const currentMobile = window.innerWidth < 1280;
            if (currentMobile !== isMobileMode) {
                currentStackedChartPage = 1;
                renderStackedDeptChart();
                currentChartPage = 1;
                renderDeptChart();
                renderPieChart();
                currentClosedChartPage = 1;
                renderClosedDeptChart();
            }
        });
    });

    function exportToExcel() {
        showToast('Export to Excel placeholder', 'info');
    }

    function exportToPdf() {
        showToast('Export PDF placeholder', 'info');
    }
</script>
@endpush