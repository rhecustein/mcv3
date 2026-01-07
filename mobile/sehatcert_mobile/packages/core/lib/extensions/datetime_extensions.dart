import 'package:intl/intl.dart';

/// Extension methods for DateTime
extension DateTimeExtensions on DateTime {
  /// Format to display format (dd MMM yyyy)
  String get toDisplayDate {
    return DateFormat('dd MMM yyyy', 'id_ID').format(this);
  }

  /// Format to display format with time (dd MMM yyyy HH:mm)
  String get toDisplayDateTime {
    return DateFormat('dd MMM yyyy HH:mm', 'id_ID').format(this);
  }

  /// Format to API format (yyyy-MM-dd)
  String get toAPIDate {
    return DateFormat('yyyy-MM-dd').format(this);
  }

  /// Format to API format with time (yyyy-MM-dd HH:mm:ss)
  String get toAPIDateTime {
    return DateFormat('yyyy-MM-dd HH:mm:ss').format(this);
  }

  /// Format to time only (HH:mm)
  String get toTimeOnly {
    return DateFormat('HH:mm').format(this);
  }

  /// Format to custom format
  String format(String pattern, {String locale = 'id_ID'}) {
    return DateFormat(pattern, locale).format(this);
  }

  /// Check if date is today
  bool get isToday {
    final now = DateTime.now();
    return year == now.year && month == now.month && day == now.day;
  }

  /// Check if date is yesterday
  bool get isYesterday {
    final yesterday = DateTime.now().subtract(const Duration(days: 1));
    return year == yesterday.year &&
        month == yesterday.month &&
        day == yesterday.day;
  }

  /// Check if date is tomorrow
  bool get isTomorrow {
    final tomorrow = DateTime.now().add(const Duration(days: 1));
    return year == tomorrow.year &&
        month == tomorrow.month &&
        day == tomorrow.day;
  }

  /// Check if date is in the past
  bool get isPast => isBefore(DateTime.now());

  /// Check if date is in the future
  bool get isFuture => isAfter(DateTime.now());

  /// Get relative time string
  /// Example: "2 jam yang lalu", "5 menit yang lalu"
  String get relativeTime {
    final now = DateTime.now();
    final difference = now.difference(this);

    if (difference.isNegative) {
      // Future date
      final futureDiff = difference.abs();
      if (futureDiff.inDays > 365) {
        final years = (futureDiff.inDays / 365).floor();
        return '$years tahun lagi';
      } else if (futureDiff.inDays > 30) {
        final months = (futureDiff.inDays / 30).floor();
        return '$months bulan lagi';
      } else if (futureDiff.inDays > 0) {
        return '${futureDiff.inDays} hari lagi';
      } else if (futureDiff.inHours > 0) {
        return '${futureDiff.inHours} jam lagi';
      } else if (futureDiff.inMinutes > 0) {
        return '${futureDiff.inMinutes} menit lagi';
      } else {
        return 'Baru saja';
      }
    }

    // Past date
    if (difference.inDays > 365) {
      final years = (difference.inDays / 365).floor();
      return '$years tahun yang lalu';
    } else if (difference.inDays > 30) {
      final months = (difference.inDays / 30).floor();
      return '$months bulan yang lalu';
    } else if (difference.inDays > 0) {
      return '${difference.inDays} hari yang lalu';
    } else if (difference.inHours > 0) {
      return '${difference.inHours} jam yang lalu';
    } else if (difference.inMinutes > 0) {
      return '${difference.inMinutes} menit yang lalu';
    } else {
      return 'Baru saja';
    }
  }

  /// Get start of day (00:00:00)
  DateTime get startOfDay {
    return DateTime(year, month, day);
  }

  /// Get end of day (23:59:59)
  DateTime get endOfDay {
    return DateTime(year, month, day, 23, 59, 59, 999);
  }

  /// Get start of week (Monday)
  DateTime get startOfWeek {
    final daysToSubtract = weekday - 1;
    return subtract(Duration(days: daysToSubtract)).startOfDay;
  }

  /// Get end of week (Sunday)
  DateTime get endOfWeek {
    final daysToAdd = 7 - weekday;
    return add(Duration(days: daysToAdd)).endOfDay;
  }

  /// Get start of month
  DateTime get startOfMonth {
    return DateTime(year, month);
  }

  /// Get end of month
  DateTime get endOfMonth {
    return DateTime(year, month + 1, 0, 23, 59, 59, 999);
  }

  /// Get start of year
  DateTime get startOfYear {
    return DateTime(year);
  }

  /// Get end of year
  DateTime get endOfYear {
    return DateTime(year, 12, 31, 23, 59, 59, 999);
  }

  /// Add business days (excluding weekends)
  DateTime addBusinessDays(int days) {
    var result = this;
    var remainingDays = days;

    while (remainingDays > 0) {
      result = result.add(const Duration(days: 1));
      if (result.weekday != DateTime.saturday &&
          result.weekday != DateTime.sunday) {
        remainingDays--;
      }
    }

    return result;
  }

  /// Check if date is weekend
  bool get isWeekend =>
      weekday == DateTime.saturday || weekday == DateTime.sunday;

  /// Check if date is weekday
  bool get isWeekday => !isWeekend;

  /// Get age from birthdate
  int get age {
    final now = DateTime.now();
    var age = now.year - year;

    if (now.month < month || (now.month == month && now.day < day)) {
      age--;
    }

    return age;
  }

  /// Copy with new values
  DateTime copyWith({
    int? year,
    int? month,
    int? day,
    int? hour,
    int? minute,
    int? second,
    int? millisecond,
    int? microsecond,
  }) {
    return DateTime(
      year ?? this.year,
      month ?? this.month,
      day ?? this.day,
      hour ?? this.hour,
      minute ?? this.minute,
      second ?? this.second,
      millisecond ?? this.millisecond,
      microsecond ?? this.microsecond,
    );
  }

  /// Get Indonesian day name
  String get dayNameIndonesian {
    const days = [
      'Senin',
      'Selasa',
      'Rabu',
      'Kamis',
      'Jumat',
      'Sabtu',
      'Minggu',
    ];
    return days[weekday - 1];
  }

  /// Get Indonesian month name
  String get monthNameIndonesian {
    const months = [
      'Januari',
      'Februari',
      'Maret',
      'April',
      'Mei',
      'Juni',
      'Juli',
      'Agustus',
      'September',
      'Oktober',
      'November',
      'Desember',
    ];
    return months[month - 1];
  }
}

/// Extension for nullable DateTime
extension NullableDateTimeExtensions on DateTime? {
  /// Get value or default
  DateTime orNow() => this ?? DateTime.now();

  /// Format or return empty string
  String toDisplayDateOrEmpty() =>
      this?.toDisplayDate ?? '';

  /// Format or return dash
  String toDisplayDateOr(String defaultValue) =>
      this?.toDisplayDate ?? defaultValue;
}
