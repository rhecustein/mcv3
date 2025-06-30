<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Package;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        Package::create([
            'name' => 'Basic',
            'code' => 'PKG-BASIC',
            'duration_in_days' => 30,
            'max_letters' => 50,
            'max_patients' => 100,
            'description' => 'Paket Basic cocok untuk klinik kecil atau perusahaan skala kecil.',
            'features' => json_encode([
                'Dashboard Statistik Dasar',
                'Pembuatan Surat Sehat dan Sakit',
                'Export PDF & Print Surat',
                'Notifikasi WA',
            ]),
            'price' => 199000,
            'is_active' => true,
            'is_recommended' => false,
        ]);

        Package::create([
            'name' => 'Pro',
            'code' => 'PKG-PRO',
            'duration_in_days' => 90,
            'max_letters' => 200,
            'max_patients' => 500,
            'description' => 'Paket Pro untuk klinik menengah dan perusahaan dengan jumlah pasien lebih banyak.',
            'features' => json_encode([
                'Semua Fitur Paket Basic',
                'Analitik Penyakit',
                'Akses Multi Outlet',
                'Export Excel & Monitoring Karyawan',
            ]),
            'price' => 499000,
            'is_active' => true,
            'is_recommended' => true,
        ]);

        Package::create([
            'name' => 'Enterprise',
            'code' => 'PKG-ENT',
            'duration_in_days' => 365,
            'max_letters' => null, // Unlimited
            'max_patients' => null, // Unlimited
            'description' => 'Paket Enterprise untuk grup rumah sakit atau perusahaan besar.',
            'features' => json_encode([
                'Semua Fitur Paket Pro',
                'Integrasi API',
                'Dukungan SLA Khusus',
                'Kustomisasi Surat & Template',
            ]),
            'price' => 1999000,
            'is_active' => true,
            'is_recommended' => false,
        ]);
    }
}
