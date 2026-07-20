<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use App\Models\GenbaManagement;
use Carbon\Carbon;


use App\Models\UserMenuPermission;
use App\Models\Menu;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::check()) {
                return response()->view('direct_403.direct_403');
            }
            
            $menuId = 101; // Genba Management Dashboard
            if ($request->is('*dashboard-biq*')) {
                $menuId = 102;
            } elseif ($request->is('*dashboard-safety*')) {
                $menuId = 106;
            } elseif ($request->is('*dashboard-internal-audit*')) {
                $menuId = 113;
            }
            
            if (!UserMenuPermission::canView($menuId)) {
                // If they land on home '/' or '/dashboard-mng' but don't have dashboard permission,
                // redirect to the first menu that they DO have permission to view.
                if ($request->is('/') || $request->is('*dashboard-mng*')) {
                    $firstMenu = $this->getFirstPermittedMenu(Auth::user()->id);
                    if ($firstMenu) {
                        return redirect($firstMenu->menu);
                    }
                }
                return response()->view('direct_403.direct_403');
            }
            
            return $next($request);
        });
    }

    private function getFirstPermittedMenu($userId)
    {
        $orderedIds = Menu::getOrderedIds();
        
        $permissions = DB::table('t100_user_menus_permission')
            ->where('id_user', $userId)
            ->where('is_view', 1)
            ->pluck('id_menus')
            ->toArray();
            
        foreach ($orderedIds as $menuId) {
            if (in_array($menuId, $permissions)) {
                $menu = DB::table('t100_menus')->where('id', $menuId)->first();
                if ($menu && !empty($menu->menu)) {
                    return $menu;
                }
            }
        }
        
        return null;
    }

    public function index()
    {
        return view('dashboard.genba_mng');
    }

    public function internal_audit_index()
    {
        return view('dashboard.internal_audit');
    }

    public function data_cards(Request $request, $category_id = 'NOT_BIQ')
    {
        $yearMonth = $request->input('yearMonth', date('Y-m'));
        [$year, $month] = explode('-', $yearMonth);
        $year = (int) $year;
        $month = (int) $month;
        $endOfMonth = Carbon::createFromDate($year, $month, 1)->endOfMonth()->format('Y-m-d 23:59:59');

        $categoryFilter = function ($q) use ($category_id) {
            if ($category_id === 'NOT_BIQ') {
                $q->where(function($sub) {
                    $sub->whereNotIn('b.Category_id', [7, 8, 9, 10])->orWhereNull('b.Category_id');
                });
            } else {
                if (is_array($category_id)) {
                    $q->whereIn('b.Category_id', $category_id);
                } else {
                    $q->where('b.Category_id', $category_id);
                }
            }
        };

        // 1. Findings Open: evidence IS NULL AND status IS NULL, IsDelete = 0
        $findingsOpen = DB::connection('sqlsrv')->table('GenbaProcAuditDtl as a')
            ->join('GenbaProcAudit as b', 'b.SysID', '=', 'a.genba_id')
            ->whereNotNull('b.Auditor')
            ->where('b.Auditor', '!=', '')
            ->where(function ($q) {
                $q->where('b.IsDelete', 0)
                    ->orWhereNull('b.IsDelete');
            })
            ->where($categoryFilter)
            ->whereNotNull('a.asign_to_dept')
            ->whereNotNull('a.findings')
            ->where(function ($q) {
                $q->whereNull('a.evidence')->orWhere('a.evidence', '0')
                    ->orWhereNull('a.corrective_action')->orWhere('a.corrective_action', '0');
            })
            ->where(function ($q) {
                $q->where('a.result', '!=', 1)
                    ->orWhereNull('a.result');
            })
            ->whereDate('a.due_date', '>=', today())
            ->where('b.Date', '<=', $endOfMonth)
            ->count();

        // 2. Need Approve: evidence = '1' AND status = '1' AND verification_result IS NULL, IsDelete = 0
        $needApprove = DB::connection('sqlsrv')->table('GenbaProcAuditDtl as a')
            ->join('GenbaProcAudit as b', 'b.SysID', '=', 'a.genba_id')
            ->whereNotNull('b.Auditor')
            ->where('b.Auditor', '!=', '')
            ->where(function ($q) {
                $q->where('b.IsDelete', 0)
                    ->orWhereNull('b.IsDelete');
            })
            ->where($categoryFilter)
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
            ->where('b.Date', '<=', $endOfMonth)
            ->count();

        // 3. Due Date (Overdue): due_date < today AND (evidence is null/0 OR corrective_action is null/0)
        $dueDateCount = DB::connection('sqlsrv')->table('GenbaProcAuditDtl as a')
            ->join('GenbaProcAudit as b', 'b.SysID', '=', 'a.genba_id')
            ->whereNotNull('b.Auditor')
            ->where('b.Auditor', '!=', '')
            ->where(function ($q) {
                $q->where('b.IsDelete', 0)
                    ->orWhereNull('b.IsDelete');
            })
            ->where($categoryFilter)
            ->whereNotNull('a.findings')
            ->whereDate('a.due_date', '<', today())
            ->where(function ($q) {
                $q->whereNull('a.evidence')
                    ->orWhere('a.evidence', 0)
                    ->orWhereNull('a.corrective_action')
                    ->orWhere('a.corrective_action', 0);
            })
            ->where(function ($q) {
                $q->where('a.result', '!=', 1)
                    ->orWhereNull('a.result');
            })
            ->where('b.Date', '<=', $endOfMonth)
            ->count();

        // 4. Closed: evidence='1' AND corrective_action='1' AND verification_result='1'
        $findingsClose = DB::connection('sqlsrv')->table('GenbaProcAuditDtl as a')
            ->join('GenbaProcAudit as b', 'b.SysID', '=', 'a.genba_id')
            ->whereNotNull('b.Auditor')
            ->where('b.Auditor', '!=', '')
            ->where(function ($q) {
                $q->where('b.IsDelete', 0)
                    ->orWhereNull('b.IsDelete');
            })
            ->where($categoryFilter)
            ->whereNotNull('a.findings')
            ->where('a.evidence', '1')
            ->where('a.corrective_action', '1')
            ->where('a.verification_result', '1')
            ->where(function ($q) {
                $q->where('a.result', '!=', 1)
                    ->orWhereNull('a.result');
            })
            ->whereYear('b.Date', $year)
            ->whereMonth('b.Date', $month)
            ->count();

        // 5. All Findings: findings is not null, IsDelete = 0
        $allFindings = DB::connection('sqlsrv')->table('GenbaProcAuditDtl as a')
            ->join('GenbaProcAudit as b', 'b.SysID', '=', 'a.genba_id')
            ->whereNotNull('b.Auditor')
            ->where('b.Auditor', '!=', '')
            ->where(function ($q) {
                $q->where('b.IsDelete', 0)
                    ->orWhereNull('b.IsDelete');
            })
            ->where($categoryFilter)
            ->whereNotNull('a.findings')
            ->where('b.Date', '<=', $endOfMonth)
            ->count();

        return response()->json([
            'findingsOpen' => $findingsOpen,
            'needApprove' => $needApprove,
            'dueDateCount' => $dueDateCount,
            'findingsClose' => $findingsClose,
            'allFindings' => $allFindings
        ]);
    }
    public function table(Request $request, $category_id = 'NOT_BIQ')
    {
        $search = $request->front_table_search;
        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $auditor = $request->auditor;
        $dept = $request->dept;
        $status_filter = $request->status;
        $detail_area = $request->detail_area;
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

        $query = GenbaManagement::get_genba_mng_activity_list($search, $date_from, $date_to, $auditor, $dept, $status_filter, $detail_area, $category_id)
            ->whereNotNull('a.asign_to_dept')
            ->where('a.asign_to_dept', '!=', '');

        $totalData = (clone $query)->count();
        $totalFiltered = $totalData;
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = ($request->input('order.0.column') == 0 ? $columns[0] : $columns[$request->input('order.0.column')]);
        $dir = ($request->input('order.0.column') == 0 ? 'desc' : $request->input('order.0.dir'));

        $posts = (clone $query)
            ->offset($start)
            ->limit($limit)
            ->reorder($order, $dir)
            ->get();

        if (!empty($search)) {
            $totalFiltered = (clone $query)->count();
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
                $auditors = array_filter(preg_split('/\s*[,&]\s*/', html_entity_decode($post->Auditor ?? '', ENT_QUOTES, 'UTF-8')));
                $auditorHtml = '<div class="flex flex-wrap gap-1">';
                foreach ($auditors as $aud) {
                    $auditorHtml .= '<span class="px-2 py-1 bg-white border border-slate-200 text-[12px] font-semibold text-slate-700 uppercase tracking-tight">' . trim($aud) . '</span>';
                }
                $auditorHtml .= '</div>';
                $nestedData['auditor'] = $auditorHtml;
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

    public function chart_all_dept($yearMonth, $category_id = 'NOT_BIQ')
    {
        [$year, $month] = explode('-', $yearMonth);
        $year = (int) $year;
        $month = (int) $month;
        $endOfMonth = Carbon::createFromDate($year, $month, 1)->endOfMonth()->format('Y-m-d 23:59:59');

        $categoryFilter = function ($q) use ($category_id) {
            if ($category_id === 'NOT_BIQ') {
                $q->where(function($sub) {
                    $sub->whereNotIn('b.Category_id', [7, 8, 9, 10])->orWhereNull('b.Category_id');
                });
            } else {
                if (is_array($category_id)) {
                    $q->whereIn('b.Category_id', $category_id);
                } else {
                    $q->where('b.Category_id', $category_id);
                }
            }
        };

        $allDepartments = DB::connection('sqlsrv')
            ->table('GenbaDept')
            ->where('Checkbox01', 1)
            ->pluck('Key1')
            ->toArray();


        $closedResults = DB::connection('sqlsrv')
            ->table('GenbaProcAuditDtl as g')
            ->join('GenbaProcAudit as b', 'g.genba_id', '=', 'b.SysID')
            ->whereNotNull('b.Auditor')
            ->where('b.Auditor', '!=', '')
            ->where($categoryFilter)
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
            ->whereYear('b.Date', $year)
            ->whereMonth('b.Date', $month)
            ->whereNotNull('g.asign_to_dept')
            ->groupBy('g.asign_to_dept')
            ->get()
            ->keyBy('asign_to_dept');

        // 4. Query Khusus OPEN & OVERDUE (TANPA Filter Bulan/Tahun)
        $openOverdueResults = DB::connection('sqlsrv')
            ->table('GenbaProcAuditDtl as g')
            ->join('GenbaProcAudit as b', 'g.genba_id', '=', 'b.SysID')
            ->whereNotNull('b.Auditor')
            ->where('b.Auditor', '!=', '')
            ->where($categoryFilter)
            ->select(
                'g.asign_to_dept',
                // Logic OPEN: Salah satu belum (fix/evidence), Belum verify, Due Date masih aman
                DB::raw("
            SUM(CASE WHEN (g.corrective_action IS NULL OR g.corrective_action = '0' OR g.corrective_action = 0 OR g.evidence IS NULL OR g.evidence = '0' OR g.evidence = 0)
                     AND (g.verification_result IS NULL OR g.verification_result = '0' OR g.verification_result = 0)
                     AND CAST(g.due_date AS DATE) >= CAST(GETDATE() AS DATE)
                     THEN 1 ELSE 0 END) AS TotalOpen
            "),
                // Logic OVERDUE: Salah satu belum (fix/evidence), Belum verify, Due Date sudah lewat
                DB::raw("
            SUM(CASE WHEN (g.corrective_action IS NULL OR g.corrective_action = '0' OR g.corrective_action = 0 OR g.evidence IS NULL OR g.evidence = '0' OR g.evidence = 0)
                     AND (g.verification_result IS NULL OR g.verification_result = '0' OR g.verification_result = 0)
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
            ->where('b.Date', '<=', $endOfMonth)
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

            // Merging legacy combined "PE & TMC" counts into PE and TMC respectively
            if ($dept === 'PE' || $dept === 'TMC') {
                $close += $closedResults['PE & TMC']->TotalClose ?? 0;
                $open += $openOverdueResults['PE & TMC']->TotalOpen ?? 0;
                $overdue += $openOverdueResults['PE & TMC']->TotalOverdue ?? 0;
                $needApprove += $openOverdueResults['PE & TMC']->TotalNeedApprove ?? 0;
            }

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

    public function biq_index()
    {
        return view('dashboard.genba_biq');
    }

    public function biq_data_cards(Request $request)
    {
        return $this->data_cards($request, [7, 8, 9]);
    }

    public function biq_table(Request $request)
    {
        return $this->table($request, [7, 8, 9]);
    }

    public function biq_chart_all_dept($yearMonth)
    {
        return $this->chart_all_dept($yearMonth, [7, 8, 9]);
    }

    public function safety_index()
    {
        return view('dashboard.genba_safety');
    }

    public function safety_data_cards(Request $request)
    {
        return $this->data_cards($request, 10);
    }

    public function safety_table(Request $request)
    {
        return $this->table($request, 10);
    }

    public function safety_chart_all_dept($yearMonth)
    {
        return $this->chart_all_dept($yearMonth, 10);
    }

    public function internal_audit_data_cards(Request $request)
    {
        $yearMonth = $request->input('yearMonth', date('Y-m'));
        [$year, $month] = explode('-', $yearMonth);
        $year = (int) $year;
        $month = (int) $month;

        $query = DB::table('CsAuditDetail as d')
            ->join('CsAuditHeader as h', 'h.id', '=', 'd.audit_header_id')
            ->whereYear('h.audit_date', $year)
            ->whereMonth('h.audit_date', $month);

        $okCount = (clone $query)->where('d.judgment', 'OK')->count();
        $minorCount = (clone $query)->where('d.judgment', 'Minor')
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                  ->from('CsAuditCar as car')
                  ->whereColumn('car.audit_detail_id', 'd.id')
                  ->whereNotNull('car.finding')
                  ->where('car.finding', '<>', '')
                  ->whereNotNull('car.requirement_no')
                  ->where('car.requirement_no', '<>', '');
            })
            ->count();
        $majorCount = (clone $query)->where('d.judgment', 'Mayor')
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                  ->from('CsAuditCar as car')
                  ->whereColumn('car.audit_detail_id', 'd.id')
                  ->whereNotNull('car.finding')
                  ->where('car.finding', '<>', '')
                  ->whereNotNull('car.requirement_no')
                  ->where('car.requirement_no', '<>', '');
            })
            ->count();
        $ofiCount = (clone $query)->where('d.judgment', 'OFI')->count();

        return response()->json([
            'ok' => $okCount,
            'minor' => $minorCount,
            'major' => $majorCount,
            'ofi' => $ofiCount
        ]);
    }

    public function internal_audit_chart_all_dept($yearMonth)
    {
        [$year, $month] = explode('-', $yearMonth);
        $year = (int) $year;
        $month = (int) $month;

        $departments = DB::table('GenbaDept')
            ->orderBy('Key1')
            ->pluck('Key1')
            ->toArray();

        $data_name_dept = [];
        $data_total_ok = [];
        $data_total_minor = [];
        $data_total_major = [];
        $data_total_ofi = [];

        foreach ($departments as $dept) {
            $query = DB::table('CsAuditDetail as d')
                ->join('CsAuditHeader as h', 'h.id', '=', 'd.audit_header_id')
                ->where('h.auditee_dept', $dept)
                ->whereYear('h.audit_date', $year)
                ->whereMonth('h.audit_date', $month);

            $ok = (clone $query)->where('d.judgment', 'OK')->count();
            $minor = (clone $query)->where('d.judgment', 'Minor')
                ->whereExists(function ($q) {
                    $q->select(DB::raw(1))
                      ->from('CsAuditCar as car')
                      ->whereColumn('car.audit_detail_id', 'd.id')
                      ->whereNotNull('car.finding')
                      ->where('car.finding', '<>', '')
                      ->whereNotNull('car.requirement_no')
                      ->where('car.requirement_no', '<>', '');
                })
                ->count();
            $major = (clone $query)->where('d.judgment', 'Mayor')
                ->whereExists(function ($q) {
                    $q->select(DB::raw(1))
                      ->from('CsAuditCar as car')
                      ->whereColumn('car.audit_detail_id', 'd.id')
                      ->whereNotNull('car.finding')
                      ->where('car.finding', '<>', '')
                      ->whereNotNull('car.requirement_no')
                      ->where('car.requirement_no', '<>', '');
                })
                ->count();
            $ofi = (clone $query)->where('d.judgment', 'OFI')->count();

            $data_name_dept[] = $dept;
            $data_total_ok[] = $ok;
            $data_total_minor[] = $minor;
            $data_total_major[] = $major;
            $data_total_ofi[] = $ofi;
        }

        return response()->json([
            'data_name_dept' => $data_name_dept,
            'data_total_ok' => $data_total_ok,
            'data_total_minor' => $data_total_minor,
            'data_total_major' => $data_total_major,
            'data_total_ofi' => $data_total_ofi,
        ]);
    }

    public function internal_audit_chart_closed_dept($yearMonth)
    {
        [$year, $month] = explode('-', $yearMonth);
        $year = (int) $year;
        $month = (int) $month;
        $today = Carbon::now()->toDateString();

        $departments = DB::table('GenbaDept')
            ->orderBy('Key1')
            ->pluck('Key1')
            ->toArray();

        $data_name_dept = [];
        $data_total_minor = [];
        $data_total_major = [];
        $data_total_minor_overdue = [];
        $data_total_major_overdue = [];

        foreach ($departments as $dept) {
            $minor = DB::table('CsAuditCar as car')
                ->join('CsAuditDetail as d', 'd.id', '=', 'car.audit_detail_id')
                ->join('CsAuditHeader as h', 'h.id', '=', 'd.audit_header_id')
                ->where('car.department', $dept)
                ->where('car.finding_category', 'Minor')
                ->where('car.status', 'Closed')
                ->whereNotNull('car.qmr_approved_at')
                ->whereYear('h.audit_date', $year)
                ->whereMonth('h.audit_date', $month)
                ->count();

            $major = DB::table('CsAuditCar as car')
                ->join('CsAuditDetail as d', 'd.id', '=', 'car.audit_detail_id')
                ->join('CsAuditHeader as h', 'h.id', '=', 'd.audit_header_id')
                ->where('car.department', $dept)
                ->where('car.finding_category', 'Mayor')
                ->where('car.status', 'Closed')
                ->whereNotNull('car.qmr_approved_at')
                ->whereYear('h.audit_date', $year)
                ->whereMonth('h.audit_date', $month)
                ->count();

            $minorOverdue = DB::table('CsAuditCar as car')
                ->join('CsAuditDetail as d', 'd.id', '=', 'car.audit_detail_id')
                ->join('CsAuditHeader as h', 'h.id', '=', 'd.audit_header_id')
                ->leftJoin('CsAuditAction as act', 'act.audit_car_id', '=', 'car.id')
                ->where('car.department', $dept)
                ->where('car.finding_category', 'Minor')
                ->where('car.status', '<>', 'Closed')
                ->whereDate('car.due_date', '<', $today)
                ->where(function($q) {
                    $q->whereNull('act.action_status')
                      ->orWhereNotIn('act.action_status', ['open_verif', 'approve_superior', 'verified']);
                })
                ->whereYear('h.audit_date', $year)
                ->whereMonth('h.audit_date', $month)
                ->count();

            $majorOverdue = DB::table('CsAuditCar as car')
                ->join('CsAuditDetail as d', 'd.id', '=', 'car.audit_detail_id')
                ->join('CsAuditHeader as h', 'h.id', '=', 'd.audit_header_id')
                ->leftJoin('CsAuditAction as act', 'act.audit_car_id', '=', 'car.id')
                ->where('car.department', $dept)
                ->where('car.finding_category', 'Mayor')
                ->where('car.status', '<>', 'Closed')
                ->whereDate('car.due_date', '<', $today)
                ->where(function($q) {
                    $q->whereNull('act.action_status')
                      ->orWhereNotIn('act.action_status', ['open_verif', 'approve_superior', 'verified']);
                })
                ->whereYear('h.audit_date', $year)
                ->whereMonth('h.audit_date', $month)
                ->count();

            $data_name_dept[] = $dept;
            $data_total_minor[] = $minor;
            $data_total_major[] = $major;
            $data_total_minor_overdue[] = $minorOverdue;
            $data_total_major_overdue[] = $majorOverdue;
        }

        return response()->json([
            'data_name_dept' => $data_name_dept,
            'data_total_minor' => $data_total_minor,
            'data_total_major' => $data_total_major,
            'data_total_minor_overdue' => $data_total_minor_overdue,
            'data_total_major_overdue' => $data_total_major_overdue,
        ]);
    }

    public function internal_audit_export(Request $request)
    {
        $query = DB::table('CsAuditCar as a')
            ->leftJoin('CsAuditDetail as b', 'b.id', '=', 'a.audit_detail_id')
            ->leftJoin('CsAuditHeader as c', 'c.id', '=', 'b.audit_header_id')
            ->leftJoin('CsAuditAction as d', 'd.audit_car_id', '=', 'a.id')
            ->leftJoin('CsChecksheetItem as e', 'e.id', '=', 'b.checksheet_item_id')
            ->whereNotNull('a.department')
            ->where('a.department', '<>', '')
            ->whereNotNull('a.finding')
            ->where('a.finding', '<>', '')
            ->select(
                'a.*', 
                'b.checksheet_item_id', 
                'b.note as detail_note', 
                'c.hash_id as schedule_hash_id', 
                'c.auditee as header_auditee',
                'c.audit_date as audit_date',
                'c.audit_type as audit_type',
                'e.scope_item as scope_item',
                'd.auditee_superior_name as superior_name',
                'd.action_status as action_status',
                'd.corrective_action_one',
                'd.corrective_action_two',
                'd.corrective_action_three',
                'd.preventive_action_one',
                'd.preventive_action_two',
                'd.preventive_action_three',
                'd.corrective_path_one',
                'd.corrective_path_two',
                'd.corrective_path_three',
                'd.preventive_path_one',
                'd.preventive_path_two',
                'd.preventive_path_three'
            );

        // Apply filters
        if ($request->has('search') && !empty($request->search)) {
            $searchValue = $request->search;
            $query->where(function($q) use ($searchValue) {
                $q->where('a.req_number', 'LIKE', "%{$searchValue}%")
                  ->orWhere('a.department', 'LIKE', "%{$searchValue}%")
                  ->orWhere('a.auditor', 'LIKE', "%{$searchValue}%")
                  ->orWhere('a.auditee', 'LIKE', "%{$searchValue}%")
                  ->orWhere('a.finding_category', 'LIKE', "%{$searchValue}%");
            });
        }
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('a.created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('a.created_at', '<=', $request->date_to);
        }
        if ($request->has('dept') && !empty($request->dept)) {
            $query->where('a.department', $request->dept);
        }
        if ($request->has('finding_category') && !empty($request->finding_category)) {
            $query->where('a.finding_category', $request->finding_category);
        }

        $records = $query->orderBy('a.created_at', 'desc')->get();

        $templatePath = public_path('tamplate-xlsx/Internal_Audit_Export_Tamplate.xlsx');
        if (file_exists($templatePath)) {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set dynamic headers (only export date changes)
            $sheet->setCellValue('R4', ': ' . date('d M Y'));
            
            // Set Column B width slightly wider to avoid #### format overflow
            $sheet->getColumnDimension('B')->setWidth(8);
            
            // We want to write data starting from row 11.
            // Let's copy style of row 11 before writing.
            $styleB11 = $sheet->getStyle('B11');
            $styleC11 = $sheet->getStyle('C11');
            $styleD11 = $sheet->getStyle('D11');
            $styleE11 = $sheet->getStyle('E11');
            $styleF11 = $sheet->getStyle('F11');
            $styleG11 = $sheet->getStyle('G11');
            $styleH11 = $sheet->getStyle('H11');
            $styleI11 = $sheet->getStyle('I11');
            $styleJ11 = $sheet->getStyle('J11');
            $styleK11 = $sheet->getStyle('K11');
            $styleL11 = $sheet->getStyle('L11');
            $styleM11 = $sheet->getStyle('M11');
            $styleN11 = $sheet->getStyle('N11');
            $styleO11 = $sheet->getStyle('O11');
            $styleP11 = $sheet->getStyle('P11');
            $styleQ11 = $sheet->getStyle('Q11');
            $styleR11 = $sheet->getStyle('R11');
            
            $startRow = 11;
            
            // Clear row 11 first (just in case)
            for ($col = 2; $col <= 18; $col++) { // Columns B to R
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                $sheet->setCellValue($colLetter . $startRow, null);
            }
            
            $currentRow = $startRow;
            foreach ($records as $index => $row) {
                if ($currentRow > $startRow) {
                    $sheet->insertNewRowBefore($currentRow, 1);
                }
                
                // Duplicate styles
                $sheet->duplicateStyle($styleB11, 'B' . $currentRow);
                $sheet->duplicateStyle($styleC11, 'C' . $currentRow);
                $sheet->duplicateStyle($styleD11, 'D' . $currentRow);
                $sheet->duplicateStyle($styleE11, 'E' . $currentRow);
                $sheet->duplicateStyle($styleF11, 'F' . $currentRow);
                $sheet->duplicateStyle($styleG11, 'G' . $currentRow);
                $sheet->duplicateStyle($styleH11, 'H' . $currentRow);
                $sheet->duplicateStyle($styleI11, 'I' . $currentRow);
                $sheet->duplicateStyle($styleJ11, 'J' . $currentRow);
                $sheet->duplicateStyle($styleK11, 'K' . $currentRow);
                $sheet->duplicateStyle($styleL11, 'L' . $currentRow);
                $sheet->duplicateStyle($styleM11, 'M' . $currentRow);
                $sheet->duplicateStyle($styleN11, 'N' . $currentRow);
                $sheet->duplicateStyle($styleO11, 'O' . $currentRow);
                $sheet->duplicateStyle($styleP11, 'P' . $currentRow);
                $sheet->duplicateStyle($styleQ11, 'Q' . $currentRow);
                $sheet->duplicateStyle($styleR11, 'R' . $currentRow);
                
                // Set alignment wrap and vertical center
                foreach (range('B', 'R') as $colLetter) {
                    $sheet->getStyle($colLetter . $currentRow)->getAlignment()->setWrapText(true);
                    $sheet->getStyle($colLetter . $currentRow)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                    
                    // Add borders
                    $sheet->getStyle($colLetter . $currentRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                }
                
                // Format dates
                $auditDateStr = $row->audit_date ? \Carbon\Carbon::parse($row->audit_date)->format('d M Y') : '-';
                $dueDateStr = $row->due_date ? \Carbon\Carbon::parse($row->due_date)->format('d M Y') : '-';
                
                // Action items formatting
                $corrective = array_filter([$row->corrective_action_one, $row->corrective_action_two, $row->corrective_action_three]);
                $preventive = array_filter([$row->preventive_action_one, $row->preventive_action_two, $row->preventive_action_three]);
                
                $corrective_str = '';
                foreach ($corrective as $cIdx => $act) {
                    $corrective_str .= ($cIdx + 1) . ". " . $act . "\n";
                }
                $corrective_str = rtrim($corrective_str);

                $preventive_str = '';
                foreach ($preventive as $pIdx => $act) {
                    $preventive_str .= ($pIdx + 1) . ". " . $act . "\n";
                }
                $preventive_str = rtrim($preventive_str);
                
                // Status calculation
                $statusText = '-';
                if ($row->status === 'Draft' || ($row->action_status ?? '') === 'draft') {
                    $statusText = 'Draft (Auditee)';
                } elseif ($row->status === 'Under Review' || ($row->action_status ?? '') === 'open_verif') {
                    $statusText = 'Waiting Superior Approval';
                } elseif ($row->status === 'Need Verification' || ($row->action_status ?? '') === 'approve_superior') {
                    $statusText = 'Waiting Auditor Approval';
                } elseif ($row->status === 'Closed' && empty($row->qmr_approved_at)) {
                    $statusText = 'Waiting QMR Approval';
                } elseif ($row->status === 'Closed' && !empty($row->qmr_approved_at)) {
                    $statusText = 'Closed';
                } else {
                    $statusText = $row->status ?? '-';
                }
                
                // Write values
                $sheet->setCellValue('B' . $currentRow, $index + 1);
                $sheet->getStyle('B' . $currentRow)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_GENERAL);
                $sheet->setCellValue('C' . $currentRow, $auditDateStr);
                $sheet->setCellValue('D' . $currentRow, $row->req_number ?? '-');
                $sheet->setCellValue('E' . $currentRow, $row->clause_title ?? '-');
                $sheet->setCellValue('F' . $currentRow, $row->audit_type ?? '-');
                $sheet->setCellValue('G' . $currentRow, $row->scope_item ?? '-');
                $sheet->setCellValue('H' . $currentRow, $row->department ?? '-');
                $sheet->setCellValue('I' . $currentRow, $row->auditee ?? '-');
                $sheet->setCellValue('J' . $currentRow, $row->superior_name ?? '-');
                $sheet->setCellValue('K' . $currentRow, $row->auditor ?? '-');
                $sheet->setCellValue('L' . $currentRow, $row->finding_category ?? '-');
                $sheet->setCellValue('M' . $currentRow, $row->finding ?? '-');
                $sheet->setCellValue('N' . $currentRow, $corrective_str ?: '-');
                $sheet->setCellValue('O' . $currentRow, $dueDateStr);
                $sheet->setCellValue('P' . $currentRow, $preventive_str ?: '-');
                $sheet->setCellValue('Q' . $currentRow, $dueDateStr);
                $sheet->setCellValue('R' . $currentRow, $statusText);
                
                $currentRow++;
            }
            
            // Set row heights for written data rows to fit multi-line values nicely
            for ($r = $startRow; $r < $currentRow; $r++) {
                $sheet->getRowDimension($r)->setRowHeight(-1); // Auto height
            }
            
        } else {
            $htmlString = view('export.xlxs_export', compact('records'))->render();
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
            $spreadsheet = $reader->loadFromString($htmlString);
            $sheet = $spreadsheet->getActiveSheet();
            foreach ($sheet->getColumnIterator() as $column) {
                $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
            }
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'Internal_Audit_Export_' . date('Ymd_His') . '.xlsx';

        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                'Cache-Control' => 'max-age=0',
            ]
        );
    }

    public function internal_audit_print(Request $request)
    {
        $query = DB::table('CsAuditCar as a')
            ->leftJoin('CsAuditDetail as b', 'b.id', '=', 'a.audit_detail_id')
            ->leftJoin('CsAuditHeader as c', 'c.id', '=', 'b.audit_header_id')
            ->leftJoin('CsAuditAction as d', 'd.audit_car_id', '=', 'a.id')
            ->leftJoin('CsChecksheetItem as e', 'e.id', '=', 'b.checksheet_item_id')
            ->whereNotNull('a.department')
            ->where('a.department', '<>', '')
            ->whereNotNull('a.finding')
            ->where('a.finding', '<>', '')
            ->select(
                'a.*', 
                'b.checksheet_item_id', 
                'b.note as detail_note', 
                'c.hash_id as schedule_hash_id', 
                'c.auditee as header_auditee',
                'c.audit_date as audit_date',
                'c.audit_type as audit_type',
                'e.scope_item as scope_item',
                'd.auditee_superior_name as superior_name',
                'd.corrective_action_one',
                'd.corrective_action_two',
                'd.corrective_action_three',
                'd.preventive_action_one',
                'd.preventive_action_two',
                'd.preventive_action_three',
                'd.corrective_path_one',
                'd.corrective_path_two',
                'd.corrective_path_three',
                'd.preventive_path_one',
                'd.preventive_path_two',
                'd.preventive_path_three'
            );

        // Apply filters
        if ($request->has('search') && !empty($request->search)) {
            $searchValue = $request->search;
            $query->where(function($q) use ($searchValue) {
                $q->where('a.req_number', 'LIKE', "%{$searchValue}%")
                  ->orWhere('a.department', 'LIKE', "%{$searchValue}%")
                  ->orWhere('a.auditor', 'LIKE', "%{$searchValue}%")
                  ->orWhere('a.auditee', 'LIKE', "%{$searchValue}%")
                  ->orWhere('a.finding_category', 'LIKE', "%{$searchValue}%");
            });
        }
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('a.created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('a.created_at', '<=', $request->date_to);
        }
        if ($request->has('dept') && !empty($request->dept)) {
            $query->where('a.department', $request->dept);
        }
        if ($request->has('finding_category') && !empty($request->finding_category)) {
            $query->where('a.finding_category', $request->finding_category);
        }

        $records = $query->orderBy('a.created_at', 'desc')->get();

        return view('export.pdf_export', compact('records'));
    }

    public function genba_mng_export(Request $request, $category_id = 'NOT_BIQ')
    {
        $search = $request->search;
        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $auditor = $request->auditor;
        $dept = $request->dept;
        $status_filter = $request->status;
        $detail_area = $request->detail_area;

        $query = GenbaManagement::get_genba_mng_activity_list($search, $date_from, $date_to, $auditor, $dept, $status_filter, $detail_area, $category_id)
            ->whereNotNull('a.asign_to_dept')
            ->where('a.asign_to_dept', '!=', '');

        $records = $query->get();

        $templatePath = public_path('tamplate-xlsx/Genba_MNG_Export_Tamplate.xlsx');
        if (file_exists($templatePath)) {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set dynamic header: K4 has export date
            $sheet->setCellValue('K4', date('d-m-Y'));
            
            // We want to write data starting from row 11.
            // Copy styling of row 11 before writing.
            $styleB11 = $sheet->getStyle('B11');
            $styleC11 = $sheet->getStyle('C11');
            $styleD11 = $sheet->getStyle('D11');
            $styleE11 = $sheet->getStyle('E11');
            $styleF11 = $sheet->getStyle('F11');
            $styleG11 = $sheet->getStyle('G11');
            $styleH11 = $sheet->getStyle('H11');
            $styleI11 = $sheet->getStyle('I11');
            
            $startRow = 11;
            
            // Clear row 11 first
            for ($col = 2; $col <= 11; $col++) { // Columns B to K
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                $sheet->setCellValue($colLetter . $startRow, null);
            }
            
            $currentRow = $startRow;
            foreach ($records as $index => $row) {
                if ($currentRow > $startRow) {
                    $sheet->insertNewRowBefore($currentRow, 1);
                }
                
                // Duplicate styles
                $sheet->duplicateStyle($styleB11, 'B' . $currentRow);
                $sheet->duplicateStyle($styleC11, 'C' . $currentRow);
                $sheet->duplicateStyle($styleD11, 'D' . $currentRow);
                $sheet->duplicateStyle($styleE11, 'E' . $currentRow);
                $sheet->duplicateStyle($styleF11, 'F' . $currentRow);
                $sheet->duplicateStyle($styleG11, 'G' . $currentRow);
                $sheet->duplicateStyle($styleH11, 'H' . $currentRow);
                $sheet->duplicateStyle($styleI11, 'I' . $currentRow);
                $sheet->duplicateStyle($styleI11, 'J' . $currentRow);
                $sheet->duplicateStyle($styleI11, 'K' . $currentRow);
                
                // Merge columns I to K for finding
                $sheet->mergeCells("I{$currentRow}:K{$currentRow}");
                
                // Set alignment wrap and vertical center
                foreach (range('B', 'K') as $colLetter) {
                    $sheet->getStyle($colLetter . $currentRow)->getAlignment()->setWrapText(true);
                    $sheet->getStyle($colLetter . $currentRow)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                    $sheet->getStyle($colLetter . $currentRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                }
                
                // Format date
                $genbaDateStr = $row->Date ? \Carbon\Carbon::parse($row->Date)->format('d M Y') : '-';
                
                // Status mapping
                if ($row->verification_result == '1' || $row->verification_result == 1) {
                    $statusText = 'CLOSE';
                } elseif (!empty($row->execution_comment) && !empty($row->execution_path)) {
                    $statusText = 'NEED VERIF';
                } elseif (!empty($row->due_date) && \Carbon\Carbon::parse($row->due_date)->lt(\Carbon\Carbon::today())) {
                    $statusText = 'OVERDUE';
                } else {
                    $statusText = 'OPEN';
                }
                
                // Write values
                $sheet->setCellValue('B' . $currentRow, $index + 1);
                $sheet->getStyle('B' . $currentRow)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_GENERAL);
                $sheet->setCellValue('C' . $currentRow, $genbaDateStr);
                $sheet->setCellValue('D' . $currentRow, $row->DocNum ?? '-');
                $sheet->setCellValue('E' . $currentRow, $row->Area_Checked ?? '-');
                $sheet->setCellValue('F' . $currentRow, $row->asign_to_dept ?? '-');
                $sheet->setCellValue('G' . $currentRow, $row->Auditor ?? '-');
                $sheet->setCellValue('H' . $currentRow, $statusText);
                $sheet->setCellValue('I' . $currentRow, $row->findings ?? '-');
                
                $currentRow++;
            }
            
            // Set row heights to fit multi-line content
            for ($r = $startRow; $r < $currentRow; $r++) {
                $sheet->getRowDimension($r)->setRowHeight(-1); // Auto height
            }
        } else {
            abort(404, 'Template file not found.');
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'Genba_MNG_Export_' . date('Ymd_His') . '.xlsx';

        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                'Cache-Control' => 'max-age=0',
            ]
        );
    }
}

