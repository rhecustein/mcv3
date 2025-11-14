# MCv3 Flutter Mobile App - Ecosystem Guide

## 📱 Overview

Panduan lengkap untuk membangun ekosistem Flutter mobile app yang terintegrasi dengan backend MCv3 (Multi-tenant Healthcare Management SaaS Platform). Dokumen ini menjelaskan semua fitur yang tersedia di backend dan bagaimana mengimplementasikannya di Flutter.

---

## 🏗️ Arsitektur Sistem

### Multi-Tenancy Architecture
```
┌─────────────────────────────────────────────────────┐
│                  Mobile App (Flutter)                │
├─────────────────────────────────────────────────────┤
│                                                       │
│  ┌──────────────┐  ┌──────────────┐  ┌───────────┐ │
│  │   B2C User   │  │ B2B Corporate│  │  Provider │ │
│  │  (Patient)   │  │   Employee   │  │   Admin   │ │
│  └──────────────┘  └──────────────┘  └───────────┘ │
│                                                       │
├─────────────────────────────────────────────────────┤
│                  API Layer (REST)                     │
├─────────────────────────────────────────────────────┤
│                                                       │
│  ┌─────────────────────────────────────────────┐   │
│  │    Laravel Backend (MCv3)                    │   │
│  │  - Multi-tenant Database                     │   │
│  │  - Subdomain Routing                         │   │
│  │  - Tenant Isolation                          │   │
│  └─────────────────────────────────────────────┘   │
│                                                       │
└─────────────────────────────────────────────────────┘
```

### Database Schema Overview
- **Tenants**: Rumah sakit/klinik yang menggunakan sistem
- **Users**: Dokter, admin, staff per tenant
- **Patients**: Data pasien per tenant
- **MCU Packages**: Paket medical checkup yang ditawarkan
- **MCU Providers**: Penyedia layanan MCU (marketplace)
- **Bookings**: Pemesanan MCU
- **Payments**: Pembayaran dengan Midtrans
- **Reviews**: Rating & review dari pasien
- **Promo Codes**: Sistem diskon dan promo
- **Corporate**: B2B corporate health management

---

## 🎯 Fitur Utama yang Tersedia

### 1. **Authentication & Authorization** 🔐

#### Fitur:
- Multi-tenant authentication (subdomain-based)
- User registration & login
- Password reset via email
- Role-based access control (RBAC)
- Session management
- 2FA support (optional)

#### API Endpoints (Yang Perlu Dibuat):
```http
POST   /api/v1/auth/register
POST   /api/v1/auth/login
POST   /api/v1/auth/logout
POST   /api/v1/auth/refresh
POST   /api/v1/auth/forgot-password
POST   /api/v1/auth/reset-password
GET    /api/v1/auth/me
PUT    /api/v1/auth/update-profile
```

#### Flutter Implementation:
```dart
// Models
class User {
  final int id;
  final String name;
  final String email;
  final String role;
  final String? avatar;
  final Tenant tenant;
}

class AuthResponse {
  final String token;
  final User user;
  final String tokenType;
  final int expiresIn;
}

// Service
class AuthService {
  Future<AuthResponse> login(String email, String password);
  Future<void> logout();
  Future<User> getCurrentUser();
  Future<User> updateProfile(UserUpdateDto dto);
  Future<void> forgotPassword(String email);
  Future<void> resetPassword(String token, String password);
}
```

#### State Management (Riverpod/Bloc):
- AuthState: authenticated, unauthenticated, loading
- UserState: current user data
- Token storage: secure_storage

---

### 2. **MCU Marketplace** 🏥

#### Fitur:
- Browse paket MCU dari berbagai provider
- Filter by: kategori, harga, lokasi, rating
- Search paket MCU
- Detail paket dengan inclusions/exclusions
- Featured packages
- Provider profiles
- Package reviews & ratings

#### API Endpoints:
```http
GET    /api/v1/marketplace/packages              # List all packages
GET    /api/v1/marketplace/packages/{id}         # Package detail
GET    /api/v1/marketplace/packages/featured     # Featured packages
GET    /api/v1/marketplace/providers             # List providers
GET    /api/v1/marketplace/providers/{id}        # Provider detail
GET    /api/v1/marketplace/categories            # List categories
GET    /api/v1/marketplace/search?q=keyword      # Search packages
```

#### Query Parameters:
```
category: string (basic, executive, comprehensive, etc.)
min_price: number
max_price: number
city: string
rating: number (1-5)
gender: string (male, female, all)
sort_by: price_asc, price_desc, rating, popular
page: number
per_page: number (default: 20)
```

#### Flutter Models:
```dart
class McuPackage {
  final int id;
  final String name;
  final String slug;
  final String description;
  final double price;
  final double? discountedPrice;
  final int? discountPercentage;
  final String category;
  final String genderTarget;
  final int? minAge;
  final int? maxAge;
  final int durationMinutes;
  final int validityDays;
  final List<String> inclusions;
  final List<String> exclusions;
  final List<String> preparationInstructions;
  final String? image;
  final List<String> images;
  final double rating;
  final int totalReviews;
  final int bookingCount;
  final bool isFeatured;
  final McuProvider provider;
}

class McuProvider {
  final int id;
  final String name;
  final String description;
  final String address;
  final String city;
  final String province;
  final String phone;
  final String email;
  final double rating;
  final int totalReviews;
  final OperatingHours operatingHours;
  final List<String> facilities;
}
```

#### Flutter Screens:
1. **MarketplaceHomePage**: Grid/list view packages
2. **PackageDetailPage**: Detail paket dengan booking CTA
3. **ProviderDetailPage**: Info provider + packages mereka
4. **SearchPage**: Search dengan filters
5. **FilterPage**: Advanced filtering

---

### 3. **Booking System** 📅

#### Fitur:
- Create booking untuk MCU
- Select date & time
- Patient information form
- Payment method selection
- Booking confirmation
- Booking history
- Booking detail with QR code
- Cancel booking
- Reschedule (future feature)

#### API Endpoints:
```http
POST   /api/v1/bookings                    # Create booking
GET    /api/v1/bookings                    # List user bookings
GET    /api/v1/bookings/{id}               # Booking detail
PUT    /api/v1/bookings/{id}/cancel        # Cancel booking
GET    /api/v1/bookings/{id}/qr-code       # Generate QR code
GET    /api/v1/bookings/stats              # Booking statistics
```

#### Request Body (Create Booking):
```json
{
  "package_id": 1,
  "provider_id": 1,
  "booking_date": "2025-11-20",
  "booking_time": "09:00:00",
  "patient_name": "John Doe",
  "patient_email": "john@example.com",
  "patient_phone": "081234567890",
  "patient_birth_date": "1990-01-01",
  "patient_gender": "male",
  "patient_nik": "3201234567890123",
  "special_notes": "First time MCU",
  "promo_code": "WELCOME10"
}
```

#### Flutter Models:
```dart
class McuBooking {
  final int id;
  final String bookingNumber;
  final McuPackage package;
  final McuProvider provider;
  final DateTime bookingDate;
  final String bookingTime;
  final String patientName;
  final String patientEmail;
  final String patientPhone;
  final DateTime patientBirthDate;
  final String patientGender;
  final String patientNik;
  final BookingStatus status;
  final double originalPrice;
  final double finalPrice;
  final double discountAmount;
  final String? promoCode;
  final Payment? payment;
  final DateTime createdAt;
}

enum BookingStatus {
  pending,
  confirmed,
  completed,
  cancelled
}
```

#### Flutter Screens:
1. **BookingFormPage**: Multi-step form
   - Step 1: Patient info
   - Step 2: Date & time selection
   - Step 3: Review & payment
2. **MyBookingsPage**: List bookings dengan tabs
3. **BookingDetailPage**: Detail dengan QR code
4. **BookingConfirmationPage**: Success page

#### Widgets:
- DateTimePicker
- PatientInfoForm
- PaymentMethodSelector
- QRCodeDisplay
- BookingStatusBadge

---

### 4. **Payment Integration** 💳

#### Fitur:
- Midtrans payment gateway integration
- Multiple payment methods:
  - Credit/Debit Card
  - Bank Transfer
  - E-Wallet (GoPay, OVO, DANA)
  - QRIS
- Payment status tracking
- Payment webhook handling
- Invoice generation

#### API Endpoints:
```http
POST   /api/v1/payments/create             # Create payment
GET    /api/v1/payments/{id}               # Payment detail
GET    /api/v1/payments/{id}/status        # Check payment status
POST   /api/v1/payments/webhooks/midtrans  # Midtrans webhook
```

#### Midtrans Snap Flow:
```dart
class PaymentService {
  // 1. Create payment and get Snap token
  Future<PaymentResponse> createPayment(int bookingId) async {
    final response = await api.post('/payments/create', {
      'booking_id': bookingId,
    });
    return PaymentResponse.fromJson(response);
  }

  // 2. Open Midtrans Snap UI
  Future<void> openMidtransSnap(String snapToken) async {
    // Use midtrans_sdk package
    await MidtransSDK.instance.startPaymentUiFlow(
      token: snapToken,
    );
  }

  // 3. Handle payment result
  void handlePaymentResult(TransactionResult result) {
    if (result.status == TransactionStatus.success) {
      // Show success page
    } else if (result.status == TransactionStatus.pending) {
      // Show pending page
    } else {
      // Show failed page
    }
  }
}
```

#### Flutter Models:
```dart
class Payment {
  final int id;
  final String paymentNumber;
  final double amount;
  final PaymentStatus status;
  final PaymentMethod method;
  final String? snapToken;
  final String? redirectUrl;
  final DateTime? paidAt;
}

enum PaymentStatus {
  pending,
  paid,
  failed,
  expired
}

enum PaymentMethod {
  creditCard,
  bankTransfer,
  eWallet,
  qris
}
```

#### Flutter Dependencies:
```yaml
dependencies:
  midtrans_sdk: ^latest_version
  webview_flutter: ^latest_version  # For payment page
```

---

### 5. **Review & Rating System** ⭐

#### Fitur:
- Rating paket MCU (1-5 bintang)
- Detailed ratings:
  - Service quality
  - Cleanliness
  - Staff friendliness
  - Value for money
- Written reviews
- Upload review photos (future)
- Helpful/Not helpful voting
- Provider responses
- Review moderation

#### API Endpoints:
```http
POST   /api/v1/reviews                     # Create review
GET    /api/v1/reviews                     # List reviews (by package/provider)
GET    /api/v1/reviews/{id}                # Review detail
PUT    /api/v1/reviews/{id}                # Update review
DELETE /api/v1/reviews/{id}                # Delete review
POST   /api/v1/reviews/{id}/helpful        # Mark helpful
POST   /api/v1/reviews/{id}/not-helpful    # Mark not helpful
```

#### Request Body (Create Review):
```json
{
  "booking_id": 123,
  "rating": 5,
  "title": "Excellent service!",
  "comment": "Very professional staff, clean facility...",
  "service_rating": 5,
  "cleanliness_rating": 5,
  "staff_rating": 5,
  "value_rating": 4,
  "is_anonymous": false
}
```

#### Flutter Models:
```dart
class Review {
  final int id;
  final int rating;
  final String? title;
  final String? comment;
  final int? serviceRating;
  final int? cleanlinessRating;
  final int? staffRating;
  final int? valueRating;
  final bool isVerified;
  final bool isAnonymous;
  final User? user;
  final DateTime createdAt;
  final int helpfulCount;
  final int notHelpfulCount;
  final String? providerResponse;
}

class ReviewStats {
  final double averageRating;
  final int totalReviews;
  final Map<int, int> ratingDistribution; // {5: 100, 4: 50, 3: 10, ...}
}
```

#### Flutter Screens:
1. **ReviewListPage**: List semua review untuk package
2. **CreateReviewPage**: Form untuk buat review
3. **ReviewDetailPage**: Detail review dengan responses

#### Widgets:
- StarRatingDisplay (read-only)
- StarRatingInput (interactive)
- RatingBreakdownChart
- ReviewCard
- DetailedRatingBars

---

### 6. **Promo Code System** 🎫

#### Fitur:
- Apply promo code saat booking
- Percentage discount atau fixed amount
- Minimum purchase requirement
- Usage limits (total & per user)
- Validity period
- Package/provider/category targeting
- User eligibility (new/existing users)
- Promo code suggestions

#### API Endpoints:
```http
POST   /api/v1/promo-codes/validate        # Validate promo code
POST   /api/v1/promo-codes/apply           # Apply to booking
GET    /api/v1/promo-codes/available       # List public promo codes
```

#### Request/Response (Validate):
```json
// Request
{
  "code": "WELCOME10",
  "amount": 500000,
  "package_id": 1
}

// Response
{
  "valid": true,
  "message": "Promo code is valid!",
  "promo_code": {
    "code": "WELCOME10",
    "name": "Welcome Discount",
    "description": "10% off for new users",
    "discount_description": "10% OFF (max Rp 50,000)"
  },
  "discount_amount": 50000,
  "final_amount": 450000,
  "savings": "You save Rp 50,000"
}
```

#### Flutter Models:
```dart
class PromoCode {
  final String code;
  final String name;
  final String description;
  final String discountDescription;
  final DiscountType discountType;
  final double discountValue;
  final double? maxDiscountAmount;
  final double minPurchaseAmount;
  final DateTime? validFrom;
  final DateTime? validUntil;
}

class PromoValidationResult {
  final bool valid;
  final String message;
  final double discountAmount;
  final double finalAmount;
}
```

#### Flutter Implementation:
```dart
class PromoCodeWidget extends StatefulWidget {
  final double amount;
  final int packageId;
  final Function(PromoValidationResult) onApplied;
}

// Service
class PromoCodeService {
  Future<PromoValidationResult> validatePromoCode({
    required String code,
    required double amount,
    int? packageId,
  });

  Future<List<PromoCode>> getAvailablePromoCodes();
}
```

---

### 7. **B2B Corporate Portal** 🏢

#### Fitur:
- Corporate employee management
- Bulk MCU scheduling
- Health report dashboard
- Employee health tracking
- MCU compliance monitoring
- Group bookings
- Corporate invoicing

#### API Endpoints:
```http
GET    /api/v1/corporate/dashboard          # Corporate dashboard
GET    /api/v1/corporate/employees          # List employees
POST   /api/v1/corporate/employees          # Add employee
PUT    /api/v1/corporate/employees/{id}     # Update employee
GET    /api/v1/corporate/employees/{id}/mcu # Employee MCU history
POST   /api/v1/corporate/bookings/bulk      # Bulk booking
GET    /api/v1/corporate/reports            # Health reports
GET    /api/v1/corporate/reports/{id}       # Report detail
```

#### Flutter Models:
```dart
class CorporateEmployee {
  final int id;
  final String employeeId;
  final String fullName;
  final String department;
  final String position;
  final DateTime? lastMcuDate;
  final DateTime? nextMcuDue;
  final HealthStatus healthStatus;
  final EmergencyContact? emergencyContact;
}

enum HealthStatus {
  fit,
  fitWithNotes,
  unfit,
  pending
}

class CorporateHealthReport {
  final int id;
  final String reportNumber;
  final ReportType reportType;
  final int totalEmployees;
  final int employeesExamined;
  final int fitCount;
  final int unfitCount;
  final Map<String, int> commonFindings;
  final double totalCost;
}
```

#### Flutter Screens (Corporate App):
1. **CorporateDashboard**: Overview stats
2. **EmployeeListPage**: Manage employees
3. **BulkBookingPage**: Schedule multiple employees
4. **HealthReportsPage**: View reports
5. **CompliancePage**: Track MCU compliance

---

### 8. **User Profile & Settings** 👤

#### Fitur:
- View/edit profile
- Change password
- Upload avatar
- Notification settings
- Language preferences
- Theme (dark/light mode)
- Logout

#### API Endpoints:
```http
GET    /api/v1/profile                      # Get profile
PUT    /api/v1/profile                      # Update profile
POST   /api/v1/profile/avatar               # Upload avatar
PUT    /api/v1/profile/password             # Change password
GET    /api/v1/profile/settings             # Get settings
PUT    /api/v1/profile/settings             # Update settings
```

#### Flutter Models:
```dart
class UserProfile {
  final int id;
  final String name;
  final String email;
  final String? phone;
  final String? avatar;
  final DateTime? birthDate;
  final String? gender;
  final String? address;
  final UserSettings settings;
}

class UserSettings {
  final bool emailNotifications;
  final bool pushNotifications;
  final bool smsNotifications;
  final String language;
  final String theme; // light, dark, system
}
```

---

### 9. **Notifications** 🔔

#### Fitur:
- Booking confirmation
- Payment success/failed
- MCU reminder (1 day before)
- Result ready notification
- Promo code alerts
- Review requests
- Push notifications (FCM)

#### API Endpoints:
```http
GET    /api/v1/notifications                # List notifications
PUT    /api/v1/notifications/{id}/read      # Mark as read
PUT    /api/v1/notifications/read-all       # Mark all as read
DELETE /api/v1/notifications/{id}           # Delete notification
POST   /api/v1/notifications/fcm-token      # Register FCM token
```

#### Flutter Models:
```dart
class Notification {
  final int id;
  final String type;
  final String title;
  final String body;
  final Map<String, dynamic>? data;
  final bool isRead;
  final DateTime createdAt;
}
```

#### Flutter Implementation:
```dart
// Use firebase_messaging package
class NotificationService {
  Future<void> initialize();
  Future<String?> getFCMToken();
  void handleForegroundMessage(RemoteMessage message);
  void handleBackgroundMessage(RemoteMessage message);
  void handleNotificationTap(RemoteMessage message);
}
```

---

### 10. **Medical Records & Results** 📋

#### Fitur:
- View MCU results
- Download PDF results
- Result history
- Share results with doctor
- Print results
- Result interpretation

#### API Endpoints:
```http
GET    /api/v1/results                      # List results
GET    /api/v1/results/{id}                 # Result detail
GET    /api/v1/results/{id}/pdf             # Download PDF
POST   /api/v1/results/{id}/share           # Share via email
```

#### Flutter Models:
```dart
class McuResult {
  final int id;
  final McuBooking booking;
  final String resultNumber;
  final DateTime examinationDate;
  final String overallStatus; // fit, fit_with_notes, unfit
  final List<ResultItem> items;
  final String? conclusion;
  final String? recommendations;
  final String? pdfUrl;
}

class ResultItem {
  final String testName;
  final String value;
  final String unit;
  final String normalRange;
  final ResultStatus status; // normal, warning, abnormal
}
```

---

## 🎨 UI/UX Design Guidelines

### Color Scheme
```dart
class AppColors {
  // Primary
  static const primary = Color(0xFF2563EB);      // Blue
  static const primaryDark = Color(0xFF1D4ED8);
  static const primaryLight = Color(0xFF60A5FA);

  // Secondary
  static const secondary = Color(0xFF10B981);    // Green
  static const accent = Color(0xFFF59E0B);       // Orange

  // Status
  static const success = Color(0xFF10B981);
  static const warning = Color(0xFFF59E0B);
  static const error = Color(0xFFEF4444);
  static const info = Color(0xFF3B82F6);

  // Neutrals
  static const dark = Color(0xFF1F2937);
  static const light = Color(0xFFF9FAFB);
  static const gray = Color(0xFF6B7280);
}
```

### Typography
```dart
class AppTextStyles {
  static const headingLarge = TextStyle(
    fontSize: 32,
    fontWeight: FontWeight.bold,
    height: 1.2,
  );

  static const headingMedium = TextStyle(
    fontSize: 24,
    fontWeight: FontWeight.bold,
    height: 1.3,
  );

  static const bodyLarge = TextStyle(
    fontSize: 16,
    fontWeight: FontWeight.normal,
    height: 1.5,
  );

  static const bodyMedium = TextStyle(
    fontSize: 14,
    fontWeight: FontWeight.normal,
    height: 1.5,
  );
}
```

### Screen Templates
1. **List Page**: Pull-to-refresh, infinite scroll, filters
2. **Detail Page**: Hero animation, FAB actions
3. **Form Page**: Multi-step, validation, progress indicator
4. **Empty State**: Illustration + CTA
5. **Loading State**: Shimmer effect
6. **Error State**: Retry button

---

## 📦 Flutter Dependencies

### Core Dependencies
```yaml
dependencies:
  flutter:
    sdk: flutter

  # State Management
  flutter_riverpod: ^2.4.0
  # atau
  flutter_bloc: ^8.1.3

  # HTTP Client
  dio: ^5.4.0
  retrofit: ^4.0.3

  # Local Storage
  hive: ^2.2.3
  hive_flutter: ^1.1.0
  shared_preferences: ^2.2.2
  flutter_secure_storage: ^9.0.0

  # Navigation
  go_router: ^13.0.0

  # UI Components
  cached_network_image: ^3.3.1
  shimmer: ^3.0.0
  flutter_svg: ^2.0.9
  carousel_slider: ^4.2.1
  smooth_page_indicator: ^1.1.0

  # Forms & Validation
  flutter_form_builder: ^9.1.1
  form_builder_validators: ^9.1.0

  # Date & Time
  intl: ^0.19.0
  flutter_datetime_picker: ^1.5.1

  # QR Code
  qr_flutter: ^4.1.0
  mobile_scanner: ^3.5.5

  # PDF
  pdf: ^3.10.7
  printing: ^5.12.0

  # Payment
  midtrans_sdk: ^0.3.0
  webview_flutter: ^4.5.0

  # Push Notifications
  firebase_core: ^2.24.2
  firebase_messaging: ^14.7.9
  flutter_local_notifications: ^16.3.0

  # Image Picker
  image_picker: ^1.0.7

  # Rating
  flutter_rating_bar: ^4.0.1

  # Charts
  fl_chart: ^0.66.0

  # Utils
  equatable: ^2.0.5
  freezed_annotation: ^2.4.1
  json_annotation: ^4.8.1

dev_dependencies:
  build_runner: ^2.4.7
  freezed: ^2.4.6
  json_serializable: ^6.7.1
  retrofit_generator: ^8.0.4
```

---

## 🔧 Project Structure

```
lib/
├── core/
│   ├── api/
│   │   ├── api_client.dart
│   │   ├── api_endpoints.dart
│   │   ├── api_interceptors.dart
│   │   └── api_response.dart
│   ├── constants/
│   │   ├── app_colors.dart
│   │   ├── app_text_styles.dart
│   │   └── app_constants.dart
│   ├── errors/
│   │   ├── exceptions.dart
│   │   └── failures.dart
│   ├── storage/
│   │   ├── hive_storage.dart
│   │   └── secure_storage.dart
│   └── utils/
│       ├── date_formatter.dart
│       ├── currency_formatter.dart
│       └── validators.dart
│
├── features/
│   ├── auth/
│   │   ├── data/
│   │   │   ├── models/
│   │   │   ├── repositories/
│   │   │   └── datasources/
│   │   ├── domain/
│   │   │   ├── entities/
│   │   │   ├── repositories/
│   │   │   └── usecases/
│   │   └── presentation/
│   │       ├── pages/
│   │       ├── widgets/
│   │       └── providers/
│   │
│   ├── marketplace/
│   │   ├── data/
│   │   ├── domain/
│   │   └── presentation/
│   │
│   ├── booking/
│   │   ├── data/
│   │   ├── domain/
│   │   └── presentation/
│   │
│   ├── payment/
│   │   ├── data/
│   │   ├── domain/
│   │   └── presentation/
│   │
│   ├── review/
│   │   ├── data/
│   │   ├── domain/
│   │   └── presentation/
│   │
│   ├── profile/
│   │   ├── data/
│   │   ├── domain/
│   │   └── presentation/
│   │
│   └── corporate/
│       ├── data/
│       ├── domain/
│       └── presentation/
│
├── routes/
│   └── app_router.dart
│
├── widgets/
│   ├── buttons/
│   ├── cards/
│   ├── dialogs/
│   ├── forms/
│   └── common/
│
└── main.dart
```

---

## 🔐 Security Considerations

### 1. **Authentication Token**
```dart
class TokenManager {
  static const _tokenKey = 'auth_token';

  Future<void> saveToken(String token) async {
    await secureStorage.write(key: _tokenKey, value: token);
  }

  Future<String?> getToken() async {
    return await secureStorage.read(key: _tokenKey);
  }

  Future<void> deleteToken() async {
    await secureStorage.delete(key: _tokenKey);
  }
}
```

### 2. **API Interceptor**
```dart
class AuthInterceptor extends Interceptor {
  @override
  void onRequest(RequestOptions options, RequestInterceptorHandler handler) async {
    final token = await tokenManager.getToken();
    if (token != null) {
      options.headers['Authorization'] = 'Bearer $token';
    }
    options.headers['Accept'] = 'application/json';
    options.headers['Content-Type'] = 'application/json';
    handler.next(options);
  }

  @override
  void onError(DioError err, ErrorInterceptorHandler handler) async {
    if (err.response?.statusCode == 401) {
      // Token expired, redirect to login
      await tokenManager.deleteToken();
      navigatorKey.currentState?.pushReplacementNamed('/login');
    }
    handler.next(err);
  }
}
```

### 3. **SSL Pinning** (Production)
```dart
class ApiClient {
  static Dio createDio() {
    final dio = Dio();

    // SSL Pinning for production
    (dio.httpClientAdapter as DefaultHttpClientAdapter).onHttpClientCreate =
      (client) {
        client.badCertificateCallback =
          (X509Certificate cert, String host, int port) {
            // Validate certificate
            return validateCertificate(cert);
          };
        return client;
      };

    return dio;
  }
}
```

---

## 🧪 Testing Strategy

### Unit Tests
```dart
// Test models
test('McuPackage.fromJson should parse correctly', () {
  final json = {...};
  final package = McuPackage.fromJson(json);
  expect(package.name, 'Executive Package');
  expect(package.price, 2500000);
});

// Test services
test('AuthService.login should return user on success', () async {
  final authService = AuthService(mockApiClient);
  final result = await authService.login('test@example.com', 'password');
  expect(result.user.email, 'test@example.com');
});
```

### Widget Tests
```dart
testWidgets('PackageCard should display package info', (tester) async {
  await tester.pumpWidget(
    MaterialApp(
      home: PackageCard(package: mockPackage),
    ),
  );

  expect(find.text('Executive Package'), findsOneWidget);
  expect(find.text('Rp 2,500,000'), findsOneWidget);
});
```

### Integration Tests
```dart
testWidgets('Complete booking flow', (tester) async {
  // 1. Browse packages
  await tester.tap(find.byType(PackageCard).first);
  await tester.pumpAndSettle();

  // 2. View package detail
  expect(find.text('Book Now'), findsOneWidget);
  await tester.tap(find.text('Book Now'));
  await tester.pumpAndSettle();

  // 3. Fill booking form
  await tester.enterText(find.byKey(Key('name')), 'John Doe');
  await tester.tap(find.text('Continue'));
  await tester.pumpAndSettle();

  // 4. Verify booking created
  expect(find.text('Booking Confirmed'), findsOneWidget);
});
```

---

## 📱 Platform-Specific Features

### iOS
```yaml
# ios/Runner/Info.plist
<key>NSCameraUsageDescription</key>
<string>We need camera access to scan QR codes</string>
<key>NSPhotoLibraryUsageDescription</key>
<string>We need photo library access to upload images</string>
```

### Android
```xml
<!-- android/app/src/main/AndroidManifest.xml -->
<uses-permission android:name="android.permission.INTERNET" />
<uses-permission android:name="android.permission.CAMERA" />
<uses-permission android:name="android.permission.READ_EXTERNAL_STORAGE" />
```

---

## 🚀 Deployment

### Environment Configuration
```dart
// lib/core/config/env_config.dart
class EnvConfig {
  static const String _env = String.fromEnvironment('ENV', defaultValue: 'dev');

  static String get baseUrl {
    switch (_env) {
      case 'prod':
        return 'https://api.mcv3.com';
      case 'staging':
        return 'https://api-staging.mcv3.com';
      default:
        return 'http://localhost:8000';
    }
  }

  static String get midtransClientKey {
    switch (_env) {
      case 'prod':
        return 'YOUR_PRODUCTION_CLIENT_KEY';
      default:
        return 'YOUR_SANDBOX_CLIENT_KEY';
    }
  }
}
```

### Build Commands
```bash
# Development
flutter run --dart-define=ENV=dev

# Staging
flutter build apk --dart-define=ENV=staging
flutter build ios --dart-define=ENV=staging

# Production
flutter build apk --release --dart-define=ENV=prod
flutter build ios --release --dart-define=ENV=prod
```

---

## 📊 Analytics & Monitoring

### Firebase Analytics
```dart
class AnalyticsService {
  final FirebaseAnalytics _analytics = FirebaseAnalytics.instance;

  // Track screens
  Future<void> logScreenView(String screenName) async {
    await _analytics.logScreenView(screenName: screenName);
  }

  // Track events
  Future<void> logBookingCreated({
    required int packageId,
    required double amount,
  }) async {
    await _analytics.logEvent(
      name: 'booking_created',
      parameters: {
        'package_id': packageId,
        'amount': amount,
      },
    );
  }

  // Track purchases
  Future<void> logPurchase({
    required String transactionId,
    required double value,
  }) async {
    await _analytics.logPurchase(
      transactionId: transactionId,
      value: value,
      currency: 'IDR',
    );
  }
}
```

### Crashlytics
```dart
void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await Firebase.initializeApp();

  FlutterError.onError = FirebaseCrashlytics.instance.recordFlutterError;

  runApp(MyApp());
}
```

---

## 🔄 Offline Support

### Caching Strategy
```dart
class CacheManager {
  // Cache packages for 1 hour
  Future<void> cachePackages(List<McuPackage> packages) async {
    await hive.put('packages', packages);
    await hive.put('packages_cached_at', DateTime.now());
  }

  Future<List<McuPackage>?> getCachedPackages() async {
    final cachedAt = hive.get('packages_cached_at') as DateTime?;
    if (cachedAt != null &&
        DateTime.now().difference(cachedAt).inHours < 1) {
      return hive.get('packages') as List<McuPackage>?;
    }
    return null;
  }
}
```

### Sync Strategy
```dart
class SyncService {
  Future<void> syncPendingActions() async {
    final pendingActions = await hive.get('pending_actions') ?? [];

    for (var action in pendingActions) {
      try {
        await executeAction(action);
        pendingActions.remove(action);
      } catch (e) {
        // Keep in queue
      }
    }

    await hive.put('pending_actions', pendingActions);
  }
}
```

---

## 📖 API Documentation (Yang Perlu Dibuat)

Backend perlu membuat API documentation menggunakan:
- **Swagger/OpenAPI**: Auto-generated documentation
- **Postman Collection**: For testing
- **API Versioning**: `/api/v1/`, `/api/v2/`

Example Laravel setup:
```bash
composer require darkaonline/l5-swagger
php artisan l5-swagger:generate
```

---

## 🎯 Milestones & Timeline

### Phase 1: MVP (4-6 weeks)
- [ ] Authentication & Authorization
- [ ] MCU Marketplace browsing
- [ ] Booking creation
- [ ] Payment integration (Midtrans)
- [ ] My Bookings list

### Phase 2: Enhanced Features (3-4 weeks)
- [ ] Review & Rating system
- [ ] Promo Code application
- [ ] User Profile & Settings
- [ ] Push Notifications
- [ ] QR Code generation

### Phase 3: B2B Features (3-4 weeks)
- [ ] Corporate Portal
- [ ] Employee management
- [ ] Bulk bookings
- [ ] Health reports

### Phase 4: Polish & Launch (2-3 weeks)
- [ ] Medical Results viewing
- [ ] Offline support
- [ ] Performance optimization
- [ ] Testing & bug fixes
- [ ] App store submission

---

## 📞 Support & Resources

### Documentation Links
- Backend API: `https://api.mcv3.com/docs`
- Design System: Figma link
- Postman Collection: Link to collection

### Team Contacts
- Backend Team: backend@mcv3.com
- Mobile Team: mobile@mcv3.com
- Design Team: design@mcv3.com

---

## ⚠️ Important Notes

1. **Multi-Tenancy**: Semua API request harus include tenant context (via subdomain atau header)
2. **Authentication**: Gunakan Bearer token untuk semua authenticated endpoints
3. **Error Handling**: Backend akan return standard error format:
   ```json
   {
     "success": false,
     "message": "Error message",
     "errors": {
       "field": ["Validation error"]
     }
   }
   ```
4. **Pagination**: Semua list endpoints menggunakan pagination:
   ```json
   {
     "data": [...],
     "meta": {
       "current_page": 1,
       "total_pages": 10,
       "per_page": 20,
       "total": 200
     }
   }
   ```
5. **Rate Limiting**: API memiliki rate limit (contoh: 60 requests per minute)

---

## 🎉 Conclusion

Dokumen ini adalah panduan lengkap untuk membangun Flutter mobile app yang terintegrasi dengan backend MCv3. Semua fitur yang dijelaskan di sini sudah tersedia di backend dan siap untuk digunakan.

**Next Steps**:
1. Backend team: Buat RESTful API endpoints sesuai spesifikasi
2. Mobile team: Setup project structure dan dependencies
3. Design team: Finalize UI/UX designs
4. Start development following the milestones

Good luck! 🚀
