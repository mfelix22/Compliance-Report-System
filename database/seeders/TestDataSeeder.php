<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Finding;
use App\Models\Inspection;
use App\Models\InspectionCategoryStatus;
use App\Models\InspectionPolicy;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        $auditor  = User::where('role', 'auditor')->first();
        $auditor2 = User::where('email', 'auditor2@foodcontrol.com')->first();
        $outlets  = Outlet::all()->keyBy('name');
        $policies = InspectionPolicy::with('items')->orderBy('sort_order')->get()->keyBy('code');
        $depts    = Department::all()->keyBy('name');

        // Wipe existing test data so seeder is safe to re-run
        $refs = ['INS-2026-04-001', 'INS-2026-05-002', 'INS-2026-05-003', 'INS-2026-03-001'];
        $existing = Inspection::whereIn('reference_no', $refs)->get();
        foreach ($existing as $insp) {
            $insp->findings()->delete();
            $insp->categoryStatuses()->delete();
            $insp->auditors()->detach();
            $insp->delete();
        }

        // ── Inspection 1: completed, mostly compliant (last month) ──────────
        $insp1 = Inspection::create([
            'title'           => 'Monthly Check 69 Bar April 2026',
            'outlet_id'       => $outlets['69 Bar & Resto']->id,
            'reference_no'    => 'INS-2026-04-001',
            'inspection_date' => '2026-04-10',
            'audit_time'      => '09:00',
            'auditor_id'      => $auditor->id,
            'status'          => 'closed',
            'reporter_name'   => 'Budi Santoso',
        ]);
        $insp1->auditors()->sync([$auditor->id]);

        $statusMap1 = [
            'I' => 'C',
            'II' => 'NC',
            'III' => 'C',
            'IV' => 'C',
            'V' => 'NC',
            'VI' => 'C',
            'VII' => 'NA',
            'VIII' => 'C',
            'IX' => 'C',
            'X' => 'C',
            'XI' => 'NA',
            'XII' => 'C',
            'XIII' => 'NC',
            'XIV' => 'NA',
            'XV' => 'C',
            'LAIN' => 'NA',
        ];
        foreach ($statusMap1 as $code => $status) {
            InspectionCategoryStatus::create([
                'inspection_id'        => $insp1->id,
                'inspection_policy_id' => $policies[$code]->id,
                'status'               => $status,
            ]);
        }

        // NC findings for inspection 1 — all closed
        $this->createFinding($insp1, $policies['II'], $depts['FB Product'], [
            'item_text'   => 'Karyawan menggunakan aksesoris berlebihan.',
            'description' => 'Ditemukan 2 karyawan memakai cincin saat bekerja di dapur.',
            'root_cause'  => 'training',
            'due_date'    => '2026-04-11',
            'status'      => 'closed',
            'date_closed' => '2026-04-11',
            'verification_status' => 'complied',
        ]);
        $this->createFinding($insp1, $policies['V'], $depts['FB Product'], [
            'item_text'   => 'Suhu Actual chiller/freezer tidak mencapai.',
            'description' => 'Suhu chiller tercatat +8°C, seharusnya maksimal +4°C.',
            'root_cause'  => 'facilities',
            'due_date'    => '2026-04-12',
            'status'      => 'closed',
            'date_closed' => '2026-04-13',
            'verification_status' => 'complied',
        ]);
        $this->createFinding($insp1, $policies['XIII'], $depts['Engineering'], [
            'item_text'   => 'Tempat sampah tidak berfungsi dengan baik.',
            'description' => 'Penutup tempat sampah injak rusak, tidak bisa menutup otomatis.',
            'root_cause'  => 'facilities',
            'due_date'    => '2026-04-17',
            'status'      => 'closed',
            'date_closed' => '2026-04-16',
            'verification_status' => 'complied',
        ]);

        // ── Inspection 2: in-progress (current month, mixed statuses) ───────
        $insp2 = Inspection::create([
            'title'           => 'Monthly Check Pool Bar May 2026',
            'outlet_id'       => $outlets['Pool Bar']->id,
            'reference_no'    => 'INS-2026-05-002',
            'inspection_date' => '2026-05-15',
            'audit_time'      => '10:30',
            'auditor_id'      => $auditor->id,
            'status'          => 'in_review',
            'reporter_name'   => 'Sari Dewi',
        ]);
        $insp2->auditors()->sync([$auditor->id, $auditor2->id]);

        $statusMap2 = [
            'I' => 'C',
            'II' => 'NC',
            'III' => 'NC',
            'IV' => 'C',
            'V' => 'C',
            'VI' => 'NC',
            'VII' => 'C',
            'VIII' => 'NC',
            'IX' => 'NA',
            'X' => 'C',
            'XI' => 'NA',
            'XII' => 'NA',
            'XIII' => 'C',
            'XIV' => 'NC',
            'XV' => 'C',
            'LAIN' => 'NA',
        ];
        foreach ($statusMap2 as $code => $status) {
            InspectionCategoryStatus::create([
                'inspection_id'        => $insp2->id,
                'inspection_policy_id' => $policies[$code]->id,
                'status'               => $status,
            ]);
        }

        // NC findings for inspection 2 — some open (overdue), some open (not yet due), one closed
        $this->createFinding($insp2, $policies['II'], $depts['FB Service'], [
            'item_text'   => 'Karyawan tidak menggunakan seragam dengan baik.',
            'description' => 'Beberapa staf bar terlihat tanpa hairnet saat pelayanan.',
            'root_cause'  => 'people',
            'due_date'    => '2026-05-16',
            'status'      => 'open',
            'keterangan'  => 'Sudah diperingatkan, menunggu tindak lanjut.',
        ]);
        $this->createFinding($insp2, $policies['III'], $depts['FB Product'], [
            'item_text'   => 'Sayur dan buah tidak dicuci/sanitasi.',
            'description' => 'Buah untuk garnish tidak melalui proses sanitasi sebelum digunakan.',
            'root_cause'  => 'training',
            'due_date'    => '2026-05-16',
            'status'      => 'open',
        ]);
        $this->createFinding($insp2, $policies['VI'], $depts['FB Product'], [
            'item_text'   => 'Penyimpanan produk alergen tidak dilakukan dengan baik.',
            'description' => 'Produk mengandung kacang tanah disimpan tanpa label alergen di chiller.',
            'root_cause'  => 'training',
            'due_date'    => '2026-05-16',
            'status'      => 'closed',
            'date_closed' => '2026-05-17',
            'verification_status' => 'pending',
        ]);
        $this->createFinding($insp2, $policies['VIII'], $depts['Engineering'], [
            'item_text'   => 'Cairan sabun/sanitasi tidak tersedia.',
            'description' => 'Dispenser sabun cuci alat kosong, tidak diisi ulang.',
            'root_cause'  => 'facilities',
            'due_date'    => '2026-05-17',
            'status'      => 'open',
        ]);
        $this->createFinding($insp2, $policies['XIV'], $depts['FB Service'], [
            'item_text'   => 'Peralatan bar tidak berfungsi dengan baik.',
            'description' => 'Mesin soda gun tidak berfungsi, perlu penggantian tabung CO2.',
            'root_cause'  => 'facilities',
            'due_date'    => '2026-05-22',
            'status'      => 'open',
        ]);

        // ── Inspection 3: fresh / open, mostly unassessed ────────────────────
        $insp3 = Inspection::create([
            'title'           => 'Monthly Check Kitchen May 2026',
            'outlet_id'       => $outlets['Main Kitchen']->id,
            'reference_no'    => 'INS-2026-05-003',
            'inspection_date' => '2026-05-20',
            'audit_time'      => '08:00',
            'auditor_id'      => $auditor2->id,
            'status'          => 'open',
            'reporter_name'   => 'Rina Hartati',
        ]);
        $insp3->auditors()->sync([$auditor2->id]);

        $statusMap3 = [
            'I' => 'C',
            'II' => 'C',
            'III' => 'NC',
            'IV' => 'NC',
            'V' => 'NC',
            'VI' => 'C',
        ];
        foreach ($statusMap3 as $code => $status) {
            InspectionCategoryStatus::create([
                'inspection_id'        => $insp3->id,
                'inspection_policy_id' => $policies[$code]->id,
                'status'               => $status,
            ]);
        }

        // NC findings — all still open and not yet due
        $this->createFinding($insp3, $policies['III'], $depts['FB Product'], [
            'item_text'   => 'Proses pelelehan tidak sesuai prosedur.',
            'description' => 'Daging beku ditemukan mencair di suhu ruang, bukan di chiller.',
            'root_cause'  => 'training',
            'due_date'    => '2026-05-21',
            'status'      => 'open',
        ]);
        $this->createFinding($insp3, $policies['III'], $depts['FB Product'], [
            'item_text'   => 'Penanganan makanan mentah tidak dilakukan dengan baik.',
            'description' => 'Talenan yang sama digunakan untuk sayur dan daging mentah.',
            'root_cause'  => 'training',
            'due_date'    => '2026-05-21',
            'status'      => 'open',
        ]);
        $this->createFinding($insp3, $policies['IV'], $depts['FB Product'], [
            'item_text'   => 'Suhu minimum internal pemasakan tidak mencapai.',
            'description' => 'Suhu internal ayam panggang hanya 68°C, standar minimal 74°C.',
            'root_cause'  => 'people',
            'due_date'    => '2026-05-22',
            'status'      => 'open',
        ]);
        $this->createFinding($insp3, $policies['V'], $depts['Engineering'], [
            'item_text'   => 'Terdapat penumpukan embun es.',
            'description' => 'Penumpukan es tebal di dinding freezer walk-in, mengganggu sirkulasi udara.',
            'root_cause'  => 'facilities',
            'due_date'    => '2026-05-22',
            'status'      => 'open',
        ]);

        // ── Inspection 4: Warunk Cuci Mata, older, all closed ───────────────
        $insp4 = Inspection::create([
            'title'           => 'Monthly Check Warunk Cuci Mata March 2026',
            'outlet_id'       => $outlets['Warunk Cuci Mata']->id,
            'reference_no'    => 'INS-2026-03-001',
            'inspection_date' => '2026-03-05',
            'audit_time'      => '09:30',
            'auditor_id'      => $auditor->id,
            'status'          => 'closed',
            'reporter_name'   => 'Deni Kusuma',
        ]);
        $insp4->auditors()->sync([$auditor->id]);

        foreach ($policies as $code => $policy) {
            InspectionCategoryStatus::create([
                'inspection_id'        => $insp4->id,
                'inspection_policy_id' => $policy->id,
                'status'               => 'C',
            ]);
        }
        // No NC findings — 100% compliant inspection for reports testing
    }

    private function createFinding(
        Inspection $inspection,
        InspectionPolicy $policy,
        Department $dept,
        array $data,
    ): Finding {
        static $counters = [];
        $key = $inspection->id;
        $counters[$key] = ($counters[$key] ?? 0) + 1;

        return Finding::create([
            'inspection_id'        => $inspection->id,
            'inspection_policy_id' => $policy->id,
            'number'               => $counters[$key],
            'finding'              => $data['item_text'],
            'root_cause'           => $data['root_cause'],
            'department_id'        => $dept->id,
            'due_date'             => $data['due_date'] ?? null,
            'keterangan'           => $data['keterangan'] ?? null,
            'status'               => $data['status'] ?? 'open',
            'date_closed'          => $data['date_closed'] ?? null,
            'verification_status'  => $data['verification_status'] ?? 'pending',
            'verification_date'    => null,
        ]);
    }
}
