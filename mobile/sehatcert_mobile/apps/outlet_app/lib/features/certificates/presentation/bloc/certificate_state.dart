part of 'certificate_bloc.dart';

/// Certificate states
@freezed
class CertificateState with _$CertificateState {
  /// Initial state
  const factory CertificateState.initial() = _Initial;

  /// Loading certificates
  const factory CertificateState.loading() = _Loading;

  /// Loading more certificates (pagination)
  const factory CertificateState.loadingMore({
    required List<Certificate> certificates,
  }) = _LoadingMore;

  /// Loading certificate detail
  const factory CertificateState.loadingDetail() = _LoadingDetail;

  /// Downloading certificate
  const factory CertificateState.downloading() = _Downloading;

  /// Searching certificates
  const factory CertificateState.searching() = _Searching;

  /// Certificates loaded successfully
  const factory CertificateState.loaded({
    required List<Certificate> certificates,
    required bool hasMore,
  }) = _Loaded;

  /// Certificate detail loaded
  const factory CertificateState.detailLoaded(Certificate certificate) =
      _DetailLoaded;

  /// Certificate downloaded successfully
  const factory CertificateState.downloaded(String filePath) = _Downloaded;

  /// Search results
  const factory CertificateState.searchResults(
    List<Certificate> certificates,
  ) = _SearchResults;

  /// PDF URL loaded
  const factory CertificateState.pdfUrlLoaded(PdfUrlData pdfUrlData) =
      _PdfUrlLoaded;

  /// Storage info loaded
  const factory CertificateState.storageInfoLoaded(StorageInfo storageInfo) =
      _StorageInfoLoaded;

  /// Error occurred
  const factory CertificateState.error(String message) = _Error;
}
