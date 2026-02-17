<?php

namespace App\Http\Controllers;

use App\Models\GenbaManagement;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SummaryGenbaController extends Controller
{
    public function index()
    {
        return view('summary.spv_verification');
    }

    public function table(Request $request)
    {
        $search = $request->search;
        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $dept = $request->dept;

        $columns = array(
            0 => 'a.created_at',
            1 => 'a.findings',
            2 => 'a.Path',
            3 => 'a.execution_comment',
            4 => 'a.preventive_action',
            5 => 'a.execution_path',
            6 => 'a.verif_img',
            7 => 'a.asign_to_dept',
            8 => 'b.Auditor'
        );

        $query = GenbaManagement::get_genba_approval_list($search, $date_from, $date_to, null, $dept);
        $query->where('a.corrective_action', 1)
            ->where('a.evidence', 1)
            ->whereNotNull('a.verification_result')
            ->where('a.verification_result', '!=', '');

        $totalData = $query->count();
        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = ($request->input('order.0.column') == 0 ? $columns[0] : $columns[$request->input('order.0.column')]);
        $dir = ($request->input('order.0.column') == 0 ? 'desc' : $request->input('order.0.dir'));

        $postsQuery = GenbaManagement::get_genba_approval_list($search, $date_from, $date_to, null, $dept);
        $postsQuery->where('a.corrective_action', 1)
            ->where('a.evidence', 1)
            ->whereNotNull('a.verification_result')
            ->where('a.verification_result', '!=', '')
            ->addSelect('a.preventive_action')
            ->addSelect('a.verif_img');

        $posts = $postsQuery->offset($start)
            ->limit($limit)
            ->reorder($order, $dir)
            ->get();

        $data = array();
        if (!empty($posts)) {
            $no = $start;
            foreach ($posts as $post) {
                $no++;

                $nestedData['no'] = $no;
                $nestedData['findings'] = $post->findings;
                $nestedData['path'] = $post->Path;
                $nestedData['execution_comment'] = $post->execution_comment;
                $nestedData['preventive_action'] = $post->preventive_action;
                $nestedData['execution_path'] = $post->execution_path;
                $nestedData['verif_img'] = $post->verif_img;
                $nestedData['verification_result'] = $post->verification_result;
                $nestedData['date'] = Carbon::parse($post->Date)->format('d M Y');
                $nestedData['DocNum'] = $post->DocNum;
                $nestedData['asign_to_dept'] = $post->asign_to_dept;
                $nestedData['auditor'] = $post->Auditor;

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
}
