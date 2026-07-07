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
        View::composer(['dashboard', 'dashboard.genba_mng', 'dashboard.genba_biq', 'dashboard.genba_safety', 'dashboard.internal_audit', 'activity.findings_genba', 'activity.internal_action_report', 'approvals.verifikasi_genba', 'approvals.verifkasi_internal_audit', 'summary.summary_verif'], function ($view) {
            $view->with('departments', GenbaManagement::get_all_departments());
            $view->with('detail_areas', GenbaManagement::get_all_detail_areas());
        });
    }
}
