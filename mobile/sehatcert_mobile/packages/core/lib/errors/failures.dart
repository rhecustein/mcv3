import 'package:equatable/equatable.dart';

/// Base class for all failures in the app
abstract class Failure extends Equatable {
  const Failure({this.message, this.statusCode});

  final String? message;
  final int? statusCode;

  @override
  List<Object?> get props => [message, statusCode];
}

/// Server-related failures
class ServerFailure extends Failure {
  const ServerFailure({super.message, super.statusCode});

  @override
  String toString() => 'ServerFailure(message: $message, statusCode: $statusCode)';
}

/// Cache-related failures
class CacheFailure extends Failure {
  const CacheFailure({super.message});

  @override
  String toString() => 'CacheFailure(message: $message)';
}

/// Network-related failures
class NetworkFailure extends Failure {
  const NetworkFailure({super.message});

  @override
  String toString() => 'NetworkFailure(message: $message)';
}

/// Validation-related failures
class ValidationFailure extends Failure {
  const ValidationFailure({super.message, this.errors});

  final Map<String, List<String>>? errors;

  @override
  List<Object?> get props => [message, statusCode, errors];

  @override
  String toString() => 'ValidationFailure(message: $message, errors: $errors)';
}

/// Authentication-related failures
class AuthenticationFailure extends Failure {
  const AuthenticationFailure({super.message, super.statusCode});

  @override
  String toString() => 'AuthenticationFailure(message: $message)';
}

/// Authorization-related failures
class AuthorizationFailure extends Failure {
  const AuthorizationFailure({super.message, super.statusCode});

  @override
  String toString() => 'AuthorizationFailure(message: $message)';
}

/// Not found failures
class NotFoundFailure extends Failure {
  const NotFoundFailure({super.message, super.statusCode});

  @override
  String toString() => 'NotFoundFailure(message: $message)';
}

/// Timeout failures
class TimeoutFailure extends Failure {
  const TimeoutFailure({super.message});

  @override
  String toString() => 'TimeoutFailure(message: $message)';
}

/// Generic failures
class GeneralFailure extends Failure {
  const GeneralFailure({super.message, super.statusCode});

  @override
  String toString() => 'GeneralFailure(message: $message, statusCode: $statusCode)';
}

/// Quota exceeded failures
class QuotaExceededFailure extends Failure {
  const QuotaExceededFailure({
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
  List<Object?> get props => [message, statusCode, quotaType, current, limit];

  @override
  String toString() =>
      'QuotaExceededFailure(quotaType: $quotaType, current: $current, limit: $limit, message: $message)';
}
