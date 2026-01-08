import 'package:equatable/equatable.dart';

/// Certificate Information entity
class CertificateInfo extends Equatable {
  const CertificateInfo({
    required this.type,
    required this.status,
    this.result,
    this.createdAt,
    this.updatedAt,
  });

  final String type;
  final String status;
  final String? result;
  final DateTime? createdAt;
  final DateTime? updatedAt;

  @override
  List<Object?> get props => [
        type,
        status,
        result,
        createdAt,
        updatedAt,
      ];

  @override
  String toString() => 'CertificateInfo(type: $type, status: $status)';
}
