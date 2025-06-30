<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Support\Facades\Hash;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        // Buat akun user untuk pasien
        $user = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@suratsehat.test',
            'password' => Hash::make('password'),
            'role_type' => 'patient',
            'phone' => '081234567893',
            'email_verified_at' => now(),
            'last_login_at' => now(),
        ]);

        // Buat data pasien
        Patient::create([
            'full_name' => 'Budi Santoso',
            'user_id' => $user->id,
            'company_id' => 1, 
            'nik' => '3174123456780001',
            'birth_place' => 'Jakarta',
            'birth_date' => '1990-05-15',
            'gender' => 'male',
            'blood_type' => 'O',
            'marital_status' => 'married',
            'address' => 'Jl. Damai No. 12, Jakarta Selatan',
            'job' => 'Karyawan Swasta',
            'religion' => 'Islam',
            'phone' => '081234567893',
        ]);
    }
}
