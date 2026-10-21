<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DataResetController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $request->validate([
            'confirmation' => ['required', 'in:DELETE ALL TEST DATA'],
            'password' => ['required', 'current_password'],
        ]);

        DB::transaction(function () {
            DB::table('curfew_logs')->delete();
            DB::table('case_assignments')->delete();
            DB::table('app_notifications')->delete();
            DB::table('chatbot_conversations')->delete();
            DB::table('resident_verifications')->delete();
            DB::table('reports')->delete();
            DB::table('audit_logs')->delete();
        });

        // DELETE leaves MySQL's auto-increment counters where they were, so restart them at 1.
        // This runs after the transaction because ALTER TABLE causes an implicit commit.
        if (DB::getDriverName() === 'mysql') {
            foreach ([
                'curfew_logs',
                'case_assignments',
                'app_notifications',
                'chatbot_conversations',
                'resident_verifications',
                'reports',
                'audit_logs',
            ] as $table) {
                DB::statement("ALTER TABLE `{$table}` AUTO_INCREMENT = 1");
            }
        }

        $disk = Storage::disk('report_photos');
        $disk->deleteDirectory('report-photos');
        $disk->deleteDirectory('verification-documents');

        return redirect()->route('profile.edit')->with('data-reset-status', __('All test data was deleted. User accounts, profile pictures, and report categories were kept.'));
    }
}
