import 'package:equatable/equatable.dart';

import 'certificate_info.dart';
import 'doctor_info.dart';
import 'patient_info.dart';
import 'pdf_metadata.dart';

/// Certificate entity representing a medical certificate
class Certificate extends Equatable {
  const Certificate({
    required this.id,
    required this.uniqueCode,
    required this.patient,
    required this.certificate,
    required this.pdf,
    this.qrcode,
    this.doctor,
    required this.outletId,
    required this.tenantId,
  });

  final String id;
  final String uniqueCode;
  final PatientInfo patient;
  final CertificateInfo certificate;
  final PdfMetadata pdf;
  final String? qrcode;
  final DoctorInfo? doctor;
  final String outletId;
  final String tenantId;

  @override
  List<Object?> get props => [
        id,
        uniqueCode,
        patient,
        certificate,
        pdf,
        qrcode,
        doctor,
        outletId,
        tenantId,
      ];

  @override
  String toString() =>
      'Certificate(id: $id, code: $uniqueCode, patient: ${patient.name})';
}
