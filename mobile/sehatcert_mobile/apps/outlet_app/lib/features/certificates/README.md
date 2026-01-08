# Certificate Management Feature

Complete implementation of Certificate Management feature following Clean Architecture principles.

## 📁 Structure

```
certificates/
├── domain/              # Business logic layer
│   ├── entities/        # Core business entities
│   │   ├── certificate.dart
│   │   ├── pdf_metadata.dart
│   │   ├── compression_info.dart
│   │   ├── patient_info.dart
│   │   ├── certificate_info.dart
│   │   └── doctor_info.dart
│   ├── repositories/    # Repository interfaces
│   │   └── certificate_repository.dart
│   └── usecases/        # Use cases
│       ├── get_certificates.dart
│       ├── get_certificate_detail.dart
│       ├── download_certificate.dart
│       ├── search_certificates.dart
│       ├── get_pdf_url.dart
│       └── get_storage_info.dart
├── data/                # Data layer
│   ├── models/          # Freezed data models
│   │   ├── certificate_model.dart
│   │   ├── pdf_metadata_model.dart
│   │   ├── compression_info_model.dart
│   │   ├── patient_info_model.dart
│   │   ├── certificate_info_model.dart
│   │   ├── doctor_info_model.dart
│   │   ├── paginated_response_model.dart
│   │   ├── storage_info_model.dart
│   │   └── pdf_url_data_model.dart
│   ├── datasources/     # Data sources
│   │   ├── certificate_remote_datasource.dart (Retrofit)
│   │   └── certificate_local_datasource.dart (Cache)
│   └── repositories/    # Repository implementations
│       └── certificate_repository_impl.dart
├── presentation/        # Presentation layer
│   ├── bloc/            # BLoC state management
│   │   ├── certificate_bloc.dart
│   │   ├── certificate_event.dart
│   │   └── certificate_state.dart
│   ├── pages/           # UI pages
│   │   ├── certificates_list_page.dart
│   │   └── certificate_detail_page.dart
│   └── widgets/         # Reusable widgets
│       ├── certificate_card.dart
│       └── storage_info_widget.dart
└── di/                  # Dependency injection
    └── certificate_module.dart
```

## ✨ Features

### 1. Certificate List
- ✅ Paginated list of certificates (20 per page)
- ✅ Pull-to-refresh functionality
- ✅ Infinite scroll for loading more
- ✅ Search by code, name, or NIK
- ✅ Real-time search with debouncing
- ✅ Cached data for offline viewing
- ✅ Loading states and error handling

### 2. Certificate Detail
- ✅ Complete certificate information
- ✅ Patient details (name, NIK, gender, DOB, age, company)
- ✅ Certificate info (type, status, result, dates)
- ✅ PDF metadata (size, compression info)
- ✅ Doctor information
- ✅ Download PDF functionality
- ✅ Share functionality (placeholder)

### 3. Storage Information
- ✅ Total certificates count
- ✅ Total storage used (formatted)
- ✅ Compressed PDFs count
- ✅ Compression ratio percentage
- ✅ Bottom sheet modal presentation

### 4. Offline Support
- ✅ Certificate list caching (15 minutes TTL)
- ✅ Certificate detail caching
- ✅ Storage info caching
- ✅ Fallback to cache on network errors

## 🔌 API Integration

### Endpoints Used
All endpoints require Sanctum authentication:

```dart
GET  /api/v1/certificates                   // List with pagination
GET  /api/v1/certificates/search?q=query    // Search
GET  /api/v1/certificates/{id}              // Detail
GET  /api/v1/certificates/{id}/download     // Download PDF
GET  /api/v1/certificates/{id}/pdf-url      // Get PDF URL
GET  /api/v1/certificates/storage/info      // Storage stats
```

## 🚀 Setup Instructions

### 1. Run Code Generation

```bash
cd apps/outlet_app
flutter pub get
flutter pub run build_runner build --delete-conflicting-outputs
```

This will generate:
- Freezed models (*.freezed.dart, *.g.dart)
- Retrofit API clients (*.g.dart)
- Injectable dependency injection (*.config.dart)
- BLoC files (*.freezed.dart)

### 2. Add Routes

Add certificate routes to your app router:

```dart
// In your router configuration
'/certificates': (context) => BlocProvider(
  create: (context) => getIt<CertificateBloc>(),
  child: const CertificatesListPage(),
),
'/certificates/detail': (context) {
  final id = ModalRoute.of(context)!.settings.arguments as String;
  return BlocProvider(
    create: (context) => getIt<CertificateBloc>(),
    child: CertificateDetailPage(certificateId: id),
  );
},
```

### 3. Navigation

Navigate to certificates from your dashboard:

```dart
// Navigate to list
Navigator.pushNamed(context, '/certificates');

// Navigate to detail
Navigator.pushNamed(
  context,
  '/certificates/detail',
  arguments: certificateId,
);
```

## 📦 Dependencies

Required packages (should already be in pubspec.yaml):

```yaml
dependencies:
  # State management
  flutter_bloc: ^8.1.3

  # Functional programming
  dartz: ^0.10.1

  # Code generation
  freezed_annotation: ^2.4.1
  json_annotation: ^4.8.1
  injectable: ^2.3.2

  # HTTP & API
  dio: ^5.4.0
  retrofit: ^4.0.3

  # Storage
  shared_preferences: ^2.2.2
  path_provider: ^2.1.1

  # Utils
  equatable: ^2.0.5
  intl: ^0.18.1

dev_dependencies:
  # Code generation
  build_runner: ^2.4.6
  freezed: ^2.4.5
  json_serializable: ^6.7.1
  injectable_generator: ^2.4.1
  retrofit_generator: ^8.0.4
```

## 🎯 Usage Examples

### Load Certificates

```dart
// In your widget
context.read<CertificateBloc>().add(
  const CertificateEvent.loadCertificates(),
);
```

### Search Certificates

```dart
context.read<CertificateBloc>().add(
  CertificateEvent.searchCertificates(query: 'john'),
);
```

### Download Certificate

```dart
context.read<CertificateBloc>().add(
  CertificateEvent.downloadCertificate(id: certificateId),
);
```

### Load Storage Info

```dart
context.read<CertificateBloc>().add(
  const CertificateEvent.loadStorageInfo(),
);
```

## 🔄 BLoC States

```dart
sealed class CertificateState {
  initial()                                   // Initial state
  loading()                                   // Loading first page
  loadingMore(certificates)                   // Loading next page
  loadingDetail()                             // Loading detail
  downloading()                               // Downloading PDF
  searching()                                 // Searching
  loaded(certificates, hasMore)               // Certificates loaded
  detailLoaded(certificate)                   // Detail loaded
  downloaded(filePath)                        // PDF downloaded
  searchResults(certificates)                 // Search results
  pdfUrlLoaded(pdfUrlData)                   // PDF URL loaded
  storageInfoLoaded(storageInfo)             // Storage info loaded
  error(message)                              // Error occurred
}
```

## 🧪 Testing

Run tests (when implemented):

```bash
flutter test test/features/certificates/
```

## 📝 TODO

- [ ] Add share functionality
- [ ] Add unit tests for use cases
- [ ] Add widget tests for pages
- [ ] Add integration tests
- [ ] Add PDF viewer page
- [ ] Add print functionality
- [ ] Add bulk download
- [ ] Add filters (date range, type, status)

## 🐛 Known Issues

None currently.

## 📄 License

Part of SehatCert Mobile Application.
