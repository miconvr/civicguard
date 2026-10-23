<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resident_verifications', function (Blueprint $table) {
            $table->text('rejection_reason')->nullable();
        });

        Schema::table('app_notifications', function (Blueprint $table) {
            $table->foreignId('verification_id')->nullable()->after('report_id')
                ->constrained('resident_verifications')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('app_notifications', function (Blueprint $table) {
            $table->dropForeign(['verification_id']);
            $table->dropColumn('verification_id');
        });

        Schema::table('resident_verifications', function (Blueprint $table) {
            $table->dropColumn('rejection_reason');
        });
    }
};
