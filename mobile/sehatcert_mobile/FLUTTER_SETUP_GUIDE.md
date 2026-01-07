# 🚀 Flutter Setup & Development Guide

Complete guide for setting up and developing SehatCert Flutter mobile applications.

## 📋 Table of Contents

1. [Prerequisites](#prerequisites)
2. [Installation](#installation)
3. [Project Structure](#project-structure)
4. [Clean Architecture](#clean-architecture)
5. [Development Workflow](#development-workflow)
6. [Code Generation](#code-generation)
7. [State Management (BLoC)](#state-management-bloc)
8. [Dependency Injection](#dependency-injection)
9. [Testing](#testing)
10. [Building & Deployment](#building--deployment)

---

## ✅ Prerequisites

### Required Software

1. **Flutter SDK (>= 3.19.0)**
   ```bash
   # macOS
   brew install flutter

   # Verify installation
   flutter doctor
   ```

2. **Dart SDK (>= 3.3.0)** - Included with Flutter

3. **IDE (choose one)**
   - **Android Studio** (Recommended)
     - Install Flutter plugin
     - Install Dart plugin
   - **VS Code**
     - Install Flutter extension
     - Install Dart extension

4. **Xcode** (macOS only, for iOS development)
   ```bash
   # Install from App Store
   # Then run:
   sudo xcode-select --switch /Applications/Xcode.app/Contents/Developer
   sudo xcodebuild -runFirstLaunch
   ```

5. **Android Studio** (for Android development)
   - Install Android SDK
   - Install Android SDK Command-line Tools
   - Create an Android emulator

6. **Melos** (monorepo management)
   ```bash
   dart pub global activate melos
   ```

### Optional But Recommended

- **FVM** (Flutter Version Management)
  ```bash
  dart pub global activate fvm
  fvm install 3.19.0
  fvm use 3.19.0
  ```

- **Fastlane** (deployment automation)
  ```bash
  # macOS
  brew install fastlane

  # Linux/Windows
  gem install fastlane
  ```

---

## 📦 Installation

### 1. Clone Repository

```bash
git clone https://github.com/sehatcert/mobile.git
cd sehatcert_mobile
```

### 2. Bootstrap Monorepo

```bash
# This will:
# - Install dependencies for all packages
# - Run code generation
# - Link local packages
melos bootstrap
```

### 3. Setup Environment Variables

```bash
# Copy example env file
cp .env.example .env

# Edit with your values
nano .env
```

Example `.env`:
```env
API_BASE_URL=https://api.sehatcert.com/v1
API_TIMEOUT=30000
ENABLE_LOGGING=true
```

### 4. Verify Setup

```bash
# Check Flutter installation
flutter doctor

# Run analyzer
melos run analyze

# Run tests
melos run test
```

---

## 📂 Project Structure

### Monorepo Structure

```
sehatcert_mobile/
├── apps/                       # Flutter applications
│   ├── outlet_app/            # Outlet Staff App
│   ├── patient_app/           # Patient App
│   └── company_app/           # Company Admin App
├── packages/                   # Shared packages
│   ├── core/                  # Core utilities & constants
│   ├── data/                  # Data layer (API, models, repos impl)
│   ├── domain/                # Domain layer (entities, use cases)
│   └── presentation/          # Shared UI components
├── melos.yaml                 # Monorepo configuration
├── analysis_options.yaml      # Linting rules
└── README.md
```

### App Structure (Clean Architecture)

```
apps/outlet_app/
├── lib/
│   ├── core/
│   │   ├── di/                # Dependency Injection
│   │   │   ├── injection.dart
│   │   │   └── injection.config.dart (generated)
│   │   ├── router/            # Navigation (go_router)
│   │   │   ├── app_router.dart
│   │   │   └── routes.dart
│   │   └── theme/             # App theming
│   │       ├── app_theme.dart
│   │       └── app_colors.dart
│   ├── features/              # Feature modules
│   │   ├── auth/
│   │   │   ├── data/
│   │   │   │   ├── datasources/
│   │   │   │   │   ├── auth_remote_datasource.dart
│   │   │   │   │   └── auth_local_datasource.dart
│   │   │   │   ├── models/
│   │   │   │   │   ├── login_request.dart
│   │   │   │   │   ├── login_response.dart
│   │   │   │   │   └── user_model.dart
│   │   │   │   └── repositories/
│   │   │   │       └── auth_repository_impl.dart
│   │   │   ├── domain/
│   │   │   │   ├── entities/
│   │   │   │   │   └── user.dart
│   │   │   │   ├── repositories/
│   │   │   │   │   └── auth_repository.dart
│   │   │   │   └── usecases/
│   │   │   │       ├── login.dart
│   │   │   │       ├── logout.dart
│   │   │   │       └── get_current_user.dart
│   │   │   └── presentation/
│   │   │       ├── bloc/
│   │   │       │   ├── auth_bloc.dart
│   │   │       │   ├── auth_event.dart
│   │   │       │   └── auth_state.dart
│   │   │       ├── pages/
│   │   │       │   ├── login_page.dart
│   │   │       │   └── splash_page.dart
│   │   │       └── widgets/
│   │   │           ├── login_form.dart
│   │   │           └── password_field.dart
│   │   ├── dashboard/
│   │   ├── patients/
│   │   └── certificates/
│   └── main.dart              # Entry point
├── test/                      # Unit & widget tests
│   ├── features/
│   │   └── auth/
│   │       ├── data/
│   │       ├── domain/
│   │       └── presentation/
│   └── helpers/
├── integration_test/          # Integration tests
└── pubspec.yaml
```

---

## 🏗️ Clean Architecture

### Layers

#### 1. **Presentation Layer**
- **Responsibility**: UI logic, state management
- **Components**: BLoC, Pages, Widgets
- **Dependencies**: Domain layer only

```dart
// Example: AuthBloc
class AuthBloc extends Bloc<AuthEvent, AuthState> {
  final Login loginUseCase;

  AuthBloc(this.loginUseCase) : super(AuthInitial()) {
    on<LoginRequested>(_onLoginRequested);
  }

  Future<void> _onLoginRequested(
    LoginRequested event,
    Emitter<AuthState> emit,
  ) async {
    emit(AuthLoading());

    final result = await loginUseCase(LoginParams(
      email: event.email,
      password: event.password,
    ));

    result.fold(
      (failure) => emit(AuthError(failure.message)),
      (user) => emit(AuthAuthenticated(user)),
    );
  }
}
```

#### 2. **Domain Layer**
- **Responsibility**: Business logic
- **Components**: Entities, Use Cases, Repository Interfaces
- **Dependencies**: None (pure Dart)

```dart
// Example: Login Use Case
class Login extends UseCase<User, LoginParams> {
  final AuthRepository repository;

  Login(this.repository);

  @override
  Future<Either<Failure, User>> call(LoginParams params) {
    return repository.login(params.email, params.password);
  }
}

class LoginParams {
  final String email;
  final String password;

  LoginParams({required this.email, required this.password});
}
```

#### 3. **Data Layer**
- **Responsibility**: Data access & persistence
- **Components**: Repository Implementations, Data Sources, Models
- **Dependencies**: Domain layer

```dart
// Example: AuthRepositoryImpl
class AuthRepositoryImpl implements AuthRepository {
  final AuthRemoteDataSource remoteDataSource;
  final AuthLocalDataSource localDataSource;

  AuthRepositoryImpl({
    required this.remoteDataSource,
    required this.localDataSource,
  });

  @override
  Future<Either<Failure, User>> login(String email, String password) async {
    try {
      final userModel = await remoteDataSource.login(email, password);
      await localDataSource.cacheUser(userModel);
      return Right(userModel.toEntity());
    } on ServerException catch (e) {
      return Left(ServerFailure(message: e.message));
    } on NetworkException {
      return Left(NetworkFailure());
    }
  }
}
```

### Dependency Rule

- **Presentation** → **Domain** ← **Data**
- Inner layers don't know about outer layers
- Use interfaces (abstract classes) for dependencies

---

## 💻 Development Workflow

### 1. Create a New Feature

```bash
# Example: Creating "Patients" feature
cd apps/outlet_app/lib/features
mkdir -p patients/{data/{datasources,models,repositories},domain/{entities,usecases,repositories},presentation/{bloc,pages,widgets}}
```

### 2. Define Domain Layer (Inside-Out Approach)

**Step 1**: Create entity
```dart
// lib/features/patients/domain/entities/patient.dart
class Patient {
  final String id;
  final String name;
  final String nik;
  final DateTime birthDate;

  Patient({
    required this.id,
    required this.name,
    required this.nik,
    required this.birthDate,
  });
}
```

**Step 2**: Create repository interface
```dart
// lib/features/patients/domain/repositories/patient_repository.dart
abstract class PatientRepository {
  Future<Either<Failure, List<Patient>>> getPatients();
  Future<Either<Failure, Patient>> getPatientById(String id);
  Future<Either<Failure, Patient>> createPatient(Patient patient);
}
```

**Step 3**: Create use case
```dart
// lib/features/patients/domain/usecases/get_patients.dart
class GetPatients extends UseCase<List<Patient>, NoParams> {
  final PatientRepository repository;

  GetPatients(this.repository);

  @override
  Future<Either<Failure, List<Patient>>> call(NoParams params) {
    return repository.getPatients();
  }
}
```

### 3. Implement Data Layer

**Step 1**: Create model (with code generation)
```dart
// lib/features/patients/data/models/patient_model.dart
import 'package:freezed_annotation/freezed_annotation.dart';
import '../../domain/entities/patient.dart';

part 'patient_model.freezed.dart';
part 'patient_model.g.dart';

@freezed
class PatientModel with _$PatientModel {
  const factory PatientModel({
    required String id,
    required String name,
    required String nik,
    @JsonKey(name: 'birth_date') required String birthDate,
  }) = _PatientModel;

  factory PatientModel.fromJson(Map<String, dynamic> json) =>
      _$PatientModelFromJson(json);
}

// Extension to convert to entity
extension PatientModelX on PatientModel {
  Patient toEntity() {
    return Patient(
      id: id,
      name: name,
      nik: nik,
      birthDate: DateTime.parse(birthDate),
    );
  }
}
```

**Step 2**: Create data source
```dart
// lib/features/patients/data/datasources/patient_remote_datasource.dart
import 'package:retrofit/retrofit.dart';
import 'package:dio/dio.dart';

part 'patient_remote_datasource.g.dart';

@RestApi()
abstract class PatientRemoteDataSource {
  factory PatientRemoteDataSource(Dio dio) = _PatientRemoteDataSource;

  @GET('/patients')
  Future<List<PatientModel>> getPatients();

  @GET('/patients/{id}')
  Future<PatientModel> getPatientById(@Path('id') String id);

  @POST('/patients')
  Future<PatientModel> createPatient(@Body() PatientModel patient);
}
```

**Step 3**: Implement repository
```dart
// lib/features/patients/data/repositories/patient_repository_impl.dart
class PatientRepositoryImpl implements PatientRepository {
  final PatientRemoteDataSource remoteDataSource;

  PatientRepositoryImpl(this.remoteDataSource);

  @override
  Future<Either<Failure, List<Patient>>> getPatients() async {
    try {
      final models = await remoteDataSource.getPatients();
      final entities = models.map((m) => m.toEntity()).toList();
      return Right(entities);
    } on DioException catch (e) {
      return Left(ServerFailure(message: e.message));
    }
  }
}
```

### 4. Implement Presentation Layer (BLoC)

**Step 1**: Define events
```dart
// lib/features/patients/presentation/bloc/patient_event.dart
part of 'patient_bloc.dart';

@freezed
class PatientEvent with _$PatientEvent {
  const factory PatientEvent.loadPatients() = _LoadPatients;
  const factory PatientEvent.searchPatients(String query) = _SearchPatients;
}
```

**Step 2**: Define states
```dart
// lib/features/patients/presentation/bloc/patient_state.dart
part of 'patient_bloc.dart';

@freezed
class PatientState with _$PatientState {
  const factory PatientState.initial() = _Initial;
  const factory PatientState.loading() = _Loading;
  const factory PatientState.loaded(List<Patient> patients) = _Loaded;
  const factory PatientState.error(String message) = _Error;
}
```

**Step 3**: Implement BLoC
```dart
// lib/features/patients/presentation/bloc/patient_bloc.dart
class PatientBloc extends Bloc<PatientEvent, PatientState> {
  final GetPatients getPatients;

  PatientBloc({required this.getPatients}) : super(const PatientState.initial()) {
    on<_LoadPatients>(_onLoadPatients);
  }

  Future<void> _onLoadPatients(
    _LoadPatients event,
    Emitter<PatientState> emit,
  ) async {
    emit(const PatientState.loading());

    final result = await getPatients(const NoParams());

    result.fold(
      (failure) => emit(PatientState.error(failure.message ?? 'Error')),
      (patients) => emit(PatientState.loaded(patients)),
    );
  }
}
```

### 5. Register Dependencies (Dependency Injection)

```dart
// lib/core/di/injection.dart
@module
abstract class PatientsModule {
  @lazySingleton
  PatientRemoteDataSource providePatientRemoteDataSource(Dio dio) {
    return PatientRemoteDataSource(dio);
  }

  @lazySingleton
  PatientRepository providePatientRepository(
    PatientRemoteDataSource remoteDataSource,
  ) {
    return PatientRepositoryImpl(remoteDataSource);
  }

  @lazySingleton
  GetPatients provideGetPatients(PatientRepository repository) {
    return GetPatients(repository);
  }
}
```

### 6. Run Code Generation

```bash
# Generate code (freezed, json_serializable, injectable, retrofit)
cd apps/outlet_app
flutter pub run build_runner build --delete-conflicting-outputs

# Or watch mode (auto-generate on changes)
flutter pub run build_runner watch --delete-conflicting-outputs
```

### 7. Create UI

```dart
// lib/features/patients/presentation/pages/patient_list_page.dart
class PatientListPage extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return BlocProvider(
      create: (context) => getIt<PatientBloc>()
        ..add(const PatientEvent.loadPatients()),
      child: Scaffold(
        appBar: AppBar(title: Text('Patients')),
        body: BlocBuilder<PatientBloc, PatientState>(
          builder: (context, state) {
            return state.when(
              initial: () => SizedBox(),
              loading: () => Center(child: CircularProgressIndicator()),
              loaded: (patients) => ListView.builder(
                itemCount: patients.length,
                itemBuilder: (context, index) {
                  final patient = patients[index];
                  return ListTile(
                    title: Text(patient.name),
                    subtitle: Text(patient.nik),
                  );
                },
              ),
              error: (message) => Center(child: Text(message)),
            );
          },
        ),
      ),
    );
  }
}
```

---

## ⚙️ Code Generation

### Tools Used

1. **freezed** - Immutable data classes with union types
2. **json_serializable** - JSON serialization/deserialization
3. **injectable** - Dependency injection code generation
4. **retrofit** - Type-safe REST client

### Running Code Generation

```bash
# One-time generation (all packages)
melos run codegen

# Watch mode (auto-generate)
melos run codegen:watch

# For specific app
cd apps/outlet_app
flutter pub run build_runner build --delete-conflicting-outputs

# Watch mode for specific app
flutter pub run build_runner watch --delete-conflicting-outputs
```

### Generated Files

- `*.freezed.dart` - Freezed data classes
- `*.g.dart` - JSON serialization
- `injection.config.dart` - DI configuration
- `*_remote_datasource.g.dart` - Retrofit API clients

**Important**: Generated files are in `.gitignore` and will be regenerated on `melos bootstrap`.

---

## 🎯 State Management (BLoC)

### BLoC Pattern Overview

**BLoC** (Business Logic Component) separates business logic from UI.

**Flow**:
```
UI → Event → BLoC → State → UI
```

### Example: Authentication BLoC

```dart
// Events
@freezed
class AuthEvent with _$AuthEvent {
  const factory AuthEvent.loginRequested(String email, String password) = _LoginRequested;
  const factory AuthEvent.logoutRequested() = _LogoutRequested;
}

// States
@freezed
class AuthState with _$AuthState {
  const factory AuthState.initial() = _Initial;
  const factory AuthState.loading() = _Loading;
  const factory AuthState.authenticated(User user) = _Authenticated;
  const factory AuthState.unauthenticated() = _Unauthenticated;
  const factory AuthState.error(String message) = _Error;
}

// BLoC
class AuthBloc extends Bloc<AuthEvent, AuthState> {
  final Login loginUseCase;
  final Logout logoutUseCase;

  AuthBloc({
    required this.loginUseCase,
    required this.logoutUseCase,
  }) : super(const AuthState.initial()) {
    on<_LoginRequested>(_onLoginRequested);
    on<_LogoutRequested>(_onLogoutRequested);
  }

  Future<void> _onLoginRequested(
    _LoginRequested event,
    Emitter<AuthState> emit,
  ) async {
    emit(const AuthState.loading());

    final result = await loginUseCase(LoginParams(
      email: event.email,
      password: event.password,
    ));

    result.fold(
      (failure) => emit(AuthState.error(failure.message ?? 'Login failed')),
      (user) => emit(AuthState.authenticated(user)),
    );
  }
}
```

### Using BLoC in UI

```dart
// Provide BLoC
BlocProvider(
  create: (context) => getIt<AuthBloc>(),
  child: LoginPage(),
)

// Dispatch events
context.read<AuthBloc>().add(AuthEvent.loginRequested(email, password));

// Listen to states
BlocBuilder<AuthBloc, AuthState>(
  builder: (context, state) {
    return state.when(
      initial: () => LoginForm(),
      loading: () => CircularProgressIndicator(),
      authenticated: (user) => DashboardPage(),
      unauthenticated: () => LoginForm(),
      error: (message) => ErrorWidget(message),
    );
  },
)

// Or use BlocListener for side effects
BlocListener<AuthBloc, AuthState>(
  listener: (context, state) {
    state.whenOrNull(
      authenticated: (user) {
        // Navigate to dashboard
        context.go('/dashboard');
      },
      error: (message) {
        // Show snackbar
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(message)),
        );
      },
    );
  },
  child: LoginForm(),
)
```

### Persisting State (HydratedBloc)

```dart
// Automatically persist state
class SettingsBloc extends HydratedBloc<SettingsEvent, SettingsState> {
  SettingsBloc() : super(SettingsState.initial()) {
    on<ThemeChanged>(_onThemeChanged);
  }

  @override
  SettingsState? fromJson(Map<String, dynamic> json) {
    return SettingsState.fromJson(json);
  }

  @override
  Map<String, dynamic>? toJson(SettingsState state) {
    return state.toJson();
  }
}
```

---

## 💉 Dependency Injection

### Setup (get_it + injectable)

**Step 1**: Create injection file
```dart
// lib/core/di/injection.dart
import 'package:get_it/get_it.dart';
import 'package:injectable/injectable.dart';

import 'injection.config.dart';

final getIt = GetIt.instance;

@InjectableInit(
  initializerName: 'init',
  preferRelativeImports: true,
  asExtension: true,
)
Future<void> configureDependencies() async {
  await getIt.init();
}
```

**Step 2**: Register dependencies
```dart
// lib/core/di/injection.dart

@module
abstract class AppModule {
  // Register Dio
  @lazySingleton
  Dio provideDio() {
    final dio = Dio(BaseOptions(
      baseUrl: AppConstants.apiBaseUrl,
      connectTimeout: Duration(milliseconds: AppConstants.apiTimeout),
    ));

    dio.interceptors.add(PrettyDioLogger());

    return dio;
  }

  // Register storage
  @lazySingleton
  FlutterSecureStorage provideSecureStorage() {
    return const FlutterSecureStorage();
  }
}
```

**Step 3**: Initialize in main.dart
```dart
void main() async {
  WidgetsFlutterBinding.ensureInitialized();

  // Initialize DI
  await configureDependencies();

  runApp(MyApp());
}
```

**Step 4**: Use dependencies
```dart
// Resolve dependency
final authBloc = getIt<AuthBloc>();

// Or in BlocProvider
BlocProvider(
  create: (context) => getIt<AuthBloc>(),
  child: LoginPage(),
)
```

### Annotations

- `@injectable` - Register as injectable
- `@lazySingleton` - Singleton (lazy initialization)
- `@singleton` - Singleton (eager initialization)
- `@module` - Module for third-party dependencies

---

## 🧪 Testing

### Unit Tests

```dart
// test/features/auth/domain/usecases/login_test.dart
void main() {
  late Login useCase;
  late MockAuthRepository mockRepository;

  setUp(() {
    mockRepository = MockAuthRepository();
    useCase = Login(mockRepository);
  });

  test('should return User when login is successful', () async {
    // Arrange
    final tUser = User(id: '1', name: 'John', email: 'john@example.com');
    when(() => mockRepository.login(any(), any()))
        .thenAnswer((_) async => Right(tUser));

    // Act
    final result = await useCase(LoginParams(
      email: 'john@example.com',
      password: 'password123',
    ));

    // Assert
    expect(result, Right(tUser));
    verify(() => mockRepository.login('john@example.com', 'password123')).called(1);
  });
}
```

### Widget Tests

```dart
// test/features/auth/presentation/pages/login_page_test.dart
void main() {
  testWidgets('should show CircularProgressIndicator when state is loading',
      (tester) async {
    // Arrange
    final mockBloc = MockAuthBloc();
    whenListen(
      mockBloc,
      Stream.fromIterable([const AuthState.loading()]),
      initialState: const AuthState.initial(),
    );

    // Act
    await tester.pumpWidget(
      MaterialApp(
        home: BlocProvider<AuthBloc>(
          create: (_) => mockBloc,
          child: LoginPage(),
        ),
      ),
    );
    await tester.pump();

    // Assert
    expect(find.byType(CircularProgressIndicator), findsOneWidget);
  });
}
```

### BLoC Tests

```dart
// test/features/auth/presentation/bloc/auth_bloc_test.dart
void main() {
  late AuthBloc authBloc;
  late MockLogin mockLogin;

  setUp(() {
    mockLogin = MockLogin();
    authBloc = AuthBloc(loginUseCase: mockLogin);
  });

  blocTest<AuthBloc, AuthState>(
    'emits [Loading, Authenticated] when login is successful',
    build: () {
      when(() => mockLogin(any()))
          .thenAnswer((_) async => Right(tUser));
      return authBloc;
    },
    act: (bloc) => bloc.add(AuthEvent.loginRequested('email', 'password')),
    expect: () => [
      const AuthState.loading(),
      AuthState.authenticated(tUser),
    ],
  );
}
```

### Running Tests

```bash
# Run all tests (all packages)
melos run test

# Run tests for specific app
cd apps/outlet_app
flutter test

# Run with coverage
flutter test --coverage
genhtml coverage/lcov.info -o coverage/html
open coverage/html/index.html

# Run integration tests
cd apps/outlet_app
flutter test integration_test
```

---

## 🚀 Building & Deployment

### Android

#### Debug Build

```bash
cd apps/outlet_app
flutter build apk --debug
```

#### Release Build

```bash
# Generate keystore (first time only)
keytool -genkey -v -keystore ~/sehatcert-outlet.jks -keyalg RSA -keysize 2048 -validity 10000 -alias sehatcert

# Create key.properties
# android/key.properties
storePassword=<password>
keyPassword=<password>
keyAlias=sehatcert
storeFile=<path-to-jks>

# Build release APK
flutter build apk --release --obfuscate --split-debug-info=build/debug-info

# Build App Bundle (for Play Store)
flutter build appbundle --release --obfuscate --split-debug-info=build/debug-info
```

### iOS

#### Debug Build

```bash
cd apps/outlet_app
flutter build ios --debug
```

#### Release Build

```bash
# First, configure signing in Xcode
open ios/Runner.xcworkspace

# Then build
flutter build ios --release --obfuscate --split-debug-info=build/debug-info

# Or build for specific flavor
flutter build ios --release --flavor production
```

### Fastlane Deployment

```bash
# Android
cd apps/outlet_app/android
fastlane beta        # Deploy to Firebase App Distribution
fastlane playstore   # Deploy to Google Play Store

# iOS
cd apps/outlet_app/ios
fastlane beta        # Deploy to TestFlight
fastlane appstore    # Deploy to App Store
```

---

## 📚 Additional Resources

- [Flutter Documentation](https://flutter.dev/docs)
- [BLoC Documentation](https://bloclibrary.dev)
- [Clean Architecture](https://blog.cleancoder.com/uncle-bob/2012/08/13/the-clean-architecture.html)
- [Melos Documentation](https://melos.invertase.dev)
- [Freezed Documentation](https://pub.dev/packages/freezed)
- [Injectable Documentation](https://pub.dev/packages/injectable)
- [Retrofit Documentation](https://pub.dev/packages/retrofit)

---

## 🐛 Troubleshooting

### Common Issues

**Issue**: `flutter doctor` shows issues
```bash
# Run and follow recommendations
flutter doctor -v
```

**Issue**: Code generation not working
```bash
# Clean and regenerate
flutter clean
flutter pub get
flutter pub run build_runner clean
flutter pub run build_runner build --delete-conflicting-outputs
```

**Issue**: Melos commands not found
```bash
# Ensure Dart global bins are in PATH
export PATH="$PATH":"$HOME/.pub-cache/bin"

# Or reinstall Melos
dart pub global activate melos
```

**Issue**: iOS build fails
```bash
# Clean iOS build
cd apps/outlet_app/ios
rm -rf Pods Podfile.lock .symlinks
pod install --repo-update
```

**Issue**: Android build fails
```bash
# Clean Android build
cd apps/outlet_app/android
./gradlew clean
flutter clean
flutter pub get
```

---

## 📞 Support

For questions or issues:
- Email: dev@sehatcert.com
- Slack: #mobile-dev
- GitHub Issues: [Create an issue](https://github.com/sehatcert/mobile/issues)

---

**Happy Coding! 🎉**
