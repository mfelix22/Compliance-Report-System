<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Finding;
use App\Models\Inspection;
use App\Models\InspectionCategoryStatus;
use App\Models\InspectionPolicy;
use App\Models\PolicyItem;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FindingController extends Controller
{
    public function create(Inspection $inspection): View
    {
        $policyId    = request('policy_id');
        $policy      = $policyId ? InspectionPolicy::with('items')->find($policyId) : null;
        $departments = Department::orderBy('name')->get();
        $nextNumber  = $inspection->findings()->max('number') + 1;
        return view('findings.create', compact('inspection', 'departments', 'nextNumber', 'policy'));
    }

    public function store(Request $request, Inspection $inspection): RedirectResponse
    {
        $request->validate([
            'inspection_policy_id' => ['required', 'exists:inspection_policies,id'],
            'finding'              => ['nullable', 'string', 'max:1000'],
            'root_cause'           => ['required', 'in:people,facilities,training,others'],
            'department_id'        => ['required', 'exists:departments,id'],
            'photo'                => ['required', 'image', 'max:5120'],
            'keterangan'           => ['nullable', 'string'],
        ], [
            'root_cause.required'    => 'Root Cause wajib dipilih.',
            'root_cause.in'          => 'Pilihan Root Cause tidak valid.',
            'department_id.required' => 'Departemen Responsible wajib dipilih.',
            'department_id.exists'   => 'Departemen tidak valid.',
            'photo.required'         => 'Foto bukti wajib diunggah.',
            'photo.image'            => 'File dokumentasi harus berupa gambar.',
            'photo.max'              => 'Ukuran foto maksimal 5 MB.',
        ]);

        $policy     = InspectionPolicy::find($request->inspection_policy_id);
        $nextNumber = ($inspection->findings()->max('number') ?? 0) + 1;

        $selectedIds = !empty($request->selected_policy_item_ids)
            ? array_map('intval', (array) $request->selected_policy_item_ids)
            : null;

        $customItems = array_values(array_filter($request->custom_finding_items ?? []));
        $customItems = !empty($customItems) ? $customItems : null;

        Finding::create([
            'inspection_id'            => $inspection->id,
            'inspection_policy_id'     => $request->inspection_policy_id,
            'number'                   => $nextNumber,
            'selected_policy_item_ids' => $selectedIds,
            'custom_finding_items'     => $customItems,
            'finding'                  => $request->finding,
            'root_cause'               => $request->root_cause,
            'department_id'            => (int) $request->department_id,
            'photo'                    => $request->file('photo')->store('findings', 'public'),
            'keterangan'               => $request->keterangan,
            'due_date'                 => $inspection->inspection_date->addDays($policy->due_date_offset_days),
            'status'                   => 'open',
            'verification_status'      => 'pending',
        ]);

        // Mark this category as NC
        InspectionCategoryStatus::updateOrCreate(
            ['inspection_id' => $inspection->id, 'inspection_policy_id' => $request->inspection_policy_id],
            ['status' => 'NC']
        );

        return redirect()->route('inspections.show', $inspection)
            ->with('success', 'Temuan berhasil disimpan.');
    }

    /**
     * Auditee fills in corrective / preventive actions.
     */
    public function edit(Finding $finding): View|RedirectResponse
    {
        $user = auth()->user();

        if ($user->isAuditee() && $user->department_id !== $finding->department_id) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda tidak memiliki akses ke temuan ini.');
        }

        $finding->load('inspection', 'department', 'policy', 'policyItem');
        return view('findings.edit', compact('finding'));
    }

    public function update(Request $request, Finding $finding): RedirectResponse
    {
        $user = auth()->user();

        if ($user->isAuditee() && $user->department_id !== $finding->department_id) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda tidak memiliki akses ke temuan ini.');
        }

        if ($user->isAuditee() || $user->isAdmin()) {
            $validated = $request->validate([
                'corrective_action' => ['required', 'string'],
                'preventive_action' => ['required', 'string'],
            ]);
            $validated['status']      = 'closed';
            $validated['date_closed'] = now()->toDateString();
            $finding->update($validated);
        }

        if (($user->isAuditor() || $user->isAdmin()) && $request->has('verification_status')) {
            $verData = $request->validate([
                'verification_status' => ['required', 'in:complied,not_complied,pending'],
                'verification_date'   => ['nullable', 'date'],
                'verification_notes'  => [
                    $request->input('verification_status') === 'not_complied' ? 'required' : 'nullable',
                    'string',
                    'max:2000',
                ],
            ]);
            $finding->update($verData);

            // If Not Complied and auditor wants a follow-up, create a follow-up finding
            if ($verData['verification_status'] === 'not_complied' && $request->boolean('create_followup')) {
                $finding->load('inspection', 'policy');

                // Only create if a follow-up finding doesn't already exist
                $alreadyExists = $finding->followUpFinding()->exists();

                if (!$alreadyExists) {
                    $nextNumber = $finding->inspection->findings()->max('number') + 1;

                    Finding::create([
                        'inspection_id'            => $finding->inspection_id,
                        'parent_finding_id'        => $finding->id,
                        'inspection_policy_id'     => $finding->inspection_policy_id,
                        'policy_item_id'           => $finding->policy_item_id,
                        'selected_policy_item_ids' => $finding->selected_policy_item_ids,
                        'custom_finding_items'     => $finding->custom_finding_items,
                        'number'                   => $nextNumber,
                        'finding'                  => $finding->finding,
                        'root_cause'               => $finding->root_cause,
                        'department_id'            => $finding->department_id,
                        'photo'                    => $finding->photo,
                        'keterangan'               => $verData['verification_notes'] ?? $finding->keterangan,
                        'due_date'                 => $finding->due_date,
                        'status'                   => 'open',
                        'verification_status'      => 'pending',
                    ]);
                }

                return redirect()->route('inspections.show', $finding->inspection_id);
                // ->with('success', 'Verifikasi disimpan. Follow-up finding berhasil dibuat.');
            }
        }

        return redirect()->route('inspections.show', $finding->inspection_id);
        // ->with('success', 'Verifikasi disimpan.');
    }

    public function destroy(Finding $finding): RedirectResponse
    {
        $inspection = $finding->inspection;
        if ($inspection->status === 'closed') {
            return redirect()->route('inspections.show', $inspection)
                ->with('error', 'Inspeksi sudah ditutup. Temuan tidak dapat dihapus.');
        }

        // Delete the stored photo if it exists
        if ($finding->photo) {
            \Storage::disk('public')->delete($finding->photo);
        }
        $inspectionId = $finding->inspection_id;
        $finding->delete();
        return redirect()->route('inspections.show', $inspectionId)
            ->with('success', 'Finding removed.');
    }

    public function verify(Finding $finding): View
    {
        $finding->load('followUpFinding', 'department', 'policy', 'policyItem', 'inspection');

        // Check if a follow-up finding already exists for this finding
        $existingFollowUp = $finding->followUpFinding;

        return view('findings.verify', compact('finding', 'existingFollowUp'));
    }
}
