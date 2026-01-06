# 🏥 SURAT SEHAT PLATFORM - Business Plan & Technical Roadmap

**Product Focus**: Multi-Outlet Digital Health Certificate Management Platform
**Target Market**: B2B (Clinics/Healthcare Providers) + B2G (Corporate/Institutional Clients)
**Last Updated**: 2026-01-06

---

## 📋 Table of Contents

1. [Current State Analysis](#current-state-analysis)
2. [Business Model](#business-model)
3. [Core Features Roadmap](#core-features-roadmap)
4. [Subscription Model Design](#subscription-model-design)
5. [Custom Template System](#custom-template-system)
6. [Privacy-Focused Statistics](#privacy-focused-statistics)
7. [MCU Add-on Service](#mcu-add-on-service)
8. [Enhanced Reporting](#enhanced-reporting)
9. [Mobile App Architecture](#mobile-app-architecture)
10. [Rebranding Recommendations](#rebranding-recommendations)

---

## 🔍 Current State Analysis

### ✅ Already Implemented

| Feature | Status | Notes |
|---------|--------|-------|
| Multi-tenancy | ✅ Complete | Tenant isolation working |
| Company Management | ✅ Complete | Company profile & admin accounts |
| Multi-Outlet System | ✅ Complete | Company can have multiple outlets |
| Patient Management | ✅ Complete | With company relation support |
| Surat Sehat Generation | ✅ Complete | MC (Medical Certificate) & SKB (Surat Keterangan Sehat) |
| QR Code Verification | ✅ Complete | Unique code per document |
| Subscription Plans | ✅ Basic | Needs enhancement for quotas |
| Activity Logging | ✅ Complete | Full audit trail |
| Role Management | ✅ Complete | Superadmin, Admin, Outlet, Company, Patient |
| Template System | ⚠️ Partial | Model exists but needs development |

### 🔨 Needs Development

| Feature | Priority | Estimated Time |
|---------|----------|----------------|
| Custom Template Request System | P0 | 2 weeks |
| Outlet/Surat Quota Management | P0 | 1 week |
| Company Statistics Dashboard | P0 | 2 weeks |
| Enhanced Reporting | P1 | 2 weeks |
| MCU Add-on Module | P1 | 3 weeks |
| Mobile App (React Native) | P2 | 8 weeks |

---

## 💼 Business Model

### Value Proposition

**For Clinics/Healthcare Providers (B2B):**
- Digitalize surat sehat issuance process
- Multi-outlet management from single dashboard
- Custom branded templates per clinic
- Reduce paper waste & storage costs
- Compliance & audit trail
- WhatsApp/Email automation

**For Corporate Clients/Institutions (B2G):**
- Real-time verification of employee health certificates
- Statistical insights (privacy-protected)
- Reduce fake certificate fraud
- Automated tracking & reminders
- Integration with HR systems

### Revenue Model

**Subscription-based (SaaS)**:
- Monthly/Yearly billing per company
- Quota-based pricing (outlets + certificates)
- Add-ons: MCU service, custom templates, API access

**Target Pricing** (per company/month):
```
Starter:   Rp 500,000/mo  → 1 outlet,  100 certificates/month
Business:  Rp 1,500,000/mo → 3 outlets, 500 certificates/month
Corporate: Rp 3,500,000/mo → 10 outlets, 2000 certificates/month
Enterprise: Custom pricing  → Unlimited outlets, custom quotas
```

---

## 🎯 Core Features Roadmap

### Phase 1: Core Platform (MVP) - ✅ 95% Complete

- [x] Multi-tenancy
- [x] Company & outlet management
- [x] Patient registration
- [x] Surat sehat generation (MC & SKB)
- [x] QR code verification
- [x] Basic subscription
- [ ] Quota enforcement (outlet + surat)
- [ ] Template customization

### Phase 2: Business Optimization - 🔨 In Progress

**Priority 0 (Must Have):**
1. **Outlet Quota Management**
   - Company can only create outlets up to subscription limit
   - Warning when approaching limit
   - Upgrade flow when limit reached

2. **Certificate Quota Management**
   - Track monthly certificate usage per company
   - Block certificate generation when quota exceeded
   - Auto-reset quota monthly
   - Quota rollover option (for annual plans)

3. **Custom Template Request System**
   - Request form for custom templates
   - Superadmin approval workflow
   - Template assignment to specific companies
   - Template versioning

4. **Company Statistics Dashboard (Privacy-Protected)**
   - Total certificates issued (by month/year)
   - Breakdown by certificate type (MC vs SKB)
   - Top 10 diagnoses (aggregated, no personal data)
   - Patient name validation (for HR verification)
   - Export to Excel/PDF

**Priority 1 (Should Have):**
5. **Enhanced Reporting**
   - Outlet performance comparison
   - Doctor productivity metrics
   - Certificate validity tracking
   - Audit reports for compliance

6. **MCU Add-on Service**
   - MCU package management
   - Booking system integration
   - MCU result attachment to certificates
   - Annual check-up tracking

### Phase 3: Scale & Automation

**Priority 2 (Nice to Have):**
7. **Mobile Applications**
   - Outlet Staff App (certificate issuance)
   - Patient App (view & download certificates)
   - Company Admin App (statistics & verification)

8. **Advanced Features**
   - API for HR system integration
   - Bulk certificate upload
   - E-signature integration
   - Telemedicine integration

---

## 💳 Subscription Model Design

### Enhanced Subscription Schema

```sql
-- Add to subscription_plans table
ALTER TABLE subscription_plans ADD COLUMN max_outlets INT DEFAULT 1;
ALTER TABLE subscription_plans ADD COLUMN max_certificates_per_month INT DEFAULT 100;
ALTER TABLE subscription_plans ADD COLUMN allow_custom_templates BOOLEAN DEFAULT FALSE;
ALTER TABLE subscription_plans ADD COLUMN allow_api_access BOOLEAN DEFAULT FALSE;
ALTER TABLE subscription_plans ADD COLUMN allow_mcu_addon BOOLEAN DEFAULT FALSE;
ALTER TABLE subscription_plans ADD COLUMN includes_whatsapp_notifications BOOLEAN DEFAULT FALSE;

-- Add to tenant_subscriptions table
ALTER TABLE tenant_subscriptions ADD COLUMN outlets_used INT DEFAULT 0;
ALTER TABLE tenant_subscriptions ADD COLUMN certificates_used_this_month INT DEFAULT 0;
ALTER TABLE tenant_subscriptions ADD COLUMN last_quota_reset_at TIMESTAMP;
ALTER TABLE tenant_subscriptions ADD COLUMN custom_outlet_quota INT NULL;
ALTER TABLE tenant_subscriptions ADD COLUMN custom_certificate_quota INT NULL;
```

### Subscription Packages

#### 1. **Starter Plan** - Rp 500,000/month
```yaml
Target: Small clinics, single location
Features:
  - 1 outlet
  - 100 certificates/month
  - Standard templates only
  - Email notifications
  - Basic statistics
  - QR verification
  - 24/7 support (email)
```

#### 2. **Business Plan** - Rp 1,500,000/month
```yaml
Target: Growing clinics, multiple locations
Features:
  - 3 outlets
  - 500 certificates/month
  - 1 custom template request/year
  - WhatsApp + Email notifications
  - Advanced statistics
  - Outlet performance reports
  - Priority support (chat)
  - API access (read-only)
```

#### 3. **Corporate Plan** - Rp 3,500,000/month
```yaml
Target: Clinic chains, hospital networks
Features:
  - 10 outlets
  - 2,000 certificates/month
  - 3 custom template requests/year
  - WhatsApp + Email + SMS notifications
  - Full analytics dashboard
  - Company portal access
  - Bulk operations
  - API access (full CRUD)
  - Dedicated account manager
  - MCU add-on (optional)
```

#### 4. **Enterprise Plan** - Custom Pricing
```yaml
Target: Large healthcare groups, government institutions
Features:
  - Unlimited outlets
  - Custom certificate quotas
  - Unlimited custom templates
  - White-label branding
  - On-premise deployment option
  - Custom integrations
  - SLA guarantee (99.9% uptime)
  - 24/7 phone support
  - Training & onboarding
```

### Add-ons (Any Plan)

| Add-on | Price | Description |
|--------|-------|-------------|
| Extra Outlet | Rp 150,000/month | Add 1 more outlet |
| Extra 100 Certificates | Rp 100,000/month | Increase quota by 100 |
| Custom Template | Rp 500,000 one-time | One exclusive template |
| MCU Module | Rp 1,000,000/month | Full MCU booking & reporting |
| API Access | Rp 750,000/month | For Business plan upgrade |
| Priority Support | Rp 300,000/month | For Starter plan upgrade |

### Quota Enforcement Logic

```php
// app/Services/QuotaService.php
class QuotaService
{
    /**
     * Check if company can create new outlet
     */
    public function canCreateOutlet(Company $company): bool
    {
        $subscription = $company->activeSubscription;
        $maxOutlets = $subscription->custom_outlet_quota
            ?? $subscription->plan->max_outlets;

        $currentOutlets = $company->outlets()->count();

        return $currentOutlets < $maxOutlets;
    }

    /**
     * Check if company can issue new certificate
     */
    public function canIssueCertificate(Company $company): bool
    {
        $subscription = $company->activeSubscription;

        // Reset quota if new month
        $this->resetQuotaIfNeeded($subscription);

        $maxCerts = $subscription->custom_certificate_quota
            ?? $subscription->plan->max_certificates_per_month;

        return $subscription->certificates_used_this_month < $maxCerts;
    }

    /**
     * Increment certificate usage
     */
    public function incrementCertificateUsage(Company $company): void
    {
        $subscription = $company->activeSubscription;
        $subscription->increment('certificates_used_this_month');
    }

    /**
     * Reset monthly quota if new month
     */
    private function resetQuotaIfNeeded(TenantSubscription $subscription): void
    {
        if (!$subscription->last_quota_reset_at
            || $subscription->last_quota_reset_at->month !== now()->month
        ) {
            $subscription->update([
                'certificates_used_this_month' => 0,
                'last_quota_reset_at' => now(),
            ]);
        }
    }

    /**
     * Get remaining quota
     */
    public function getRemainingQuota(Company $company): array
    {
        $subscription = $company->activeSubscription;
        $this->resetQuotaIfNeeded($subscription);

        $maxCerts = $subscription->custom_certificate_quota
            ?? $subscription->plan->max_certificates_per_month;

        $maxOutlets = $subscription->custom_outlet_quota
            ?? $subscription->plan->max_outlets;

        $currentOutlets = $company->outlets()->count();

        return [
            'certificates' => [
                'used' => $subscription->certificates_used_this_month,
                'max' => $maxCerts,
                'remaining' => max(0, $maxCerts - $subscription->certificates_used_this_month),
                'percentage' => round(($subscription->certificates_used_this_month / $maxCerts) * 100, 1),
            ],
            'outlets' => [
                'used' => $currentOutlets,
                'max' => $maxOutlets,
                'remaining' => max(0, $maxOutlets - $currentOutlets),
                'percentage' => round(($currentOutlets / $maxOutlets) * 100, 1),
            ],
        ];
    }
}
```

---

## 🎨 Custom Template System

### Database Schema

```sql
CREATE TABLE template_requests (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    company_id BIGINT UNSIGNED NOT NULL,
    request_number VARCHAR(50) UNIQUE NOT NULL,
    template_name VARCHAR(255) NOT NULL,
    description TEXT,
    usage_purpose TEXT,

    -- Request details
    requested_by INT UNSIGNED NOT NULL,
    requested_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    -- Branding requirements
    company_logo_url VARCHAR(500),
    header_text TEXT,
    footer_text TEXT,
    color_scheme VARCHAR(50),
    additional_fields JSON,

    -- Sample/mockup
    mockup_file_url VARCHAR(500),
    reference_template_id BIGINT UNSIGNED NULL,

    -- Approval workflow
    status ENUM('pending', 'in_review', 'approved', 'rejected', 'completed') DEFAULT 'pending',
    reviewed_by INT UNSIGNED NULL,
    reviewed_at TIMESTAMP NULL,
    review_notes TEXT NULL,

    -- Implementation
    implemented_by INT UNSIGNED NULL,
    implemented_at TIMESTAMP NULL,
    template_result_id BIGINT UNSIGNED NULL, -- Link to actual template

    -- Rejection reason
    rejection_reason TEXT NULL,

    FOREIGN KEY (tenant_id) REFERENCES tenants(id),
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (requested_by) REFERENCES users(id),
    FOREIGN KEY (reviewed_by) REFERENCES users(id),
    FOREIGN KEY (template_result_id) REFERENCES template_results(id),

    INDEX idx_company_status (company_id, status),
    INDEX idx_status (status),
    INDEX idx_request_number (request_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Enhance template_results table
ALTER TABLE template_results ADD COLUMN is_exclusive BOOLEAN DEFAULT FALSE;
ALTER TABLE template_results ADD COLUMN exclusive_for_company_id BIGINT UNSIGNED NULL;
ALTER TABLE template_results ADD COLUMN template_category ENUM('standard', 'premium', 'custom') DEFAULT 'standard';
ALTER TABLE template_results ADD COLUMN template_version VARCHAR(20) DEFAULT '1.0';
ALTER TABLE template_results ADD COLUMN is_active BOOLEAN DEFAULT TRUE;
```

### Template Request Workflow

```
1. Company Admin submits request
   ↓
2. Superadmin receives notification
   ↓
3. Superadmin reviews request
   ├─→ Approve → Assign to developer → Create custom template → Mark completed
   └─→ Reject → Notify company with reason
```

### Implementation Components

**1. Request Form** (`resources/views/company/templates/request.blade.php`)
```php
// Fields:
- Template Name
- Purpose/Usage
- Company Logo Upload
- Header/Footer Text
- Color Scheme Selection
- Additional Fields (JSON)
- Mockup/Reference Upload
```

**2. Superadmin Review Panel** (`resources/views/superadmin/template-requests/index.blade.php`)
```php
// Features:
- List all pending requests
- Filter by status
- View request details
- Approve/Reject with notes
- Assign to developer
```

**3. Template Builder** (Manual for MVP, can automate later)
```php
// Superadmin creates Blade template file manually
// File naming: company-{company_id}-{template_name}.blade.php
// Save to: resources/views/templates/custom/
```

---

## 📊 Privacy-Focused Statistics

### Company Statistics Dashboard

**Principle**: Company/Instansi can only see **aggregated anonymous data** + **name validation**

### Allowed Data

✅ **Aggregated Statistics:**
- Total certificates issued (by month/year/type)
- Certificate type breakdown (MC vs SKB)
- Top 10 diagnoses (without patient names)
- Certificate validity distribution
- Outlet performance comparison
- Monthly trends

✅ **Name Validation Only:**
- Employee submits name → System returns "FOUND" or "NOT FOUND"
- No other patient data exposed
- Audit log of who checked what name

### Restricted Data

❌ **Not Accessible:**
- Individual patient health details
- Specific diagnoses per person
- Doctor names/signatures
- Certificate content/text
- NIK or personal identifiers

### Database Schema

```sql
CREATE TABLE company_statistics_cache (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    period_type ENUM('daily', 'weekly', 'monthly', 'yearly') NOT NULL,
    period_date DATE NOT NULL,

    -- Aggregated metrics
    total_certificates INT DEFAULT 0,
    total_mc INT DEFAULT 0,
    total_skb INT DEFAULT 0,

    -- Top diagnoses (no patient link)
    top_diagnoses JSON, -- [{"name": "Flu", "count": 15}, ...]

    -- Outlet breakdown
    outlet_stats JSON, -- [{"outlet_id": 1, "count": 20}, ...]

    -- Generated at
    generated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (company_id) REFERENCES companies(id),
    UNIQUE KEY unique_company_period (company_id, period_type, period_date),
    INDEX idx_company_period (company_id, period_date)
) ENGINE=InnoDB;

CREATE TABLE company_name_verifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    verified_by INT UNSIGNED NOT NULL, -- Company admin who checked
    patient_name_searched VARCHAR(255) NOT NULL,
    found BOOLEAN NOT NULL,
    verified_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),

    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (verified_by) REFERENCES users(id),
    INDEX idx_company_verifications (company_id, verified_at)
) ENGINE=InnoDB;
```

### API Endpoints for Company Portal

```php
// routes/api.php - Company Admin only

// Get aggregated statistics
GET /api/company/statistics?period={monthly}&year={2026}&month={1}

// Verify patient name
POST /api/company/verify-patient
{
    "patient_name": "John Doe"
}
// Response: {"found": true} atau {"found": false}

// Export statistics
GET /api/company/statistics/export?format={pdf|excel}&period={monthly}&year={2026}
```

### Implementation

```php
// app/Services/CompanyStatisticsService.php
class CompanyStatisticsService
{
    /**
     * Generate monthly statistics for company
     */
    public function generateMonthlyStats(Company $company, Carbon $month): array
    {
        $results = Result::where('company_id', $company->id)
            ->whereYear('created_at', $month->year)
            ->whereMonth('created_at', $month->month)
            ->get();

        return [
            'period' => $month->format('F Y'),
            'total_certificates' => $results->count(),
            'total_mc' => $results->where('type', 'MC')->count(),
            'total_skb' => $results->where('type', 'SKB')->count(),
            'top_diagnoses' => $this->getTopDiagnoses($results, 10),
            'outlet_breakdown' => $this->getOutletBreakdown($results),
            'monthly_trend' => $this->getMonthlyTrend($company, 6), // Last 6 months
        ];
    }

    /**
     * Get top diagnoses (anonymous)
     */
    private function getTopDiagnoses($results, int $limit = 10): array
    {
        return $results
            ->groupBy('medical_diagnosis_id')
            ->map(function ($group) {
                return [
                    'diagnosis' => $group->first()->diagnosis->name ?? 'Unknown',
                    'count' => $group->count(),
                ];
            })
            ->sortByDesc('count')
            ->take($limit)
            ->values()
            ->toArray();
    }

    /**
     * Verify patient name (for HR verification)
     */
    public function verifyPatientName(Company $company, string $patientName, User $verifiedBy): array
    {
        $exists = Patient::where('company_id', $company->id)
            ->where('full_name', 'LIKE', "%{$patientName}%")
            ->exists();

        // Log verification attempt
        CompanyNameVerification::create([
            'company_id' => $company->id,
            'verified_by' => $verifiedBy->id,
            'patient_name_searched' => $patientName,
            'found' => $exists,
            'ip_address' => request()->ip(),
        ]);

        return [
            'found' => $exists,
            'message' => $exists
                ? 'Patient name found in records'
                : 'Patient name not found',
        ];
    }
}
```

---

## 🏥 MCU Add-on Service

### Overview

MCU (Medical Check-Up) as **optional add-on** service for existing Surat Sehat platform users.

### Features

1. **MCU Package Management**
   - Pre-defined packages (Basic, Standard, Executive, VIP)
   - Custom package builder for corporate clients
   - Pricing management

2. **Online Booking**
   - Patient self-booking
   - Company bulk booking
   - Calendar availability
   - Payment integration

3. **MCU Workflow**
   - Check-in with QR code
   - Station-by-station tracking
   - Result input by medical staff
   - Doctor review & sign-off

4. **MCU Reporting**
   - Comprehensive health report PDF
   - Integration with Surat Sehat
   - Auto-issue SKB if healthy
   - Follow-up reminders

### Integration with Surat Sehat

```
MCU Complete → Generate Health Report → Auto-issue SKB (if fit)
```

### Pricing

**For Companies with Surat Sehat Subscription:**
- MCU Module: +Rp 1,000,000/month
- Includes: Booking system, reporting, 10% commission on each MCU

**Commission Model:**
- Platform takes 10-15% of MCU package price
- Clinic/Outlet gets 85-90%
- Auto-settlement monthly

### Database Schema (Already Exists)

```
✅ mcu_providers
✅ mcu_packages
✅ mcu_bookings
✅ payments
✅ reviews
```

---

## 📈 Enhanced Reporting

### Report Types

#### 1. **Outlet Performance Report**
```yaml
Metrics:
  - Total certificates issued
  - Certificates per doctor
  - Average processing time
  - Peak hours analysis
  - Patient satisfaction (if reviews enabled)
Export: PDF, Excel
Frequency: Daily, Weekly, Monthly
```

#### 2. **Doctor Productivity Report**
```yaml
Metrics:
  - Certificates issued per doctor
  - Average consultation time
  - Diagnosis distribution
  - Working hours utilization
Export: PDF, Excel
Frequency: Weekly, Monthly
Access: Outlet Admin, Company Admin
```

#### 3. **Certificate Validity Tracking**
```yaml
Purpose: Track expiring certificates for companies
Metrics:
  - Certificates expiring this week/month
  - Expired certificates count
  - Renewal reminders sent
Use Case: Corporate HR department
```

#### 4. **Compliance Audit Report**
```yaml
Purpose: For regulatory compliance (Kemenkes, audit)
Includes:
  - All certificates issued (date range)
  - Doctor credentials verification
  - QR code integrity check
  - Activity log summary
Export: PDF (official letterhead)
```

#### 5. **Revenue Report** (For Clinic Owners)
```yaml
Metrics:
  - Total certificates issued
  - Revenue per certificate type
  - Subscription revenue
  - MCU commission (if enabled)
  - Month-over-month growth
Export: Excel (with charts)
```

### Implementation

```php
// app/Services/ReportService.php
class ReportService
{
    public function generateOutletPerformance(Outlet $outlet, Carbon $startDate, Carbon $endDate)
    {
        // Generate report logic
    }

    public function generateDoctorProductivity(Doctor $doctor, string $period)
    {
        // Generate report logic
    }

    public function generateComplianceAudit(Company $company, Carbon $startDate, Carbon $endDate)
    {
        // Generate official audit report
    }

    public function scheduleAutomatedReports()
    {
        // Cron job to auto-generate & email reports
    }
}
```

---

## 📱 Mobile App Architecture

### Platform: **React Native** (Cross-platform: iOS + Android)

### 3 Separate Apps

#### 1. **Outlet Staff App** (Priority 1)

**Purpose**: Certificate issuance on-the-go

**Features**:
- Login (outlet staff credentials)
- Patient search/registration
- Quick certificate generation
- QR code scanner (for patient check-in)
- Camera for patient photo
- E-signature capture
- Send WhatsApp/Email directly
- Offline mode (sync when online)

**Screens**:
```
1. Login
2. Dashboard (today's certificates, quick actions)
3. Patient Search/Add
4. Certificate Form (MC/SKB)
5. Diagnosis Selection
6. Doctor Selection
7. Preview & Sign
8. Send Notification
9. Certificate History
10. Settings
```

**Tech Stack**:
```yaml
Framework: React Native (Expo)
State Management: Redux Toolkit / Zustand
API: REST (Laravel Sanctum auth)
Offline: Redux Persist + Queue
Camera: expo-camera
QR: react-native-qrcode-scanner
E-signature: react-native-signature-canvas
Push Notifications: Expo Notifications
```

#### 2. **Patient App** (Priority 2)

**Purpose**: View & download certificates

**Features**:
- Register/Login
- View all my certificates
- Download PDF
- Share via WhatsApp/Email
- QR code display (for verification)
- Certificate verification (scan others' QR)
- Request new certificate
- MCU booking (if add-on enabled)
- Health history

**Screens**:
```
1. Onboarding
2. Login/Register
3. Home (certificate list)
4. Certificate Detail
5. Certificate PDF Viewer
6. QR Code Display
7. Verify Certificate (scan QR)
8. Request Certificate (redirect to outlet)
9. Profile
10. Settings
```

#### 3. **Company Admin App** (Priority 3)

**Purpose**: Statistics & verification on-the-go

**Features**:
- Login (company admin)
- Dashboard (statistics)
- Name verification tool
- Export reports
- View outlet performance
- Approve template requests
- Notification settings

**Screens**:
```
1. Login
2. Dashboard (charts & metrics)
3. Statistics Detail
4. Name Verification
5. Outlet List
6. Outlet Detail
7. Reports
8. Settings
```

### API Requirements

All apps will consume REST API dari Laravel backend:

```
Base URL: https://api.suratsehat.id/v1/

Authentication: Laravel Sanctum (token-based)

Endpoints:
- /auth/login
- /auth/logout
- /auth/refresh

Outlet App:
- /patients (search, create)
- /certificates (create, list, detail)
- /doctors (list)
- /diagnoses (search)
- /outlets/me

Patient App:
- /my/certificates
- /my/profile
- /certificates/{id}/verify

Company App:
- /company/statistics
- /company/verify-patient
- /company/outlets
- /company/reports
```

### Development Timeline

| Phase | Duration | Deliverable |
|-------|----------|-------------|
| **Phase 1**: API Enhancement | 2 weeks | REST API complete & documented |
| **Phase 2**: Outlet App MVP | 4 weeks | Core features (certificate issuance) |
| **Phase 3**: Patient App MVP | 3 weeks | View & download certificates |
| **Phase 4**: Company App MVP | 3 weeks | Statistics & verification |
| **Phase 5**: Beta Testing | 2 weeks | Bug fixes & improvements |
| **Phase 6**: Production Release | 1 week | App Store & Play Store submission |

**Total Estimated Time**: 15 weeks (~ 4 months)

---

## 🎨 Rebranding Recommendations

Given the **clear focus on Surat Sehat (Health Certificates)**, here are tailored name recommendations:

### **Tier 1: Professional & Medical-Focused** ⭐ RECOMMENDED

1. **CertifyMed**
   - **Tagline**: *"Digital Health Certificates, Simplified"*
   - **Makna**: Certification + Medical
   - **Domain**: `certifymed.id` / `certifymed.co.id`
   - **Appeal**: Professional, medical authority, clear purpose
   - **Rating**: 9.5/10

2. **MediCert**
   - **Tagline**: *"Your Trusted Medical Certificate Platform"*
   - **Makna**: Medical + Certificate
   - **Domain**: `medicert.id`
   - **Appeal**: Direct, trustworthy, easy to remember
   - **Rating**: 9.3/10

3. **SehatCert**
   - **Tagline**: *"Platform Surat Sehat Digital Indonesia"*
   - **Makna**: Sehat (Healthy) + Certificate
   - **Domain**: `sehatcert.id` / `sehatcert.com`
   - **Appeal**: Indonesian market fit, clear purpose
   - **Rating**: 9.2/10

### **Tier 2: Modern & Tech-Savvy**

4. **CertifyHealth**
   - **Tagline**: *"Certifying Health, Digitally"*
   - **Domain**: `certifyhealth.id`
   - **Appeal**: Professional, global-ready
   - **Rating**: 9.0/10

5. **DocuHealth**
   - **Tagline**: *"Healthcare Documentation Platform"*
   - **Makna**: Document + Health
   - **Domain**: `docuhealth.id`
   - **Appeal**: Broader scope (can expand beyond certificates)
   - **Rating**: 8.8/10

6. **VerifyMed**
   - **Tagline**: *"Verify. Trust. Proceed."*
   - **Makna**: Emphasizes verification aspect
   - **Domain**: `verifymed.id`
   - **Appeal**: Security-focused positioning
   - **Rating**: 8.7/10

### **Tier 3: Indonesian Touch**

7. **SuratSehat.id** (Domain Branding)
   - **Tagline**: *"Solusi Surat Kesehatan Digital"*
   - **Makna**: Langsung ke sasaran
   - **Domain**: `suratsehat.id` (premium domain)
   - **Appeal**: SEO friendly, easy to find
   - **Rating**: 8.5/10

8. **DigiSehat**
   - **Tagline**: *"Digitalisasi Surat Kesehatan"*
   - **Domain**: `digisehat.id`
   - **Appeal**: Modern + Indonesian
   - **Rating**: 8.3/10

9. **WijayaCert** (Maintain Family Brand)
   - **Tagline**: *"From Wijaya, For Your Health"*
   - **Domain**: `wijayacert.id`
   - **Appeal**: Family legacy + new direction
   - **Rating**: 8.0/10

### **🏆 Top 3 Final Recommendations**

| Rank | Name | Score | Why? |
|------|------|-------|------|
| 🥇 | **CertifyMed** | 9.5/10 | Perfect balance: professional, clear, global-ready, memorable |
| 🥈 | **MediCert** | 9.3/10 | Direct & trustworthy, strong medical authority positioning |
| 🥉 | **SehatCert** | 9.2/10 | Indonesian market appeal, SEO-friendly, descriptive |

### Brand Positioning Matrix

| Name | Medical Authority | Indonesian Appeal | Tech-Savvy | Scalability | SEO |
|------|------------------|-------------------|------------|-------------|-----|
| CertifyMed | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ |
| MediCert | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ |
| SehatCert | ⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ |

### Next Steps for Rebranding

1. **Domain Check**: Verify availability
   ```bash
   whois certifymed.id
   whois medicert.id
   whois sehatcert.id
   ```

2. **Trademark Search**: Check DJKI (Direktorat Jenderal Kekayaan Intelektual)

3. **Logo Design**: Hire designer for professional logo
   - Colors: Blue (trust), Green (health), White (clean)
   - Style: Modern, minimalist, professional

4. **Brand Guidelines**: Create brand manual
   - Logo usage
   - Color palette
   - Typography
   - Tone of voice

---

## 🚀 Implementation Priority

### Q1 2026 (Jan - Mar) - Foundation

- [x] PHPStan audit & code quality
- [ ] Enhanced subscription with quotas (2 weeks)
- [ ] Custom template request system (2 weeks)
- [ ] Company statistics dashboard (2 weeks)
- [ ] API documentation (1 week)
- [ ] Rebranding decision & domain purchase (1 week)

### Q2 2026 (Apr - Jun) - Enhancement

- [ ] Enhanced reporting system (3 weeks)
- [ ] MCU add-on module (3 weeks)
- [ ] Mobile app API preparation (2 weeks)
- [ ] Beta testing with pilot clients (ongoing)

### Q3 2026 (Jul - Sep) - Mobile Launch

- [ ] Outlet staff app development (4 weeks)
- [ ] Patient app development (3 weeks)
- [ ] Company admin app development (3 weeks)
- [ ] Mobile app beta testing (2 weeks)
- [ ] App store submission (1 week)

### Q4 2026 (Oct - Dec) - Scale

- [ ] Marketing campaign
- [ ] Enterprise client acquisition
- [ ] Feature enhancements based on feedback
- [ ] API v2 with GraphQL
- [ ] White-label offering

---

## 📞 Support & Resources

**Target Launch Date**: Q2 2026 (with mobile apps by Q3)

**For Questions/Discussion**:
- Technical: Check PHPStan audit results in `phpstan-results.txt`
- Business: Review this document
- Development: Follow roadmap priorities

**Success Metrics (Year 1)**:
- ✅ 50+ clinic/company clients
- ✅ 10,000+ certificates issued/month
- ✅ 95%+ uptime SLA
- ✅ 4.5+ star rating (from users)
- ✅ Mobile apps with 5,000+ downloads

---

**Document Owner**: Tech Lead + Product Manager
**Status**: ✅ Ready for Development & Business Review
**Version**: 1.0 - Comprehensive Business Plan
