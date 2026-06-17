<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Menu;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share menu data with sidebar
        View::composer('layouts.sidebar', function ($view) {
            $menuIds = [5, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100, 101, 102];
            $menus = Menu::whereIn('id', $menuIds)->orderBy('sequence_id')->get()->keyBy('id');

            $menuStructure = [
                'label' => $menus[5] ?? null,
                'mainMenus' => [
                    [
                        'menu' => $menus[100] ?? null,
                        'children' => [
                            ['menu' => $menus[101] ?? null, 'children' => []],
                            ['menu' => $menus[102] ?? null, 'children' => []],
                        ]
                    ],
                    [
                        'menu' => $menus[85] ?? null,
                        'children' => [
                            ['menu' => $menus[86] ?? null, 'children' => [87, 88]],
                            ['menu' => $menus[89] ?? null, 'children' => [90, 91]],
                            ['menu' => $menus[92] ?? null, 'children' => [93, 94]],
                        ]
                    ],
                    [
                        'menu' => $menus[95] ?? null,
                        'children' => [
                            ['menu' => $menus[96] ?? null, 'children' => []],
                            ['menu' => $menus[97] ?? null, 'children' => []],
                            ['menu' => $menus[98] ?? null, 'children' => []],
                            ['menu' => $menus[99] ?? null, 'children' => []],
                        ]
                    ]
                ]
            ];

            $view->with('menuStructure', $menuStructure)
                ->with('menus', $menus);
        });
    }
}
