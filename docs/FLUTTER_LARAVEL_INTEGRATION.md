# 📱 Flutter Desktop ↔ Laravel Backend Integration

Dokumentasi lengkap API requirements untuk **MCv4 Flutter Desktop App** ke **Laravel Backend**.

---

## 🎯 Konsep Arsitektur

```
┌─────────────────────────────────────────────┐
│   FLUTTER DESKTOP APP                       │
│   ✓ Offline-first (SQLite local)           │
│   ✓ Bekerja tanpa internet                 │
│   ✓ Sync ke cloud secara periodik          │
└──────────────────┬──────────────────────────┘
                   │
                   │ REST API (HTTP/HTTPS)
                   │ JSON Request/Response
                   ▼
┌─────────────────────────────────────────────┐
│   LARAVEL BACKEND API                       │
│   ✓ License verification                   │
│   ✓ Document online storage                │
│   ✓ Software updates                       │
│   ✓ Backup & sync                          │
└─────────────────────────────────────────────┘
```

---

## 1️⃣ LICENSE VERIFICATION

### Use Case
- Saat app startup, verify license key
- Setiap hari, re-check license status
- Sebelum sync, verify license masih valid

### API Endpoints

#### **POST /api/license/verify**
Verify license key dan get tenant info

**Flutter Request:**
```dart
final response = await dio.post(
  '$baseUrl/api/license/verify',
  data: {
    'license_key': 'MCv4-ABCD1234-EFGH5678-IJKL9012',
    'app_version': '1.0.0',
    'platform': 'windows', // windows|macos|linux
  },
);
```

**Laravel Response (Success):**
```json
{
  "success": true,
  "message": "License valid",
  "tenant": {
    "id": "clinic-abc123",
    "name": "Klinik Sehat Sentosa",
    "email": "admin@kliniksehat.com",
    "subdomain": "kliniksehat",
    "plan": "pro",
    "license_status": "active",
    "license_expires_at": "2025-12-31T23:59:59Z",
    "trial_ends_at": null,
    "remaining_trial_days": 0,
    "limits": {
      "max_users": 10,
      "max_documents": 1000,
      "max_storage_mb": 5000,
      "current_users": 3,
      "current_documents": 145,
      "current_storage_mb": 850
    }
  }
}
```

**Laravel Response (Failed - License Expired):**
```json
{
  "success": false,
  "message": "Your license has expired. Please renew your subscription.",
  "code": "LICENSE_EXPIRED",
  "tenant": {
    "name": "Klinik Sehat Sentosa",
    "license_status": "expired",
    "trial_ends_at": null,
    "license_expires_at": "2024-12-31T23:59:59Z"
  }
}
```

**Error Codes:**
- `INVALID_LICENSE` - License key tidak ditemukan
- `LICENSE_EXPIRED` - License sudah expired
- `TRIAL_EXPIRED` - Trial period habis
- `LICENSE_SUSPENDED` - License di-suspend admin
- `ACCOUNT_INACTIVE` - Akun tidak aktif

**Flutter Action:**
```dart
if (response.data['success']) {
  // Save tenant info ke local storage
  await storage.saveTenant(response.data['tenant']);

  // Check usage limits
  final limits = response.data['tenant']['limits'];
  if (limits['current_documents'] >= limits['max_documents']) {
    showWarning('Document limit reached!');
  }
} else {
  // Show error dialog
  final errorCode = response.data['code'];
  if (errorCode == 'LICENSE_EXPIRED') {
    showExpiredDialog();
  } else if (errorCode == 'TRIAL_EXPIRED') {
    showPurchaseDialog();
  }
}
```

#### **GET /api/license/status/{licenseKey}**
Quick check license status (tanpa full tenant info)

**Flutter Request:**
```dart
final status = await dio.get('$baseUrl/api/license/status/$licenseKey');
```

**Laravel Response:**
```json
{
  "success": true,
  "license": {
    "status": "active",
    "is_valid": true,
    "is_trial": false,
    "expires_at": "2025-12-31T23:59:59Z",
    "remaining_trial_days": 0,
    "plan": "pro"
  }
}
```

#### **GET /api/license/usage/{licenseKey}**
Check usage limits only

**Laravel Response:**
```json
{
  "success": true,
  "usage": {
    "users": {
      "current": 3,
      "max": 10,
      "percentage": 30.0,
      "exceeded": false
    },
    "documents": {
      "current": 145,
      "max": 1000,
      "percentage": 14.5,
      "exceeded": false
    },
    "storage": {
      "current_mb": 850,
      "max_mb": 5000,
      "percentage": 17.0,
      "exceeded": false
    }
  }
}
```

---

## 2️⃣ DOCUMENT PUBLICATION

### Use Case
- Setelah generate surat (MC/SKB) di desktop
- User klik "Publish Online" untuk verifikasi online
- Upload PDF + metadata ke backend
- Dapat QR code untuk verifikasi

### API Endpoints

#### **POST /api/documents/publish**
Upload dokumen dari desktop ke online

**Flutter Request (Multipart Form):**
```dart
final formData = FormData.fromMap({
  // Required fields
  'tenant_id': 'clinic-abc123',
  'license_key': 'MCv4-ABCD1234-EFGH5678-IJKL9012',

  // Document info
  'document_type': 'medical_certificate', // medical_certificate|skb|mcu_report
  'document_number': 'MC-20231117-ABCDEF',

  // Patient info
  'patient_name': 'John Doe',
  'patient_id_number': '3174012345678901', // NIK (optional)
  'patient_dob': '1990-05-15',
  'patient_phone': '081234567890',
  'patient_email': 'john@example.com',

  // Doctor info
  'doctor_name': 'Dr. Jane Smith',
  'doctor_license': 'SIP.123/ABC/2023',

  // Medical details
  'diagnosis': 'Influenza',
  'notes': 'Istirahat 3 hari',
  'issued_date': '2023-11-17',
  'valid_until': '2023-11-20', // optional

  // Company (for B2B) - optional
  'company_name': 'PT. ABC Indonesia',
  'company_id': 'company-123',

  // File upload
  'file': await MultipartFile.fromFile(
    pdfPath,
    filename: 'MC-20231117-ABCDEF.pdf',
    contentType: MediaType('application', 'pdf'),
  ),

  // Desktop metadata
  'desktop_created_at': '2023-11-17T10:30:00Z',
});

final response = await dio.post(
  '$baseUrl/api/documents/publish',
  data: formData,
);
```

**Laravel Response:**
```json
{
  "success": true,
  "message": "Document published successfully",
  "document": {
    "id": 12345,
    "document_number": "MC-20231117-ABCDEF",
    "document_type": "medical_certificate",
    "verification_code": "QR7X8Y9Z1A2B3C4D",
    "public_url": "https://api.mcv4.app/verify/QR7X8Y9Z1A2B3C4D",
    "qr_code_url": "https://api.mcv4.app/qr/QR7X8Y9Z1A2B3C4D.png",
    "status": "published",
    "is_public": true,
    "file_url": "https://api.mcv4.app/storage/documents/tenant123/MC-20231117-ABCDEF.pdf",
    "created_at": "2023-11-17T10:35:22Z"
  }
}
```

**Flutter Action:**
```dart
if (response.data['success']) {
  final doc = response.data['document'];

  // Save verification code to local DB
  await db.updateDocument(
    documentId,
    verificationCode: doc['verification_code'],
    publicUrl: doc['public_url'],
    publishedAt: DateTime.now(),
  );

  // Show success with QR code
  showSuccessDialog(
    title: 'Document Published!',
    qrCodeUrl: doc['qr_code_url'],
    publicUrl: doc['public_url'],
  );

  // Update usage count
  await refreshLicenseUsage();
}
```

#### **POST /api/documents/bulk-publish**
Upload multiple documents sekaligus

**Flutter Request:**
```dart
final formData = FormData.fromMap({
  'tenant_id': 'clinic-abc123',
  'license_key': 'MCv4-...',
  'documents': jsonEncode([
    {
      'document_number': 'MC-001',
      'patient_name': 'Patient A',
      // ... fields lainnya
    },
    {
      'document_number': 'MC-002',
      'patient_name': 'Patient B',
      // ... fields lainnya
    },
  ]),
  'files[]': [
    await MultipartFile.fromFile('MC-001.pdf'),
    await MultipartFile.fromFile('MC-002.pdf'),
  ],
});
```

**Laravel Response:**
```json
{
  "success": true,
  "message": "2 documents published successfully",
  "documents": [
    { "id": 123, "document_number": "MC-001", "verification_code": "..." },
    { "id": 124, "document_number": "MC-002", "verification_code": "..." }
  ],
  "failed": []
}
```

#### **GET /api/verify/{verificationCode}**
Public endpoint untuk verifikasi QR (tanpa auth)

**Usage:** Scan QR code → open URL → show document info

**Laravel Response (HTML or JSON):**
```json
{
  "success": true,
  "document": {
    "document_number": "MC-20231117-ABCDEF",
    "document_type": "Medical Certificate",
    "patient_name": "John Doe",
    "doctor_name": "Dr. Jane Smith",
    "issued_date": "2023-11-17",
    "valid_until": "2023-11-20",
    "status": "valid",
    "clinic_name": "Klinik Sehat Sentosa",
    "verified_at": "2023-11-18T14:20:00Z",
    "view_count": 5
  }
}
```

#### **GET /api/documents/{tenantId}**
List semua documents milik tenant (untuk web portal future)

**Laravel Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 123,
      "document_number": "MC-001",
      "patient_name": "John Doe",
      "issued_date": "2023-11-17",
      "status": "published",
      "view_count": 3
    }
  ],
  "meta": {
    "current_page": 1,
    "total": 145,
    "per_page": 20
  }
}
```

---

## 3️⃣ SOFTWARE UPDATES

### Use Case
- Saat app startup, check update
- Setiap 24 jam, auto-check update
- User manual check dari settings
- Download & install update

### API Endpoints

#### **GET /api/updates/check**
Check apakah ada update baru

**Flutter Request:**
```dart
final response = await dio.get(
  '$baseUrl/api/updates/check',
  queryParameters: {
    'current_version': '1.0.0',
    'platform': 'windows', // windows|macos|linux
    'build_number': '20231117001',
  },
);
```

**Laravel Response (Update Available):**
```json
{
  "success": true,
  "update_available": true,
  "latest_version": "1.2.0",
  "current_version": "1.0.0",
  "is_critical": false,
  "release_type": "stable",
  "update": {
    "id": 45,
    "version": "1.2.0",
    "build_number": "20231120001",
    "codename": "Aurora",
    "title": "MCv4 v1.2.0 - Performance Update",
    "description": "Major performance improvements and bug fixes",
    "changelog": "## New Features\n- Faster PDF generation\n- Improved sync\n\n## Bug Fixes\n- Fixed crash on macOS",
    "breaking_changes": null,
    "download_url": "https://downloads.mcv4.app/v1.2.0/MCv4-Setup-1.2.0-windows.exe",
    "file_name": "MCv4-Setup-1.2.0-windows.exe",
    "file_size": 52428800,
    "file_hash": "sha256:abcd1234...",
    "minimum_version": "1.0.0",
    "published_at": "2023-11-20T10:00:00Z"
  }
}
```

**Laravel Response (No Update):**
```json
{
  "success": true,
  "update_available": false,
  "latest_version": "1.0.0",
  "current_version": "1.0.0",
  "message": "You are using the latest version"
}
```

**Flutter Action:**
```dart
if (response.data['update_available']) {
  final update = response.data['update'];

  if (update['is_critical']) {
    // Force update - blocking dialog
    showForceUpdateDialog(update);
  } else {
    // Optional update - notification
    showUpdateNotification(update);
  }
}
```

#### **GET /api/updates/latest/{platform}**
Get detail update terbaru untuk platform tertentu

**Flutter Request:**
```dart
final update = await dio.get('$baseUrl/api/updates/latest/windows');
```

**Laravel Response:** (sama seperti update object di atas)

#### **POST /api/updates/download/{id}**
Record bahwa user download update

**Flutter Request:**
```dart
await dio.post('$baseUrl/api/updates/download/45', data: {
  'tenant_id': 'clinic-abc123',
  'platform': 'windows',
});
```

**Laravel Response:**
```json
{
  "success": true,
  "message": "Download recorded"
}
```

**Flutter Action:**
```dart
// Start download
final downloadUrl = update['download_url'];
await downloader.download(
  downloadUrl,
  savePath: '/downloads/MCv4-Setup.exe',
  onProgress: (progress) {
    setState(() => downloadProgress = progress);
  },
  onComplete: () async {
    // Verify file hash
    final fileHash = await calculateSHA256(filePath);
    if (fileHash != update['file_hash']) {
      showError('File corrupted!');
      return;
    }

    // Record download success
    await api.recordDownload(update['id']);

    // Prompt install
    showInstallDialog();
  },
);
```

#### **POST /api/updates/install/{id}**
Record bahwa update berhasil diinstall

**Flutter Request:**
```dart
await dio.post('$baseUrl/api/updates/install/45', data: {
  'tenant_id': 'clinic-abc123',
  'old_version': '1.0.0',
  'new_version': '1.2.0',
  'platform': 'windows',
});
```

---

## 4️⃣ DATA SYNCHRONIZATION

### Use Case
- Periodic sync (auto setiap 6 jam)
- Manual sync (user klik button)
- Backup full database
- Restore dari backup

### Sync Strategy

```
Desktop (SQLite)          Laravel (MySQL)
─────────────────         ───────────────
patients                  (tidak disync - local only)
doctors                   (tidak disync - local only)
documents                 ✓ Sync to online (untuk verifikasi)
settings                  (tidak disync - local only)
```

**Yang Di-sync:**
1. **Documents** - Untuk online verification
2. **Usage stats** - Update usage count ke server
3. **Backup** - Full database backup (optional)

### API Endpoints

#### **POST /api/sync/start**
Mulai sync session

**Flutter Request:**
```dart
final response = await dio.post('$baseUrl/api/sync/start', data: {
  'tenant_id': 'clinic-abc123',
  'license_key': 'MCv4-...',
  'sync_type': 'incremental', // full|incremental|documents_only|backup
  'client_version': '1.0.0',
  'client_platform': 'windows',
  'last_sync_at': '2023-11-17T10:00:00Z', // untuk incremental
});
```

**Laravel Response:**
```json
{
  "success": true,
  "message": "Sync session started",
  "sync_session": {
    "id": 789,
    "session_token": "sync_token_abc123...",
    "sync_type": "incremental",
    "status": "processing",
    "started_at": "2023-11-17T15:00:00Z"
  }
}
```

#### **POST /api/sync/upload**
Upload data yang akan di-sync

**Flutter Request:**
```dart
// Get new/updated documents since last sync
final documents = await db.getDocumentsSince(lastSyncAt);

final response = await dio.post('$baseUrl/api/sync/upload', data: {
  'sync_id': 789,
  'session_token': 'sync_token_abc123...',
  'tenant_id': 'clinic-abc123',
  'data': {
    'documents': documents.map((doc) => {
      'document_number': doc.documentNumber,
      'patient_name': doc.patientName,
      'doctor_name': doc.doctorName,
      'diagnosis': doc.diagnosis,
      'issued_date': doc.issuedDate.toIso8601String(),
      // ... other fields
    }).toList(),
    'usage': {
      'documents_count': totalDocuments,
      'storage_mb': totalStorageMB,
    },
  },
});
```

**Laravel Response:**
```json
{
  "success": true,
  "message": "Data uploaded successfully",
  "sync_log": {
    "id": 789,
    "total_records": 25,
    "processed_records": 25,
    "failed_records": 0,
    "skipped_records": 0,
    "status": "processing"
  }
}
```

#### **POST /api/sync/complete**
Mark sync sebagai completed

**Flutter Request:**
```dart
await dio.post('$baseUrl/api/sync/complete', data: {
  'sync_id': 789,
  'session_token': 'sync_token_abc123...',
});
```

**Laravel Response:**
```json
{
  "success": true,
  "message": "Sync completed successfully",
  "sync_log": {
    "id": 789,
    "status": "completed",
    "total_records": 25,
    "processed_records": 25,
    "failed_records": 0,
    "duration_seconds": 12,
    "data_size_bytes": 524288,
    "completed_at": "2023-11-17T15:00:12Z"
  }
}
```

#### **POST /api/sync/fail**
Mark sync sebagai failed (jika ada error)

**Flutter Request:**
```dart
await dio.post('$baseUrl/api/sync/fail', data: {
  'sync_id': 789,
  'session_token': 'sync_token_abc123...',
  'error_message': 'Network timeout',
  'error_details': {
    'exception': 'SocketException',
    'stack_trace': '...',
  },
});
```

#### **GET /api/sync/status/{syncId}**
Check status sync (untuk monitoring)

**Laravel Response:**
```json
{
  "success": true,
  "sync_log": {
    "id": 789,
    "status": "processing",
    "progress_percentage": 65,
    "total_records": 100,
    "processed_records": 65,
    "failed_records": 2,
    "skipped_records": 0
  }
}
```

#### **GET /api/sync/history/{tenantId}**
Get sync history

**Laravel Response:**
```json
{
  "success": true,
  "history": [
    {
      "id": 789,
      "sync_type": "incremental",
      "status": "completed",
      "total_records": 25,
      "duration_seconds": 12,
      "created_at": "2023-11-17T15:00:00Z"
    },
    {
      "id": 788,
      "sync_type": "full",
      "status": "completed",
      "total_records": 145,
      "duration_seconds": 45,
      "created_at": "2023-11-16T10:00:00Z"
    }
  ]
}
```

#### **POST /api/sync/backup**
Upload full database backup

**Flutter Request:**
```dart
// Export SQLite to file
final backupFile = await db.exportToFile();

final formData = FormData.fromMap({
  'tenant_id': 'clinic-abc123',
  'license_key': 'MCv4-...',
  'backup_type': 'full', // full|incremental
  'file': await MultipartFile.fromFile(
    backupFile.path,
    filename: 'backup_20231117.db',
  ),
  'file_hash': await calculateSHA256(backupFile.path),
  'database_version': '1.0',
});

final response = await dio.post(
  '$baseUrl/api/sync/backup',
  data: formData,
);
```

**Laravel Response:**
```json
{
  "success": true,
  "message": "Backup uploaded successfully",
  "backup": {
    "id": 456,
    "file_name": "backup_20231117_150000.db",
    "file_size": 10485760,
    "file_hash": "sha256:xyz789...",
    "created_at": "2023-11-17T15:00:00Z",
    "download_url": "https://api.mcv4.app/backups/download/clinic-abc123/backup_20231117_150000.db"
  }
}
```

#### **GET /api/sync/backup/download/{tenantId}/{filename}**
Download backup file (untuk restore)

**Flutter Usage:**
```dart
final backups = await api.getBackupList();
// User pilih backup
final selectedBackup = backups[0];

// Download
await downloader.download(
  selectedBackup['download_url'],
  savePath: '/temp/restore.db',
  onComplete: () async {
    // Verify hash
    final hash = await calculateSHA256('/temp/restore.db');
    if (hash == selectedBackup['file_hash']) {
      // Restore database
      await db.restoreFromFile('/temp/restore.db');
      showSuccess('Database restored!');
    }
  },
);
```

---

## 5️⃣ ERROR HANDLING

### HTTP Status Codes

```dart
try {
  final response = await dio.post('/api/...');
  // Handle success
} on DioException catch (e) {
  switch (e.response?.statusCode) {
    case 401:
      // Unauthorized - Invalid license
      handleInvalidLicense();
      break;
    case 403:
      // Forbidden - License expired/suspended
      handleLicenseExpired();
      break;
    case 404:
      // Not found
      showError('Resource not found');
      break;
    case 422:
      // Validation error
      final errors = e.response?.data['errors'];
      showValidationErrors(errors);
      break;
    case 429:
      // Rate limit exceeded
      showError('Too many requests. Please try again later.');
      break;
    case 500:
      // Server error
      showError('Server error. Please contact support.');
      break;
    default:
      showError('Network error. Please check your connection.');
  }
}
```

### Standard Error Response

```json
{
  "success": false,
  "message": "Error message here",
  "code": "ERROR_CODE",
  "errors": {
    "field_name": ["Validation error message"]
  }
}
```

---

## 6️⃣ AUTHENTICATION & HEADERS

### Required Headers

```dart
final dio = Dio(BaseOptions(
  baseUrl: 'https://api.mcv4.app',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'User-Agent': 'MCv4-Desktop/1.0.0 (Windows)',
    'X-Client-Version': '1.0.0',
    'X-Client-Platform': 'windows',
  },
));
```

### License Key in Requests

Semua endpoints yang butuh auth, include `license_key` di request body:

```dart
{
  'license_key': 'MCv4-ABCD1234-EFGH5678-IJKL9012',
  'tenant_id': 'clinic-abc123',
  // ... data lainnya
}
```

---

## 7️⃣ OFFLINE-FIRST STRATEGY

### Data Flow

```
User Action → Save to SQLite → Show Success
              ↓ (background)
              Queue for Sync
              ↓ (when online)
              Upload to Laravel
              ↓
              Update local status
```

### Implementation

```dart
class DocumentService {
  Future<void> createDocument(Document doc) async {
    // 1. Save to local DB
    await db.insertDocument(doc);

    // 2. Show success to user
    showSuccess('Document created!');

    // 3. Queue for background sync (when online)
    await syncQueue.add(SyncTask(
      type: 'document_create',
      data: doc.toJson(),
      retryCount: 3,
    ));
  }

  Future<void> backgroundSync() async {
    if (!await connectivity.isOnline()) return;

    final tasks = await syncQueue.getPending();
    for (final task in tasks) {
      try {
        await api.syncDocument(task.data);
        await syncQueue.markCompleted(task.id);
      } catch (e) {
        if (task.retryCount > 0) {
          await syncQueue.decrementRetry(task.id);
        } else {
          await syncQueue.markFailed(task.id);
        }
      }
    }
  }
}
```

---

## 8️⃣ FILE SIZE LIMITS

### Upload Limits

```dart
// Document PDF
maxPdfSize: 10 MB

// Backup Database
maxBackupSize: 100 MB

// Multiple files (bulk publish)
maxBulkFiles: 50 files
maxBulkSize: 100 MB
```

### Laravel Config (.env)

```env
UPLOAD_MAX_FILESIZE=10M
POST_MAX_SIZE=20M
MAX_EXECUTION_TIME=300
```

---

## 9️⃣ RATE LIMITING

### Limits

```
License verification: 10 requests / minute
Document publish: 30 requests / minute
Sync operations: 5 sessions / hour
Update check: 100 requests / day
```

### Flutter Handling

```dart
if (response.statusCode == 429) {
  final retryAfter = response.headers['Retry-After'];
  await Future.delayed(Duration(seconds: int.parse(retryAfter ?? '60')));
  // Retry request
}
```

---

## 🔟 ENVIRONMENT CONFIG

### Flutter .env

```env
API_BASE_URL=https://api.mcv4.app
API_TIMEOUT=30000
ENABLE_LOGGING=true
AUTO_SYNC_INTERVAL=21600  # 6 hours in seconds
```

### Usage

```dart
class ApiConfig {
  static String get baseUrl => dotenv.env['API_BASE_URL']!;
  static int get timeout => int.parse(dotenv.env['API_TIMEOUT']!);
}
```

---

## 📊 SUMMARY TABLE

| Feature | Endpoint | Method | Auth Required | Flutter Priority |
|---------|----------|--------|---------------|------------------|
| Verify License | `/api/license/verify` | POST | No | ⭐⭐⭐ Critical |
| Check License Status | `/api/license/status/{key}` | GET | No | ⭐⭐ High |
| Publish Document | `/api/documents/publish` | POST | Yes | ⭐⭐⭐ Critical |
| Verify QR | `/api/verify/{code}` | GET | No | ⭐⭐⭐ Critical |
| Check Update | `/api/updates/check` | GET | No | ⭐⭐ High |
| Start Sync | `/api/sync/start` | POST | Yes | ⭐⭐ High |
| Upload Sync Data | `/api/sync/upload` | POST | Yes | ⭐⭐ High |
| Upload Backup | `/api/sync/backup` | POST | Yes | ⭐ Medium |

---

**Created**: 2025-11-17
**Version**: 1.0.0
**For**: MCv4 Flutter Desktop App
