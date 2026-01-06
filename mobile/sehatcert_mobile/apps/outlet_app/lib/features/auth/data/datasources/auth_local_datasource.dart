import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:core/core.dart';
import 'dart:convert';

import '../models/user_model.dart';

/// Local data source for authentication data
class AuthLocalDataSource {
  AuthLocalDataSource(this._secureStorage);

  final FlutterSecureStorage _secureStorage;

  // Storage keys
  static const _keyAccessToken = AppConstants.keyAccessToken;
  static const _keyRefreshToken = AppConstants.keyRefreshToken;
  static const _keyUser = 'user_data';

  /// Save access token
  Future<void> saveAccessToken(String token) async {
    await _secureStorage.write(key: _keyAccessToken, value: token);
  }

  /// Get access token
  Future<String?> getAccessToken() async {
    return _secureStorage.read(key: _keyAccessToken);
  }

  /// Save refresh token
  Future<void> saveRefreshToken(String token) async {
    await _secureStorage.write(key: _keyRefreshToken, value: token);
  }

  /// Get refresh token
  Future<String?> getRefreshToken() async {
    return _secureStorage.read(key: _keyRefreshToken);
  }

  /// Save user data
  Future<void> saveUser(UserModel user) async {
    final userJson = jsonEncode(user.toJson());
    await _secureStorage.write(key: _keyUser, value: userJson);
  }

  /// Get user data
  Future<UserModel?> getUser() async {
    try {
      final userJson = await _secureStorage.read(key: _keyUser);
      if (userJson == null) return null;

      final Map<String, dynamic> json = jsonDecode(userJson);
      return UserModel.fromJson(json);
    } catch (e) {
      throw CacheException(message: 'Failed to get user data: $e');
    }
  }

  /// Check if user is authenticated (has access token)
  Future<bool> isAuthenticated() async {
    final token = await getAccessToken();
    return token != null && token.isNotEmpty;
  }

  /// Clear all auth data
  Future<void> clearAuthData() async {
    await Future.wait([
      _secureStorage.delete(key: _keyAccessToken),
      _secureStorage.delete(key: _keyRefreshToken),
      _secureStorage.delete(key: _keyUser),
    ]);
  }
}
