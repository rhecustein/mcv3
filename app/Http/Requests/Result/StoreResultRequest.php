<?php

namespace App\Http\Requests\Result;

use App\Models\Result;
use Illuminate\Foundation\Http\FormRequest;

class StoreResultRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Result::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $type = $this->input('type', 'skb'); // Default to SKB if not specified

        // Common rules for both SKB and MC
        $rules = [
            'doctor_id'             => 'required|exists:doctors,id',
            'patient_id'            => 'nullable|exists:patients,id',
            'patient_name'          => 'required_without:patient_id|string|max:255',
            'dob'                   => 'required_without:patient_id|date',
            'gender'                => 'required_without:patient_id|in:L,P',
            'phone'                 => 'nullable|string|max:20',
            'nik'                   => 'nullable|string|max:50',
            'identity'              => 'nullable|string|max:50',
            'address'               => 'nullable|string|max:255',
            'company'               => 'nullable|string|max:255',
            'company_id'            => 'nullable|exists:companies,id',
            'sign_type'             => 'nullable|in:qrcode,sign_virtual',
            'sign_value'            => 'nullable|string',
            'send_notif_wa'         => 'nullable|boolean',
            'send_notif_email'      => 'nullable|boolean',
            'icd_master_id'         => 'nullable|exists:icd_masters,id',
            'icd_name'              => 'nullable|string|max:255',
            'diagnosis_description' => 'nullable|string|max:500',
            'medical_diagnosis_id'  => 'nullable|exists:medical_diagnoses,id',
        ];

        // Type-specific rules
        if ($type === 'skb') {
            // SKB (Surat Keterangan Berobat) rules
            $rules['date'] = 'required|date';
            $rules['time'] = 'required';
        } else {
            // MC (Medical Certificate) rules
            $rules['start_date'] = 'required|date';
            $rules['end_date'] = 'nullable|date|after_or_equal:start_date';
            $rules['duration'] = 'required|integer|min:1';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'doctor_id.required' => 'Dokter harus dipilih.',
            'doctor_id.exists' => 'Dokter yang dipilih tidak valid.',
            'patient_name.required_without' => 'Nama pasien wajib diisi jika tidak memilih pasien yang sudah ada.',
            'dob.required_without' => 'Tanggal lahir pasien wajib diisi.',
            'dob.date' => 'Tanggal lahir harus berformat tanggal yang valid.',
            'gender.required_without' => 'Jenis kelamin pasien wajib dipilih.',
            'gender.in' => 'Jenis kelamin harus L (Laki-laki) atau P (Perempuan).',
            'date.required' => 'Tanggal pemeriksaan wajib diisi.',
            'date.date' => 'Tanggal pemeriksaan harus berformat tanggal yang valid.',
            'time.required' => 'Waktu pemeriksaan wajib diisi.',
            'start_date.required' => 'Tanggal mulai istirahat wajib diisi.',
            'start_date.date' => 'Tanggal mulai harus berformat tanggal yang valid.',
            'end_date.date' => 'Tanggal selesai harus berformat tanggal yang valid.',
            'end_date.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'duration.required' => 'Durasi istirahat wajib diisi.',
            'duration.integer' => 'Durasi harus berupa angka.',
            'duration.min' => 'Durasi minimal 1 hari.',
            'company_id.exists' => 'Perusahaan yang dipilih tidak valid.',
            'icd_master_id.exists' => 'Kode ICD yang dipilih tidak valid.',
            'sign_type.in' => 'Tipe tanda tangan harus qrcode atau sign_virtual.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'doctor_id' => 'dokter',
            'patient_id' => 'pasien',
            'patient_name' => 'nama pasien',
            'dob' => 'tanggal lahir',
            'gender' => 'jenis kelamin',
            'phone' => 'nomor telepon',
            'nik' => 'NIK',
            'address' => 'alamat',
            'company' => 'nama perusahaan',
            'company_id' => 'perusahaan',
            'date' => 'tanggal pemeriksaan',
            'time' => 'waktu pemeriksaan',
            'start_date' => 'tanggal mulai',
            'end_date' => 'tanggal selesai',
            'duration' => 'durasi',
            'icd_master_id' => 'kode ICD',
            'diagnosis_description' => 'deskripsi diagnosis',
        ];
    }
}
