<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;

class AuditLogController extends Controller
{
    public function index()
    {
        $auditLogs = AuditLog::with('user')->latest()->paginate(25);

        return view('admin.audit-logs', compact('auditLogs'));
    }

    public function exportPdf()
    {
        $auditLogs = AuditLog::with('user')->latest()->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.audit-logs-pdf', compact('auditLogs'));

        return $pdf->download('civicguard-audit-logs-' . now()->format('Y-m-d') . '.pdf');
    }
}