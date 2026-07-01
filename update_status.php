<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$actions = DB::table('CsAuditAction')->get();
$count = 0;
foreach ($actions as $action) {
    $car = DB::table('CsAuditCar')->where('id', $action->audit_car_id)->first();
    if ($car) {
        if ($car->status === 'Need Verification' && ($action->action_status === 'complete' || empty($action->action_status))) {
            DB::table('CsAuditAction')->where('id', $action->id)->update(['action_status' => 'approve_superior']);
            $count++;
        } elseif ($car->status === 'Under Review' && ($action->action_status === 'complete' || empty($action->action_status))) {
            DB::table('CsAuditAction')->where('id', $action->id)->update(['action_status' => 'open_verif']);
            $count++;
        } elseif ($car->status === 'Closed' && ($action->action_status === 'complete' || empty($action->action_status))) {
            DB::table('CsAuditAction')->where('id', $action->id)->update(['action_status' => 'verified']);
            $count++;
        }
    }
}
echo "Migrated $count records!\n";
