import 'package:freezed_annotation/freezed_annotation.dart';

import '../../domain/entities/patient_info.dart';

part 'patient_info_model.freezed.dart';
part 'patient_info_model.g.dart';

/// Data model for Patient Info
@freezed
class PatientInfoModel with _$PatientInfoModel {
  const factory PatientInfoModel({
    required String name,
    String? nik,
    String? birthdate,
    String? gender,
    int? age,
    String? company,
  }) = _PatientInfoModel;

  const PatientInfoModel._();

  factory PatientInfoModel.fromJson(Map<String, dynamic> json) =>
      _$PatientInfoModelFromJson(json);

  /// Convert model to domain entity
  PatientInfo toEntity() {
    return PatientInfo(
      name: name,
      nik: nik,
      birthdate: birthdate,
      gender: gender,
      age: age,
      company: company,
    );
  }

  /// Create model from domain entity
  factory PatientInfoModel.fromEntity(PatientInfo entity) {
    return PatientInfoModel(
      name: entity.name,
      nik: entity.nik,
      birthdate: entity.birthdate,
      gender: entity.gender,
      age: entity.age,
      company: entity.company,
    );
  }
}
