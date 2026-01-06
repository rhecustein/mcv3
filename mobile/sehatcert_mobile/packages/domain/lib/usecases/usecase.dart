import 'package:dartz/dartz.dart';
import 'package:core/core.dart';

/// Base class for all use cases
///
/// [Type] is the return type
/// [Params] is the parameters type
abstract class UseCase<Type, Params> {
  /// Execute the use case
  Future<Either<Failure, Type>> call(Params params);
}

/// Use case with no parameters
class NoParams {
  const NoParams();
}
