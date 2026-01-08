<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CertificateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'unique_code' => $this->unique_code,
            'patient' => [
                'name' => $this->name,
                'nik' => $this->nik,
                'birthdate' => $this->birthdate,
                'gender' => $this->gender,
                'age' => $this->age,
                'company' => $this->company,
            ],
            'certificate' => [
                'type' => $this->type,
                'status' => $this->status,
                'result' => $this->result,
                'created_at' => $this->created_at?->toISOString(),
                'updated_at' => $this->updated_at?->toISOString(),
            ],
            'pdf' => [
                'path' => $this->pdf_path,
                'url' => $this->pdf_path ? Storage::disk('public')->url($this->pdf_path) : null,
                'size' => $this->pdf_size_bytes,
                'size_formatted' => formatBytes($this->pdf_size_bytes),
                'generated_at' => $this->pdf_generated_at?->toISOString(),
                'is_compressed' => (bool) $this->pdf_compressed,
                'compression' => $this->pdf_compressed ? [
                    'original_size' => $this->pdf_original_size_bytes,
                    'original_size_formatted' => formatBytes($this->pdf_original_size_bytes),
                    'compressed_at' => $this->pdf_compressed_at?->toISOString(),
                    'ratio' => $this->pdf_compression_ratio,
                    'method' => $this->pdf_compression_method,
                    'savings' => $this->pdf_original_size_bytes - $this->pdf_size_bytes,
                    'savings_formatted' => formatBytes($this->pdf_original_size_bytes - $this->pdf_size_bytes),
                ] : null,
            ],
            'qrcode' => $this->qrcode,
            'doctor' => [
                'id' => $this->doctor_id,
                'name' => $this->doctor?->name,
            ],
            'outlet' => [
                'id' => $this->outlet_id,
                'tenant_id' => $this->tenant_id,
            ],
        ];
    }
}
