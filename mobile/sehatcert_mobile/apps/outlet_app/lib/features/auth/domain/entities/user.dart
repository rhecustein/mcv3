import 'package:equatable/equatable.dart';

/// User entity representing an authenticated user
class User extends Equatable {
  const User({
    required this.id,
    required this.name,
    required this.email,
    required this.tenantId,
    required this.outletId,
    this.outletName,
    this.phone,
    this.avatar,
    this.role,
  });

  final String id;
  final String name;
  final String email;
  final String tenantId;
  final String outletId;
  final String? outletName;
  final String? phone;
  final String? avatar;
  final String? role;

  @override
  List<Object?> get props => [
        id,
        name,
        email,
        tenantId,
        outletId,
        outletName,
        phone,
        avatar,
        role,
      ];

  @override
  String toString() => 'User(id: $id, name: $name, email: $email)';
}
