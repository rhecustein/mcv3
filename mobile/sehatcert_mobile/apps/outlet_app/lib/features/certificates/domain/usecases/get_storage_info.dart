import 'package:dartz/dartz.dart';
import 'package:core/core.dart';
import 'package:domain/domain.dart';

import '../repositories/certificate_repository.dart';

/// Use case for getting storage information
class GetStorageInfo extends UseCase<StorageInfo, NoParams> {
  GetStorageInfo(this.repository);

  final CertificateRepository repository;

  @override
  Future<Either<Failure, StorageInfo>> call(NoParams params) {
    return repository.getStorageInfo();
  }
}
