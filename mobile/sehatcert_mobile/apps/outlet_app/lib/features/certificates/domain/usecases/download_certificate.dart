import 'package:dartz/dartz.dart';
import 'package:core/core.dart';
import 'package:domain/domain.dart';
import 'package:equatable/equatable.dart';

import '../repositories/certificate_repository.dart';

/// Use case for downloading certificate PDF
class DownloadCertificate extends UseCase<String, DownloadCertificateParams> {
  DownloadCertificate(this.repository);

  final CertificateRepository repository;

  @override
  Future<Either<Failure, String>> call(DownloadCertificateParams params) {
    return repository.downloadCertificate(id: params.id);
  }
}

/// Parameters for DownloadCertificate use case
class DownloadCertificateParams extends Equatable {
  const DownloadCertificateParams({required this.id});

  final String id;

  @override
  List<Object?> get props => [id];
}
