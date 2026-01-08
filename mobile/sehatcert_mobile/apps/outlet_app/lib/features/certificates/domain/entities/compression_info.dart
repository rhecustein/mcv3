import 'package:equatable/equatable.dart';

/// Compression Information entity
class CompressionInfo extends Equatable {
  const CompressionInfo({
    required this.originalSize,
    required this.originalSizeFormatted,
    this.compressedAt,
    required this.ratio,
    this.method,
    required this.savings,
    required this.savingsFormatted,
  });

  final int originalSize;
  final String originalSizeFormatted;
  final DateTime? compressedAt;
  final double ratio;
  final String? method;
  final int savings;
  final String savingsFormatted;

  @override
  List<Object?> get props => [
        originalSize,
        originalSizeFormatted,
        compressedAt,
        ratio,
        method,
        savings,
        savingsFormatted,
      ];

  @override
  String toString() =>
      'CompressionInfo(ratio: $ratio%, savings: $savingsFormatted)';
}
