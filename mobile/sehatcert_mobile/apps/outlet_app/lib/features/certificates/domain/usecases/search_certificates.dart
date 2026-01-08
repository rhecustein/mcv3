import 'package:dartz/dartz.dart';
import 'package:core/core.dart';
import 'package:domain/domain.dart';
import 'package:equatable/equatable.dart';

import '../entities/certificate.dart';
import '../repositories/certificate_repository.dart';

/// Use case for searching certificates
class SearchCertificates
    extends UseCase<List<Certificate>, SearchCertificatesParams> {
  SearchCertificates(this.repository);

  final CertificateRepository repository;

  @override
  Future<Either<Failure, List<Certificate>>> call(
      SearchCertificatesParams params) {
    return repository.searchCertificates(query: params.query);
  }
}

/// Parameters for SearchCertificates use case
class SearchCertificatesParams extends Equatable {
  const SearchCertificatesParams({required this.query});

  final String query;

  @override
  List<Object?> get props => [query];
}
