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
            $deptIds = $user->departments->pluck('id');

            $myFindings = Finding::with(['inspection.outlet', 'department'])
                ->whereIn('department_id', $deptIds)
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
            'overdue_findings'  => Finding::where('status', 'open')
                ->whereNotNull('due_date')
                ->where('due_date', '<', now()->startOfDay())
                ->count(),
        ];

        $recentFindings = Finding::with(['inspection', 'department'])
            ->latest()
            ->take(10)
            ->get();

        $recentInspections = Inspection::with('outlet')
            ->latest()
            ->take(5)
            ->get();

        // Chart data
        $trendData = Finding::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $byDepartment = Finding::selectRaw('d.name as department, COUNT(*) as count')
            ->join('departments as d', 'findings.department_id', '=', 'd.id')
            ->groupBy('d.name')
            ->orderByDesc('count')
            ->get();

        $byRootCause = Finding::selectRaw('root_cause, COUNT(*) as count')
            ->groupBy('root_cause')
            ->orderByDesc('count')
            ->get();

        return view('dashboard', compact('stats', 'recentFindings', 'recentInspections', 'trendData', 'byDepartment', 'byRootCause'));
    }
}
