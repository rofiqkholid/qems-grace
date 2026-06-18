<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// Find all user IDs who have permission for menu 104 (Setting)
$settingsPermissions = DB::table('t100_user_menus_permission')->where('id_menus', 104)->get();

echo "Found " . count($settingsPermissions) . " users with Setting menu permission.\n";

foreach ($settingsPermissions as $perm) {
    DB::table('t100_user_menus_permission')->updateOrInsert(
        [
            'id_user' => $perm->id_user,
            'id_menus' => 105,
        ],
        [
            'is_view' => 1,
            'is_delete' => 1,
        ]
    );
    echo "Assigned menu 105 to user ID: {$perm->id_user}\n";
}

echo "Done!\n";
unlink(__FILE__);
