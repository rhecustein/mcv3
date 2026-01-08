import 'dart:convert';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:core/core.dart';

import '../models/certificate_model.dart';
import '../models/storage_info_model.dart';

/// Local data source for caching certificates
class CertificateLocalDataSource {
  CertificateLocalDataSource(this._prefs);

  final SharedPreferences _prefs;

  // Cache keys
  static const _keyCertificatesList = 'certificates_list_cache';
  static const _keyCertificateDetail = 'certificate_detail_';
  static const _keyStorageInfo = 'storage_info_cache';
  static const _keyCacheTimestamp = '_timestamp';

  // Cache duration (15 minutes)
  static const _cacheDuration = Duration(minutes: 15);

  /// Cache certificates list
  Future<void> cacheCertificates(List<CertificateModel> certificates) async {
    try {
      final json = certificates.map((c) => c.toJson()).toList();
      await _prefs.setString(_keyCertificatesList, jsonEncode(json));
      await _prefs.setInt(
        '$_keyCertificatesList$_keyCacheTimestamp',
        DateTime.now().millisecondsSinceEpoch,
      );
    } catch (e) {
      throw CacheException(
          message: 'Failed to cache certificates list: $e');
    }
  }

  /// Get cached certificates list
  Future<List<CertificateModel>?> getCachedCertificates() async {
    try {
      if (!_isCacheValid(_keyCertificatesList)) {
        return null;
      }

      final json = _prefs.getString(_keyCertificatesList);
      if (json == null) return null;

      final List<dynamic> list = jsonDecode(json);
      return list.map((e) => CertificateModel.fromJson(e)).toList();
    } catch (e) {
      throw CacheException(
          message: 'Failed to get cached certificates: $e');
    }
  }

  /// Cache certificate detail
  Future<void> cacheCertificateDetail(CertificateModel certificate) async {
    try {
      final key = '$_keyCertificateDetail${certificate.id}';
      await _prefs.setString(key, jsonEncode(certificate.toJson()));
      await _prefs.setInt(
        '$key$_keyCacheTimestamp',
        DateTime.now().millisecondsSinceEpoch,
      );
    } catch (e) {
      throw CacheException(
          message: 'Failed to cache certificate detail: $e');
    }
  }

  /// Get cached certificate detail
  Future<CertificateModel?> getCachedCertificateDetail(String id) async {
    try {
      final key = '$_keyCertificateDetail$id';
      if (!_isCacheValid(key)) {
        return null;
      }

      final json = _prefs.getString(key);
      if (json == null) return null;

      return CertificateModel.fromJson(jsonDecode(json));
    } catch (e) {
      throw CacheException(
          message: 'Failed to get cached certificate detail: $e');
    }
  }

  /// Cache storage info
  Future<void> cacheStorageInfo(StorageInfoModel info) async {
    try {
      await _prefs.setString(_keyStorageInfo, jsonEncode(info.toJson()));
      await _prefs.setInt(
        '$_keyStorageInfo$_keyCacheTimestamp',
        DateTime.now().millisecondsSinceEpoch,
      );
    } catch (e) {
      throw CacheException(message: 'Failed to cache storage info: $e');
    }
  }

  /// Get cached storage info
  Future<StorageInfoModel?> getCachedStorageInfo() async {
    try {
      if (!_isCacheValid(_keyStorageInfo)) {
        return null;
      }

      final json = _prefs.getString(_keyStorageInfo);
      if (json == null) return null;

      return StorageInfoModel.fromJson(jsonDecode(json));
    } catch (e) {
      throw CacheException(
          message: 'Failed to get cached storage info: $e');
    }
  }

  /// Clear all certificates cache
  Future<void> clearCache() async {
    final keys = _prefs.getKeys().where(
          (key) =>
              key.startsWith(_keyCertificatesList) ||
              key.startsWith(_keyCertificateDetail) ||
              key.startsWith(_keyStorageInfo),
        );

    await Future.wait(keys.map((key) => _prefs.remove(key)));
  }

  /// Check if cache is still valid
  bool _isCacheValid(String key) {
    final timestamp = _prefs.getInt('$key$_keyCacheTimestamp');
    if (timestamp == null) return false;

    final cacheTime = DateTime.fromMillisecondsSinceEpoch(timestamp);
    final now = DateTime.now();

    return now.difference(cacheTime) < _cacheDuration;
  }
}
