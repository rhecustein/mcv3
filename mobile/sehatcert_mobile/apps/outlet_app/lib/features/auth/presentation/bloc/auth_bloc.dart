import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:freezed_annotation/freezed_annotation.dart';
import 'package:domain/domain.dart';

import '../../domain/entities/user.dart';
import '../../domain/usecases/check_auth_status.dart';
import '../../domain/usecases/get_current_user.dart';
import '../../domain/usecases/login.dart';
import '../../domain/usecases/logout.dart';

part 'auth_event.dart';
part 'auth_state.dart';
part 'auth_bloc.freezed.dart';

/// BLoC for authentication
class AuthBloc extends Bloc<AuthEvent, AuthState> {
  AuthBloc({
    required this.loginUseCase,
    required this.logoutUseCase,
    required this.getCurrentUserUseCase,
    required this.checkAuthStatusUseCase,
  }) : super(const AuthState.initial()) {
    on<_CheckAuthStatus>(_onCheckAuthStatus);
    on<_LoginRequested>(_onLoginRequested);
    on<_LogoutRequested>(_onLogoutRequested);
    on<_GetCurrentUser>(_onGetCurrentUser);
  }

  final Login loginUseCase;
  final Logout logoutUseCase;
  final GetCurrentUser getCurrentUserUseCase;
  final CheckAuthStatus checkAuthStatusUseCase;

  /// Check authentication status
  Future<void> _onCheckAuthStatus(
    _CheckAuthStatus event,
    Emitter<AuthState> emit,
  ) async {
    emit(const AuthState.checking());

    final isAuthenticated = await checkAuthStatusUseCase();

    if (isAuthenticated) {
      // Get current user
      final result = await getCurrentUserUseCase(const NoParams());

      result.fold(
        (failure) => emit(const AuthState.unauthenticated()),
        (user) => emit(AuthState.authenticated(user)),
      );
    } else {
      emit(const AuthState.unauthenticated());
    }
  }

  /// Handle login
  Future<void> _onLoginRequested(
    _LoginRequested event,
    Emitter<AuthState> emit,
  ) async {
    emit(const AuthState.loading());

    final result = await loginUseCase(
      LoginParams(
        email: event.email,
        password: event.password,
      ),
    );

    result.fold(
      (failure) => emit(
        AuthState.error(failure.message ?? 'Login failed'),
      ),
      (user) => emit(AuthState.authenticated(user)),
    );
  }

  /// Handle logout
  Future<void> _onLogoutRequested(
    _LogoutRequested event,
    Emitter<AuthState> emit,
  ) async {
    emit(const AuthState.loading());

    final result = await logoutUseCase(const NoParams());

    result.fold(
      (failure) => emit(
        AuthState.error(failure.message ?? 'Logout failed'),
      ),
      (_) => emit(const AuthState.unauthenticated()),
    );
  }

  /// Get current user
  Future<void> _onGetCurrentUser(
    _GetCurrentUser event,
    Emitter<AuthState> emit,
  ) async {
    final result = await getCurrentUserUseCase(const NoParams());

    result.fold(
      (failure) => emit(
        AuthState.error(failure.message ?? 'Failed to get user'),
      ),
      (user) => emit(AuthState.authenticated(user)),
    );
  }
}
