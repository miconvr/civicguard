<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('curfew_logs', function (Blueprint $table) {
            $table->foreignId('report_id')->after('id')->constrained('reports')->cascadeOnDelete();
            $table->string('minor_name')->after('report_id');
            $table->unsignedTinyInteger('minor_age')->nullable()->after('minor_name');
            $table->string('guardian_name')->nullable()->after('minor_age');
            $table->string('guardian_contact')->nullable()->after('guardian_name');
            $table->dateTime('apprehension_datetime')->after('guardian_contact');
            $table->string('apprehension_location')->after('apprehension_datetime');
            $table->unsignedInteger('prior_violations_count')->default(0)->after('apprehension_location');
            $table->foreignId('tanod_id')->after('prior_violations_count')->constrained('users');
            $table->text('notes')->nullable()->after('tanod_id');
        });
    }

    public function down(): void
    {
        Schema::table('curfew_logs', function (Blueprint $table) {
            $table->dropForeign(['report_id']);
            $table->dropForeign(['tanod_id']);
            $table->dropColumn([
                'report_id', 'minor_name', 'minor_age', 'guardian_name',
                'guardian_contact', 'apprehension_datetime', 'apprehension_location',
                'prior_violations_count', 'tanod_id', 'notes',
            ]);
        });
    }
};