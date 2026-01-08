part of 'certificate_bloc.dart';

/// Certificate events
@freezed
class CertificateEvent with _$CertificateEvent {
  /// Load certificates (first page)
  const factory CertificateEvent.loadCertificates({
    String? search,
  }) = _LoadCertificates;

  /// Load more certificates (pagination)
  const factory CertificateEvent.loadMoreCertificates() = _LoadMoreCertificates;

  /// Refresh certificates (pull to refresh)
  const factory CertificateEvent.refreshCertificates() = _RefreshCertificates;

  /// Load certificate detail
  const factory CertificateEvent.loadCertificateDetail({
    required String id,
  }) = _LoadCertificateDetail;

  /// Download certificate PDF
  const factory CertificateEvent.downloadCertificate({
    required String id,
  }) = _DownloadCertificate;

  /// Search certificates
  const factory CertificateEvent.searchCertificates({
    required String query,
  }) = _SearchCertificates;

  /// Get PDF URL for preview
  const factory CertificateEvent.getPdfUrl({
    required String id,
  }) = _GetPdfUrl;

  /// Load storage information
  const factory CertificateEvent.loadStorageInfo() = _LoadStorageInfo;
}
