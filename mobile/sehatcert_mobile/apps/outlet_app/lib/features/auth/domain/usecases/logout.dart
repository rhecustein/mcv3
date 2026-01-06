import 'package:dartz/dartz.dart';
import 'package:core/core.dart';
import 'package:domain/domain.dart';

import '../repositories/auth_repository.dart';

/// Use case for user logout
class Logout extends UseCase<void, NoParams> {
  Logout(this.repository);

  final AuthRepository repository;

  @override
  Future<Either<Failure, void>> call(NoParams params) {
    return repository.logout();
  }
}
