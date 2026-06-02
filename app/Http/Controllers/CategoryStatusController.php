<?php

namespace App\Http\Controllers;

use App\Models\Inspection;
use App\Models\InspectionCategoryStatus;
use App\Models\InspectionPolicy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CategoryStatusController extends Controller
{
    public function update(Request $request, Inspection $inspection, InspectionPolicy $policy): RedirectResponse
    {
        if ($inspection->status === 'closed') {
            return redirect()->route('inspections.show', $inspection)
                ->with('error', 'Inspeksi sudah ditutup. Status kategori tidak dapat diubah.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:C,NC,NA'],
        ]);

        InspectionCategoryStatus::updateOrCreate(
            [
                'inspection_id'        => $inspection->id,
                'inspection_policy_id' => $policy->id,
            ],
            ['status' => $validated['status']]
        );

        return redirect()->route('inspections.show', $inspection)
            ->with('success', 'Category status updated.');
    }
}
