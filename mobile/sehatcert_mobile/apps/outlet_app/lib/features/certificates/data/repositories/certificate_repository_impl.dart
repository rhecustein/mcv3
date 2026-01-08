import 'package:dartz/dartz.dart';
import 'package:dio/dio.dart';
import 'package:core/core.dart';
import 'package:path_provider/path_provider.dart';
import 'dart:io';

import '../../domain/entities/certificate.dart';
import '../../domain/repositories/certificate_repository.dart';
import '../datasources/certificate_local_datasource.dart';
import '../datasources/certificate_remote_datasource.dart';

/// Implementation of CertificateRepository
class CertificateRepositoryImpl implements CertificateRepository {
  CertificateRepositoryImpl({
    required this.remoteDataSource,
    required this.localDataSource,
  });

  final CertificateRemoteDataSource remoteDataSource;
  final CertificateLocalDataSource localDataSource;

  @override
  Future<Either<Failure, PaginatedData<Certificate>>> getCertificates({
    int page = 1,
    int perPage = 20,
    String? search,
  }) async {
    try {
      // Fetch from remote API
      final response = await remoteDataSource.getCertificates(
        page: page,
        perPage: perPage,
        search: search,
      );

      // Cache the first page of results
      if (page == 1 && search == null) {
        await localDataSource.cacheCertificates(response.data);
      }

      // Convert to domain entity
      final paginatedData = response.toEntity((model) => model.toEntity());

      return Right(paginatedData);
    } on DioException catch (e) {
      // On network error, try to return cached data
      if (page == 1 && search == null) {
        final cached = await localDataSource.getCachedCertificates();
        if (cached != null) {
          final certificates = cached.map((m) => m.toEntity()).toList();
          return Right(PaginatedData(
            data: certificates,
            currentPage: 1,
            total: certificates.length,
            perPage: perPage,
            lastPage: 1,
          ));
        }
      }
      return Left(_handleDioError(e));
    } on CacheException catch (e) {
      return Left(CacheFailure(message: e.message));
    } catch (e) {
      return Left(GeneralFailure(message: e.toString()));
    }
  }

  @override
  Future<Either<Failure, List<Certificate>>> searchCertificates({
    required String query,
  }) async {
    try {
      final response = await remoteDataSource.searchCertificates(query: query);

      if (!response.success) {
        return Left(ServerFailure(
          message: response.message ?? 'Search failed',
        ));
      }

      final certificates = response.data.map((m) => m.toEntity()).toList();
      return Right(certificates);
    } on DioException catch (e) {
      return Left(_handleDioError(e));
    } catch (e) {
      return Left(GeneralFailure(message: e.toString()));
    }
  }

  @override
  Future<Either<Failure, Certificate>> getCertificateDetail({
    required String id,
  }) async {
    try {
      // Try cache first
      final cached = await localDataSource.getCachedCertificateDetail(id);
      if (cached != null) {
        return Right(cached.toEntity());
      }

      // Fetch from remote
      final response = await remoteDataSource.getCertificateDetail(id: id);

      if (!response.success) {
        return Left(ServerFailure(
          message: response.message ?? 'Failed to get certificate detail',
        ));
      }

      // Cache the result
      await localDataSource.cacheCertificateDetail(response.data);

      return Right(response.data.toEntity());
    } on DioException catch (e) {
      return Left(_handleDioError(e));
    } on CacheException catch (e) {
      return Left(CacheFailure(message: e.message));
    } catch (e) {
      return Left(GeneralFailure(message: e.toString()));
    }
  }

  @override
  Future<Either<Failure, String>> downloadCertificate({
    required String id,
  }) async {
    try {
      // Download PDF bytes from API
      final response = await remoteDataSource.downloadCertificate(id: id);

      if (response.response.statusCode != 200) {
        return Left(ServerFailure(
          message: 'Failed to download certificate',
          statusCode: response.response.statusCode,
        ));
      }

      // Get downloads directory
      final directory = await getApplicationDocumentsDirectory();
      final downloadsPath = '${directory.path}/downloads';

      // Create downloads folder if it doesn't exist
      final downloadsDir = Directory(downloadsPath);
      if (!await downloadsDir.exists()) {
        await downloadsDir.create(recursive: true);
      }

      // Generate filename
      final timestamp = DateTime.now().millisecondsSinceEpoch;
      final filename = 'certificate_$id\_$timestamp.pdf';
      final filePath = '$downloadsPath/$filename';

      // Write file
      final file = File(filePath);
      await file.writeAsBytes(response.data);

      return Right(filePath);
    } on DioException catch (e) {
      return Left(_handleDioError(e));
    } catch (e) {
      return Left(GeneralFailure(message: e.toString()));
    }
  }

  @override
  Future<Either<Failure, PdfUrlData>> getPdfUrl({
    required String id,
  }) async {
    try {
      final response = await remoteDataSource.getPdfUrl(id: id);

      if (!response.success) {
        return Left(ServerFailure(
          message: response.message ?? 'Failed to get PDF URL',
        ));
      }

      return Right(response.data.toEntity());
    } on DioException catch (e) {
      return Left(_handleDioError(e));
    } catch (e) {
      return Left(GeneralFailure(message: e.toString()));
    }
  }

  @override
  Future<Either<Failure, StorageInfo>> getStorageInfo() async {
    try {
      // Try cache first
      final cached = await localDataSource.getCachedStorageInfo();
      if (cached != null) {
        return Right(cached.toEntity());
      }

      // Fetch from remote
      final response = await remoteDataSource.getStorageInfo();

      if (!response.success) {
        return Left(ServerFailure(
          message: response.message ?? 'Failed to get storage info',
        ));
      }

      // Cache the result
      await localDataSource.cacheStorageInfo(response.data);

      return Right(response.data.toEntity());
    } on DioException catch (e) {
      return Left(_handleDioError(e));
    } on CacheException catch (e) {
      return Left(CacheFailure(message: e.message));
    } catch (e) {
      return Left(GeneralFailure(message: e.toString()));
    }
  }

  /// Handle Dio errors and convert to Failures
  Failure _handleDioError(DioException error) {
    switch (error.type) {
      case DioExceptionType.connectionTimeout:
      case DioExceptionType.sendTimeout:
      case DioExceptionType.receiveTimeout:
        return const TimeoutFailure(
          message: AppConstants.errorTimeout,
        );

      case DioExceptionType.badResponse:
        final statusCode = error.response?.statusCode;
        final message = _extractErrorMessage(error.response?.data);

        if (statusCode == 401) {
          return AuthenticationFailure(
            message: message ?? AppConstants.errorUnauthorized,
            statusCode: statusCode,
          );
        } else if (statusCode == 403) {
          return AuthorizationFailure(
            message: message ?? AppConstants.errorForbidden,
            statusCode: statusCode,
          );
        } else if (statusCode == 404) {
          return NotFoundFailure(
            message: message ?? AppConstants.errorNotFound,
            statusCode: statusCode,
          );
        } else if (statusCode == 422) {
          final errors = _extractValidationErrors(error.response?.data);
          return ValidationFailure(
            message: message ?? AppConstants.errorValidation,
            statusCode: statusCode,
            errors: errors,
          );
        } else if (statusCode != null && statusCode >= 500) {
          return ServerFailure(
            message: message ?? AppConstants.errorServer,
            statusCode: statusCode,
          );
        }

        return ServerFailure(
          message: message ?? AppConstants.errorGeneric,
          statusCode: statusCode,
        );

      case DioExceptionType.connectionError:
      case DioExceptionType.unknown:
        return const NetworkFailure(
          message: AppConstants.errorNetwork,
        );

      default:
        return GeneralFailure(
          message: error.message ?? AppConstants.errorGeneric,
        );
    }
  }

  /// Extract error message from response
  String? _extractErrorMessage(dynamic data) {
    if (data == null) return null;

    if (data is Map<String, dynamic>) {
      return data['message'] as String? ??
          data['error'] as String? ??
          data['detail'] as String?;
    }

    return null;
  }

  /// Extract validation errors from response
  Map<String, List<String>>? _extractValidationErrors(dynamic data) {
    if (data == null) return null;

    if (data is Map<String, dynamic>) {
      final errors = data['errors'] as Map<String, dynamic>?;
      if (errors == null) return null;

      return errors.map(
        (key, value) => MapEntry(
          key,
          (value is List)
              ? value.map((e) => e.toString()).toList()
              : [value.toString()],
        ),
      );
    }

    return null;
  }
}
