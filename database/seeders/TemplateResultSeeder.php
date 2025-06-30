<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TemplateResult;

class TemplateResultSeeder extends Seeder
{
    public function run(): void
    {
        TemplateResult::create([
            'name' => 'Surat Sehat Default',
            'code' => 'SKB-DEFAULT',
            'type' => 'skb',
            'description' => 'Template surat keterangan sehat standar.',
            'html_content' => '
                <h1 class="text-center font-bold text-xl">SURAT KETERANGAN SEHAT</h1>
                <p>Yang bertanda tangan di bawah ini, dokter yang memeriksa menerangkan bahwa:</p>
                <ul>
                    <li>Nama: {{ patient_name }}</li>
                    <li>Tempat, Tanggal Lahir: {{ birth_place }}, {{ birth_date }}</li>
                    <li>Pekerjaan: {{ job }}</li>
                    <li>Alamat: {{ address }}</li>
                </ul>
                <p>Setelah dilakukan pemeriksaan, dinyatakan dalam keadaan SEHAT.</p>
                <br><p>Demikian surat ini dibuat untuk dipergunakan sebagaimana mestinya.</p>',
            'default' => true,
            'is_active' => true,
            'created_by' => 1 // ID superadmin dari UserSeeder
        ]);

        TemplateResult::create([
            'name' => 'Surat Sakit Default',
            'code' => 'MC-DEFAULT',
            'type' => 'mc',
            'description' => 'Template surat keterangan sakit standar.',
            'html_content' => '
                <h1 class="text-center font-bold text-xl">SURAT KETERANGAN SAKIT</h1>
                <p>Yang bertanda tangan di bawah ini menerangkan bahwa:</p>
                <ul>
                    <li>Nama: {{ patient_name }}</li>
                    <li>Usia: {{ age }} tahun</li>
                    <li>Alamat: {{ address }}</li>
                </ul>
                <p>Perlu beristirahat selama {{ duration }} hari, terhitung mulai {{ start_date }}.</p>
                <p>Demikian surat ini diberikan untuk dipergunakan sebagaimana mestinya.</p>',
            'default' => true,
            'is_active' => true,
            'created_by' => 1
        ]);
    }
}
