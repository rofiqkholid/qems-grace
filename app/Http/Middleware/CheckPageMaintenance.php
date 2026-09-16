<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckPageMaintenance
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $path = trim($request->path(), '/');

        // Whitelisted paths (Auth, Assets, Maintenance Setting)
        if ($request->is('page-maintenance*') || 
            $request->is('login*') || 
            $request->is('logout*') || 
            $request->is('api/*') || 
            $request->is('_debugbar*')) {
            return $next($request);
        }

        // Fetch menu_ids that are currently marked as under maintenance
        $maintenanceMenuIds = DB::table('t100_page_maintenance')
            ->where('is_maintenance', 1)
            ->pluck('menu_id')
            ->toArray();

        if (empty($maintenanceMenuIds)) {
            return $next($request);
        }

        // Get the route/menu definitions for menus in maintenance
        $maintenanceMenus = DB::table('t100_menus')
            ->whereIn('id', $maintenanceMenuIds)
            ->get();

        foreach ($maintenanceMenus as $menuItem) {
            $menuPath = trim($menuItem->menu, '/');
            if (empty($menuPath)) {
                continue;
            }

            // Check if request matches menu path or any sub-route of the menu
            if ($path === $menuPath || str_starts_with($path, $menuPath . '/')) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Halaman ini sedang dalam pemeliharaan (Under Maintenance).'
                    ], 503);
                }
                return response()->view('errors.maintenance');
            }
        }

        return $next($request);
    }
}
