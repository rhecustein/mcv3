# 📱 SehatCert Mobile Apps

Monorepo for SehatCert mobile applications built with Flutter.

## 🎯 Overview

This repository contains three Flutter applications:

1. **Outlet App** - For healthcare outlet staff to issue certificates
2. **Patient App** - For patients to view and manage their health certificates
3. **Company App** - For company admins to view statistics and manage employees

## 🏗️ Architecture

- **Framework**: Flutter 3.19+ (Dart 3.3+)
- **Architecture Pattern**: Clean Architecture + BLoC
- **State Management**: flutter_bloc + hydrated_bloc
- **Navigation**: go_router
- **API Client**: dio + retrofit
- **Dependency Injection**: get_it + injectable
- **Code Generation**: freezed + json_serializable

## 📦 Project Structure

```
sehatcert_mobile/
├── apps/
│   ├── outlet_app/          # Outlet Staff App
│   ├── patient_app/         # Patient App
│   └── company_app/         # Company Admin App
├── packages/
│   ├── core/                # Core utilities, constants
│   ├── data/                # Data layer (API, models, repositories)
│   ├── domain/              # Domain layer (entities, use cases)
│   └── presentation/        # Shared UI components
├── .github/
│   └── workflows/           # CI/CD pipelines
├── melos.yaml               # Monorepo configuration
└── analysis_options.yaml    # Dart linting rules
```

## 🚀 Getting Started

### Prerequisites

- Flutter SDK (>= 3.19.0)
- Dart SDK (>= 3.3.0)
- Xcode (for iOS development, macOS only)
- Android Studio (for Android development)

### Installation

1. **Install Flutter**

```bash
# macOS
brew install flutter

# Linux/Windows
# Download from https://flutter.dev/docs/get-started/install
```

2. **Verify Flutter installation**

```bash
flutter doctor
```

3. **Install Melos (monorepo management)**

```bash
dart pub global activate melos
```

4. **Clone repository**

```bash
git clone https://github.com/sehatcert/mobile.git
cd sehatcert_mobile
```

5. **Bootstrap monorepo**

```bash
melos bootstrap
```

This will:
- Install dependencies for all packages
- Run code generation
- Link local packages

## 🛠️ Development

### Available Melos Commands

```bash
# Code generation
melos run codegen              # Generate code once
melos run codegen:watch        # Watch and generate code

# Testing
melos run test                 # Run all tests
melos run test:unit            # Run unit tests only
melos run test:widget          # Run widget tests only
melos run test:integration     # Run integration tests
melos run test:coverage        # Run tests with coverage

# Code quality
melos run analyze              # Run Flutter analyzer
melos run format               # Format all code
melos run format:check         # Check if code is formatted

# Cleanup
melos clean                    # Clean all packages
melos run clean:deep           # Deep clean and re-bootstrap

# Build
melos run build:android        # Build Android APKs
melos run build:ios            # Build iOS apps
melos run build:appbundle      # Build Android App Bundle

# Run apps
melos run run:outlet           # Run Outlet App
melos run run:patient          # Run Patient App
melos run run:company          # Run Company App
```

### Running Individual Apps

```bash
# Outlet Staff App
cd apps/outlet_app
flutter run

# Patient App
cd apps/patient_app
flutter run

# Company Admin App
cd apps/company_app
flutter run
```

### Running on Specific Device

```bash
# List available devices
flutter devices

# Run on specific device
flutter run -d <device-id>

# Run on iOS simulator
flutter run -d iPhone

# Run on Android emulator
flutter run -d emulator-5554
```

## 🧪 Testing

### Unit Tests

```bash
# Run all unit tests
melos run test:unit

# Run tests for specific package
cd packages/data
flutter test test/unit
```

### Widget Tests

```bash
# Run all widget tests
melos run test:widget

# Run widget tests for specific app
cd apps/outlet_app
flutter test test/widget
```

### Integration Tests

```bash
# Run integration tests
cd apps/outlet_app
flutter test integration_test
```

### Code Coverage

```bash
# Generate coverage report
melos run test:coverage

# View coverage (requires lcov)
genhtml coverage/lcov.info -o coverage/html
open coverage/html/index.html
```

## 🏭 Building for Production

### Android

```bash
# Build APK (for testing)
cd apps/outlet_app
flutter build apk --release

# Build App Bundle (for Play Store)
flutter build appbundle --release

# Build with obfuscation
flutter build appbundle --release --obfuscate --split-debug-info=build/debug-info
```

### iOS

```bash
# Build iOS app
cd apps/outlet_app
flutter build ios --release

# Build for specific configuration
flutter build ios --release --flavor production
```

## 📝 Code Generation

This project uses code generation for:

- **freezed**: Immutable data classes
- **json_serializable**: JSON serialization
- **injectable**: Dependency injection

### Generate code once

```bash
melos run codegen
```

### Watch mode (auto-generate on file changes)

```bash
melos run codegen:watch
```

## 🎨 Code Style

This project follows [Very Good Analysis](https://pub.dev/packages/very_good_analysis) linting rules.

### Format code

```bash
melos run format
```

### Check formatting

```bash
melos run format:check
```

### Analyze code

```bash
melos run analyze
```

## 🔧 Configuration

### Environment Variables

Each app uses environment variables for configuration:

```bash
# Copy example env file
cp .env.example .env

# Edit with your values
API_BASE_URL=https://api.sehatcert.com/v1
API_TIMEOUT=30000
```

### Firebase Setup

1. Create Firebase project
2. Add iOS and Android apps
3. Download configuration files:
   - `google-services.json` → `android/app/`
   - `GoogleService-Info.plist` → `ios/Runner/`

## 🚢 Continuous Integration/Deployment

### GitHub Actions

This project includes GitHub Actions workflows for:

- ✅ Linting and code analysis
- ✅ Unit and widget tests
- ✅ Build verification
- ✅ Beta distribution (Firebase App Distribution)
- ✅ Production release (App Store & Play Store)

### Fastlane (iOS & Android)

Fastlane scripts are available for:

- Beta distribution
- App Store / Play Store deployment
- Certificate management

```bash
# iOS
cd apps/outlet_app/ios
fastlane beta

# Android
cd apps/outlet_app/android
fastlane beta
```

## 📚 Documentation

- [Architecture Guide](docs/ARCHITECTURE.md)
- [Contributing Guidelines](docs/CONTRIBUTING.md)
- [API Documentation](docs/API.md)
- [Deployment Guide](docs/DEPLOYMENT.md)

## 🤝 Contributing

1. Create a feature branch (`git checkout -b feature/amazing-feature`)
2. Commit your changes (`git commit -m 'Add amazing feature'`)
3. Push to the branch (`git push origin feature/amazing-feature`)
4. Open a Pull Request

Please ensure:
- All tests pass (`melos run test`)
- Code is formatted (`melos run format`)
- Code analysis passes (`melos run analyze`)
- Coverage is maintained or improved

## 📄 License

Copyright © 2026 SehatCert. All rights reserved.

## 👥 Team

- **Project Manager**: [Name]
- **Lead Flutter Developer**: [Name]
- **Flutter Developer**: [Name]
- **Backend Developer**: [Name]
- **UI/UX Designer**: [Name]
- **QA Engineer**: [Name]

## 📞 Support

- Email: dev@sehatcert.com
- Slack: #mobile-dev
- Documentation: https://docs.sehatcert.com

---

Built with ❤️ using Flutter
