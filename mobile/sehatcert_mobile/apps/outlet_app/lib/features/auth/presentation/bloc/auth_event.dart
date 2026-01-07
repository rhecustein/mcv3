part of 'auth_bloc.dart';

/// Authentication events
@freezed
class AuthEvent with _$AuthEvent {
  /// Check if user is authenticated
  const factory AuthEvent.checkAuthStatus() = _CheckAuthStatus;

  /// Login requested
  const factory AuthEvent.loginRequested({
    required String email,
    required String password,
  }) = _LoginRequested;

  /// Logout requested
  const factory AuthEvent.logoutRequested() = _LogoutRequested;

  /// Get current user
  const factory AuthEvent.getCurrentUser() = _GetCurrentUser;
}
