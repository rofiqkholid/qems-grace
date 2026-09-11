<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GenbaManagementController;
use App\Http\Controllers\ExecutionGenbaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SummaryGenbaController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\InternalAuditController;
use App\Http\Controllers\AgentChatController;
use App\Http\Controllers\KPICompanyController;
use App\Http\Controllers\NotificationController;
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
        return redirect()->route('dashboard');
    });
    Route::view('/dashboard-mng', 'dashboard.genba-mng')->name('dashboard');

    Route::get('/genba-management', function () {
        return view('activity.genba-header-form');
    })->name('genba-management');

    Route::get('/internal-audit', [InternalAuditController::class, 'index'])->name('internal_audit');
    Route::get('/internal-action-report', [InternalAuditController::class, 'actionReport'])->name('internal_audit.action_report');
    Route::get('/verifikasi-internal-audit', [InternalAuditController::class, 'verification'])->name('internal_audit.verification');
    Route::post('/verifikasi-internal-audit/table', [InternalAuditController::class, 'verificationTable'])->name('internal_audit.verification.table');
    Route::get('/internal-action-report/preview/{id}', [InternalAuditController::class, 'actionReportPreview'])->name('internal_audit.action_report.preview');
    Route::post('/internal-action-report/preview/{id}/save-action', [InternalAuditController::class, 'saveActionReportDetails'])->name('internal_audit.action_report.save_action');
    Route::post('/internal-action-report/preview/{id}/rollback', [InternalAuditController::class, 'rollbackActionPlan'])->name('internal_audit.action_report.rollback');
    Route::get('/internal-action-report/preview/{id}/export', [InternalAuditController::class, 'exportCarExcel'])->name('internal_audit.action_report.export');
    Route::post('/internal-audit/schedules', [InternalAuditController::class, 'getSchedules'])->name('internal_audit.schedules');
    Route::post('/internal-audit/schedules/store', [InternalAuditController::class, 'storeSchedule'])->name('internal_audit.schedules.store');
    Route::get('/internal-audit/schedules/detail/{id}', [InternalAuditController::class, 'getScheduleDetail'])->name('internal_audit.schedules.detail');
    Route::post('/internal-audit/schedules/delete/{id}', [InternalAuditController::class, 'deleteSchedule'])->name('internal_audit.schedules.delete');
    Route::get('/internal-audit/checksheet', [InternalAuditController::class, 'getChecksheet'])->name('internal_audit.checksheet');
    Route::post('/internal-audit/get-users', [InternalAuditController::class, 'getUsers'])->name('internal_audit.get_users');
    Route::post('/internal-audit/get-auditors', [InternalAuditController::class, 'getAuditors'])->name('internal_audit.get_auditors');
    Route::post('/internal-audit/get-superiors', [InternalAuditController::class, 'getSuperiors'])->name('internal_audit.get_superiors');
    Route::get('/internal-audit/conduct/{schedule_id}', [InternalAuditController::class, 'conduct'])->name('internal_audit.conduct');
    Route::get('/internal-audit/conduct/{schedule_id}/export', [InternalAuditController::class, 'exportExcel'])->name('internal_audit.export');
    Route::get('/internal-audit/conduct/{schedule_id}/car/{item_id}', [InternalAuditController::class, 'carForm'])->name('internal_audit.car_form');
    Route::post('/internal-audit/conduct/{schedule_id}/car/{item_id}/send-draft', [InternalAuditController::class, 'sendDraftCarForm'])->name('internal_audit.car_form.send_draft');
    Route::post('/internal-audit/submit', [InternalAuditController::class, 'submitAudit'])->name('internal_audit.submit');
    Route::post('/internal-audit/save-judgment', [InternalAuditController::class, 'saveJudgment'])->name('internal_audit.save_judgment');
    Route::post('/internal-audit/cars', [InternalAuditController::class, 'getCars'])->name('internal_audit.cars');
    Route::post('/internal-audit/cars/update', [InternalAuditController::class, 'updateCarPlan'])->name('internal_audit.cars.update');
    Route::post('/internal-audit/cars/approve', [InternalAuditController::class, 'approveCar'])->name('internal_audit.cars.approve');
    Route::post('/internal-audit/cars/rollback', [InternalAuditController::class, 'rollbackCar'])->name('internal_audit.cars.rollback');
    Route::post('/internal-audit/cars/reject', [InternalAuditController::class, 'rejectCar'])->name('internal_audit.cars.reject');
    Route::post('/internal-audit/cars/delete', [InternalAuditController::class, 'deleteCar'])->name('internal_audit.cars.delete');
    Route::post('/internal-audit/get-requirements', [InternalAuditController::class, 'getRequirements'])->name('internal_audit.get_requirements');
    Route::post('/internal-audit/get-clause-titles', [InternalAuditController::class, 'getClauseTitles'])->name('internal_audit.get_clause_titles');
    Route::post('/internal-audit/detail/save-note', [InternalAuditController::class, 'saveDetailNote'])->name('internal_audit.detail.save_note');

    Route::get('/team', function () {
        return view('activity.setup.genba-team');
    })->name('genba-team');

    Route::get('/room-team', function () {
        return view('activity.setup.room-team');
    })->name('room-team');
    Route::get('/team_member', function () {
        return redirect()->route('room-team');
    });
    Route::get('/team-member', function () {
        return redirect()->route('room-team');
    });

    Route::get('/genba-mng-management', function () {
        return view('activity.findings-genba');
    })->name('genba-mng-management');

    Route::get('/verifikasi-genba', function () {
        return view('approvals.verifikasi-genba');
    })->name('verifikasi-genba');

    Route::get('/spv-verification', [SummaryGenbaController::class, 'index'])->name('summary-verif');
    Route::get('/spv_verification', function() {
        return redirect()->route('summary-verif');
    });
    Route::get('/room-chat-agent', [AgentChatController::class, 'index'])->name('agent.chat');
    Route::post('/room-chat-agent/send', [AgentChatController::class, 'send'])->name('agent.chat.send');

    // Dashboard Routes
    Route::get('/dashboard-mng/data_cards', [DashboardController::class, 'data_cards'])->name('dashboard.data_cards');
    Route::post('/dashboard-mng/table', [DashboardController::class, 'table'])->name('dashboard.table');
    Route::get('/dashboard-mng/chart-data/{yearMonth}', [DashboardController::class, 'chart_all_dept'])->name('dashboard.chart_data');
    Route::get('/dashboard-mng/export', [DashboardController::class, 'genba_mng_export'])->name('dashboard.export');


    // Genba BIQ Dashboard Routes
    Route::get('/dashboard-biq', [DashboardController::class, 'biq_index'])->name('dashboard.biq');
    Route::get('/dashboard-biq/data_cards', [DashboardController::class, 'biq_data_cards'])->name('dashboard.biq.data_cards');
    Route::post('/dashboard-biq/table', [DashboardController::class, 'biq_table'])->name('dashboard.biq.table');
    Route::get('/dashboard-biq/chart-data/{yearMonth}', [DashboardController::class, 'biq_chart_all_dept'])->name('dashboard.biq.chart_data');

    // Genba Safety Dashboard Routes
    Route::get('/dashboard-safety', [DashboardController::class, 'safety_index'])->name('dashboard.safety');
    Route::get('/dashboard-safety/data_cards', [DashboardController::class, 'safety_data_cards'])->name('dashboard.safety.data_cards');
    Route::post('/dashboard-safety/table', [DashboardController::class, 'safety_table'])->name('dashboard.safety.table');
    Route::get('/dashboard-safety/chart-data/{yearMonth}', [DashboardController::class, 'safety_chart_all_dept'])->name('dashboard.safety.chart_data');

    // Internal Audit Dashboard Routes
    Route::view('/dashboard-internal-audit', 'dashboard.internal-audit')->name('dashboard.internal-audit');
    Route::get('/dashboard-internal-audit/data_cards', [DashboardController::class, 'internal_audit_data_cards'])->name('dashboard.internal-audit.data_cards');
    Route::get('/dashboard-internal-audit/chart-data/{yearMonth}', [DashboardController::class, 'internal_audit_chart_all_dept'])->name('dashboard.internal-audit.chart_data');
    Route::get('/dashboard-internal-audit/closed-chart-data/{yearMonth}', [DashboardController::class, 'internal_audit_chart_closed_dept'])->name('dashboard.internal-audit.closed_chart_data');
    Route::get('/dashboard-internal-audit/clause-chart-data/{yearMonth}', [DashboardController::class, 'internal_audit_chart_clause_data'])->name('dashboard.internal-audit.clause_chart_data');
    Route::get('/dashboard-internal-audit/export', [DashboardController::class, 'internal_audit_export'])->name('dashboard.internal-audit.export');
    Route::get('/dashboard-internal-audit/print', [DashboardController::class, 'internal_audit_print'])->name('dashboard.internal-audit.print');

    // KPI Dashboard Routes
    Route::get('/dashboard-kpi', [DashboardController::class, 'kpi_index'])->name('dashboard.kpi');
    Route::get('/dashboard-kpi/chart-data/{year}', [DashboardController::class, 'kpi_chart_data'])->name('dashboard.kpi.chart_data');
    Route::get('/dashboard-kpi/summary-cards/{year}', [DashboardController::class, 'kpi_summary_cards'])->name('dashboard.kpi.summary_cards');


    // Notification Routes
    Route::get('/notifications', [NotificationController::class, 'getNotifications'])->name('notifications.get');
    Route::post('/notifications/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark_read');

    // Master Notification Routes (ICT only)
    Route::get('/data-master/notification', [NotificationController::class, 'masterIndex'])->name('master.notification');
    Route::post('/data-master/notification/table', [NotificationController::class, 'masterTable'])->name('master.notification.table');
    Route::post('/data-master/notification/send', [NotificationController::class, 'sendBroadcast'])->name('master.notification.send');
    Route::post('/data-master/notification/update', [NotificationController::class, 'updateNotification'])->name('master.notification.update');
    Route::post('/data-master/notification/delete', [NotificationController::class, 'deleteNotification'])->name('master.notification.delete');

    // Genba Header Routes
    Route::post('/genba-header/table', [GenbaManagementController::class, 'genbaHeaderTable'])->name('genba.header.table');
    Route::post('/genba-header/delete', [GenbaManagementController::class, 'genbaHeaderDelete'])->name('genba.header.delete');
    Route::post('/genba-header/activity', [GenbaManagementController::class, 'form_genba_header_activity'])->name('genba.header.activity');
    Route::post('/genba-header/add', [GenbaManagementController::class, 'add_genba'])->name('genba.header.add');
    Route::post('/genba-header/area', [GenbaManagementController::class, 'get_genba_area'])->name('genba.header.area');
    Route::post('/genba-header/category', [GenbaManagementController::class, 'get_genba_category'])->name('genba.header.category');
    Route::post('/genba/get_section', [GenbaManagementController::class, 'get_section'])->name('genba.get_section');
    Route::post('/genba/get_user_data', [GenbaManagementController::class, 'get_user_data'])->name('genba.get_user_data');
    Route::post('/genba/post_form_spv', [GenbaManagementController::class, 'post_form_spv'])->name('genba.post_form_spv');
    Route::post('/genba/get_data_photo', [GenbaManagementController::class, 'get_data_photo'])->name('genba.get_data_photo');
    Route::post('/genba/post_photo_spv', [GenbaManagementController::class, 'post_photo_spv'])->name('genba.post_photo_spv');
    Route::post('/genba/submit_form_genba', [GenbaManagementController::class, 'submit_form_genba'])->name('genba.submit_form_genba');
    Route::get('/genba-header/view/{id}', [GenbaManagementController::class, 'genbaHeaderView'])->name('genba.header.view');

    Route::post('/genba/table', [GenbaManagementController::class, 'front_mng_table'])->name('genba.table');
    Route::post('/genba/delete', [GenbaManagementController::class, 'delete'])->name('genba.delete');
    Route::post('/genba/mng_activity', [GenbaManagementController::class, 'mng_activity'])->name('genba.mng_activity');
    Route::get('/genba/preview/{id}', [GenbaManagementController::class, 'preview'])->name('genba.preview');
    Route::post('/genba/save_action_plan', [GenbaManagementController::class, 'save_action_plan'])->name('genba.save_action_plan');
    Route::get('/genba/search-doc', [GenbaManagementController::class, 'search_doc'])->name('genba.search_doc');
    Route::post('/genba/update_department', [GenbaManagementController::class, 'update_department'])->name('genba.update_department');
    Route::post('/genba/update_detail_area', [GenbaManagementController::class, 'update_detail_area'])->name('genba.update_detail_area');
    Route::post('/genba/get_stations', [GenbaManagementController::class, 'get_stations'])->name('genba.get_stations');

    // Execution Genba Routes
    Route::post('/execution-genba/table', [ExecutionGenbaController::class, 'table'])->name('execution_genba.table');
    Route::post('/spv-verification/table', [SummaryGenbaController::class, 'table'])->name('spv_verification.table');
    Route::post('/execution-genba/approve', [ExecutionGenbaController::class, 'approve'])->name('execution_genba.approve');
    Route::post('/execution-genba/rollback', [ExecutionGenbaController::class, 'rollback'])->name('execution_genba.rollback');

    // Data Master Routes
    Route::prefix('data-master')->group(function () {
        Route::get('/line-checked', [MasterController::class, 'line_checked'])->name('master.line-checked');
        Route::post('/line-checked/table', [MasterController::class, 'line_checked_table'])->name('master.line-checked.table');
        Route::post('/line-checked/store', [MasterController::class, 'store_line_checked'])->name('master.line-checked.store');
        Route::post('/line-checked/update', [MasterController::class, 'update_line_checked'])->name('master.line-checked.update');
        Route::post('/line-checked/delete', [MasterController::class, 'delete_line_checked'])->name('master.line-checked.delete');

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

        Route::get('/clauses', [MasterController::class, 'clauses'])->name('master.clauses');
        Route::post('/clauses/table', [MasterController::class, 'clauses_table'])->name('master.clauses.table');
        Route::post('/', [MasterController::class, 'store_clauses'])->name('master.clauses.store');
        Route::post('/clauses/update', [MasterController::class, 'update_clauses'])->name('master.clauses.update');
        Route::post('/clauses/delete', [MasterController::class, 'delete_clauses'])->name('master.clauses.delete');

        Route::get('/check-item', [MasterController::class, 'check_item'])->name('master.check-item');
        Route::post('/check-item/table', [MasterController::class, 'check_item_table'])->name('master.check-item.table');
        Route::post('/check-item/store', [MasterController::class, 'store_check_item'])->name('master.check-item.store');
        Route::post('/check-item/update', [MasterController::class, 'update_check_item'])->name('master.check-item.update');
        Route::post('/check-item/delete', [MasterController::class, 'delete_check_item'])->name('master.check-item.delete');

        Route::get('/intr-check-item', [MasterController::class, 'intr_check_item'])->name('master.intr-check-item');
        Route::post('/intr-check-item/table', [MasterController::class, 'intr_check_item_table'])->name('master.intr-check-item.table');
        Route::post('/intr-check-item/store', [MasterController::class, 'store_intr_check_item'])->name('master.intr-check-item.store');
        Route::post('/intr-check-item/update', [MasterController::class, 'update_intr_check_item'])->name('master.intr-check-item.update');
        Route::post('/intr-check-item/delete', [MasterController::class, 'delete_intr_check_item'])->name('master.intr-check-item.delete');

        Route::get('/roles', [MasterController::class, 'roles'])->name('master.roles');
        Route::post('/roles/table', [MasterController::class, 'roles_table'])->name('master.roles.table');
        Route::post('/roles/store', [MasterController::class, 'store_roles'])->name('master.roles.store');
        Route::post('/roles/update', [MasterController::class, 'update_roles'])->name('master.roles.update');
        Route::post('/roles/delete', [MasterController::class, 'delete_roles'])->name('master.roles.delete');
        Route::get('/user-auditor', [MasterController::class, 'user_auditor'])->name('master.user-auditor');
        Route::post('/user-auditor/table', [MasterController::class, 'user_auditor_table'])->name('master.user-auditor.table');
        Route::post('/user-auditor/toggle', [MasterController::class, 'toggle_user_auditor'])->name('master.user-auditor.toggle');

        Route::get('/kpi-list', [MasterController::class, 'kpi_list'])->name('master.kpi_list');
        Route::post('/kpi-list/table', [MasterController::class, 'kpi_list_table'])->name('master.kpi_list.table');
        Route::post('/kpi-list/store', [MasterController::class, 'store_kpi_list'])->name('master.kpi_list.store');
        Route::post('/kpi-list/update', [MasterController::class, 'update_kpi_list'])->name('master.kpi_list.update');
        Route::post('/kpi-list/delete', [MasterController::class, 'delete_kpi_list'])->name('master.kpi_list.delete');
        Route::post('/kpi-list/options', [MasterController::class, 'kpi_list_options'])->name('master.kpi_list.options');
        Route::get('/kpi-list/history/{id}', [MasterController::class, 'get_kpi_history'])->name('master.kpi_list.history');
        Route::get('/kpi-list/formula/{kpi_list_id}', [MasterController::class, 'get_kpi_formula'])->name('master.kpi_list.formula');
        Route::post('/kpi-list/formula/save', [MasterController::class, 'save_kpi_formula'])->name('master.kpi_list.formula.save');

        Route::get('/kpi-unit', [MasterController::class, 'kpi_unit'])->name('master.kpi_unit');
        Route::post('/kpi-unit/table', [MasterController::class, 'kpi_unit_table'])->name('master.kpi_unit.table');
        Route::post('/kpi-unit/store', [MasterController::class, 'store_kpi_unit'])->name('master.kpi_unit.store');
        Route::post('/kpi-unit/update', [MasterController::class, 'update_kpi_unit'])->name('master.kpi_unit.update');
        Route::post('/kpi-unit/delete', [MasterController::class, 'delete_kpi_unit'])->name('master.kpi_unit.delete');
        Route::post('/kpi-unit/options', [MasterController::class, 'kpi_unit_options'])->name('master.kpi_unit.options');


    });

    Route::get('/user-management', [MasterController::class, 'user_management'])->name('master.user_management');
    Route::get('/user-management/list', [MasterController::class, 'user_list'])->name('master.user_management.list');
    Route::get('/user-management/{id}/permissions', [MasterController::class, 'get_user_permissions'])->name('master.user_management.get_permissions');
    Route::post('/user-management/table', [MasterController::class, 'user_management_table'])->name('master.user_management.table');
    Route::post('/user-management/update-permission', [MasterController::class, 'update_user_permission'])->name('master.user_management.update_permission');

    Route::get('/user-setting', [MasterController::class, 'user_setting'])->name('master.user_setting');
    Route::post('/user-setting/update', [MasterController::class, 'update_user_setting'])->name('master.user_setting.update');
    Route::post('/user-setting/store', [MasterController::class, 'store_user'])->name('master.user_setting.store');

    Route::get('/menu-management', [MasterController::class, 'menu_management'])->name('master.menu_management');
    Route::post('/menu-management/table', [MasterController::class, 'menu_management_table'])->name('master.menu_management.table');

    // Key Performance Indicator Routes
    Route::prefix('kpi')->group(function () {
        Route::get('/company', [KPICompanyController::class, 'index'])->name('kpi.company');
        Route::get('/company/detail/{id}', [KPICompanyController::class, 'detail'])->name('kpi.company.detail');
        Route::get('/company/manage-activity-plan/{id}', [KPICompanyController::class, 'manageActivityPlan'])->name('kpi.company.manage_activity_plan');
        Route::post('/company/activity-plan/store', [KPICompanyController::class, 'storeActivityPlan'])->name('kpi.company.activity_plan.store');
        Route::post('/company/activity-plan/delete', [KPICompanyController::class, 'deleteActivityPlan'])->name('kpi.company.activity_plan.delete');
        Route::post('/company/activity-plan/toggle-remark', [KPICompanyController::class, 'toggleRemark'])->name('kpi.company.activity_plan.toggle_remark');
        Route::post('/company/activity-plan/upload-evidence', [KPICompanyController::class, 'uploadEvidence'])->name('kpi.company.activity_plan.upload_evidence');
        Route::get('/company/activity/edit/{id}', [KPICompanyController::class, 'editActivity'])->name('kpi.company.activity.edit');
        Route::post('/company/activity/update/{id}', [KPICompanyController::class, 'updateActivity'])->name('kpi.company.activity.update');
        Route::get('/company/activity/cancel/{id}', [KPICompanyController::class, 'cancelActivity'])->name('kpi.company.activity.cancel');
        Route::post('/company/formula/operator', [KPICompanyController::class, 'saveCalcOperator'])->name('kpi.company.formula.operator');
        Route::post('/company/table', [KPICompanyController::class, 'table'])->name('kpi.company.table');
        Route::post('/company/store', [KPICompanyController::class, 'store'])->name('kpi.company.store');
        Route::post('/company/update', [KPICompanyController::class, 'update'])->name('kpi.company.update');
        Route::post('/company/delete', [KPICompanyController::class, 'delete'])->name('kpi.company.delete');
        Route::post('/company/departments', [KPICompanyController::class, 'departments'])->name('kpi.company.departments');
        Route::get('/department', function () {
            return view('errors.coming-soon');
        })->name('kpi.department');
        Route::get('/monthly-summary', function () {
            return view('kpi.monthly-summary');
        })->name('kpi.monthly-summary');
        Route::get('/print-report', function () {
            return view('errors.coming-soon');
        })->name('kpi.print-report');
    });

    // Fallback for 404 inside auth middleware
    Route::fallback(function () {
        return response()->view('errors.404', [], 404);
    });
});
