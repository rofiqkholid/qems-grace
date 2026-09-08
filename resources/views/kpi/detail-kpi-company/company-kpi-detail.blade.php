@extends('layouts.app')

@php
    $hideCentralToast = true;

    $parseLocalNum = function($vStr) {
        if ($vStr === null || $vStr === '') return 0;
        if (is_numeric($vStr)) return (float)$vStr;
        $clean = str_replace('.', '', (string)$vStr);
        $clean = str_replace(',', '.', $clean);
        return (float)$clean;
    };

    $unitVal = trim($kpi->unit ?? '');
    $isNumberUnit = in_array(strtolower($unitVal), ['number', 'num']);
    $unitSuffix = (!empty($unitVal) && !$isNumberUnit) ? ' ' . $unitVal : '';

    $formatNumWithUnit = function($num) use ($parseLocalNum, $unitSuffix) {
        if ($num === null || $num === '' || $num === '-') return $num;
        $val = $parseLocalNum($num);
        $formatted = (floor($val) == $val) ? number_format($val, 0, ',', '.') : number_format($val, 2, ',', '.');
        return $formatted . $unitSuffix;
    };

    $calculatedActuals = [];
    $monthsList = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    foreach ($monthsList as $m) {
        $act = $activities->firstWhere('bulan', $m);
        $calculatedActual = null;

        if (isset($formula) && $formula && !empty($components)) {
            $op = ($act && $act->calc_operator !== null && $act->calc_operator !== '') ? $act->calc_operator : (($formula && !empty($formula->calc_operator)) ? $formula->calc_operator : null);
            if (!empty($op)) {
                if (strpos($op, '[') !== false) {
                    $expr = $op;
                    // Auto-prefix master style [comp_X] with current month name
                    $expr = preg_replace('/\[(comp_\d+)\]/', '[' . $m . '.$1]', $expr);
                    preg_match_all('/\[([A-Za-z]{3})\.(comp_\d+)\]/', $expr, $matches, PREG_SET_ORDER);
                    
                    $hasAnyFormulaComponentValue = false;
                    $countComponentsInExpr = 0;
                    foreach ($matches as $match) {
                        $mName = $match[1];
                        $cCol = $match[2];
                        $actForMonth = $activities->firstWhere('bulan', $mName);
                        $rawVal = $actForMonth ? $actForMonth->$cCol : null;
                        if ($rawVal !== null && $rawVal !== '') {
                            $hasAnyFormulaComponentValue = true;
                            $countComponentsInExpr++;
                        }
                        $compVal = ($actForMonth && $actForMonth->$cCol !== null && $actForMonth->$cCol !== '') ? $actForMonth->$cCol : 0;
                        $val = $parseLocalNum($compVal);
                        $expr = str_replace($match[0], $val, $expr);
                    }
                    if ($hasAnyFormulaComponentValue) {
                        $expr = str_replace(['x', 'X'], '*', $expr);
                        $exprClean = preg_replace('/[^0-9\+\-\*\/\(\)\.\s]/', '', $expr);
                        if (!empty($exprClean)) {
                            try {
                                $calculatedActual = @eval("return ({$exprClean});");
                                $unit = ($act && !empty($act->unit)) ? $act->unit : ($kpi->unit ?? '');
                                $isPercentUnit = in_array(strtolower(trim($unit)), ['%', 'percent', 'persen']);
                                if ($isPercentUnit && strpos($exprClean, '*') !== false && strpos($exprClean, '/') === false && $countComponentsInExpr > 1) {
                                    $calculatedActual = $calculatedActual / pow(100, $countComponentsInExpr - 1);
                                }
                            } catch (\Throwable $t) {
                                $calculatedActual = null;
                            }
                        }
                    }
                } else {
                    $vals = [];
                    for ($i = 1; $i <= 20; $i++) {
                        $col = 'comp_' . $i;
                        if (!empty($formula->$col)) {
                            $compVal = ($act && $act->{'comp_' . $i} !== null) ? $act->{'comp_' . $i} : null;
                            if ($compVal !== null) {
                                $vals[] = $parseLocalNum($compVal);
                            }
                        }
                    }

                    if (!empty($vals)) {
                        $unit = $act->unit ?? ($kpi->unit ?? '');
                        $isPercentUnit = in_array(strtolower(trim($unit)), ['%', 'percent', 'persen']);
                        if ($op === '+') {
                            $calculatedActual = array_sum($vals);
                        } elseif ($op === '-') {
                            $calculatedActual = array_reduce(array_slice($vals, 1), function($carry, $item) {
                                return $carry - $item;
                            }, $vals[0]);
                        } elseif ($op === 'x' || $op === '*') {
                            $calculatedActual = array_reduce($vals, function($carry, $item) {
                                return $carry * $item;
                            }, 1);
                            if ($isPercentUnit && count($vals) > 1) {
                                $calculatedActual = $calculatedActual / pow(100, count($vals) - 1);
                            }
                        } elseif ($op === '/') {
                            $calculatedActual = array_reduce(array_slice($vals, 1), function($carry, $item) {
                                return $item != 0 ? $carry / $item : 0;
                            }, $vals[0]);
                        } elseif ($op === 'Average') {
                            $calculatedActual = array_sum($vals) / count($vals);
                        }
                    }
                }
            } else {
                $vals = [];
                $hasSomeComponentValue = false;
                for ($i = 1; $i <= 20; $i++) {
                    $col = 'comp_' . $i;
                    if (!empty($formula->$col)) {
                        $compVal = ($act && $act->$col !== null && $act->$col !== '') ? $act->$col : null;
                        if ($compVal !== null) {
                            $vals[] = $parseLocalNum($compVal);
                            $hasSomeComponentValue = true;
                        }
                    }
                }
                if ($hasSomeComponentValue) {
                    $calculatedActual = array_sum($vals);
                }
            }
        }

        if ($calculatedActual !== null && is_numeric($calculatedActual)) {
            $calculatedActual = round((float) $calculatedActual, 2);
        }

        $calculatedActuals[$m] = ($calculatedActual !== null) ? $calculatedActual : ($act ? ($act->actual !== null && is_numeric($act->actual) ? round((float) $act->actual, 2) : $act->actual) : null);
    }
@endphp

@section('title', 'Detail KPI Company')

@section('content')
@include('layouts.sidebar')
@include('components.toast')
@if(session('info'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        showToast("{{ session('info') }}", 'info');
    });
</script>
@endif

<!-- Main Content -->
<div class="lg:ml-20 min-h-screen flex flex-col bg-slate-50">
    @include('layouts.header')

    <!-- Page Content -->
    <main class="flex-1 p-6">
        <!-- Back Button & Page Title -->
        <div class="mb-6 flex items-center gap-3">
            <a href="{{ route('kpi.company') }}" class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl shrink-0 flex items-center justify-center bg-white border border-slate-200 hover:bg-slate-50 transition-colors">
                <i class="fa-solid fa-arrow-left text-[11px] sm:text-sm text-slate-600"></i>
            </a>
            <div>
                <h1 class="text-lg sm:text-2xl font-bold text-slate-800">Detail KPI Company</h1>
                <p class="text-slate-500 text-sm">Detailed performance tracking, objective details and monthly actual trend.</p>
            </div>
        </div>

        <!-- Detail Content -->
        <div class="space-y-6">
            <!-- Unified Detail Card -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-6">
                <!-- Summary Info -->
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-6 border-b border-slate-200 w-full">
                    <!-- Left side: Text blocks grouped together -->
                    <div class="grid grid-cols-2 lg:flex lg:flex-row lg:items-center gap-4 lg:gap-16 w-full lg:w-auto">
                        <div class="col-span-1 lg:flex-initial min-w-0">
                            <span class="text-slate-500 text-[10px] sm:text-xs tracking-wider block mb-1">KPI Objective</span>
                            <span class="text-sm font-semibold text-slate-700 block truncate" title="{{ $kpi->no_kpi }} - {{ $kpi->objective }}">{{ $kpi->no_kpi }} - {{ $kpi->objective }}</span>
                        </div>
                        <div class="col-span-1 lg:flex-initial">
                            <span class="text-slate-500 text-[10px] sm:text-xs tracking-wider block mb-1">Pillar / Dept</span>
                            <span class="text-sm font-semibold text-slate-700 block">{{ $kpi->pillar ?? '-' }} / {{ $kpi->department_code }}</span>
                        </div>
                        <div class="col-span-1 lg:flex-initial">
                            <span class="text-slate-500 text-[10px] sm:text-xs tracking-wider block mb-1">Periode / Target</span>
                            <span class="text-sm font-semibold text-slate-700 block">{{ $kpi->periode }} / {{ $kpi->operator }} {{ $kpi->target }} {{ $kpi->unit }}</span>
                        </div>
                        <div class="col-span-1 lg:flex-initial">
                            <span class="text-slate-500 text-[10px] sm:text-xs tracking-wider block mb-1">Calculation Method</span>
                            <span class="text-sm font-semibold text-slate-700 block">{{ $kpi->calculation_method }}</span>
                        </div>
                    </div>
                    
                    <!-- Right side: Buttons -->
                    <div class="lg:flex-initial flex items-center lg:justify-end w-full lg:w-auto">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-2 w-full sm:w-auto">
                            <div class="flex gap-2 w-full sm:w-auto">
                                <!-- Print PDF Button -->
                                <button type="button" class="inline-flex items-center justify-center gap-2 flex-1 sm:flex-none sm:w-32 py-3 text-xs font-semibold text-white bg-rose-600 rounded-xl hover:bg-rose-700 transition-colors shadow-sm">
                                    <i class="fa-solid fa-file-pdf text-sm"></i>
                                    Print
                                </button>
                                <!-- Export Excel Button -->
                                <button type="button" class="inline-flex items-center justify-center gap-2 flex-1 sm:flex-none sm:w-36 py-3 text-xs font-semibold text-white bg-emerald-600 rounded-xl hover:bg-emerald-700 transition-colors shadow-sm">
                                    <i class="fa-solid fa-file-excel text-sm"></i>
                                    Export Excel
                                </button>
                            </div>
                            <div class="hidden sm:block w-px h-6 bg-slate-200 mx-1"></div>
                            <!-- Manage Activity Button -->
                            <a href="{{ route('kpi.company.manage_activity_plan', \App\Http\Controllers\KPICompanyController::encodeId($kpi->id)) }}" class="inline-flex items-center justify-center gap-2 w-full sm:w-auto px-5 py-3 text-xs font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-colors shadow-sm whitespace-nowrap">
                                <i class="fa-solid fa-bars-progress text-sm"></i>
                                Manage Activity Plan
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Chart & Table section -->
                <div>
                <!-- Title Header -->
                <div class="mb-6 text-center lg:text-left">
                    <h3 class="text-sm sm:text-lg font-bold text-slate-700">12-Month Performance Summary</h3>
                </div>

                <!-- Flex container for indicator and chart -->
                <div class="flex flex-col lg:flex-row items-center gap-6">
                    <!-- Target direction indicator (Left) -->
                    <div class="flex items-center gap-3 sm:gap-4 lg:h-32 w-full lg:w-auto justify-center lg:justify-start">
                        <div class="flex items-center gap-1.5 sm:gap-2 border border-green-500 rounded-lg sm:rounded-xl px-2.5 py-1 sm:px-4 sm:py-2 bg-green-50/30 shrink-0">
                            @if(strtolower($kpi->arrow_target ?? '') === 'down')
                                <!-- Down Arrow -->
                                <svg class="w-4 h-4 sm:w-6 sm:h-6 text-green-600 fill-current" viewBox="0 0 24 24">
                                    <path d="M20 12l-1.41-1.41L13 16.17V4h-2v12.17l-5.58-5.59L4 12l8 8 8-8z"/>
                                </svg>
                                <span class="text-green-600 font-bold text-sm sm:text-lg">Good</span>
                            @else
                                <!-- Up Arrow -->
                                <svg class="w-4 h-4 sm:w-6 sm:h-6 text-green-600 fill-current" viewBox="0 0 24 24">
                                    <path d="M4 12l1.41 1.41L11 7.83V20h2V7.83l5.58 5.59L20 12l-8-8-8 8z"/>
                                </svg>
                                <span class="text-green-600 font-bold text-sm sm:text-lg">Good</span>
                            @endif
                        </div>

                        <!-- Explicit Separator Line -->
                        <div class="w-px h-12 lg:h-32 bg-slate-200 shrink-0 mx-2 sm:mx-4"></div>

                        <!-- Chart Pagination (Visible on Mobile only, positioned to the right of the separator) -->
                        <div id="chartPagination" class="hidden items-center gap-1.5 shrink-0">
                            <span id="chartPageIndicator" class="text-xs sm:text-sm text-slate-600 font-medium mr-1 text-nowrap">1/2</span>
                            <button type="button" id="btnChartPrev" class="w-8 h-8 flex items-center justify-center border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 hover:text-blue-600 rounded-lg disabled:opacity-50 disabled:hover:text-slate-600 disabled:hover:bg-white transition-colors shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            <button type="button" id="btnChartNext" class="w-8 h-8 flex items-center justify-center border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 hover:text-blue-600 rounded-lg disabled:opacity-50 disabled:hover:text-slate-600 disabled:hover:bg-white transition-colors shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex-1 w-full min-w-0 relative h-[280px]">
                        @php
                            $actualList = [];
                            foreach (['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] as $m) {
                                $actualList[] = ($calculatedActuals[$m] !== null) ? (float) $calculatedActuals[$m] : null;
                            }
                        @endphp
                        <canvas id="kpiPerformanceChart" data-actual='{!! json_encode($actualList) !!}' data-target="{{ $kpi->target }}" data-operator="{!! $kpi->operator !!}" data-arrow-target="{{ strtolower($kpi->arrow_target ?? '') }}"></canvas>
                    </div>
                </div>

                <!-- Divider Line -->
                <div class="border-t border-slate-200 my-6"></div>

                <!-- Data Table -->
                <div class="overflow-x-auto border border-slate-200 rounded-xl">
                    <table class="w-full text-sm text-left text-slate-700">
                        <thead class="bg-slate-100 text-slate-700 font-semibold border-b border-slate-200">
                            <tr>
                                <th class="p-3 border-r border-slate-200 whitespace-nowrap">Bulan / Component</th>
                                @foreach(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] as $m)
                                    <th class="p-3 text-center border-r border-slate-200">{{ $m }}</th>
                                @endforeach
                                <th class="p-3 text-center font-bold">{{ ($kpi->result === 'Average') ? 'Average' : 'Total' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="bg-slate-50 border-b border-slate-200 font-bold text-slate-800">
                                <td class="p-3 border-r border-slate-200 whitespace-nowrap">
                                    Target <span class="text-slate-500 font-medium">({{ $kpi->unit }}) {{ $kpi->operator }} {{ $kpi->target }}</span>
                                </td>
                                @php
                                    $targetSum = 0;
                                @endphp
                                @foreach(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] as $m)
                                    @php
                                        $targetSum += $parseLocalNum($kpi->target);
                                    @endphp
                                    <td class="p-3 text-center border-r border-slate-200">
                                        {{ $formatNumWithUnit($kpi->target) }}
                                    </td>
                                @endforeach
                                @php
                                    $targetFinal = ($kpi->result === 'Average') ? ($parseLocalNum($kpi->target)) : $targetSum;
                                @endphp
                                <td class="p-3 text-center">
                                    {{ $formatNumWithUnit($targetFinal) }}
                                </td>
                            </tr>

                            @if(!empty($components))
                                @foreach($components as $index => $name)
                                    <tr class="border-b border-slate-200">
                                        <td class="p-3 font-normal text-slate-700 border-r border-slate-200 whitespace-nowrap">{{ $name }}</td>
                                        @php
                                            $compSum = 0;
                                            $hasComp = false;
                                            $compCount = 0;
                                        @endphp
                                        @foreach(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] as $m)
                                            @php
                                                $act = $activities->firstWhere('bulan', $m);
                                                $compVal = ($act && $act->{'comp_' . $index} !== null && $act->{'comp_' . $index} !== '') ? $act->{'comp_' . $index} : null;
                                            @endphp
                                            @if($compVal !== null)
                                                @php
                                                    $val = $parseLocalNum($compVal);
                                                    $compSum += $val;
                                                    $hasComp = true;
                                                    $compCount++;
                                                @endphp
                                                <td class="p-3 text-center border-r border-slate-200 font-normal text-slate-700">
                                                    {{ $formatNumWithUnit($compVal) }}
                                                </td>
                                            @else
                                                <td class="p-3 text-center border-r border-slate-200 text-slate-300 select-none"></td>
                                            @endif
                                        @endforeach
                                        @php
                                            $compFinal = $hasComp ? (($kpi->result === 'Average') ? ($compCount > 0 ? ($compSum / $compCount) : 0) : $compSum) : '';
                                        @endphp
                                        <td class="p-3 text-center font-semibold text-slate-700">
                                            {{ $compFinal !== '' ? $formatNumWithUnit($compFinal) : '' }}
                                        </td>
                                    </tr>
                                @endforeach
                            @endif

                            <tr class="bg-slate-50 border-b border-slate-200 font-bold text-slate-800">
                                <td class="p-3 border-r border-slate-200 whitespace-nowrap">Actual ({{ $kpi->unit }})</td>
                                @php
                                    $actualSum = 0;
                                    $hasActual = false;
                                    $actualCount = 0;
                                @endphp
                                @foreach(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] as $m)
                                    @php
                                        $act = $activities->firstWhere('bulan', $m);
                                        $valVal = $calculatedActuals[$m];
                                        if ($valVal !== null) {
                                            $val = $parseLocalNum($valVal);
                                            $actualSum += $val;
                                            $hasActual = true;
                                            $actualCount++;
                                        }
                                    @endphp
                                     <td class="p-3 text-center border-r border-slate-200">
                                         @if($act)
                                             @php
                                                 $hasInput = ($valVal !== null && $valVal !== '');
                                                 $editRoute = route('kpi.company.activity.edit', $act->hash_id);
                                                 $deleteRoute = route('kpi.company.activity.cancel', $act->hash_id);
                                             @endphp
                                             @if($hasInput)
                                                 <button type="button" 
                                                     onclick="openActionModal('{{ $act->bulan }}', '{{ $editRoute }}', '{{ $deleteRoute }}')"
                                                     class="inline-block text-slate-700 hover:text-slate-900 font-bold transition-colors" 
                                                     title="Actual {{ $m }}">
                                                     {{ $formatNumWithUnit($valVal) }}
                                                 </button>
                                             @else
                                                 <a href="{{ $editRoute }}" class="inline-block w-full h-full min-h-[24px] cursor-pointer hover:bg-slate-100/50 transition-colors" title="Input Actual {{ $m }}">
                                                     &nbsp;
                                                 </a>
                                             @endif
                                         @else
                                             {{ $valVal !== null ? $formatNumWithUnit($valVal) : (empty($components) ? '-' : '') }}
                                         @endif
                                     </td>
                                @endforeach
                                @php
                                    $actualFinal = $hasActual ? (($kpi->result === 'Average') ? ($actualCount > 0 ? ($actualSum / $actualCount) : 0) : $actualSum) : '-';
                                @endphp
                                <td class="p-3 text-center">
                                    {{ $actualFinal !== '-' ? $formatNumWithUnit($actualFinal) : '-' }}
                                </td>
                            </tr>
                            <tr class="border-b border-slate-200">
                                 <td class="p-3 font-semibold border-r border-slate-200 whitespace-nowrap">Status</td>
                                 @foreach(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] as $m)
                                     @php
                                         $act = $activities->firstWhere('bulan', $m);
                                         $valVal = $calculatedActuals[$m];
                                         $hasActVal = ($act && $valVal !== null && $valVal !== '');
                                     @endphp
                                     @if($hasActVal)
                                         <td class="p-3 text-center border-r border-slate-200">
                                             @php
                                                 $targetVal = $parseLocalNum($kpi->target);
                                                 $actValNum = $parseLocalNum($valVal);
                                                 $op = trim(htmlspecialchars_decode($kpi->operator));
                                                 $isAchieved = false;
                                                 switch ($op) {
                                                     case '>=': $isAchieved = ($actValNum >= $targetVal); break;
                                                     case '<=': $isAchieved = ($actValNum <= $targetVal); break;
                                                     case '>':  $isAchieved = ($actValNum > $targetVal); break;
                                                     case '<':  $isAchieved = ($actValNum < $targetVal); break;
                                                     case '=':
                                                     default:   $isAchieved = ($actValNum == $targetVal); break;
                                                 }
                                             @endphp
                                             @if($isAchieved)
                                                 <div class="inline-flex w-7 h-7 rounded-full items-center justify-center bg-green-100 text-green-600" title="Achieved">
                                                     <i class="fas fa-circle text-[12px]"></i>
                                                 </div>
                                             @else
                                                 <div class="inline-flex w-7 h-7 rounded-full items-center justify-center bg-red-100 text-red-600" title="Not Achieved">
                                                     <i class="fas fa-circle text-[12px]"></i>
                                                 </div>
                                             @endif
                                         </td>
                                     @else
                                         <td class="p-3 text-center text-slate-400 border-r border-slate-200"></td>
                                     @endif
                                 @endforeach
                                 <td class="p-3 text-center"></td>
                             </tr>
                        </tbody>
                    </table>
                </div>


            </div>
            </div>


        </div>
    </main>
    @include('layouts.footer')
</div>

<!-- Action Options Modal (Edit or Clear/Delete) -->
<div id="actionModal" class="fixed inset-0 z-[1000] hidden">
    <div class="fixed inset-0 bg-slate-900/50 transition-opacity" onclick="closeActionModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-md transform transition-all shadow-2xl">
            <div class="p-8 text-center">
                <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-5">
                    <svg class="w-10 h-10 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-3" id="actionModalTitle">Data Actual</h3>
                <p class="text-base text-slate-500 mb-8">Pilih aksi untuk data bulan ini. Anda dapat mengedit data atau menghapus/meresetnya.</p>
                <div class="flex gap-3 justify-center">
                    <button type="button" onclick="closeActionModal()" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-xl transition-colors text-base min-w-[90px]">Cancel</button>
                    <a id="actionModalEditBtn" href="#" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition-colors text-base inline-flex items-center justify-center min-w-[100px]">Edit</a>
                    <button type="button" onclick="triggerDeleteFromActionModal()" class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-xl transition-colors text-base min-w-[100px]">Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-[1000] hidden">
    <div class="fixed inset-0 bg-slate-900/50 transition-opacity" onclick="closeDeleteModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl w-full max-w-sm transform transition-all shadow-xl">
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-triangle-exclamation text-2xl text-red-600"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">Confirm Delete</h3>
                <p class="text-slate-500 text-sm">Apakah Anda yakin ingin menghapus/mereset data actual dan status bulan ini? Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="p-6 pt-0 flex gap-3">
                <button type="button" onclick="closeDeleteModal()" class="flex-1 px-4 py-2 bg-slate-100 text-slate-700 font-medium rounded-lg hover:bg-slate-200 transition-colors">Cancel</button>
                <button type="button" onclick="executeDelete()" class="flex-1 px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Problem Solving Preview Modal -->
<div id="problemPreviewModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-slate-900/50 transition-opacity" onclick="closeProblemPreview()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl w-full max-w-2xl shadow-2xl my-4">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-6 border-b border-slate-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-clipboard-list text-blue-600"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Problem Solving Detail</h3>
                </div>
                <button onclick="closeProblemPreview()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-500 transition-colors">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <!-- Modal Body -->
            <div class="p-6 space-y-5 max-h-[70vh] overflow-y-auto">
                <!-- Problem Description -->
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Problem Description</p>
                    <p id="prev_problem_description" class="text-sm text-slate-700 bg-slate-50 rounded-lg p-3">-</p>
                </div>
                <!-- Root Cause -->
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Root Cause</p>
                    <p id="prev_root_cause" class="text-sm text-slate-700 bg-slate-50 rounded-lg p-3">-</p>
                </div>
                <!-- 4M1E -->
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-2">Root Cause Factor (4M1E)</p>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="bg-slate-50 rounded-lg p-3">
                            <p class="text-xs text-slate-400 mb-0.5">Machine</p>
                            <p id="prev_machine" class="text-sm font-medium text-slate-700">-</p>
                        </div>
                        <div class="bg-slate-50 rounded-lg p-3">
                            <p class="text-xs text-slate-400 mb-0.5">Material</p>
                            <p id="prev_material" class="text-sm font-medium text-slate-700">-</p>
                        </div>
                        <div class="bg-slate-50 rounded-lg p-3">
                            <p class="text-xs text-slate-400 mb-0.5">Man</p>
                            <p id="prev_man" class="text-sm font-medium text-slate-700">-</p>
                        </div>
                        <div class="bg-slate-50 rounded-lg p-3">
                            <p class="text-xs text-slate-400 mb-0.5">Method</p>
                            <p id="prev_method" class="text-sm font-medium text-slate-700">-</p>
                        </div>
                        <div class="bg-slate-50 rounded-lg p-3">
                            <p class="text-xs text-slate-400 mb-0.5">Money</p>
                            <p id="prev_money" class="text-sm font-medium text-slate-700">-</p>
                        </div>
                        <div class="bg-slate-50 rounded-lg p-3">
                            <p class="text-xs text-slate-400 mb-0.5">Environment</p>
                            <p id="prev_environment" class="text-sm font-medium text-slate-700">-</p>
                        </div>
                    </div>
                </div>
                <!-- Actions -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Temporary Action</p>
                        <p id="prev_temporary_action" class="text-sm text-slate-700 bg-slate-50 rounded-lg p-3">-</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Permanent Action</p>
                        <p id="prev_permanent_action" class="text-sm text-slate-700 bg-slate-50 rounded-lg p-3">-</p>
                    </div>
                </div>
                <!-- Timeline & PIC -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div class="bg-slate-50 rounded-lg p-3">
                        <p class="text-xs text-slate-400 mb-0.5">Start Date</p>
                        <p id="prev_start_date" class="text-sm font-medium text-slate-700">-</p>
                    </div>
                    <div class="bg-slate-50 rounded-lg p-3">
                        <p class="text-xs text-slate-400 mb-0.5">Finish Date</p>
                        <p id="prev_finish_date" class="text-sm font-medium text-slate-700">-</p>
                    </div>
                    <div class="bg-slate-50 rounded-lg p-3">
                        <p class="text-xs text-slate-400 mb-0.5">PIC Dept</p>
                        <p id="prev_pic_dept" class="text-sm font-medium text-slate-700">-</p>
                    </div>
                    <div class="bg-slate-50 rounded-lg p-3">
                        <p class="text-xs text-slate-400 mb-0.5">Follow Up By</p>
                        <p id="prev_follow_up_by" class="text-sm font-medium text-slate-700">-</p>
                    </div>
                </div>
                <!-- Closed Status -->
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Closed Status</p>
                    <p id="prev_closed_status" class="text-sm font-medium text-slate-700">-</p>
                </div>
            </div>
            <!-- Modal Footer -->
            <div class="p-6 pt-0 border-t border-slate-100 mt-2">
                <button onclick="closeProblemPreview()" class="w-full px-4 py-2 bg-slate-100 text-slate-700 font-medium rounded-lg hover:bg-slate-200 transition-colors">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentChartPage = 1;
    let chartPageSize = 12;

    function updatePageSize() {
        const width = window.innerWidth;
        if (width < 640) {
            chartPageSize = 6;
        } else {
            chartPageSize = 12;
            currentChartPage = 1;
        }
    }

    function renderKPIChart() {
        const canvas = document.getElementById('kpiPerformanceChart');
        if (!canvas) return;
        
        if (typeof window.Chart === 'undefined') {
            setTimeout(renderKPIChart, 50);
            return;
        }
        
        try {
            updatePageSize();
            const ctx = canvas.getContext('2d');
            const fullActualData = JSON.parse(canvas.dataset.actual || '[]');
            const targetVal = parseFloat(canvas.dataset.target) || 0;
            const operator = canvas.dataset.operator || '';
            const arrowTarget = canvas.dataset.arrowTarget || '';
            const isUp = arrowTarget !== 'down';

            // Decode operator for accurate JS evaluation
            const decodedOperator = (operator === '&gt;=' || operator === '>=') ? '>=' :
                                    (operator === '&lt;=' || operator === '<=') ? '<=' :
                                    (operator === '&gt;' || operator === '>') ? '>' :
                                    (operator === '&lt;' || operator === '<') ? '<' : '=';

            const targetColor = isUp ? '#475569' : ((decodedOperator === '<=' || decodedOperator === '<') ? '#FF4560' : '#22c55e'); // target line is slate for upper limits, green otherwise

            const fullMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const fullTargetData = new Array(12).fill(targetVal);

            const totalItems = 12;
            const totalPages = Math.ceil(totalItems / chartPageSize) || 1;
            
            if (chartPageSize < 12) {
                $('#chartPagination').removeClass('hidden').addClass('flex');
                $('#chartPageIndicator').text(`${currentChartPage}/${totalPages}`);
                $('#btnChartPrev').prop('disabled', currentChartPage === 1);
                $('#btnChartNext').prop('disabled', currentChartPage === totalPages);
            } else {
                $('#chartPagination').removeClass('flex').addClass('hidden');
            }

            const startIndex = (currentChartPage - 1) * chartPageSize;
            const endIndex = startIndex + chartPageSize;

            const months = fullMonths.slice(startIndex, endIndex);
            const actualData = fullActualData.slice(startIndex, endIndex);
            const targetData = fullTargetData.slice(startIndex, endIndex);

            if (window.kpiChart && typeof window.kpiChart.destroy === 'function') {
                window.kpiChart.destroy();
            }

            window.kpiChart = new window.Chart(ctx, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [
                        {
                            label: 'Actual',
                            data: actualData,
                            backgroundColor: function(context) {
                                if (context.dataIndex === undefined) {
                                    return '#3b82f6'; // Default blue color for legend
                                }
                                const val = actualData[context.dataIndex];
                                if (val === null || val === undefined) return 'rgba(0, 0, 0, 0)';
                                
                                let isAchieved = false;
                                if (decodedOperator === '>=') {
                                    isAchieved = (val >= targetVal);
                                } else if (decodedOperator === '<=') {
                                    isAchieved = (val <= targetVal);
                                } else if (decodedOperator === '>') {
                                    isAchieved = (val > targetVal);
                                } else if (decodedOperator === '<') {
                                    isAchieved = (val < targetVal);
                                } else {
                                    isAchieved = (val == targetVal);
                                }
                                
                                return isAchieved ? '#22c55e' : '#FF4560';
                            },
                            borderColor: function(context) {
                                if (context.dataIndex === undefined) {
                                    return '#3b82f6';
                                }
                                const val = actualData[context.dataIndex];
                                if (val === null || val === undefined) return 'rgba(0, 0, 0, 0)';
                                
                                let isAchieved = false;
                                if (decodedOperator === '>=') {
                                    isAchieved = (val >= targetVal);
                                } else if (decodedOperator === '<=') {
                                    isAchieved = (val <= targetVal);
                                } else if (decodedOperator === '>') {
                                    isAchieved = (val > targetVal);
                                } else if (decodedOperator === '<') {
                                    isAchieved = (val < targetVal);
                                } else {
                                    isAchieved = (val == targetVal);
                                }
                                
                                return isAchieved ? '#22c55e' : '#FF4560';
                            },
                            borderWidth: 1,
                        },
                        {
                            type: 'line',
                            label: 'Target',
                            data: targetData,
                            borderColor: targetColor,
                            borderDash: [5, 5],
                            borderWidth: 1.5,
                            pointStyle: 'circle',
                            pointRadius: 6,
                            pointHoverRadius: 8,
                            pointBackgroundColor: targetColor,
                            fill: false
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animations: {
                        y: {
                            duration: 1000,
                            easing: 'easeInQuad',
                            from: (context) => {
                                if (context.type === 'data' && context.mode === 'default') {
                                    const scale = context.chart.scales.y;
                                    if (scale) return scale.getPixelForValue(0);
                                }
                                return undefined;
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                padding: 20,
                                font: {
                                    family: 'Outfit, sans-serif',
                                    size: 12
                                },
                                color: '#475569',
                                generateLabels: function(chart) {
                                    const datasets = chart.data.datasets;
                                    return datasets.map((dataset, i) => {
                                        let color = dataset.borderColor;
                                        if (dataset.label === 'Actual') {
                                            color = '#22c55e'; // Default green
                                            for (let idx = 0; idx < actualData.length; idx++) {
                                                const val = actualData[idx];
                                                if (val !== null && val !== undefined) {
                                                    let isAchieved = false;
                                                    if (decodedOperator === '>=') {
                                                        isAchieved = (val >= targetVal);
                                                    } else if (decodedOperator === '<=') {
                                                        isAchieved = (val <= targetVal);
                                                    } else if (decodedOperator === '>') {
                                                        isAchieved = (val > targetVal);
                                                    } else if (decodedOperator === '<') {
                                                        isAchieved = (val < targetVal);
                                                    } else {
                                                        isAchieved = (val == targetVal);
                                                    }
                                                    color = isAchieved ? '#22c55e' : '#FF4560';
                                                    break;
                                                }
                                            }
                                        }
                                        return {
                                            text: dataset.label,
                                            fillStyle: color,
                                            strokeStyle: color,
                                            lineWidth: dataset.borderWidth || 1,
                                            hidden: !chart.isDatasetVisible(i),
                                            index: i
                                        };
                                    });
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            grid: {
                                color: '#f1f5f9'
                            },
                            ticks: {
                                maxTicksLimit: 4,
                                font: {
                                    family: 'Outfit, sans-serif',
                                    size: 11
                                }
                            },
                            suggestedMax: (() => {
                                const validVals = fullActualData.filter(v => v !== null && v !== undefined);
                                const maxVal = validVals.length > 0 ? Math.max(...validVals, targetVal) : targetVal;
                                return maxVal + (maxVal * 0.15 || 1);
                            })()
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    family: 'Outfit, sans-serif',
                                    size: 13
                                }
                            }
                        }
                    }
                },
                plugins: []
            });
        } catch (e) {
            console.error("Error rendering chart:", e);
        }
    }

    $(document).ready(function() {
        renderKPIChart();

        // Pagination button handlers
        $('#btnChartPrev').on('click', function() {
            if (currentChartPage > 1) {
                currentChartPage--;
                renderKPIChart();
            }
        });

        $('#btnChartNext').on('click', function() {
            const totalPages = Math.ceil(12 / chartPageSize) || 1;
            if (currentChartPage < totalPages) {
                currentChartPage++;
                renderKPIChart();
            }
        });

        // Resize handler
        $(window).on('resize.kpi', function() {
            const oldPageSize = chartPageSize;
            updatePageSize();
            if (oldPageSize !== chartPageSize) {
                renderKPIChart();
            }
        });
        
        // Scroll to activity section if URL has #activity-section anchor
        if (window.location.hash === '#activity-section') {
            const el = document.getElementById('activity-section');
            if (el) {
                setTimeout(function() {
                    el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 200);
            }
        }
    });

    // Execute immediately in case DOMContentLoaded or document ready has already fired
    renderKPIChart();
    let deleteUrl = null;
    let deleteCallback = null;
    let currentPendingDeleteUrl = null;

    function openActionModal(month, editUrl, deleteUrlTarget) {
        document.getElementById('actionModalTitle').textContent = 'Pilih Aksi Bulan ' + month;
        document.getElementById('actionModalEditBtn').setAttribute('href', editUrl);
        currentPendingDeleteUrl = deleteUrlTarget;
        document.getElementById('actionModal').classList.remove('hidden');
    }

    function closeActionModal() {
        currentPendingDeleteUrl = null;
        document.getElementById('actionModal').classList.add('hidden');
    }

    function triggerDeleteFromActionModal() {
        const routeUrl = currentPendingDeleteUrl;
        closeActionModal();
        openDeleteModal({ dataset: { route: routeUrl } });
    }

    function openDeleteModal(el, callback = null, customText = null) {
        if (customText) {
            document.querySelector('#deleteModal p').textContent = customText;
        } else {
            document.querySelector('#deleteModal p').textContent = 'Apakah Anda yakin ingin menghapus/mereset data actual dan status bulan ini? Tindakan ini tidak dapat dibatalkan.';
        }

        if (callback) {
            deleteCallback = callback;
            deleteUrl = null;
        } else {
            deleteUrl = el.dataset.route;
            deleteCallback = null;
        }
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        deleteUrl = null;
        deleteCallback = null;
        document.getElementById('deleteModal').classList.add('hidden');
    }

    function executeDelete() {
        if (deleteUrl) {
            window.location.href = deleteUrl;
        } else if (deleteCallback) {
            deleteCallback();
            closeDeleteModal();
        }
    }

    function openProblemPreview(problem) {
        const fields = ['problem_description', 'root_cause', 'machine', 'material', 'man', 'method', 'money', 'environment', 'temporary_action', 'permanent_action', 'start_date', 'finish_date', 'pic_dept', 'follow_up_by', 'closed_status'];
        fields.forEach(function(f) {
            const el = document.getElementById('prev_' + f);
            if (el) el.textContent = problem[f] || '-';
        });
        document.getElementById('problemPreviewModal').classList.remove('hidden');
    }

    function closeProblemPreview() {
        document.getElementById('problemPreviewModal').classList.add('hidden');
    }

</script>
@endpush
@endsection
