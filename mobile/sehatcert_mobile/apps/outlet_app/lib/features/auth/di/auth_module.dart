import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:injectable/injectable.dart';

import '../data/datasources/auth_local_datasource.dart';
import '../data/datasources/auth_remote_datasource.dart';
import '../data/repositories/auth_repository_impl.dart';
import '../domain/repositories/auth_repository.dart';
import '../domain/usecases/check_auth_status.dart';
import '../domain/usecases/get_current_user.dart';
import '../domain/usecases/login.dart';
import '../domain/usecases/logout.dart';
import '../presentation/bloc/auth_bloc.dart';

/// Dependency injection module for Auth feature
@module
abstract class AuthModule {
  // Data sources
  @lazySingleton
  AuthRemoteDataSource provideAuthRemoteDataSource(Dio dio) {
    return AuthRemoteDataSource(dio);
  }

  @lazySingleton
  AuthLocalDataSource provideAuthLocalDataSource(
    FlutterSecureStorage secureStorage,
  ) {
    return AuthLocalDataSource(secureStorage);
  }

  // Repository
  @lazySingleton
  AuthRepository provideAuthRepository(
    AuthRemoteDataSource remoteDataSource,
    AuthLocalDataSource localDataSource,
  ) {
    return AuthRepositoryImpl(
      remoteDataSource: remoteDataSource,
      localDataSource: localDataSource,
    );
  }

  // Use cases
  @lazySingleton
  Login provideLogin(AuthRepository repository) {
    return Login(repository);
  }

  @lazySingleton
  Logout provideLogout(AuthRepository repository) {
    return Logout(repository);
  }

  @lazySingleton
  GetCurrentUser provideGetCurrentUser(AuthRepository repository) {
    return GetCurrentUser(repository);
  }

  @lazySingleton
  CheckAuthStatus provideCheckAuthStatus(AuthRepository repository) {
    return CheckAuthStatus(repository);
  }

  // BLoC
  @injectable
  AuthBloc provideAuthBloc(
    Login loginUseCase,
    Logout logoutUseCase,
    GetCurrentUser getCurrentUserUseCase,
    CheckAuthStatus checkAuthStatusUseCase,
  ) {
    return AuthBloc(
      loginUseCase: loginUseCase,
      logoutUseCase: logoutUseCase,
      getCurrentUserUseCase: getCurrentUserUseCase,
      checkAuthStatusUseCase: checkAuthStatusUseCase,
    );
  }
}
