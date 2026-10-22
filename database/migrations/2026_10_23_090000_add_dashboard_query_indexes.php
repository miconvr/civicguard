<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->index(['status', 'severity', 'created_at'], 'reports_status_severity_created_idx');
            $table->index(['assigned_to', 'status', 'created_at'], 'reports_assignee_status_created_idx');
            $table->index(['user_id', 'created_at'], 'reports_user_created_idx');
        });

        Schema::table('app_notifications', function (Blueprint $table) {
            $table->index(['user_id', 'is_read', 'created_at'], 'notifications_user_read_created_idx');
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->index(['action', 'created_at'], 'audit_action_created_idx');
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropIndex('reports_status_severity_created_idx');
            $table->dropIndex('reports_assignee_status_created_idx');
            $table->dropIndex('reports_user_created_idx');
        });

        Schema::table('app_notifications', function (Blueprint $table) {
            $table->dropIndex('notifications_user_read_created_idx');
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropIndex('audit_action_created_idx');
        });
    }
};
