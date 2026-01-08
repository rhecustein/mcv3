import 'package:freezed_annotation/freezed_annotation.dart';

import '../../domain/repositories/certificate_repository.dart';

part 'storage_info_model.freezed.dart';
part 'storage_info_model.g.dart';

/// Data model for Storage Info
@freezed
class StorageInfoModel with _$StorageInfoModel {
  const factory StorageInfoModel({
    @JsonKey(name: 'total_certificates') required int totalCertificates,
    @JsonKey(name: 'total_size') required int totalSize,
    @JsonKey(name: 'total_size_formatted') required String totalSizeFormatted,
    @JsonKey(name: 'compressed_count') required int compressedCount,
    @JsonKey(name: 'compression_ratio') required double compressionRatio,
  }) = _StorageInfoModel;

  const StorageInfoModel._();

  factory StorageInfoModel.fromJson(Map<String, dynamic> json) =>
      _$StorageInfoModelFromJson(json);

  /// Convert model to domain entity
  StorageInfo toEntity() {
    return StorageInfo(
      totalCertificates: totalCertificates,
      totalSize: totalSize,
      totalSizeFormatted: totalSizeFormatted,
      compressedCount: compressedCount,
      compressionRatio: compressionRatio,
    );
  }
}
