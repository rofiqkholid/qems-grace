<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GenbaManagement;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Result;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class GenbaManagementController extends Controller
{
    public function findingsGenba()
    {
        return view('activity.findings_genba');
    }

    public function front_mng_table(Request $request)
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
                $button = '<div class="flex items-center justify-center gap-2">
                                <button type="button" title="Preview" class="w-10 h-10 flex items-center justify-center rounded-xl bg-blue-50 text-blue-500 hover:bg-blue-100 hover:text-blue-600 transition-all duration-200" id="btn_form_view_doc_' . $no . '" onclick="document_preview(' . $sys_id . ',' . $no . ')">
                                    <span id="svg_form_view_doc_' . $no . '" class="flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="currentColor"></path>
                                            <path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="currentColor"></path>
                                            <path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="currentColor"></path>
                                        </svg>
                                    </span>
                                    <span id="spinner_form_view_doc_' . $no . '" class="hidden animate-spin rounded-full h-4 w-4 border-b-2 border-current"></span>
                                </button>
                                
                                <button type="button" title="Delete" class="w-10 h-10 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-100 hover:text-red-600 transition-all duration-200" 
                                    id="btn_f_genba_conform_delete_' . $no . '" 
                                    onclick="f_genba_conform_delete(' . $sys_id . ',' . $no . ')">
                                    
                                    <span id="icon_f_genba_conform_delete_' . $no . '" class="flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-600" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.3" d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"/>
                                            <path d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V7H5V5Z" fill="currentColor"/>
                                            <path d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"/>
                                        </svg>
                                    </span>
                                    
                                    <span id="loader_f_genba_conform_delete_' . $no . '" class="hidden animate-spin rounded-full h-4 w-4 border-b-2 border-current"></span>
                                </button>
                           </div>';

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
                $nestedData['path'] = $post->Path;
                $nestedData['findings'] = $post->findings;
                $nestedData['due_date'] = $post->due_date;
                $nestedData['execution_comment'] = $post->execution_comment;
                $nestedData['execution_path'] = '<button class="btn btn-sm w-9 h-9 flex items-center justify-center bg-blue-600 text-white hover:bg-blue-700 rounded-lg transition-colors" id="btn_corrective_path_' . $no . '" onclick="btn_corrective(' . $sys_id . ',' . $no . ')"><i class="fa fa-camera"></i></button>';
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

    public function delete(Request $request)
    {
        try {
            $rawSysId = trim($request->sys_id, "'");
            $sysId = Crypt::decryptString(str_replace("-", "=", explode('_', $rawSysId)[0]));

            DB::connection('sqlsrv')->table('GenbaProcAuditDtl')->where('SysID', $sysId)->delete();

            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }

    public function preview($id)
    {
        try {
            // Decrypt the ID
            $sysId = Crypt::decryptString(str_replace("-", "=", explode('_', $id)[0]));

            // Get the genba data using DB query builder
            $genba = DB::connection('sqlsrv')
                ->table('GenbaProcAuditDtl as a')
                ->leftJoin('GenbaProcAudit as b', 'b.SysID', '=', 'a.genba_id')
                ->join('GenbaCategory as c', 'c.SysID', '=', 'b.Category_id')
                ->select(
                    'a.SysID',
                    'b.Date',
                    'b.Area_Checked',
                    'a.findings',
                    'a.Path',
                    'a.asign_to',
                    'a.asign_to_dept',
                    'a.type',
                    'a.area_detail',
                    'a.area_detail',
                    'a.corrective_action',
                    'a.preventive_action',
                    'a.evidence',
                    'a.evidence',
                    'a.status',
                    'a.due_date',
                    'a.complete_date',
                    'a.execution_comment',
                    'a.execution_path',
                    'a.verification_result',
                    'b.Auditor',
                    DB::raw("FORMAT(b.Date, 'ddMMyy') + '-' + CAST(a.SysID AS VARCHAR(20)) as DocNum")
                )
                ->where('a.SysID', $sysId)
                ->where('b.IsDelete', 0)
                ->first();

            if (!$genba) {
                abort(404, 'Data tidak ditemukan');
            }

            // Determine status and badge class
            if (empty($genba->execution_comment)) {
                $genba->status = 'Need Action Plan';
                $genba->statusBadgeClass = 'bg-amber-50 text-amber-700 border-amber-200';
            } else if (empty($genba->execution_path)) {
                $genba->status = 'Need Evidence';
                $genba->statusBadgeClass = 'bg-amber-50 text-amber-700 border-amber-200';
            } else if (empty($genba->verification_result)) {
                $genba->status = 'Process Verification';
                $genba->statusBadgeClass = 'bg-blue-50 text-blue-700 border-blue-200';
            } else {
                $genba->status = 'Close';
                $genba->statusBadgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
            }

            // Add trc_unix_id for save action
            $genba->trc_unix_id = $id;

            return view('activity.findings_genba_preview', compact('genba'));
        } catch (\Exception $e) {
            abort(404, 'Data tidak valid: ' . $e->getMessage());
        }
    }

    // ========== GENBA HEADER METHODS ==========

    public function genbaHeaderTable(Request $request)
    {
        $search = $request->front_table_search;
        $status_id = $request->status_id;
        $columns = array(
            0 => 'SysID',
            1 => 'date',
            2 => 'process',
            3 => 'station',
            4 => 'Area_checked',
            5 => 'auditor',
            6 => 'category',
        );
        $totalData = GenbaManagement::get_genba_activity_list($search, $status_id)->count();
        $totalFiltered = $totalData;
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = ($request->input('order.0.column') == 0 ? $columns[0] : $columns[$request->input('order.0.column')]);
        $dir = ($request->input('order.0.column') == 0 ? 'desc' : $request->input('order.0.dir'));
        if (empty($search)) {
            $posts = GenbaManagement::get_genba_activity_list($search, $status_id)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $posts = GenbaManagement::get_genba_activity_list($search, $status_id)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = GenbaManagement::get_genba_activity_list($search, $status_id)->count();
        }
        $data = array();
        if (!empty($posts)) {
            $no = $start;
            foreach ($posts as $post) {
                $no++;
                $trc_id = Crypt::encryptString($post->SysID);
                $sys_id = "'" . str_replace("=", "-", $trc_id) . '_' . $no . "'";

                $button = '<div class="flex items-center justify-center gap-2">
                                <button type="button" title="View" class="w-10 h-10 flex items-center justify-center rounded-xl bg-blue-50 text-blue-500 hover:bg-blue-100 hover:text-blue-600 transition-all duration-200" onclick="document_view(' . $sys_id . ',' . $no . ')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="currentColor"></path>
                                    <path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="currentColor"></path><path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="currentColor"></path>
                                </svg>
                                </button>
                                
                                <button type="button" title="Delete" class="w-10 h-10 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-100 hover:text-red-600 transition-all duration-200" 
                                    id="btn_f_genba_header_delete_' . $no . '" 
                                    onclick="f_genba_header_delete(' . $sys_id . ',' . $no . ')">
                                    
                                    <span id="icon_f_genba_header_delete_' . $no . '" class="flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-600" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.3" d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"/>
                                            <path d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V7H5V5Z" fill="currentColor"/>
                                            <path d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"/>
                                        </svg>
                                    </span>
                                    
                                    <span id="loader_f_genba_header_delete_' . $no . '" class="hidden animate-spin rounded-full h-4 w-4 border-b-2 border-current"></span>
                                </button>
                           </div>';

                $date = Carbon::parse($post->Date)->format('d M Y');

                $nestedData['no'] = $no;
                $nestedData['date'] = $date;
                $nestedData['process'] = $post->process;
                $nestedData['station'] = $post->station;
                $nestedData['line_checked'] = $post->Area_checked;
                $nestedData['auditor'] = $post->Auditor;
                $nestedData['category'] = $post->category;
                $nestedData['action'] = $button;
                $data[] = $nestedData;
            }
        } else {
            $nestedData['no'] = '';
            $nestedData['date'] = '';
            $nestedData['process'] = '';
            $nestedData['station'] = '';
            $nestedData['line_checked'] = '';
            $nestedData['auditor'] = '';
            $nestedData['category'] = '';
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

    public function genbaHeaderDelete(Request $request)
    {
        try {
            $rawSysId = trim($request->sys_id, "'");
            $sysId = Crypt::decryptString(str_replace("-", "=", explode('_', $rawSysId)[0]));

            // Soft delete by setting IsDelete to 1
            DB::connection('sqlsrv')->table('GenbaProcAudit')->where('SysID', $sysId)->update(['IsDelete' => 1]);

            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }

    public function genbaHeaderView($id)
    {
        try {
            $sysId = Crypt::decryptString(str_replace("-", "=", explode('_', $id)[0]));

            $genba = DB::connection('sqlsrv')->table('GenbaProcAudit as a')
                ->leftJoin('GenbaCategory as b', 'b.SysID', '=', 'a.Category_id')
                ->leftJoin('GenbaProcess as c', 'c.SysID', '=', 'a.process_id')
                ->select(
                    'a.*',
                    'b.category_name',
                    'c.process_name'
                )
                ->where('a.SysID', $sysId)
                ->where('a.IsDelete', 0)
                ->first();

            if (!$genba) {
                abort(404, 'Data tidak ditemukan');
            }

            return view('activity.genba_header_view', compact('genba'));
        } catch (\Exception $e) {
            abort(404, 'Data tidak valid: ' . $e->getMessage());
        }
    }

    public function form_genba_header_activity(Request $request)
    {
        $str_req = explode("_", $request->trc_unix_id);

        if ($str_req[0] != "0") {
            $str = explode("_", Crypt::decryptString(str_replace("-", "=", $str_req[0])));
            $sysID = $str[0];
        } else {
            $sysID = $str_req[0];
        }
        $data = GenbaManagement::get_genba_activity($sysID)->get();
        $count = GenbaManagement::get_genba_activity($sysID)->count();
        $form_genba = [];
        if ($count > 0) {
            foreach ($data as $d) {
                $form_genba['area_checked'] = $d->Area_Checked;
                $form_genba['auditor'] = $d->Auditor;
                $form_genba['date'] = $d->Date;
                $form_genba['process'] = $d->process;
                $form_genba['station'] = $d->station;
                $form_genba['category_id'] = $d->Category_id;
                $form_genba['category'] = $d->category . ' - ' . $d->category_desc;
            }
        } else {
            $form_genba['area_checked'] = '';
            $form_genba['auditor'] = Auth::user()->full_name ?? '';
            $form_genba['date'] = date('Y-m-d');
            $form_genba['process'] = '';
            $form_genba['station'] = '';
            $form_genba['category_id'] = 0;
            $form_genba['category'] = '';
        }
        $form_genba['process_options'] = ['STP', 'ASSY', 'Receiving & Delivery', 'Storage'];
        $form_genba['trc_unix_id'] = $request->trc_unix_id;
        $form_genba['head_title'] = "Genba Activity";

        return response()->json($form_genba);
    }

    public function get_genba_area(Request $request)
    {
        $search = $request->search;
        $processFilter = $request->process;
        $page = $request->post('page', 1);
        $pageSize = 10;

        $query = GenbaManagement::get_genba_area();

        if (!empty($processFilter)) {
            $query->where('Process', $processFilter);
        }

        if ($search) {
            $query->where('Area_name', 'LIKE', '%' . $search . '%');
        }

        $areas = $query->paginate($pageSize, ['*'], 'page', $page);

        return response()->json([
            'items' => collect($areas->items())->map(function ($area) {
                return [
                    'id' => $area->SysID,
                    'name' => $area->Area_name
                ];
            }),
            'pagination' => [
                'more' => $areas->hasMorePages(),
            ]
        ]);
    }

    public function get_genba_category(Request $request)
    {
        $search = $request->search;
        $page = $request->post('page', 1);
        $pageSize = 10;
        $query = GenbaManagement::get_genba_category();
        if ($search) {
            $query->where('Description', 'LIKE', '%' . $search . '%');
        }
        $categories = $query->paginate($pageSize, ['*'], 'page', $page);
        return response()->json([
            'items' => collect($categories->items())->map(function ($categories) {
                return [
                    'id' => $categories->SysID,
                    'name' => $categories->Category . '-' . $categories->Description
                ];
            }),
            'pagination' => [
                'more' => $categories->hasMorePages(),
            ]
        ]);
    }

    public function get_section(Request $request)
    {
        $search = $request->search;
        $page = $request->post('page', 1);
        $pageSize = 10;

        $query = GenbaManagement::get_section_list();
        if ($search) {
            $query->where('Desc', 'LIKE', '%' . $search . '%');
        }
        $areas = $query->paginate($pageSize, ['*'], 'page', $page);
        return response()->json([
            'items' => collect($areas->items())->map(function ($area) {
                return [
                    'id' => $area->id,
                    'name' => $area->desc
                ];
            })->values(),
            'pagination' => [
                'more' => $areas->hasMorePages(),
            ]
        ]);
    }

    public function get_user_data(Request $request)
    {
        $search = $request->search;
        $page = $request->post('page', 1);
        $pageSize = 10;

        $query = GenbaManagement::get_users($search);

        $users = $query->paginate($pageSize, ['*'], 'page', $page);

        return response()->json([
            'items' => collect($users->items())->map(function ($user) {
                return [
                    'id' => $user->username,
                    'name' => $user->full_name,
                    'text' => $user->full_name // For compatibility
                ];
            }),
            'pagination' => [
                'more' => $users->hasMorePages(),
            ]
        ]);
    }

    public function add_genba(Request $request)
    {
        $str_req = explode("_", $request->trc_unix_id);

        if ($str_req[0] != "0") {
            $str = explode("_", Crypt::decryptString(str_replace("-", "=", $str_req[0])));
            $sysID = $str[0];
        } else {
            $sysID = $str_req[0];
        }

        $area_checked = $request->input('line_checked'); // Updated from area_checked to match form name
        $date = $request->input('date');
        $auditor = $request->input('auditor');
        $category = $request->input('category'); // Updated from genba_category to match form name
        $station = $request->input('station');
        $process = $request->input('process');

        $insert = GenbaManagement::add_genba_activity($area_checked, $auditor, $category, $date, $sysID, $station, $process);

        if ($insert > 0) {
            $db = DB::table('GenbaProcAudit')
                ->where('SysID', $insert)
                ->select('SysID', 'Category_id');

            if ($db->count() > 0) {
                $category_id = $db->first()->Category_id;

                if ($category_id == 4) {
                    return $this->add_genba_rusty($db->first()->SysID);
                }

                // Default logic for other categories (omitted/placeholder based on user provided snippet)
                // Assuming the user will add more logic or this is sufficient for now.
                // The snippet provided has logic for other categories, will include it.

                $db_activity = DB::table('GenbaProcAuditDtl')
                    ->where('SysID', $insert)
                    ->select('SysID');

                $data["code"] = 200;
                $data["id_activity"] = $db->first()->SysID;
                $data["process"] = $process . '-' . $station;

                $dbScopes = DB::table('GenbaAuditItem as b')
                    ->leftJoin('GenbaProcAuditDtl as c', function ($join) use ($insert) {
                        $join->on('b.SysID', '=', 'c.check_item_id')
                            ->where('c.genba_id', '=', $insert);
                    })
                    ->leftJoin('GenbaProcAudit as d', function ($join) {
                        $join->on('d.SysID', '=', 'c.genba_id')
                            ->where('d.IsDelete', '=', 0);
                    })
                    ->where('b.Category', '=', $category_id)
                    ->select(
                        'b.scope_id as scope_id',
                        'b.scope_item',
                        'b.SysID as check_item_id',
                        'b.Photos as foto',
                        'b.Check_item',
                        'b.Check_item_eng',
                        'c.result as Hasil',
                        'c.Path'
                    )
                    ->get()
                    ->unique('check_item_id');

                $scopes = [];
                foreach ($dbScopes as $item) {
                    $scopes[$item->scope_item][] = [
                        'scope_id' => $item->scope_id,
                        'check_item_id' => $item->check_item_id,
                        'check_item' => $item->Check_item,
                        'check_item_eng' => $item->Check_item_eng,
                        'foto' => $item->foto,
                        'result' => $item->Hasil,
                        'photo' => $item->Path
                    ];
                }

                // --- NEW: Fetch all finding rows to determine status of distinct findings (1, 2, 3) ---
                $allFindings = DB::table('GenbaProcAuditDtl')
                    ->where('genba_id', $insert)
                    ->orderBy('SysID', 'asc') // Important: Order by SysID correlates to Index 1, 2, 3
                    ->get();

                $findingStatus = [];
                $itemCounts = []; // Helper to track index per check_item_id

                foreach ($allFindings as $finding) {
                    $itemId = $finding->check_item_id;

                    if (!isset($itemCounts[$itemId])) {
                        $itemCounts[$itemId] = 0;
                    }
                    $itemCounts[$itemId]++;
                    $currentIndex = $itemCounts[$itemId];

                    // Mark as true if there is content (findings text or photos path)
                    $hasContent = !empty($finding->findings) || !empty($finding->Path);

                    $findingStatus["{$itemId}_{$currentIndex}"] = $hasContent;
                }

                $data["scopes"] = $scopes;
                $data["finding_status"] = $findingStatus;
                $data["finding_types"] = collect(['Quality', 'Safety & Environment', 'Cost', 'Delivery'])->map(function ($t) {
                    return ['id' => $t, 'name' => $t];
                })->toArray();
                // Double check view path for standard activity
                return view('activity.form-checksheet.activity_form', $data); // Assuming standard path
            }
        } else {
            $data["code"] = 500;
        }
        return $data;
    }

    public function add_genba_rusty($id_activity)
    {
        $audit = DB::table('GenbaProcAudit')->where('SysID', $id_activity)->first();

        if (!$audit) {
            abort(404, 'Data audit dengan ID tersebut tidak ditemukan.');
        }
        $category_id = $audit->Category_id;
        $dbScopes = DB::table('GenbaAuditItem as b')
            ->leftJoin('GenbaProcAuditDtl as s', 'b.scope_id', '=', 's.SysID')
            ->where('b.Category', $category_id)
            ->select('b.SysID as check_item_id', 'b.scope_id')
            ->select(
                'b.scope_id as scope_id',
                'b.scope_item'
            )
            ->get();

        $scopes = [];
        foreach ($dbScopes as $item) {
            $scopes[$item->scope_item][] = [
                'scope_id' => $item->scope_id,
            ];
        }

        return view('activity.no-checksheet.activity_rusty', [
            'id_activity' => $id_activity,
            'scopes' => $scopes
        ]);
    }

    public function post_form_spv(Request $request)
    {
        $my_id = Auth::user()->username;
        $id_activity = $request->input('activity_id');
        $scope_id = $request->input('scope_id');
        $check_item_id = $request->input('check_item_id');
        $result = $request->input('answer');
        $check_date = GenbaManagement::check_date_activity($id_activity);

        $date = null;
        foreach ($check_date->get() as $d) {
            $date = $d->Date;
        }

        if (!$date) {
            return json_encode([
                'code' => 500,
                'check_item_id' => $check_item_id,
                'message' => 'Date not found for activity',
                'result' => ''
            ]);
        }

        $due_date = Carbon::parse($date)->addWeeks(2)->format('Y-m-d');

        $insert = GenbaManagement::save_genba_activity_detail($my_id, $id_activity, $scope_id, $check_item_id, $result, $due_date);

        $data = [];
        if ($insert) {
            $data['code'] = 200;
            $data['check_item_id'] = $check_item_id;
            $data['message'] = 'Data berhasil disimpan';
            $data['result'] = $result;
        } else {
            $data['code'] = 500;
            $data['check_item_id'] = $check_item_id;
            $data['message'] = 'Data gagal disimpan';
            $data['result'] = '';
        }
        return json_encode($data);
    }

    public function get_data_photo(Request $request)
    {
        $activity_id = $request->input('activity_id');
        $scope_id = $request->input('scope_id');
        $check_item_id = $request->input('check_item_id');
        $finding_index = $request->input('finding_index', 1); // Default to 1

        // Fetch all rows for this item, sorted by ID to ensure finding 1 is first
        $existingRows = DB::table('GenbaProcAuditDtl')
            ->where('genba_id', $activity_id)
            ->where('scope_id', $scope_id)
            ->where('check_item_id', $check_item_id)
            ->orderBy('SysID', 'asc')
            ->get();

        if ($existingRows->count() == 0) {
            $data['photo'] = []; // Array for frontend
            $data['findings'] = '';
            $data['asign_to'] = '';
            $data['asign_to_name'] = '';
            $data['asign_to_dept'] = '';
            $data['asign_to_dept_name'] = '';
            $data['type'] = '';
            $data['area_detail'] = '';
            echo json_encode($data);
        } else {
            $targetRow = null;
            $currentIndex = 1;

            // Iterate to find the row corresponding to finding_index
            foreach ($existingRows as $row) {
                if ($currentIndex == $finding_index) {
                    $targetRow = $row;
                    break;
                }
                $currentIndex++;
            }

            if ($targetRow) {
                // Return data specific to this row
                // Decode fields if they are JSON, though mostly they should be strings now.
                // However, for compatibility if they are still JSON arrays in DB, we treat them as strings if they are simple values.
                // Assuming the new storage logic sets them as plain strings (imploded arrays/text).

                // BUT: We stored implode(',', photoPaths) in 'Path'
                // and 'findings' text in 'findings'
                // so we don't need JSON decoding anymore for the main fields.

                $photoString = $targetRow->Path ?? '';
                $data['photo'] = !empty($photoString) ? explode(',', $photoString) : [];

                $data['findings'] = $targetRow->findings ?? '';
                $data['asign_to'] = $targetRow->asign_to ?? '';
                $data['asign_to_name'] = $targetRow->asign_to_name ?? '';
                $data['asign_to_dept'] = $targetRow->asign_to_dept ?? '';
                $data['asign_to_dept_name'] = $targetRow->asign_to_dept_name ?? '';
                $data['type'] = $targetRow->type ?? '';
                $data['area_detail'] = $targetRow->area_detail ?? '';
                $data['type'] = $targetRow->type ?? '';
            } else {
                // Row for this index doesn't exist yet
                $data['photo'] = [];
                $data['findings'] = '';
                $data['asign_to'] = '';
                $data['asign_to_name'] = '';
                $data['asign_to_dept'] = '';
                $data['asign_to_dept_name'] = '';
                $data['type'] = '';
                $data['area_detail'] = '';
            }

            echo json_encode($data);
        }
    }

    public function post_photo_spv(Request $request)
    {
        $my_id = Auth::user()->username;
        $id_activity = $request->input('activity_id');
        $scope_id = $request->input('scope_id');
        $check_item_id = $request->input('check_item_id');
        $finding_index = $request->input('finding_index', 1); // Default to 1
        $dataphoto = $request->input('dataphoto');
        if (!is_array($dataphoto)) $dataphoto = [];

        $findings_text = $request->input('findings');
        $asign_to = $request->input('asign_to');
        $asign_to_name = $request->input('asign_to_name');
        $asign_to_dept_name = $request->input('asign_to_dept_name');
        $asign_to_dept = $request->input('asign_to_dept');

        $detail_area = $request->input('detail_area');
        $due_date = $request->input('due_date');
        $type = $request->input('type');

        if (!$due_date) {
            $check_date = GenbaManagement::check_date_activity($id_activity);
            $date_header = null;
            foreach ($check_date->get() as $d) {
                $date_header = $d->Date;
            }
            if ($date_header) {
                $due_date = Carbon::parse($date_header)->addWeeks(2)->format('Y-m-d');
            }
        }

        // Validation - check required fields
        $errors = [];

        if (empty($findings_text)) {
            $errors[] = 'Findings are required';
        }

        if (empty($asign_to_dept)) {
            $errors[] = 'Assign to is required';
        }

        // Check if there are photos (either new or existing)
        $photoPaths = [];

        // Handle existing photos
        if ($request->has('existing_photos')) {
            $existing_photos = $request->input('existing_photos', []);
            if (is_array($existing_photos)) {
                $photoPaths = $existing_photos;
            }
        }

        // Process new photos
        if (!empty($dataphoto) && is_array($dataphoto)) {
            foreach ($dataphoto as $index => $image) {
                if (preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {
                    $imageBase64 = substr($image, strpos($image, ',') + 1);
                    $type = strtolower($type[1]);
                    if (!in_array($type, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                        continue;
                    }
                    $imageData = base64_decode($imageBase64);
                    $imageName = uniqid() . '_' . time() . ".{$type}";
                    $path = 'photos/' . $imageName;
                    $fullPath = public_path('findings-photo/' . $path);

                    // Create directory if not exists
                    if (!file_exists(public_path('findings-photo/photos'))) {
                        mkdir(public_path('findings-photo/photos'), 0755, true);
                    }

                    file_put_contents($fullPath, $imageData);
                    $photoPaths[] = $path;
                } elseif ($image instanceof \Illuminate\Http\UploadedFile) {
                    $imageName = uniqid() . '_' . time() . '.' . $image->getClientOriginalExtension();
                    $path = 'photos/' . $imageName;
                    $image->move(public_path('findings-photo/photos'), $imageName);
                    $photoPaths[] = $path;
                }
            }
        }

        // Validate photo count and existence
        if (count($photoPaths) == 0) {
            $errors[] = 'Evidence photo is required';
        }

        if (count($photoPaths) > 5) {
            $errors[] = 'Maksimal 5 foto';
        }

        // Return validation errors if any
        if (count($errors) > 0) {
            return response()->json([
                'success' => false,
                'message' => implode(', ', $errors),
                'errors' => $errors
            ], 422);
        }

        // --- Row-Based Storage Logic ---

        // Fetch all rows for this item, ordered by SysID
        $existingRows = DB::table('GenbaProcAuditDtl')
            ->where('genba_id', $id_activity)
            ->where('scope_id', $scope_id)
            ->where('check_item_id', $check_item_id)
            ->orderBy('SysID', 'asc')
            ->get();

        $targetRow = null;
        $rowExists = false;

        // Ensure we have enough rows for the requested index
        // If we want Finding 3 but have 0 rows, we need to create Row 1, Row 2, Row 3.
        while ($existingRows->count() < $finding_index) {
            // Create New SysID (Insert new row)
            $result = 2;
            if ($existingRows->count() > 0) {
                $result = $existingRows->first()->result;
            }

            DB::table('GenbaProcAuditDtl')->insert([
                'genba_id' => $id_activity,
                'scope_id' => $scope_id,
                'check_item_id' => $check_item_id,
                'result' => $result,
                'user_id' => $my_id,
                'due_date' => $due_date,
                'type' => $type,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now()
            ]);

            // Refresh count locally or re-fetch? 
            // Better to re-fetch after loop to get IDs correct.
            // But to loop condition work, we must increment count reference or just break and refetch.
            // Actually, let's just re-fetch the collection after padding.
            $existingRows = DB::table('GenbaProcAuditDtl')
                ->where('genba_id', $id_activity)
                ->where('scope_id', $scope_id)
                ->where('check_item_id', $check_item_id)
                ->orderBy('SysID', 'asc')
                ->get();
        }

        // Now we definitely have the row at index ($finding_index - 1)
        // Access 0-based index from the collection
        $targetRow = $existingRows->get($finding_index - 1);

        $photoPathsString = implode(',', $photoPaths);

        $updates = false;

        if ($targetRow) {
            // Update existing row
            $updates = DB::table('GenbaProcAuditDtl')
                ->where('SysID', $targetRow->SysID)
                ->update([
                    'Path' => $photoPathsString,
                    'findings' => $findings_text,
                    'asign_to' => $asign_to,
                    'asign_to_name' => $asign_to_name,
                    'asign_to_dept' => $asign_to_dept,
                    'asign_to_dept_name' => $asign_to_dept_name,
                    'area_detail' => $detail_area,
                    'due_date' => $due_date,
                    'type' => $type,
                    'updated_at' => \Carbon\Carbon::now()
                ]);
            $updates = true;
        }

        if ($updates) {
            return response()->json([
                'message' => 'Foto berhasil disimpan.',
                'photos' => $photoPaths
            ]);
        } else {
            return response()->json([
                'message' => 'Foto berhasil disimpan (no changes detected).',
                'photos' => $photoPaths
            ]);
        }
    }

    public function submit_form_genba(Request $request)
    {
        $activity_id = $request->input('genba_id');
        $insert = DB::table('GenbaProcAudit')
            ->where('SysID', $activity_id)
            ->update([
                'status' => 3,
                'updated_at' => Carbon::now()
            ]);
        if ($insert) {
            $data['code'] = 200;
            $data['message'] = 'Data berhasil disimpan';
        } else {
            $data['code'] = 500;
            $data['message'] = 'Data gagal disimpan';
        }
        return json_encode($data);
    }

    public function save_action_plan(Request $request)
    {
        $str_req = explode("_", $request->trc_unix_id);
        if ($str_req[0] != "0") {
            $str = explode("_", Crypt::decryptString(str_replace("-", "=", $str_req[0])));
            $sysID = $str[0];
        } else {
            $sysID = $str_req[0];
        }

        $my_id = Auth::user()->username;
        // Initialize photo paths with existing photos only if explicitly provided in request
        // This logic differs from previous check which relied on checking DB if dataphoto was empty
        // Now frontend sends EVERYTHING that should be kept.

        $execution_comment = $request->input('action_plan');
        $execution_comment = $request->input('action_plan');
        $preventive_action = $request->input('preventive_action');
        $photoPaths = [];
        $dataphoto = $request->input('dataphoto', []);

        // Handle existing photos
        $existing_photos = $request->input('existing_photos', []);
        if (is_array($existing_photos)) {
            $photoPaths = $existing_photos;
        }

        // Process new photos
        if (!empty($dataphoto) && is_array($dataphoto)) {
            $uploadPath = public_path('evidence-photo/photos');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            foreach ($dataphoto as $image) {
                if (preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {
                    $image = substr($image, strpos($image, ',') + 1);
                    $type = strtolower($type[1]);

                    if (!in_array($type, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                        continue;
                    }
                    $imageData = base64_decode($image);
                    $imageName = uniqid() . '_' . time() . ".{$type}";
                    $fullPath = $uploadPath . '/' . $imageName;

                    if (file_put_contents($fullPath, $imageData)) {
                        $photoPaths[] = 'photos/' . $imageName;
                    }
                }
            }
        }

        // Validate total count
        if (count($photoPaths) > 5) {
            return response()->json([
                'code' => 400,
                'message' => 'Maksimal 5 foto bukti',
            ]);
        }

        $photoPathsString = !empty($photoPaths) ? implode(',', $photoPaths) : null;

        // Check evidence & corrective_action
        $evidence = !empty($photoPathsString) ? 1 : 0;
        $corrective_action = !empty($execution_comment) ? 1 : 0;

        $updates = DB::table('GenbaProcAuditDtl')
            ->where('SysID', $sysID)
            ->update([
                'execution_path' => $photoPathsString,
                'execution_path' => $photoPathsString,
                'execution_comment' => $execution_comment,
                'preventive_action' => $preventive_action,
                'evidence' => $evidence,
                'corrective_action' => $corrective_action,
                'complete_date' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

        if ($updates) {
            return response()->json([
                'code' => 200,
                'message' => 'Data saved successfully.',
                'photos' => $photoPaths
            ]);
        } else {
            return response()->json([
                'code' => 500,
                'message' => 'Failed to save data.',
                'photos' => ''
            ]);
        }
    }
}
