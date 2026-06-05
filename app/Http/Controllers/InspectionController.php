<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Finding;
use App\Models\Inspection;
use App\Models\InspectionPolicy;
use App\Models\InspectionTemplate;
use App\Models\Outlet;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InspectionController extends Controller
{
    public function index(Request $request): View
    {
        $query = Inspection::with(['auditors', 'findings', 'outlet'])
            ->latest();

        if ($request->filled('outlet_id')) {
            $query->where('outlet_id', $request->outlet_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $inspections = $query->paginate(15)->withQueryString();
        $outlets     = Outlet::where('is_active', true)->orderBy('name')->get();

        return view('inspections.index', compact('inspections', 'outlets'));
    }

    public function create(): View
    {
        $outlets   = Outlet::where('is_active', true)->orderBy('name')->get();
        $auditors  = User::whereIn('role', ['admin', 'auditor'])->orderBy('name')->get();
        $templates = InspectionTemplate::where('is_active', true)->orderBy('name')->get();
        return view('inspections.create', compact('outlets', 'auditors', 'templates'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'           => ['required', 'string', 'max:255'],
            'outlet_id'       => ['required', 'exists:outlets,id'],
            'inspection_date' => ['required', 'date'],
            'auditor_ids'     => ['required', 'array', 'min:1'],
            'auditor_ids.*'   => ['exists:users,id'],
            'template_id'     => ['nullable', 'exists:inspection_templates,id'],
            'notes'           => ['nullable', 'string'],
        ]);

        $validated['auditor_id']   = $validated['auditor_ids'][0];
        $validated['reference_no'] = Inspection::generateReferenceNo();
        $validated['status']       = 'open';

        $inspection = Inspection::create($validated);
        $inspection->auditors()->sync($validated['auditor_ids']);

        // Pre-populate findings from template if one was selected
        if (! empty($validated['template_id'])) {
            $template = InspectionTemplate::with('items')->find($validated['template_id']);
            foreach ($template->items as $index => $item) {
                Finding::create([
                    'inspection_id'   => $inspection->id,
                    'number'          => $index + 1,
                    'finding'         => $item->description,
                    'root_cause'      => $item->suggested_root_cause ?? 'others',
                    'department_id'   => $item->suggested_department_id,
                    'status'          => 'open',
                    'verification_status' => 'pending',
                ]);
            }
        }

        return redirect()->route('inspections.show', $inspection)
            ->with('success', 'Inspection created.' . (($validated['template_id'] ?? null) ? ' Findings pre-loaded from template — review and adjust as needed.' : ' You can now add findings.'));
    }

    public function show(Inspection $inspection): View
    {
        $inspection->load([
            'outlet',
            'auditors',
            'findings.department',
            'findings.policy',
            'findings.policyItem',
            'findings.parentFinding',
            'findings.followUpFinding',
            'findings'           => fn($q) => $q->orderBy('number'),
            'categoryStatuses',
        ]);

        $policies        = InspectionPolicy::with('items')->orderBy('sort_order')->get();
        $statusByPolicy  = $inspection->categoryStatuses->keyBy('inspection_policy_id');
        $findingsByPolicy = $inspection->findings->groupBy('inspection_policy_id');
        $departments     = Department::orderBy('name')->get();

        return view('inspections.show', compact(
            'inspection',
            'policies',
            'statusByPolicy',
            'findingsByPolicy',
            'departments'
        ));
    }

    public function pdf(Inspection $inspection)
    {
        $inspection->load([
            'outlet',
            'auditors',
            'findings.department',
            'findings'           => fn($q) => $q->orderBy('number'),
            'categoryStatuses',
        ]);

        $policies         = InspectionPolicy::with('items')->orderBy('sort_order')->get();
        $statusByPolicy   = $inspection->categoryStatuses->keyBy('inspection_policy_id');
        $findingsByPolicy = $inspection->findings->groupBy('inspection_policy_id');

        $pdf = Pdf::loadView('inspections.pdf', compact(
            'inspection',
            'policies',
            'statusByPolicy',
            'findingsByPolicy',
        ))->setPaper('a4', 'portrait');

        return $pdf->download($inspection->reference_no . '.pdf');
    }

    public function close(Inspection $inspection): RedirectResponse
    {
        if ($inspection->status === 'closed') {
            return redirect()->route('inspections.show', $inspection)
                ->with('error', 'Inspection is already closed.');
        }

        $inspection->load(['categoryStatuses', 'findings']);
        $policies      = InspectionPolicy::orderBy('sort_order')->get();
        $totalPolicies = $policies->count();
        $assessed      = $inspection->categoryStatuses->count();
        $openFindings  = $inspection->findings->where('status', 'open')->count();

        $warnings = [];

        if ($assessed < $totalPolicies) {
            $warnings[] = ($totalPolicies - $assessed) . ' checklist category/categories have not been assessed yet (C / NC / N/A).';
        }

        if ($openFindings > 0) {
            $warnings[] = $openFindings . ' finding(s) are still open (not yet closed by auditee).';
        }

        if (!empty($warnings)) {
            return redirect()->route('inspections.show', $inspection)
                ->with('close_warnings', $warnings);
        }

        $inspection->update(['status' => 'closed']);

        return redirect()->route('inspections.show', $inspection)
            ->with('success', 'Inspection has been closed.');
    }

    public function edit(Inspection $inspection): View
    {
        $inspection->load(['auditors', 'outlet']);
        $outlets  = Outlet::where('is_active', true)->orderBy('name')->get();
        $auditors = User::whereIn('role', ['admin', 'auditor'])->orderBy('name')->get();
        return view('inspections.edit', compact('inspection', 'outlets', 'auditors'));
    }

    public function update(Request $request, Inspection $inspection): RedirectResponse
    {
        $validated = $request->validate([
            'title'           => ['required', 'string', 'max:255'],
            'outlet_id'       => ['required', 'exists:outlets,id'],
            'inspection_date' => ['required', 'date'],
            'auditor_ids'     => ['required', 'array', 'min:1'],
            'auditor_ids.*'   => ['exists:users,id'],
            'status'          => ['required', 'in:open,closed,in_review'],
            'notes'           => ['nullable', 'string'],
        ]);

        $validated['auditor_id'] = $validated['auditor_ids'][0];
        $inspection->update($validated);
        $inspection->auditors()->sync($validated['auditor_ids']);

        return redirect()->route('inspections.show', $inspection);
        // ->with('success', 'Inspection updated.');
    }

    public function destroy(Inspection $inspection): RedirectResponse
    {
        $inspection->delete();
        return redirect()->route('inspections.index')
            ->with('success', 'Inspection deleted.');
    }

    /**
     * Show the "create follow-up inspection" form pre-loaded with not-complied findings.
     */
    public function createFollowUp(Inspection $parent): View
    {
        $parent->load(['notCompliedFindings.department', 'outlet', 'auditors']);
        $outlets   = Outlet::where('is_active', true)->orderBy('name')->get();
        $auditors  = User::whereIn('role', ['admin', 'auditor'])->orderBy('name')->get();
        return view('inspections.follow_up', compact('parent', 'outlets', 'auditors'));
    }

    /**
     * Store the follow-up inspection and copy not-complied findings as new open findings.
     */
    public function storeFollowUp(Request $request, Inspection $parent): RedirectResponse
    {
        $validated = $request->validate([
            'title'           => ['required', 'string', 'max:255'],
            'outlet_id'       => ['required', 'exists:outlets,id'],
            'inspection_date' => ['required', 'date'],
            'auditor_ids'     => ['required', 'array', 'min:1'],
            'auditor_ids.*'   => ['exists:users,id'],
            'notes'           => ['nullable', 'string'],
        ]);

        $validated['auditor_id']          = $validated['auditor_ids'][0];
        $validated['reference_no']        = Inspection::generateReferenceNo();
        $validated['status']              = 'open';
        $validated['parent_inspection_id'] = $parent->id;

        $inspection = Inspection::create($validated);
        $inspection->auditors()->sync($validated['auditor_ids']);

        // Copy all not-complied findings from parent, preserving their original due_date
        foreach ($parent->notCompliedFindings()->with('department')->get() as $index => $src) {
            Finding::create([
                'inspection_id'       => $inspection->id,
                'number'              => $index + 1,
                'finding'             => $src->finding,
                'root_cause'          => $src->root_cause,
                'department_id'       => $src->department_id,
                'due_date'            => $src->due_date,   // keep original deadline — it doesn't reset
                'status'              => 'open',
                'verification_status' => 'pending',
            ]);
        }

        return redirect()->route('inspections.show', $inspection)
            ->with('success', 'Follow-up inspection created with ' . $parent->notCompliedFindings()->count() . ' carried-over finding(s).');
    }
}
