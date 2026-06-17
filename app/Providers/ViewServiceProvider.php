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
            $menus = Menu::orderBy('sequence_id')->get()->keyBy('id');

            $config = Menu::getMenuStructureConfig();
            
            $mainMenus = [];
            foreach ($config['mainMenus'] as $main) {
                $children = [];
                foreach ($main['children'] as $sub) {
                    $children[] = [
                        'menu' => $menus[$sub['menu']] ?? null,
                        'children' => $sub['children']
                    ];
                }
                $mainMenus[] = [
                    'menu' => $menus[$main['menu']] ?? null,
                    'children' => $children
                ];
            }
            
            $menuStructure = [
                'label' => $menus[$config['label']] ?? null,
                'mainMenus' => $mainMenus
            ];

            $view->with('menuStructure', $menuStructure)
                ->with('menus', $menus);
        });
    }
}
