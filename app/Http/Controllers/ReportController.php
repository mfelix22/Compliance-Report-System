<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Finding;
use App\Models\Inspection;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $departments = Department::orderBy('name')->get();
        $outlets     = Outlet::where('is_active', true)->orderBy('name')->get();

        $query = Finding::with(['inspection.outlet', 'department'])->orderBy('created_at', 'desc');

        if ($request->filled('outlet_id')) {
            $query->whereHas('inspection', fn($q) => $q->where('outlet_id', $request->outlet_id));
        }
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('verification_status')) {
            $query->where('verification_status', $request->verification_status);
        }
        if ($request->filled('root_cause')) {
            $query->where('root_cause', $request->root_cause);
        }
        if ($request->filled('date_from')) {
            $query->whereHas('inspection', fn($q) => $q->where('inspection_date', '>=', $request->date_from));
        }
        if ($request->filled('date_to')) {
            $query->whereHas('inspection', fn($q) => $q->where('inspection_date', '<=', $request->date_to));
        }

        $findings = $query->paginate(25)->withQueryString();

        $summary = [
            'open'        => Finding::where('status', 'open')->count(),
            'closed'      => Finding::where('status', 'closed')->count(),
            'complied'    => Finding::where('verification_status', 'complied')->count(),
            'not_complied' => Finding::where('verification_status', 'not_complied')->count(),
        ];

        return view('reports.index', compact('findings', 'departments', 'outlets', 'summary'));
    }
}
