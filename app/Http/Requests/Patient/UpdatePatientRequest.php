<?php

namespace App\Http\Requests\Patient;

use App\Models\Patient;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePatientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $patient = $this->route('patient');
        return $this->user()->can('update', $patient);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $patient = $this->route('patient');

        return [
            'full_name'   => 'required|string|max:255',
            'gender'      => 'nullable|in:L,P',
            'birth_date'  => 'nullable|date|before:today',
            'birth_place' => 'nullable|string|max:255',
            'blood_type'  => 'nullable|in:A,B,AB,O',
            'phone'       => 'nullable|string|max:20',
            'nik'         => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('patients', 'nik')->ignore($patient->id),
            ],
            'identity'    => 'nullable|string|max:50',
            'address'     => 'nullable|string|max:255',
            'outlet_id'   => 'required|exists:outlets,id',
            'company_id'  => 'nullable|exists:companies,id',
            'marital_status' => 'nullable|in:single,married,divorced,widowed',
            'job'         => 'nullable|string|max:255',
            'religion'    => 'nullable|string|max:50',
            'emergency_contact_name' => 'nullable|string|max:255',
            'medical_notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'full_name.required' => 'Nama lengkap pasien wajib diisi.',
            'full_name.max' => 'Nama lengkap maksimal 255 karakter.',
            'gender.in' => 'Jenis kelamin harus L (Laki-laki) atau P (Perempuan).',
            'birth_date.date' => 'Tanggal lahir harus berformat tanggal yang valid.',
            'birth_date.before' => 'Tanggal lahir harus sebelum hari ini.',
            'blood_type.in' => 'Golongan darah harus A, B, AB, atau O.',
            'phone.max' => 'Nomor telepon maksimal 20 karakter.',
            'nik.max' => 'NIK maksimal 50 karakter.',
            'nik.unique' => 'NIK sudah terdaftar untuk pasien lain.',
            'outlet_id.required' => 'Outlet wajib dipilih.',
            'outlet_id.exists' => 'Outlet yang dipilih tidak valid.',
            'company_id.exists' => 'Perusahaan yang dipilih tidak valid.',
            'marital_status.in' => 'Status pernikahan tidak valid.',
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
            'full_name' => 'nama lengkap',
            'gender' => 'jenis kelamin',
            'birth_date' => 'tanggal lahir',
            'birth_place' => 'tempat lahir',
            'blood_type' => 'golongan darah',
            'phone' => 'nomor telepon',
            'nik' => 'NIK',
            'address' => 'alamat',
            'outlet_id' => 'outlet',
            'company_id' => 'perusahaan',
            'marital_status' => 'status pernikahan',
            'job' => 'pekerjaan',
            'religion' => 'agama',
            'emergency_contact_name' => 'kontak darurat',
            'medical_notes' => 'catatan medis',
        ];
    }
}
