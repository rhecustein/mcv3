import 'package:intl/intl.dart';

/// Extension methods for String
extension StringExtensions on String {
  /// Check if string is a valid email
  bool get isValidEmail {
    final emailRegex = RegExp(
      r'^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$',
    );
    return emailRegex.hasMatch(this);
  }

  /// Check if string is a valid Indonesian phone number
  bool get isValidPhoneNumber {
    // Remove all non-digit characters
    final digitsOnly = replaceAll(RegExp(r'\D'), '');

    // Check if starts with 0, 62, or +62
    final phoneRegex = RegExp(r'^(0|62|\+62)[0-9]{8,12}$');
    return phoneRegex.hasMatch(digitsOnly);
  }

  /// Check if string is a valid NIK (16 digits)
  bool get isValidNIK {
    final digitsOnly = replaceAll(RegExp(r'\D'), '');
    return digitsOnly.length == 16;
  }

  /// Capitalize first letter
  String get capitalize {
    if (isEmpty) return this;
    return '${this[0].toUpperCase()}${substring(1).toLowerCase()}';
  }

  /// Capitalize first letter of each word
  String get capitalizeWords {
    if (isEmpty) return this;
    return split(' ').map((word) => word.capitalize).join(' ');
  }

  /// Format phone number to Indonesian format
  /// Example: 081234567890 -> +62 812-3456-7890
  String get formatPhoneNumber {
    final digitsOnly = replaceAll(RegExp(r'\D'), '');

    // Convert to +62 format
    var formatted = digitsOnly;
    if (formatted.startsWith('0')) {
      formatted = '62${formatted.substring(1)}';
    } else if (!formatted.startsWith('62')) {
      formatted = '62$formatted';
    }

    // Add formatting
    if (formatted.length >= 12) {
      return '+${formatted.substring(0, 2)} ${formatted.substring(2, 5)}-${formatted.substring(5, 9)}-${formatted.substring(9)}';
    }

    return '+$formatted';
  }

  /// Parse string to DateTime
  DateTime? toDateTime([String? format]) {
    try {
      if (format != null) {
        return DateFormat(format).parse(this);
      }
      return DateTime.tryParse(this);
    } catch (e) {
      return null;
    }
  }

  /// Truncate string with ellipsis
  String truncate(int maxLength, {String ellipsis = '...'}) {
    if (length <= maxLength) return this;
    return '${substring(0, maxLength)}$ellipsis';
  }

  /// Remove HTML tags
  String get removeHtmlTags {
    return replaceAll(RegExp(r'<[^>]*>'), '');
  }

  /// Convert to snake_case
  String get toSnakeCase {
    return replaceAllMapped(
      RegExp(r'[A-Z]'),
      (match) => '_${match.group(0)!.toLowerCase()}',
    ).replaceAll(RegExp(r'^_'), '');
  }

  /// Convert to camelCase
  String get toCamelCase {
    if (isEmpty) return this;
    final words = split('_');
    if (words.length == 1) return this;

    return words.first +
        words.skip(1).map((word) => word.capitalize).join();
  }

  /// Mask sensitive data (e.g., email, phone)
  String mask({int visibleStart = 2, int visibleEnd = 2, String maskChar = '*'}) {
    if (length <= visibleStart + visibleEnd) return this;

    final start = substring(0, visibleStart);
    final end = substring(length - visibleEnd);
    final masked = maskChar * (length - visibleStart - visibleEnd);

    return '$start$masked$end';
  }

  /// Check if string contains only numbers
  bool get isNumeric {
    return RegExp(r'^[0-9]+$').hasMatch(this);
  }

  /// Parse to int safely
  int? toIntOrNull() {
    return int.tryParse(this);
  }

  /// Parse to double safely
  double? toDoubleOrNull() {
    return double.tryParse(this);
  }

  /// Format as currency (Indonesian Rupiah)
  String get toCurrency {
    final number = toDoubleOrNull();
    if (number == null) return this;

    final formatter = NumberFormat.currency(
      locale: 'id_ID',
      symbol: 'Rp ',
      decimalDigits: 0,
    );

    return formatter.format(number);
  }
}

/// Extension for nullable strings
extension NullableStringExtensions on String? {
  /// Check if string is null or empty
  bool get isNullOrEmpty => this == null || this!.isEmpty;

  /// Check if string is not null and not empty
  bool get isNotNullOrEmpty => !isNullOrEmpty;

  /// Get value or default
  String orEmpty() => this ?? '';

  /// Get value or custom default
  String orDefault(String defaultValue) => this ?? defaultValue;
}
