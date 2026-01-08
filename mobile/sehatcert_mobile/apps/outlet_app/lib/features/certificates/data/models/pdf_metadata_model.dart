import 'package:freezed_annotation/freezed_annotation.dart';

import '../../domain/entities/pdf_metadata.dart';
import 'compression_info_model.dart';

part 'pdf_metadata_model.freezed.dart';
part 'pdf_metadata_model.g.dart';

/// Data model for PDF Metadata
@freezed
class PdfMetadataModel with _$PdfMetadataModel {
  const factory PdfMetadataModel({
    String? path,
    String? url,
    required int size,
    @JsonKey(name: 'size_formatted') required String sizeFormatted,
    @JsonKey(name: 'generated_at') DateTime? generatedAt,
    @JsonKey(name: 'is_compressed') required bool isCompressed,
    CompressionInfoModel? compression,
  }) = _PdfMetadataModel;

  const PdfMetadataModel._();

  factory PdfMetadataModel.fromJson(Map<String, dynamic> json) =>
      _$PdfMetadataModelFromJson(json);

  /// Convert model to domain entity
  PdfMetadata toEntity() {
    return PdfMetadata(
      path: path,
      url: url,
      size: size,
      sizeFormatted: sizeFormatted,
      generatedAt: generatedAt,
      isCompressed: isCompressed,
      compression: compression?.toEntity(),
    );
  }

  /// Create model from domain entity
  factory PdfMetadataModel.fromEntity(PdfMetadata entity) {
    return PdfMetadataModel(
      path: entity.path,
      url: entity.url,
      size: entity.size,
      sizeFormatted: entity.sizeFormatted,
      generatedAt: entity.generatedAt,
      isCompressed: entity.isCompressed,
      compression: entity.compression != null
          ? CompressionInfoModel.fromEntity(entity.compression!)
          : null,
    );
  }
}
