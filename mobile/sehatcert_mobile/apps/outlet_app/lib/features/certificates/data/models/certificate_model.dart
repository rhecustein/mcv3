import 'package:freezed_annotation/freezed_annotation.dart';

import '../../domain/entities/certificate.dart';
import 'certificate_info_model.dart';
import 'doctor_info_model.dart';
import 'patient_info_model.dart';
import 'pdf_metadata_model.dart';

part 'certificate_model.freezed.dart';
part 'certificate_model.g.dart';

/// Data model for Certificate
@freezed
class CertificateModel with _$CertificateModel {
  const factory CertificateModel({
    required String id,
    @JsonKey(name: 'unique_code') required String uniqueCode,
    required PatientInfoModel patient,
    required CertificateInfoModel certificate,
    required PdfMetadataModel pdf,
    String? qrcode,
    DoctorInfoModel? doctor,
    @JsonKey(name: 'outlet') required OutletInfoModel outlet,
  }) = _CertificateModel;

  const CertificateModel._();

  factory CertificateModel.fromJson(Map<String, dynamic> json) =>
      _$CertificateModelFromJson(json);

  /// Convert model to domain entity
  Certificate toEntity() {
    return Certificate(
      id: id,
      uniqueCode: uniqueCode,
      patient: patient.toEntity(),
      certificate: certificate.toEntity(),
      pdf: pdf.toEntity(),
      qrcode: qrcode,
      doctor: doctor?.toEntity(),
      outletId: outlet.id,
      tenantId: outlet.tenantId,
    );
  }

  /// Create model from domain entity
  factory CertificateModel.fromEntity(Certificate entity) {
    return CertificateModel(
      id: entity.id,
      uniqueCode: entity.uniqueCode,
      patient: PatientInfoModel.fromEntity(entity.patient),
      certificate: CertificateInfoModel.fromEntity(entity.certificate),
      pdf: PdfMetadataModel.fromEntity(entity.pdf),
      qrcode: entity.qrcode,
      doctor: entity.doctor != null
          ? DoctorInfoModel.fromEntity(entity.doctor!)
          : null,
      outlet: OutletInfoModel(
        id: entity.outletId,
        tenantId: entity.tenantId,
      ),
    );
  }
}

/// Outlet info model (nested in certificate response)
@freezed
class OutletInfoModel with _$OutletInfoModel {
  const factory OutletInfoModel({
    required String id,
    @JsonKey(name: 'tenant_id') required String tenantId,
  }) = _OutletInfoModel;

  factory OutletInfoModel.fromJson(Map<String, dynamic> json) =>
      _$OutletInfoModelFromJson(json);
}
