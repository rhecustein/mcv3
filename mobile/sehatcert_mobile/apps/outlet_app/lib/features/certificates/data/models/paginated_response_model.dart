import 'package:freezed_annotation/freezed_annotation.dart';

import '../../domain/repositories/certificate_repository.dart';

part 'paginated_response_model.freezed.dart';
part 'paginated_response_model.g.dart';

/// Paginated API response model
@Freezed(genericArgumentFactories: true)
class PaginatedResponseModel<T> with _$PaginatedResponseModel<T> {
  const factory PaginatedResponseModel({
    required List<T> data,
    required MetaModel meta,
    required LinksModel links,
  }) = _PaginatedResponseModel<T>;

  const PaginatedResponseModel._();

  factory PaginatedResponseModel.fromJson(
    Map<String, dynamic> json,
    T Function(Object?) fromJsonT,
  ) =>
      _$PaginatedResponseModelFromJson(json, fromJsonT);

  /// Convert to domain PaginatedData
  PaginatedData<R> toEntity<R>(R Function(T) converter) {
    return PaginatedData<R>(
      data: data.map(converter).toList(),
      currentPage: meta.currentPage,
      total: meta.total,
      perPage: meta.perPage,
      lastPage: meta.lastPage,
      nextPageUrl: links.next,
      prevPageUrl: links.prev,
    );
  }
}

@freezed
class MetaModel with _$MetaModel {
  const factory MetaModel({
    @JsonKey(name: 'current_page') required int currentPage,
    required int total,
    @JsonKey(name: 'per_page') required int perPage,
    @JsonKey(name: 'last_page') required int lastPage,
  }) = _MetaModel;

  factory MetaModel.fromJson(Map<String, dynamic> json) =>
      _$MetaModelFromJson(json);
}

@freezed
class LinksModel with _$LinksModel {
  const factory LinksModel({
    String? first,
    String? last,
    String? prev,
    String? next,
  }) = _LinksModel;

  factory LinksModel.fromJson(Map<String, dynamic> json) =>
      _$LinksModelFromJson(json);
}
