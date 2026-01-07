# 📱 SehatCert Mobile Apps - Development Roadmap

**Start Date**: Q1 2026 (February)
**Target Launch**: Q3 2026 (July-August)
**Platform**: Flutter (iOS + Android)
**Total Timeline**: 24 weeks (6 months)
**Architecture**: Clean Architecture + BLoC Pattern

---

## 🎯 Overview

### 3 Mobile Applications

1. **SehatCert Outlet** (Outlet Staff App) - Priority 1
2. **SehatCert Patient** (Patient App) - Priority 2
3. **SehatCert Company** (Company Admin App) - Priority 3

### Development Strategy

**Approach**: Agile/Scrum with 2-week sprints
**Team Size**:
- 2 Flutter developers (iOS + Android experience)
- 1 Backend developer (API)
- 1 UI/UX designer
- 1 QA tester
- 1 Project manager

---

## 📅 Development Timeline

### **Phase 0: Preparation** (Week 1-2) - Feb 2026

**Sprint 0: Foundation Setup**

**Week 1: Environment & Infrastructure**
- [ ] Setup development environment
  - Flutter SDK (latest stable)
  - iOS simulator (macOS) + Android emulator
  - VSCode / Android Studio + Flutter extensions
  - Git repository setup (monorepo: `sehatcert-mobile`)
- [ ] Finalize tech stack (Flutter)
  - State management: flutter_bloc + hydrated_bloc
  - Navigation: go_router
  - API client: dio + retrofit
  - Dependency Injection: get_it + injectable
  - UI library: Material 3 (built-in)
  - Form handling: flutter_form_builder + form_builder_validators
  - Testing: flutter_test + bloc_test + mocktail
- [ ] Setup CI/CD pipeline
  - GitHub Actions for automated builds
  - Firebase App Distribution for beta testing
  - Codemagic / Fastlane for release automation
- [ ] Backend API preparation
  - Review existing API endpoints
  - Create missing endpoints (see checklist below)
  - API documentation with Postman/Swagger

**Week 2: Design System & Prototypes**
- [ ] Mobile UI/UX design (Figma)
  - Design system based on SehatCert branding
  - Component library (buttons, inputs, cards, etc.)
  - Screen mockups (all 3 apps)
  - User flow diagrams
  - Prototype for stakeholder review
- [ ] Design review & approval
- [ ] Create reusable Flutter widgets
  - Custom buttons, inputs, cards, app bars, etc.
  - Theme configuration (Material 3 theme with SehatCert colors)
  - Shared widget package for all 3 apps

**Deliverables:**
- ✅ Dev environment ready
- ✅ Tech stack finalized
- ✅ CI/CD pipeline working
- ✅ Figma designs approved
- ✅ Component library (v1)

---

### **Phase 1: Outlet Staff App** (Week 3-10) - Feb-Apr 2026

**Target**: Certificate issuance on-the-go for outlet staff

#### Sprint 1 (Week 3-4): Authentication & Core Navigation

**Goals:**
- [ ] Implement authentication flow
  - Login screen (email + password)
  - Token storage (SecureStore)
  - Auto-login if token valid
  - Logout functionality
- [ ] Setup main navigation
  - Bottom tab navigator (Home, Patients, Certificates, Settings)
  - Stack navigator for screen hierarchies
  - Deep linking setup
- [ ] API integration
  - POST /api/auth/login
  - POST /api/auth/logout
  - GET /api/auth/me
- [ ] Splash screen & onboarding (first-time users)

**Deliverables:**
- Login/logout working
- Navigation structure complete
- API authentication integrated

#### Sprint 2 (Week 5-6): Dashboard & Patient Management

**Goals:**
- [ ] Dashboard screen
  - Today's certificate count
  - Quick actions (New Certificate, Search Patient)
  - Recent certificates list
  - Quota usage indicator
- [ ] Patient list screen
  - Search patient by name/NIK
  - Patient card display (photo, name, last visit)
  - Pull-to-refresh
  - Pagination/infinite scroll
- [ ] Patient detail screen
  - Patient info display
  - Medical history
  - Edit patient button
- [ ] Add/Edit patient screen
  - Form fields (NIK, name, DOB, address, etc.)
  - Camera for patient photo
  - Form validation
  - Submit to API

**API Endpoints Needed:**
- GET /api/patients?search={query}
- GET /api/patients/{id}
- POST /api/patients (create)
- PUT /api/patients/{id} (update)
- GET /api/patients/{id}/certificates

**Deliverables:**
- Patient search working
- Patient CRUD operations complete
- Dashboard showing real data

#### Sprint 3 (Week 7-8): Certificate Generation (Part 1)

**Goals:**
- [ ] Certificate type selection screen
  - Choose between MC (Medical Certificate) or SKB (Surat Keterangan Sehat)
  - Type description & typical use cases
- [ ] Certificate form screen (MC)
  - Patient selection/confirmation
  - Doctor selection dropdown
  - Diagnosis input (search from ICD-10 master)
  - Start date & end date picker
  - Duration auto-calculation
  - Additional notes (optional)
- [ ] Diagnosis search screen
  - Search ICD-10 codes
  - Recently used diagnoses
  - Favorite diagnoses

**API Endpoints Needed:**
- GET /api/doctors?outlet_id={id}
- GET /api/diagnoses?search={query}
- GET /api/diagnoses/recent
- GET /api/diagnoses/favorites

**Deliverables:**
- Certificate type selection
- MC form functional
- Diagnosis search working

#### Sprint 4 (Week 9-10): Certificate Generation (Part 2) & Preview

**Goals:**
- [ ] Certificate form screen (SKB)
  - Simplified form (no duration needed)
  - Medical examination results (optional fields)
  - Fit for work confirmation
- [ ] Certificate preview screen
  - PDF preview (react-native-pdf or WebView)
  - Edit button (go back to form)
  - Confirm & Generate button
- [ ] E-signature capture
  - Signature canvas (react-native-signature-canvas)
  - Clear & retry
  - Save signature
- [ ] Certificate generation flow
  - Loading state while generating
  - Success screen with QR code
  - Download PDF option
  - Share via WhatsApp/Email
- [ ] Error handling
  - Quota exceeded error
  - Network error
  - Form validation errors

**API Endpoints Needed:**
- POST /api/certificates (create)
- GET /api/certificates/{id}/pdf
- POST /api/certificates/{id}/notify (send WhatsApp/Email)

**Deliverables:**
- SKB form functional
- PDF preview working
- E-signature captured
- Certificate generation end-to-end flow complete

**🎉 Milestone: Outlet App MVP Complete**

---

### **Phase 2: Patient App** (Week 11-16) - Apr-May 2026

**Target**: Patients can view & download their health certificates

#### Sprint 5 (Week 11-12): Authentication & Home

**Goals:**
- [ ] Patient registration flow
  - Phone number input
  - OTP verification
  - Basic profile setup (name, DOB, NIK)
- [ ] Patient login
  - Phone + password / OTP
  - Biometric login (fingerprint/face ID)
- [ ] Home screen
  - Certificate list (card view)
  - Filter by type (MC/SKB)
  - Search by date range
  - Empty state (no certificates yet)
- [ ] Pull-to-refresh & offline support
  - Cache certificates locally
  - Sync when online

**API Endpoints Needed:**
- POST /api/auth/patient/register
- POST /api/auth/patient/verify-otp
- POST /api/auth/patient/login
- GET /api/patient/me/certificates

**Deliverables:**
- Patient registration working
- Login with OTP/biometric
- Certificate list displayed

#### Sprint 6 (Week 13-14): Certificate Detail & Download

**Goals:**
- [ ] Certificate detail screen
  - Full certificate info display
  - Validity status (active/expired)
  - QR code display (large, scannable)
  - Doctor & outlet info
- [ ] PDF viewer
  - View certificate PDF in-app
  - Zoom/pan support
  - Print option (if supported)
- [ ] Download & share
  - Download PDF to device
  - Share via WhatsApp, Email, other apps
  - Share QR code only (for quick verification)
- [ ] Certificate verification
  - Scan someone else's certificate QR
  - Verify authenticity
  - Display verification result

**API Endpoints Needed:**
- GET /api/certificates/{id}
- GET /api/certificates/{id}/pdf
- POST /api/certificates/verify (by QR code)

**Deliverables:**
- Certificate detail view complete
- PDF download working
- QR code verification functional

#### Sprint 7 (Week 15-16): Profile & Settings

**Goals:**
- [ ] Profile screen
  - View/edit personal info
  - Medical history (read-only)
  - Company affiliation (if any)
- [ ] Settings screen
  - Notification preferences
  - Biometric login toggle
  - Language selection (ID/EN)
  - App version & about
  - Logout
- [ ] Notifications
  - Push notification setup (Expo Notifications)
  - Certificate ready notification
  - Certificate expiry reminder
  - In-app notification list
- [ ] Offline mode
  - Cache strategy for certificates
  - Sync queue when back online
  - Offline indicator

**API Endpoints Needed:**
- GET /api/patient/me
- PUT /api/patient/me (update profile)
- GET /api/patient/notifications
- PUT /api/patient/notification-settings

**Deliverables:**
- Profile management working
- Push notifications integrated
- Offline support functional

**🎉 Milestone: Patient App MVP Complete**

---

### **Phase 3: Company Admin App** (Week 17-22) - May-Jun 2026

**Target**: Company admins can view statistics & verify employee certificates

#### Sprint 8 (Week 17-18): Authentication & Dashboard

**Goals:**
- [ ] Company admin login
  - Email + password
  - 2FA support (optional)
- [ ] Dashboard screen
  - Key metrics cards
    - Total certificates this month
    - Active employees with certificates
    - Quota usage (certificates & outlets)
    - Top 5 diagnoses
  - Charts
    - Monthly trend (line chart)
    - Certificate type breakdown (pie chart)
  - Period selector (this week/month/year, custom range)

**API Endpoints Needed:**
- POST /api/auth/company/login
- GET /api/company/statistics?period={monthly}&year={2026}&month={1}

**Deliverables:**
- Company admin login working
- Dashboard with charts displayed

#### Sprint 9 (Week 19-20): Statistics & Reports

**Goals:**
- [ ] Statistics detail screen
  - Drill-down by outlet
  - Drill-down by diagnosis
  - Date range filtering
  - Comparison view (period vs period)
- [ ] Outlet performance screen
  - List all outlets
  - Certificates issued per outlet
  - Performance trend per outlet
- [ ] Export reports
  - Generate report (PDF/Excel)
  - Email report to self/stakeholders
  - Scheduled reports (optional)

**API Endpoints Needed:**
- GET /api/company/statistics/detail?outlet_id={id}
- GET /api/company/outlets
- GET /api/company/reports/export?format={pdf|excel}

**Deliverables:**
- Statistics drill-down working
- Outlet performance view complete
- Report export functional

#### Sprint 10 (Week 21-22): Employee Verification & Settings

**Goals:**
- [ ] Employee name verification screen
  - Input employee name
  - Search button
  - Result: FOUND / NOT FOUND
  - Audit log display (who verified, when)
- [ ] Outlet management screen
  - List all outlets
  - Outlet details (address, active doctors, certificate count)
  - Add outlet button (if quota allows)
- [ ] Settings screen
  - Company profile view
  - Subscription plan info
  - Quota usage details
  - Upgrade plan CTA
  - Notification settings
  - Logout

**API Endpoints Needed:**
- POST /api/company/verify-patient
  ```json
  { "patient_name": "John Doe" }
  ```
  Response: `{ "found": true }`
- GET /api/company/outlets
- GET /api/company/subscription

**Deliverables:**
- Employee verification working
- Outlet management functional
- Settings screen complete

**🎉 Milestone: Company Admin App MVP Complete**

---

### **Phase 4: Testing & Optimization** (Week 23-24) - Jun-Jul 2026

#### Sprint 11 (Week 23): Integration Testing & Bug Fixes

**Goals:**
- [ ] Integration testing
  - Test all API integrations
  - Test offline scenarios
  - Test edge cases (no network, slow network)
  - Test error handling
- [ ] Cross-platform testing
  - iOS (iPhone 12, 13, 14, 15)
  - Android (Samsung, Xiaomi, Oppo, Vivo)
  - Different screen sizes (small, medium, large)
- [ ] Performance testing
  - App startup time (<3 seconds)
  - Certificate generation time (<5 seconds)
  - Memory usage optimization
  - Battery consumption check
- [ ] Bug fixes
  - Fix critical bugs
  - Fix UI/UX issues
  - Fix performance bottlenecks

**Deliverables:**
- All critical bugs fixed
- App performance optimized
- Cross-platform compatibility verified

#### Sprint 12 (Week 24): Beta Testing & Pre-launch

**Goals:**
- [ ] Beta testing program
  - Recruit 20-30 beta testers
  - Distribute via Firebase App Distribution
  - Collect feedback via Google Forms
  - Monitor crash reports (Crashlytics)
- [ ] Feedback incorporation
  - Prioritize feedback items
  - Implement critical improvements
  - Update documentation
- [ ] App store preparation
  - App screenshots (5-10 per platform)
  - App description (ID & EN)
  - Keywords optimization
  - Privacy policy URL
  - Support URL
- [ ] Final polishing
  - UI refinements
  - Animation improvements
  - Accessibility improvements (font scaling, contrast)
  - Localization check (Indonesian language)

**Deliverables:**
- Beta testing complete
- Feedback incorporated
- App store assets ready
- Apps ready for submission

---

## 🚀 Launch & Post-Launch (Week 25+) - Jul-Aug 2026

### App Store Submission (Week 25)

**iOS (App Store):**
- [ ] Create Apple Developer account ($99/year)
- [ ] App Store Connect setup
- [ ] Submit for review (typically 1-2 days)
- [ ] Address any rejection reasons
- [ ] Approval & go live

**Android (Play Store):**
- [ ] Create Google Play Console account ($25 one-time)
- [ ] Play Store listing
- [ ] Submit for review (typically few hours)
- [ ] Approval & go live

### Soft Launch (Week 26)

- [ ] Launch to pilot clients only
- [ ] Monitor usage & crashes
- [ ] Quick bug fixes if needed
- [ ] Collect initial reviews

### Public Launch (Week 27-28)

- [ ] Press release
- [ ] Social media campaign
- [ ] Email announcement to all users
- [ ] App Store Optimization (ASO)
- [ ] Monitor downloads & ratings
- [ ] Respond to user reviews

### Post-Launch Support (Ongoing)

- [ ] Weekly bug fix releases (if needed)
- [ ] Monthly feature updates
- [ ] User feedback monitoring
- [ ] Performance monitoring (Crashlytics, Analytics)
- [ ] Customer support via in-app chat

---

## 🔌 API Endpoints Checklist

### Authentication
- [x] POST /api/auth/login (already exists)
- [x] POST /api/auth/logout (already exists)
- [x] GET /api/auth/me (already exists)
- [ ] POST /api/auth/patient/register
- [ ] POST /api/auth/patient/verify-otp
- [ ] POST /api/auth/patient/login
- [ ] POST /api/auth/company/login

### Patients
- [ ] GET /api/patients?search={query}&outlet_id={id}
- [ ] GET /api/patients/{id}
- [ ] POST /api/patients
- [ ] PUT /api/patients/{id}
- [ ] GET /api/patients/{id}/certificates
- [ ] POST /api/patients/upload-photo

### Certificates
- [ ] POST /api/certificates (create)
- [ ] GET /api/certificates?outlet_id={id}&date_from={}&date_to={}
- [ ] GET /api/certificates/{id}
- [ ] GET /api/certificates/{id}/pdf
- [ ] POST /api/certificates/{id}/notify (WhatsApp/Email)
- [ ] POST /api/certificates/verify (by QR code)
- [ ] GET /api/patient/me/certificates (patient's own certificates)

### Doctors & Diagnoses
- [ ] GET /api/doctors?outlet_id={id}
- [ ] GET /api/diagnoses?search={query}
- [ ] GET /api/diagnoses/recent?outlet_id={id}
- [ ] GET /api/diagnoses/favorites?user_id={id}

### Company Statistics
- [x] GET /api/company/statistics (already exists)
- [ ] GET /api/company/statistics/detail?outlet_id={id}
- [ ] POST /api/company/verify-patient
- [ ] GET /api/company/outlets
- [ ] GET /api/company/subscription
- [ ] GET /api/company/reports/export?format={pdf|excel}

### Notifications
- [ ] GET /api/patient/notifications
- [ ] PUT /api/patient/notification-settings
- [ ] POST /api/push-tokens (register device token)

### Quota
- [ ] GET /api/quota/remaining (outlet quota & certificate quota)

---

## 🛠️ Technical Specifications

### Architecture

```
┌─────────────────────────────────────┐
│      Flutter Apps (3 apps)          │
├─────────────────────────────────────┤
│  - Outlet Staff App                 │
│  - Patient App                      │
│  - Company Admin App                │
│                                     │
│  Clean Architecture Layers:        │
│  ├── Presentation (BLoC + UI)      │
│  ├── Domain (Entities + Use Cases) │
│  └── Data (Repositories + API)     │
└────────────┬────────────────────────┘
             │
             │ REST API (JSON)
             ▼
┌─────────────────────────────────────┐
│     Laravel Backend (API)           │
├─────────────────────────────────────┤
│  - Sanctum Authentication           │
│  - Multi-tenancy Support            │
│  - Rate Limiting                    │
│  - API Versioning (v1)              │
└────────────┬────────────────────────┘
             │
             ▼
┌─────────────────────────────────────┐
│     MySQL Database                  │
└─────────────────────────────────────┘
```

### Tech Stack

**Frontend (Mobile):**
```yaml
Framework: Flutter 3.19+ (Dart 3.3+)
Language: Dart
Architecture: Clean Architecture + BLoC Pattern
State Management: flutter_bloc + hydrated_bloc (persistent state)
Navigation: go_router (declarative routing)
API Client: dio + retrofit + pretty_dio_logger
Dependency Injection: get_it + injectable (code generation)
Code Generation: freezed + json_serializable (immutable models)
Form Handling: flutter_form_builder + form_builder_validators
UI Components: Material 3 (built-in) + custom widgets
Icons: phosphor_flutter / lucide_icons
Date Picker: flutter_datetime_picker_plus
PDF Viewer: syncfusion_flutter_pdfviewer / native_pdf_view
QR Code Scanner: mobile_scanner (fast, native)
QR Code Generator: qr_flutter
Signature: signature (canvas-based)
Image Picker: image_picker
Camera: camera (official plugin)
Local Storage: shared_preferences + flutter_secure_storage + hive
Push Notifications: firebase_messaging + flutter_local_notifications
Crash Reporting: firebase_crashlytics + sentry_flutter
Analytics: firebase_analytics + mixpanel_flutter
Testing: flutter_test + bloc_test + mocktail + golden_toolkit
```

**Backend (API):**
```yaml
Framework: Laravel 12
Authentication: Laravel Sanctum (token-based)
Database: MySQL 8.0
Cache: Redis
Queue: Redis (for async tasks)
File Storage: AWS S3 / DigitalOcean Spaces
Rate Limiting: Laravel built-in
API Documentation: Scribe / OpenAPI (Swagger)
```

**DevOps:**
```yaml
Version Control: Git (GitHub monorepo with Melos)
CI/CD: GitHub Actions + Codemagic / Fastlane
Beta Distribution: Firebase App Distribution / TestFlight
OTA Updates: Shorebird (Flutter CodePush alternative)
Monitoring: Sentry + Firebase Crashlytics + Firebase Performance
App Store: Apple App Store + Google Play Store
Code Quality: flutter analyze + dart format + very_good_analysis
```

### Security

- [ ] HTTPS only for all API calls (Dio interceptor)
- [ ] Token-based authentication (Sanctum)
- [ ] Secure token storage (flutter_secure_storage)
- [ ] Certificate pinning (dio with certificate pinning)
- [ ] Biometric authentication (local_auth for fingerprint/face ID)
- [ ] Input validation & sanitization (freezed + validators)
- [ ] No sensitive data in logs (custom logger with masking)
- [ ] Code obfuscation (Flutter build --obfuscate)
- [ ] Root/Jailbreak detection (flutter_jailbreak_detection)
- [ ] Compliance with GDPR/privacy laws

### Performance Targets

| Metric | Target | Measurement |
|--------|--------|-------------|
| App launch time | < 3 seconds | Cold start on mid-range device |
| Screen transition | < 300ms | 60 FPS animations |
| API response | < 2 seconds | 95th percentile |
| Certificate generation | < 5 seconds | End-to-end flow |
| Crash-free rate | > 99.5% | Per Firebase Crashlytics |
| App size | < 50 MB | After installation |
| Memory usage | < 200 MB | Typical usage |

---

## 👥 Team & Roles

### Core Team

**Project Manager:**
- Sprint planning & backlog grooming
- Stakeholder communication
- Progress tracking & reporting
- Risk management

**UI/UX Designer:**
- Figma designs for all 3 apps
- Design system maintenance
- User flow optimization
- Usability testing

**Flutter Developer #1 (Lead):**
- Architecture decisions (Clean Architecture + BLoC)
- Outlet Staff App (primary)
- Shared packages setup
- Code reviews
- Technical documentation

**Flutter Developer #2:**
- Patient App (primary)
- Company Admin App (primary)
- Shared widgets library
- Testing & code generation

**Backend Developer:**
- API endpoint development
- Database optimization
- API documentation
- Backend bug fixes

**QA Tester:**
- Test case creation
- Manual testing (all devices)
- Automated testing (Flutter integration tests + widget tests)
- Bug reporting & tracking
- Golden file testing

### Extended Team (Part-time/Consultant)

- DevOps Engineer (CI/CD setup)
- Security Consultant (penetration testing)
- Content Writer (app store descriptions, in-app copy)

---

## 💰 Budget Estimation

### Development Costs (6 months)

| Role | Rate (IDR/month) | Duration | Total (IDR) |
|------|------------------|----------|-------------|
| Project Manager (0.5 FTE) | 15,000,000 | 6 months | 90,000,000 |
| UI/UX Designer (0.5 FTE) | 12,000,000 | 3 months | 36,000,000 |
| Flutter Dev #1 (Senior) | 25,000,000 | 6 months | 150,000,000 |
| Flutter Dev #2 (Mid-level) | 20,000,000 | 6 months | 120,000,000 |
| Backend Developer (0.5 FTE) | 20,000,000 | 3 months | 60,000,000 |
| QA Tester (0.5 FTE) | 10,000,000 | 4 months | 40,000,000 |
| **Subtotal** | | | **496,000,000** |

### Infrastructure & Tools

| Item | Cost (IDR) | Period |
|------|------------|--------|
| Apple Developer Account | 1,500,000 | Yearly |
| Google Play Console | 350,000 | One-time |
| Firebase (Blaze Plan) | 500,000 | Monthly × 6 |
| Shorebird (OTA updates) | 0-1,500,000 | Free tier or Pro |
| Sentry (Error tracking) | 0 | Free tier |
| Codemagic CI/CD | 0-2,000,000 | Free tier or paid |
| Design Tools (Figma Pro) | 150,000 | Monthly × 3 |
| **Subtotal** | | **~6,000,000** |

### **Total Budget: ~Rp 500,000,000** (6 months)

### Alternative: Outsourcing

**If outsourced to agency:**
- Outlet App: Rp 150M - 200M
- Patient App: Rp 100M - 150M
- Company App: Rp 80M - 120M
- **Total**: Rp 330M - 470M (comparable)

**Recommendation**: In-house development for better control & knowledge transfer

---

## 📊 Success Metrics (KPIs)

### Download & Adoption

| Metric | Month 1 | Month 3 | Month 6 |
|--------|---------|---------|---------|
| Total downloads (all apps) | 500 | 2,000 | 5,000 |
| Active users (DAU) | 100 | 500 | 1,500 |
| Outlet App users | 50 | 200 | 500 |
| Patient App users | 300 | 1,500 | 4,000 |
| Company App users | 20 | 100 | 300 |

### Engagement

| Metric | Target |
|--------|--------|
| Certificates issued via mobile | 60% of total |
| Patient app certificate views | 80% of patients |
| Company app weekly active users | >50% of admins |
| Session duration (Outlet App) | >5 minutes |
| Session duration (Patient App) | >2 minutes |

### Quality

| Metric | Target |
|--------|--------|
| App Store rating | >4.5/5.0 |
| Crash-free rate | >99.5% |
| User retention (D7) | >40% |
| User retention (D30) | >25% |
| Uninstall rate | <10% |

### Business Impact

| Metric | Target |
|--------|--------|
| Conversion from web to mobile | +20% |
| Certificate generation speed | +50% faster |
| Customer satisfaction (NPS) | +15 points |
| Support ticket reduction | -30% |

---

## 🎯 Risks & Mitigation

### Technical Risks

**Risk #1: API performance issues**
- Impact: High
- Probability: Medium
- Mitigation: Load testing, caching strategy, CDN for PDFs

**Risk #2: Cross-platform compatibility bugs**
- Impact: Medium
- Probability: High
- Mitigation: Extensive device testing, beta program

**Risk #3: App store rejection**
- Impact: High
- Probability: Low
- Mitigation: Follow guidelines strictly, pre-submission review

### Business Risks

**Risk #4: Low adoption rate**
- Impact: High
- Probability: Medium
- Mitigation: User training, incentives, marketing campaign

**Risk #5: Delayed development**
- Impact: Medium
- Probability: Medium
- Mitigation: Agile methodology, buffer time in schedule

---

## 🚀 Quick Start Guide (For Developers)

### Setup Development Environment

```bash
# 1. Install Flutter SDK (latest stable)
# macOS:
brew install flutter

# Linux:
# Download from https://flutter.dev/docs/get-started/install/linux

# Windows:
# Download from https://flutter.dev/docs/get-started/install/windows

# 2. Verify Flutter installation
flutter doctor

# 3. Install Xcode (iOS development) - macOS only
# Download from Mac App Store
sudo xcode-select --switch /Applications/Xcode.app/Contents/Developer
sudo xcodebuild -runFirstLaunch

# 4. Install Android Studio (Android development)
# Download from developer.android.com
# Install Flutter and Dart plugins

# 5. Install Melos (monorepo management)
dart pub global activate melos

# 6. Clone repository
git clone https://github.com/sehatcert/mobile.git
cd mobile

# 7. Bootstrap monorepo with Melos
melos bootstrap

# 8. Setup environment variables
cp .env.example .env
# Edit .env with API endpoint

# 9. Generate code (for freezed, json_serializable, injectable)
melos run codegen

# 10. Run on iOS (Outlet App example)
cd apps/outlet_app
flutter run

# 11. Run on Android
cd apps/outlet_app
flutter run -d <device-id>

# 12. Run all tests
melos run test

# 13. Format code
melos run format

# 14. Analyze code
melos run analyze
```

### Project Structure

```
sehatcert-mobile/
├── apps/
│   ├── outlet_app/           # Outlet Staff App (Flutter)
│   ├── patient_app/          # Patient App (Flutter)
│   └── company_app/          # Company Admin App (Flutter)
├── packages/
│   ├── core/                 # Core utilities, constants, extensions
│   ├── data/                 # Data layer: API clients, models, repositories
│   ├── domain/               # Domain layer: Entities, use cases, repository interfaces
│   └── presentation/         # Presentation layer: Shared widgets, themes, BLoCs
├── .github/
│   └── workflows/            # CI/CD pipelines (GitHub Actions)
├── melos.yaml                # Monorepo configuration
├── analysis_options.yaml     # Dart/Flutter linting rules
└── README.md
```

Each app follows Clean Architecture:
```
apps/outlet_app/
├── lib/
│   ├── core/
│   │   ├── di/              # Dependency injection (get_it)
│   │   ├── router/          # Navigation (go_router)
│   │   └── theme/           # App theme
│   ├── features/
│   │   ├── auth/
│   │   │   ├── data/        # Data sources, repositories impl
│   │   │   ├── domain/      # Entities, use cases
│   │   │   └── presentation/ # BLoCs, pages, widgets
│   │   ├── dashboard/
│   │   ├── patients/
│   │   └── certificates/
│   └── main.dart            # Entry point
├── test/                    # Unit & widget tests
├── integration_test/        # Integration tests
└── pubspec.yaml
```

---

## 📝 Next Steps

### This Week (Week 1)

- [x] ✅ Mobile app roadmap created
- [ ] Approve roadmap with stakeholders
- [ ] Hire/assign developers
- [ ] Purchase Apple Developer account
- [ ] Purchase Google Play Console account
- [ ] Setup Firebase project

### Next Week (Week 2)

- [ ] Setup development environment
- [ ] Create Figma designs
- [ ] Review & enhance API endpoints
- [ ] Create mobile app repositories
- [ ] Setup CI/CD pipeline

### Week 3 (Sprint 1 Start)

- [ ] Daily standup meetings
- [ ] Sprint planning
- [ ] Start development: Outlet App authentication

---

**Roadmap Version**: 1.0
**Status**: ✅ Ready to Start
**Next Review**: End of Sprint 1 (Week 4)

*Let's build amazing mobile apps for SehatCert! 🚀📱*
