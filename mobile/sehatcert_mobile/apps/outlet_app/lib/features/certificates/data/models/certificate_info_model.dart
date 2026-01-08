import 'package:freezed_annotation/freezed_annotation.dart';

import '../../domain/entities/certificate_info.dart';

part 'certificate_info_model.freezed.dart';
part 'certificate_info_model.g.dart';

/// Data model for Certificate Info
@freezed
class CertificateInfoModel with _$CertificateInfoModel {
  const factory CertificateInfoModel({
    required String type,
    required String status,
    String? result,
    @JsonKey(name: 'created_at') DateTime? createdAt,
    @JsonKey(name: 'updated_at') DateTime? updatedAt,
  }) = _CertificateInfoModel;

  const CertificateInfoModel._();

  factory CertificateInfoModel.fromJson(Map<String, dynamic> json) =>
      _$CertificateInfoModelFromJson(json);

  /// Convert model to domain entity
  CertificateInfo toEntity() {
    return CertificateInfo(
      type: type,
      status: status,
      result: result,
      createdAt: createdAt,
      updatedAt: updatedAt,
    );
  }

  /// Create model from domain entity
  factory CertificateInfoModel.fromEntity(CertificateInfo entity) {
    return CertificateInfoModel(
      type: entity.type,
      status: entity.status,
      result: entity.result,
      createdAt: entity.createdAt,
      updatedAt: entity.updatedAt,
    );
  }
}
