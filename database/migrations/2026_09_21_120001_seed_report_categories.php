<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $categories = [
            ['name' => 'Stray Animal', 'default_severity' => 'low'],
            ['name' => 'Public Disturbance', 'default_severity' => 'moderate'],
            ['name' => 'Dispute', 'default_severity' => 'moderate'],
            ['name' => 'Curfew Violation', 'default_severity' => 'high'],
            ['name' => 'Noise Complaint', 'default_severity' => 'low'],
            ['name' => 'Vandalism', 'default_severity' => 'moderate'],
            ['name' => 'Other', 'default_severity' => 'low'],
        ];

        foreach ($categories as $category) {
            DB::table('report_categories')->insertOrIgnore([
                ...$category,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('report_categories')
            ->whereIn('name', [
                'Stray Animal',
                'Public Disturbance',
                'Dispute',
                'Curfew Violation',
                'Noise Complaint',
                'Vandalism',
                'Other',
            ])
            ->delete();
    }
};
