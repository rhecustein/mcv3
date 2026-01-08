import 'package:dio/dio.dart';
import 'package:injectable/injectable.dart';
import 'package:shared_preferences/shared_preferences.dart';

import '../data/datasources/certificate_local_datasource.dart';
import '../data/datasources/certificate_remote_datasource.dart';
import '../data/repositories/certificate_repository_impl.dart';
import '../domain/repositories/certificate_repository.dart';
import '../domain/usecases/download_certificate.dart';
import '../domain/usecases/get_certificate_detail.dart';
import '../domain/usecases/get_certificates.dart';
import '../domain/usecases/get_pdf_url.dart';
import '../domain/usecases/get_storage_info.dart';
import '../domain/usecases/search_certificates.dart';
import '../presentation/bloc/certificate_bloc.dart';

/// Dependency injection module for Certificate feature
@module
abstract class CertificateModule {
  // Data sources
  @lazySingleton
  CertificateRemoteDataSource provideCertificateRemoteDataSource(Dio dio) {
    return CertificateRemoteDataSource(dio);
  }

  @lazySingleton
  CertificateLocalDataSource provideCertificateLocalDataSource(
    SharedPreferences prefs,
  ) {
    return CertificateLocalDataSource(prefs);
  }

  // Repository
  @lazySingleton
  CertificateRepository provideCertificateRepository(
    CertificateRemoteDataSource remoteDataSource,
    CertificateLocalDataSource localDataSource,
  ) {
    return CertificateRepositoryImpl(
      remoteDataSource: remoteDataSource,
      localDataSource: localDataSource,
    );
  }

  // Use cases
  @lazySingleton
  GetCertificates provideGetCertificates(CertificateRepository repository) {
    return GetCertificates(repository);
  }

  @lazySingleton
  GetCertificateDetail provideGetCertificateDetail(
    CertificateRepository repository,
  ) {
    return GetCertificateDetail(repository);
  }

  @lazySingleton
  DownloadCertificate provideDownloadCertificate(
    CertificateRepository repository,
  ) {
    return DownloadCertificate(repository);
  }

  @lazySingleton
  SearchCertificates provideSearchCertificates(
    CertificateRepository repository,
  ) {
    return SearchCertificates(repository);
  }

  @lazySingleton
  GetPdfUrl provideGetPdfUrl(CertificateRepository repository) {
    return GetPdfUrl(repository);
  }

  @lazySingleton
  GetStorageInfo provideGetStorageInfo(CertificateRepository repository) {
    return GetStorageInfo(repository);
  }

  // BLoC
  @injectable
  CertificateBloc provideCertificateBloc(
    GetCertificates getCertificatesUseCase,
    GetCertificateDetail getCertificateDetailUseCase,
    DownloadCertificate downloadCertificateUseCase,
    SearchCertificates searchCertificatesUseCase,
    GetPdfUrl getPdfUrlUseCase,
    GetStorageInfo getStorageInfoUseCase,
  ) {
    return CertificateBloc(
      getCertificatesUseCase: getCertificatesUseCase,
      getCertificateDetailUseCase: getCertificateDetailUseCase,
      downloadCertificateUseCase: downloadCertificateUseCase,
      searchCertificatesUseCase: searchCertificatesUseCase,
      getPdfUrlUseCase: getPdfUrlUseCase,
      getStorageInfoUseCase: getStorageInfoUseCase,
    );
  }
}
