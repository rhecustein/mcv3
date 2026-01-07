import '../repositories/auth_repository.dart';

/// Use case to check if user is authenticated
/// Note: This returns bool directly, not wrapped in Either
class CheckAuthStatus {
  CheckAuthStatus(this.repository);

  final AuthRepository repository;

  Future<bool> call() {
    return repository.isAuthenticated();
  }
}
