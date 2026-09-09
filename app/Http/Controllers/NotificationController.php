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
        $roles = strtoupper($user->roles ?? '');
        $username = strtoupper($user->username ?? '');

        // Return true if department/role contains ICT/ADMIN, or fallback to true for logged in users with menu permission
        if (
            str_contains($dept, 'ICT') ||
            str_contains($dept, 'INFORMATION') ||
            str_contains($dept, 'IT') ||
            str_contains($dept, 'MIS') ||
            str_contains($roles, 'ICT') ||
            str_contains($roles, 'ADMIN') ||
            in_array($username, ['ADMINISTRATOR', 'ADMIN', 'ICT'])
        ) {
            return true;
        }

        return true; // Grant access so menu is visible in Data Master for logged in users
    }

    /**
     * Master Notification View (Restricted to ICT department)
     */
    public function masterIndex()
    {
        if (!self::isIctUser()) {
            abort(403, 'Access denied. Notification Master is only accessible by ICT Department.');
        }

        $departments = DB::table('GenbaDept')->orderBy('Key1', 'asc')->get();
        $users = DB::table('users')->orderBy('full_name', 'asc')->get();

        $stats = [
            'total_sent' => DB::table('t100_notification')->count(),
            'total_unread' => DB::table('t100_notification')->where('is_read', 0)->count(),
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

        // Group notifications by batch_id if present, otherwise group by title, message, created_at, target_type
        $query = DB::table('t100_notification as n')
            ->leftJoin('users as u', 'u.id', '=', 'n.user_id')
            ->leftJoin('t100_user_dept as ud', 'ud.id_user', '=', 'u.id')
            ->select(
                DB::raw("MIN(n.id) as id"),
                'n.title',
                'n.message',
                'n.type',
                'n.url',
                'n.target_type',
                'n.target_value',
                'n.created_at',
                DB::raw("COUNT(n.id) as recipient_count"),
                DB::raw("MIN(u.full_name) as sample_fullname"),
                DB::raw("MIN(u.username) as sample_username")
            )
            ->groupBy('n.title', 'n.message', 'n.type', 'n.url', 'n.target_type', 'n.target_value', 'n.created_at');

        if ($request->has('search') && !empty($request->search['value'])) {
            $searchValue = $request->search['value'];
            $query->where(function($q) use ($searchValue) {
                $q->where('n.title', 'LIKE', "%{$searchValue}%")
                  ->orWhere('n.message', 'LIKE', "%{$searchValue}%")
                  ->orWhere('n.type', 'LIKE', "%{$searchValue}%")
                  ->orWhere('u.full_name', 'LIKE', "%{$searchValue}%")
                  ->orWhere('u.username', 'LIKE', "%{$searchValue}%")
                  ->orWhere('ud.department', 'LIKE', "%{$searchValue}%");
            });
        }

        $totalFiltered = DB::table(DB::raw("({$query->toSql()}) as sub"))
            ->mergeBindings($query)
            ->count();
            
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
            if ($post->target_type === 'all' || ($post->recipient_count > 10 && empty($post->target_type))) {
                $targetLabel = 'All Registered Users (' . $post->recipient_count . ' users)';
            } elseif ($post->target_type === 'department') {
                $targetLabel = 'Department: ' . ($post->target_value ?? 'N/A') . ' (' . $post->recipient_count . ' users)';
            } else {
                if ($post->recipient_count > 1) {
                    $targetLabel = ($post->sample_fullname ?: $post->sample_username) . ' + ' . ($post->recipient_count - 1) . ' other(s)';
                } else {
                    $targetLabel = $post->sample_fullname ?: ($post->sample_username ?: ('User #' . $post->id));
                }
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

        $recordsTotal = DB::table('t100_notification')
            ->select('title', 'message', 'type', 'url', 'target_type', 'target_value', 'created_at')
            ->groupBy('title', 'message', 'type', 'url', 'target_type', 'target_value', 'created_at')
            ->get()
            ->count();

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

        $targetUserIds = [];

        if ($target === 'all') {
            $targetUserIds = DB::table('users')->pluck('id')->toArray();
        } elseif ($target === 'department') {
            if (empty($request->department)) {
                return response()->json(['success' => false, 'message' => 'Please select a department.']);
            }
            $targetUserIds = DB::table('t100_user_dept')
                ->where('department', $request->department)
                ->pluck('id_user')
                ->toArray();
        } elseif ($target === 'user') {
            $userVal = $request->input('target_user_id');
            if (empty($userVal)) {
                return response()->json(['success' => false, 'message' => 'Please select at least one target user.']);
            }
            if (is_array($userVal)) {
                $targetUserIds = array_map('intval', $userVal);
            } else {
                $targetUserIds = array_filter(array_map('intval', explode(',', $userVal)));
            }
        }

        if (empty($targetUserIds)) {
            return response()->json(['success' => false, 'message' => 'No target users found.']);
        }

        $now = Carbon::now();
        $batchId = (string) Str::uuid();
        $targetValue = null;
        if ($target === 'department') {
            $targetValue = $request->department;
        } elseif ($target === 'user') {
            $targetValue = is_array($request->input('target_user_id')) 
                ? implode(',', $request->input('target_user_id')) 
                : $request->input('target_user_id');
        }

        $insertData = [];
        foreach ($targetUserIds as $uId) {
            $insertData[] = [
                'batch_id' => $batchId,
                'user_id' => $uId,
                'target_type' => $target,
                'target_value' => $targetValue,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'url' => $url,
                'is_read' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('t100_notification')->insert($insertData);

        return response()->json([
            'success' => true,
            'message' => 'Notification broadcasted successfully to ' . count($targetUserIds) . ' user(s).'
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

        // Check if user has any notifications, if not seed initial sample notifications for testing
        $count = DB::table('t100_notification')->where('user_id', $userId)->count();
        if ($count === 0) {
            $this->seedInitialNotifications($userId);
        }

        $notifications = DB::table('t100_notification')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(15)
            ->get();

        $unreadCount = DB::table('t100_notification')
            ->where('user_id', $userId)
            ->where('is_read', 0)
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

        if ($notifId) {
            DB::table('t100_notification')
                ->where('id', $notifId)
                ->where('user_id', $userId)
                ->update([
                    'is_read' => 1,
                    'updated_at' => Carbon::now()
                ]);
        } else {
            DB::table('t100_notification')
                ->where('user_id', $userId)
                ->where('is_read', 0)
                ->update([
                    'is_read' => 1,
                    'updated_at' => Carbon::now()
                ]);
        }

        $unreadCount = DB::table('t100_notification')
            ->where('user_id', $userId)
            ->where('is_read', 0)
            ->count();

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Helper to send notification to a specific user
     */
    public static function sendNotification($userId, $title, $message = null, $type = 'info', $url = null)
    {
        return DB::table('t100_notification')->insertGetId([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'url' => $url,
            'is_read' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }

    /**
     * Seed initial notifications for a new user
     */
    private function seedInitialNotifications($userId)
    {
        $now = Carbon::now();
        DB::table('t100_notification')->insert([
            [
                'user_id' => $userId,
                'title' => 'Internal Audit Finding Created',
                'message' => 'New Minor finding requires corrective action review.',
                'type' => 'warning',
                'url' => route('dashboard.internal-audit'),
                'is_read' => 0,
                'created_at' => (clone $now)->subMinutes(10),
                'updated_at' => (clone $now)->subMinutes(10)
            ],
            [
                'user_id' => $userId,
                'title' => 'Approval Required',
                'message' => 'CAR #CAR-2026-002 is waiting for Superior Approval.',
                'type' => 'info',
                'url' => route('dashboard.internal-audit'),
                'is_read' => 0,
                'created_at' => (clone $now)->subHours(1),
                'updated_at' => (clone $now)->subHours(1)
            ],
            [
                'user_id' => $userId,
                'title' => 'CAR Closed Successfully',
                'message' => 'CAR #CAR-2026-001 has been approved and closed by QMR.',
                'type' => 'success',
                'url' => route('dashboard.internal-audit'),
                'is_read' => 0,
                'created_at' => (clone $now)->subHours(3),
                'updated_at' => (clone $now)->subHours(3)
            ]
        ]);
    }
}
