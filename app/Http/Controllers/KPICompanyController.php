<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\UserMenuPermission;

class KPICompanyController extends Controller
{
    /**
     * Reversible base-26 hashing for database IDs.
     * Generates a 9-character format like xxx-xxx-xxx.
     */
    public static function encodeId(int $id)
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyz';
        $idVal = $id;
        $idChars = '';
        for ($i = 0; $i < 5; $i++) {
            $idChars .= $alphabet[$idVal % 26];
            $idVal = (int)($idVal / 26);
        }
        
        $saltVal = ($id * 1234567 + 890123) % 456976; // 26^4 = 456976
        $saltChars = '';
        for ($i = 0; $i < 4; $i++) {
            $saltChars .= $alphabet[$saltVal % 26];
            $saltVal = (int)($saltVal / 26);
        }
        
        $hash = $idChars[0] . $saltChars[0] . $idChars[1] . $saltChars[1] . $idChars[2] . $saltChars[2] . $idChars[3] . $saltChars[3] . $idChars[4];
        return substr($hash, 0, 3) . '-' . substr($hash, 3, 3) . '-' . substr($hash, 6, 3);
    }

    public static function decodeId(string $hash)
    {
        $code = str_replace('-', '', $hash);
        if (strlen($code) !== 9) {
            return null;
        }
        
        $alphabet = 'abcdefghijklmnopqrstuvwxyz';
        $idChars = $code[0] . $code[2] . $code[4] . $code[6] . $code[8];
        
        $id = 0;
        for ($i = 4; $i >= 0; $i--) {
            $char = $idChars[$i];
            $pos = strpos($alphabet, $char);
            if ($pos === false) {
                return null;
            }
            $id = $id * 26 + $pos;
        }
        
        $saltChars = $code[1] . $code[3] . $code[5] . $code[7];
        $saltVal = 0;
        for ($i = 3; $i >= 0; $i--) {
            $char = $saltChars[$i];
            $pos = strpos($alphabet, $char);
            if ($pos === false) {
                return null;
            }
            $saltVal = $saltVal * 26 + $pos;
        }
        
        $expectedSaltVal = ($id * 1234567 + 890123) % 456976;
        if ($saltVal !== $expectedSaltVal) {
            return null;
        }
        
        return $id;
    }

    /**
     * Display the Company KPI dashboard view.
     */
    public function index()
    {
        $kpiList = DB::table('KPIList')->select('id', 'no_kpi', 'objective', 'pillar', 'target', 'unit', 'operator', 'calculation_method')->get();
        $departments = DB::table('GenbaDept')->orderBy('Key1', 'asc')->get();
        $pillars = DB::table('KPIList')->distinct()->pluck('pillar')->filter()->values();
        
        $periods = DB::table('KPICompany')->distinct()->pluck('periode')->filter()->values();
        if ($periods->isEmpty()) {
            $periods = collect([date('Y')]);
        }

        return view('kpi.company-kpi', compact('kpiList', 'departments', 'pillars', 'periods'));
    }

    /**
     * Fetch KPICompany records for serverside DataTable.
     */
    public function table(Request $request)
    {
        $user = Auth::user();
        $userDept = DB::table('t100_user_dept')->where('id_user', $user->id)->value('department');

        $query = DB::table('KPICompany as child')
            ->leftJoin('KPIList as parent', 'child.kpi_list_id', '=', 'parent.id')
            ->select('child.*', 'parent.objective', 'parent.pillar', 'parent.no_kpi', 'parent.target as target', 'parent.operator as operator', 'parent.unit as unit', 'parent.calculation_method as calculation_method')
            ->orderBy('child.id', 'desc');

        if (in_array($userDept, ['ICT', 'QMS'])) {
            // ICT / QMS can see all departments
        } elseif (!empty($userDept)) {
            // Regular user with department assigned
            $query->where('child.department_code', $userDept);
        } else {
            // User has no department assigned -> return no data
            $query->whereRaw('1 = 0');
        }

        // Apply filters
        if ($request->has('pillar') && !empty($request->pillar)) {
            $query->where('parent.pillar', $request->pillar);
        }
        if ($request->has('objective') && !empty($request->objective)) {
            $query->where('child.kpi_list_id', $request->objective);
        }
        if ($request->has('periode') && !empty($request->periode)) {
            $query->where('child.periode', $request->periode);
        }
        if ($request->has('department') && !empty($request->department)) {
            $query->where('child.department_code', $request->department);
        }

        if ($request->has('search') && !empty($request->search['value'])) {
            $searchValue = $request->search['value'];
            $query->where(function ($q) use ($searchValue) {
                $q->where('child.department_code', 'LIKE', "%{$searchValue}%")
                  ->orWhere('parent.objective', 'LIKE', "%{$searchValue}%")
                  ->orWhere('parent.target', 'LIKE', "%{$searchValue}%")
                  ->orWhere('parent.operator', 'LIKE', "%{$searchValue}%")
                  ->orWhere('parent.unit', 'LIKE', "%{$searchValue}%")
                  ->orWhere('child.periode', 'LIKE', "%{$searchValue}%")
                  ->orWhere('parent.calculation_method', 'LIKE', "%{$searchValue}%");
            });
        }

        $totalQuery = DB::table('KPICompany');
        if (in_array($userDept, ['ICT', 'QMS'])) {
            // ICT / QMS
        } elseif (!empty($userDept)) {
            $totalQuery->where('department_code', $userDept);
        } else {
            $totalQuery->whereRaw('1 = 0');
        }
        $totalRecords = $totalQuery->count();
        $filteredRecords = $query->count();

        if ($request->has('start') && $request->has('length')) {
            $query->skip($request->start)->take($request->length);
        }

        $data = $query->get();

        $canDelete = UserMenuPermission::canDelete(117);

        $currentYear = Carbon::now()->year;
        $years = [];
        for ($i = 5; $i >= 1; $i--) {
            $years[] = $currentYear - $i;
        }

        $response = [
            "draw" => intval($request->draw),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $filteredRecords,
            "data" => $data->map(function ($item, $key) use ($request, $canDelete, $years) {
                $start = $request->start ?? 0;
                
                $deleteBtn = '';
                if ($canDelete) {
                    $deleteBtn = ' <button type="button" title="Delete" class="w-10 h-10 shrink-0 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-100 hover:text-red-600 transition-all duration-200" 
                                    id="btn_delete_' . ($start + $key + 1) . '" 
                                    onclick="handleDelete(' . $item->id . ',' . ($start + $key + 1) . ')">
                                    <span id="icon_delete_' . ($start + $key + 1) . '" class="flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-600" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.3" d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"/>
                                            <path d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V7H5V5Z" fill="currentColor"/>
                                            <path d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"/>
                                        </svg>
                                    </span>
                                    <span id="loader_delete_' . ($start + $key + 1) . '" class="hidden animate-spin rounded-full h-4 w-4 border-b-2 border-current"></span>
                                </button>';
                }

                $histories = DB::table('KPIAchievementHistory')
                    ->where('kpi_list_id', $item->kpi_list_id)
                    ->whereIn('year', $years)
                    ->get()
                    ->keyBy('year');

                $row = [
                    "no" => $start + $key + 1,
                    "id" => $item->id,
                    "department_code" => $item->department_code,
                    "pillar" => $item->pillar ?? '-',
                    "objective" => isset($item->no_kpi) ? $item->no_kpi . ' - ' . $item->objective : ($item->objective ?? '-'),
                    "target" => isset($item->target) ? (($item->operator && $item->operator !== '-' ? $item->operator : '') . $item->target . ($item->unit === '%' ? '<span class="text-slate-500 font-normal">%</span>' : ($item->unit ? ' <span class="text-slate-500 font-normal">' . htmlspecialchars($item->unit) . '</span>' : ''))) : '-',
                    "operator" => $item->operator ?? '-',
                    "unit" => $item->unit ?? '-',
                    "periode" => $item->periode ?? '-',
                    "calculation_method" => Str::limit($item->calculation_method ?? '-', 50, '...'),
                ];

                foreach ($years as $yr) {
                    if (isset($histories[$yr]) && $histories[$yr]->achievement !== null) {
                        $achVal = $histories[$yr]->achievement;
                        $unitVal = $histories[$yr]->unit ?? $item->unit ?? '';
                        if ($unitVal === '%') {
                            $row["year_" . $yr] = $achVal . '<span class="text-slate-500 font-normal">%</span>';
                        } elseif ($unitVal) {
                            $row["year_" . $yr] = $achVal . ' <span class="text-slate-500 font-normal">' . htmlspecialchars($unitVal) . '</span>';
                        } else {
                            $row["year_" . $yr] = $achVal;
                        }
                    } else {
                        $row["year_" . $yr] = 'New KPI';
                    }
                }

                $row["action"] = '<div class="flex items-center justify-start gap-2 whitespace-nowrap">
                                <button type="button" title="Detail" class="w-10 h-10 shrink-0 flex items-center justify-center rounded-xl bg-green-50 text-green-500 hover:bg-green-100 hover:text-green-600 transition-all duration-200"
                                    onclick="handleDetail(\'' . self::encodeId($item->id) . '\')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                        <polyline points="12 5 19 12 12 19"></polyline>
                                    </svg>
                                </button>
                                <button type="button" title="Edit" class="w-10 h-10 shrink-0 flex items-center justify-center rounded-xl bg-blue-50 text-blue-500 hover:bg-blue-100 hover:text-blue-600 transition-all duration-200"
                                    onclick="handleEdit(this)"
                                    data-id="' . $item->id . '"
                                    data-kpi_list_id="' . $item->kpi_list_id . '"
                                    data-department_code="' . htmlspecialchars($item->department_code) . '"
                                    data-target="' . htmlspecialchars($item->target ?? '') . '"
                                    data-periode="' . htmlspecialchars($item->periode ?? '') . '">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="currentColor"></path>
                                    <path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="currentColor"></path><path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="currentColor"></path>
                                </svg>
                                </button>
                                ' . $deleteBtn . '
                           </div>';

                return $row;
            })
        ];

        return response()->json($response);
    }

    /**
     * Store a newly created KPICompany record.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kpi_list_id' => 'required|exists:KPIList,id',
            'department_code' => 'required',
            'periode' => 'required|in:' . Carbon::now()->year,
        ]);

        try {
            // Department code bisa berupa string dengan pemisah koma (multi-select)
            $departments = explode(',', $request->department_code);
            $departments = array_filter(array_map('trim', $departments));

            if (empty($departments)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select at least one department.'
                ], 400);
            }

            DB::beginTransaction();

            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

            foreach ($departments as $deptCode) {
                // Cek jika record untuk kpi_list_id, department_code, dan periode tersebut sudah ada agar tidak duplikat
                $exists = DB::table('KPICompany')
                    ->where('kpi_list_id', $request->kpi_list_id)
                    ->where('department_code', $deptCode)
                    ->where('periode', $request->periode)
                    ->exists();

                if ($exists) {
                    continue; // Skip jika sudah ada
                }

                $kpiCompanyId = DB::table('KPICompany')->insertGetId([
                    'kpi_list_id' => $request->kpi_list_id,
                    'department_code' => $deptCode,
                    'periode' => $request->periode,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);

                $activities = [];
                foreach ($months as $m) {
                    $activities[] = [
                        'kpi_company_id' => $kpiCompanyId,
                        'tahun' => $request->periode,
                        'bulan' => $m,
                        'actual' => null,
                        'status' => null,
                        'problem_solve' => null,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ];
                }
                DB::table('KPICompanyActivity')->insert($activities);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Company KPI added successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an existing KPICompany record.
     */
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'kpi_list_id' => 'required|exists:KPIList,id',
            'department_code' => 'required',
            'periode' => 'required|in:' . Carbon::now()->year,
        ]);

        try {
            DB::table('KPICompany')->where('id', $request->id)->update([
                'kpi_list_id' => $request->kpi_list_id,
                'department_code' => $request->department_code,
                'periode' => $request->periode,
                'updated_at' => Carbon::now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Company KPI updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an existing KPICompany record.
     */
    public function delete(Request $request)
    {
        if (!UserMenuPermission::canDelete(117)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        $request->validate([
            'id' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $activities = DB::table('KPICompanyActivity')
                ->where('kpi_company_id', $request->id)
                ->pluck('id');
            
            if ($activities->isNotEmpty()) {
                $problems = DB::table('KPICompanyActivityProblem')
                    ->whereIn('kpi_company_activity_id', $activities)
                    ->get();
                
                $fields = ['problem_image', 'root_cause_image', 'temporary_action_image', 'permanent_action_image', 'evidence'];
                foreach ($problems as $problem) {
                    foreach ($fields as $field) {
                        if (isset($problem->$field) && file_exists(public_path($problem->$field))) {
                            @unlink(public_path($problem->$field));
                        }
                    }
                }
                
                DB::table('KPICompanyActivityProblem')
                    ->whereIn('kpi_company_activity_id', $activities)
                    ->delete();
                
                DB::table('KPICompanyActivity')
                    ->where('kpi_company_id', $request->id)
                    ->delete();
            }

            DB::table('KPICompany')->where('id', $request->id)->delete();
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Company KPI deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function departments(Request $request)
    {
        $search = $request->input('search');
        $page = $request->input('page', 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $query = DB::table('GenbaDept');

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('Key1', 'LIKE', "%{$search}%")
                  ->orWhere('Desc', 'LIKE', "%{$search}%");
            });
        }

        $total = $query->count();
        $items = $query->orderBy('Key1', 'asc')->skip($offset)->take($limit)->get();

        $formattedItems = $items->map(function($item) {
            return [
                'id' => $item->Key1,
                'name' => $item->Key1
            ];
        });

        return response()->json([
            'items' => $formattedItems,
            'pagination' => [
                'more' => ($offset + $limit) < $total
            ]
        ]);
    }

    /**
     * Display the detail page for a specific Company KPI record.
     */
    public function detail(string $id)
    {
        $dbId = self::decodeId($id);
        if (!$dbId) {
            abort(404, 'Invalid KPI Company ID.');
        }

        $kpi = DB::table('KPICompany as child')
            ->leftJoin('KPIList as parent', 'child.kpi_list_id', '=', 'parent.id')
            ->select('child.*', 'parent.objective', 'parent.pillar', 'parent.no_kpi', 'parent.target as target', 'parent.operator', 'parent.unit', 'parent.calculation_method', 'parent.arrow_target', 'parent.result')
            ->where('child.id', $dbId)
            ->first();

        if (!$kpi) {
            abort(404);
        }

        // Fetch formula components from KPIFormula
        $formula = DB::table('KPIFormula')->where('kpi_list_id', $kpi->kpi_list_id)->first();
        $components = [];
        if ($formula) {
            for ($i = 1; $i <= 20; $i++) {
                $col = 'comp_' . $i;
                if (!empty($formula->$col)) {
                    $components[$i] = $formula->$col;
                }
            }
        }

        $departments = DB::table('GenbaDept')->orderBy('Key1', 'asc')->get();

        // Reset status to null if actual is null to clean up corrupted data
        DB::table('KPICompanyActivity')
            ->where('kpi_company_id', $dbId)
            ->whereNull('actual')
            ->update(['status' => null]);

        // Recalculate all months to automatically update statuses and correct historical data on refresh
        $this->recalculateAllMonths($dbId);

        $activities = DB::table('KPICompanyActivity')
            ->where('kpi_company_id', $dbId)
            ->orderBy('id', 'asc')
            ->get();

        if ($activities->isEmpty()) {
            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            $insertData = [];
            foreach ($months as $m) {
                $insertData[] = [
                    'kpi_company_id' => $dbId,
                    'tahun' => $kpi->periode ?? Carbon::now()->year,
                    'bulan' => $m,
                    'actual' => null,
                    'status' => null,
                    'problem_solve' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
            }
            DB::table('KPICompanyActivity')->insert($insertData);

            $activities = DB::table('KPICompanyActivity')
                ->where('kpi_company_id', $dbId)
                ->orderBy('id', 'asc')
                ->get();
        }

        // Map activities to add dynamic hash_id and problem data
        $activities = $activities->map(function($act) {
            $act->hash_id = self::encodeId($act->id);
            $act->problem = DB::table('KPICompanyActivityProblem')
                ->where('kpi_company_activity_id', $act->id)
                ->first();
            return $act;
        });

        $hasDeletePermission = UserMenuPermission::canDelete(117);

        return view('kpi.detail-kpi-company.company-kpi-detail', compact('kpi', 'departments', 'activities', 'hasDeletePermission', 'components', 'formula'));
    }

    /**
     * Display the Manage Activity Plan page for a specific Company KPI record.
     */
    public function manageActivityPlan(string $id)
    {
        $dbId = self::decodeId($id);
        if (!$dbId) {
            abort(404, 'Invalid KPI Company ID.');
        }

        $kpi = DB::table('KPICompany as child')
            ->leftJoin('KPIList as parent', 'child.kpi_list_id', '=', 'parent.id')
            ->select('child.*', 'parent.objective', 'parent.pillar', 'parent.no_kpi', 'parent.target as target', 'parent.operator', 'parent.unit', 'parent.calculation_method', 'parent.arrow_target', 'parent.result')
            ->where('child.id', $dbId)
            ->first();

        if (!$kpi) {
            abort(404);
        }

        $kpi->hash_id = self::encodeId($kpi->id);

        $formula = DB::table('KPIFormula')->where('kpi_list_id', $kpi->kpi_list_id)->first();
        $components = [];
        if ($formula) {
            for ($i = 1; $i <= 20; $i++) {
                $col = 'comp_' . $i;
                if (!empty($formula->$col)) {
                    $components[$i] = $formula->$col;
                }
            }
        }

        $departments = DB::table('GenbaDept')->orderBy('Key1', 'asc')->get();

        $activityPlans = DB::table('KPIActivityPlan')
            ->where('kpi_company_id', $dbId)
            ->orderBy('id', 'asc')
            ->get();

        $hasDeletePermission = UserMenuPermission::canDelete(117);

        return view('kpi.detail-kpi-company.manage-activity-plan', compact('kpi', 'departments', 'activityPlans', 'hasDeletePermission', 'components', 'formula'));
    }

    public function storeActivityPlan(Request $request)
    {
        $request->validate([
            'kpi_company_id' => 'required',
            'activity_plan' => 'required|string',
        ]);

        $dbId = self::decodeId($request->kpi_company_id);
        if (!$dbId) {
            return response()->json(['success' => false, 'message' => 'Invalid KPI Company ID'], 400);
        }

        $supportTopicArr = $request->input('support_topic', []);
        $supportTopicStr = is_array($supportTopicArr) ? implode(', ', $supportTopicArr) : $supportTopicArr;

        $supportingArr = $request->input('supporting', []);
        $supportingStr = is_array($supportingArr) ? implode(', ', $supportingArr) : $supportingArr;

        $startMonth = (int) $request->input('start_month', 1);
        $endMonth = (int) $request->input('end_month', 1);

        // Build active months checklist array
        $monthsData = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthsData[$m] = ($m >= $startMonth && $m <= $endMonth);
        }

        $id = $request->input('id');
        $data = [
            'kpi_company_id' => $dbId,
            'support_topic' => $supportTopicStr,
            'activity_plan' => $request->activity_plan,
            'pic' => $request->pic,
            'supporting' => $supportingStr,
            'quick_plan' => $request->quick_plan,
            'start_month' => $startMonth,
            'end_month' => $endMonth,
            'months_data' => json_encode($monthsData),
            'remark' => $request->remark ?: 'Closed',
            'updated_at' => Carbon::now()
        ];

        // Calculate success rate based on uploaded evidence vs active planned months
        $plannedMonths = [];
        for ($m = 1; $m <= 12; $m++) {
            if (!empty($monthsData[$m]) || !empty($monthsData[(string)$m])) {
                $plannedMonths[] = $m;
            }
        }
        $plannedCount = count($plannedMonths);

        $existingPlan = $id ? DB::table('KPIActivityPlan')->where('id', $id)->first() : null;
        $evidences = ($existingPlan && is_string($existingPlan->evidences_data)) ? json_decode($existingPlan->evidences_data, true) : [];

        $uploadedCount = 0;
        foreach ($plannedMonths as $m) {
            if ((isset($evidences[$m]) && !empty($evidences[$m])) || (isset($evidences[(string)$m]) && !empty($evidences[(string)$m]))) {
                $uploadedCount++;
            }
        }

        $calculatedRate = ($plannedCount > 0) ? min(100, round(($uploadedCount / $plannedCount) * 100)) : 0;
        $data['success_rate'] = $calculatedRate;
        $data['remark'] = ($calculatedRate >= 100) ? 'Closed' : 'Open';

        if ($id) {
            DB::table('KPIActivityPlan')->where('id', $id)->update($data);
        } else {
            $data['status'] = 'On Progress';
            $data['created_at'] = Carbon::now();
            DB::table('KPIActivityPlan')->insert($data);
        }

        return response()->json(['success' => true, 'message' => 'Activity Plan saved successfully.']);
    }

    public function deleteActivityPlan(Request $request)
    {
        $id = $request->id;
        if (!$id) {
            return response()->json(['success' => false, 'message' => 'Invalid ID'], 400);
        }

        $plan = DB::table('KPIActivityPlan')->where('id', $id)->first();
        if ($plan) {
            // Delete associated physical files from uploads directory
            $evidences = is_string($plan->evidences_data) ? json_decode($plan->evidences_data, true) : (array)($plan->evidences_data ?? []);
            if (!empty($evidences)) {
                foreach ($evidences as $filePath) {
                    if (!empty($filePath)) {
                        $fullPath = public_path($filePath);
                        if (file_exists($fullPath)) {
                            @unlink($fullPath);
                        }
                    }
                }
            }

            DB::table('KPIActivityPlan')->where('id', $id)->delete();
        }

        return response()->json(['success' => true, 'message' => 'Activity Plan deleted successfully.']);
    }

    public function toggleRemark(Request $request)
    {
        $id = $request->input('id');
        $plan = DB::table('KPIActivityPlan')->where('id', $id)->first();
        if (!$plan) {
            return response()->json(['success' => false, 'message' => 'Activity Plan not found.'], 404);
        }

        $newRemark = ($plan->remark === 'Open') ? 'Closed' : 'Open';
        DB::table('KPIActivityPlan')->where('id', $id)->update([
            'remark' => $newRemark,
            'updated_at' => Carbon::now()
        ]);

        return response()->json(['success' => true, 'remark' => $newRemark]);
    }

    public function uploadEvidence(Request $request)
    {
        $request->validate([
            'activity_plan_id' => 'required',
            'month_num' => 'required|integer|min:1|max:12',
            'evidence' => 'required|file|max:20480'
        ]);

        $id = $request->input('activity_plan_id');
        $month = (int) $request->input('month_num');

        $plan = DB::table('KPIActivityPlan')->where('id', $id)->first();
        if (!$plan) {
            return response()->json(['success' => false, 'message' => 'Activity Plan not found.'], 404);
        }

        if ($request->hasFile('evidence')) {
            $evidences = is_string($plan->evidences_data) ? json_decode($plan->evidences_data, true) : (array)($plan->evidences_data ?? []);

            // Delete old file if replacing for same month
            if (isset($evidences[$month]) && !empty($evidences[$month])) {
                $oldPath = public_path($evidences[$month]);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $file = $request->file('evidence');
            $fileName = time() . '_' . $month . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('kpi/activity-plans'), $fileName);
            $filePath = 'kpi/activity-plans/' . $fileName;

            $evidences[$month] = $filePath;

            // Recalculate % Success Rate automatically based on uploaded evidence files vs planned active months
            $monthsData = is_string($plan->months_data) ? json_decode($plan->months_data, true) : (array)($plan->months_data ?? []);
            
            $plannedMonths = [];
            for ($m = 1; $m <= 12; $m++) {
                $isPlanned = false;
                if (!empty($monthsData)) {
                    $isPlanned = !empty($monthsData[$m]) || !empty($monthsData[(string)$m]);
                } else {
                    $startM = (int)($plan->start_month ?? 1);
                    $endM = (int)($plan->end_month ?? 1);
                    $isPlanned = ($m >= $startM && $m <= $endM);
                }

                if ($isPlanned) {
                    $plannedMonths[] = $m;
                }
            }

            $plannedCount = count($plannedMonths);
            $uploadedCount = 0;

            foreach ($plannedMonths as $m) {
                if ((isset($evidences[$m]) && !empty($evidences[$m])) || (isset($evidences[(string)$m]) && !empty($evidences[(string)$m]))) {
                    $uploadedCount++;
                }
            }

            $successRate = ($plannedCount > 0) ? min(100, round(($uploadedCount / $plannedCount) * 100)) : 0;
            $remark = ($successRate >= 100) ? 'Closed' : 'Open';

            DB::table('KPIActivityPlan')->where('id', $id)->update([
                'evidences_data' => json_encode($evidences),
                'success_rate' => $successRate,
                'remark' => $remark,
                'updated_at' => Carbon::now()
            ]);

            return response()->json(['success' => true, 'message' => 'Evidence file uploaded successfully.', 'path' => asset($filePath)]);
        }

        return response()->json(['success' => false, 'message' => 'No file uploaded.'], 400);
    }

    public function editActivity(string $id)
    {
        $dbId = self::decodeId($id);
        if (!$dbId) {
            abort(404, 'Invalid Activity ID.');
        }

        $activity = DB::table('KPICompanyActivity as act')
            ->join('KPICompany as child', 'act.kpi_company_id', '=', 'child.id')
            ->leftJoin('KPIList as parent', 'child.kpi_list_id', '=', 'parent.id')
            ->select(
                'act.id',
                'act.kpi_company_id',
                'act.tahun',
                'act.bulan',
                'act.actual',
                'act.status',
                'act.problem_solve',
                'act.calc_operator',
                'act.created_at',
                'act.updated_at',
                'act.comp_1', 'act.comp_2', 'act.comp_3', 'act.comp_4', 'act.comp_5',
                'act.comp_6', 'act.comp_7', 'act.comp_8', 'act.comp_9', 'act.comp_10',
                'act.comp_11', 'act.comp_12', 'act.comp_13', 'act.comp_14', 'act.comp_15',
                'act.comp_16', 'act.comp_17', 'act.comp_18', 'act.comp_19', 'act.comp_20',
                'child.department_code',
                'child.kpi_list_id',
                'parent.operator as operator',
                'parent.unit as unit',
                'child.periode as periode',
                'parent.objective',
                'parent.target as master_target'
            )
            ->where('act.id', $dbId)
            ->first();

        if (!$activity) {
            abort(404);
        }

        $activity->hash_id = self::encodeId($activity->id);
        $activity->kpi_company_hash_id = self::encodeId($activity->kpi_company_id);

        // Fetch formula components mapping from KPIFormula
        $formula = DB::table('KPIFormula')->where('kpi_list_id', $activity->kpi_list_id)->first();
        $components = [];
        if ($formula) {
            for ($i = 1; $i <= 20; $i++) {
                $col = 'comp_' . $i;
                if (!empty($formula->$col)) {
                    $components[$i] = $formula->$col;
                }
            }
        }

        $departments = DB::table('GenbaDept')->orderBy('Key1', 'asc')->get();
        $problem = DB::table('KPICompanyActivityProblem')->where('kpi_company_activity_id', $dbId)->first();

        return view('kpi.detail-kpi-company.company-kpi-insert', compact('activity', 'departments', 'problem', 'components', 'formula'));
    }

    public function updateActivity(Request $request, string $id)
    {
        $dbId = self::decodeId($id);
        if (!$dbId) {
            return redirect()->back()->with('error', 'Invalid Activity ID.');
        }

        try {
            $activity = DB::table('KPICompanyActivity as act')
                ->join('KPICompany as child', 'act.kpi_company_id', '=', 'child.id')
                ->leftJoin('KPIList as parent', 'child.kpi_list_id', '=', 'parent.id')
                ->select(
                    'act.id',
                    'act.kpi_company_id',
                    'child.kpi_list_id',
                    'act.tahun',
                    'act.bulan',
                    'act.actual',
                    'act.status',
                    'act.problem_solve',
                    'act.calc_operator',
                    'parent.operator as operator',
                    'parent.unit as unit',
                    'parent.target as master_target'
                )
                ->where('act.id', $dbId)
                ->first();

            if (!$activity) {
                return redirect()->back()->with('error', 'Activity record not found.');
            }

            // Dynamically validate based on components
            $formula = DB::table('KPIFormula')->where('kpi_list_id', $activity->kpi_list_id)->first();
            $rules = [];
            if ($formula) {
                for ($i = 1; $i <= 20; $i++) {
                    $col = 'comp_' . $i;
                    if (!empty($formula->$col)) {
                        $rules['comp_' . $i] = 'required';
                    }
                }
            } else {
                $rules['actual'] = 'required';
            }
            $request->validate($rules);

            // Extract actual value
            $actualValInput = $request->actual;
            if ($formula) {
                $vals = [];
                for ($i = 1; $i <= 20; $i++) {
                    $col = 'comp_' . $i;
                    if (!empty($formula->$col)) {
                        $compVal = $request->input('comp_' . $i);
                        if ($compVal !== null) {
                            $vals[] = $this->parseLocalNumber($compVal);
                        }
                    }
                }
                
                $op = !empty($activity->calc_operator) ? $activity->calc_operator : (DB::table('KPIFormula')->where('kpi_list_id', $activity->kpi_list_id)->value('calc_operator'));
                $calculatedActual = null;
                if (!empty($op) && !empty($vals)) {
                    if (strpos($op, '[') !== false) {
                        $expr = $op;
                        // Auto-prefix master style [comp_X] with current month name
                        $expr = preg_replace('/\[(comp_\d+)\]/', '[' . $activity->bulan . '.$1]', $expr);
                        preg_match_all('/\[([A-Za-z]{3})\.(comp_\d+)\]/', $expr, $matches, PREG_SET_ORDER);
                        $countComponentsInExpr = 0;
                        foreach ($matches as $match) {
                            $mName = $match[1];
                            $cCol = $match[2];
                            // Since we are updating, we can fetch month values from DB, but for current month we use current request values
                            if ($mName === $activity->bulan) {
                                $compVal = $request->input($cCol);
                            } else {
                                $actForMonth = DB::table('KPICompanyActivity')
                                    ->where('kpi_company_id', $activity->kpi_company_id)
                                    ->where('bulan', $mName)
                                    ->first();
                                $compVal = ($actForMonth && $actForMonth->$cCol !== null) ? $actForMonth->$cCol : 0;
                            }
                            if ($compVal !== null && $compVal !== '') {
                                $countComponentsInExpr++;
                            }
                            $val = $this->parseLocalNumber($compVal);
                            $expr = str_replace($match[0], $val, $expr);
                        }
                        $expr = str_replace(['x', 'X'], '*', $expr);
                        $exprClean = preg_replace('/[^0-9\+\-\*\/\(\)\.\s]/', '', $expr);
                        if (!empty($exprClean)) {
                            try {
                                $calculatedActual = @eval("return ({$exprClean});");
                                $unit = $activity->unit ?? '';
                                $isPercentUnit = in_array(strtolower(trim($unit)), ['%', 'percent', 'persen']);
                                if ($isPercentUnit && strpos($exprClean, '*') !== false && strpos($exprClean, '/') === false && $countComponentsInExpr > 1) {
                                    $calculatedActual = $calculatedActual / pow(100, $countComponentsInExpr - 1);
                                }
                            } catch (\Throwable $t) {
                                $calculatedActual = null;
                            }
                        }
                    } else {
                        $unit = $activity->unit ?? '';
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
                } elseif (!empty($vals)) {
                    // Fallback: if no custom formula is set, but components are entered, default to sum!
                    $calculatedActual = array_sum($vals);
                }
                if ($calculatedActual !== null) {
                    $actualValInput = $calculatedActual;
                }
            }

            $actualVal = null;
            $status = null;

            if ($actualValInput !== null && $actualValInput !== '') {
                $actValNum = (float) $actualValInput;
                $actualVal = number_format($actValNum, 2, ',', '');
                
                // Automatically calculate status based on actual, master_target, and operator
                $operator = trim(htmlspecialchars_decode($activity->operator ?? '>='));
                $targetStr = $activity->master_target ?? '0';
                $targetVal = $this->parseLocalNumber($targetStr);

                $isAchieved = false;
                switch ($operator) {
                    case '>=': $isAchieved = ($actValNum >= $targetVal); break;
                    case '<=': $isAchieved = ($actValNum <= $targetVal); break;
                    case '>':  $isAchieved = ($actValNum > $targetVal); break;
                    case '<':  $isAchieved = ($actValNum < $targetVal); break;
                    case '=':
                    default:   $isAchieved = ($actValNum == $targetVal); break;
                }
                $status = $isAchieved ? 'Achieved' : 'Not Achieved';
            }

            $wasAlreadyProblem = false;
            if ($activity->actual !== null) {
                $wasAlreadyProblem = ((float)filter_var($activity->actual, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) > 0);
            }

            DB::beginTransaction();

            // Update the actual, status, and components in KPICompanyActivity
            $updateData = [
                'actual' => ($actualVal !== null) ? $actualVal : null,
                'status' => $status,
                'updated_at' => Carbon::now()
            ];
            for ($i = 1; $i <= 20; $i++) {
                if ($request->has('comp_' . $i)) {
                    $updateData['comp_' . $i] = $request->input('comp_' . $i);
                }
            }

            DB::table('KPICompanyActivity')
                ->where('id', $dbId)
                ->update($updateData);

            if ($status === 'Not Achieved' || $request->filled('problem_description')) {
                // If they submitted, validate the problem solving fields
                $request->validate([
                    'problem_description' => 'required',
                    'root_cause' => 'required',
                    'temporary_action' => 'required',
                    'permanent_action' => 'required',
                    'start_date' => 'required|date',
                    'finish_date' => 'required|date',
                    'closed_status' => 'required|string',
                    'pic_dept' => 'required|string',
                    'follow_up_by' => 'required|string',
                    'problem_image' => 'nullable|image|max:2048',
                    'root_cause_image' => 'nullable|image|max:2048',
                    'temporary_action_image' => 'nullable|image|max:2048',
                    'permanent_action_image' => 'nullable|image|max:2048',
                    'evidence' => 'nullable|file|max:5120',
                ]);

                // File Uploads
                $uploadedFiles = [];
                $fileFields = ['problem_image', 'root_cause_image', 'temporary_action_image', 'permanent_action_image', 'evidence'];
                foreach ($fileFields as $field) {
                    if ($request->hasFile($field)) {
                        $file = $request->file($field);
                        $fileName = $field . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                        $file->move(public_path('uploads/kpi/problems'), $fileName);
                        $uploadedFiles[$field] = 'uploads/kpi/problems/' . $fileName;
                    }
                }

                $existing = DB::table('KPICompanyActivityProblem')
                    ->where('kpi_company_activity_id', $dbId)
                    ->first();

                $problemData = [
                    'problem_description' => $request->problem_description,
                    'root_cause' => $request->root_cause,
                    'temporary_action' => $request->temporary_action,
                    'permanent_action' => $request->permanent_action,
                    'machine' => $request->machine,
                    'material' => $request->material,
                    'man' => $request->man,
                    'method' => $request->method,
                    'money' => $request->money,
                    'environment' => $request->environment,
                    'start_date' => $request->start_date,
                    'finish_date' => $request->finish_date,
                    'closed_status' => $request->closed_status,
                    'pic_dept' => $request->pic_dept,
                    'follow_up_by' => $request->follow_up_by,
                    'updated_at' => Carbon::now()
                ];

                foreach ($fileFields as $field) {
                    if (isset($uploadedFiles[$field])) {
                        $problemData[$field] = $uploadedFiles[$field];
                    } elseif ($request->input('delete_' . $field) == '1') {
                        $problemData[$field] = null;
                        if ($existing && isset($existing->$field) && file_exists(public_path($existing->$field))) {
                            @unlink(public_path($existing->$field));
                        }
                    } elseif ($existing && isset($existing->$field)) {
                        $problemData[$field] = $existing->$field;
                    }
                }

                if ($existing) {
                    DB::table('KPICompanyActivityProblem')
                        ->where('kpi_company_activity_id', $dbId)
                        ->update($problemData);
                } else {
                    $problemData['kpi_company_activity_id'] = $dbId;
                    $problemData['created_at'] = Carbon::now();
                    DB::table('KPICompanyActivityProblem')->insert($problemData);
                }
            } else {
                // If actual is 0 (or <= 0), delete the problem record and its files
                $problem = DB::table('KPICompanyActivityProblem')
                    ->where('kpi_company_activity_id', $dbId)
                    ->first();
                if ($problem) {
                    $fields = ['problem_image', 'root_cause_image', 'temporary_action_image', 'permanent_action_image', 'evidence'];
                    foreach ($fields as $field) {
                        if (isset($problem->$field) && file_exists(public_path($problem->$field))) {
                            @unlink(public_path($problem->$field));
                        }
                    }
                    DB::table('KPICompanyActivityProblem')
                        ->where('kpi_company_activity_id', $dbId)
                        ->delete();
                }
            }

            $this->recalculateAllMonths($activity->kpi_company_id);

            DB::commit();

            return redirect()
                ->to(route('kpi.company.detail', self::encodeId($activity->kpi_company_id)))
                ->with('success', 'Monthly performance and problem solving details updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update: ' . $e->getMessage());
        }
    }

    public function cancelActivity(string $id)
    {
        $dbId = self::decodeId($id);
        if (!$dbId) {
            return redirect()->back()->with('error', 'Invalid Activity ID.');
        }

        try {
            DB::beginTransaction();

            $activity = DB::table('KPICompanyActivity')->where('id', $dbId)->first();
            if (!$activity) {
                return redirect()->back()->with('error', 'Activity record not found.');
            }

            // Reset actual, status, calc_operator and all component values (comp_1 to comp_20)
            $updateData = [
                'calc_operator' => null,
                'actual' => null,
                'status' => null,
                'updated_at' => Carbon::now()
            ];
            for ($i = 1; $i <= 20; $i++) {
                $updateData['comp_' . $i] = null;
            }

            DB::table('KPICompanyActivity')
                ->where('id', $dbId)
                ->update($updateData);

            // Delete associated problem record if any
            $problem = DB::table('KPICompanyActivityProblem')
                ->where('kpi_company_activity_id', $dbId)
                ->first();
            if ($problem) {
                $fields = ['problem_image', 'root_cause_image', 'temporary_action_image', 'permanent_action_image', 'evidence'];
                foreach ($fields as $field) {
                    if (isset($problem->$field) && file_exists(public_path($problem->$field))) {
                        @unlink(public_path($problem->$field));
                    }
                }
                DB::table('KPICompanyActivityProblem')
                    ->where('kpi_company_activity_id', $dbId)
                    ->delete();
            }

            DB::commit();

            return redirect()
                ->to(route('kpi.company.detail', self::encodeId($activity->kpi_company_id)))
                ->with('info', 'Activity entry has been canceled.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Failed to cancel: ' . $e->getMessage());
        }
    }

    public function saveCalcOperator(Request $request)
    {
        $request->validate([
            'activity_id' => 'required|string',
            'calc_operator' => 'nullable|string|max:500',
        ]);

        $dbId = self::decodeId($request->activity_id);
        if (!$dbId) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Activity ID.'
            ], 400);
        }

        try {
            $activity = DB::table('KPICompanyActivity')->where('id', $dbId)->first();
            if (!$activity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Activity not found.'
                ], 404);
            }

            if (empty($request->calc_operator)) {
                DB::table('KPICompanyActivity')
                    ->where('id', $dbId)
                    ->update([
                        'calc_operator' => null,
                        'actual' => null,
                        'status' => null,
                        'updated_at' => \Carbon\Carbon::now()
                    ]);
            } else {
                DB::table('KPICompanyActivity')
                    ->where('id', $dbId)
                    ->update([
                        'calc_operator' => $request->calc_operator,
                        'updated_at' => \Carbon\Carbon::now()
                    ]);
            }

            $this->recalculateAllMonths($activity->kpi_company_id);

            return response()->json([
                'success' => true,
                'message' => 'Calculation operator updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function recalculateAllMonths($kpiCompanyId)
    {
        $activities = DB::table('KPICompanyActivity')
            ->where('kpi_company_id', $kpiCompanyId)
            ->orderBy('id', 'asc')
            ->get();

        $kpi = DB::table('KPICompany as child')
            ->leftJoin('KPIList as parent', 'child.kpi_list_id', '=', 'parent.id')
            ->select('child.*', 'parent.operator', 'parent.target', 'parent.unit')
            ->where('child.id', $kpiCompanyId)
            ->first();

        if (!$kpi) return;

        $formula = DB::table('KPIFormula')->where('kpi_list_id', $kpi->kpi_list_id)->first();
        if (!$formula) return;

        $targetVal = $this->parseLocalNumber($kpi->target);
        $operator = trim(htmlspecialchars_decode($kpi->operator));

        foreach ($activities as $activity) {
            $op = !empty($activity->calc_operator) ? $activity->calc_operator : ($formula ? $formula->calc_operator : null);
            
            $vals = [];
            $hasSomeComponentValue = false;
            for ($i = 1; $i <= 20; $i++) {
                $col = 'comp_' . $i;
                if ($formula && !empty($formula->$col)) {
                    $compVal = $activity->$col;
                    if ($compVal !== null && $compVal !== '') {
                        $vals[] = $this->parseLocalNumber($compVal);
                        $hasSomeComponentValue = true;
                    }
                }
            }

            $calculatedActual = null;

            if (!empty($op)) {
                if (strpos($op, '[') !== false) {
                    $expr = $op;
                    // Auto-prefix master style [comp_X] with current month name
                    $expr = preg_replace('/\[(comp_\d+)\]/', '[' . $activity->bulan . '.$1]', $expr);
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
                        $val = $this->parseLocalNumber($compVal);
                        $expr = str_replace($match[0], $val, $expr);
                    }
                    if ($hasAnyFormulaComponentValue) {
                        $expr = str_replace(['x', 'X'], '*', $expr);
                        $exprClean = preg_replace('/[^0-9\+\-\*\/\(\)\.\s]/', '', $expr);
                        if (!empty($exprClean)) {
                            try {
                                $calculatedActual = @eval("return ({$exprClean});");
                                $unit = $activity->unit ?? ($kpi->unit ?? '');
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
                    $unit = $activity->unit ?? ($kpi->unit ?? '');
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
            } elseif ($hasSomeComponentValue) {
                // Fallback: default to sum if no formula is set but values exist
                $calculatedActual = array_sum($vals);
            }

            if ($calculatedActual !== null) {
                $isAchieved = false;
                switch ($operator) {
                    case '>=': $isAchieved = ($calculatedActual >= $targetVal); break;
                    case '<=': $isAchieved = ($calculatedActual <= $targetVal); break;
                    case '>':  $isAchieved = ($calculatedActual > $targetVal); break;
                    case '<':  $isAchieved = ($calculatedActual < $targetVal); break;
                    case '=':
                    default:   $isAchieved = ($calculatedActual == $targetVal); break;
                }
                $status = $isAchieved ? 'Achieved' : 'Not Achieved';

                DB::table('KPICompanyActivity')
                    ->where('id', $activity->id)
                    ->update([
                        'actual' => number_format((float)$calculatedActual, 2, ',', ''),
                        'status' => $status,
                        'updated_at' => \Carbon\Carbon::now()
                    ]);
            }
        }
    }

    private function parseLocalNumber($val)
    {
        if ($val === null || $val === '') {
            return 0.0;
        }
        $val = trim($val);
        if (strpos($val, ',') !== false) {
            $val = str_replace('.', '', $val);
            $val = str_replace(',', '.', $val);
        } else {
            if (substr_count($val, '.') > 1) {
                $val = str_replace('.', '', $val);
            }
        }
        return (float) filter_var($val, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }
}
