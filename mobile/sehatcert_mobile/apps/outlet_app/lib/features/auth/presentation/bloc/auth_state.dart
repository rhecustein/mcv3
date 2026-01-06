part of 'auth_bloc.dart';

/// Authentication states
@freezed
class AuthState with _$AuthState {
  /// Initial state
  const factory AuthState.initial() = _Initial;

  /// Checking authentication status
  const factory AuthState.checking() = _Checking;

  /// Loading (login/logout in progress)
  const factory AuthState.loading() = _Loading;

  /// User is authenticated
  const factory AuthState.authenticated(User user) = _Authenticated;

  /// User is not authenticated
  const factory AuthState.unauthenticated() = _Unauthenticated;

  /// Error occurred
  const factory AuthState.error(String message) = _Error;
}
