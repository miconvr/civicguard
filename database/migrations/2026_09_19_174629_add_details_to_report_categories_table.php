<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('report_categories', function (Blueprint $table) {
            $table->string('name')->unique()->after('id');
            $table->text('description')->nullable()->after('name');
            $table->enum('default_severity', ['low', 'moderate', 'high', 'critical'])->default('low')->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('report_categories', function (Blueprint $table) {
            $table->dropColumn(['name', 'description', 'default_severity']);
        });
    }
};