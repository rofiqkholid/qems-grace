<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop constraint
        DB::statement("ALTER TABLE CsAuditCar DROP CONSTRAINT csauditcar_audit_detail_id_foreign");
        
        // Alter column to be nullable
        DB::statement("ALTER TABLE CsAuditCar ALTER COLUMN audit_detail_id bigint NULL");
        
        // Add constraint with ON DELETE SET NULL
        DB::statement("ALTER TABLE CsAuditCar ADD CONSTRAINT csauditcar_audit_detail_id_foreign FOREIGN KEY (audit_detail_id) REFERENCES CsAuditDetail(id) ON DELETE SET NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop constraint
        DB::statement("ALTER TABLE CsAuditCar DROP CONSTRAINT csauditcar_audit_detail_id_foreign");
        
        // Alter column to be NOT nullable
        // Note: Make sure there are no NULL values in database before running rollback, otherwise it will fail
        DB::statement("ALTER TABLE CsAuditCar ALTER COLUMN audit_detail_id bigint NOT NULL");
        
        // Add constraint with ON DELETE CASCADE
        DB::statement("ALTER TABLE CsAuditCar ADD CONSTRAINT csauditcar_audit_detail_id_foreign FOREIGN KEY (audit_detail_id) REFERENCES CsAuditDetail(id) ON DELETE CASCADE");
    }
};
