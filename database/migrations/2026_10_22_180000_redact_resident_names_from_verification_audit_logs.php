<?php

use App\Models\ResidentVerification;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $verificationLogs = DB::table('audit_logs')
            ->where('auditable_type', ResidentVerification::class)
            ->where('action', 'resident_verified');

        $verificationLogs->update(['description' => 'Residence verification approved.']);

        DB::table('audit_logs')
            ->where('auditable_type', ResidentVerification::class)
            ->where('action', 'residence_verification_rejected')
            ->update(['description' => 'Residence verification declined.']);
    }

    public function down(): void
    {
        // Personal details removed by this migration are intentionally not restored.
    }
};
