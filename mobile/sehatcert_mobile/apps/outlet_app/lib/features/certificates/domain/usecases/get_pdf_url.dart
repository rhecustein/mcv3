import 'package:dartz/dartz.dart';
import 'package:core/core.dart';
import 'package:domain/domain.dart';
import 'package:equatable/equatable.dart';

import '../repositories/certificate_repository.dart';

/// Use case for getting PDF URL
class GetPdfUrl extends UseCase<PdfUrlData, GetPdfUrlParams> {
  GetPdfUrl(this.repository);

  final CertificateRepository repository;

  @override
  Future<Either<Failure, PdfUrlData>> call(GetPdfUrlParams params) {
    return repository.getPdfUrl(id: params.id);
  }
}

/// Parameters for GetPdfUrl use case
class GetPdfUrlParams extends Equatable {
  const GetPdfUrlParams({required this.id});

  final String id;

  @override
  List<Object?> get props => [id];
}
