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
                    'name' => $item->Key1 . ' - ' . $item->Desc
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
                    ->join('CsAuditDetail as b', 'b.id', '=', 'a.audit_detail_id')
                    ->join('CsAuditHeader as c', 'c.id', '=', 'b.audit_header_id')
                    ->where('a.id', $carId)
                    ->select(
                        'a.*', 
                        'b.checksheet_item_id', 
                        'c.hash_id as schedule_hash_id', 
                        'b.evidence', 
                        'b.finding_photo_path'
                    )
                    ->first();
            }

            // Fallback for database hash_id, legacy Crypt, or direct ID lookup
            if (!$car) {
                $car = DB::table('CsAuditCar as a')
                    ->join('CsAuditDetail as b', 'b.id', '=', 'a.audit_detail_id')
                    ->join('CsAuditHeader as c', 'c.id', '=', 'b.audit_header_id')
                    ->where('a.hash_id', $id)
                    ->select(
                        'a.*', 
                        'b.checksheet_item_id', 
                        'c.hash_id as schedule_hash_id', 
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
                    ->join('CsAuditDetail as b', 'b.id', '=', 'a.audit_detail_id')
                    ->join('CsAuditHeader as c', 'c.id', '=', 'b.audit_header_id')
                    ->where('a.id', $carId)
                    ->select(
                        'a.*', 
                        'b.checksheet_item_id', 
                        'c.hash_id as schedule_hash_id', 
                        'b.evidence', 
                        'b.finding_photo_path'
                    )
                    ->first();
            }

            if (!$car) {
                return redirect()->route('internal_audit.action_report')->with('error', 'CAR Action Report not found.');
            }

            $car->formatted_date = $car->created_at ? Carbon::parse($car->created_at)->format('d F Y') : '-';

            $action = DB::table('CsAuditAction')->where('audit_car_id', $car->id)->first();

            return view('activity.internal_action_preview', compact('car', 'action'));
        } catch (\Exception $e) {
            return redirect()->route('internal_audit.action_report')->with('error', $e->getMessage());
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
                'causal_factor' => 'nullable|string',
                'analyzed_by' => 'nullable|string',
                'corrective_action' => 'nullable|string',
                'preventive_action' => 'nullable|string',
                'notes' => 'nullable|string',
                'auditee_name' => 'nullable|string',
                'auditee_superior_name' => 'nullable|string',
            ]);

            $existingAction = DB::table('CsAuditAction')->where('audit_car_id', $car->id)->first();
            if ($existingAction) {
                DB::table('CsAuditAction')
                    ->where('id', $existingAction->id)
                    ->update([
                        'causal_factor' => $request->causal_factor,
                        'analyzed_by' => $request->analyzed_by,
                        'corrective_action' => $request->corrective_action,
                        'preventive_action' => $request->preventive_action,
                        'notes' => $request->notes,
                        'auditee_name' => $request->auditee_name,
                        'auditee_superior_name' => $request->auditee_superior_name,
                        'updated_at' => Carbon::now()
                    ]);
            } else {
                DB::table('CsAuditAction')->insert([
                    'audit_car_id' => $car->id,
                    'causal_factor' => $request->causal_factor,
                    'analyzed_by' => $request->analyzed_by,
                    'corrective_action' => $request->corrective_action,
                    'preventive_action' => $request->preventive_action,
                    'notes' => $request->notes,
                    'auditee_name' => $request->auditee_name,
                    'auditee_superior_name' => $request->auditee_superior_name,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }

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
                  ->orWhere('a.auditee_dept', 'LIKE', "%{$searchValue}%");
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
            $deptName = $dept ? $dept->Desc : $post->auditee_dept;

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
            'agenda_name' => 'required|string|max:255',
            'schedule_date' => 'required|date',
            'auditor_niks' => 'required|string',
            'auditee_dept' => 'required|string',
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

                $dateFormatted = Carbon::parse($request->schedule_date)->format('dmY');
                $reqNumber = $dateFormatted . $schedule->id;

                DB::table('CsAuditHeader')
                    ->where('hash_id', $scheduleId)
                    ->update([
                        'req_number' => $reqNumber,
                        'auditee' => $request->agenda_name,
                        'audit_date' => $request->schedule_date,
                        'auditor_names' => $request->auditor_niks,
                        'auditee_dept' => $request->auditee_dept,
                        'status' => 'Scheduled',
                        'updated_at' => Carbon::now()
                    ]);
                $hash = $scheduleId;
            } else {
                // Insert
                $hash = strtolower(\Illuminate\Support\Str::random(3) . '-' . \Illuminate\Support\Str::random(3) . '-' . \Illuminate\Support\Str::random(3));
                
                $id = DB::table('CsAuditHeader')->insertGetId([
                    'hash_id' => $hash,
                    'req_number' => '', // placeholder
                    'auditee' => $request->agenda_name,
                    'audit_date' => $request->schedule_date,
                    'auditor_names' => $request->auditor_niks,
                    'auditee_dept' => $request->auditee_dept,
                    'status' => 'Scheduled',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);

                $dateFormatted = Carbon::parse($request->schedule_date)->format('dmY');
                $reqNumber = $dateFormatted . $id;

                DB::table('CsAuditHeader')
                    ->where('id', $id)
                    ->update(['req_number' => $reqNumber]);
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
        $schedule->auditee_dept_name = $dept ? "{$dept->Key1} - {$dept->Desc}" : $schedule->auditee_dept;

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
        $schedule->auditee_dept_name = $dept ? $dept->Desc : $schedule->auditee_dept;

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

        $items = DB::table('CsChecksheetItem')
            ->where('is_active', 1)
            ->where('department', $schedule->auditee_dept)
            ->get();

        $details = DB::table('CsAuditDetail')
            ->where('audit_header_id', $schedule->id)
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
                $evidence = $itemData['evidence'] ?? null;
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
                        'finding_photo_path' => $photoPath,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                }

                // Create or update CAR if judgment is not OK (OFI, Mayor, Minor)
                if ($judgment !== 'OK') {
                    $existingCar = DB::table('CsAuditCar')
                        ->where('audit_detail_id', $detailId)
                        ->first();

                    $header = DB::table('CsAuditHeader')->where('id', $headerId)->first();
                    $item = DB::table('CsChecksheetItem')->where('id', $itemId)->first();

                    if ($existingCar) {
                        DB::table('CsAuditCar')
                            ->where('id', $existingCar->id)
                            ->update([
                                'req_number' => $header->req_number ?? null,
                                'check_item' => $item->check_item_idn ?? null,
                                'finding_category' => $judgment,
                                'updated_at' => Carbon::now()
                            ]);
                    } else {
                        DB::table('CsAuditCar')->insert([
                            'audit_detail_id' => $detailId,
                            'req_number' => $header->req_number ?? null,
                            'check_item' => $item->check_item_idn ?? null,
                            'finding_category' => $judgment,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ]);
                    }
                } else {
                    // Delete CAR if judgment changed back to OK
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
            ->join('CsAuditDetail as b', 'b.id', '=', 'a.audit_detail_id')
            ->join('CsAuditHeader as c', 'c.id', '=', 'b.audit_header_id')
            ->whereNotNull('a.department')
            ->where('a.department', '<>', '')
            ->select('a.*', 'b.checksheet_item_id', 'c.hash_id as schedule_hash_id');

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
                'auditee' => $post->auditee ?? '-',
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
            
            // First try to look up by hash_id
            $car = DB::table('CsAuditCar')->where('hash_id', $sysId)->first();

            // Fallback for legacy decryption
            if (!$car) {
                try {
                    $sysIdStr = Crypt::decryptString($sysId);
                    $carId = explode('_', $sysIdStr)[0];
                    $car = DB::table('CsAuditCar')->where('id', $carId)->first();
                } catch (\Exception $e) {
                    $car = DB::table('CsAuditCar')->where('id', $sysId)->first();
                }
            }

            if ($car) {
                DB::table('CsAuditDetail')
                    ->where('id', $car->audit_detail_id)
                    ->update([
                        'judgment' => 'OK',
                        'evidence' => null,
                        'finding_photo_path' => null,
                        'updated_at' => Carbon::now()
                    ]);

                DB::table('CsAuditCar')->where('id', $car->id)->delete();
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
                $updateData['evidence_file_path'] = 'uploads/cs_audit/' . $fileName;
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
            'role' => 'required|string'
        ]);

        try {
            $user = Auth::user();
            $role = $request->role;
            $updateData = [];

            if ($role === 'dept') {
                $updateData['dept_head_nik'] = $user->username;
                $updateData['dept_head_approved_at'] = Carbon::now();
            } elseif ($role === 'auditor') {
                $updateData['auditor_nik'] = $user->username;
                $updateData['auditor_verified_at'] = Carbon::now();
                $updateData['auditor_comments'] = $request->comments;
            } elseif ($role === 'qmr') {
                $updateData['qmr_nik'] = $user->username;
                $updateData['qmr_approved_at'] = Carbon::now();
                $updateData['status'] = 'Closed';
                $updateData['completion_date'] = Carbon::now()->toDateString();
            }

            DB::table('CsAuditCar')->where('id', $request->car_id)->update($updateData);

            return response()->json(['success' => true, 'message' => 'Approval signature submitted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function saveJudgment(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|integer',
            'checksheet_item_id' => 'required|integer',
            'judgment' => 'required|string|in:OK,OFI,Minor,Mayor'
        ]);

        try {
            DB::beginTransaction();

            $headerId = $request->schedule_id;
            $itemId = $request->checksheet_item_id;
            $judgment = $request->judgment;

            $header = DB::table('CsAuditHeader')->where('id', $headerId)->first();
            if (!$header) {
                return response()->json(['success' => false, 'message' => 'Schedule not found.']);
            }

            $existingDetail = DB::table('CsAuditDetail')
                ->where('audit_header_id', $headerId)
                ->where('checksheet_item_id', $itemId)
                ->first();

            if ($existingDetail) {
                DB::table('CsAuditDetail')
                    ->where('id', $existingDetail->id)
                    ->update([
                        'judgment' => $judgment,
                        'updated_at' => Carbon::now()
                    ]);
                $detailId = $existingDetail->id;
            } else {
                $detailId = DB::table('CsAuditDetail')->insertGetId([
                    'audit_header_id' => $headerId,
                    'checksheet_item_id' => $itemId,
                    'judgment' => $judgment,
                    'evidence' => null,
                    'finding_photo_path' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }

            if ($judgment !== 'OK') {
                $existingCar = DB::table('CsAuditCar')
                    ->where('audit_detail_id', $detailId)
                    ->first();

                $header = DB::table('CsAuditHeader')->where('id', $headerId)->first();
                $item = DB::table('CsChecksheetItem')->where('id', $itemId)->first();

                if (!$existingCar) {
                    DB::table('CsAuditCar')->insert([
                        'audit_detail_id' => $detailId,
                        'req_number' => $header->req_number ?? null,
                        'check_item' => $item->check_item_idn ?? null,
                        'finding_category' => $judgment,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                } else {
                    DB::table('CsAuditCar')
                        ->where('id', $existingCar->id)
                        ->update([
                            'req_number' => $header->req_number ?? null,
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
        if (!$car) {
            $carId = DB::table('CsAuditCar')->insertGetId([
                'audit_detail_id' => $detail->id,
                'req_number' => $schedule->req_number ?? null,
                'check_item' => $item->check_item_idn ?? null,
                'finding_category' => $detail->judgment ?? 'OFI',
                'auditor' => $schedule->auditor_names ?? null,
                'auditee' => $schedule->auditee ?? null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            $car = DB::table('CsAuditCar')->where('id', $carId)->first();
        }

        $dept = DB::table('GenbaDept')->where('Key1', $schedule->auditee_dept)->first();
        $schedule->auditee_dept_name = $dept ? $dept->Desc : $schedule->auditee_dept;

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
                        'evidence' => $findingDescVal,
                        'finding_photo_path' => $photoPath,
                        'updated_at' => Carbon::now()
                    ]);
                $detailId = $detail->id;
            } else {
                $detailId = DB::table('CsAuditDetail')->insertGetId([
                    'audit_header_id' => $schedule->id,
                    'checksheet_item_id' => $item_id,
                    'judgment' => $judgmentVal,
                    'evidence' => $findingDescVal,
                    'finding_photo_path' => $photoPath,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }

            $categories = $request->audit_category ?? [];
            $surveillance = in_array('Surveillance', $sources) ? ($request->audit_source_surveillance_text ?? '') : null;
            $external = in_array('External', $sources) ? ($request->audit_source_external_text ?? '') : null;
            $internalAudit = in_array('Internal Audit', $sources) ? implode(', ', $categories) : null;

            $item = DB::table('CsChecksheetItem')->where('id', $item_id)->first();

            $car = DB::table('CsAuditCar')->where('audit_detail_id', $detailId)->first();
            if ($car) {
                DB::table('CsAuditCar')
                    ->where('id', $car->id)
                    ->update([
                        'req_number' => $schedule->req_number ?? null,
                        'check_item' => $item->check_item_idn ?? null,
                        'surveillance' => $surveillance,
                        'external' => $external,
                        'internal_audit' => $internalAudit,
                        'department' => $request->department,
                        'requirement_no' => $request->requirement_no,
                        'clause_title' => $request->clause_title,
                        'clause_text' => $request->clause_text,
                        'finding_category' => $judgmentVal,
                        'finding' => $request->finding,
                        'auditor' => $request->auditor,
                        'auditee' => $request->auditee,
                        'updated_at' => Carbon::now()
                    ]);
            } else {
                DB::table('CsAuditCar')->insert([
                    'audit_detail_id' => $detailId,
                    'req_number' => $schedule->req_number ?? null,
                    'check_item' => $item->check_item_idn ?? null,
                    'surveillance' => $surveillance,
                    'external' => $external,
                    'internal_audit' => $internalAudit,
                    'department' => $request->department,
                    'requirement_no' => $request->requirement_no,
                    'clause_title' => $request->clause_title,
                    'clause_text' => $request->clause_text,
                    'finding_category' => $judgmentVal,
                    'finding' => $request->finding,
                    'auditor' => $request->auditor,
                    'auditee' => $request->auditee,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }

            DB::commit();

            if ($request->has('draft') || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Draft saved successfully.']);
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
}
