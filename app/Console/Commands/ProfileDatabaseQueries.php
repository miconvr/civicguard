<?php

namespace App\Console\Commands;

use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProfileDatabaseQueries extends Command
{
    protected $signature = 'civicguard:profile-db {--rows=10000 : Synthetic reports to create (1000–100000)}';

    protected $description = 'Seed an isolated test database and print query plans for common resident and staff screens.';

    public function handle(): int
    {
        if (! app()->environment('testing') || env('CIVICGUARD_DB_BENCHMARK') !== '1') {
            $this->error('This command only runs in a testing environment with CIVICGUARD_DB_BENCHMARK=1.');

            return self::FAILURE;
        }

        $rowCount = (int) $this->option('rows');
        if ($rowCount < 1000 || $rowCount > 100000) {
            $this->error('Choose between 1000 and 100000 synthetic reports.');

            return self::FAILURE;
        }

        if (Report::query()->exists()) {
            $this->error('Use an empty, disposable database with report categories seeded.');

            return self::FAILURE;
        }

        $residentIds = collect(range(1, 25))->map(fn ($number) => User::factory()->create([
            'name' => "Benchmark Resident {$number}",
            'email' => "resident.{$number}@benchmark.test",
            'role' => 'resident',
        ])->id)->all();
        $tanodIds = collect(range(1, 5))->map(fn ($number) => User::factory()->create([
            'name' => "Benchmark Tanod {$number}",
            'email' => "tanod.{$number}@benchmark.test",
            'role' => 'tanod',
        ])->id)->all();
        $categoryIds = ReportCategory::query()->pluck('id')->all();

        if ($categoryIds === []) {
            $this->error('Run the default database seeder first so report categories exist.');

            return self::FAILURE;
        }

        mt_srand(20261023);
        $baseDate = now();
        $rows = [];

        for ($number = 1; $number <= $rowCount; $number++) {
            $createdAt = $baseDate->copy()->subMinutes(mt_rand(0, 525600));
            $status = ['pending', 'in_progress', 'resolved'][mt_rand(0, 2)];
            $rows[] = [
                'user_id' => $residentIds[mt_rand(0, count($residentIds) - 1)],
                'category_id' => $categoryIds[mt_rand(0, count($categoryIds) - 1)],
                'description' => 'Synthetic benchmark report '.$number,
                'location_text' => 'Benchmark location '.($number % 500),
                'severity' => ['low', 'moderate', 'high', 'critical'][mt_rand(0, 3)],
                'status' => $status,
                'assigned_to' => mt_rand(0, 3) === 0 ? null : $tanodIds[mt_rand(0, count($tanodIds) - 1)],
                'resolved_at' => $status === 'resolved' ? $createdAt->copy()->addMinutes(min(mt_rand(5, 10000), (int) $createdAt->diffInMinutes($baseDate))) : null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];

            if (count($rows) === 500) {
                DB::table('reports')->insert($rows);
                $rows = [];
            }
        }

        if ($rows !== []) {
            DB::table('reports')->insert($rows);
        }

        $reportIds = DB::table('reports')->orderBy('id')->pluck('id');
        $reportRows = $reportIds->all();
        $notificationRows = [];
        $auditRows = [];

        foreach (range(1, min(5000, $rowCount)) as $number) {
            $reportId = $reportRows[$number - 1];
            $userId = $residentIds[($number - 1) % count($residentIds)];
            $date = $baseDate->copy()->subMinutes($number);
            $notificationRows[] = [
                'user_id' => $userId,
                'report_id' => $reportId,
                'message' => 'Synthetic benchmark notification',
                'is_read' => $number % 3 === 0,
                'created_at' => $date,
                'updated_at' => $date,
            ];
            $auditRows[] = [
                'user_id' => $userId,
                'action' => 'report_submitted',
                'auditable_type' => Report::class,
                'auditable_id' => $reportId,
                'description' => 'Synthetic benchmark action',
                'metadata' => json_encode(['severity_source' => 'keyword rules']),
                'created_at' => $date,
                'updated_at' => $date,
            ];

            if (count($notificationRows) === 500) {
                DB::table('app_notifications')->insert($notificationRows);
                DB::table('audit_logs')->insert($auditRows);
                $notificationRows = [];
                $auditRows = [];
            }
        }

        if ($notificationRows !== []) {
            DB::table('app_notifications')->insert($notificationRows);
            DB::table('audit_logs')->insert($auditRows);
        }

        $residentId = $residentIds[0];
        $tanodId = $tanodIds[0];
        $queries = [
            'Resident report history' => Report::where('user_id', $residentId)
                ->orderByDesc('created_at')->orderByDesc('id')->limit(15),
            'Staff active report queue' => Report::whereIn('status', ['pending', 'in_progress'])
                ->orderByUrgency()->oldest()->limit(15),
            'Tanod assigned report queue' => Report::where('assigned_to', $tanodId)
                ->whereIn('status', ['pending', 'in_progress'])->orderByUrgency()->oldest()->limit(10),
            'Resident unread notifications' => DB::table('app_notifications')->where('user_id', $residentId)
                ->where('is_read', false)->latest('created_at')->limit(20),
            'Report submission audit history' => DB::table('audit_logs')->where('action', 'report_submitted')
                ->latest('created_at')->limit(25),
        ];

        $this->info("Seeded {$rowCount} reports, up to 5000 notifications, and up to 5000 audit rows.");
        foreach ($queries as $label => $query) {
            $plan = DB::select('EXPLAIN '.$query->toSql(), $query->getBindings());
            $this->line("\n{$label}");
            $this->line(json_encode($plan, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }

        return self::SUCCESS;
    }
}
