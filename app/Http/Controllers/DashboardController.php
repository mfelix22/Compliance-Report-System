<?php

namespace App\Http\Controllers;

use App\Models\Finding;
use App\Models\Inspection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        if ($user->isAuditee()) {
            $deptId = $user->department_id;

            $myFindings = Finding::with(['inspection.outlet', 'department'])
                ->where('department_id', $deptId)
                ->orderByRaw("CASE WHEN status='open' THEN 0 ELSE 1 END")
                ->orderBy('due_date')
                ->get();

            $stats = [
                'needs_response' => $myFindings->where('status', 'open')
                    ->filter(fn($f) => empty($f->corrective_action))->count(),
                'overdue'        => $myFindings->filter(fn($f) => $f->isOverdue)->count(),
                'closed'         => $myFindings->where('status', 'closed')->count(),
                'pending_verify' => $myFindings->where('status', 'closed')
                    ->where('verification_status', 'pending')->count(),
            ];

            return view('dashboard', compact('stats', 'myFindings'));
        }

        $stats = [
            'total_inspections' => Inspection::count(),
            'open_findings'     => Finding::where('status', 'open')->count(),
            'closed_findings'   => Finding::where('status', 'closed')->count(),
            'pending_verify'    => Finding::where('verification_status', 'pending')
                ->where('status', 'closed')->count(),
        ];

        $recentFindings = Finding::with(['inspection', 'department'])
            ->latest()
            ->take(10)
            ->get();

        $recentInspections = Inspection::with('outlet')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'recentFindings', 'recentInspections'));
    }
}
