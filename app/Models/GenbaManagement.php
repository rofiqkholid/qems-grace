<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class GenbaManagement extends Model
{

    public static function get_all_departments()
    {
        // 1. Fetch from Master Department (Just Codes)
        $masterDepts = DB::table('GenbaDept')
            ->orderBy('Key1')
            ->pluck('Key1')
            ->toArray();

        // 2. Fetch distinct used in transaction (legacy support)
        $usedDepts = DB::connection('sqlsrv')
            ->table('GenbaProcAuditDtl')
            ->select('asign_to_dept')
            ->distinct()
            ->whereNotNull('asign_to_dept')
            ->pluck('asign_to_dept')
            ->toArray();

        $allDepartments = array_unique(array_merge($masterDepts, $usedDepts));
        sort($allDepartments);

        return $allDepartments;
    }

    public static function get_master_departments()
    {
        // Fetch from Master Department with Name
        return DB::table('GenbaDept')
            ->select('Key1 as id', 'Desc as name')
            ->orderBy('Key1')
            ->get()
            ->map(function ($item) {
                return (object) ['id' => $item->id, 'name' => $item->name];
            })
            ->values()
            ->all();
    }

    public static function get_genba_mng_activity_list(mixed $search, $date_from = null, $date_to = null, $auditor = null, $dept = null, $status = null, $detail_area = null, $category_id = null)
    {
        // $my_id = Auth::user()->username;
        // $qems = ['121020-002', '031114-001', '260422-001'];
        $result = DB::connection('sqlsrv')->table('GenbaProcAuditDtl as a')
            ->join('GenbaProcAudit as b', 'b.SysID', '=', 'a.genba_id')
            ->leftJoin('GenbaCategory as c', 'c.SysID', '=', 'b.Category_id')
            ->select(
                'a.SysID',
                'b.Date',
                'b.Area_Checked',
                'a.findings',
                'a.Path',
                'a.asign_to',
                'a.asign_to_dept',
                'a.asign_to_dept_name',
                'a.type',
                'a.area_detail',
                'a.corrective_action',
                'a.evidence',
                'a.status',
                'a.due_date',
                'a.complete_date',
                'a.execution_comment',
                'a.execution_path',
                'a.verification_result',
                'a.verification_result',
                'b.Auditor',
                'b.process',
                DB::raw("FORMAT(b.Date, 'ddMMyy') + '-' + CAST(a.SysID AS VARCHAR(20)) as DocNum")
            )
            ->where(function ($q) {
                $q->where('b.IsDelete', 0)
                    ->orWhereNull('b.IsDelete');
            })
            ->orderBy('a.created_at', 'DESC')
            ->where(function ($q) {
                $q->where('a.result', '!=', 1)
                    ->orWhereNull('a.result');
            })
            ->whereNotNull('b.Auditor')
            ->where('b.Auditor', '!=', '');

        if ($category_id !== null) {
            if ($category_id === 'NOT_BIQ') {
                $result->where(function ($q) {
                    $q->whereNotIn('b.Category_id', [7, 8, 9])
                      ->orWhereNull('b.Category_id');
                });
            } elseif (is_array($category_id)) {
                $result->whereIn('b.Category_id', $category_id);
            } else {
                $result->where('b.Category_id', $category_id);
            }
        }

        if ($search) {
            $result->where(function ($q) use ($search) {
                $q->where('b.Area_Checked', 'LIKE', "%{$search}%")
                    ->orWhere('b.Auditor', 'LIKE', "%{$search}%")
                    ->orWhere('a.findings', 'LIKE', "%{$search}%")
                    ->orWhere('a.area_detail', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("FORMAT(b.Date, 'ddMMyy') + '-' + CAST(a.SysID AS VARCHAR(20))"), 'LIKE', "%{$search}%");
            });
        }

        if (!empty($date_from) && !empty($date_to)) {
            $result->where(DB::raw('CAST(b.Date AS DATE)'), '>=', $date_from)->where(DB::raw('CAST(b.Date AS DATE)'), '<=', $date_to);
        } elseif (!empty($date_from)) {
            $result->where(DB::raw('CAST(b.Date AS DATE)'), '>=', $date_from);
        } elseif (!empty($date_to)) {
            $result->where(DB::raw('CAST(b.Date AS DATE)'), '<=', $date_to);
        }

        if (!empty($auditor)) {
            $result->where('b.Auditor', 'LIKE', '%' . $auditor . '%');
        }

        if (!empty($dept)) {
            $result->where('a.asign_to_dept', $dept);
        }

        if (!empty($detail_area)) {
            $result->where('a.area_detail', $detail_area);
        }

        if (!empty($status)) {
            if ($status == 'OPEN') {
                $result->where(function ($q) {
                    $q->whereNull('a.execution_comment')->orWhere('a.execution_comment', '')
                        ->orWhereNull('a.execution_path')->orWhere('a.execution_path', '');
                })->where(function ($q) {
                    $q->whereNull('a.verification_result')->orWhere('a.verification_result', '')
                        ->orWhere('a.verification_result', '0');
                })->whereDate('a.due_date', '>=', Carbon::today());
            } elseif ($status == 'NEED_VERIF') {
                $result->whereNotNull('a.execution_comment')->where('a.execution_comment', '!=', '')
                    ->whereNotNull('a.execution_path')->where('a.execution_path', '!=', '')
                    ->where(function ($q) {
                        $q->whereNull('a.verification_result')->orWhere('a.verification_result', '')
                            ->orWhere('a.verification_result', '0');
                    });
            } elseif ($status == 'CLOSE') {
                $result->where(function ($q) {
                    $q->where('a.verification_result', '1')
                        ->orWhere('a.verification_result', 1);
                });
            } elseif ($status == 'OVERDUE') {
                $result->where(function ($q) {
                    $q->whereNull('a.execution_comment')->orWhere('a.execution_comment', '')
                        ->orWhereNull('a.execution_path')->orWhere('a.execution_path', '');
                })->where(function ($q) {
                    $q->whereNull('a.verification_result')->orWhere('a.verification_result', '')
                        ->orWhere('a.verification_result', '0');
                })->whereDate('a.due_date', '<', Carbon::today());
            }
        }

        return $result;
    }
    public static function get_genba_approval_list(mixed $search, $date_from = null, $date_to = null, $auditor = null, $dept = null, $detail_area = null)
    {
        // $my_id = Auth::user()->username;
        // $qems = ['121020-002', '031114-001', '260422-001'];
        $result = DB::connection('sqlsrv')->table('GenbaProcAuditDtl as a')
            ->join('GenbaProcAudit as b', 'b.SysID', '=', 'a.genba_id')
            ->leftJoin('GenbaCategory as c', 'c.SysID', '=', 'b.Category_id')
            ->select(
                'a.SysID',
                'b.Date',
                'b.Area_Checked',
                'a.asign_to_dept',
                'a.findings',
                'a.Path',
                'a.asign_to',
                'a.asign_to_dept',
                'a.type',
                'a.area_detail',
                'a.corrective_action',
                'a.evidence',
                'a.status',
                'a.due_date',
                'a.complete_date',
                'a.execution_comment',
                'a.execution_path',
                'a.verification_result',
                'a.verification_result',
                'b.Auditor',
                DB::raw("FORMAT(b.Date, 'ddMMyy') + '-' + CAST(a.SysID AS VARCHAR(20)) as DocNum")
            )
            ->where(function ($q) {
                $q->where('b.IsDelete', 0)
                    ->orWhereNull('b.IsDelete');
            })
            ->orderBy('a.created_at', 'DESC')
            ->where(function ($q) {
                $q->where('a.result', '!=', 1)
                    ->orWhereNull('a.result');
            })
            ->whereNotNull('b.Auditor')
            ->where('b.Auditor', '!=', '');

        if ($search) {
            $result->where(function ($q) use ($search) {
                $q->where('a.asign_to_dept', 'LIKE', "%{$search}%")
                    ->orWhere('b.Auditor', 'LIKE', "%{$search}%")
                    ->orWhere('a.findings', 'LIKE', "%{$search}%")
                    ->orWhere('a.area_detail', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("FORMAT(b.Date, 'ddMMyy') + '-' + CAST(a.SysID AS VARCHAR(20))"), 'LIKE', "%{$search}%");
            });
        }

        if (!empty($date_from) && !empty($date_to)) {
            $result->where(DB::raw('CAST(b.Date AS DATE)'), '>=', $date_from)->where(DB::raw('CAST(b.Date AS DATE)'), '<=', $date_to);
        } elseif (!empty($date_from)) {
            $result->where(DB::raw('CAST(b.Date AS DATE)'), '>=', $date_from);
        } elseif (!empty($date_to)) {
            $result->where(DB::raw('CAST(b.Date AS DATE)'), '<=', $date_to);
        }

        if (!empty($auditor)) {
            $result->where('b.Auditor', 'LIKE', '%' . $auditor . '%');
        }

        if (!empty($dept)) {
            $result->where('a.asign_to_dept', $dept);
        }

        if (!empty($detail_area)) {
            $result->where('a.area_detail', $detail_area);
        }

        return $result;
    }

    public static function get_genba_activity_list(mixed $search, mixed $status_id, bool $is_room_team = false)
    {
        $my_nik = Auth::user()->username;
        $my_id = Auth::user()->id;
        $my_name = Auth::user()->full_name;
        $result = DB::connection('sqlsrv')->table('GenbaProcAudit as a')
            ->join('GenbaCategory as b ', 'b.SysID', '=', 'a.Category_id')
            ->select(
                'a.SysID',
                'a.Date',
                'a.process',
                'a.station',
                'a.Area_checked',
                'a.Auditor',
                'a.Category_id',
                'b.Description as category'
            );
        if (!empty($search)) {
            $result = $result->where(function ($q) use ($search) {
                $q->where('a.Date', 'LIKE', "%$search%")
                    ->orWhere('a.process', 'LIKE', "%$search%")
                    ->orWhere('a.station', 'LIKE', "%$search%")
                    ->orWhere('a.Area_checked', 'LIKE', "%$search%")
                    ->orWhere('a.Auditor', 'LIKE', "%$search%")
                    ->orWhere('b.Description', 'LIKE', "%$search%");
            });
        }

        if ($is_room_team) {
            $result->where('a.is_team', 'LIKE', '%' . trim($my_id) . '%');
        } else {
            // Admin users who can see everything
            $admins = ['270723-001', '260422-001', '121020-002'];
            if (!in_array($my_nik, $admins)) {
                $result = $result->where(function($q) use ($my_name, $my_id) {
                    $q->where('a.Auditor', 'LIKE', '%' . $my_name . '%')
                      ->orWhere('a.is_team', 'LIKE', '%' . $my_id . '%');
                });
            }
        }

        if ($status_id == 4) {
            $result = $result->where('a.status', 4);
        } else if ($status_id == 3) {
            $result = $result->where('a.status', 3);
        }

        return $result->where('IsDelete', 0);
    }

    public static function get_genba_activity(mixed $id)
    {
        $result = DB::connection('sqlsrv')->table('GenbaProcAudit as a')
            ->leftJoin('GenbaCategory as b', 'b.SysID', '=', 'a.Category_id')
            ->select(
                'a.SysID',
                'a.Date',
                'a.Area_Checked',
                'a.Auditor',
                'a.process',
                'a.station',
                'a.Category_id',
                'a.is_team',
                'b.Category as category',
                'b.Description as category_desc'
            )
            ->where('a.SysID', $id);
        return $result;
    }

    public static function get_genba_area()
    {
        $result = DB::connection('sqlsrv')->table('Genba_Area')
            ->select('SysID', 'Area_name', 'Process');

        return $result;
    }

    public static function get_genba_category()
    {
        $result = DB::connection('sqlsrv')->table('GenbaCategory')
            ->select('SysID', 'Category', 'Description');
        return $result;
    }

    public static function get_users($search = null)
    {
        $query = DB::table('users')
            ->select('id', 'username', 'full_name');

        if ($search) {
            $query->where('full_name', 'like', '%' . $search . '%')
                ->orWhere('username', 'like', '%' . $search . '%');
        }

        return $query;
    }

    public static function get_section_list()
    {
        $result = DB::connection('sqlsrv')->table('GenbaDept')
            ->select('Key1 as id', 'Desc as desc')
            ->where('CheckBox01', 1);
        return $result;
    }

    public static function add_genba_activity(mixed $Area_Checked, mixed $Auditor, mixed $category, mixed $Date, mixed $sysID, mixed $station, mixed $process, mixed $is_team = null)
    {
        $result = DB::connection('sqlsrv')->table('GenbaProcAudit as a')
            ->where('a.SysID', $sysID)
            ->select('a.SysID');

        $data_genba = $result;
        if ($data_genba->count() == 0) {
            return DB::connection('sqlsrv')->table('GenbaProcAudit')->insertGetId([
                'Area_Checked'  => $Area_Checked,
                'Date'          => $Date,
                'Auditor'       => $Auditor,
                'Category_id'   => $category,
                'station'       => $station,
                'process'       => $process,
                'is_team'       => $is_team,
                'created_at'    => Carbon::now(),
                'updated_at'    => Carbon::now(),
                'status'        => 4,
                'IsDelete'      => 0
            ]);
        } else {
            $existing_id = $data_genba->first()->SysID;
            $update = DB::connection('sqlsrv')->table('GenbaProcAudit')
                ->where('SysID', $existing_id)
                ->update([
                    'Area_Checked'  => $Area_Checked,
                    'Date'          => $Date,
                    'Auditor'       => $Auditor,
                    'station'       => $station,
                    'process'       => $process,
                    'Category_id'   => $category,
                    'is_team'       => $is_team,
                    'updated_at'    => Carbon::now()
                ]);
            return $existing_id;
        }
    }
    public static function check_date_activity(mixed $id_activity)
    {
        $result = DB::table('GenbaProcAudit as a')
            ->where('SysID', $id_activity)
            ->select('a.Date', 'a.Area_Checked', 'a.Auditor', 'a.process', 'a.station', 'a.Category_id');
        return $result;
    }

    public static function save_genba_activity_detail(mixed $my_id, mixed $id_activity, mixed $scope_id, mixed $check_item_id, mixed $answer, mixed $due_date)
    {
        $result = DB::connection('sqlsrv')->table('GenbaProcAuditDtl as a')->where('a.genba_id', $id_activity)
            ->where('a.scope_id', $scope_id)
            ->where('a.check_item_id', $check_item_id)
            ->select('a.SysID');
        $data_genba = $result;
        if ($data_genba->count() == 0) {
            $updateHeader = DB::table('GenbaProcAudit')
                ->where('SysID', $id_activity)
                ->update([
                    'updated_at' => Carbon::now()->toDateTimeString(),
                    'status' => 4
                ]);
            return DB::table('GenbaProcAuditDtl')->insert([
                'genba_id'  => $id_activity,
                'scope_id'  => $scope_id,
                'check_item_id' => $check_item_id,
                'due_date' => $due_date,
                'result'       => $answer,
                'user_id'       => $my_id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        } else {
            $updateHeader = DB::table('GenbaProcAudit')
                ->where('SysID', $id_activity)
                ->update([
                    'updated_at' => Carbon::now()->toDateTimeString(),
                    'status' => 4
                ]);
            return DB::table('GenbaProcAuditDtl')
                ->where('SysID', $data_genba->first()->SysID)
                ->where('genba_id', $id_activity)
                ->where('scope_id', $scope_id)
                ->where('check_item_id', $check_item_id)
                ->update([
                    'due_date' => $due_date,
                    'result'       => $answer,
                    'updated_at' => Carbon::now()->toDateTimeString()
                ]);
        }
    }

    public static function get_genba_activity_detail(mixed $activity_id, mixed $scope_id, mixed $check_item_id)
    {
        $result = DB::connection('sqlsrv')->table('GenbaProcAuditDtl as a')
            ->join('GenbaProcAudit as b', 'b.SysID', '=', 'a.genba_id')
            ->whereNotNull('b.Auditor')
            ->where('b.Auditor', '!=', '')
            ->select(
                'a.findings',
                'a.Path',
                'a.asign_to',
                'a.asign_to_name',
                'a.asign_to_dept',
                'a.asign_to_dept_name',
                'a.type',
                'a.area_detail',
                'b.process',
                'b.station'
            )
            ->where('genba_id', $activity_id)
            ->where('scope_id', $scope_id)
            ->where('check_item_id', $check_item_id);
        return $result;
    }

    public static function get_stations($search = null)
    {
        $query = DB::connection('sqlsrv')
            ->table('GenbaStationMech')
            ->select('Area as LineDesc', 'DetailArea as Station');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('DetailArea', 'like', '%' . $search . '%')
                    ->orWhere('Area', 'like', '%' . $search . '%');
            });
        }

        return $query;
    }

    public static function get_all_detail_areas()
    {
        $stations = DB::connection('sqlsrv')
            ->table('GenbaStationMech')
            ->select('DetailArea as id', 'Area as LineDesc')
            ->distinct()
            ->whereNotNull('DetailArea')
            ->where('DetailArea', '!=', '')
            ->get();

        $formatted = [];
        foreach ($stations as $item) {
            $formatted[] = [
                'id' => $item->id,
                'name' => ($item->id) . ' (' . ($item->LineDesc ?? 'N/A') . ')'
            ];
        }

        usort($formatted, function($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        return $formatted;
    }
}
