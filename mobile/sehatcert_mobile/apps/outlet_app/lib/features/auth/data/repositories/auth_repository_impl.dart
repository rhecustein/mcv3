import 'package:dartz/dartz.dart';
import 'package:dio/dio.dart';
import 'package:core/core.dart';

import '../../domain/entities/user.dart';
import '../../domain/repositories/auth_repository.dart';
import '../datasources/auth_local_datasource.dart';
import '../datasources/auth_remote_datasource.dart';
import '../models/login_request.dart';

/// Implementation of AuthRepository
class AuthRepositoryImpl implements AuthRepository {
  AuthRepositoryImpl({
    required this.remoteDataSource,
    required this.localDataSource,
  });

  final AuthRemoteDataSource remoteDataSource;
  final AuthLocalDataSource localDataSource;

  @override
  Future<Either<Failure, User>> login({
    required String email,
    required String password,
  }) async {
    try {
      // Call remote API
      final response = await remoteDataSource.login(
        LoginRequest(email: email, password: password),
      );

      // Save tokens locally
      await localDataSource.saveAccessToken(response.accessToken);
      if (response.refreshToken != null) {
        await localDataSource.saveRefreshToken(response.refreshToken!);
      }

      // Save user data locally
      await localDataSource.saveUser(response.user);

      // Return user entity
      return Right(response.user.toEntity());
    } on DioException catch (e) {
      return Left(_handleDioError(e));
    } on CacheException catch (e) {
      return Left(CacheFailure(message: e.message));
    } catch (e) {
      return Left(GeneralFailure(message: e.toString()));
    }
  }

  @override
  Future<Either<Failure, void>> logout() async {
    try {
      // Call remote API (best effort - ignore failures)
      try {
        await remoteDataSource.logout();
      } catch (_) {
        // Ignore remote logout failures
      }

      // Clear local data
      await localDataSource.clearAuthData();

      return const Right(null);
    } on CacheException catch (e) {
      return Left(CacheFailure(message: e.message));
    } catch (e) {
      return Left(GeneralFailure(message: e.toString()));
    }
  }

  @override
  Future<Either<Failure, User>> getCurrentUser() async {
    try {
      // First try to get from local cache
      final cachedUser = await localDataSource.getUser();
      if (cachedUser != null) {
        return Right(cachedUser.toEntity());
      }

      // If not in cache, fetch from remote
      final user = await remoteDataSource.getCurrentUser();

      // Save to cache
      await localDataSource.saveUser(user);

      return Right(user.toEntity());
    } on DioException catch (e) {
      return Left(_handleDioError(e));
    } on CacheException catch (e) {
      return Left(CacheFailure(message: e.message));
    } catch (e) {
      return Left(GeneralFailure(message: e.toString()));
    }
  }

  @override
  Future<bool> isAuthenticated() async {
    return localDataSource.isAuthenticated();
  }

  @override
  Future<String?> getAccessToken() async {
    return localDataSource.getAccessToken();
  }

  @override
  Future<String?> getRefreshToken() async {
    return localDataSource.getRefreshToken();
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
          // Validation error
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
