import 'package:freezed_annotation/freezed_annotation.dart';

import '../../domain/entities/user.dart';

part 'user_model.freezed.dart';
part 'user_model.g.dart';

/// Data model for User
@freezed
class UserModel with _$UserModel {
  const factory UserModel({
    required String id,
    required String name,
    required String email,
    @JsonKey(name: 'tenant_id') required String tenantId,
    @JsonKey(name: 'outlet_id') required String outletId,
    @JsonKey(name: 'outlet_name') String? outletName,
    String? phone,
    String? avatar,
    String? role,
  }) = _UserModel;

  const UserModel._();

  factory UserModel.fromJson(Map<String, dynamic> json) =>
      _$UserModelFromJson(json);

  /// Convert model to domain entity
  User toEntity() {
    return User(
      id: id,
      name: name,
      email: email,
      tenantId: tenantId,
      outletId: outletId,
      outletName: outletName,
      phone: phone,
      avatar: avatar,
      role: role,
    );
  }

  /// Create model from domain entity
  factory UserModel.fromEntity(User entity) {
    return UserModel(
      id: entity.id,
      name: entity.name,
      email: entity.email,
      tenantId: entity.tenantId,
      outletId: entity.outletId,
      outletName: entity.outletName,
      phone: entity.phone,
      avatar: entity.avatar,
      role: entity.role,
    );
  }
}
