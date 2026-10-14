<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Barryvdh\DomPDF\Facade\Pdf;

class AuditLogController extends Controller
{
    public function index()
    {
        $query = AuditLog::with('user')->latest();

        if ($search = trim((string) request('search'))) {
            $like = '%'.addcslashes($search, '%_\\').'%';
            $query->where(function ($q) use ($like) {
                $q->where('description', 'like', $like)
                    ->orWhere('action', 'like', $like)
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', $like));
            });
        }

        if ($action = request('action')) {
            $query->where('action', $action);
        }

        $auditLogs = $query->paginate(25)->withQueryString();
        $actions = AuditLog::query()->distinct()->orderBy('action')->pluck('action');
        $stats = [
            'total' => AuditLog::count(),
            'today' => AuditLog::where('created_at', '>=', now()->startOfDay())->count(),
            'week' => AuditLog::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        return view('admin.audit-logs', compact('auditLogs', 'actions', 'stats'));
    }

    public function exportPdf()
    {
        $auditLogs = AuditLog::with('user')->latest()->get();

        $pdf = Pdf::loadView('admin.audit-logs-pdf', compact('auditLogs'));

        return $pdf->download('civicguard-audit-logs-'.now()->format('Y-m-d').'.pdf');
    }
}
