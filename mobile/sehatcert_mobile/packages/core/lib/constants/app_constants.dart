/// App-wide constants for SehatCert mobile applications
class AppConstants {
  AppConstants._();

  // API Configuration
  static const String apiBaseUrl = String.fromEnvironment(
    'API_BASE_URL',
    defaultValue: 'https://api.sehatcert.com/v1',
  );

  static const int apiTimeout = int.fromEnvironment(
    'API_TIMEOUT',
    defaultValue: 30000, // 30 seconds
  );

  // Storage Keys
  static const String keyAccessToken = 'access_token';
  static const String keyRefreshToken = 'refresh_token';
  static const String keyUserId = 'user_id';
  static const String keyUserEmail = 'user_email';
  static const String keyTenantId = 'tenant_id';
  static const String keyOutletId = 'outlet_id';
  static const String keyCompanyId = 'company_id';
  static const String keyIsFirstLaunch = 'is_first_launch';
  static const String keyThemeMode = 'theme_mode';
  static const String keyLanguageCode = 'language_code';

  // Pagination
  static const int defaultPageSize = 20;
  static const int maxPageSize = 100;

  // Certificate Types
  static const String certificateTypeMC = 'MC'; // Medical Certificate
  static const String certificateTypeSKB = 'SKB'; // Surat Keterangan Sehat

  // Date Formats
  static const String dateFormatDisplay = 'dd MMM yyyy';
  static const String dateFormatAPI = 'yyyy-MM-dd';
  static const String dateTimeFormatDisplay = 'dd MMM yyyy HH:mm';
  static const String dateTimeFormatAPI = 'yyyy-MM-dd HH:mm:ss';
  static const String timeFormatDisplay = 'HH:mm';

  // Validation
  static const int minPasswordLength = 8;
  static const int maxPasswordLength = 128;
  static const int nikLength = 16;
  static const int phoneMinLength = 10;
  static const int phoneMaxLength = 15;

  // Cache Duration
  static const Duration cacheShortDuration = Duration(minutes: 5);
  static const Duration cacheMediumDuration = Duration(minutes: 30);
  static const Duration cacheLongDuration = Duration(hours: 24);

  // Assets
  static const String assetLogoPath = 'assets/images/logo.png';
  static const String assetEmptyStatePath = 'assets/images/empty_state.png';
  static const String assetErrorStatePath = 'assets/images/error_state.png';

  // Deep Links
  static const String deepLinkScheme = 'sehatcert';
  static const String deepLinkHost = 'app';

  // Notification Channels
  static const String notificationChannelId = 'sehatcert_notifications';
  static const String notificationChannelName = 'SehatCert Notifications';
  static const String notificationChannelDescription =
      'Notifications for health certificates and updates';

  // Error Messages
  static const String errorGeneric =
      'Terjadi kesalahan. Silakan coba lagi.';
  static const String errorNetwork =
      'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
  static const String errorTimeout =
      'Koneksi timeout. Silakan coba lagi.';
  static const String errorUnauthorized =
      'Sesi Anda telah berakhir. Silakan login kembali.';
  static const String errorForbidden =
      'Anda tidak memiliki akses untuk melakukan tindakan ini.';
  static const String errorNotFound =
      'Data yang Anda cari tidak ditemukan.';
  static const String errorServer =
      'Server mengalami gangguan. Silakan coba lagi nanti.';
  static const String errorValidation =
      'Data yang Anda masukkan tidak valid.';

  // Success Messages
  static const String successSaved = 'Data berhasil disimpan';
  static const String successDeleted = 'Data berhasil dihapus';
  static const String successUpdated = 'Data berhasil diperbarui';
  static const String successSent = 'Berhasil dikirim';

  // Feature Flags
  static const bool enableBiometric = true;
  static const bool enableOfflineMode = true;
  static const bool enablePushNotifications = true;
  static const bool enableCrashReporting = true;
  static const bool enableAnalytics = true;
}
