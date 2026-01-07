import 'package:bloc_test/bloc_test.dart';
import 'package:dartz/dartz.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:mocktail/mocktail.dart';
import 'package:outlet_app/features/auth/domain/entities/user.dart';
import 'package:outlet_app/features/auth/domain/usecases/check_auth_status.dart';
import 'package:outlet_app/features/auth/domain/usecases/get_current_user.dart';
import 'package:outlet_app/features/auth/domain/usecases/login.dart';
import 'package:outlet_app/features/auth/domain/usecases/logout.dart';
import 'package:outlet_app/features/auth/presentation/bloc/auth_bloc.dart';
import 'package:core/core.dart';
import 'package:domain/domain.dart';

// Mocks
class MockLogin extends Mock implements Login {}

class MockLogout extends Mock implements Logout {}

class MockGetCurrentUser extends Mock implements GetCurrentUser {}

class MockCheckAuthStatus extends Mock implements CheckAuthStatus {}

// Fallback values
class FakeLoginParams extends Fake implements LoginParams {}

class FakeNoParams extends Fake implements NoParams {}

void main() {
  late AuthBloc authBloc;
  late MockLogin mockLogin;
  late MockLogout mockLogout;
  late MockGetCurrentUser mockGetCurrentUser;
  late MockCheckAuthStatus mockCheckAuthStatus;

  setUpAll(() {
    registerFallbackValue(FakeLoginParams());
    registerFallbackValue(FakeNoParams());
  });

  setUp(() {
    mockLogin = MockLogin();
    mockLogout = MockLogout();
    mockGetCurrentUser = MockGetCurrentUser();
    mockCheckAuthStatus = MockCheckAuthStatus();

    authBloc = AuthBloc(
      loginUseCase: mockLogin,
      logoutUseCase: mockLogout,
      getCurrentUserUseCase: mockGetCurrentUser,
      checkAuthStatusUseCase: mockCheckAuthStatus,
    );
  });

  tearDown(() {
    authBloc.close();
  });

  const tUser = User(
    id: '1',
    name: 'John Doe',
    email: 'john@example.com',
    tenantId: 'tenant-1',
    outletId: 'outlet-1',
  );

  const tEmail = 'test@example.com';
  const tPassword = 'password123';

  group('AuthBloc', () {
    test('initial state should be AuthState.initial()', () {
      expect(authBloc.state, const AuthState.initial());
    });

    group('CheckAuthStatus', () {
      blocTest<AuthBloc, AuthState>(
        'emits [checking, authenticated] when user is authenticated',
        build: () {
          when(() => mockCheckAuthStatus()).thenAnswer((_) async => true);
          when(() => mockGetCurrentUser(any()))
              .thenAnswer((_) async => const Right(tUser));
          return authBloc;
        },
        act: (bloc) => bloc.add(const AuthEvent.checkAuthStatus()),
        expect: () => [
          const AuthState.checking(),
          const AuthState.authenticated(tUser),
        ],
        verify: (_) {
          verify(() => mockCheckAuthStatus()).called(1);
          verify(() => mockGetCurrentUser(const NoParams())).called(1);
        },
      );

      blocTest<AuthBloc, AuthState>(
        'emits [checking, unauthenticated] when user is not authenticated',
        build: () {
          when(() => mockCheckAuthStatus()).thenAnswer((_) async => false);
          return authBloc;
        },
        act: (bloc) => bloc.add(const AuthEvent.checkAuthStatus()),
        expect: () => [
          const AuthState.checking(),
          const AuthState.unauthenticated(),
        ],
        verify: (_) {
          verify(() => mockCheckAuthStatus()).called(1);
          verifyNever(() => mockGetCurrentUser(any()));
        },
      );

      blocTest<AuthBloc, AuthState>(
        'emits [checking, unauthenticated] when getting current user fails',
        build: () {
          when(() => mockCheckAuthStatus()).thenAnswer((_) async => true);
          when(() => mockGetCurrentUser(any())).thenAnswer(
            (_) async => const Left(
              ServerFailure(message: 'Failed to get user'),
            ),
          );
          return authBloc;
        },
        act: (bloc) => bloc.add(const AuthEvent.checkAuthStatus()),
        expect: () => [
          const AuthState.checking(),
          const AuthState.unauthenticated(),
        ],
      );
    });

    group('LoginRequested', () {
      blocTest<AuthBloc, AuthState>(
        'emits [loading, authenticated] when login is successful',
        build: () {
          when(() => mockLogin(any()))
              .thenAnswer((_) async => const Right(tUser));
          return authBloc;
        },
        act: (bloc) => bloc.add(
          const AuthEvent.loginRequested(
            email: tEmail,
            password: tPassword,
          ),
        ),
        expect: () => [
          const AuthState.loading(),
          const AuthState.authenticated(tUser),
        ],
        verify: (_) {
          verify(
            () => mockLogin(
              const LoginParams(email: tEmail, password: tPassword),
            ),
          ).called(1);
        },
      );

      blocTest<AuthBloc, AuthState>(
        'emits [loading, error] when login fails',
        build: () {
          when(() => mockLogin(any())).thenAnswer(
            (_) async => const Left(
              AuthenticationFailure(message: 'Invalid credentials'),
            ),
          );
          return authBloc;
        },
        act: (bloc) => bloc.add(
          const AuthEvent.loginRequested(
            email: tEmail,
            password: tPassword,
          ),
        ),
        expect: () => [
          const AuthState.loading(),
          const AuthState.error('Invalid credentials'),
        ],
      );

      blocTest<AuthBloc, AuthState>(
        'emits [loading, error] with generic message when failure has no message',
        build: () {
          when(() => mockLogin(any())).thenAnswer(
            (_) async => const Left(ServerFailure()),
          );
          return authBloc;
        },
        act: (bloc) => bloc.add(
          const AuthEvent.loginRequested(
            email: tEmail,
            password: tPassword,
          ),
        ),
        expect: () => [
          const AuthState.loading(),
          const AuthState.error('Login failed'),
        ],
      );
    });

    group('LogoutRequested', () {
      blocTest<AuthBloc, AuthState>(
        'emits [loading, unauthenticated] when logout is successful',
        build: () {
          when(() => mockLogout(any()))
              .thenAnswer((_) async => const Right(null));
          return authBloc;
        },
        act: (bloc) => bloc.add(const AuthEvent.logoutRequested()),
        expect: () => [
          const AuthState.loading(),
          const AuthState.unauthenticated(),
        ],
        verify: (_) {
          verify(() => mockLogout(const NoParams())).called(1);
        },
      );

      blocTest<AuthBloc, AuthState>(
        'emits [loading, error] when logout fails',
        build: () {
          when(() => mockLogout(any())).thenAnswer(
            (_) async => const Left(ServerFailure(message: 'Logout failed')),
          );
          return authBloc;
        },
        act: (bloc) => bloc.add(const AuthEvent.logoutRequested()),
        expect: () => [
          const AuthState.loading(),
          const AuthState.error('Logout failed'),
        ],
      );
    });

    group('GetCurrentUser', () {
      blocTest<AuthBloc, AuthState>(
        'emits [authenticated] when getting user is successful',
        build: () {
          when(() => mockGetCurrentUser(any()))
              .thenAnswer((_) async => const Right(tUser));
          return authBloc;
        },
        act: (bloc) => bloc.add(const AuthEvent.getCurrentUser()),
        expect: () => [
          const AuthState.authenticated(tUser),
        ],
      );

      blocTest<AuthBloc, AuthState>(
        'emits [error] when getting user fails',
        build: () {
          when(() => mockGetCurrentUser(any())).thenAnswer(
            (_) async => const Left(
              ServerFailure(message: 'Failed to get user'),
            ),
          );
          return authBloc;
        },
        act: (bloc) => bloc.add(const AuthEvent.getCurrentUser()),
        expect: () => [
          const AuthState.error('Failed to get user'),
        ],
      );
    });
  });
}
