import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:freezed_annotation/freezed_annotation.dart';
import 'package:domain/domain.dart';

import '../../domain/entities/certificate.dart';
import '../../domain/repositories/certificate_repository.dart';
import '../../domain/usecases/download_certificate.dart';
import '../../domain/usecases/get_certificate_detail.dart';
import '../../domain/usecases/get_certificates.dart';
import '../../domain/usecases/get_pdf_url.dart';
import '../../domain/usecases/get_storage_info.dart';
import '../../domain/usecases/search_certificates.dart';

part 'certificate_event.dart';
part 'certificate_state.dart';
part 'certificate_bloc.freezed.dart';

/// BLoC for certificate management
class CertificateBloc extends Bloc<CertificateEvent, CertificateState> {
  CertificateBloc({
    required this.getCertificatesUseCase,
    required this.getCertificateDetailUseCase,
    required this.downloadCertificateUseCase,
    required this.searchCertificatesUseCase,
    required this.getPdfUrlUseCase,
    required this.getStorageInfoUseCase,
  }) : super(const CertificateState.initial()) {
    on<_LoadCertificates>(_onLoadCertificates);
    on<_LoadMoreCertificates>(_onLoadMoreCertificates);
    on<_LoadCertificateDetail>(_onLoadCertificateDetail);
    on<_DownloadCertificate>(_onDownloadCertificate);
    on<_SearchCertificates>(_onSearchCertificates);
    on<_GetPdfUrl>(_onGetPdfUrl);
    on<_LoadStorageInfo>(_onLoadStorageInfo);
    on<_RefreshCertificates>(_onRefreshCertificates);
  }

  final GetCertificates getCertificatesUseCase;
  final GetCertificateDetail getCertificateDetailUseCase;
  final DownloadCertificate downloadCertificateUseCase;
  final SearchCertificates searchCertificatesUseCase;
  final GetPdfUrl getPdfUrlUseCase;
  final GetStorageInfo getStorageInfoUseCase;

  // Current pagination state
  int _currentPage = 1;
  bool _hasMorePages = true;
  String? _currentSearch;

  /// Load certificates (first page)
  Future<void> _onLoadCertificates(
    _LoadCertificates event,
    Emitter<CertificateState> emit,
  ) async {
    emit(const CertificateState.loading());

    _currentPage = 1;
    _currentSearch = event.search;

    final result = await getCertificatesUseCase(
      GetCertificatesParams(
        page: _currentPage,
        perPage: 20,
        search: event.search,
      ),
    );

    result.fold(
      (failure) => emit(
        CertificateState.error(failure.message ?? 'Failed to load certificates'),
      ),
      (paginatedData) {
        _hasMorePages = paginatedData.hasNextPage;
        emit(CertificateState.loaded(
          certificates: paginatedData.data,
          hasMore: _hasMorePages,
        ));
      },
    );
  }

  /// Load more certificates (next page)
  Future<void> _onLoadMoreCertificates(
    _LoadMoreCertificates event,
    Emitter<CertificateState> emit,
  ) async {
    if (!_hasMorePages) return;

    // Keep current state while loading more
    final currentState = state;
    if (currentState is! _Loaded) return;

    emit(CertificateState.loadingMore(
      certificates: currentState.certificates,
    ));

    _currentPage++;

    final result = await getCertificatesUseCase(
      GetCertificatesParams(
        page: _currentPage,
        perPage: 20,
        search: _currentSearch,
      ),
    );

    result.fold(
      (failure) {
        _currentPage--; // Revert page increment on error
        emit(CertificateState.loaded(
          certificates: currentState.certificates,
          hasMore: _hasMorePages,
        ));
      },
      (paginatedData) {
        _hasMorePages = paginatedData.hasNextPage;
        final allCertificates = [
          ...currentState.certificates,
          ...paginatedData.data,
        ];
        emit(CertificateState.loaded(
          certificates: allCertificates,
          hasMore: _hasMorePages,
        ));
      },
    );
  }

  /// Refresh certificates (pull to refresh)
  Future<void> _onRefreshCertificates(
    _RefreshCertificates event,
    Emitter<CertificateState> emit,
  ) async {
    _currentPage = 1;

    final result = await getCertificatesUseCase(
      GetCertificatesParams(
        page: _currentPage,
        perPage: 20,
        search: _currentSearch,
      ),
    );

    result.fold(
      (failure) {
        // Keep current state on refresh error
        if (state is _Loaded) {
          final currentState = state as _Loaded;
          emit(CertificateState.loaded(
            certificates: currentState.certificates,
            hasMore: currentState.hasMore,
          ));
        }
      },
      (paginatedData) {
        _hasMorePages = paginatedData.hasNextPage;
        emit(CertificateState.loaded(
          certificates: paginatedData.data,
          hasMore: _hasMorePages,
        ));
      },
    );
  }

  /// Load certificate detail
  Future<void> _onLoadCertificateDetail(
    _LoadCertificateDetail event,
    Emitter<CertificateState> emit,
  ) async {
    emit(const CertificateState.loadingDetail());

    final result = await getCertificateDetailUseCase(
      GetCertificateDetailParams(id: event.id),
    );

    result.fold(
      (failure) => emit(
        CertificateState.error(
          failure.message ?? 'Failed to load certificate detail',
        ),
      ),
      (certificate) => emit(
        CertificateState.detailLoaded(certificate),
      ),
    );
  }

  /// Download certificate
  Future<void> _onDownloadCertificate(
    _DownloadCertificate event,
    Emitter<CertificateState> emit,
  ) async {
    emit(const CertificateState.downloading());

    final result = await downloadCertificateUseCase(
      DownloadCertificateParams(id: event.id),
    );

    result.fold(
      (failure) => emit(
        CertificateState.error(
          failure.message ?? 'Failed to download certificate',
        ),
      ),
      (filePath) => emit(
        CertificateState.downloaded(filePath),
      ),
    );
  }

  /// Search certificates
  Future<void> _onSearchCertificates(
    _SearchCertificates event,
    Emitter<CertificateState> emit,
  ) async {
    if (event.query.isEmpty) {
      // If search is cleared, reload all certificates
      add(const CertificateEvent.loadCertificates());
      return;
    }

    emit(const CertificateState.searching());

    final result = await searchCertificatesUseCase(
      SearchCertificatesParams(query: event.query),
    );

    result.fold(
      (failure) => emit(
        CertificateState.error(
          failure.message ?? 'Search failed',
        ),
      ),
      (certificates) => emit(
        CertificateState.searchResults(certificates),
      ),
    );
  }

  /// Get PDF URL
  Future<void> _onGetPdfUrl(
    _GetPdfUrl event,
    Emitter<CertificateState> emit,
  ) async {
    final result = await getPdfUrlUseCase(
      GetPdfUrlParams(id: event.id),
    );

    result.fold(
      (failure) => emit(
        CertificateState.error(
          failure.message ?? 'Failed to get PDF URL',
        ),
      ),
      (pdfUrlData) => emit(
        CertificateState.pdfUrlLoaded(pdfUrlData),
      ),
    );
  }

  /// Load storage info
  Future<void> _onLoadStorageInfo(
    _LoadStorageInfo event,
    Emitter<CertificateState> emit,
  ) async {
    final result = await getStorageInfoUseCase(const NoParams());

    result.fold(
      (failure) => emit(
        CertificateState.error(
          failure.message ?? 'Failed to load storage info',
        ),
      ),
      (storageInfo) => emit(
        CertificateState.storageInfoLoaded(storageInfo),
      ),
    );
  }
}
