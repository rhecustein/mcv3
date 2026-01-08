import 'package:dartz/dartz.dart';
import 'package:core/core.dart';

import '../entities/certificate.dart';

/// Repository interface for certificate operations
abstract class CertificateRepository {
  /// Get paginated list of certificates
  Future<Either<Failure, PaginatedData<Certificate>>> getCertificates({
    int page = 1,
    int perPage = 20,
    String? search,
  });

  /// Search certificates by query
  Future<Either<Failure, List<Certificate>>> searchCertificates({
    required String query,
  });

  /// Get certificate detail by ID
  Future<Either<Failure, Certificate>> getCertificateDetail({
    required String id,
  });

  /// Download certificate PDF
  Future<Either<Failure, String>> downloadCertificate({
    required String id,
  });

  /// Get PDF URL for preview
  Future<Either<Failure, PdfUrlData>> getPdfUrl({
    required String id,
  });

  /// Get storage information
  Future<Either<Failure, StorageInfo>> getStorageInfo();
}

/// Paginated data wrapper
class PaginatedData<T> {
  const PaginatedData({
    required this.data,
    required this.currentPage,
    required this.total,
    required this.perPage,
    required this.lastPage,
    this.nextPageUrl,
    this.prevPageUrl,
  });

  final List<T> data;
  final int currentPage;
  final int total;
  final int perPage;
  final int lastPage;
  final String? nextPageUrl;
  final String? prevPageUrl;

  bool get hasNextPage => currentPage < lastPage;
  bool get hasPrevPage => currentPage > 1;
}

/// PDF URL data
class PdfUrlData {
  const PdfUrlData({
    required this.url,
    required this.filename,
    required this.size,
    required this.sizeFormatted,
  });

  final String url;
  final String filename;
  final int size;
  final String sizeFormatted;
}

/// Storage information
class StorageInfo {
  const StorageInfo({
    required this.totalCertificates,
    required this.totalSize,
    required this.totalSizeFormatted,
    required this.compressedCount,
    required this.compressionRatio,
  });

  final int totalCertificates;
  final int totalSize;
  final String totalSizeFormatted;
  final int compressedCount;
  final double compressionRatio;
}
