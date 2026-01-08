import 'package:equatable/equatable.dart';

import 'compression_info.dart';

/// PDF Metadata entity representing PDF file information
class PdfMetadata extends Equatable {
  const PdfMetadata({
    this.path,
    this.url,
    required this.size,
    required this.sizeFormatted,
    this.generatedAt,
    required this.isCompressed,
    this.compression,
  });

  final String? path;
  final String? url;
  final int size;
  final String sizeFormatted;
  final DateTime? generatedAt;
  final bool isCompressed;
  final CompressionInfo? compression;

  @override
  List<Object?> get props => [
        path,
        url,
        size,
        sizeFormatted,
        generatedAt,
        isCompressed,
        compression,
      ];

  @override
  String toString() =>
      'PdfMetadata(size: $sizeFormatted, compressed: $isCompressed)';
}
