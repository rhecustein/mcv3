import 'package:freezed_annotation/freezed_annotation.dart';

import '../../domain/repositories/certificate_repository.dart';

part 'pdf_url_data_model.freezed.dart';
part 'pdf_url_data_model.g.dart';

/// Data model for PDF URL Data
@freezed
class PdfUrlDataModel with _$PdfUrlDataModel {
  const factory PdfUrlDataModel({
    required String url,
    required String filename,
    required int size,
    @JsonKey(name: 'size_formatted') required String sizeFormatted,
  }) = _PdfUrlDataModel;

  const PdfUrlDataModel._();

  factory PdfUrlDataModel.fromJson(Map<String, dynamic> json) =>
      _$PdfUrlDataModelFromJson(json);

  /// Convert model to domain entity
  PdfUrlData toEntity() {
    return PdfUrlData(
      url: url,
      filename: filename,
      size: size,
      sizeFormatted: sizeFormatted,
    );
  }
}
