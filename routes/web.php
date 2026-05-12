<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GenbaManagementController;
use App\Http\Controllers\ExecutionGenbaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SummaryGenbaController;
use App\Http\Controllers\MasterController;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard.index');

    Route::get('/genba_management', function () {
        return view('activity.genba_header_form');
    })->name('genba_management');

    Route::get('/team', function () {
        return view('activity.setup.genba_team');
    })->name('genba_team');

    Route::get('/room-team', function () {
        return view('activity.setup.room_team');
    })->name('room_team');

    Route::get('/genba_mng_management', function () {
        return view('activity.findings_genba');
    })->name('genba_mng_management');

    Route::get('/verifikasi_genba', function () {
        return view('approvals.verifikasi_genba');
    })->name('verifikasi_genba');

    Route::get('/spv_verification', [SummaryGenbaController::class, 'index'])->name('summary_verif');

    // Dashboard Routes
    Route::get('/dashboard/data_cards', [DashboardController::class, 'data_cards'])->name('dashboard.data_cards');
    Route::post('/dashboard/table', [DashboardController::class, 'table'])->name('dashboard.table');
    Route::get('/dashboard/chart-data/{yearMonth}', [DashboardController::class, 'chart_all_dept'])->name('dashboard.chart_data');


    // Genba Header Routes
    Route::post('/genba_header/table', [GenbaManagementController::class, 'genbaHeaderTable'])->name('genba.header.table');
    Route::post('/genba_header/delete', [GenbaManagementController::class, 'genbaHeaderDelete'])->name('genba.header.delete');
    Route::post('/genba_header/activity', [GenbaManagementController::class, 'form_genba_header_activity'])->name('genba.header.activity');
    Route::post('/genba_header/add', [GenbaManagementController::class, 'add_genba'])->name('genba.header.add');
    Route::post('/genba_header/area', [GenbaManagementController::class, 'get_genba_area'])->name('genba.header.area');
    Route::post('/genba_header/category', [GenbaManagementController::class, 'get_genba_category'])->name('genba.header.category');
    Route::post('/genba/get_section', [GenbaManagementController::class, 'get_section'])->name('genba.get_section');
    Route::post('/genba/get_user_data', [GenbaManagementController::class, 'get_user_data'])->name('genba.get_user_data');
    Route::post('/genba/post_form_spv', [GenbaManagementController::class, 'post_form_spv'])->name('genba.post_form_spv');
    Route::post('/genba/get_data_photo', [GenbaManagementController::class, 'get_data_photo'])->name('genba.get_data_photo');
    Route::post('/genba/post_photo_spv', [GenbaManagementController::class, 'post_photo_spv'])->name('genba.post_photo_spv');
    Route::post('/genba/submit_form_genba', [GenbaManagementController::class, 'submit_form_genba'])->name('genba.submit_form_genba');
    Route::get('/genba_header/view/{id}', [GenbaManagementController::class, 'genbaHeaderView'])->name('genba.header.view');

    Route::post('/genba/table', [GenbaManagementController::class, 'front_mng_table'])->name('genba.table');
    Route::post('/genba/delete', [GenbaManagementController::class, 'delete'])->name('genba.delete');
    Route::post('/genba/mng_activity', [GenbaManagementController::class, 'mng_activity'])->name('genba.mng_activity');
    Route::get('/genba/preview/{id}', [GenbaManagementController::class, 'preview'])->name('genba.preview');
    Route::post('/genba/save_action_plan', [GenbaManagementController::class, 'save_action_plan'])->name('genba.save_action_plan');
    Route::get('/genba/search-doc', [GenbaManagementController::class, 'search_doc'])->name('genba.search_doc');
    Route::post('/genba/update_department', [GenbaManagementController::class, 'update_department'])->name('genba.update_department');
    Route::post('/genba/get_stations', [GenbaManagementController::class, 'get_stations'])->name('genba.get_stations');

    // Execution Genba Routes
    Route::post('/execution_genba/table', [ExecutionGenbaController::class, 'table'])->name('execution_genba.table');
    Route::post('/spv_verification/table', [SummaryGenbaController::class, 'table'])->name('spv_verification.table');
    Route::post('/execution_genba/approve', [ExecutionGenbaController::class, 'approve'])->name('execution_genba.approve');
    Route::post('/execution_genba/rollback', [ExecutionGenbaController::class, 'rollback'])->name('execution_genba.rollback');

    // Data Master Routes
    Route::prefix('data-master')->group(function () {
        Route::get('/line-checked', [MasterController::class, 'line_checked'])->name('master.line_checked');
        Route::post('/line-checked/table', [MasterController::class, 'line_checked_table'])->name('master.line_checked.table');
        Route::post('/line-checked/store', [MasterController::class, 'store_line_checked'])->name('master.line_checked.store');
        Route::post('/line-checked/update', [MasterController::class, 'update_line_checked'])->name('master.line_checked.update');
        Route::post('/line-checked/delete', [MasterController::class, 'delete_line_checked'])->name('master.line_checked.delete');

        Route::get('/category', [MasterController::class, 'category'])->name('master.category');
        Route::post('/category/table', [MasterController::class, 'category_table'])->name('master.category.table');
        Route::post('/category/store', [MasterController::class, 'store_category'])->name('master.category.store');
        Route::post('/category/update', [MasterController::class, 'update_category'])->name('master.category.update');
        Route::post('/category/delete', [MasterController::class, 'delete_category'])->name('master.category.delete');

        Route::get('/department', [MasterController::class, 'department'])->name('master.department');
        Route::post('/department/table', [MasterController::class, 'department_table'])->name('master.department.table');
        Route::post('/department/store', [MasterController::class, 'store_department'])->name('master.department.store');
        Route::post('/department/update', [MasterController::class, 'update_department'])->name('master.department.update');
        Route::post('/department/delete', [MasterController::class, 'delete_department'])->name('master.department.delete');
        Route::post('/department/delete', [MasterController::class, 'delete_department'])->name('master.department.delete');

        Route::get('/check-item', [MasterController::class, 'check_item'])->name('master.check_item');
        Route::post('/check-item/table', [MasterController::class, 'check_item_table'])->name('master.check_item.table');
        Route::post('/check-item/store', [MasterController::class, 'store_check_item'])->name('master.check_item.store');
        Route::post('/check-item/update', [MasterController::class, 'update_check_item'])->name('master.check_item.update');
        Route::post('/check-item/delete', [MasterController::class, 'delete_check_item'])->name('master.check_item.delete');
    });

    // Fallback for 404 inside auth middleware
    Route::fallback(function () {
        return response()->view('errors.404', [], 404);
    });
});
