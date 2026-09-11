<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;

class AgentChatController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $message = strtolower($request->input('message'));
        $today = Carbon::now()->toDateString();

        // 1. Dynamically retrieve unique department codes and detect all mentioned departments
        $detectedDepts = [];
        try {
            // Collect departments from both Internal Audit and Genba tables to be comprehensive
            $auditDepts = DB::table('CsAuditCar')
                ->whereNotNull('department')
                ->where('department', '<>', '')
                ->distinct()
                ->pluck('department')
                ->toArray();

            $genbaDepts = DB::table('GenbaDept')
                ->whereNotNull('DepartmentName')
                ->where('DepartmentName', '<>', '')
                ->distinct()
                ->pluck('DepartmentName')
                ->toArray();

            $dbDepts = array_unique(array_merge($auditDepts, $genbaDepts));
                
            foreach ($dbDepts as $dept) {
                $lowerDept = strtolower($dept);
                // Match word boundary
                if (preg_match('/\b' . preg_quote($lowerDept, '/') . '\b/i', $message)) {
                    $detectedDepts[] = $dept;
                }
            }
            
            // Fallback alias checking for HR
            if (empty($detectedDepts) && (str_contains($message, 'hr') || str_contains($message, 'human resource'))) {
                foreach ($dbDepts as $dept) {
                    if (str_starts_with(strtolower($dept), 'hr')) {
                        $detectedDepts[] = $dept;
                    }
                }
            }
        } catch (\Exception $e) {
            // Fallback to hardcoded check if queries fail
            $departments = ['qc', 'hrga', 'hr', 'mtc', 'sls', 'tmc', 'pe', 'pur', 'qms', 'stp', 'tmf', 'ppic', 'lh', 'fa', 'ict'];
            foreach ($departments as $dept) {
                if (preg_match('/\b' . preg_quote($dept, '/') . '\b/i', $message)) {
                    $actualDept = ($dept === 'hr') ? 'HRGA' : strtoupper($dept);
                    if (!in_array($actualDept, $detectedDepts)) {
                        $detectedDepts[] = $actualDept;
                    }
                }
            }
        }

        // 2. Check for OVERDUE query (supports common typos like "overude")
        if (str_contains($message, 'overdue') || str_contains($message, 'overude') || str_contains($message, 'lewat batas') || str_contains($message, 'terlambat')) {
            // --- Query Internal Audit (CAR) ---
            $auditQuery = DB::table('CsAuditCar as a')
                ->leftJoin('CsAuditDetail as b', 'b.id', '=', 'a.audit_detail_id')
                ->leftJoin('CsAuditHeader as c', 'c.id', '=', 'b.audit_header_id')
                ->where('a.status', '<>', 'Closed')
                ->whereDate('a.due_date', '<', $today)
                ->whereNotNull('a.clause_title')
                ->where('a.clause_title', '<>', '')
                ->whereNotExists(function($sub) {
                    $sub->select(DB::raw(1))
                        ->from('CsAuditAction as act')
                        ->whereColumn('act.audit_car_id', 'a.id')
                        ->whereIn('act.action_status', ['open_verif', 'approve_superior', 'verified']);
                });

            if (!empty($detectedDepts)) {
                $auditQuery->whereIn('a.department', $detectedDepts);
            }

            $overdueList = $auditQuery->select('a.id', 'a.req_number', 'a.finding_category', 'a.due_date', 'a.department', 'a.finding')
                ->get();

            // --- Query Genba Management ---
            $genbaQuery = DB::table('GenbaProcAuditDtl as a')
                ->join('GenbaProcAudit as b', 'b.SysID', '=', 'a.genba_id')
                ->leftJoin('GenbaCategory as c', 'c.SysID', '=', 'b.Category_id')
                ->whereNotNull('b.Auditor')
                ->where('b.Auditor', '!=', '')
                ->where(function ($q) {
                    $q->where('b.IsDelete', '!=', 1)
                        ->orWhereNull('b.IsDelete');
                })
                ->whereNotNull('a.findings')
                ->whereDate('a.due_date', '<', $today)
                ->where(function ($q) {
                    $q->whereNull('a.evidence')
                        ->orWhere('a.evidence', 0)
                        ->orWhereNull('a.corrective_action')
                        ->orWhere('a.corrective_action', 0);
                })
                ->where(function ($q) {
                    $q->where('a.result', '!=', 1)
                        ->orWhereNull('a.result');
                });

            if (!empty($detectedDepts)) {
                $genbaQuery->whereIn('a.asign_to_dept', $detectedDepts);
            }

            $genbaOverdueList = $genbaQuery->select(
                'a.SysID',
                'a.asign_to_dept',
                'a.findings',
                'a.due_date',
                'c.Category',
                DB::raw("FORMAT(b.Date, 'ddMMyy') + '-' + CAST(a.SysID AS VARCHAR(20)) as req_number")
            )->get();

            $totalCount = $overdueList->count() + $genbaOverdueList->count();
            $deptSuffix = !empty($detectedDepts) ? " untuk departemen <strong>" . implode(', ', $detectedDepts) . "</strong>" : "";

            if ($totalCount === 0) {
                return response()->json([
                    'status' => 'success',
                    'response' => "Tidak ada temuan (Audit maupun Genba) yang berstatus <strong>Overdue</strong> saat ini{$deptSuffix}."
                ]);
            }

            $response = "<p class='mb-2'>Terdapat total <strong>{$totalCount} temuan Overdue</strong>{$deptSuffix}:</p>";

            // Render Internal Audit Table
            if ($overdueList->count() > 0) {
                $response .= "<p class='text-xs font-bold text-slate-700 mt-3 mb-1.5'>Audit Internal ({$overdueList->count()})</p>";
                $response .= '<div class="overflow-x-auto border border-slate-200 rounded-xl mb-4">';
                $response .= '<table class="min-w-full divide-y divide-slate-200 text-[11px] text-left">';
                $response .= '  <thead class="bg-slate-50 sticky top-0">';
                $response .= '    <tr>';
                $response .= '      <th class="px-3 py-2 font-semibold text-slate-500">Doc No</th>';
                $response .= '      <th class="px-3 py-2 font-semibold text-slate-500">Dept</th>';
                $response .= '      <th class="px-3 py-2 font-semibold text-slate-500">Cat</th>';
                $response .= '      <th class="px-3 py-2 font-semibold text-slate-500">Finding</th>';
                $response .= '      <th class="px-3 py-2 font-semibold text-slate-500">Due Date</th>';
                $response .= '      <th class="px-3 py-2 font-semibold text-slate-500">Status</th>';
                $response .= '    </tr>';
                $response .= '  </thead>';
                $response .= '  <tbody class="divide-y divide-slate-100 bg-white">';
                foreach ($overdueList as $item) {
                    $formattedDate = $item->due_date ? Carbon::parse($item->due_date)->format('d/m/Y') : '-';
                    $encryptedId = $this->encryptCarId($item->id);
                    $link = route('internal_audit.action_report.preview', $encryptedId);
                    
                    $response .= "    <tr class='hover:bg-slate-50/50'>";
                    $response .= "      <td class='px-3 py-2 font-medium whitespace-nowrap'><a href='{$link}' target='_blank' class='text-blue-600 hover:underline'>{$item->req_number}</a></td>";
                    $response .= "      <td class='px-3 py-2 text-slate-600 font-medium'>{$item->department}</td>";
                    $response .= "      <td class='px-3 py-2 text-slate-600'>{$item->finding_category}</td>";
                    $response .= "      <td class='px-3 py-2 text-slate-600 min-w-[150px]'>{$item->finding}</td>";
                    $response .= "      <td class='px-3 py-2 text-slate-600 whitespace-nowrap'>{$formattedDate}</td>";
                    $response .= "      <td class='px-3 py-2 whitespace-nowrap'><span class='px-1.5 py-0.5 rounded text-[9px] font-bold bg-red-100 text-red-800'>Overdue</span></td>";
                    $response .= "    </tr>";
                }
                $response .= '  </tbody>';
                $response .= '</table>';
                $response .= '</div>';
            }

            // Render Genba Table
            if ($genbaOverdueList->count() > 0) {
                $response .= "<p class='text-xs font-bold text-slate-700 mt-6 mb-1.5'>Genba Management ({$genbaOverdueList->count()})</p>";
                $response .= '<div class="overflow-x-auto border border-slate-200 rounded-xl">';
                $response .= '<table class="min-w-full divide-y divide-slate-200 text-[11px] text-left">';
                $response .= '  <thead class="bg-slate-50 sticky top-0">';
                $response .= '    <tr>';
                $response .= '      <th class="px-3 py-2 font-semibold text-slate-500">Doc No</th>';
                $response .= '      <th class="px-3 py-2 font-semibold text-slate-500">Dept</th>';
                $response .= '      <th class="px-3 py-2 font-semibold text-slate-500">Cat</th>';
                $response .= '      <th class="px-3 py-2 font-semibold text-slate-500">Finding</th>';
                $response .= '      <th class="px-3 py-2 font-semibold text-slate-500">Due Date</th>';
                $response .= '      <th class="px-3 py-2 font-semibold text-slate-500">Status</th>';
                $response .= '    </tr>';
                $response .= '  </thead>';
                $response .= '  <tbody class="divide-y divide-slate-100 bg-white">';
                foreach ($genbaOverdueList as $item) {
                    $formattedDate = $item->due_date ? Carbon::parse($item->due_date)->format('d/m/Y') : '-';
                    $trc_id = Crypt::encryptString($item->SysID);
                    $encryptedId = str_replace("=", "-", $trc_id);
                    $link = route('genba.preview', $encryptedId);
                    
                    $response .= "    <tr class='hover:bg-slate-50/50'>";
                    $response .= "      <td class='px-3 py-2 font-medium whitespace-nowrap'><a href='{$link}' target='_blank' class='text-blue-600 hover:underline'>{$item->req_number}</a></td>";
                    $response .= "      <td class='px-3 py-2 text-slate-600 font-medium'>{$item->asign_to_dept}</td>";
                    $response .= "      <td class='px-3 py-2 text-slate-600'>{$item->Category}</td>";
                    $response .= "      <td class='px-3 py-2 text-slate-600 min-w-[150px]'>{$item->findings}</td>";
                    $response .= "      <td class='px-3 py-2 text-slate-600 whitespace-nowrap'>{$formattedDate}</td>";
                    $response .= "      <td class='px-3 py-2 whitespace-nowrap'><span class='px-1.5 py-0.5 rounded text-[9px] font-bold bg-red-100 text-red-800'>Overdue</span></td>";
                    $response .= "    </tr>";
                }
                $response .= '  </tbody>';
                $response .= '</table>';
                $response .= '</div>';
            }

            return response()->json([
                'status' => 'success',
                'response' => $response
            ]);
        }

        // 3. Check for NEED VERIFICATION / BELUM APPROVE query
        if (str_contains($message, 'belum di approve') || str_contains($message, 'belum diapprove') || str_contains($message, 'belum di-approve') || str_contains($message, 'need verif') || str_contains($message, 'perlu verifikasi') || str_contains($message, 'verif')) {
            // --- Query Internal Audit (CAR) ---
            $auditQuery = DB::table('CsAuditCar as a')
                ->leftJoin('CsAuditDetail as b', 'b.id', '=', 'a.audit_detail_id')
                ->leftJoin('CsAuditHeader as c', 'c.id', '=', 'b.audit_header_id')
                ->whereNotNull('a.clause_title')
                ->where('a.clause_title', '<>', '')
                ->where(function($q) {
                    $q->where(function($q2) {
                        $q2->where('a.status', 'Closed')
                           ->whereNull('a.qmr_approved_at');
                    })->orWhere(function($q2) {
                        $q2->where('a.status', '<>', 'Closed')
                           ->whereExists(function($sub) {
                               $sub->select(DB::raw(1))
                                   ->from('CsAuditAction as act')
                                   ->whereColumn('act.audit_car_id', 'a.id')
                                   ->whereIn('act.action_status', ['open_verif', 'approve_superior']);
                           });
                    });
                });

            if (!empty($detectedDepts)) {
                $auditQuery->whereIn('a.department', $detectedDepts);
            }

            $needVerifList = $auditQuery->select('a.id', 'a.req_number', 'a.finding_category', 'a.department', 'a.finding')
                ->get();

            // --- Query Genba Management ---
            $genbaQuery = DB::table('GenbaProcAuditDtl as a')
                ->join('GenbaProcAudit as b', 'b.SysID', '=', 'a.genba_id')
                ->leftJoin('GenbaCategory as c', 'c.SysID', '=', 'b.Category_id')
                ->whereNotNull('b.Auditor')
                ->where('b.Auditor', '!=', '')
                ->where(function ($q) {
                    $q->where('b.IsDelete', '!=', 1)
                        ->orWhereNull('b.IsDelete');
                })
                ->whereNotNull('a.findings')
                ->where(function ($q) {
                    $q->where('a.corrective_action', '1')
                        ->orWhere('a.corrective_action', 1);
                })
                ->where(function ($q) {
                    $q->where('a.evidence', '1')
                        ->orWhere('a.evidence', 1);
                })
                ->where(function ($q) {
                    $q->whereNull('a.verification_result')
                        ->orWhere('a.verification_result', '0')
                        ->orWhere('a.verification_result', 0);
                })
                ->where(function ($q) {
                    $q->where('a.result', '!=', 1)
                        ->orWhereNull('a.result');
                });

            if (!empty($detectedDepts)) {
                $genbaQuery->whereIn('a.asign_to_dept', $detectedDepts);
            }

            $genbaNeedVerifList = $genbaQuery->select(
                'a.SysID',
                'a.asign_to_dept',
                'a.findings',
                'c.Category',
                DB::raw("FORMAT(b.Date, 'ddMMyy') + '-' + CAST(a.SysID AS VARCHAR(20)) as req_number")
            )->get();

            $totalCount = $needVerifList->count() + $genbaNeedVerifList->count();
            $deptSuffix = !empty($detectedDepts) ? " untuk departemen <strong>" . implode(', ', $detectedDepts) . "</strong>" : "";

            if ($totalCount === 0) {
                return response()->json([
                    'status' => 'success',
                    'response' => "Tidak ada temuan (Audit maupun Genba) yang menunggu persetujuan saat ini{$deptSuffix}."
                ]);
            }

            $response = "<p class='mb-2'>Terdapat total <strong>{$totalCount} temuan</strong> menunggu persetujuan{$deptSuffix}:</p>";

            // Render Audit Table
            if ($needVerifList->count() > 0) {
                $response .= "<p class='text-xs font-bold text-slate-700 mt-3 mb-1.5'>Audit Internal ({$needVerifList->count()})</p>";
                $response .= '<div class="overflow-x-auto border border-slate-200 rounded-xl mb-4">';
                $response .= '<table class="min-w-full divide-y divide-slate-200 text-[11px] text-left">';
                $response .= '  <thead class="bg-slate-50 sticky top-0">';
                $response .= '    <tr>';
                $response .= '      <th class="px-3 py-2 font-semibold text-slate-500">Doc No</th>';
                $response .= '      <th class="px-3 py-2 font-semibold text-slate-500">Dept</th>';
                $response .= '      <th class="px-3 py-2 font-semibold text-slate-500">Cat</th>';
                $response .= '      <th class="px-3 py-2 font-semibold text-slate-500">Finding</th>';
                $response .= '      <th class="px-3 py-2 font-semibold text-slate-500">Status</th>';
                $response .= '    </tr>';
                $response .= '  </thead>';
                $response .= '  <tbody class="divide-y divide-slate-100 bg-white">';
                foreach ($needVerifList as $item) {
                    $encryptedId = $this->encryptCarId($item->id);
                    $link = route('internal_audit.action_report.preview', $encryptedId);

                    $response .= "    <tr class='hover:bg-slate-50/50'>";
                    $response .= "      <td class='px-3 py-2 font-medium whitespace-nowrap'><a href='{$link}' target='_blank' class='text-blue-600 hover:underline'>{$item->req_number}</a></td>";
                    $response .= "      <td class='px-3 py-2 text-slate-600 font-medium'>{$item->department}</td>";
                    $response .= "      <td class='px-3 py-2 text-slate-600'>{$item->finding_category}</td>";
                    $response .= "      <td class='px-3 py-2 text-slate-600 min-w-[180px]'>{$item->finding}</td>";
                    $response .= "      <td class='px-3 py-2 whitespace-nowrap'><span class='px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-100 text-amber-800'>Need Verif</span></td>";
                    $response .= "    </tr>";
                }
                $response .= '  </tbody>';
                $response .= '</table>';
                $response .= '</div>';
            }

            // Render Genba Table
            if ($genbaNeedVerifList->count() > 0) {
                $response .= "<p class='text-xs font-bold text-slate-700 mt-6 mb-1.5'>Genba Management ({$genbaNeedVerifList->count()})</p>";
                $response .= '<div class="overflow-x-auto border border-slate-200 rounded-xl">';
                $response .= '<table class="min-w-full divide-y divide-slate-200 text-[11px] text-left">';
                $response .= '  <thead class="bg-slate-50 sticky top-0">';
                $response .= '    <tr>';
                $response .= '      <th class="px-3 py-2 font-semibold text-slate-500">Doc No</th>';
                $response .= '      <th class="px-3 py-2 font-semibold text-slate-500">Dept</th>';
                $response .= '      <th class="px-3 py-2 font-semibold text-slate-500">Cat</th>';
                $response .= '      <th class="px-3 py-2 font-semibold text-slate-500">Finding</th>';
                $response .= '      <th class="px-3 py-2 font-semibold text-slate-500">Status</th>';
                $response .= '    </tr>';
                $response .= '  </thead>';
                $response .= '  <tbody class="divide-y divide-slate-100 bg-white">';
                foreach ($genbaNeedVerifList as $item) {
                    $trc_id = Crypt::encryptString($item->SysID);
                    $encryptedId = str_replace("=", "-", $trc_id);
                    $link = route('genba.preview', $encryptedId);

                    $response .= "    <tr class='hover:bg-slate-50/50'>";
                    $response .= "      <td class='px-3 py-2 font-medium whitespace-nowrap'><a href='{$link}' target='_blank' class='text-blue-600 hover:underline'>{$item->req_number}</a></td>";
                    $response .= "      <td class='px-3 py-2 text-slate-600 font-medium'>{$item->asign_to_dept}</td>";
                    $response .= "      <td class='px-3 py-2 text-slate-600'>{$item->Category}</td>";
                    $response .= "      <td class='px-3 py-2 text-slate-600 min-w-[180px]'>{$item->findings}</td>";
                    $response .= "      <td class='px-3 py-2 whitespace-nowrap'><span class='px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-100 text-amber-800'>Need Verif</span></td>";
                    $response .= "    </tr>";
                }
                $response .= '  </tbody>';
                $response .= '</table>';
                $response .= '</div>';
            }

            return response()->json([
                'status' => 'success',
                'response' => $response
            ]);
        }

        // 4. Check for total / count findings query
        if (str_contains($message, 'jumlah temuan') || str_contains($message, 'total temuan') || str_contains($message, 'berapa temuan')) {
            // Count active CARs
            $queryTotal = DB::table('CsAuditCar')
                ->where('status', '<>', 'Closed')
                ->whereNotNull('clause_title')
                ->where('clause_title', '<>', '');

            if (!empty($detectedDepts)) {
                $queryTotal->whereIn('department', $detectedDepts);
            }
            $totalAudit = $queryTotal->count();

            // Count active Genba
            $queryGenbaTotal = DB::table('GenbaProcAuditDtl as a')
                ->join('GenbaProcAudit as b', 'b.SysID', '=', 'a.genba_id')
                ->whereNotNull('b.Auditor')
                ->where('b.Auditor', '!=', '')
                ->where(function ($q) {
                    $q->where('b.IsDelete', '!=', 1)
                        ->orWhereNull('b.IsDelete');
                })
                ->whereNotNull('a.findings')
                ->where(function ($q) {
                    $q->whereNull('a.verification_result')
                        ->orWhere('a.verification_result', '0')
                        ->orWhere('a.verification_result', 0);
                })
                ->where(function ($q) {
                    $q->where('a.result', '!=', 1)
                        ->orWhereNull('a.result');
                });

            if (!empty($detectedDepts)) {
                $queryGenbaTotal->whereIn('a.asign_to_dept', $detectedDepts);
            }
            $totalGenba = $queryGenbaTotal->count();

            $totalActive = $totalAudit + $totalGenba;
            $deptSuffix = !empty($detectedDepts) ? " untuk departemen <strong>" . implode(', ', $detectedDepts) . "</strong>" : "";

            $response = "<p>Jumlah temuan aktif (Open/Belum Selesai) saat ini{$deptSuffix} adalah <strong>{$totalActive} temuan</strong>:</p>";
            $response .= "<ul class='list-disc list-inside text-xs mt-1.5 space-y-1 text-slate-600'>";
            $response .= "  <li><strong>Audit Internal (CAR):</strong> {$totalAudit} temuan</li>";
            $response .= "  <li><strong>Genba Management:</strong> {$totalGenba} temuan</li>";
            $response .= "</ul>";

            return response()->json([
                'status' => 'success',
                'response' => $response
            ]);
        }

        // 5. Default helpful guide
        $response = "
            <p>Halo! Saya adalah <strong>Asisten GRACE</strong>. Saya dapat membantu mencari informasi temuan audit langsung dari database sistem secara real-time.</p>
            <p class='mt-2'>Silakan coba tanyakan hal-hari seperti:</p>
            <ul class='list-disc list-inside text-xs mt-1.5 space-y-1 text-slate-600'>
                <li><button onclick=\"usePrompt('Ada berapa temuan yang overdue di departemen QC?')\" class='text-blue-600 hover:underline text-left font-semibold'>\"Ada berapa temuan yang overdue di departemen QC?\"</button></li>
                <li><button onclick=\"usePrompt('Temuan yang belum di-approve untuk HR?')\" class='text-blue-600 hover:underline text-left font-semibold'>\"Temuan yang belum di-approve untuk HR?\"</button></li>
                <li><button onclick=\"usePrompt('Berapa total temuan saat ini?')\" class='text-blue-600 hover:underline text-left font-semibold'>\"Berapa total temuan saat ini?\"</button></li>
            </ul>
        ";

        return response()->json([
            'status' => 'success',
            'response' => $response
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
}
