import 'package:freezed_annotation/freezed_annotation.dart';

import '../../domain/entities/compression_info.dart';

part 'compression_info_model.freezed.dart';
part 'compression_info_model.g.dart';

/// Data model for Compression Info
@freezed
class CompressionInfoModel with _$CompressionInfoModel {
  const factory CompressionInfoModel({
    @JsonKey(name: 'original_size') required int originalSize,
    @JsonKey(name: 'original_size_formatted')
    required String originalSizeFormatted,
    @JsonKey(name: 'compressed_at') DateTime? compressedAt,
    required double ratio,
    String? method,
    required int savings,
    @JsonKey(name: 'savings_formatted') required String savingsFormatted,
  }) = _CompressionInfoModel;

  const CompressionInfoModel._();

  factory CompressionInfoModel.fromJson(Map<String, dynamic> json) =>
      _$CompressionInfoModelFromJson(json);

  /// Convert model to domain entity
  CompressionInfo toEntity() {
    return CompressionInfo(
      originalSize: originalSize,
      originalSizeFormatted: originalSizeFormatted,
      compressedAt: compressedAt,
      ratio: ratio,
      method: method,
      savings: savings,
      savingsFormatted: savingsFormatted,
    );
  }

  /// Create model from domain entity
  factory CompressionInfoModel.fromEntity(CompressionInfo entity) {
    return CompressionInfoModel(
      originalSize: entity.originalSize,
      originalSizeFormatted: entity.originalSizeFormatted,
      compressedAt: entity.compressedAt,
      ratio: entity.ratio,
      method: entity.method,
      savings: entity.savings,
      savingsFormatted: entity.savingsFormatted,
    );
  }
}
