import 'package:dartz/dartz.dart';
import 'package:core/core.dart';
import 'package:domain/domain.dart';

import '../entities/user.dart';
import '../repositories/auth_repository.dart';

/// Use case to get current authenticated user
class GetCurrentUser extends UseCase<User, NoParams> {
  GetCurrentUser(this.repository);

  final AuthRepository repository;

  @override
  Future<Either<Failure, User>> call(NoParams params) {
    return repository.getCurrentUser();
  }
}
