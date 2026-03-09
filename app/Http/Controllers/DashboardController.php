<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\Models\GenbaManagement;
use Carbon\Carbon;


class DashboardController extends Controller
{
    public function data_cards(Request $request)
    {
        $yearMonth = $request->input('yearMonth', date('Y-m'));
        [$year, $month] = explode('-', $yearMonth);
        $year = (int) $year;
        $month = (int) $month;
        $endOfMonth = Carbon::createFromDate($year, $month, 1)->endOfMonth()->format('Y-m-d 23:59:59');

        // 1. Findings Open: evidence IS NULL AND status IS NULL, IsDelete = 0
        $findingsOpen = DB::connection('sqlsrv')->table('GenbaProcAuditDtl as a')
            ->leftJoin('GenbaProcAudit as b', 'b.SysID', '=', 'a.genba_id')
            ->where('b.IsDelete', 0)
            ->whereNotNull('a.asign_to_dept')
            ->whereNotNull('a.findings')
            ->where(function ($q) {
                $q->whereNull('a.evidence')->orWhere('a.evidence', '0');
            })
            ->where(function ($q) {
                $q->whereNull('a.corrective_action')->orWhere('a.corrective_action', '0');
            })
            ->where(function ($q) {
                $q->where('a.result', '!=', 1)
                    ->orWhereNull('a.result');
            })
            ->whereDate('a.due_date', '>=', today())
            ->where('a.created_at', '<=', $endOfMonth)
            ->count();

        // 2. Need Approve: evidence = '1' AND status = '1' AND verification_result IS NULL, IsDelete = 0
        $needApprove = DB::connection('sqlsrv')->table('GenbaProcAuditDtl as a')
            ->leftJoin('GenbaProcAudit as b', 'b.SysID', '=', 'a.genba_id')
            ->where('b.IsDelete', 0)
            ->whereNotNull('a.findings')
            ->where('a.evidence', '1')
            ->where('a.corrective_action', '1')
            ->where(function ($q) {
                $q->whereNull('a.verification_result')->orWhere('a.verification_result', '0');
            })
            ->where(function ($q) {
                $q->where('a.result', '!=', 1)
                    ->orWhereNull('a.result');
            })
            ->where('a.created_at', '<=', $endOfMonth)
            ->count();

        // 3. Due Date (Overdue): due_date < today AND (evidence is null/0 OR corrective_action is null/0)
        $dueDateCount = DB::connection('sqlsrv')->table('GenbaProcAuditDtl as a')
            ->leftJoin('GenbaProcAudit as b', 'b.SysID', '=', 'a.genba_id')
            ->where('b.IsDelete', 0)
            ->whereNotNull('a.findings')
            ->whereDate('a.due_date', '<', today())
            ->where(function ($q) {
                $q->where(function ($sub) {
                    $sub->whereNull('a.evidence')
                        ->orWhere('a.evidence', 0);
                })
                    ->where(function ($sub) {
                        $sub->whereNull('a.corrective_action')
                            ->orWhere('a.corrective_action', 0);
                    });
            })
            ->where(function ($q) {
                $q->where('a.result', '!=', 1)
                    ->orWhereNull('a.result');
            })
            ->where('a.created_at', '<=', $endOfMonth)
            ->count();

        // 4. Closed: evidence='1' AND corrective_action='1' AND verification_result='1'
        $findingsClose = DB::connection('sqlsrv')->table('GenbaProcAuditDtl as a')
            ->leftJoin('GenbaProcAudit as b', 'b.SysID', '=', 'a.genba_id')
            ->where('b.IsDelete', 0)
            ->whereNotNull('a.findings')
            ->where('a.evidence', '1')
            ->where('a.corrective_action', '1')
            ->where('a.verification_result', '1')
            ->where(function ($q) {
                $q->where('a.result', '!=', 1)
                    ->orWhereNull('a.result');
            })
            ->whereYear('a.created_at', $year)
            ->whereMonth('a.created_at', $month)
            ->count();

        // 5. All Findings: findings is not null, IsDelete = 0
        $allFindings = DB::connection('sqlsrv')->table('GenbaProcAuditDtl as a')
            ->leftJoin('GenbaProcAudit as b', 'b.SysID', '=', 'a.genba_id')
            ->where('b.IsDelete', 0)
            ->whereNotNull('a.findings')
            ->where('a.created_at', '<=', $endOfMonth)
            ->count();

        return response()->json([
            'findingsOpen' => $findingsOpen,
            'needApprove' => $needApprove,
            'dueDateCount' => $dueDateCount,
            'findingsClose' => $findingsClose,
            'allFindings' => $allFindings
        ]);
    }
    public function table(Request $request)
    {
        $search = $request->front_table_search;
        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $auditor = $request->auditor;
        $dept = $request->dept;
        $columns = array(
            0 => 'a.SysID',
            1 => 'DocNum',
            2 => 'a.Path',
            3 => 'b.Date',
            4 => 'b.Area_Checked',
            5 => 'a.findings',
            6 => 'b.Auditor',
            7 => 'a.status',
            8 => 'a.SysID'
        );

        $totalData = GenbaManagement::get_genba_mng_activity_list($search, $date_from, $date_to, $auditor, $dept)->count();
        $totalFiltered = $totalData;
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = ($request->input('order.0.column') == 0 ? $columns[0] : $columns[$request->input('order.0.column')]);
        $dir = ($request->input('order.0.column') == 0 ? 'desc' : $request->input('order.0.dir'));

        if (empty($search)) {
            $posts = GenbaManagement::get_genba_mng_activity_list($search, $date_from, $date_to, $auditor, $dept)
                ->offset($start)
                ->limit($limit)
                ->reorder($order, $dir)
                ->get();
        } else {
            $posts = GenbaManagement::get_genba_mng_activity_list($search, $date_from, $date_to, $auditor, $dept)
                ->offset($start)
                ->limit($limit)
                ->reorder($order, $dir)
                ->get();
            $totalFiltered = GenbaManagement::get_genba_mng_activity_list($search, $date_from, $date_to, $auditor, $dept)->count();
        }

        $data = array();
        if (!empty($posts)) {
            $no = $start;
            foreach ($posts as $post) {
                $no++;
                $trc_id = Crypt::encryptString($post->SysID);
                $sys_id = "'" . str_replace("=", "-", $trc_id) . '_' . $no . "'";

                $verification_result = $post->verification_result;

                // Action Button Logic
                if ($verification_result != '' && $verification_result != null) {
                    // CLOSED -> Show Photo Icon for Before/After View
                    $findingsEnc = rawurlencode($post->findings); // URL encode to safely pass to JS
                    $commentEnc = rawurlencode($post->execution_comment);
                    $pathBefore = $post->Path;
                    $pathAfter = $post->execution_path;

                    $button = '<div class="flex items-center justify-center gap-2">
                                <button type="button" title="View Photos" class="w-10 h-10 flex items-center justify-center rounded-xl border border-blue-200 bg-blue-50 text-blue-500 hover:bg-blue-100 hover:text-blue-600 transition-all duration-200" 
                                    onclick="viewGenbaImages(\'' . $pathBefore . '\', \'' . $pathAfter . '\', \'' . $findingsEnc . '\', \'' . $commentEnc . '\')">
                                    <span class="flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                            <polyline points="21 15 16 10 5 21"></polyline>
                                        </svg>
                                    </span>
                                </button>
                           </div>';
                } else {
                    // NOT CLOSED -> Show Link/Preview Icon (Existing Logic)
                    $button = '<div class="flex items-center justify-center gap-2">
                                <button type="button" title="Preview" class="w-10 h-10 flex items-center justify-center rounded-xl border border-blue-200 bg-blue-50 text-blue-500 hover:bg-blue-100 hover:text-blue-600 transition-all duration-200" id="btn_form_view_doc_' . $no . '" onclick="document_preview(' . $sys_id . ',' . $no . ')">
                                    <span id="svg_form_view_doc_' . $no . '" class="flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                                            <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
                                        </svg>
                                    </span>
                                    <span id="spinner_form_view_doc_' . $no . '" class="hidden animate-spin rounded-full h-4 w-4 border-b-2 border-current"></span>
                                </button>
                           </div>';
                }

                $date = Carbon::parse($post->Date)->format('d M Y');
                $corrective_action = $post->corrective_action;
                $execution_comment = $post->execution_comment;
                $verification_result = $post->verification_result;
                $execution_path = $post->execution_path;

                // Stepper Logic
                $line = '<div class="w-8 h-0.5 bg-gray-200"></div>';
                $activeLine = '<div class="w-8 h-0.5 bg-blue-200"></div>';

                // Helper for progress circles
                $renderCircle = function ($isActive) {
                    return $isActive
                        ? '<div class="w-10 h-10 rounded-full bg-blue-50 border border-blue-200 flex items-center justify-center text-blue-500 shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                           </div>'
                        : '<div class="w-10 h-10 rounded-full border border-slate-200 bg-white shadow-sm"></div>';
                };

                if ($execution_comment == '' || $execution_comment == null) {
                    // Need Action Plan
                    $steps = $renderCircle(false) . $line . $renderCircle(false) . $line . $renderCircle(false);
                } else if ($execution_path == '' || $execution_path == null) {
                    // Need Evidence
                    $steps = $renderCircle(true) . $line . $renderCircle(false) . $line . $renderCircle(false);
                } else if ($verification_result == '' || $verification_result == null) {
                    // Process Verification
                    $steps = $renderCircle(true) . $activeLine . $renderCircle(true) . $line . $renderCircle(false);
                } else {
                    // Closed
                    $steps = $renderCircle(true) . $activeLine . $renderCircle(true) . $activeLine . $renderCircle(true);
                }

                $status = '<div class="flex items-center justify-center gap-0 py-1">' . $steps . '</div>';

                $nestedData['no'] = $no;
                $nestedData['DocNum'] = $post->DocNum;
                $nestedData['date'] = $date;

                $nestedData['area_checked'] = $post->Area_Checked;
                $nestedData['findings'] = $post->findings;
                $nestedData['path'] = $post->Path;
                $nestedData['dept'] = $post->asign_to_dept;
                $nestedData['due_date'] = $post->due_date;
                $nestedData['execution_comment'] = $post->execution_comment;
                $nestedData['execution_path'] = $post->execution_path;
                $nestedData['status'] = $status;
                $nestedData['action'] = $button;
                $nestedData['auditor'] = $post->Auditor;
                $data[] = $nestedData;
            }
        } else {

            $nestedData['no'] = '';
            $nestedData['area_checked'] = '';
            $nestedData['path'] = '';
            $nestedData['date'] = '';
            $nestedData['status'] = '';
            $nestedData['action'] = '';
            $data[] = $nestedData;
        }
        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );
        echo json_encode($json_data);
    }

    public function chart_all_dept($yearMonth)
    {
        [$year, $month] = explode('-', $yearMonth);
        $year = (int) $year;
        $month = (int) $month;
        $endOfMonth = \Carbon\Carbon::createFromDate($year, $month, 1)->endOfMonth()->format('Y-m-d 23:59:59');

        $departments = [
            'PUR',
            'HRGA',
            'TMF',
            'SLS',
            'FA',
            'NPC',
            'PPIC',
            'DPC',
            'ICT',
            'STP',
            'ASSY',
            'MTC'
        ];

        $deptFromDb = DB::connection('sqlsrv')
            ->table('GenbaProcAuditDtl as g')
            ->join('GenbaProcAudit as b', 'g.genba_id', '=', 'b.SysID')
            ->distinct()
            ->whereNotNull('g.asign_to_dept')
            ->where(function ($q) {
                $q->where('b.IsDelete', '!=', 1)
                    ->orWhereNull('b.IsDelete');
            })
            ->pluck('g.asign_to_dept')
            ->toArray();

        $allDepartments = array_unique(array_merge($departments, $deptFromDb));


        $closedResults = DB::connection('sqlsrv')
            ->table('GenbaProcAuditDtl as g')
            ->join('GenbaProcAudit as b', 'g.genba_id', '=', 'b.SysID')
            ->select(
                'g.asign_to_dept',
                DB::raw("SUM(CASE WHEN (g.verification_result = '1' OR g.verification_result = 1) THEN 1 ELSE 0 END) AS TotalClose")
            )
            ->where(function ($q) {
                $q->where('b.IsDelete', '!=', 1)
                    ->orWhereNull('b.IsDelete');
            })
            ->where(function ($q) {
                $q->where('g.result', '!=', 1)
                    ->orWhereNull('g.result');
            })
            // Filter Waktu AKTIF disini
            ->whereYear('g.created_at', $year)
            ->whereMonth('g.created_at', $month)
            ->whereNotNull('g.asign_to_dept')
            ->groupBy('g.asign_to_dept')
            ->get()
            ->keyBy('asign_to_dept');

        // 4. Query Khusus OPEN & OVERDUE (TANPA Filter Bulan/Tahun)
        $openOverdueResults = DB::connection('sqlsrv')
            ->table('GenbaProcAuditDtl as g')
            ->join('GenbaProcAudit as b', 'g.genba_id', '=', 'b.SysID')
            ->select(
                'g.asign_to_dept',
                // Logic OPEN: Belum fix, Belum verify, Due Date masih aman
                DB::raw("
            SUM(CASE WHEN g.corrective_action IS NULL AND g.evidence IS NULL
                     AND CAST(g.due_date AS DATE) >= CAST(GETDATE() AS DATE)
                     THEN 1 ELSE 0 END) AS TotalOpen
            "),
                // Logic OVERDUE: Belum fix, Belum verify, Due Date sudah lewat
                DB::raw("
            SUM(CASE WHEN g.corrective_action IS NULL AND g.evidence IS NULL AND g.verification_result IS NULL
                     AND CAST(g.due_date AS DATE) < CAST(GETDATE() AS DATE)
                     THEN 1 ELSE 0 END) AS TotalOverdue
            "),
                // Logic NEED APPROVE: Sudah fix (evidence & action), Belum verify
                DB::raw("
            SUM(CASE WHEN (g.corrective_action = '1' OR g.corrective_action = 1) 
                     AND (g.evidence = '1' OR g.evidence = 1) 
                     AND (g.verification_result IS NULL OR g.verification_result = '0' OR g.verification_result = 0)
                     THEN 1 ELSE 0 END) AS TotalNeedApprove
            ")
            )
            ->where(function ($q) {
                $q->where('b.IsDelete', '!=', 1)
                    ->orWhereNull('b.IsDelete');
            })
            ->where(function ($q) {
                $q->where('g.result', '!=', 1)
                    ->orWhereNull('g.result');
            })
            ->where('g.created_at', '<=', $endOfMonth)
            ->whereNotNull('g.asign_to_dept')
            ->groupBy('g.asign_to_dept')
            ->get()
            ->keyBy('asign_to_dept');

        $data = [];
        foreach ($allDepartments as $dept) {
            $close = $closedResults[$dept]->TotalClose ?? 0;

            $open = $openOverdueResults[$dept]->TotalOpen ?? 0;
            $overdue = $openOverdueResults[$dept]->TotalOverdue ?? 0;
            $needApprove = $openOverdueResults[$dept]->TotalNeedApprove ?? 0;

            $deptName = $dept;
            if ($deptName === 'TS') $deptName = 'Mtc';

            $data[] = [
                'name' => $deptName,
                'open' => (int) $open,
                'close' => (int) $close,
                'overdue' => (int) $overdue,
                'need_approve' => (int) $needApprove,
            ];
        }

        // 6. Sorting (Berdasarkan Close terbanyak, opsional)
        usort($data, function ($a, $b) {
            return $b['close'] <=> $a['close'];
        });

        return response()->json([
            'data_total_open' => array_column($data, 'open'),
            'data_total_close' => array_column($data, 'close'),
            'data_total_overdue' => array_column($data, 'overdue'),
            'data_total_need_approve' => array_column($data, 'need_approve'),
            'data_name_dept' => array_column($data, 'name'),
        ]);
    }
}
