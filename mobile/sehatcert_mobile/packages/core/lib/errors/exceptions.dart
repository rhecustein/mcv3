/// Base exception class
class AppException implements Exception {
  AppException({this.message, this.statusCode});

  final String? message;
  final int? statusCode;

  @override
  String toString() => 'AppException(message: $message, statusCode: $statusCode)';
}

/// Server exception
class ServerException extends AppException {
  ServerException({super.message, super.statusCode});

  @override
  String toString() => 'ServerException(message: $message, statusCode: $statusCode)';
}

/// Cache exception
class CacheException extends AppException {
  CacheException({super.message});

  @override
  String toString() => 'CacheException(message: $message)';
}

/// Network exception
class NetworkException extends AppException {
  NetworkException({super.message});

  @override
  String toString() => 'NetworkException(message: $message)';
}

/// Validation exception
class ValidationException extends AppException {
  ValidationException({super.message, super.statusCode, this.errors});

  final Map<String, List<String>>? errors;

  @override
  String toString() =>
      'ValidationException(message: $message, errors: $errors)';
}

/// Authentication exception
class AuthenticationException extends AppException {
  AuthenticationException({super.message, super.statusCode});

  @override
  String toString() =>
      'AuthenticationException(message: $message, statusCode: $statusCode)';
}

/// Authorization exception
class AuthorizationException extends AppException {
  AuthorizationException({super.message, super.statusCode});

  @override
  String toString() =>
      'AuthorizationException(message: $message, statusCode: $statusCode)';
}

/// Not found exception
class NotFoundException extends AppException {
  NotFoundException({super.message, super.statusCode});

  @override
  String toString() =>
      'NotFoundException(message: $message, statusCode: $statusCode)';
}

/// Timeout exception
class TimeoutException extends AppException {
  TimeoutException({super.message});

  @override
  String toString() => 'TimeoutException(message: $message)';
}

/// Quota exceeded exception
class QuotaExceededException extends AppException {
  QuotaExceededException({
    super.message,
    super.statusCode,
    this.quotaType,
    this.current,
    this.limit,
  });

  final String? quotaType;
  final int? current;
  final int? limit;

  @override
  String toString() =>
      'QuotaExceededException(quotaType: $quotaType, current: $current, limit: $limit)';
}
