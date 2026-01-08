import 'package:freezed_annotation/freezed_annotation.dart';

import '../../domain/entities/doctor_info.dart';

part 'doctor_info_model.freezed.dart';
part 'doctor_info_model.g.dart';

/// Data model for Doctor Info
@freezed
class DoctorInfoModel with _$DoctorInfoModel {
  const factory DoctorInfoModel({
    required String id,
    String? name,
  }) = _DoctorInfoModel;

  const DoctorInfoModel._();

  factory DoctorInfoModel.fromJson(Map<String, dynamic> json) =>
      _$DoctorInfoModelFromJson(json);

  /// Convert model to domain entity
  DoctorInfo toEntity() {
    return DoctorInfo(
      id: id,
      name: name,
    );
  }

  /// Create model from domain entity
  factory DoctorInfoModel.fromEntity(DoctorInfo entity) {
    return DoctorInfoModel(
      id: entity.id,
      name: entity.name,
    );
  }
}
