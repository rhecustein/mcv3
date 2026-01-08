import 'package:equatable/equatable.dart';

/// Patient Information entity
class PatientInfo extends Equatable {
  const PatientInfo({
    required this.name,
    this.nik,
    this.birthdate,
    this.gender,
    this.age,
    this.company,
  });

  final String name;
  final String? nik;
  final String? birthdate;
  final String? gender;
  final int? age;
  final String? company;

  @override
  List<Object?> get props => [
        name,
        nik,
        birthdate,
        gender,
        age,
        company,
      ];

  @override
  String toString() => 'PatientInfo(name: $name, nik: $nik)';
}
