<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\InspectionPolicy;
use App\Models\Outlet;
use App\Models\PolicyItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Outlets ──────────────────────────────────────────────
        $outletNames = [
            '69 Bar & Resto',
            'Warunk Cuci Mata',
            'Pool Bar',
            'Main Kitchen',
            'Butcher',
            'Receiving',
            'Dimsum Kitchen',
            'Bale Sante',
        ];
        foreach ($outletNames as $name) {
            Outlet::firstOrCreate(['name' => $name], ['is_active' => true]);
        }

        // ── Departments ───────────────────────────────────────────
        $deptNames = [
            'Engineering',
            'Housekeeping',
            'FB Service',
            'FB Product',
            'Finance & Accounting',
            'Human Resources',
            'Purchasing',
            'Sales',
            'FO',
            'BDD',
            'General Manager',
        ];
        foreach ($deptNames as $name) {
            Department::firstOrCreate(['name' => $name]);
        }

        // ── Users ─────────────────────────────────────────────────
        $fbProduct = Department::where('name', 'FB Product')->first();
        $fbService = Department::where('name', 'FB Service')->first();

        $users = [
            ['name' => 'Admin User',      'email' => 'admin@foodcontrol.com',    'role' => 'admin',   'department_id' => null],
            ['name' => 'Auditor Utama',   'email' => 'auditor@foodcontrol.com',  'role' => 'auditor', 'department_id' => null],
            ['name' => 'Auditor Dua',     'email' => 'auditor2@foodcontrol.com', 'role' => 'auditor', 'department_id' => null],
            ['name' => 'Staff Kitchen',   'email' => 'kitchen@foodcontrol.com',  'role' => 'auditee', 'department_id' => $fbProduct?->id],
            ['name' => 'Staff FnB',       'email' => 'fnb@foodcontrol.com',      'role' => 'auditee', 'department_id' => $fbService?->id],
        ];
        foreach ($users as $data) {
            User::firstOrCreate(
                ['email' => $data['email']],
                array_merge($data, ['password' => Hash::make('password')])
            );
        }

        // ── Inspection Policies + Items ───────────────────────────
        $policies = [
            [
                'code' => 'I',
                'name' => 'I. Cuci Tangan & Penggunaan APD',
                'due_date_offset_days' => 1,
                'score' => 12,
                'sort_order' => 1,
                'items' => [
                    'Tempat cuci tangan tidak berfungsi dengan baik.',
                    'Peralatan mencuci tangan (sabun, pengering tangan, gel antiseptik, air hangat) tidak tersedia.',
                    'Karyawan tidak mencuci tangan sesuai prosedur.',
                    'APD (hairnet, masker, sarung tangan sekali pakai) tidak tersedia.',
                    'Karyawan tidak menggunakan APD dengan baik.',
                    'Lain-lain',
                ],
            ],
            [
                'code' => 'II',
                'name' => 'II. Kebiasaan dan Kebersihan Diri',
                'due_date_offset_days' => 1,
                'score' => 12,
                'sort_order' => 2,
                'items' => [
                    'Karyawan menggunakan aksesoris berlebihan.',
                    'Karyawan memiliki kuku panjang/berwarna.',
                    'Karyawan tidak menggunakan seragam dengan baik.',
                    'Karyawan sakit terobservasi menangani makanan.',
                    'Lain-lain',
                ],
            ],
            [
                'code' => 'III',
                'name' => 'III. Persiapan dan Pengolahan Makanan',
                'due_date_offset_days' => 1,
                'score' => 12,
                'sort_order' => 3,
                'items' => [
                    'Sayur dan buah tidak dicuci/sanitasi.',
                    'Cairan sanitasi tidak tersedia/berfungsi dengan baik.',
                    'Pelelehan makanan tidak dilakukan dengan baik.',
                    'Proses pelelehan tidak sesuai prosedur.',
                    'Penanganan makanan mentah tidak dilakukan dengan baik.',
                    'Pencatatan suhu penyajian makanan dingin tidak dilakukan.',
                    'Lain-lain.',
                ],
            ],
            [
                'code' => 'IV',
                'name' => 'IV. Pemasakan, Pendinginan, Pemanasan Ulang, dan Umur Simpan Makanan',
                'due_date_offset_days' => 2,
                'score' => 8,
                'sort_order' => 4,
                'items' => [
                    'Suhu minimum internal pemasakan tidak mencapai.',
                    'Pendinginan makanan panas tidak dilakukan dengan baik.',
                    'Umur simpan sekunder makanan telah melebihi dari waktu yang ditentukan.',
                    'Makanan dalam kemasan vakum tidak diberi label/disimpan dengan baik.',
                    'Pencatatan dokumen suhu pemasakan/pendinginan/pemanasan ulang tidak sesuai.',
                    'Lain-lain.',
                ],
            ],
            [
                'code' => 'V',
                'name' => 'V. Suhu & Perawatan Ruang Pendingin',
                'due_date_offset_days' => 2,
                'score' => 8,
                'sort_order' => 5,
                'items' => [
                    'Suhu Actual chiller/freezer tidak mencapai.',
                    'Suhu Display chiller/freezer tidak sesuai.',
                    'Interior chiller/freezer tidak berfungsi dengan baik.',
                    'Eksterior chiller/freezer dalam kondisi tidak baik.',
                    'Terdapat penumpukan embun es.',
                    'Penyimpanan dalam chiller/freezer tidak sesuai.',
                    'FIFO/FEFO tidak berjalan.',
                    'Lain-lain.',
                ],
            ],
            [
                'code' => 'VI',
                'name' => 'VI. Produk Pangan Alergen',
                'due_date_offset_days' => 1,
                'score' => 12,
                'sort_order' => 6,
                'items' => [
                    'Penyimpanan produk alergen tidak dilakukan dengan baik.',
                    'Karyawan tidak memahami pemahaman alergen.',
                    'Penanganan produk alergen tidak dilakukan dengan baik.',
                    'Lain-lain.',
                ],
            ],
            [
                'code' => 'VII',
                'name' => 'VII. Peralatan Masak dan Makan',
                'due_date_offset_days' => 7,
                'score' => 1,
                'sort_order' => 7,
                'items' => [
                    'Talenan/pisau tidak dalam kondisi baik.',
                    'Talenan/pisau disimpan dalam kondisi kurang bersih.',
                    'Talenan/pisau tidak dicuci/disanitasi sebelum digunakan.',
                    'Peralatan masak/makan dalam kondisi tidak baik.',
                    'Penyimpanan alat masak/makan tidak bersih dan sesuai.',
                    'Mesin es krim tidak berfungsi dengan baik.',
                    'Mesin es krim dalam kondisi tidak bersih.',
                    'Mesin pengiris/pemotong tidak berfungsi dengan baik.',
                    'Mesin pengiris/pemotong dalam kondisi tidak bersih.',
                    'Lain-lain.',
                ],
            ],
            [
                'code' => 'VIII',
                'name' => 'VIII. Pencucian',
                'due_date_offset_days' => 2,
                'score' => 8,
                'sort_order' => 8,
                'items' => [
                    'Suhu Dishwashing Machine tidak mencapai.',
                    'Interior/Eksterior Dishwashing Machine tidak dalam kondisi baik.',
                    'Cairan sabun/sanitasi tidak tersedia.',
                    'Tempat Cuci tidak dalam kondisi baik.',
                    'Terdapat sabut logam.',
                    'Lain-lain',
                ],
            ],
            [
                'code' => 'IX',
                'name' => 'IX. Penerimaan dan Supplier',
                'due_date_offset_days' => 2,
                'score' => 8,
                'sort_order' => 9,
                'items' => [
                    'Proses penerimaan tidak sesuai.',
                    'Produk yang diterima tidak dalam kondisi bagus.',
                    'Surat Perjanjian Kerjasama tidak tersedia.',
                    'Suhu penerimaan makanan segar tidak sesuai.',
                    'Lain-lain.',
                ],
            ],
            [
                'code' => 'X',
                'name' => 'X. Penyimpanan Produk Kering',
                'due_date_offset_days' => 7,
                'score' => 1,
                'sort_order' => 10,
                'items' => [
                    'Penyimpanan produk kering tidak sesuai.',
                    'Suhu ruangan tidak mencapai.',
                    'Humiditas ruangan tidak mencapai.',
                    'FIFO/FEFO tidak diterapkan.',
                    'Lain-lain.',
                ],
            ],
            [
                'code' => 'XI',
                'name' => 'XI. Produk Makanan Retail',
                'due_date_offset_days' => 7,
                'score' => 1,
                'sort_order' => 11,
                'items' => [
                    'Catatan produk makanan retail tidak tersedia.',
                    'Produk makanan retail tidak sesuai.',
                    'Produsen tidak sesuai ketentuan.',
                    'Lain-lain.',
                ],
            ],
            [
                'code' => 'XII',
                'name' => 'XII. Restoran, Katering Internal dan Eksternal',
                'due_date_offset_days' => 7,
                'score' => 1,
                'sort_order' => 12,
                'items' => [
                    'Area penyajian tidak bersih / tersanitasi.',
                    'Catatan inspeksi Lokasi tidak lengkap/tersedia.',
                    'Sampel makanan tidak tersedia.',
                    'Lain-lain.',
                ],
            ],
            [
                'code' => 'XIII',
                'name' => 'XIII. Tempat Pembuangan, Pengendalian Hama, Bahan Kimia & Penanganannya',
                'due_date_offset_days' => 7,
                'score' => 1,
                'sort_order' => 13,
                'items' => [
                    'Tempat sampah tidak berfungsi dengan baik.',
                    'Tempat sampah dalam kondisi tidak baik.',
                    'Tempat pembuangan tidak bersih.',
                    'Tempat monitoring hama tidak berfungsi dengan baik.',
                    'Tempat monitoring hama tidak berada di tempatnya.',
                    'SDS Bahan kimia tidak lengkap.',
                    'Peletakan bahan kimia tidak sesuai.',
                    'Lain-lain.',
                ],
            ],
            [
                'code' => 'XIV',
                'name' => 'XIV. Pemeriksaan Konter Bar',
                'due_date_offset_days' => 7,
                'score' => 1,
                'sort_order' => 14,
                'items' => [
                    'Catatan pemeriksaan konter bar tidak tersedia.',
                    'Peralatan bar tidak berfungsi dengan baik.',
                    'Peralatan bar dalam kondisi tidak baik.',
                    'Lain-lain.',
                ],
            ],
            [
                'code' => 'XV',
                'name' => 'XV. Komitmen Manajemen, Pelatihan, dan Disiplin',
                'due_date_offset_days' => 1,
                'score' => 12,
                'sort_order' => 15,
                'items' => [
                    'Kebijakan Keamanan Pangan tidak diterapkan dengan baik.',
                    'Terdapat manipulasi data pencatatan.',
                    'Karyawan tidak memahami Kebijakan Keamanan Pangan.',
                    'Lain-lain.',
                ],
            ],
            [
                'code' => 'LAIN',
                'name' => 'Lain-Lain',
                'due_date_offset_days' => 7,
                'score' => 1,
                'sort_order' => 16,
                'items' => [
                    'Pembersihan dan Sanitasi',
                    'Pemeliharaan Fasilitas',
                    'Bahaya Fisik, Kimia, Biologis',
                    'Penggunaan Bahan Kimia',
                    'Manajemen Hama',
                    'Kelengkapan Dokumen',
                ],
            ],
        ];

        foreach ($policies as $policyData) {
            $items = $policyData['items'];
            unset($policyData['items']);

            $policy = InspectionPolicy::updateOrCreate(
                ['code' => $policyData['code']],
                $policyData
            );

            // Sync items
            $policy->items()->delete();
            foreach ($items as $idx => $text) {
                PolicyItem::create([
                    'inspection_policy_id' => $policy->id,
                    'text'       => $text,
                    'sort_order' => $idx + 1,
                ]);
            }
        }

        $this->call(TestDataSeeder::class);
    }
}
