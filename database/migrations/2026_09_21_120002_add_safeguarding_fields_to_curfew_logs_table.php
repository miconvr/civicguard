<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('curfew_logs', function (Blueprint $table) {
            $table->boolean('guardian_notified')->default(false)->after('guardian_contact');
            $table->string('referral_action')->nullable()->after('guardian_notified');
        });
    }

    public function down(): void
    {
        Schema::table('curfew_logs', function (Blueprint $table) {
            $table->dropColumn(['guardian_notified', 'referral_action']);
        });
    }
};
