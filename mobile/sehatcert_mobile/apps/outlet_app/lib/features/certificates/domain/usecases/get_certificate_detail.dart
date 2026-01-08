import 'package:dartz/dartz.dart';
import 'package:core/core.dart';
import 'package:domain/domain.dart';
import 'package:equatable/equatable.dart';

import '../entities/certificate.dart';
import '../repositories/certificate_repository.dart';

/// Use case for getting certificate detail
class GetCertificateDetail
    extends UseCase<Certificate, GetCertificateDetailParams> {
  GetCertificateDetail(this.repository);

  final CertificateRepository repository;

  @override
  Future<Either<Failure, Certificate>> call(
      GetCertificateDetailParams params) {
    return repository.getCertificateDetail(id: params.id);
  }
}

/// Parameters for GetCertificateDetail use case
class GetCertificateDetailParams extends Equatable {
  const GetCertificateDetailParams({required this.id});

  final String id;

  @override
  List<Object?> get props => [id];
}
