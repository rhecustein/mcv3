# Flutter Desktop Application - Design Document

## 1. Entity Relationship Diagram (ERD)

### 1.1 Entitas Utama

#### **User**
- `id` (Primary Key)
- `username` (String, Unique)
- `email` (String, Unique)
- `password` (String, Hashed)
- `full_name` (String)
- `role` (Enum: admin, user, guest)
- `avatar` (String, nullable)
- `created_at` (DateTime)
- `updated_at` (DateTime)
- `last_login` (DateTime, nullable)

#### **Settings**
- `id` (Primary Key)
- `user_id` (Foreign Key → User)
- `theme` (Enum: light, dark, system)
- `language` (String, default: 'id')
- `notifications_enabled` (Boolean, default: true)
- `auto_sync` (Boolean, default: false)
- `created_at` (DateTime)
- `updated_at` (DateTime)

#### **Task** (Contoh Entitas Bisnis)
- `id` (Primary Key)
- `user_id` (Foreign Key → User)
- `title` (String)
- `description` (Text, nullable)
- `status` (Enum: pending, in_progress, completed, cancelled)
- `priority` (Enum: low, medium, high)
- `due_date` (DateTime, nullable)
- `created_at` (DateTime)
- `updated_at` (DateTime)

#### **Category**
- `id` (Primary Key)
- `name` (String)
- `color` (String, hex color code)
- `icon` (String, icon name)
- `user_id` (Foreign Key → User)
- `created_at` (DateTime)
- `updated_at` (DateTime)

#### **TaskCategory** (Many-to-Many)
- `task_id` (Foreign Key → Task)
- `category_id` (Foreign Key → Category)

#### **AuditLog**
- `id` (Primary Key)
- `user_id` (Foreign Key → User)
- `action` (String: create, update, delete, login, logout)
- `entity_type` (String: Task, Category, User, etc.)
- `entity_id` (Integer)
- `old_values` (JSON, nullable)
- `new_values` (JSON, nullable)
- `ip_address` (String, nullable)
- `user_agent` (String, nullable)
- `created_at` (DateTime)

### 1.2 Relasi Antar Entitas

```
User (1) ----< (N) Task
User (1) ----< (N) Category
User (1) ----< (1) Settings
User (1) ----< (N) AuditLog

Task (N) ----< (M) Category (through TaskCategory)
```

### 1.3 Diagram ERD (Representasi Text)

```
┌─────────────────┐
│     USER        │
├─────────────────┤
│ id (PK)         │
│ username        │
│ email           │
│ password        │
│ full_name       │
│ role            │
│ avatar          │
│ created_at      │
│ updated_at      │
│ last_login      │
└────────┬────────┘
         │
         │ 1:N
         │
    ┌────┴────┬──────────┬──────────┐
    │         │          │          │
┌───▼──────┐ ┌▼────────┐ ┌▼───────┐ ┌▼─────────┐
│ TASK     │ │CATEGORY │ │SETTINGS│ │AUDIT_LOG │
├──────────┤ ├─────────┤ ├────────┤ ├──────────┤
│id (PK)   │ │id (PK)  │ │id (PK) │ │id (PK)   │
│user_id   │ │name     │ │user_id │ │user_id   │
│title     │ │color    │ │theme   │ │action    │
│desc      │ │icon     │ │language│ │entity_   │
│status    │ │user_id  │ │notify  │ │type      │
│priority  │ └────┬────┘ │auto_   │ │entity_id │
│due_date  │      │      │sync    │ │old_vals  │
└────┬─────┘      │      └────────┘ │new_vals  │
     │            │                 │ip_addr   │
     │  N:M       │                 │created_at│
     └────────────┘                 └──────────┘
         │
    ┌────▼────────┐
    │TASK_CATEGORY│
    ├─────────────┤
    │task_id (FK) │
    │cat_id (FK)  │
    └─────────────┘
```

---

## 2. Controller Architecture

### 2.1 Struktur Controller (MVC Pattern untuk Flutter)

Flutter menggunakan pola **Provider**, **Riverpod**, **GetX**, atau **BLoC** untuk state management. Berikut struktur controller yang disarankan:

#### **Base Controller Structure**
```
lib/
├── controllers/
│   ├── auth_controller.dart
│   ├── task_controller.dart
│   ├── category_controller.dart
│   ├── settings_controller.dart
│   ├── theme_controller.dart
│   └── navigation_controller.dart
├── models/
│   ├── user_model.dart
│   ├── task_model.dart
│   ├── category_model.dart
│   └── settings_model.dart
├── services/
│   ├── database_service.dart
│   ├── api_service.dart
│   ├── auth_service.dart
│   └── storage_service.dart
├── views/
│   ├── screens/
│   │   ├── home_screen.dart
│   │   ├── task_screen.dart
│   │   ├── settings_screen.dart
│   │   └── login_screen.dart
│   └── widgets/
│       ├── task_card.dart
│       ├── sidebar_menu.dart
│       └── custom_button.dart
└── utils/
    ├── constants.dart
    ├── helpers.dart
    └── validators.dart
```

### 2.2 Contoh Controller Implementation

#### **AuthController** (menggunakan Provider)
```dart
import 'package:flutter/foundation.dart';
import '../models/user_model.dart';
import '../services/auth_service.dart';

class AuthController extends ChangeNotifier {
  final AuthService _authService = AuthService();

  User? _currentUser;
  bool _isLoading = false;
  String? _errorMessage;

  User? get currentUser => _currentUser;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;
  bool get isAuthenticated => _currentUser != null;

  // Login
  Future<bool> login(String email, String password) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      _currentUser = await _authService.login(email, password);
      _isLoading = false;
      notifyListeners();
      return true;
    } catch (e) {
      _errorMessage = e.toString();
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  // Logout
  Future<void> logout() async {
    await _authService.logout();
    _currentUser = null;
    notifyListeners();
  }

  // Register
  Future<bool> register(String email, String password, String fullName) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      _currentUser = await _authService.register(email, password, fullName);
      _isLoading = false;
      notifyListeners();
      return true;
    } catch (e) {
      _errorMessage = e.toString();
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }
}
```

#### **TaskController**
```dart
import 'package:flutter/foundation.dart';
import '../models/task_model.dart';
import '../services/database_service.dart';

class TaskController extends ChangeNotifier {
  final DatabaseService _dbService = DatabaseService();

  List<Task> _tasks = [];
  bool _isLoading = false;
  String? _errorMessage;

  List<Task> get tasks => _tasks;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;

  // Fetch all tasks
  Future<void> fetchTasks({String? status, String? priority}) async {
    _isLoading = true;
    notifyListeners();

    try {
      _tasks = await _dbService.getTasks(status: status, priority: priority);
      _isLoading = false;
      notifyListeners();
    } catch (e) {
      _errorMessage = e.toString();
      _isLoading = false;
      notifyListeners();
    }
  }

  // Create task
  Future<bool> createTask(Task task) async {
    try {
      final newTask = await _dbService.createTask(task);
      _tasks.add(newTask);
      notifyListeners();
      return true;
    } catch (e) {
      _errorMessage = e.toString();
      notifyListeners();
      return false;
    }
  }

  // Update task
  Future<bool> updateTask(Task task) async {
    try {
      await _dbService.updateTask(task);
      final index = _tasks.indexWhere((t) => t.id == task.id);
      if (index != -1) {
        _tasks[index] = task;
        notifyListeners();
      }
      return true;
    } catch (e) {
      _errorMessage = e.toString();
      notifyListeners();
      return false;
    }
  }

  // Delete task
  Future<bool> deleteTask(int taskId) async {
    try {
      await _dbService.deleteTask(taskId);
      _tasks.removeWhere((task) => task.id == taskId);
      notifyListeners();
      return true;
    } catch (e) {
      _errorMessage = e.toString();
      notifyListeners();
      return false;
    }
  }

  // Filter tasks
  List<Task> filterByStatus(String status) {
    return _tasks.where((task) => task.status == status).toList();
  }

  List<Task> filterByPriority(String priority) {
    return _tasks.where((task) => task.priority == priority).toList();
  }
}
```

#### **ThemeController**
```dart
import 'package:flutter/material.dart';
import '../services/storage_service.dart';

class ThemeController extends ChangeNotifier {
  final StorageService _storage = StorageService();

  ThemeMode _themeMode = ThemeMode.system;

  ThemeMode get themeMode => _themeMode;

  ThemeController() {
    _loadTheme();
  }

  Future<void> _loadTheme() async {
    final savedTheme = await _storage.getThemeMode();
    _themeMode = savedTheme ?? ThemeMode.system;
    notifyListeners();
  }

  Future<void> setThemeMode(ThemeMode mode) async {
    _themeMode = mode;
    await _storage.saveThemeMode(mode);
    notifyListeners();
  }

  void toggleTheme() {
    if (_themeMode == ThemeMode.light) {
      setThemeMode(ThemeMode.dark);
    } else {
      setThemeMode(ThemeMode.light);
    }
  }
}
```

### 2.3 Controller Best Practices

1. **Single Responsibility**: Setiap controller hanya menangani satu domain/fitur
2. **Error Handling**: Selalu tangani error dengan baik dan berikan feedback ke user
3. **Loading States**: Gunakan loading indicator untuk operasi async
4. **Separation of Concerns**: Controller tidak langsung akses database, gunakan Service layer
5. **Memory Management**: Dispose controller yang tidak digunakan
6. **State Immutability**: Hindari mutasi langsung, create new state objects

---

## 3. List Menu & Navigation

### 3.1 Main Menu Structure

#### **Sidebar Menu (Desktop Layout)**

```
┌─────────────────────────┐
│   APP LOGO / TITLE      │
├─────────────────────────┤
│                         │
│  🏠 Dashboard           │
│  ✓ Tasks                │
│    ├─ All Tasks         │
│    ├─ In Progress       │
│    ├─ Completed         │
│    └─ Overdue           │
│  📁 Categories          │
│  📊 Reports             │
│  📈 Analytics           │
│  ⚙️  Settings           │
│    ├─ Profile           │
│    ├─ Preferences       │
│    ├─ Theme             │
│    └─ About             │
│                         │
├─────────────────────────┤
│  👤 User Profile        │
│  🚪 Logout              │
└─────────────────────────┘
```

### 3.2 Menu Items dengan Routes

#### **Menu Configuration**
```dart
class MenuItems {
  static const List<MenuItem> mainMenu = [
    MenuItem(
      id: 'dashboard',
      title: 'Dashboard',
      icon: Icons.home,
      route: '/dashboard',
    ),
    MenuItem(
      id: 'tasks',
      title: 'Tasks',
      icon: Icons.task_alt,
      route: '/tasks',
      subMenus: [
        SubMenuItem(
          id: 'all_tasks',
          title: 'All Tasks',
          route: '/tasks/all',
        ),
        SubMenuItem(
          id: 'in_progress',
          title: 'In Progress',
          route: '/tasks/in-progress',
        ),
        SubMenuItem(
          id: 'completed',
          title: 'Completed',
          route: '/tasks/completed',
        ),
        SubMenuItem(
          id: 'overdue',
          title: 'Overdue',
          route: '/tasks/overdue',
        ),
      ],
    ),
    MenuItem(
      id: 'categories',
      title: 'Categories',
      icon: Icons.folder,
      route: '/categories',
    ),
    MenuItem(
      id: 'reports',
      title: 'Reports',
      icon: Icons.assessment,
      route: '/reports',
    ),
    MenuItem(
      id: 'analytics',
      title: 'Analytics',
      icon: Icons.analytics,
      route: '/analytics',
    ),
    MenuItem(
      id: 'settings',
      title: 'Settings',
      icon: Icons.settings,
      route: '/settings',
      subMenus: [
        SubMenuItem(
          id: 'profile',
          title: 'Profile',
          route: '/settings/profile',
        ),
        SubMenuItem(
          id: 'preferences',
          title: 'Preferences',
          route: '/settings/preferences',
        ),
        SubMenuItem(
          id: 'theme',
          title: 'Theme',
          route: '/settings/theme',
        ),
        SubMenuItem(
          id: 'about',
          title: 'About',
          route: '/settings/about',
        ),
      ],
    ),
  ];
}
```

### 3.3 Context Menu (Right Click)

#### **Task Context Menu**
- Edit Task
- Delete Task
- Duplicate Task
- Change Status
- Change Priority
- Add to Category
- Set Due Date
- View Details

#### **Category Context Menu**
- Edit Category
- Delete Category
- Change Color
- Change Icon
- View Tasks in Category

### 3.4 Top Bar Menu (Desktop Window Controls)

#### **File Menu**
- New Task (Ctrl+N)
- New Category (Ctrl+Shift+N)
- Import Data (Ctrl+I)
- Export Data (Ctrl+E)
- Settings (Ctrl+,)
- Exit (Alt+F4)

#### **Edit Menu**
- Undo (Ctrl+Z)
- Redo (Ctrl+Y)
- Cut (Ctrl+X)
- Copy (Ctrl+C)
- Paste (Ctrl+V)
- Select All (Ctrl+A)

#### **View Menu**
- Toggle Sidebar (Ctrl+B)
- Toggle Dark Mode (Ctrl+D)
- Zoom In (Ctrl++)
- Zoom Out (Ctrl+-)
- Reset Zoom (Ctrl+0)
- Full Screen (F11)

#### **Help Menu**
- Documentation (F1)
- Keyboard Shortcuts (Ctrl+/)
- Report Bug
- Check for Updates
- About

### 3.5 Navigation Implementation

```dart
class AppRoutes {
  static const String splash = '/';
  static const String login = '/login';
  static const String register = '/register';
  static const String dashboard = '/dashboard';
  static const String tasks = '/tasks';
  static const String taskDetail = '/tasks/:id';
  static const String categories = '/categories';
  static const String reports = '/reports';
  static const String analytics = '/analytics';
  static const String settings = '/settings';
  static const String profile = '/settings/profile';
  static const String preferences = '/settings/preferences';
  static const String theme = '/settings/theme';
  static const String about = '/settings/about';

  static Map<String, WidgetBuilder> getRoutes() {
    return {
      splash: (context) => const SplashScreen(),
      login: (context) => const LoginScreen(),
      register: (context) => const RegisterScreen(),
      dashboard: (context) => const DashboardScreen(),
      tasks: (context) => const TasksScreen(),
      categories: (context) => const CategoriesScreen(),
      reports: (context) => const ReportsScreen(),
      analytics: (context) => const AnalyticsScreen(),
      settings: (context) => const SettingsScreen(),
      profile: (context) => const ProfileScreen(),
      preferences: (context) => const PreferencesScreen(),
      theme: (context) => const ThemeScreen(),
      about: (context) => const AboutScreen(),
    };
  }
}
```

### 3.6 Keyboard Shortcuts

| Shortcut | Action |
|----------|--------|
| `Ctrl+N` | New Task |
| `Ctrl+S` | Save |
| `Ctrl+F` | Search |
| `Ctrl+P` | Quick Command Palette |
| `Ctrl+B` | Toggle Sidebar |
| `Ctrl+D` | Toggle Dark Mode |
| `Ctrl+,` | Open Settings |
| `Ctrl+Q` | Quit Application |
| `F1` | Help |
| `F5` | Refresh |
| `Esc` | Close Dialog/Modal |

---

## 4. Database Implementation

### 4.1 Local Database (SQLite)

Untuk aplikasi desktop Flutter, gunakan **sqflite** atau **drift** (moor):

```yaml
dependencies:
  drift: ^2.14.0
  sqlite3_flutter_libs: ^0.5.0
  path_provider: ^2.1.1
  path: ^1.8.3
```

### 4.2 Migration Strategy

```dart
// Database version management
class DatabaseMigrations {
  static const int latestVersion = 1;

  static Future<void> migrate(Database db, int oldVersion, int newVersion) async {
    if (oldVersion < 1) {
      await _createInitialTables(db);
    }
    // Add more migrations as needed
  }

  static Future<void> _createInitialTables(Database db) async {
    await db.execute('''
      CREATE TABLE users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE NOT NULL,
        email TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        full_name TEXT NOT NULL,
        role TEXT NOT NULL,
        avatar TEXT,
        created_at TEXT NOT NULL,
        updated_at TEXT NOT NULL,
        last_login TEXT
      )
    ''');

    await db.execute('''
      CREATE TABLE tasks (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        title TEXT NOT NULL,
        description TEXT,
        status TEXT NOT NULL,
        priority TEXT NOT NULL,
        due_date TEXT,
        created_at TEXT NOT NULL,
        updated_at TEXT NOT NULL,
        FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
      )
    ''');

    // Add more tables...
  }
}
```

---

## 5. Teknologi Stack

### 5.1 Core Technologies
- **Framework**: Flutter 3.x
- **Language**: Dart 3.x
- **State Management**: Provider / Riverpod / GetX
- **Local Database**: Drift (SQLite)
- **Navigation**: go_router
- **Desktop Support**: Windows, macOS, Linux

### 5.2 Essential Packages

```yaml
dependencies:
  flutter:
    sdk: flutter

  # State Management
  provider: ^6.1.1

  # Database
  drift: ^2.14.0
  sqlite3_flutter_libs: ^0.5.0

  # Navigation
  go_router: ^13.0.0

  # UI Components
  flutter_riverpod: ^2.4.9
  animations: ^2.0.11

  # Storage
  path_provider: ^2.1.1
  shared_preferences: ^2.2.2

  # Utils
  intl: ^0.18.1
  uuid: ^4.2.2

  # Desktop
  window_manager: ^0.3.7
  bitsdojo_window: ^0.1.6

dev_dependencies:
  flutter_test:
    sdk: flutter
  flutter_lints: ^3.0.1
  build_runner: ^2.4.7
  drift_dev: ^2.14.0
```

---

## 6. File Structure Lengkap

```
flutter_desktop_app/
├── lib/
│   ├── main.dart
│   ├── app.dart
│   │
│   ├── controllers/
│   │   ├── auth_controller.dart
│   │   ├── task_controller.dart
│   │   ├── category_controller.dart
│   │   ├── settings_controller.dart
│   │   ├── theme_controller.dart
│   │   └── navigation_controller.dart
│   │
│   ├── models/
│   │   ├── user_model.dart
│   │   ├── task_model.dart
│   │   ├── category_model.dart
│   │   ├── settings_model.dart
│   │   └── audit_log_model.dart
│   │
│   ├── services/
│   │   ├── database_service.dart
│   │   ├── auth_service.dart
│   │   ├── storage_service.dart
│   │   ├── sync_service.dart
│   │   └── export_service.dart
│   │
│   ├── views/
│   │   ├── screens/
│   │   │   ├── splash_screen.dart
│   │   │   ├── login_screen.dart
│   │   │   ├── register_screen.dart
│   │   │   ├── dashboard_screen.dart
│   │   │   ├── task_screen.dart
│   │   │   ├── task_detail_screen.dart
│   │   │   ├── category_screen.dart
│   │   │   ├── reports_screen.dart
│   │   │   ├── analytics_screen.dart
│   │   │   └── settings_screen.dart
│   │   │
│   │   └── widgets/
│   │       ├── sidebar_menu.dart
│   │       ├── task_card.dart
│   │       ├── task_form.dart
│   │       ├── category_chip.dart
│   │       ├── custom_button.dart
│   │       ├── custom_text_field.dart
│   │       └── loading_indicator.dart
│   │
│   ├── routes/
│   │   └── app_routes.dart
│   │
│   ├── config/
│   │   ├── themes.dart
│   │   ├── constants.dart
│   │   └── database_config.dart
│   │
│   └── utils/
│       ├── helpers.dart
│       ├── validators.dart
│       ├── extensions.dart
│       └── enums.dart
│
├── assets/
│   ├── images/
│   ├── icons/
│   └── fonts/
│
├── windows/
├── macos/
├── linux/
│
├── pubspec.yaml
├── analysis_options.yaml
└── README.md
```

---

## 7. Next Steps

1. ✅ Setup Flutter Desktop Project
2. ✅ Implement Database Schema
3. ✅ Create Models
4. ✅ Implement Controllers
5. ✅ Design UI/UX
6. ✅ Implement Navigation
7. ✅ Add State Management
8. ✅ Implement Authentication
9. ✅ Test on Multiple Platforms
10. ✅ Build & Deploy

---

**Dibuat pada**: 2025-11-17
**Versi**: 1.0.0
**Status**: Draft
