<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Menu;
use App\Models\UserMenuPermission;
use App\Http\Controllers\NotificationController;

class IntroducingController extends Controller
{
    /**
     * Display the introducing / landing page with user info and module access.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $userDept = ($user?->department ?? DB::table('t100_user_dept')->where('id_user', $user?->id)->value('department')) ?: 'Not Set';

        $userRoleData = DB::table('user_role')->where('id_user', $user?->id)->first();
        $userRoleText = 'Not Set';

        if ($userRoleData && !empty($userRoleData->role)) {
            $rawRole = trim($userRoleData->role);
            if ($rawRole !== '' && $rawRole !== '[]' && $rawRole !== '{}' && $rawRole !== 'null') {
                $decodedRoles = json_decode($rawRole, true);
                if (is_array($decodedRoles)) {
                    $filtered = array_filter(array_map('trim', $decodedRoles));
                    if (!empty($filtered)) {
                        $userRoleText = implode(', ', $filtered);
                    }
                } else {
                    $userRoleText = $rawRole;
                }
            }
        }

        $isIct = NotificationController::isIctUser($user);
        $allMenus = Menu::all()->keyBy('id');
        $menuStruct = Menu::getMenuStructureConfig();

        $accessibleModules = [];
        $hasMasterSetting = $isIct;

        foreach ($menuStruct['mainMenus'] as $mItem) {
            if (!empty($mItem['menu'])) {
                $mId = is_object($mItem['menu']) ? $mItem['menu']->id : $mItem['menu'];
                $menuObj = is_object($mItem['menu']) ? $mItem['menu'] : ($allMenus[$mId] ?? null);

                if ($menuObj) {
                    $mName = $menuObj->menu_name;
                    $mCode = strtolower($menuObj->menu ?? '');

                    if (UserMenuPermission::canView($mId, $user?->id)) {
                        if (in_array($mCode, ['data-master', 'setting', 'master-data', 'user-management', 'menu-management', 'master'])) {
                            $hasMasterSetting = true;
                        } else {
                            $accessibleModules[] = $mName;
                        }
                    }
                }
            }
        }

        if ($hasMasterSetting) {
            $accessLevel = 'Akses Penuh';
        } elseif (!empty($accessibleModules)) {
            $accessLevel = implode(', ', array_unique($accessibleModules));
        } else {
            $accessLevel = 'Terbatas';
        }

        $ua = $request->header('User-Agent', '');
        $browser = 'Browser';
        if (str_contains($ua, 'Edg')) { $browser = 'Edge'; }
        elseif (str_contains($ua, 'Chrome')) { $browser = 'Chrome'; }
        elseif (str_contains($ua, 'Firefox')) { $browser = 'Firefox'; }
        elseif (str_contains($ua, 'Safari')) { $browser = 'Safari'; }

        $os = 'OS';
        if (str_contains($ua, 'Windows')) { $os = 'Windows'; }
        elseif (str_contains($ua, 'Macintosh')) { $os = 'macOS'; }
        elseif (str_contains($ua, 'Linux')) { $os = 'Linux'; }
        elseif (str_contains($ua, 'Android')) { $os = 'Android'; }
        elseif (str_contains($ua, 'iPhone')) { $os = 'iOS'; }

        $deviceInfo = "$browser ($os)";

        return view('introducing', compact(
            'user',
            'userDept',
            'userRoleText',
            'accessLevel',
            'deviceInfo'
        ));
    }
}
