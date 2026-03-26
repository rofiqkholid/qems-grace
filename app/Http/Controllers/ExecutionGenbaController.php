<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GenbaManagement;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExecutionGenbaController extends Controller
{

    public function table(Request $request)
    {
        $search = $request->search;
        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $dept = $request->dept;

        // For Verification/Approval, we might want to see all or filter by specific status
        // Reusing logic from GenbaManagementController for consistency

        $columns = array(
            0 => 'a.created_at',
            1 => 'DocNum',
            2 => 'b.Date',
            3 => 'a.findings',
            4 => 'a.asign_to_dept',
            5 => 'b.Auditor',
            6 => 'a.Path',
            7 => 'a.execution_path',
            8 => 'status_computed',
            9 => 'a.SysID'
        );
        $query = GenbaManagement::get_genba_approval_list($search, $date_from, $date_to, null, $dept);
        $query->where('a.corrective_action', 1)->where('a.evidence', 1);

        $totalData = $query->count();
        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = ($request->input('order.0.column') == 0 ? $columns[0] : $columns[$request->input('order.0.column')]);
        $dir = ($request->input('order.0.column') == 0 ? 'desc' : $request->input('order.0.dir'));

        $postsQuery = GenbaManagement::get_genba_approval_list($search, $date_from, $date_to, null, $dept);
        $postsQuery->where('a.corrective_action', 1)->where('a.evidence', 1);
        $postsQuery->addSelect(DB::raw("(CASE 
            WHEN (a.execution_comment IS NULL OR a.execution_comment = '') THEN 'Need Action Plan' 
            WHEN (a.execution_path IS NULL OR a.execution_path = '') THEN 'Need Evidence' 
            WHEN (a.execution_path IS NULL OR a.execution_path = '') THEN 'Need Evidence' 
            WHEN (a.verification_result = 2) THEN 'Rejected'
            WHEN (a.verification_result IS NULL OR a.verification_result = '') THEN 'Proccess Verification' 
            WHEN (a.verification_result = 1) THEN 'Close' 
            ELSE 'Close' 
        END) as status_computed"));
        $postsQuery->addSelect('a.verif_img');

        $posts = $postsQuery->offset($start)
            ->limit($limit)
            ->reorder($order, $dir)
            ->get();

        $data = array();
        if (!empty($posts)) {
            $no = $start;
            foreach ($posts as $post) {
                $no++;
                $trc_id = Crypt::encryptString($post->SysID);
                $sys_id = "'" . str_replace("=", "-", $trc_id) . '_' . $no . "'";

                $execution_comment = $post->execution_comment;
                $verification_result = $post->verification_result;
                $execution_path = $post->execution_path;
                $date = Carbon::parse($post->Date)->format('d M Y');

                if ($verification_result == 1 || $verification_result == 2) {
                    $verifImg = $post->verif_img ? $post->verif_img : '';
                    $button = '<div class="flex items-center justify-center gap-2">
                                <button type="button" title="Rollback" class="w-10 h-10 flex items-center justify-center rounded-xl bg-amber-50 text-amber-600 border border-amber-200 hover:bg-amber-100 hover:text-amber-700 transition-all duration-200" onclick="rollbackGenba(' . $sys_id . ', \'' . $verifImg . '\')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
                                        <path d="M3 3v5h5"></path>
                                    </svg>
                                </button>
                           </div>';
                } else {
                    $button = '<div class="flex items-center justify-center gap-2">
                                <button type="button" title="Approve" class="w-10 h-10 flex items-center justify-center rounded-xl bg-green-50 text-green-600 border border-green-200 hover:bg-green-100 hover:text-green-700 transition-all duration-200" onclick="approveGenba(' . $sys_id . ')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </button>
                           </div>';
                }

                $line = '<div class="w-8 h-0.5 bg-gray-200"></div>';
                $activeLine = '<div class="w-8 h-0.5 bg-blue-200"></div>';
                $rejectedLine = '<div class="w-8 h-0.5 bg-red-100"></div>';
                $renderCircle = function ($isActive) {
                    return $isActive
                        ? '<div class="w-10 h-10 rounded-full bg-blue-50 border border-blue-200 flex items-center justify-center text-blue-500 shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                           </div>'
                        : '<div class="w-10 h-10 rounded-full border border-slate-200 bg-white shadow-sm"></div>';
                };
                $renderRejectedCircle = function () {
                    return '<div class="w-10 h-10 rounded-full bg-red-50 border border-red-200 flex items-center justify-center text-red-500 shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                           </div>';
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
                } else if ($verification_result == 2) {
                    // Rejected
                    $steps = $renderCircle(true) . $activeLine . $renderCircle(true) . $rejectedLine . $renderRejectedCircle();
                } else {
                    // Closed
                    $steps = $renderCircle(true) . $activeLine . $renderCircle(true) . $activeLine . $renderCircle(true);
                }

                $statusIcons = '<div class="flex items-center justify-center gap-0 py-1">' . $steps . '</div>';

                $nestedData['no'] = $no;
                $nestedData['DocNum'] = $post->DocNum;
                $nestedData['path'] = $post->Path;
                $nestedData['execution_path'] = $post->execution_path;
                $nestedData['date'] = $date;
                $nestedData['asign_to_dept'] = $post->asign_to_dept;
                $nestedData['findings'] = $post->findings;
                $nestedData['status'] = $statusIcons;
                $nestedData['action'] = $button;
                $nestedData['auditor'] = $post->Auditor;
                $nestedData['execution_comment'] = $post->execution_comment;
                $nestedData['execution_comment'] = $post->execution_comment;
                $nestedData['area_detail'] = $post->area_detail;
                $nestedData['area_checked'] = $post->Area_Checked;
                $nestedData['verif_img'] = $post->verif_img;

                $data[] = $nestedData;
            }
        }

        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );

        return response()->json($json_data);
    }

    public function approve(Request $request)
    {
        try {
            $id = $request->id;
            $verificationResult = $request->input('verification_result', 1);
            if ($verificationResult === '' || $verificationResult === null) {
                $verificationResult = 1;
            }
            $verificationResult = (int) $verificationResult;
            if (!in_array($verificationResult, [1, 2], true)) {
                return response()->json(['status' => 'error', 'message' => 'Invalid verification result.']);
            }
            // ... (decryption logic same as before, simplified for this snippet)
            $parts = explode('_', $id);
            if (count($parts) > 1 && is_numeric(end($parts))) {
                array_pop($parts);
                $encrypted_id = implode('_', $parts);
            } else {
                $encrypted_id = $id;
            }
            $encrypted_id = str_replace("-", "=", $encrypted_id);
            $decrypted_id = Crypt::decryptString($encrypted_id);

            // Handle File Upload
            $fileName = null;
            $dbPath = null;
            if ($request->hasFile('verif_img')) {
                $file = $request->file('verif_img');
                $fileName = time() . '_' . $decrypted_id . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('verif-photo/photos'), $fileName);
                $dbPath = 'photos/' . $fileName;
            } else {
                return response()->json(['status' => 'error', 'message' => 'Verification photo is required!']);
            }

            DB::connection('sqlsrv')->table('GenbaProcAuditDtl')
                ->where('SysID', $decrypted_id)
                ->update([
                    'verification_result' => $verificationResult,
                    'verif_img' => $dbPath,
                    'updated_at' => Carbon::now()
                ]);

            $successMessage = $verificationResult === 2 ? 'Rejected successfully' : 'Approved successfully';
            return response()->json(['status' => 'success', 'message' => $successMessage]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function rollback(Request $request)
    {
        try {
            $id = $request->id;

            // Decryption logic
            $parts = explode('_', $id);
            if (count($parts) > 1 && is_numeric(end($parts))) {
                array_pop($parts);
                $encrypted_id = implode('_', $parts);
            } else {
                $encrypted_id = $id;
            }
            $encrypted_id = str_replace("-", "=", $encrypted_id);
            $decrypted_id = Crypt::decryptString($encrypted_id);

            // Get existing image to delete
            $existing = DB::connection('sqlsrv')->table('GenbaProcAuditDtl')
                ->where('SysID', $decrypted_id)
                ->select('verif_img')
                ->first();

            if ($existing && $existing->verif_img) {
                // Now verif_img includes 'photos/', so we look in 'verif-photo/'
                $filePath = public_path('verif-photo/' . $existing->verif_img);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            // Update the record: Set verification_result to NULL
            DB::connection('sqlsrv')->table('GenbaProcAuditDtl')
                ->where('SysID', $decrypted_id)
                ->update([
                    'verification_result' => null,
                    'verif_img' => null,
                    'updated_at' => Carbon::now()
                ]);

            return response()->json(['status' => 'success', 'message' => 'Rollback successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
}
