<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('case_assignments', function (Blueprint $table) {
            $table->foreignId('report_id')->after('id')->constrained('reports')->cascadeOnDelete();
            $table->foreignId('assigned_to')->after('report_id')->constrained('users');
            $table->foreignId('assigned_by')->after('assigned_to')->constrained('users');
            $table->timestamp('assigned_at')->after('assigned_by')->useCurrent();
            $table->enum('status', ['assigned', 'in_progress', 'completed'])->default('assigned')->after('assigned_at');
            $table->text('notes')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('case_assignments', function (Blueprint $table) {
            $table->dropForeign(['report_id']);
            $table->dropForeign(['assigned_to']);
            $table->dropForeign(['assigned_by']);
            $table->dropColumn(['report_id', 'assigned_to', 'assigned_by', 'assigned_at', 'status', 'notes']);
        });
    }
};