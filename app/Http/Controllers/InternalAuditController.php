<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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
                    'clause_number' => 'IATF 16949 - 8.5.1',
                    'requirement_desc' => 'Is the production equipment maintained and calibrated according to customer specifications?',
                    'is_active' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'clause_number' => 'IATF 16949 - 8.5.2',
                    'requirement_desc' => 'Is traceability established throughout the assembly and packing lines?',
                    'is_active' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'clause_number' => 'IATF 16949 - 8.5.1.1',
                    'requirement_desc' => 'Are setup verifications conducted using representative parts or limit samples?',
                    'is_active' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
            ]);
        }

        $items = DB::table('CsChecksheetItem')->where('is_active', 1)->get();

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
                    'clause_number' => 'IATF 16949 - 8.5.1',
                    'requirement_desc' => 'Is the production equipment maintained and calibrated according to customer specifications?',
                    'is_active' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'clause_number' => 'IATF 16949 - 8.5.2',
                    'requirement_desc' => 'Is traceability established throughout the assembly and packing lines?',
                    'is_active' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'clause_number' => 'IATF 16949 - 8.5.1.1',
                    'requirement_desc' => 'Are setup verifications conducted using representative parts or limit samples?',
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
                    }
                }

                $detailId = DB::table('CsAuditDetail')->insertGetId([
                    'audit_header_id' => $headerId,
                    'checksheet_item_id' => $itemId,
                    'judgment' => $judgment,
                    'evidence' => $evidence,
                    'finding_photo_path' => $photoPath,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);

                // Create CAR if judgment is not OK (OFI, Mayor, Minor)
                if ($judgment !== 'OK') {
                    $docSeq = DB::table('CsAuditCar')->count() + 1;
                    $carNumber = 'CAR/CS/' . Carbon::parse($request->audit_date)->format('dMMyy') . '/' . sprintf('%03d', $docSeq);

                    DB::table('CsAuditCar')->insert([
                        'audit_detail_id' => $detailId,
                        'car_number' => $carNumber,
                        'finding_desc' => $evidence ?? 'Finding observed during clause evaluation.',
                        'status' => 'Open',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
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
            ->select('a.*', 'c.auditee_dept', 'c.auditor_names');

        if ($request->has('search') && !empty($request->search['value'])) {
            $searchValue = $request->search['value'];
            $query->where(function($q) use ($searchValue) {
                $q->where('a.car_number', 'LIKE', "%{$searchValue}%")
                  ->orWhere('a.finding_desc', 'LIKE', "%{$searchValue}%")
                  ->orWhere('c.auditee_dept', 'LIKE', "%{$searchValue}%");
            });
        }

        $totalData = DB::table('CsAuditCar')->count();
        $totalFiltered = $query->count();

        $limit = $request->input('length', 10);
        $start = $request->input('start', 0);
        
        $posts = $query->offset($start)
            ->limit($limit)
            ->orderBy('a.created_at', 'desc')
            ->get();

        $data = [];
        foreach ($posts as $post) {
            $dept = DB::table('GenbaDept')->where('Key1', $post->auditee_dept)->first();
            $deptName = $dept ? "{$dept->Key1} ({$dept->Desc})" : $post->auditee_dept;

            $statusBadge = '';
            switch ($post->status) {
                case 'Closed':
                    $statusBadge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">Closed</span>';
                    break;
                case 'Under Review':
                    $statusBadge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">Under Review</span>';
                    break;
                default:
                    $statusBadge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">Open</span>';
                    break;
            }

            $action = '<button type="button" onclick="viewCarDetail(' . $post->id . ', \'' . htmlspecialchars($post->car_number, ENT_QUOTES) . '\', \'' . htmlspecialchars($post->finding_desc, ENT_QUOTES) . '\', \'' . htmlspecialchars($deptName, ENT_QUOTES) . '\', \'' . $post->status . '\', \'' . htmlspecialchars($post->corrective_action, ENT_QUOTES) . '\', \'' . htmlspecialchars($post->preventive_action, ENT_QUOTES) . '\', \'' . $post->due_date . '\', \'' . ($post->dept_head_approved_at ? 'Approved' : 'Pending') . '\', \'' . ($post->auditor_verified_at ? 'Verified' : 'Pending') . '\', \'' . ($post->qmr_approved_at ? 'Closed' : 'Pending') . '\')" class="px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg text-xs font-bold transition-colors">
                        Open Details
                       </button>';

            $data[] = [
                'car_number' => $post->car_number,
                'finding_desc' => $post->finding_desc,
                'auditee_dept' => $deptName,
                'due_date' => $post->due_date ?? '-',
                'status' => $statusBadge,
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
}
