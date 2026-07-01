<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\Models\UserMenuPermission;

class InternalAuditController extends Controller
{
    public function index()
    {
        $departments = DB::table('GenbaDept')
            ->where('CheckBox01', 1)
            ->get()
            ->map(function ($item) {
                return (object)[
                    'Key1' => $item->Key1,
                    'Desc' => $item->Desc,
                    'id' => $item->Key1,
                    'name' => $item->Desc
                ];
            });
        return view('activity.internal_audit', compact('departments'));
    }

    public function actionReport()
    {
        return view('activity.internal_action_report');
    }

    public function actionReportPreview($id)
    {
        try {
            $carId = $this->decryptCarId($id);
            $car = null;

            if ($carId) {
                $car = DB::table('CsAuditCar as a')
                    ->leftJoin('CsAuditDetail as b', 'b.id', '=', 'a.audit_detail_id')
                    ->leftJoin('CsAuditHeader as c', 'c.id', '=', 'b.audit_header_id')
                    ->where('a.id', $carId)
                    ->select(
                        'a.*', 
                        'b.checksheet_item_id', 
                        'c.hash_id as schedule_hash_id', 
                        'c.audit_type',
                        'c.auditee as header_auditee',
                        'b.evidence', 
                        'b.finding_photo_path'
                    )
                    ->first();
            }

            // Fallback for database hash_id, legacy Crypt, or direct ID lookup
            if (!$car) {
                $car = DB::table('CsAuditCar as a')
                    ->leftJoin('CsAuditDetail as b', 'b.id', '=', 'a.audit_detail_id')
                    ->leftJoin('CsAuditHeader as c', 'c.id', '=', 'b.audit_header_id')
                    ->where('a.hash_id', $id)
                    ->select(
                        'a.*', 
                        'b.checksheet_item_id', 
                        'c.hash_id as schedule_hash_id', 
                        'c.audit_type',
                        'c.auditee as header_auditee',
                        'b.evidence', 
                        'b.finding_photo_path'
                    )
                    ->first();
            }

            if (!$car) {
                try {
                    $decryptedId = Crypt::decryptString($id);
                    $carId = explode('_', $decryptedId)[0];
                } catch (\Exception $e) {
                    $carId = $id;
                }

                $car = DB::table('CsAuditCar as a')
                    ->leftJoin('CsAuditDetail as b', 'b.id', '=', 'a.audit_detail_id')
                    ->leftJoin('CsAuditHeader as c', 'c.id', '=', 'b.audit_header_id')
                    ->where('a.id', $carId)
                    ->select(
                        'a.*', 
                        'b.checksheet_item_id', 
                        'c.hash_id as schedule_hash_id', 
                        'c.audit_type',
                        'c.auditee as header_auditee',
                        'b.evidence', 
                        'b.finding_photo_path'
                    )
                    ->first();
            }

            if (!$car) {
                return redirect()->route('internal_audit.action_report')->with('error', 'CAR Action Report not found.');
            }

            $car->formatted_date = $car->created_at ? Carbon::parse($car->created_at)->format('d F Y') : '-';

            // Auto-fill due_date if missing
            if (empty($car->due_date)) {
                $schedule = DB::table('CsAuditHeader')
                    ->join('CsAuditDetail as d', 'd.audit_header_id', '=', 'CsAuditHeader.id')
                    ->where('d.id', $car->audit_detail_id)
                    ->select('CsAuditHeader.audit_date', 'CsAuditHeader.schedule_date')
                    ->first();
                $auditDate = $schedule->audit_date ?? $schedule->schedule_date ?? null;
                if ($auditDate) {
                    $autoDueDate = Carbon::parse($auditDate)->addWeeks(2)->toDateString();
                    DB::table('CsAuditCar')->where('id', $car->id)->update([
                        'due_date' => $autoDueDate,
                        'updated_at' => Carbon::now()
                    ]);
                    $car->due_date = $autoDueDate;
                }
            }

            $action = DB::table('CsAuditAction')->where('audit_car_id', $car->id)->first();

            return view('activity.internal_action_preview', compact('car', 'action'));
        } catch (\Exception $e) {
            return redirect()->route('internal_audit.action_report')->with('error', $e->getMessage());
        }
    }

    public function verification()
    {
        $departments = DB::table('GenbaDept')->orderBy('Key1', 'asc')->pluck('Key1');
        
        $superiorCount = DB::table('CsAuditCar as a')
            ->join('CsAuditAction as d', 'd.audit_car_id', '=', 'a.id')
            ->where('d.action_status', 'open_verif')
            ->where('a.status', 'Under Review')
            ->count();

        $auditorCount = DB::table('CsAuditCar as a')
            ->join('CsAuditAction as d', 'd.audit_car_id', '=', 'a.id')
            ->where('d.action_status', 'approve_superior')
            ->where('a.status', 'Need Verification')
            ->count();

        $closedCount = DB::table('CsAuditCar as a')
            ->join('CsAuditAction as d', 'd.audit_car_id', '=', 'a.id')
            ->where('d.action_status', 'verified')
            ->where('a.status', 'Closed')
            ->count();

        return view('approvals.verifkasi_internal_audit', compact('departments', 'superiorCount', 'auditorCount', 'closedCount'));
    }

    public function verificationTable(Request $request)
    {
        $role = $request->role ?? 'superior';

        $query = DB::table('CsAuditCar as a')
            ->join('CsAuditAction as d', 'd.audit_car_id', '=', 'a.id')
            ->leftJoin('CsAuditDetail as b', 'b.id', '=', 'a.audit_detail_id')
            ->leftJoin('CsAuditHeader as c', 'c.id', '=', 'b.audit_header_id');

        // Apply role filter (show all CARs at this stage)
        if ($role === 'superior') {
            $query->where('d.action_status', 'open_verif')
                  ->where('a.status', 'Under Review');
        } elseif ($role === 'auditor') {
            $query->where('d.action_status', 'approve_superior')
                  ->where('a.status', 'Need Verification');
        } elseif ($role === 'closed') {
            $query->where('d.action_status', 'verified')
                  ->where('a.status', 'Closed');
        }

        $totalRecords = $query->count();

        // Search
        if ($request->has('search') && !empty($request->search['value'])) {
            $searchValue = $request->search['value'];
            $query->where(function ($q) use ($searchValue) {
                $q->where('a.req_number', 'LIKE', "%{$searchValue}%")
                    ->orWhere('a.department', 'LIKE', "%{$searchValue}%")
                    ->orWhere('a.external', 'LIKE', "%{$searchValue}%")
                    ->orWhere('a.finding', 'LIKE', "%{$searchValue}%");
            });
        }

        // Apply filters
        if ($request->filled('date_from')) {
            $query->whereDate('a.created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('a.created_at', '<=', $request->date_to);
        }
        if ($request->filled('dept')) {
            $query->where('a.department', $request->dept);
        }

        $filteredRecords = $query->count();

        // Pagination
        if ($request->has('start') && $request->has('length')) {
            $query->skip($request->start)->take($request->length);
        }

        $data = $query->select('a.*', 'd.action_status', 'd.id as action_id', 'd.auditee_superior_name', 'c.auditee as header_auditee')
            ->orderBy('a.id', 'desc')
            ->get();

        $response = [
            "draw" => intval($request->draw),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $filteredRecords,
            "data" => $data->map(function ($item, $key) use ($request) {
                $start = $request->start ?? 0;
                
                $isApproved = !empty($item->qmr_approved_at) || $item->status === 'Closed';
                
                $encryptedId = $this->encryptCarId($item->id);
                $rowNo = $start + $key + 1;
                $previewBtn = '
                    <button type="button" title="Preview" class="w-9 h-9 flex items-center justify-center rounded-lg bg-blue-50 text-blue-500 hover:bg-blue-100 hover:text-blue-600 transition-all duration-200 border border-blue-200" id="btn_form_view_doc_' . $rowNo . '" onclick="previewCar(\'' . $encryptedId . '\')">
                        <span id="svg_form_view_doc_' . $rowNo . '" class="flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500" viewBox="0 0 24 24" fill="none">
                                <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="currentColor"></path>
                                <path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="currentColor"></path>
                                <path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="currentColor"></path>
                            </svg>
                        </span>
                        <span id="spinner_form_view_doc_' . $rowNo . '" class="hidden animate-spin rounded-full h-4 w-4 border-b-2 border-current"></span>
                    </button>';

                $user = Auth::user();
                $canAction = false;
                $role = $request->role ?? 'superior';

                if ($role === 'superior') {
                    if (!empty($item->auditee_superior_name) && strcasecmp(trim($user->full_name), trim($item->auditee_superior_name)) === 0) {
                        $canAction = true;
                    }
                } elseif ($role === 'auditor') {
                    if (!empty($item->auditor)) {
                        $auditors = array_map('trim', explode(',', $item->auditor));
                        foreach ($auditors as $auditorName) {
                            if (strcasecmp(trim($user->full_name), $auditorName) === 0) {
                                $canAction = true;
                                break;
                            }
                        }
                    }
                }

                $isUserAuditor = false;
                if (!empty($item->auditor)) {
                    $auditors = array_map('trim', explode(',', $item->auditor));
                    foreach ($auditors as $auditorName) {
                        if (strcasecmp(trim($user->full_name), $auditorName) === 0) {
                            $isUserAuditor = true;
                            break;
                        }
                    }
                }

                $isUserSuperior = !empty($item->auditee_superior_name) && strcasecmp(trim($user->full_name), trim($item->auditee_superior_name)) === 0;

                $actionBtn = '';
                if ($isApproved) {
                    if ($isUserAuditor) {
                        $actionBtn = '
                            <div class="flex items-center justify-start gap-2">
                                ' . $previewBtn . '
                                <button type="button" onclick="rollbackCarAction(' . $item->id . ')" class="w-9 h-9 flex items-center justify-center rounded-lg bg-amber-50 text-amber-600 border border-amber-200 hover:bg-amber-100 hover:text-amber-700 transition-all duration-200" title="Rollback Approval">
                                    <i class="fa-solid fa-rotate-left text-sm"></i>
                                </button>
                            </div>';
                    } else {
                        $actionBtn = '
                            <div class="flex items-center justify-start gap-2">
                                ' . $previewBtn . '
                            </div>';
                    }
                } elseif ($canAction) {
                    if ($role === 'superior') {
                        $actionBtn = '
                            <div class="flex items-center justify-start gap-2">
                                ' . $previewBtn . '
                                <button type="button" onclick="approveCarAction(' . $item->id . ')" class="w-9 h-9 flex items-center justify-center rounded-lg bg-green-50 text-green-600 border border-green-200 hover:bg-green-100 hover:text-green-700 transition-all duration-200" title="Approve">
                                    <i class="fa-solid fa-check text-sm"></i>
                                </button>
                                <button type="button" onclick="rejectCarAction(' . $item->id . ')" class="w-9 h-9 flex items-center justify-center rounded-lg bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 hover:text-red-700 transition-all duration-200" title="Reject to Draft">
                                    <i class="fa-solid fa-xmark text-sm"></i>
                                </button>
                            </div>';
                    } else {
                        $actionBtn = '
                            <div class="flex items-center justify-start gap-2">
                                ' . $previewBtn . '
                            </div>';
                    }
                } else {
                    if ($role === 'auditor' && $isUserSuperior) {
                        $actionBtn = '
                            <div class="flex items-center justify-start gap-2">
                                ' . $previewBtn . '
                                <button type="button" onclick="rollbackCarAction(' . $item->id . ')" class="w-9 h-9 flex items-center justify-center rounded-lg bg-amber-50 text-amber-600 border border-amber-200 hover:bg-amber-100 hover:text-amber-700 transition-all duration-200" title="Rollback Approval">
                                    <i class="fa-solid fa-rotate-left text-sm"></i>
                                </button>
                            </div>';
                    } else {
                        $actionBtn = '
                            <div class="flex items-center justify-start gap-2">
                                ' . $previewBtn . '
                            </div>';
                    }
                }

                return [
                    "no" => $start + $key + 1,
                    "id" => $item->id,
                    "req_number" => $item->req_number,
                    "external" => $item->external,
                    "department" => $item->department,
                    "finding" => $item->finding,
                    "clause_title" => $item->clause_title ?? '-',
                    "due_date" => $item->due_date ? \Carbon\Carbon::parse($item->due_date)->format('d M Y') : '-',
                    "finding_category" => $item->finding_category,
                    "auditor" => $item->auditor ?? '-',
                    "auditee" => $item->header_auditee ?? $item->auditee ?? '-',
                    "superior" => $item->auditee_superior_name ?? '-',
                    "action_status" => $item->action_status,
                    "action" => $actionBtn
                ];
            }),
            "superiorCount" => DB::table('CsAuditCar as a')
                ->join('CsAuditAction as d', 'd.audit_car_id', '=', 'a.id')
                ->where('d.action_status', 'open_verif')
                ->where('a.status', 'Under Review')
                ->count(),
            "auditorCount" => DB::table('CsAuditCar as a')
                ->join('CsAuditAction as d', 'd.audit_car_id', '=', 'a.id')
                ->where('d.action_status', 'approve_superior')
                ->where('a.status', 'Need Verification')
                ->count(),
            "closedCount" => DB::table('CsAuditCar as a')
                ->join('CsAuditAction as d', 'd.audit_car_id', '=', 'a.id')
                ->where('d.action_status', 'verified')
                ->where('a.status', 'Closed')
                ->count()
        ];

        return response()->json($response);
    }

    public function rollbackCar(Request $request)
    {
        $request->validate([
            'car_id' => 'required|integer',
        ]);

        try {
            $user = Auth::user();
            
            $car = DB::table('CsAuditCar')->where('id', $request->car_id)->first();
            if (!$car) {
                return response()->json(['success' => false, 'message' => 'CAR not found.']);
            }

            $action = DB::table('CsAuditAction')->where('audit_car_id', $request->car_id)->first();
            if (!$action) {
                return response()->json(['success' => false, 'message' => 'Action Plan not found for this CAR.']);
            }

            $carStatus = $car->status ?? 'Under Review';

            if ($carStatus === 'Closed') {
                // Verify that the user is the Auditor
                $isAuditor = false;
                if (!empty($car->auditor)) {
                    $auditors = array_map('trim', explode(',', $car->auditor));
                    foreach ($auditors as $auditorName) {
                        if (strcasecmp(trim($user->full_name), $auditorName) === 0) {
                            $isAuditor = true;
                            break;
                        }
                    }
                }

                if (!$isAuditor) {
                    return response()->json(['success' => false, 'message' => 'Only the designated Auditor (' . ($car->auditor ?? '-') . ') is allowed to rollback this closed CAR.']);
                }

                DB::table('CsAuditCar')
                    ->where('id', $request->car_id)
                    ->update([
                        'status' => 'Need Verification',
                        'updated_at' => Carbon::now()
                    ]);

                DB::table('CsAuditAction')
                    ->where('audit_car_id', $request->car_id)
                    ->update([
                        'action_status' => 'approve_superior',
                        'updated_at' => Carbon::now()
                    ]);

                return response()->json(['success' => true, 'message' => 'CAR Action Report rolled back to Auditor verification queue successfully.']);
            } elseif ($carStatus === 'Need Verification') {
                // Verify that the user is the Auditee Superior
                if (empty($action->auditee_superior_name) || strcasecmp(trim($user->full_name), trim($action->auditee_superior_name)) !== 0) {
                    return response()->json(['success' => false, 'message' => 'Only the Auditee Superior (' . ($action->auditee_superior_name ?? '-') . ') is allowed to rollback approval at this stage.']);
                }

                DB::table('CsAuditCar')
                    ->where('id', $request->car_id)
                    ->update([
                        'status' => 'Under Review',
                        'updated_at' => Carbon::now()
                    ]);

                DB::table('CsAuditAction')
                    ->where('audit_car_id', $request->car_id)
                    ->update([
                        'action_status' => 'open_verif',
                        'updated_at' => Carbon::now()
                    ]);

                return response()->json(['success' => true, 'message' => 'CAR Action Report rolled back to Superior review stage successfully.']);
            } else {
                return response()->json(['success' => false, 'message' => 'Rollback is not allowed at the current CAR stage (' . $carStatus . ').']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function rejectCar(Request $request)
    {
        $request->validate([
            'car_id' => 'required|integer',
            'notes' => 'nullable|string'
        ]);

        try {
            $user = Auth::user();
            $car = DB::table('CsAuditCar')->where('id', $request->car_id)->first();
            if (!$car) {
                return response()->json(['success' => false, 'message' => 'CAR not found.']);
            }

            $action = DB::table('CsAuditAction')->where('audit_car_id', $request->car_id)->first();
            if (!$action) {
                return response()->json(['success' => false, 'message' => 'Action Plan not found for this CAR.']);
            }

            $carStatus = $car->status ?? 'Under Review';

            if ($carStatus === 'Under Review') {
                if (empty($action->auditee_superior_name) || strcasecmp(trim($user->full_name), trim($action->auditee_superior_name)) !== 0) {
                    return response()->json(['success' => false, 'message' => 'Only the Auditee Superior (' . ($action->auditee_superior_name ?? '-') . ') is allowed to reject at this stage.']);
                }
            } elseif ($carStatus === 'Need Verification') {
                $isAuditor = false;
                if (!empty($car->auditor)) {
                    $auditors = array_map('trim', explode(',', $car->auditor));
                    foreach ($auditors as $auditorName) {
                        if (strcasecmp(trim($user->full_name), $auditorName) === 0) {
                            $isAuditor = true;
                            break;
                        }
                    }
                }

                if (!$isAuditor) {
                    return response()->json(['success' => false, 'message' => 'Only the designated Auditor (' . ($car->auditor ?? '-') . ') is allowed to reject at this stage.']);
                }
            } else {
                return response()->json(['success' => false, 'message' => 'Rejection is not allowed at the current CAR stage (' . $carStatus . ').']);
            }

            DB::beginTransaction();

            $actionUpdate = [
                'action_status' => 'draft',
                'updated_at' => Carbon::now()
            ];
            if ($request->exists('notes')) {
                $actionUpdate['notes'] = $request->notes;
            }

            DB::table('CsAuditAction')
                ->where('audit_car_id', $request->car_id)
                ->update($actionUpdate);

            DB::table('CsAuditCar')
                ->where('id', $request->car_id)
                ->update([
                    'status' => 'Draft',
                    'updated_at' => Carbon::now()
                ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'CAR Action Plan rejected and returned to draft successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function saveActionReportDetails($id, Request $request)
    {
        try {
            $carId = $this->decryptCarId($id);
            if (!$carId) {
                $car = DB::table('CsAuditCar')->where('hash_id', $id)->first();
                if (!$car) {
                    try {
                        $decryptedId = Crypt::decryptString($id);
                        $carId = explode('_', $decryptedId)[0];
                    } catch (\Exception $e) {
                        $carId = $id;
                    }
                } else {
                    $carId = $car->id;
                }
            }

            $car = DB::table('CsAuditCar')->where('id', $carId)->first();
            if (!$car) {
                return redirect()->route('internal_audit.action_report')->with('error', 'CAR Action Report not found.');
            }

             $request->validate([
                  'why_one' => 'required|string',
                  'why_two' => 'required|string',
                  'why_three' => 'required|string',
                  'why_four' => 'nullable|string',
                  'why_five' => 'nullable|string',
                  'root_cause' => 'required|string',
                  'analyzed_by' => 'required|string',
                  'corrective_action_one' => 'required|string',
                  'corrective_action_two' => 'required|string',
                  'corrective_action_three' => 'required|string',
                  'preventive_action_one' => 'required|string',
                  'preventive_action_two' => 'required|string',
                  'preventive_action_three' => 'required|string',
                  'notes' => 'nullable|string',
                  'auditee_name' => 'nullable|string',
                  'auditee_superior_name' => 'nullable|string',
                  'corrective_photo_one' => 'nullable|array',
                  'corrective_photo_one.*' => 'file|mimes:jpg,jpeg,png,pdf|max:10240',
                  'corrective_photo_two' => 'nullable|array',
                  'corrective_photo_two.*' => 'file|mimes:jpg,jpeg,png,pdf|max:10240',
                  'corrective_photo_three' => 'nullable|array',
                  'corrective_photo_three.*' => 'file|mimes:jpg,jpeg,png,pdf|max:10240',
                  'preventive_photo_one' => 'nullable|array',
                  'preventive_photo_one.*' => 'file|mimes:jpg,jpeg,png,pdf|max:10240',
                  'preventive_photo_two' => 'nullable|array',
                  'preventive_photo_two.*' => 'file|mimes:jpg,jpeg,png,pdf|max:10240',
                  'preventive_photo_three' => 'nullable|array',
                  'preventive_photo_three.*' => 'file|mimes:jpg,jpeg,png,pdf|max:10240',
                  'existing_corrective_photo_one' => 'nullable|string',
                  'existing_corrective_photo_two' => 'nullable|string',
                  'existing_corrective_photo_three' => 'nullable|string',
                  'existing_preventive_photo_one' => 'nullable|string',
                  'existing_preventive_photo_two' => 'nullable|string',
                  'existing_preventive_photo_three' => 'nullable|string',
             ]);

             $fields = [
                 'corrective_path_one' => ['file' => 'corrective_photo_one', 'existing' => 'existing_corrective_photo_one', 'prefix' => 'evidence_corr_one_'],
                 'corrective_path_two' => ['file' => 'corrective_photo_two', 'existing' => 'existing_corrective_photo_two', 'prefix' => 'evidence_corr_two_'],
                 'corrective_path_three' => ['file' => 'corrective_photo_three', 'existing' => 'existing_corrective_photo_three', 'prefix' => 'evidence_corr_three_'],
                 'preventive_path_one' => ['file' => 'preventive_photo_one', 'existing' => 'existing_preventive_photo_one', 'prefix' => 'evidence_prev_one_'],
                 'preventive_path_two' => ['file' => 'preventive_photo_two', 'existing' => 'existing_preventive_photo_two', 'prefix' => 'evidence_prev_two_'],
                 'preventive_path_three' => ['file' => 'preventive_photo_three', 'existing' => 'existing_preventive_photo_three', 'prefix' => 'evidence_prev_three_'],
             ];

             $existingAction = DB::table('CsAuditAction')->where('audit_car_id', $car->id)->first();
             $photoPaths = [];

             foreach ($fields as $col => $info) {
                 // 1. Parse retained existing paths
                 $retainedStr = $request->input($info['existing'], '');
                 $retainedPaths = array_filter(array_map('trim', explode(',', $retainedStr)));

                 // 2. Identify and delete files that were removed by the user
                 if ($existingAction && !empty($existingAction->$col)) {
                     $dbPaths = array_filter(array_map('trim', explode(',', $existingAction->$col)));
                     foreach ($dbPaths as $dbPath) {
                         if (!in_array($dbPath, $retainedPaths)) {
                             $filePath = public_path($dbPath);
                             if (file_exists($filePath) && is_file($filePath)) {
                                 @unlink($filePath);
                             }
                         }
                     }
                 }

                 // 3. Process newly uploaded files
                 $uploadedPaths = [];
                 if ($request->hasFile($info['file'])) {
                     $files = $request->file($info['file']);
                     if (!is_array($files)) {
                         $files = [$files];
                     }
                     foreach ($files as $file) {
                         if ($file && $file->isValid()) {
                             $fileName = $info['prefix'] . uniqid() . '.' . $file->getClientOriginalExtension();
                             $file->move(public_path('evidence-photo'), $fileName);
                             $uploadedPaths[] = 'evidence-photo/' . $fileName;
                         }
                     }
                 }

                 // 4. Combine retained and newly uploaded paths
                 $finalPaths = array_merge($retainedPaths, $uploadedPaths);
                 $photoPaths[$col] = !empty($finalPaths) ? implode(',', $finalPaths) : null;
             }

             $corrPathsList = array_filter([$photoPaths['corrective_path_one'], $photoPaths['corrective_path_two'], $photoPaths['corrective_path_three']]);
             $prevPathsList = array_filter([$photoPaths['preventive_path_one'], $photoPaths['preventive_path_two'], $photoPaths['preventive_path_three']]);
             
             $legacyCorrectivePath = implode(',', $corrPathsList);
             $legacyPreventivePath = implode(',', $prevPathsList);

             $existingAction = DB::table('CsAuditAction')->where('audit_car_id', $car->id)->first();
             if ($existingAction) {
                 DB::table('CsAuditAction')
                     ->where('id', $existingAction->id)
                     ->update([
                         'why_one' => $request->why_one,
                         'why_two' => $request->why_two,
                         'why_three' => $request->why_three,
                         'why_four' => $request->why_four,
                         'why_five' => $request->why_five,
                         'root_cause' => $request->root_cause,
                         'analyzed_by' => $request->analyzed_by,
                         'corrective_action_one' => $request->corrective_action_one,
                         'corrective_action_two' => $request->corrective_action_two,
                         'corrective_action_three' => $request->corrective_action_three,
                         'corrective_path' => $legacyCorrectivePath,
                         'corrective_path_one' => $photoPaths['corrective_path_one'],
                         'corrective_path_two' => $photoPaths['corrective_path_two'],
                         'corrective_path_three' => $photoPaths['corrective_path_three'],
                         'preventive_action_one' => $request->preventive_action_one,
                         'preventive_action_two' => $request->preventive_action_two,
                         'preventive_action_three' => $request->preventive_action_three,
                         'preventive_path' => $legacyPreventivePath,
                         'preventive_path_one' => $photoPaths['preventive_path_one'],
                         'preventive_path_two' => $photoPaths['preventive_path_two'],
                         'preventive_path_three' => $photoPaths['preventive_path_three'],
                         'notes' => $request->notes,
                         'auditee_name' => $request->auditee_name,
                         'auditee_superior_name' => $request->auditee_superior_name,
                         'action_status' => 'open_verif',
                         'updated_at' => Carbon::now()
                     ]);
             } else {
                 DB::table('CsAuditAction')->insert([
                     'audit_car_id' => $car->id,
                     'why_one' => $request->why_one,
                     'why_two' => $request->why_two,
                     'why_three' => $request->why_three,
                     'why_four' => $request->why_four,
                     'why_five' => $request->why_five,
                     'root_cause' => $request->root_cause,
                     'analyzed_by' => $request->analyzed_by,
                     'corrective_action_one' => $request->corrective_action_one,
                     'corrective_action_two' => $request->corrective_action_two,
                     'corrective_action_three' => $request->corrective_action_three,
                     'corrective_path' => $legacyCorrectivePath,
                     'corrective_path_one' => $photoPaths['corrective_path_one'],
                     'corrective_path_two' => $photoPaths['corrective_path_two'],
                     'corrective_path_three' => $photoPaths['corrective_path_three'],
                     'preventive_action_one' => $request->preventive_action_one,
                     'preventive_action_two' => $request->preventive_action_two,
                     'preventive_action_three' => $request->preventive_action_three,
                     'preventive_path' => $legacyPreventivePath,
                     'preventive_path_one' => $photoPaths['preventive_path_one'],
                     'preventive_path_two' => $photoPaths['preventive_path_two'],
                     'preventive_path_three' => $photoPaths['preventive_path_three'],
                     'notes' => $request->notes,
                     'auditee_name' => $request->auditee_name,
                     'auditee_superior_name' => $request->auditee_superior_name,
                     'action_status' => 'open_verif',
                     'created_at' => Carbon::now(),
                     'updated_at' => Carbon::now()
                 ]);
             }

            // Update CAR status to Under Review
            DB::table('CsAuditCar')->where('id', $car->id)->update([
                'status' => 'Under Review',
                'updated_at' => Carbon::now()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Action Plan details saved successfully.'
                ]);
            }

            return redirect()->route('internal_audit.action_report.preview', $id)->with('toast_success', 'Action Plan details saved successfully.');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function rollbackActionPlan($id, Request $request)
    {
        try {
            $carId = $this->decryptCarId($id);
            if (!$carId) {
                $car = DB::table('CsAuditCar')->where('hash_id', $id)->first();
                if (!$car) {
                    try {
                        $decryptedId = Crypt::decryptString($id);
                        $carId = explode('_', $decryptedId)[0];
                    } catch (\Exception $e) {
                        $carId = $id;
                    }
                } else {
                    $carId = $car->id;
                }
            }

            $car = DB::table('CsAuditCar')->where('id', $carId)->first();
            if (!$car) {
                return redirect()->route('internal_audit.action_report')->with('error', 'CAR Action Report not found.');
            }

            $user = Auth::user();
            $carStatus = $car->status ?? '';

            if ($carStatus === 'Closed') {
                $isAuditor = false;
                if (!empty($car->auditor)) {
                    $auditors = array_map('trim', explode(',', $car->auditor));
                    foreach ($auditors as $auditorName) {
                        if (strcasecmp(trim($user->full_name), $auditorName) === 0) {
                            $isAuditor = true;
                            break;
                        }
                    }
                }

                if (!$isAuditor) {
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Only the designated Auditor (' . ($car->auditor ?? '-') . ') is allowed to rollback this closed CAR.'
                        ]);
                    }
                    return redirect()->route('internal_audit.action_report.preview', $id)->with('error', 'Only the designated Auditor (' . ($car->auditor ?? '-') . ') is allowed to rollback this closed CAR.');
                }
            } elseif ($carStatus === 'Under Review') {
                $isAuditee = false;
                if (!empty($car->auditee)) {
                    $auditees = array_map('trim', explode(',', $car->auditee));
                    foreach ($auditees as $auditeeName) {
                        if (strcasecmp(trim($user->full_name), $auditeeName) === 0) {
                            $isAuditee = true;
                            break;
                        }
                    }
                }

                if (!$isAuditee) {
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Only the Auditee is allowed to edit the action plan at this stage.'
                        ]);
                    }
                    return redirect()->route('internal_audit.action_report.preview', $id)->with('error', 'Only the Auditee is allowed to edit the action plan at this stage.');
                }
            } else {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Rollback is not allowed at the current stage (' . $carStatus . ').'
                    ]);
                }
                return redirect()->route('internal_audit.action_report.preview', $id)->with('error', 'Rollback is not allowed at the current stage (' . $carStatus . ').');
            }

            DB::beginTransaction();

            if ($carStatus === 'Closed') {
                DB::table('CsAuditCar')
                    ->where('id', $car->id)
                    ->update([
                        'status' => 'Need Verification',
                        'updated_at' => Carbon::now()
                    ]);

                DB::table('CsAuditAction')
                    ->where('audit_car_id', $car->id)
                    ->update([
                        'action_status' => 'approve_superior',
                        'updated_at' => Carbon::now()
                    ]);
                $message = 'CAR Action Report has been rolled back to verification successfully.';
            } else {
                DB::table('CsAuditCar')
                    ->where('id', $car->id)
                    ->update([
                        'status' => 'Draft',
                        'updated_at' => Carbon::now()
                    ]);

                DB::table('CsAuditAction')
                    ->where('audit_car_id', $car->id)
                    ->update([
                        'action_status' => 'draft',
                        'updated_at' => Carbon::now()
                    ]);
                $message = 'CAR Action Report has been rolled back to draft successfully.';
            }

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            return redirect()->route('internal_audit.action_report.preview', $id)->with('toast_success', $message);
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function getSchedules(Request $request)
    {
        $query = DB::table('CsAuditHeader as a')
            ->select('a.*');

        if ($request->has('status_filter') && $request->input('status_filter') !== 'All') {
            $query->where('a.status', $request->input('status_filter'));
        }

        if ($request->has('search') && !empty($request->search['value'])) {
            $searchValue = $request->search['value'];
            $query->where(function($q) use ($searchValue) {
                $q->where('a.auditee', 'LIKE', "%{$searchValue}%")
                  ->orWhere('a.auditor_names', 'LIKE', "%{$searchValue}%")
                  ->orWhere('a.auditee_dept', 'LIKE', "%{$searchValue}%")
                  ->orWhere('a.audit_type', 'LIKE', "%{$searchValue}%");
            });
        }

        $totalData = DB::table('CsAuditHeader')->count();
        $totalFiltered = $query->count();

        $limit = $request->input('length', 10);
        $start = $request->input('start', 0);
        
        $posts = $query->offset($start)
            ->limit($limit)
            ->orderBy('a.audit_date', 'desc')
            ->get();

        $data = [];
        $no = $start;
        $hasDeletePermission = UserMenuPermission::canDelete(108);
        foreach ($posts as $post) {
            $no++;
            
            // Fetch department name
            $dept = DB::table('GenbaDept')->where('Key1', $post->auditee_dept)->first();
            $deptName = $dept ? $dept->Key1 : $post->auditee_dept;

            $action = '<div class="flex items-center justify-start gap-2">';
            if ($post->status === 'Scheduled') {
                $action .= '<button type="button" onclick="editAuditSchedule(\'' . $post->hash_id . '\')" class="w-10 h-10 flex items-center justify-center rounded-xl bg-blue-50 text-blue-500 hover:bg-blue-100 hover:text-blue-600 transition-all duration-200" title="Edit Agenda / Schedule">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="currentColor"></path>
                                    <path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="currentColor"></path>
                                    <path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="currentColor"></path>
                                </svg>
                            </button>';
            } else {
                $action .= '<button type="button" onclick="editAuditSchedule(\'' . $post->hash_id . '\')" class="w-10 h-10 flex items-center justify-center rounded-xl bg-blue-50 text-blue-500 hover:bg-blue-100 hover:text-blue-600 transition-all duration-200" title="Review Completed Audit">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="currentColor"></path>
                                    <path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="currentColor"></path>
                                    <path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="currentColor"></path>
                                </svg>
                            </button>';
            }
            if ($hasDeletePermission) {
                $action .= '<button type="button" onclick="deleteAuditSchedule(\'' . $post->hash_id . '\')" class="w-10 h-10 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-100 hover:text-red-600 transition-all duration-200" title="Delete Schedule">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-600" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"/>
                                    <path d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V7H5V5Z" fill="currentColor"/>
                                    <path d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"/>
                                </svg>
                            </button>';
            }
            $action .= '</div>';

            $statusText = $post->status;

            $auditors = array_filter(preg_split('/\s*[,&]\s*/', $post->auditor_names));
            $auditorHtml = '<div class="flex flex-wrap gap-1">';
            foreach ($auditors as $aud) {
                $auditorHtml .= '<span class="px-2 py-1 bg-white border border-slate-200 text-[12px] font-semibold text-slate-700 uppercase tracking-tight">' . trim($aud) . '</span>';
            }
            $auditorHtml .= '</div>';

            $data[] = [
                'no' => $no,
                'id' => $post->hash_id,
                'agenda_name' => $post->auditee,
                'schedule_date' => Carbon::parse($post->audit_date)->format('d M Y'),
                'audit_type' => $post->audit_type ?? '-',
                'auditor_niks' => $auditorHtml,
                'auditee_dept' => $deptName,
                'status' => $statusText,
                'action' => $action
            ];
        }

        return response()->json([
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        ]);
    }

    public function storeSchedule(Request $request)
    {
        $request->validate([
            'agenda_name' => 'required|string|max:1000',
            'schedule_date' => 'required|date',
            'auditor_niks' => 'required|string',
            'auditee_dept' => 'required|string',
            'audit_type' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $scheduleId = $request->schedule_id;

            if ($scheduleId) {
                // Update
                $schedule = DB::table('CsAuditHeader')->where('hash_id', $scheduleId)->first();
                if (!$schedule) {
                    return response()->json(['success' => false, 'message' => 'Schedule not found.']);
                }

                DB::table('CsAuditHeader')
                    ->where('hash_id', $scheduleId)
                    ->update([
                        'auditee' => $request->agenda_name,
                        'audit_date' => $request->schedule_date,
                        'auditor_names' => $request->auditor_niks,
                        'auditee_dept' => $request->auditee_dept,
                        'audit_type' => $request->audit_type,
                        'status' => 'Scheduled',
                        'updated_at' => Carbon::now()
                    ]);
                $hash = $scheduleId;
            } else {
                // Insert
                $hash = strtolower(\Illuminate\Support\Str::random(3) . '-' . \Illuminate\Support\Str::random(3) . '-' . \Illuminate\Support\Str::random(3));
                
                DB::table('CsAuditHeader')->insert([
                    'hash_id' => $hash,
                    'auditee' => $request->agenda_name,
                    'audit_date' => $request->schedule_date,
                    'auditor_names' => $request->auditor_niks,
                    'auditee_dept' => $request->auditee_dept,
                    'audit_type' => $request->audit_type,
                    'status' => 'Scheduled',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Schedule saved successfully.', 'schedule_id' => $hash]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function getScheduleDetail($id)
    {
        $schedule = DB::table('CsAuditHeader')->where('hash_id', $id)->first();
        if (!$schedule) {
            return response()->json(['success' => false, 'message' => 'Schedule not found.']);
        }

        $dept = DB::table('GenbaDept')->where('Key1', $schedule->auditee_dept)->first();
        $schedule->auditee_dept_name = $dept ? $dept->Key1 : $schedule->auditee_dept;

        // Override id to be hash_id for the form input
        $schedule->id = $schedule->hash_id;

        return response()->json([
            'success' => true,
            'schedule' => $schedule
        ]);
    }

    public function conduct($schedule_id)
    {
        $schedule = DB::table('CsAuditHeader')->where('hash_id', $schedule_id)->first();
        if (!$schedule) {
            abort(404, 'Schedule not found.');
        }

        // Map column names for compatibility with the conduct view
        $schedule->auditor_niks = $schedule->auditor_names;
        $schedule->schedule_date = $schedule->audit_date;

        // Fetch department description
        $dept = DB::table('GenbaDept')->where('Key1', $schedule->auditee_dept)->first();
        $schedule->auditee_dept_name = $dept ? $dept->Key1 : $schedule->auditee_dept;

        // Seed default checksheet items if empty
        $count = DB::table('CsChecksheetItem')->count();
        if ($count === 0) {
            DB::table('CsChecksheetItem')->insert([
                [
                    'check_item_idn' => 'Apakah peralatan produksi dipelihara dan dikalibrasi sesuai dengan spesifikasi pelanggan?',
                    'check_item_en' => 'Is the production equipment maintained and calibrated according to customer specifications?',
                    'department' => 'ICT',
                    'scope_id' => 1,
                    'scope_item' => 'Equipment calibration',
                    'is_active' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'check_item_idn' => 'Apakah ketertelusuran ditetapkan di seluruh lini perakitan dan pengepakan?',
                    'check_item_en' => 'Is traceability established throughout the assembly and packing lines?',
                    'department' => 'ICT',
                    'scope_id' => 1,
                    'scope_item' => 'Traceability',
                    'is_active' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'check_item_idn' => 'Apakah verifikasi penyetelan dilakukan menggunakan komponen representatif atau sampel batas?',
                    'check_item_en' => 'Are setup verifications conducted using representative parts or limit samples?',
                    'department' => 'ICT',
                    'scope_id' => 2,
                    'scope_item' => 'Setup verification',
                    'is_active' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
            ]);
        }

        $scheduleDepts = array_map('trim', explode(',', $schedule->auditee_dept ?? ''));
        $items = DB::table('CsChecksheetItem')
            ->where('is_active', 1)
            ->where('audit_type', $schedule->audit_type)
            ->where(function($q) use ($scheduleDepts) {
                foreach ($scheduleDepts as $dept) {
                    if ($dept) {
                        $q->orWhere('department', 'LIKE', '%' . $dept . '%');
                    }
                }
            })
            ->get();

        $details = DB::table('CsAuditDetail as d')
            ->leftJoin('CsAuditCar as c', 'c.audit_detail_id', '=', 'd.id')
            ->where('d.audit_header_id', $schedule->id)
            ->select('d.*', 'c.finding as car_finding')
            ->get()
            ->keyBy('checksheet_item_id');

        return view('activity.form-checksheet-intr.activity_intr_form', compact('schedule', 'items', 'details'));
    }

    public function getUsers(\Illuminate\Http\Request $request)
    {
        $search = $request->search;
        $page = $request->post('page', 1);
        $pageSize = 10;

        $query = DB::table('users');
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'LIKE', '%' . $search . '%')
                  ->orWhere('username', 'LIKE', '%' . $search . '%');
            });
        }
        $users = $query->paginate($pageSize, ['*'], 'page', $page);
        return response()->json([
            'items' => collect($users->items())->map(function ($user) {
                return [
                    'id' => $user->full_name,
                    'name' => $user->full_name
                ];
            })->values(),
            'pagination' => [
                'more' => $users->hasMorePages(),
            ]
        ]);
    }

    public function getChecksheet()
    {
        // Seed default checksheet items if empty
        $count = DB::table('CsChecksheetItem')->count();
        if ($count === 0) {
            DB::table('CsChecksheetItem')->insert([
                [
                    'check_item_idn' => 'Apakah peralatan produksi dipelihara dan dikalibrasi sesuai dengan spesifikasi pelanggan?',
                    'check_item_en' => 'Is the production equipment maintained and calibrated according to customer specifications?',
                    'department' => 'ICT',
                    'scope_id' => 1,
                    'scope_item' => 'Equipment calibration',
                    'is_active' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'check_item_idn' => 'Apakah ketertelusuran ditetapkan di seluruh lini perakitan dan pengepakan?',
                    'check_item_en' => 'Is traceability established throughout the assembly and packing lines?',
                    'department' => 'ICT',
                    'scope_id' => 1,
                    'scope_item' => 'Traceability',
                    'is_active' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'check_item_idn' => 'Apakah verifikasi penyetelan dilakukan menggunakan komponen representatif atau sampel batas?',
                    'check_item_en' => 'Are setup verifications conducted using representative parts or limit samples?',
                    'department' => 'ICT',
                    'scope_id' => 2,
                    'scope_item' => 'Setup verification',
                    'is_active' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
            ]);
        }

        $items = DB::table('CsChecksheetItem')->where('is_active', 1)->get();
        return response()->json($items);
    }

    public function submitAudit(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|integer',
            'audit_date' => 'required|date',
            'auditor_names' => 'required|string',
            'auditee_dept' => 'required|string',
            'results' => 'required|array'
        ]);

        try {
            DB::beginTransaction();

            $headerId = $request->schedule_id;
            DB::table('CsAuditHeader')
                ->where('id', $headerId)
                ->update([
                    'audit_date' => $request->audit_date,
                    'auditor_names' => $request->auditor_names,
                    'auditee_dept' => $request->auditee_dept,
                    'status' => 'Done',
                    'updated_at' => Carbon::now()
                ]);

            foreach ($request->results as $itemId => $itemData) {
                $judgment = $itemData['judgment'] ?? 'OK';
                $rawEvidence = $itemData['evidence'] ?? null;
                $evidence = null;
                $note = null;
                if ($judgment === 'OK' || $judgment === 'OFI') {
                    $note = $rawEvidence;
                }
                $photoPath = null;

                // Fetch existing detail for this checksheet item under this header
                $existingDetail = DB::table('CsAuditDetail')
                    ->where('audit_header_id', $headerId)
                    ->where('checksheet_item_id', $itemId)
                    ->first();

                // Handle base64 image upload if provided
                if (!empty($itemData['photo'])) {
                    $imageData = $itemData['photo'];
                    if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
                        $imageData = substr($imageData, strpos($imageData, ',') + 1);
                        $type = strtolower($type[1]); // jpg, png, etc

                        if (in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                            $imageData = base64_decode($imageData);
                            $fileName = 'finding_' . uniqid() . '.' . $type;
                            $publicPath = public_path('uploads/cs_audit/' . $fileName);
                            
                            if (!file_exists(public_path('uploads/cs_audit'))) {
                                mkdir(public_path('uploads/cs_audit'), 0777, true);
                            }
                            
                            file_put_contents($publicPath, $imageData);
                            $photoPath = 'uploads/cs_audit/' . $fileName;
                        }
                    } elseif (is_string($imageData) && strpos($imageData, 'uploads/cs_audit') !== false) {
                        // Preserve the existing photo path
                        $pos = strpos($imageData, 'uploads/cs_audit');
                        $photoPath = substr($imageData, $pos);
                    }
                }

                if ($existingDetail) {
                    DB::table('CsAuditDetail')
                        ->where('id', $existingDetail->id)
                        ->update([
                            'judgment' => $judgment,
                            'evidence' => $evidence,
                            'note' => $note,
                            'finding_photo_path' => $photoPath,
                            'updated_at' => Carbon::now()
                        ]);
                    $detailId = $existingDetail->id;
                } else {
                    $detailId = DB::table('CsAuditDetail')->insertGetId([
                        'audit_header_id' => $headerId,
                        'checksheet_item_id' => $itemId,
                        'judgment' => $judgment,
                        'evidence' => $evidence,
                        'note' => $note,
                        'finding_photo_path' => $photoPath,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                }

                // Create or update CAR if judgment is Minor or Mayor
                if ($judgment === 'Minor' || $judgment === 'Mayor') {
                    $existingCar = DB::table('CsAuditCar')
                        ->where('audit_detail_id', $detailId)
                        ->first();

                    $header = DB::table('CsAuditHeader')->where('id', $headerId)->first();
                    $item = DB::table('CsChecksheetItem')->where('id', $itemId)->first();

                    if ($existingCar) {
                        DB::table('CsAuditCar')
                            ->where('id', $existingCar->id)
                            ->update([
                                'check_item' => $item->check_item_idn ?? null,
                                'finding_category' => $judgment,
                                'updated_at' => Carbon::now()
                            ]);
                    } else {
                        $dept = $header ? $header->auditee_dept : null;
                        $reqNumber = $dept ? $this->generateCarReqNumber($dept) : null;
                        DB::table('CsAuditCar')->insert([
                            'audit_detail_id' => $detailId,
                            'req_number' => $reqNumber,
                            'department' => $dept,
                            'check_item' => $item->check_item_idn ?? null,
                            'finding_category' => $judgment,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ]);
                    }
                } else {
                    // Delete CAR if judgment changed back to OK or OFI
                    DB::table('CsAuditCar')
                        ->where('audit_detail_id', $detailId)
                        ->delete();
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Audit results submitted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function getCars(Request $request)
    {
        $query = DB::table('CsAuditCar as a')
            ->leftJoin('CsAuditDetail as b', 'b.id', '=', 'a.audit_detail_id')
            ->leftJoin('CsAuditHeader as c', 'c.id', '=', 'b.audit_header_id')
            ->whereNotNull('a.department')
            ->where('a.department', '<>', '')
            ->whereNotNull('a.finding')
            ->where('a.finding', '<>', '')
            ->select('a.*', 'b.checksheet_item_id', 'c.hash_id as schedule_hash_id', 'c.auditee as header_auditee');

        if ($request->has('search') && !empty($request->search['value'])) {
            $searchValue = $request->search['value'];
            $query->where(function($q) use ($searchValue) {
                $q->where('a.req_number', 'LIKE', "%{$searchValue}%")
                  ->orWhere('a.department', 'LIKE', "%{$searchValue}%")
                  ->orWhere('a.auditor', 'LIKE', "%{$searchValue}%")
                  ->orWhere('a.auditee', 'LIKE', "%{$searchValue}%")
                  ->orWhere('a.finding_category', 'LIKE', "%{$searchValue}%");
            });
        }

        // Apply filters if any
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

        $totalData = DB::table('CsAuditCar')
            ->whereNotNull('department')
            ->where('department', '<>', '')
            ->whereNotNull('finding')
            ->where('finding', '<>', '')
            ->count();
        $totalFiltered = $query->count();

        $limit = $request->input('length', 10);
        $start = $request->input('start', 0);
        
        $posts = $query->offset($start)
            ->limit($limit)
            ->orderBy('a.created_at', 'desc')
            ->get();

        $data = [];
        $no = $start + 1;
        $hasDeletePermission = UserMenuPermission::canDelete(110);
        foreach ($posts as $post) {
            $statusBadge = $post->finding_category ?? 'OFI';

            $sys_id = "'" . $this->encryptCarId($post->id) . "'";

            $action = '<div class="flex items-center justify-start gap-2">';
            $action .= '
                <button type="button" title="Preview" class="w-10 h-10 flex items-center justify-center rounded-xl bg-blue-50 text-blue-500 hover:bg-blue-100 hover:text-blue-600 transition-all duration-200" id="btn_form_view_doc_' . $no . '" onclick="document_preview(' . $sys_id . ',' . $no . ')">
                    <span id="svg_form_view_doc_' . $no . '" class="flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500" viewBox="0 0 24 24" fill="none">
                            <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="currentColor"></path>
                            <path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="currentColor"></path>
                            <path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="currentColor"></path>
                        </svg>
                    </span>
                    <span id="spinner_form_view_doc_' . $no . '" class="hidden animate-spin rounded-full h-4 w-4 border-b-2 border-current"></span>
                </button>';

            if ($hasDeletePermission) {
                $action .= '
                    <button type="button" title="Delete" class="w-10 h-10 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-100 hover:text-red-600 transition-all duration-200" id="btn_f_genba_conform_delete_' . $no . '" onclick="f_genba_conform_delete(' . $sys_id . ',' . $no . ')">
                        <span id="icon_f_genba_conform_delete_' . $no . '" class="flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-600" viewBox="0 0 24 24" fill="none">
                                <path opacity="0.3" d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"/>
                                <path d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V7H5V5Z" fill="currentColor"/>
                                <path d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"/>
                            </svg>
                        </span>
                        <span id="loader_f_genba_conform_delete_' . $no . '" class="hidden animate-spin rounded-full h-4 w-4 border-b-2 border-current"></span>
                    </button>';
            }
            $action .= '</div>';

            $data[] = [
                'no' => $no++,
                'req_number' => $post->req_number ?? '-',
                'department' => $post->department ?? '-',
                'finding_category' => $statusBadge,
                'auditor' => $post->auditor ?? '-',
                'auditee' => $post->header_auditee ?? $post->auditee ?? '-',
                'action' => $action,
                'schedule_hash_id' => $post->schedule_hash_id,
                'checksheet_item_id' => $post->checksheet_item_id
            ];
        }

        return response()->json([
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        ]);
    }

    public function deleteCar(Request $request)
    {
        $request->validate([
            'sys_id' => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            $sysId = $request->sys_id;
            
            // Decrypt custom encrypted ID
            $carId = $this->decryptCarId($sysId);
            $car = null;
            if ($carId) {
                $car = DB::table('CsAuditCar')->where('id', $carId)->first();
            }

            // Fallback for legacy decryption
            if (!$car) {
                try {
                    $sysIdStr = Crypt::decryptString($sysId);
                    $carIdLegacy = explode('_', $sysIdStr)[0];
                    $car = DB::table('CsAuditCar')->where('id', $carIdLegacy)->first();
                } catch (\Exception $e) {
                    $car = DB::table('CsAuditCar')->where('id', $sysId)->first();
                }
            }

            if ($car) {
                // 1. Delete CsAuditAction corrective and preventive photos from disk
                $action = DB::table('CsAuditAction')->where('audit_car_id', $car->id)->first();
                if ($action) {
                    // Corrective Photos (legacy)
                    if (!empty($action->corrective_path)) {
                        $paths = explode(',', $action->corrective_path);
                        foreach ($paths as $path) {
                            $filePath = public_path(trim($path));
                            if (file_exists($filePath) && is_file($filePath)) {
                                @unlink($filePath);
                            }
                        }
                    }
                    // Preventive Photos (legacy)
                    if (!empty($action->preventive_path)) {
                        $paths = explode(',', $action->preventive_path);
                        foreach ($paths as $path) {
                            $filePath = public_path(trim($path));
                            if (file_exists($filePath) && is_file($filePath)) {
                                @unlink($filePath);
                            }
                        }
                    }
                    // Individual Action Photos
                    $actionPaths = [
                        $action->corrective_path_one,
                        $action->corrective_path_two,
                        $action->corrective_path_three,
                        $action->preventive_path_one,
                        $action->preventive_path_two,
                        $action->preventive_path_three,
                    ];
                    foreach ($actionPaths as $path) {
                        if (!empty($path)) {
                            $filePath = public_path(trim($path));
                            if (file_exists($filePath) && is_file($filePath)) {
                                @unlink($filePath);
                            }
                        }
                    }
                }

                // 2. Delete CsAuditCar finding photos from disk
                if (!empty($car->finding_file_path)) {
                    $paths = explode(',', $car->finding_file_path);
                    foreach ($paths as $path) {
                        $filePath = public_path(trim($path));
                        if (file_exists($filePath) && is_file($filePath)) {
                            @unlink($filePath);
                        }
                    }
                }

                // 3. Delete CsAuditDetail finding photos from disk
                $detail = DB::table('CsAuditDetail')->where('id', $car->audit_detail_id)->first();
                if ($detail && !empty($detail->finding_photo_path)) {
                    $paths = explode(',', $detail->finding_photo_path);
                    foreach ($paths as $path) {
                        $filePath = public_path(trim($path));
                        if (file_exists($filePath) && is_file($filePath)) {
                            @unlink($filePath);
                        }
                    }
                }

                DB::table('CsAuditCar')->where('id', $car->id)->delete();
                DB::table('CsAuditDetail')->where('id', $car->audit_detail_id)->delete();
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'CAR Action Report deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function updateCarPlan(Request $request)
    {
        $request->validate([
            'car_id' => 'required|integer',
            'corrective_action' => 'required|string',
            'preventive_action' => 'required|string',
            'due_date' => 'required|date'
        ]);

        try {
            $updateData = [
                'corrective_action' => $request->corrective_action,
                'preventive_action' => $request->preventive_action,
                'due_date' => $request->due_date,
                'status' => 'Under Review',
                'updated_at' => Carbon::now()
            ];

            // Handle file upload if present
            if ($request->hasFile('evidence_file')) {
                $file = $request->file('evidence_file');
                $fileName = 'evidence_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/cs_audit'), $fileName);
                $updateData['finding_file_path'] = 'uploads/cs_audit/' . $fileName;
            }

            DB::table('CsAuditCar')->where('id', $request->car_id)->update($updateData);

            return response()->json(['success' => true, 'message' => 'CAR action plans updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function approveCar(Request $request)
    {
        $request->validate([
            'car_id' => 'required|integer',
            'role' => 'required|string',
            'notes' => $request->role === 'auditor' ? 'required|string' : 'nullable|string',
            'corrective_action_one_verif' => 'nullable|string|in:approve,reject',
            'corrective_action_two_verif' => 'nullable|string|in:approve,reject',
            'corrective_action_three_verif' => 'nullable|string|in:approve,reject',
            'preventive_action_one_verif' => 'nullable|string|in:approve,reject',
            'preventive_action_two_verif' => 'nullable|string|in:approve,reject',
            'preventive_action_three_verif' => 'nullable|string|in:approve,reject',
        ]);

        try {
            $user = Auth::user();
            $role = $request->role; // 'superior' or 'auditor'
            if ($role === 'qmr') {
                $role = 'superior';
            }

            $car = DB::table('CsAuditCar')->where('id', $request->car_id)->first();
            if (!$car) {
                return response()->json(['success' => false, 'message' => 'CAR not found.']);
            }

            $action = DB::table('CsAuditAction')->where('audit_car_id', $request->car_id)->first();
            if (!$action) {
                return response()->json(['success' => false, 'message' => 'Action Plan not found for this CAR.']);
            }

            // Check if any of the actions were rejected
            $hasRejection = $request->corrective_action_one_verif === 'reject'
                || $request->corrective_action_two_verif === 'reject'
                || $request->corrective_action_three_verif === 'reject'
                || $request->preventive_action_one_verif === 'reject'
                || $request->preventive_action_two_verif === 'reject'
                || $request->preventive_action_three_verif === 'reject';

            if ($role === 'superior') {
                if (empty($action->auditee_superior_name) || strcasecmp(trim($user->full_name), trim($action->auditee_superior_name)) !== 0) {
                    return response()->json(['success' => false, 'message' => 'Only the Auditee Superior (' . ($action->auditee_superior_name ?? '-') . ') is allowed to approve at this stage.']);
                }
            } elseif ($role === 'auditor') {
                // Verify the user is the Auditor
                $isAuditor = false;
                if (!empty($car->auditor)) {
                    $auditors = array_map('trim', explode(',', $car->auditor));
                    foreach ($auditors as $auditorName) {
                        if (strcasecmp(trim($user->full_name), $auditorName) === 0) {
                            $isAuditor = true;
                            break;
                        }
                    }
                }

                if (!$isAuditor) {
                    return response()->json(['success' => false, 'message' => 'Only the designated Auditor (' . ($car->auditor ?? '-') . ') is allowed to verify and close this CAR.']);
                }
            } else {
                return response()->json(['success' => false, 'message' => 'Invalid role specified.']);
            }

            DB::beginTransaction();

            // Prepare verif data
            $verifData = [
                'corrective_action_one_verif' => ($role === 'superior') ? 'approve' : $request->corrective_action_one_verif,
                'corrective_action_two_verif' => ($role === 'superior') ? 'approve' : $request->corrective_action_two_verif,
                'corrective_action_three_verif' => ($role === 'superior') ? 'approve' : $request->corrective_action_three_verif,
                'preventive_action_one_verif' => ($role === 'superior') ? 'approve' : $request->preventive_action_one_verif,
                'preventive_action_two_verif' => ($role === 'superior') ? 'approve' : $request->preventive_action_two_verif,
                'preventive_action_three_verif' => ($role === 'superior') ? 'approve' : $request->preventive_action_three_verif,
                'updated_at' => Carbon::now()
            ];

            if ($request->exists('notes')) {
                $verifData['notes'] = $request->notes;
            }

            if ($hasRejection) {
                // Return to draft
                $verifData['action_status'] = 'draft';
                DB::table('CsAuditAction')->where('audit_car_id', $request->car_id)->update($verifData);
                
                DB::table('CsAuditCar')->where('id', $request->car_id)->update([
                    'status' => 'Draft',
                    'updated_at' => Carbon::now()
                ]);

                DB::commit();
                return response()->json(['success' => true, 'message' => 'Action Plan rejected and returned to draft due to rejected items.']);
            } else {
                // All approved / not rejected
                $verifData['action_status'] = ($role === 'superior') ? 'approve_superior' : 'verified';
                DB::table('CsAuditAction')->where('audit_car_id', $request->car_id)->update($verifData);

                $targetStatus = ($role === 'superior') ? 'Need Verification' : 'Closed';
                DB::table('CsAuditCar')->where('id', $request->car_id)->update([
                    'status' => $targetStatus,
                    'updated_at' => Carbon::now()
                ]);

                DB::commit();
                
                $msg = ($role === 'superior') 
                    ? 'Action Plan approved by Superior. Now waiting for Auditor verification.' 
                    : 'CAR verified and Closed successfully by Auditor.';
                return response()->json(['success' => true, 'message' => $msg]);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function saveJudgment(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|integer',
            'checksheet_item_id' => 'required|integer',
            'judgment' => 'required|string|in:OK,OFI,Minor,Mayor',
            'note' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $headerId = $request->schedule_id;
            $itemId = $request->checksheet_item_id;
            $judgment = $request->judgment;
            $note = $request->note;

            $header = DB::table('CsAuditHeader')->where('id', $headerId)->first();
            if (!$header) {
                return response()->json(['success' => false, 'message' => 'Schedule not found.']);
            }

            $existingDetail = DB::table('CsAuditDetail')
                ->where('audit_header_id', $headerId)
                ->where('checksheet_item_id', $itemId)
                ->first();

            if ($existingDetail) {
                $updateData = [
                    'judgment' => $judgment,
                    'updated_at' => Carbon::now()
                ];
                if ($request->exists('note')) {
                    $updateData['note'] = $note;
                }
                DB::table('CsAuditDetail')
                    ->where('id', $existingDetail->id)
                    ->update($updateData);
                $detailId = $existingDetail->id;
            } else {
                $detailId = DB::table('CsAuditDetail')->insertGetId([
                    'audit_header_id' => $headerId,
                    'checksheet_item_id' => $itemId,
                    'judgment' => $judgment,
                    'note' => $note ?? null,
                    'evidence' => null,
                    'finding_photo_path' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }

            if ($judgment === 'Minor' || $judgment === 'Mayor') {
                $existingCar = DB::table('CsAuditCar')
                    ->where('audit_detail_id', $detailId)
                    ->first();

                $header = DB::table('CsAuditHeader')->where('id', $headerId)->first();
                $item = DB::table('CsChecksheetItem')->where('id', $itemId)->first();

                if (!$existingCar) {
                    $dept = $header ? $header->auditee_dept : null;
                    $reqNumber = $dept ? $this->generateCarReqNumber($dept) : null;
                    DB::table('CsAuditCar')->insert([
                        'audit_detail_id' => $detailId,
                        'req_number' => $reqNumber,
                        'department' => $dept,
                        'check_item' => $item->check_item_idn ?? null,
                        'finding_category' => $judgment,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                } else {
                    DB::table('CsAuditCar')
                        ->where('id', $existingCar->id)
                        ->update([
                            'check_item' => $item->check_item_idn ?? null,
                            'finding_category' => $judgment,
                            'updated_at' => Carbon::now()
                        ]);
                }
            } else {
                DB::table('CsAuditCar')
                    ->where('audit_detail_id', $detailId)
                    ->delete();
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Judgment updated.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function carForm($schedule_id, $item_id)
    {
        $schedule = DB::table('CsAuditHeader')->where('hash_id', $schedule_id)->first();
        if (!$schedule) abort(404);
        $schedule->formatted_date = $schedule->audit_date ? Carbon::parse($schedule->audit_date)->format('d M Y') : '-';

        $item = DB::table('CsChecksheetItem')->where('id', $item_id)->first();
        if (!$item) abort(404);

        $detail = DB::table('CsAuditDetail')
            ->where('audit_header_id', $schedule->id)
            ->where('checksheet_item_id', $item_id)
            ->first();

        if (!$detail) {
            $detailId = DB::table('CsAuditDetail')->insertGetId([
                'audit_header_id' => $schedule->id,
                'checksheet_item_id' => $item_id,
                'judgment' => request('judgment', 'OFI'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            $detail = DB::table('CsAuditDetail')->where('id', $detailId)->first();
        }

        $car = DB::table('CsAuditCar')->where('audit_detail_id', $detail->id)->first();
        // Auto-fill department and req_number if missing
        $depts = array_map('trim', explode(',', $schedule->auditee_dept ?? ''));
        $defaultDept = !empty($depts[0]) ? $depts[0] : null;
        if (!$car) {
            $reqNumber = $defaultDept ? $this->generateCarReqNumber($defaultDept) : null;
            $auditDate = $schedule->audit_date ?? $schedule->schedule_date ?? null;
            $carId = DB::table('CsAuditCar')->insertGetId([
                'audit_detail_id' => $detail->id,
                'req_number' => $reqNumber,
                'department' => $defaultDept,
                'check_item' => $item->check_item_idn ?? null,
                'finding_category' => $detail->judgment ?? 'OFI',
                'auditor' => $schedule->auditor_names ?? null,
                'auditee' => $schedule->auditee ?? null,
                'due_date' => $auditDate ? Carbon::parse($auditDate)->addWeeks(2)->toDateString() : null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            $car = DB::table('CsAuditCar')->where('id', $carId)->first();
        } elseif (empty($car->department) && $defaultDept) {
            // Existing CAR with no department — auto-fill it
            $reqNumber = $this->generateCarReqNumber($defaultDept);
            $auditDate = $schedule->audit_date ?? $schedule->schedule_date ?? null;
            DB::table('CsAuditCar')->where('id', $car->id)->update([
                'department' => $defaultDept,
                'req_number' => $reqNumber,
                'due_date' => $car->due_date ?? ($auditDate ? Carbon::parse($auditDate)->addWeeks(2)->toDateString() : null),
                'updated_at' => Carbon::now()
            ]);
            $car = DB::table('CsAuditCar')->where('id', $car->id)->first();
        }
        // Auto-fill due_date if still missing (existing old records)
        if (empty($car->due_date)) {
            $auditDate = $schedule->audit_date ?? $schedule->schedule_date ?? null;
            $autoDueDate = $auditDate ? Carbon::parse($auditDate)->addWeeks(2)->toDateString() : null;
            if ($autoDueDate) {
                DB::table('CsAuditCar')->where('id', $car->id)->update([
                    'due_date' => $autoDueDate,
                    'updated_at' => Carbon::now()
                ]);
                $car = DB::table('CsAuditCar')->where('id', $car->id)->first();
            }
        }
        $deptNames = DB::table('GenbaDept')->whereIn('Key1', $depts)->pluck('Key1')->toArray();
        $schedule->auditee_dept_name = !empty($deptNames) ? implode(', ', $deptNames) : $schedule->auditee_dept;

        $departments = DB::table('GenbaDept')
            ->where('CheckBox01', 1)
            ->get();

        $requirements = DB::table('CsKlausul')
            ->select('clause_no')
            ->distinct()
            ->get()
            ->map(function ($r) {
                return [
                    'id' => $r->clause_no,
                    'name' => $r->clause_no
                ];
            });

        $clauseTitles = DB::table('CsKlausul')
            ->select('clause_title')
            ->distinct()
            ->get()
            ->map(function ($r) {
                return [
                    'id' => $r->clause_title,
                    'name' => $r->clause_title
                ];
            });

        return view('activity.form-checksheet-intr.car_form', compact('schedule', 'item', 'detail', 'car', 'departments', 'requirements', 'clauseTitles'));
    }

    public function sendDraftCarForm(Request $request, $schedule_id, $item_id)
    {
        $schedule = DB::table('CsAuditHeader')->where('hash_id', $schedule_id)->first();
        if (!$schedule) abort(404);

        $request->validate([
            'judgment' => 'nullable|string|in:OK,OFI,Minor,Mayor,Observation',
            'finding_desc' => 'nullable|string',
            'audit_source' => 'nullable|array',
            'audit_category' => 'nullable|array',
            'observation_number' => 'nullable|string',
            'observation_date' => 'nullable|date',
            'corrective_action' => 'nullable|string',
            'preventive_action' => 'nullable|string',
            'due_date' => 'nullable|date',
            'photo' => 'nullable|image|max:5120',
            'finding_photo' => 'nullable|array',
            'finding_photo.*' => 'nullable|image|max:5120',
            'existing_photos' => 'nullable|string',
            'department' => 'nullable|string',
            'requirement_no' => 'nullable|string',
            'clause_title' => 'nullable|string',
            'clause_text' => 'nullable|string',
            'finding' => 'nullable|string',
            'auditor' => 'nullable|string',
            'auditee' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $detail = DB::table('CsAuditDetail')
                ->where('audit_header_id', $schedule->id)
                ->where('checksheet_item_id', $item_id)
                ->first();

            $photoPath = $detail ? $detail->finding_photo_path : null;

            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $fileName = 'finding_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/cs_audit'), $fileName);
                $photoPath = 'uploads/cs_audit/' . $fileName;
            }

            $sources = $request->audit_source ?? [];
            $finalSources = [];
            foreach ($sources as $src) {
                if ($src === 'Surveillance') {
                    $finalSources[] = 'Surveillance: ' . ($request->audit_source_surveillance_text ?? '');
                } elseif ($src === 'External') {
                    $finalSources[] = 'External: ' . ($request->audit_source_external_text ?? '');
                } else {
                    $finalSources[] = $src;
                }
            }
            $auditSourceStr = implode(', ', $finalSources);

            $judgmentVal = $request->judgment ?? ($detail ? $detail->judgment : 'OFI');
            $findingDescVal = $request->finding ?? $request->finding_desc ?? ($detail ? $detail->evidence : '');

            if ($detail) {
                DB::table('CsAuditDetail')
                    ->where('id', $detail->id)
                    ->update([
                        'judgment' => $judgmentVal,
                        'evidence' => null,
                        'finding_photo_path' => $photoPath,
                        'updated_at' => Carbon::now()
                    ]);
                $detailId = $detail->id;
            } else {
                $detailId = DB::table('CsAuditDetail')->insertGetId([
                    'audit_header_id' => $schedule->id,
                    'checksheet_item_id' => $item_id,
                    'judgment' => $judgmentVal,
                    'evidence' => null,
                    'finding_photo_path' => $photoPath,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }

            $categories = $request->audit_category ?? [];
            $surveillance = in_array('Surveillance', $sources) ? ($request->audit_source_surveillance_text ?? '') : null;
            $external = in_array('External', $sources) ? ($request->audit_source_external_text ?? '') : null;
            $internalAudit = in_array('Internal Audit', $sources) ? implode(', ', $categories) : null;
            if (empty($internalAudit) && $schedule) {
                $internalAudit = $schedule->audit_type;
            }

            $item = DB::table('CsChecksheetItem')->where('id', $item_id)->first();

            $car = DB::table('CsAuditCar')->where('audit_detail_id', $detailId)->first();
            $department = $request->department;
            
            // Read remaining existing photos sent from form
            $findingPhotoPath = $request->input('existing_photos', '');

            if ($request->hasFile('finding_photo')) {
                $files = $request->file('finding_photo');
                $paths = [];
                foreach ($files as $file) {
                    if ($file) {
                        $fileName = 'finding_car_' . uniqid() . '.' . $file->getClientOriginalExtension();
                        $file->move(public_path('findings-photo'), $fileName);
                        $paths[] = 'findings-photo/' . $fileName;
                    }
                }
                $existingArray = array_filter(explode(',', $findingPhotoPath));
                $allPaths = array_merge($existingArray, $paths);
                $findingPhotoPath = implode(',', $allPaths);
            }

            if ($car) {
                $reqNumber = $car->req_number;
                if (!$reqNumber || $car->department !== $department) {
                    $reqNumber = $this->generateCarReqNumber($department);
                }
                DB::table('CsAuditCar')
                    ->where('id', $car->id)
                    ->update([
                        'req_number' => $reqNumber,
                        'check_item' => $item->check_item_idn ?? null,
                        'surveillance' => $surveillance,
                        'external' => $external,
                        'department' => $department,
                        'requirement_no' => $request->requirement_no,
                        'clause_title' => $request->clause_title,
                        'clause_text' => $request->clause_text,
                        'finding_category' => $judgmentVal,
                        'finding' => $request->finding,
                        'auditor' => $request->auditor,
                        'auditee' => $request->auditee,
                        'due_date' => $request->due_date,
                        'finding_file_path' => $findingPhotoPath,
                        'updated_at' => Carbon::now()
                    ]);
            } else {
                $reqNumber = $this->generateCarReqNumber($department);
                DB::table('CsAuditCar')->insert([
                    'audit_detail_id' => $detailId,
                    'req_number' => $reqNumber,
                    'check_item' => $item->check_item_idn ?? null,
                    'surveillance' => $surveillance,
                    'external' => $external,
                    'department' => $department,
                    'requirement_no' => $request->requirement_no,
                    'clause_title' => $request->clause_title,
                    'clause_text' => $request->clause_text,
                    'finding_category' => $judgmentVal,
                    'finding' => $request->finding,
                    'auditor' => $request->auditor,
                    'auditee' => $request->auditee,
                    'due_date' => $request->due_date,
                    'finding_file_path' => $findingPhotoPath,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }

            DB::commit();

            if ($request->has('draft') || $request->ajax()) {
                $updatedCar = DB::table('CsAuditCar')->where('audit_detail_id', $detailId)->first();
                return response()->json(['success' => true, 'message' => 'Draft saved successfully.', 'req_number' => $updatedCar->req_number ?? null]);
            }

            return redirect()->route('internal_audit.conduct', $schedule_id)
                ->with('toast_success', 'CAR details saved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->has('draft') || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()]);
            }
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function deleteSchedule($id)
    {
        if (!UserMenuPermission::canDelete(108)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.']);
        }
        try {
            DB::table('CsAuditHeader')->where('hash_id', $id)->delete();
            return response()->json(['success' => true, 'message' => 'Schedule deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function getRequirements(Request $request)
    {
        $search = $request->search;
        $page = $request->post('page', 1);
        $pageSize = 10;

        $query = DB::table('CsKlausul')
            ->select('clause_no')
            ->distinct();

        if ($search) {
            $query->where('clause_no', 'LIKE', '%' . $search . '%');
        }

        $results = $query->paginate($pageSize, ['*'], 'page', $page);

        return response()->json([
            'items' => collect($results->items())->map(function ($r) {
                return [
                    'id' => $r->clause_no,
                    'name' => $r->clause_no
                ];
            })->values(),
            'pagination' => [
                'more' => $results->hasMorePages(),
            ]
        ]);
    }

    public function getClauseTitles(Request $request)
    {
        $search = $request->search;
        $page = $request->post('page', 1);
        $pageSize = 10;

        $query = DB::table('CsKlausul')
            ->select('clause_title')
            ->distinct();

        if ($search) {
            $query->where('clause_title', 'LIKE', '%' . $search . '%');
        }

        $results = $query->paginate($pageSize, ['*'], 'page', $page);

        return response()->json([
            'items' => collect($results->items())->map(function ($r) {
                return [
                    'id' => $r->clause_title,
                    'name' => $r->clause_title
                ];
            })->values(),
            'pagination' => [
                'more' => $results->hasMorePages(),
            ]
        ]);
    }

    private function encryptCarId($id)
    {
        $s1 = str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
        $s2 = str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
        $mid = str_pad($id, 6, '0', STR_PAD_LEFT);
        $plain = $s1 . $mid . $s2;
        
        $appKey = config('app.key', 'qms_secret_fallback_key_123');
        $key = substr(md5($appKey), 0, 16);
        
        $encrypted = '';
        for ($i = 0; $i < 16; $i++) {
            $encrypted .= chr(ord($plain[$i]) ^ ord($key[$i]));
        }
        
        $hex = bin2hex($encrypted);
        
        return sprintf('%s-%s-%s-%s-%s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20, 12)
        );
    }

    private function decryptCarId($uuid)
    {
        try {
            $hex = str_replace('-', '', $uuid);
            if (strlen($hex) !== 32) return null;
            
            $encrypted = @hex2bin($hex);
            if ($encrypted === false) return null;
            
            $appKey = config('app.key', 'qms_secret_fallback_key_123');
            $key = substr(md5($appKey), 0, 16);
            
            $plain = '';
            for ($i = 0; $i < 16; $i++) {
                $plain .= chr(ord($encrypted[$i]) ^ ord($key[$i]));
            }
            
            $s1 = substr($plain, 0, 5);
            $mid = substr($plain, 5, 6);
            $s2 = substr($plain, 11, 5);
            
            if (is_numeric($s1) && is_numeric($mid) && is_numeric($s2)) {
                return (int)$mid;
            }
        } catch (\Exception $e) {
            return null;
        }
        return null;
    }

    private function generateCarReqNumber($department)
    {
        if (empty($department)) {
            return null;
        }

        $latestCar = DB::table('CsAuditCar')
            ->where('department', $department)
            ->whereNotNull('req_number')
            ->where('req_number', 'LIKE', "SAI - INT - {$department} - %")
            ->orderBy('req_number', 'desc')
            ->first();

        $nextNum = 1;
        if ($latestCar) {
            $parts = explode('-', $latestCar->req_number);
            $lastPart = trim(end($parts));
            if (is_numeric($lastPart)) {
                $nextNum = intval($lastPart) + 1;
            }
        }

        return "SAI - INT - " . strtoupper($department) . " - " . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
    }
}
