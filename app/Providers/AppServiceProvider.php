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
        View::composer(['dashboard', 'activity.findings_genba', 'approvals.verifikasi_genba', 'summary.summary_verif'], function ($view) {
            $view->with('departments', GenbaManagement::get_all_departments());
            $view->with('detail_areas', GenbaManagement::get_all_detail_areas());
        });
    }
}
