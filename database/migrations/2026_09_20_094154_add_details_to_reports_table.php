<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->foreignId('user_id')->after('id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->after('user_id')->constrained('report_categories');
            $table->text('description')->after('category_id');
            $table->string('photo_path')->nullable()->after('description');
            $table->string('location_text')->nullable()->after('photo_path');
            $table->decimal('latitude', 10, 7)->nullable()->after('location_text');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->enum('severity', ['low', 'moderate', 'high', 'critical'])->default('low')->after('longitude');
            $table->enum('status', ['pending', 'in_progress', 'resolved'])->default('pending')->after('severity');
            $table->foreignId('assigned_to')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable()->after('assigned_to');
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['category_id']);
            $table->dropForeign(['assigned_to']);
            $table->dropColumn([
                'user_id', 'category_id', 'description', 'photo_path',
                'location_text', 'latitude', 'longitude', 'severity',
                'status', 'assigned_to', 'resolved_at',
            ]);
        });
    }
};