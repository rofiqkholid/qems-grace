<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class NotificationController extends Controller
{
    /**
     * Check if user belongs to ICT department or is Admin
     */
    public static function isIctUser($user = null)
    {
        $user = $user ?? Auth::user();
        if (!$user) return false;

        $dept = strtoupper($user->department ?? '');

        // Fetch department from t100_user_dept if empty on user model
        if (empty($dept)) {
            $deptVal = DB::table('t100_user_dept')->where('id_user', $user->id)->value('department');
            $dept = strtoupper($deptVal ?? '');
        }

        if (
            str_contains($dept, 'ICT') ||
            str_contains($dept, 'INFORMATION') ||
            str_contains($dept, 'IT') ||
            str_contains($dept, 'MIS')
        ) {
            return true;
        }

        return false;
    }

    /**
     * Master Notification View (Restricted to ICT department)
     */
    public function masterIndex()
    {
        if (!self::isIctUser()) {
            abort(403, 'Access denied. Notification Master is only accessible by ICT Department.');
        }

        $departments = DB::table('GenbaDept')
            ->where('Key1', '!=', 'BOD, AGM, GM')
            ->where('Key1', 'NOT LIKE', '%BOD%')
            ->where('Key1', 'NOT LIKE', '%AGM%')
            ->where('Key1', 'NOT LIKE', '%GM%')
            ->orderBy('Key1', 'asc')
            ->get();
        $users = DB::table('users')->orderBy('full_name', 'asc')->get();

        $currentUserId = Auth::user()?->id;
        $stats = [
            'total_sent' => DB::table('t100_notification')->count(),
            'total_unread' => DB::table('t100_notification')
                ->whereNotIn('id', function($q) use ($currentUserId) {
                    $q->select('notification_id')->from('t100_notification_reads')->where('user_id', $currentUserId);
                })->count(),
            'total_users' => DB::table('users')->count(),
        ];

        return view('master.notification', compact('departments', 'users', 'stats'));
    }

    /**
     * DataTables endpoint for Master Notification
     */
    public function masterTable(Request $request)
    {
        if (!self::isIctUser()) {
            return response()->json(['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0]);
        }

        $query = DB::table('t100_notification as n')
            ->select(
                'n.id',
                'n.title',
                'n.message',
                'n.type',
                'n.url',
                'n.target_type',
                'n.target_value',
                'n.created_at'
            );

        if ($request->has('search') && !empty($request->search['value'])) {
            $searchValue = $request->search['value'];
            $query->where(function($q) use ($searchValue) {
                $q->where('n.title', 'LIKE', "%{$searchValue}%")
                  ->orWhere('n.message', 'LIKE', "%{$searchValue}%")
                  ->orWhere('n.type', 'LIKE', "%{$searchValue}%")
                  ->orWhere('n.target_type', 'LIKE', "%{$searchValue}%")
                  ->orWhere('n.target_value', 'LIKE', "%{$searchValue}%");
            });
        }

        $totalFiltered = $query->count();
            
        $limit = $request->input('length', 10);
        $start = $request->input('start', 0);

        $posts = $query->offset($start)
            ->limit($limit)
            ->orderBy('n.created_at', 'desc')
            ->get();

        $data = [];
        $no = $start + 1;
        foreach ($posts as $post) {
            $typeBadge = match($post->type) {
                'warning' => '<span class="inline-block text-center w-20 px-2.5 py-1 text-xs font-semibold rounded-none border border-amber-200 bg-amber-50 text-amber-600">Warning</span>',
                'success' => '<span class="inline-block text-center w-20 px-2.5 py-1 text-xs font-semibold rounded-none border border-emerald-200 bg-emerald-50 text-emerald-600">Success</span>',
                'danger' => '<span class="inline-block text-center w-20 px-2.5 py-1 text-xs font-semibold rounded-none border border-rose-200 bg-rose-50 text-rose-600">Danger</span>',
                default => '<span class="inline-block text-center w-20 px-2.5 py-1 text-xs font-semibold rounded-none border border-blue-200 bg-blue-50 text-blue-600">Info</span>',
            };

            // Format recipient label
            if (empty($post->target_type) || $post->target_type === 'all') {
                $targetLabel = 'All Registered Users';
            } elseif ($post->target_type === 'department') {
                $targetLabel = 'Department: ' . ($post->target_value ?? 'N/A');
            } else {
                $targetLabel = 'User Target (' . ($post->target_value ?? 'N/A') . ')';
            }

            $sys_id = $post->id;
            $notifJson = htmlspecialchars(json_encode($post), ENT_QUOTES, 'UTF-8');

            $action = '
                <div class="flex items-center justify-center gap-2">
                    <button type="button" title="Edit" class="w-10 h-10 flex items-center justify-center rounded-xl bg-blue-50 text-blue-500 hover:bg-blue-100 hover:text-blue-600 transition-all duration-200" id="btn_edit_notif_' . $no . '" onclick="openEditModal(' . $sys_id . ', ' . $notifJson . ')">
                        <span id="svg_edit_notif_' . $no . '" class="flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500" viewBox="0 0 24 24" fill="none">
                                <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="currentColor"></path>
                                <path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="currentColor"></path>
                                <path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="currentColor"></path>
                            </svg>
                        </span>
                        <span id="spinner_edit_notif_' . $no . '" class="hidden animate-spin rounded-full h-4 w-4 border-b-2 border-current"></span>
                    </button>
                    <button type="button" title="Delete" class="w-10 h-10 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-100 hover:text-red-600 transition-all duration-200" id="btn_delete_notif_' . $no . '" onclick="confirmDeleteNotif(' . $sys_id . ')">
                        <span id="icon_delete_notif_' . $no . '" class="flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-600" viewBox="0 0 24 24" fill="none">
                                <path opacity="0.3" d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"/>
                                <path d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V7H5V5Z" fill="currentColor"/>
                                <path d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"/>
                            </svg>
                        </span>
                        <span id="loader_delete_notif_' . $no . '" class="hidden animate-spin rounded-full h-4 w-4 border-b-2 border-current"></span>
                    </button>
                </div>
            ';

            $data[] = [
                'no' => $no++,
                'title' => '<div class="font-semibold text-slate-800">' . e($post->title) . '</div>',
                'target' => e($targetLabel),
                'type' => $typeBadge,
                'message' => '<div class="text-sm text-slate-600 truncate max-w-[280px]" title="' . e($post->message) . '">' . e($post->message ?? '-') . '</div>',
                'created_at' => $post->created_at ? Carbon::parse($post->created_at)->format('d M Y H:i') : '-',
                'action' => $action
            ];
        }

        $recordsTotal = DB::table('t100_notification')->count();

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $totalFiltered,
            'data' => $data
        ]);
    }

    /**
     * Update existing notification (ICT only)
     */
    public function updateNotification(Request $request)
    {
        if (!self::isIctUser()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized']);
        }

        $request->validate([
            'id' => 'required|integer',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|string|in:info,warning,success,danger',
            'url' => 'nullable|string|max:255',
        ]);

        $id = $request->input('id');
        $notif = DB::table('t100_notification')->where('id', $id)->first();

        if (!$notif) {
            return response()->json(['success' => false, 'message' => 'Notification not found']);
        }

        DB::table('t100_notification')->where('id', $id)->update([
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type,
            'url' => $request->url,
            'updated_at' => Carbon::now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Notification updated successfully.']);
    }

    /**
     * Broadcast notification to All Users, Department, or Specific User (ICT only)
     */
    public function sendBroadcast(Request $request)
    {
        if (!self::isIctUser()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized']);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|string|in:info,warning,success,danger',
            'target' => 'required|string|in:all,department,user',
            'department' => 'nullable|string',
            'target_user_id' => 'nullable',
            'url' => 'nullable|string|max:255',
        ]);

        $title = $request->title;
        $message = $request->message;
        $type = $request->type;
        $target = $request->target;
        $url = $request->url;

        $targetValue = null;
        if ($target === 'department') {
            if (empty($request->department)) {
                return response()->json(['success' => false, 'message' => 'Please select a department.']);
            }
            $targetValue = $request->department;
        } elseif ($target === 'user') {
            $userVal = $request->input('target_user_id');
            if (empty($userVal)) {
                return response()->json(['success' => false, 'message' => 'Please select at least one target user.']);
            }
            $targetValue = is_array($userVal) ? implode(',', $userVal) : $userVal;
        }

        $now = Carbon::now();
        $batchId = (string) Str::uuid();

        // Create exactly 1 notification record in DB
        DB::table('t100_notification')->insert([
            'batch_id' => $batchId,
            'user_id' => ($target === 'user' && is_numeric($targetValue)) ? intval($targetValue) : null,
            'target_type' => $target,
            'target_value' => $targetValue,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'url' => $url,
            'is_read' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification broadcasted successfully.'
        ]);
    }

    /**
     * Delete notification record
     */
    public function deleteNotification(Request $request)
    {
        if (!self::isIctUser()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized']);
        }

        $id = $request->input('id');
        if ($id) {
            DB::table('t100_notification')->where('id', $id)->delete();
            DB::table('t100_notification_reads')->where('notification_id', $id)->delete();
            return response()->json(['success' => true, 'message' => 'Notification deleted successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Invalid ID']);
    }

    /**
     * Get list of notifications and unread count for current user
     */
    public function getNotifications(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'notifications' => [],
                'unread_count' => 0
            ]);
        }

        $userId = $user->id;
        $userDept = $user->department ?? null;
        if (empty($userDept)) {
            $userDept = DB::table('t100_user_dept')->where('id_user', $userId)->value('department');
        }

        $notifications = DB::table('t100_notification as n')
            ->leftJoin('t100_notification_reads as nr', function ($join) use ($userId) {
                $join->on('nr.notification_id', '=', 'n.id')
                     ->where('nr.user_id', '=', $userId);
            })
            ->where(function ($q) use ($userId, $userDept) {
                $q->whereNull('n.target_type')
                  ->orWhere('n.target_type', 'all')
                  ->orWhere('n.user_id', $userId);

                if (!empty($userDept)) {
                    $q->orWhere(function ($sub) use ($userDept) {
                        $sub->where('n.target_type', 'department')
                            ->where('n.target_value', $userDept);
                    });
                }
            })
            ->select(
                'n.id',
                'n.title',
                'n.message',
                'n.type',
                'n.url',
                'n.created_at',
                DB::raw("CASE WHEN nr.id IS NOT NULL THEN 1 ELSE 0 END as is_read")
            )
            ->orderBy('n.created_at', 'desc')
            ->limit(15)
            ->get();

        $unreadCount = DB::table('t100_notification as n')
            ->leftJoin('t100_notification_reads as nr', function ($join) use ($userId) {
                $join->on('nr.notification_id', '=', 'n.id')
                     ->where('nr.user_id', '=', $userId);
            })
            ->whereNull('nr.id')
            ->where(function ($q) use ($userId, $userDept) {
                $q->whereNull('n.target_type')
                  ->orWhere('n.target_type', 'all')
                  ->orWhere('n.user_id', $userId);

                if (!empty($userDept)) {
                    $q->orWhere(function ($sub) use ($userDept) {
                        $sub->where('n.target_type', 'department')
                            ->where('n.target_value', $userDept);
                    });
                }
            })
            ->count();

        $formatted = $notifications->map(function ($notif) {
            $notif->time_ago = $notif->created_at ? Carbon::parse($notif->created_at)->diffForHumans() : '';
            return $notif;
        });

        return response()->json([
            'success' => true,
            'notifications' => $formatted,
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Mark single notification or all notifications as read for current user
     */
    public function markAsRead(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized']);
        }

        $userId = $user->id;
        $notifId = $request->input('id');
        $now = Carbon::now();

        if ($notifId) {
            DB::table('t100_notification_reads')->updateOrInsert(
                ['notification_id' => $notifId, 'user_id' => $userId],
                ['created_at' => $now]
            );
        } else {
            $userDept = $user->department ?? DB::table('t100_user_dept')->where('id_user', $userId)->value('department');

            $allNotifIds = DB::table('t100_notification')
                ->where(function ($q) use ($userId, $userDept) {
                    $q->whereNull('target_type')
                      ->orWhere('target_type', 'all')
                      ->orWhere('user_id', $userId);
                    if (!empty($userDept)) {
                        $q->orWhere(function ($sub) use ($userDept) {
                            $sub->where('target_type', 'department')
                                ->where('target_value', $userDept);
                        });
                    }
                })
                ->pluck('id');

            foreach ($allNotifIds as $nid) {
                DB::table('t100_notification_reads')->updateOrInsert(
                    ['notification_id' => $nid, 'user_id' => $userId],
                    ['created_at' => $now]
                );
            }
        }

        $userDept = $user->department ?? DB::table('t100_user_dept')->where('id_user', $userId)->value('department');

        $unreadCount = DB::table('t100_notification as n')
            ->leftJoin('t100_notification_reads as nr', function ($join) use ($userId) {
                $join->on('nr.notification_id', '=', 'n.id')
                     ->where('nr.user_id', '=', $userId);
            })
            ->whereNull('nr.id')
            ->where(function ($q) use ($userId, $userDept) {
                $q->whereNull('n.target_type')
                  ->orWhere('n.target_type', 'all')
                  ->orWhere('n.user_id', $userId);

                if (!empty($userDept)) {
                    $q->orWhere(function ($sub) use ($userDept) {
                        $sub->where('n.target_type', 'department')
                            ->where('n.target_value', $userDept);
                    });
                }
            })
            ->count();

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Helper to send notification (app updates / info)
     */
    public static function sendNotification($userId, $title, $message = null, $type = 'info', $url = null)
    {
        return DB::table('t100_notification')->insertGetId([
            'user_id' => $userId,
            'target_type' => $userId ? 'user' : 'all',
            'target_value' => $userId ? (string)$userId : null,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'url' => $url,
            'is_read' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
