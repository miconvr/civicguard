<?php

namespace Database\Seeders;

use App\Models\ReportCategory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
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
            ReportCategory::firstOrCreate(['name' => $category['name']], $category);
        }
    }
}