<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->timestamp('confirmed_at')->nullable()->after('resolved_at');
            $table->unsignedSmallInteger('reopen_count')->default(0)->after('confirmed_at');
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn(['confirmed_at', 'reopen_count']);
        });
    }
};
