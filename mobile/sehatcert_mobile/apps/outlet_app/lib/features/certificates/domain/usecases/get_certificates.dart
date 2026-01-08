import 'package:dartz/dartz.dart';
import 'package:core/core.dart';
import 'package:domain/domain.dart';
import 'package:equatable/equatable.dart';

import '../entities/certificate.dart';
import '../repositories/certificate_repository.dart';

/// Use case for getting paginated certificates
class GetCertificates
    extends UseCase<PaginatedData<Certificate>, GetCertificatesParams> {
  GetCertificates(this.repository);

  final CertificateRepository repository;

  @override
  Future<Either<Failure, PaginatedData<Certificate>>> call(
      GetCertificatesParams params) {
    return repository.getCertificates(
      page: params.page,
      perPage: params.perPage,
      search: params.search,
    );
  }
}

/// Parameters for GetCertificates use case
class GetCertificatesParams extends Equatable {
  const GetCertificatesParams({
    this.page = 1,
    this.perPage = 20,
    this.search,
  });

  final int page;
  final int perPage;
  final String? search;

  @override
  List<Object?> get props => [page, perPage, search];

  GetCertificatesParams copyWith({
    int? page,
    int? perPage,
    String? search,
  }) {
    return GetCertificatesParams(
      page: page ?? this.page,
      perPage: perPage ?? this.perPage,
      search: search ?? this.search,
    );
  }
}
