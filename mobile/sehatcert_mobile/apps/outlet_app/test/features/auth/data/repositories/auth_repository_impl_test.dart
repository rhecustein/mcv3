import 'package:dartz/dartz.dart';
import 'package:dio/dio.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:mocktail/mocktail.dart';
import 'package:outlet_app/features/auth/data/datasources/auth_local_datasource.dart';
import 'package:outlet_app/features/auth/data/datasources/auth_remote_datasource.dart';
import 'package:outlet_app/features/auth/data/models/login_request.dart';
import 'package:outlet_app/features/auth/data/models/login_response.dart';
import 'package:outlet_app/features/auth/data/models/user_model.dart';
import 'package:outlet_app/features/auth/data/repositories/auth_repository_impl.dart';
import 'package:core/core.dart';

// Mocks
class MockAuthRemoteDataSource extends Mock implements AuthRemoteDataSource {}

class MockAuthLocalDataSource extends Mock implements AuthLocalDataSource {}

// Fallback values for Mocktail
class FakeLoginRequest extends Fake implements LoginRequest {}

class FakeUserModel extends Fake implements UserModel {}

void main() {
  late AuthRepositoryImpl repository;
  late MockAuthRemoteDataSource mockRemoteDataSource;
  late MockAuthLocalDataSource mockLocalDataSource;

  setUpAll(() {
    registerFallbackValue(FakeLoginRequest());
    registerFallbackValue(FakeUserModel());
  });

  setUp(() {
    mockRemoteDataSource = MockAuthRemoteDataSource();
    mockLocalDataSource = MockAuthLocalDataSource();
    repository = AuthRepositoryImpl(
      remoteDataSource: mockRemoteDataSource,
      localDataSource: mockLocalDataSource,
    );
  });

  const tEmail = 'test@example.com';
  const tPassword = 'password123';
  const tAccessToken = 'access_token_123';
  const tUserModel = UserModel(
    id: '1',
    name: 'John Doe',
    email: tEmail,
    tenantId: 'tenant-1',
    outletId: 'outlet-1',
    outletName: 'Outlet Test',
  );
  const tLoginResponse = LoginResponse(
    accessToken: tAccessToken,
    refreshToken: 'refresh_token_123',
    user: tUserModel,
  );

  group('login', () {
    test('should return User when login is successful', () async {
      // Arrange
      when(() => mockRemoteDataSource.login(any()))
          .thenAnswer((_) async => tLoginResponse);
      when(() => mockLocalDataSource.saveAccessToken(any()))
          .thenAnswer((_) async => Future.value());
      when(() => mockLocalDataSource.saveRefreshToken(any()))
          .thenAnswer((_) async => Future.value());
      when(() => mockLocalDataSource.saveUser(any()))
          .thenAnswer((_) async => Future.value());

      // Act
      final result = await repository.login(
        email: tEmail,
        password: tPassword,
      );

      // Assert
      expect(result.isRight(), true);
      result.fold(
        (failure) => fail('Should return user'),
        (user) {
          expect(user.id, tUserModel.id);
          expect(user.email, tUserModel.email);
          expect(user.name, tUserModel.name);
        },
      );

      // Verify interactions
      verify(() => mockRemoteDataSource.login(any())).called(1);
      verify(() => mockLocalDataSource.saveAccessToken(tAccessToken))
          .called(1);
      verify(() => mockLocalDataSource.saveUser(tUserModel)).called(1);
    });

    test('should return AuthenticationFailure when credentials are invalid',
        () async {
      // Arrange
      when(() => mockRemoteDataSource.login(any())).thenThrow(
        DioException(
          requestOptions: RequestOptions(path: '/auth/login'),
          response: Response(
            requestOptions: RequestOptions(path: '/auth/login'),
            statusCode: 401,
            data: {'message': 'Invalid credentials'},
          ),
        ),
      );

      // Act
      final result = await repository.login(
        email: tEmail,
        password: tPassword,
      );

      // Assert
      expect(result.isLeft(), true);
      result.fold(
        (failure) {
          expect(failure, isA<AuthenticationFailure>());
          expect(failure.statusCode, 401);
        },
        (_) => fail('Should return failure'),
      );
    });

    test('should return NetworkFailure when there is no internet connection',
        () async {
      // Arrange
      when(() => mockRemoteDataSource.login(any())).thenThrow(
        DioException(
          requestOptions: RequestOptions(path: '/auth/login'),
          type: DioExceptionType.connectionError,
        ),
      );

      // Act
      final result = await repository.login(
        email: tEmail,
        password: tPassword,
      );

      // Assert
      expect(result.isLeft(), true);
      result.fold(
        (failure) => expect(failure, isA<NetworkFailure>()),
        (_) => fail('Should return failure'),
      );
    });
  });

  group('logout', () {
    test('should clear local data when logout is successful', () async {
      // Arrange
      when(() => mockRemoteDataSource.logout())
          .thenAnswer((_) async => Future.value());
      when(() => mockLocalDataSource.clearAuthData())
          .thenAnswer((_) async => Future.value());

      // Act
      final result = await repository.logout();

      // Assert
      expect(result.isRight(), true);
      verify(() => mockLocalDataSource.clearAuthData()).called(1);
    });

    test('should still clear local data when remote logout fails', () async {
      // Arrange
      when(() => mockRemoteDataSource.logout()).thenThrow(
        DioException(
          requestOptions: RequestOptions(path: '/auth/logout'),
        ),
      );
      when(() => mockLocalDataSource.clearAuthData())
          .thenAnswer((_) async => Future.value());

      // Act
      final result = await repository.logout();

      // Assert
      expect(result.isRight(), true);
      verify(() => mockLocalDataSource.clearAuthData()).called(1);
    });
  });

  group('isAuthenticated', () {
    test('should return true when user has access token', () async {
      // Arrange
      when(() => mockLocalDataSource.isAuthenticated())
          .thenAnswer((_) async => true);

      // Act
      final result = await repository.isAuthenticated();

      // Assert
      expect(result, true);
    });

    test('should return false when user has no access token', () async {
      // Arrange
      when(() => mockLocalDataSource.isAuthenticated())
          .thenAnswer((_) async => false);

      // Act
      final result = await repository.isAuthenticated();

      // Assert
      expect(result, false);
    });
  });
}
