<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('app_notifications', function (Blueprint $table) {
            $table->foreignId('user_id')->after('id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('report_id')->nullable()->after('user_id')->constrained('reports')->nullOnDelete();
            $table->string('message')->after('report_id');
            $table->boolean('is_read')->default(false)->after('message');
        });
    }

    public function down(): void
    {
        Schema::table('app_notifications', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['report_id']);
            $table->dropColumn(['user_id', 'report_id', 'message', 'is_read']);
        });
    }
};