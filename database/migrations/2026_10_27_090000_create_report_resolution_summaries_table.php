<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('report_resolution_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->unique()->constrained('reports')->cascadeOnDelete();
            $table->foreignId('written_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('resolution_type', 40);
            $table->text('narrative');
            $table->text('action_taken');
            $table->text('persons_involved')->nullable();
            $table->boolean('follow_up_needed')->default(false);
            $table->text('follow_up_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_resolution_summaries');
    }
};
