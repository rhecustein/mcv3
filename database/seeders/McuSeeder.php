<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\McuProvider;
use App\Models\McuPackage;
use Illuminate\Database\Seeder;

class McuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create MCU Providers (not tenant-specific, available for all)
        $providers = [
            [
                'name' => 'Prodia Laboratory',
                'slug' => 'prodia',
                'code' => 'PRO001',
                'description' => 'Prodia adalah laboratorium klinik terkemuka dengan lebih dari 350 cabang di Indonesia. Menyediakan layanan medical check-up komprehensif dengan peralatan modern dan tenaga medis profesional.',
                'email' => 'info@prodia.co.id',
                'phone' => '021-8900-1000',
                'address' => 'Jl. Kramat Raya No. 150',
                'city' => 'Jakarta Pusat',
                'province' => 'DKI Jakarta',
                'postal_code' => '10430',
                'operating_hours' => [
                    'monday' => '07:00-19:00',
                    'tuesday' => '07:00-19:00',
                    'wednesday' => '07:00-19:00',
                    'thursday' => '07:00-19:00',
                    'friday' => '07:00-19:00',
                    'saturday' => '07:00-14:00',
                    'sunday' => 'Closed',
                ],
                'facilities' => ['Lab Modern', 'Ruang Tunggu Nyaman', 'Parkir Luas', 'WiFi Gratis'],
                'certifications' => ['ISO 9001:2015', 'ISO 15189:2012', 'Akreditasi KARS'],
                'is_verified' => true,
                'is_active' => true,
                'rating' => 4.8,
                'total_reviews' => 1234,
            ],
            [
                'name' => 'Pramita Laboratory',
                'slug' => 'pramita',
                'code' => 'PRA001',
                'description' => 'Pramita merupakan laboratorium klinik terpercaya dengan pengalaman lebih dari 30 tahun. Menawarkan paket MCU lengkap dengan harga kompetitif.',
                'email' => 'cs@pramita.co.id',
                'phone' => '021-424-1000',
                'address' => 'Jl. Panglima Polim Raya No. 46',
                'city' => 'Jakarta Selatan',
                'province' => 'DKI Jakarta',
                'postal_code' => '12160',
                'operating_hours' => [
                    'monday' => '06:30-20:00',
                    'tuesday' => '06:30-20:00',
                    'wednesday' => '06:30-20:00',
                    'thursday' => '06:30-20:00',
                    'friday' => '06:30-20:00',
                    'saturday' => '06:30-16:00',
                    'sunday' => '07:00-12:00',
                ],
                'facilities' => ['Laboratorium Canggih', 'Konsultasi Dokter', 'Home Service', 'Online Results'],
                'certifications' => ['ISO 9001:2015', 'ISO 15189:2012'],
                'is_verified' => true,
                'is_active' => true,
                'rating' => 4.7,
                'total_reviews' => 987,
            ],
            [
                'name' => 'RS Siloam',
                'slug' => 'siloam',
                'code' => 'SIL001',
                'description' => 'Rumah Sakit Siloam menyediakan paket medical check-up premium dengan fasilitas lengkap dan teknologi terkini.',
                'email' => 'mcu@siloamhospitals.com',
                'phone' => '021-5666-9000',
                'address' => 'Jl. Garnisun Dalam No. 2-3',
                'city' => 'Jakarta Pusat',
                'province' => 'DKI Jakarta',
                'postal_code' => '10110',
                'operating_hours' => [
                    'monday' => '08:00-17:00',
                    'tuesday' => '08:00-17:00',
                    'wednesday' => '08:00-17:00',
                    'thursday' => '08:00-17:00',
                    'friday' => '08:00-17:00',
                    'saturday' => '08:00-14:00',
                    'sunday' => 'Closed',
                ],
                'facilities' => ['RS Terakreditasi', 'Dokter Spesialis', 'CT Scan', 'MRI', 'Kamar VIP'],
                'certifications' => ['JCI Accredited', 'ISO 9001:2015', 'KARS Paripurna'],
                'is_verified' => true,
                'is_active' => true,
                'rating' => 4.9,
                'total_reviews' => 2156,
            ],
        ];

        foreach ($providers as $providerData) {
            $provider = McuProvider::create($providerData);

            // Create packages for each provider
            $this->createPackagesForProvider($provider);
        }

        $this->command->info('MCU providers and packages created successfully!');
    }

    private function createPackagesForProvider(McuProvider $provider)
    {
        $packages = [
            [
                'name' => 'Paket MCU Basic',
                'slug' => $provider->slug . '-basic',
                'code' => $provider->code . '-PKG001',
                'description' => 'Paket pemeriksaan kesehatan dasar yang mencakup pemeriksaan fisik dan laboratorium standar. Cocok untuk screening kesehatan rutin.',
                'inclusions' => [
                    'Pemeriksaan Fisik Lengkap',
                    'Pemeriksaan Darah Lengkap',
                    'Pemeriksaan Urine Lengkap',
                    'Gula Darah Puasa',
                    'Kolesterol Total',
                    'Asam Urat',
                    'Rontgen Thorax',
                    'EKG',
                    'Konsultasi Dokter',
                ],
                'exclusions' => ['CT Scan', 'MRI', 'Endoskopi'],
                'preparation_instructions' => [
                    'Puasa 10-12 jam sebelum pemeriksaan',
                    'Tidur cukup malam sebelumnya',
                    'Hindari olahraga berat sehari sebelumnya',
                    'Bawa hasil MCU sebelumnya (jika ada)',
                ],
                'price' => 500000,
                'discounted_price' => 450000,
                'discount_percentage' => 10,
                'category' => 'basic',
                'gender_target' => 'all',
                'duration_minutes' => 120,
                'validity_days' => 180,
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Paket MCU Standard',
                'slug' => $provider->slug . '-standard',
                'code' => $provider->code . '-PKG002',
                'description' => 'Paket pemeriksaan kesehatan standar dengan cakupan lebih lengkap. Termasuk pemeriksaan organ penting dan fungsi tubuh.',
                'inclusions' => [
                    'Semua yang ada di Paket Basic',
                    'Fungsi Hati (SGOT, SGPT)',
                    'Fungsi Ginjal (Ureum, Creatinine)',
                    'Profil Lipid Lengkap',
                    'HbA1c',
                    'USG Abdomen',
                    'Audiometri',
                    'Spirometri',
                    'Treadmill',
                ],
                'exclusions' => ['CT Scan', 'MRI', 'Endoskopi', 'Kolonoskopi'],
                'preparation_instructions' => [
                    'Puasa 10-12 jam sebelum pemeriksaan',
                    'Tidur cukup malam sebelumnya',
                    'Hindari olahraga berat sehari sebelumnya',
                    'Bawa hasil MCU sebelumnya (jika ada)',
                    'Kenakan pakaian yang nyaman',
                ],
                'price' => 1500000,
                'discounted_price' => 1350000,
                'discount_percentage' => 10,
                'category' => 'standard',
                'gender_target' => 'all',
                'duration_minutes' => 180,
                'validity_days' => 365,
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Paket MCU Premium',
                'slug' => $provider->slug . '-premium',
                'code' => $provider->code . '-PKG003',
                'description' => 'Paket pemeriksaan kesehatan premium dengan teknologi terkini. Mencakup screening komprehensif untuk deteksi dini penyakit.',
                'inclusions' => [
                    'Semua yang ada di Paket Standard',
                    'Tumor Marker (CEA, AFP, CA 19-9)',
                    'Hepatitis B & C',
                    'CT Scan Thorax',
                    'Echocardiography',
                    'Pap Smear (Wanita)',
                    'PSA (Pria)',
                    'Bone Densitometry',
                    'Konsultasi Dokter Spesialis',
                    'Makan Siang',
                ],
                'exclusions' => ['MRI', 'PET Scan'],
                'preparation_instructions' => [
                    'Puasa 10-12 jam sebelum pemeriksaan',
                    'Tidur cukup malam sebelumnya',
                    'Hindari olahraga berat 2 hari sebelumnya',
                    'Bawa hasil MCU sebelumnya (wajib)',
                    'Kenakan pakaian yang nyaman',
                    'Datang 30 menit lebih awal',
                ],
                'price' => 3500000,
                'discounted_price' => 3150000,
                'discount_percentage' => 10,
                'category' => 'premium',
                'gender_target' => 'all',
                'duration_minutes' => 240,
                'validity_days' => 365,
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Paket MCU Executive',
                'slug' => $provider->slug . '-executive',
                'code' => $provider->code . '-PKG004',
                'description' => 'Paket pemeriksaan kesehatan executive dengan layanan VIP dan pemeriksaan paling lengkap.',
                'inclusions' => [
                    'Semua yang ada di Paket Premium',
                    'MRI Brain',
                    'CT Scan Cardiac',
                    'Whole Abdomen CT Scan',
                    'Colonoscopy',
                    'Gastroscopy',
                    'Full Body Tumor Marker',
                    'Genetic Risk Screening',
                    'Konsultasi Multi-Spesialis',
                    'Ruang VIP',
                    'Breakfast & Lunch',
                    'Free Follow-up 3 bulan',
                ],
                'exclusions' => [],
                'preparation_instructions' => [
                    'Puasa 12 jam sebelum pemeriksaan',
                    'Hindari alkohol 3 hari sebelumnya',
                    'Tidur cukup malam sebelumnya',
                    'Hindari olahraga berat 3 hari sebelumnya',
                    'Bawa hasil MCU sebelumnya (wajib)',
                    'Bawa daftar obat yang sedang dikonsumsi',
                    'Siapkan waktu full day',
                    'Datang 1 jam lebih awal',
                ],
                'price' => 8500000,
                'discounted_price' => null,
                'discount_percentage' => 0,
                'category' => 'executive',
                'gender_target' => 'all',
                'duration_minutes' => 360,
                'validity_days' => 365,
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Paket MCU Wanita',
                'slug' => $provider->slug . '-women',
                'code' => $provider->code . '-PKG005',
                'description' => 'Paket pemeriksaan kesehatan khusus wanita dengan fokus pada organ reproduksi dan screening kanker.',
                'inclusions' => [
                    'Pemeriksaan Fisik Lengkap',
                    'Pemeriksaan Darah Lengkap',
                    'Pemeriksaan Urine Lengkap',
                    'Profil Lipid Lengkap',
                    'Fungsi Hati & Ginjal',
                    'Pap Smear',
                    'USG Mammae',
                    'USG Abdomen & Pelvis',
                    'Tumor Marker (CA 125, CA 15-3)',
                    'Bone Densitometry',
                    'Hormon (FSH, LH, Estradiol)',
                    'Konsultasi Dokter Spesialis Kandungan',
                ],
                'exclusions' => ['CT Scan', 'MRI'],
                'preparation_instructions' => [
                    'Puasa 10-12 jam sebelum pemeriksaan',
                    'Tidak sedang menstruasi',
                    'Tidur cukup malam sebelumnya',
                    'Bawa hasil MCU sebelumnya',
                ],
                'price' => 2500000,
                'discounted_price' => 2250000,
                'discount_percentage' => 10,
                'category' => 'specialized',
                'gender_target' => 'female',
                'duration_minutes' => 180,
                'validity_days' => 365,
                'is_featured' => false,
                'is_active' => true,
            ],
        ];

        foreach ($packages as $packageData) {
            $packageData['provider_id'] = $provider->id;
            McuPackage::create($packageData);
        }
    }
}
