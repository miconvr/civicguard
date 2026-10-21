<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resident_verifications', function (Blueprint $table) {
            $table->index(['status', 'submitted_at']);
        });
    }

    public function down(): void
    {
        Schema::table('resident_verifications', function (Blueprint $table) {
            $table->dropIndex(['status', 'submitted_at']);
        });
    }
};
