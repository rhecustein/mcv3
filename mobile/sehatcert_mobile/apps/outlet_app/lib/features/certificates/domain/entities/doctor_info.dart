import 'package:equatable/equatable.dart';

/// Doctor Information entity
class DoctorInfo extends Equatable {
  const DoctorInfo({
    required this.id,
    this.name,
  });

  final String id;
  final String? name;

  @override
  List<Object?> get props => [id, name];

  @override
  String toString() => 'DoctorInfo(id: $id, name: $name)';
}
