<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\GenbaManagement;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer(['dashboard', 'dashboard.genba-mng', 'dashboard.genba-biq', 'dashboard.genba-safety', 'dashboard.internal-audit', 'dashboard.kpi', 'activity.findings-genba', 'activity.internal-action-report', 'approvals.verifikasi-genba', 'approvals.verifkasi-internal-audit', 'summary.summary-verif'], function ($view) {
            $depts = array_filter(GenbaManagement::get_all_departments(), function($dept) {
                return $dept !== 'PE & TMC';
            });
            $view->with('departments', $depts);
            $view->with('detail_areas', GenbaManagement::get_all_detail_areas());
            $view->with('categories', GenbaManagement::get_genba_category()->get()->map(function($c) {
                return [
                    'id' => $c->SysID,
                    'name' => $c->Category . ' - ' . $c->Description
                ];
            })->toArray());
        });
    }
}
