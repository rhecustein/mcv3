import 'package:dartz/dartz.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:mocktail/mocktail.dart';
import 'package:outlet_app/features/auth/domain/entities/user.dart';
import 'package:outlet_app/features/auth/domain/repositories/auth_repository.dart';
import 'package:outlet_app/features/auth/domain/usecases/login.dart';

// Mock repository
class MockAuthRepository extends Mock implements AuthRepository {}

void main() {
  late Login useCase;
  late MockAuthRepository mockRepository;

  setUp(() {
    mockRepository = MockAuthRepository();
    useCase = Login(mockRepository);
  });

  const tEmail = 'test@example.com';
  const tPassword = 'password123';
  const tUser = User(
    id: '1',
    name: 'John Doe',
    email: tEmail,
    tenantId: 'tenant-1',
    outletId: 'outlet-1',
    outletName: 'Outlet Test',
  );

  group('Login Use Case', () {
    test('should return User when login is successful', () async {
      // Arrange
      when(
        () => mockRepository.login(
          email: any(named: 'email'),
          password: any(named: 'password'),
        ),
      ).thenAnswer((_) async => const Right(tUser));

      // Act
      final result = await useCase(
        const LoginParams(email: tEmail, password: tPassword),
      );

      // Assert
      expect(result, const Right(tUser));
      verify(
        () => mockRepository.login(email: tEmail, password: tPassword),
      ).called(1);
      verifyNoMoreInteractions(mockRepository);
    });

    test('should return Failure when login fails', () async {
      // Arrange
      when(
        () => mockRepository.login(
          email: any(named: 'email'),
          password: any(named: 'password'),
        ),
      ).thenAnswer(
        (_) async => const Left(
          AuthenticationFailure(message: 'Invalid credentials'),
        ),
      );

      // Act
      final result = await useCase(
        const LoginParams(email: tEmail, password: tPassword),
      );

      // Assert
      expect(result.isLeft(), true);
      result.fold(
        (failure) {
          expect(failure, isA<AuthenticationFailure>());
          expect(failure.message, 'Invalid credentials');
        },
        (_) => fail('Should return failure'),
      );
    });
  });
}
