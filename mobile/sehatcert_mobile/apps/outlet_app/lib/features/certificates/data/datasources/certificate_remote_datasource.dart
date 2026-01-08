import 'package:dio/dio.dart';
import 'package:retrofit/retrofit.dart';

import '../models/certificate_model.dart';
import '../models/paginated_response_model.dart';
import '../models/pdf_url_data_model.dart';
import '../models/storage_info_model.dart';

part 'certificate_remote_datasource.g.dart';

/// Remote data source for certificates
@RestApi()
abstract class CertificateRemoteDataSource {
  factory CertificateRemoteDataSource(Dio dio, {String baseUrl}) =
      _CertificateRemoteDataSource;

  /// Get paginated list of certificates
  @GET('/api/v1/certificates')
  Future<PaginatedResponseModel<CertificateModel>> getCertificates({
    @Query('page') int? page,
    @Query('per_page') int? perPage,
    @Query('search') String? search,
  });

  /// Search certificates
  @GET('/api/v1/certificates/search')
  Future<ApiResponse<List<CertificateModel>>> searchCertificates({
    @Query('q') required String query,
  });

  /// Get certificate detail
  @GET('/api/v1/certificates/{id}')
  Future<ApiResponse<CertificateModel>> getCertificateDetail({
    @Path('id') required String id,
  });

  /// Download certificate PDF (returns file bytes)
  @GET('/api/v1/certificates/{id}/download')
  Future<HttpResponse<List<int>>> downloadCertificate({
    @Path('id') required String id,
  });

  /// Get PDF URL for preview
  @GET('/api/v1/certificates/{id}/pdf-url')
  Future<ApiResponse<PdfUrlDataModel>> getPdfUrl({
    @Path('id') required String id,
  });

  /// Get storage information
  @GET('/api/v1/certificates/storage/info')
  Future<ApiResponse<StorageInfoModel>> getStorageInfo();
}

/// Generic API response wrapper
class ApiResponse<T> {
  ApiResponse({
    required this.success,
    required this.data,
    this.message,
  });

  final bool success;
  final T data;
  final String? message;

  factory ApiResponse.fromJson(
    Map<String, dynamic> json,
    T Function(Object?) fromJsonT,
  ) {
    return ApiResponse<T>(
      success: json['success'] as bool? ?? true,
      data: fromJsonT(json['data']),
      message: json['message'] as String?,
    );
  }
}
