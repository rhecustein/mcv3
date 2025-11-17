# 🚀 SaaS Transformation Plan - Medical Certificate v3
## Kimia Farma Health SaaS Platform

**Version:** 1.0
**Date:** 2025-11-13
**Status:** Planning Phase

---

## 📋 Executive Summary

Transformasi dari single-tenant medical certificate system menjadi **multi-tenant SaaS platform** dengan 3 modul terintegrasi:

1. **MODUL 1**: Manajemen Surat Kesehatan (Enhanced)
2. **MODUL 2**: Medical Check-Up (MCU) System (NEW)
3. **MODUL 3**: Portal Perusahaan B2B (NEW)

**Target Market:**
- **B2C**: Pasien individual untuk MCU marketplace
- **B2B**: Klinik/RS untuk subscription SaaS
- **B2B2C**: Perusahaan untuk employee health management

---

## 🎯 PDF Optimization (✅ COMPLETED)

### Implementasi yang Sudah Selesai:

1. **DomPDF Configuration**
   - ✅ Enable font subsetting (reduces font size ~40%)
   - ✅ Lower DPI from 96 to 72 (reduces image size ~25%)
   - File: `config/dompdf.php`

2. **Image Optimization Helper**
   - ✅ Created `ImageOptimizer` class in `app/helpers.php`
   - ✅ Compress PNG images from 412KB to ~80KB (80% reduction)
   - ✅ Caching mechanism (24 hours)
   - Features:
     - Resize images (max width 400px for logos, 300px for icons)
     - Quality compression (70%)
     - Preserve transparency
     - Base64 data URL embedding

3. **PDF Templates Updated**
   - ✅ `resources/views/pdf/surat_sakit.blade.php`
   - ✅ `resources/views/pdf/surat_sehat.blade.php`
   - Uses optimized logo and icon images

### Expected Results:
- **Before**: ~2-3 MB per PDF
- **After**: ~400-600 KB per PDF (70-80% reduction)

---

## 🏗️ Phase 1: Multi-Tenancy Foundation (Weeks 1-3)

### 1.1 Database Architecture

#### New Core Tables:

```sql
-- Tenants (Klinik/RS as tenant)
CREATE TABLE tenants (
    id BIGINT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    domain VARCHAR(255) UNIQUE, -- custom domain
    logo VARCHAR(255),
    subscription_plan VARCHAR(50), -- starter, professional, enterprise
    subscription_status ENUM('trial', 'active', 'suspended', 'cancelled'),
    trial_ends_at TIMESTAMP,
    subscription_ends_at TIMESTAMP,
    max_users INT DEFAULT 10,
    max_documents INT DEFAULT 1000,
    max_storage_mb INT DEFAULT 5000,
    settings JSON, -- tenant-specific settings
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Tenant subscriptions & billing
CREATE TABLE tenant_subscriptions (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT,
    plan_name VARCHAR(50),
    price DECIMAL(10,2),
    billing_cycle ENUM('monthly', 'yearly'),
    starts_at TIMESTAMP,
    ends_at TIMESTAMP,
    auto_renew BOOLEAN DEFAULT TRUE,
    payment_method VARCHAR(50),
    status VARCHAR(20),
    FOREIGN KEY (tenant_id) REFERENCES tenants(id)
);

-- Usage tracking
CREATE TABLE tenant_usage (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT,
    period_start DATE,
    period_end DATE,
    documents_generated INT DEFAULT 0,
    mcu_bookings INT DEFAULT 0,
    storage_used_mb DECIMAL(10,2),
    api_calls INT DEFAULT 0,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id)
);
```

#### Modified Existing Tables:
- Add `tenant_id` to: users, outlets, results, patients, doctors, companies, document_queues
- Create unique indexes: `(tenant_id, slug/email/etc)`

### 1.2 Multi-Tenancy Implementation

**Package:** `stancl/tenancy` (Laravel multi-tenancy package)

```php
// app/Models/Tenant.php
class Tenant extends TenancyModel
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'domain', 'logo',
        'subscription_plan', 'subscription_status',
        'trial_ends_at', 'subscription_ends_at',
        'max_users', 'max_documents', 'max_storage_mb',
        'settings'
    ];

    protected $casts = [
        'settings' => 'array',
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
    ];

    public function isTrialing() { /* ... */ }
    public function isSubscribed() { /* ... */ }
    public function canCreateUser() { /* ... */ }
    public function canGenerateDocument() { /* ... */ }
}
```

**Tenant Detection Methods:**
1. **Subdomain**: `tenant1.mcv3.com`, `tenant2.mcv3.com`
2. **Custom Domain**: `clinic-abc.com` → mapped to tenant
3. **Path-based** (admin): `mcv3.com/admin/tenant1`

### 1.3 Subscription Plans

| Feature | Starter | Professional | Enterprise |
|---------|---------|--------------|------------|
| **Price/Month** | Rp 500K | Rp 2 juta | Custom |
| Max Users | 5 | 25 | Unlimited |
| Max Documents/month | 500 | 5,000 | Unlimited |
| Storage | 2 GB | 20 GB | Unlimited |
| MCU Marketplace | ❌ | ✅ | ✅ |
| API Access | ❌ | ✅ | ✅ |
| Custom Domain | ❌ | ✅ | ✅ |
| White-label | ❌ | ❌ | ✅ |
| Support | Email | Priority | Dedicated |

---

## 📦 Phase 2: MODUL 1 Enhancement (Weeks 4-5)

### 2.1 Multi-Template System

```php
// database/migrations/create_document_templates_table.php
CREATE TABLE document_templates (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT,
    name VARCHAR(255),
    type ENUM('mc', 'skb'), -- medical certificate, surat keterangan berobat
    blade_template VARCHAR(255), -- path to blade file
    thumbnail VARCHAR(255),
    is_default BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    settings JSON, -- template-specific settings (colors, fonts, etc)
    created_at TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id)
);
```

**Features:**
- Visual template editor (WYSIWYG)
- Custom branding per template
- Multi-language support
- Template marketplace (public templates)

### 2.2 Enhanced PDF Compression

Already implemented! (See PDF Optimization section above)

### 2.3 Notification Enhancement

```php
// app/Services/NotificationService.php
- WhatsApp Business API integration
- Email templating engine
- SMS gateway integration
- Push notifications (web + mobile)
- Notification preferences per user
- Bulk notifications
```

### 2.4 Analytics Dashboard

```php
// Real-time metrics:
- Documents generated (today, week, month, year)
- Top doctors by volume
- Top companies by requests
- Document types breakdown
- Success/failure rates
- Average generation time
- Peak usage hours
```

---

## 🏥 Phase 3: MODUL 2 - MCU System (Weeks 6-10)

### 3.1 Database Schema

```sql
-- MCU Packages
CREATE TABLE mcu_packages (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT, -- klinik yang offering
    name VARCHAR(255),
    slug VARCHAR(255),
    category ENUM('basic', 'standard', 'premium', 'executive', 'custom'),
    description TEXT,
    short_description VARCHAR(500),
    price DECIMAL(10,2),
    discounted_price DECIMAL(10,2),
    duration_minutes INT, -- estimated duration
    thumbnail VARCHAR(255),
    images JSON, -- multiple images
    is_active BOOLEAN DEFAULT TRUE,
    is_featured BOOLEAN DEFAULT FALSE,
    min_age INT,
    max_age INT,
    gender ENUM('male', 'female', 'all'),
    tags JSON, -- ['rekrutmen', 'visa', 'general']
    benefits JSON, -- list of benefits
    preparation_notes TEXT,
    terms_conditions TEXT,
    created_at TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id)
);

-- MCU Package Items (exams included)
CREATE TABLE mcu_package_items (
    id BIGINT PRIMARY KEY,
    package_id BIGINT,
    exam_category VARCHAR(100), -- 'Lab', 'Radiologi', 'EKG', etc
    exam_name VARCHAR(255),
    description TEXT,
    sort_order INT,
    FOREIGN KEY (package_id) REFERENCES mcu_packages(id)
);

-- MCU Bookings
CREATE TABLE mcu_bookings (
    id BIGINT PRIMARY KEY,
    booking_number VARCHAR(50) UNIQUE,
    tenant_id BIGINT, -- klinik
    package_id BIGINT,
    patient_id BIGINT,
    booking_type ENUM('individual', 'corporate'), -- B2C or B2B
    company_id BIGINT, -- if corporate booking

    -- Schedule
    scheduled_date DATE,
    scheduled_time TIME,
    duration_minutes INT,

    -- Pricing
    original_price DECIMAL(10,2),
    discount_amount DECIMAL(10,2),
    final_price DECIMAL(10,2),
    promo_code VARCHAR(50),

    -- Payment
    payment_status ENUM('pending', 'paid', 'refunded', 'failed'),
    payment_method VARCHAR(50),
    payment_reference VARCHAR(100),
    payment_date TIMESTAMP,

    -- Status
    status ENUM('pending', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show'),
    confirmation_code VARCHAR(20),
    qr_code TEXT,

    -- Communication
    notes TEXT,
    special_requests TEXT,
    reminder_sent_at TIMESTAMP,

    -- Workflow
    checked_in_at TIMESTAMP,
    started_at TIMESTAMP,
    completed_at TIMESTAMP,
    report_generated_at TIMESTAMP,
    report_sent_at TIMESTAMP,

    cancelled_at TIMESTAMP,
    cancellation_reason TEXT,

    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (tenant_id) REFERENCES tenants(id),
    FOREIGN KEY (package_id) REFERENCES mcu_packages(id),
    FOREIGN KEY (patient_id) REFERENCES patients(id),
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

-- MCU Results (per exam/item)
CREATE TABLE mcu_results (
    id BIGINT PRIMARY KEY,
    booking_id BIGINT,
    exam_category VARCHAR(100),
    exam_name VARCHAR(255),
    result_value VARCHAR(500),
    result_unit VARCHAR(50),
    reference_range VARCHAR(100),
    interpretation ENUM('normal', 'abnormal', 'critical', 'borderline'),
    notes TEXT,
    performed_by_user_id BIGINT, -- doctor/technician
    performed_at TIMESTAMP,
    verified_by_user_id BIGINT,
    verified_at TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES mcu_bookings(id)
);

-- MCU Reports (final PDF)
CREATE TABLE mcu_reports (
    id BIGINT PRIMARY KEY,
    booking_id BIGINT,
    report_number VARCHAR(50) UNIQUE,
    pdf_path VARCHAR(255),
    pdf_size_kb INT,
    total_pages INT,
    summary TEXT,
    recommendations TEXT,
    doctor_id BIGINT,
    doctor_signature VARCHAR(255),
    signed_at TIMESTAMP,
    sent_at TIMESTAMP,
    downloaded_at TIMESTAMP,
    created_at TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES mcu_bookings(id),
    FOREIGN KEY (doctor_id) REFERENCES doctors(id)
);

-- MCU Marketplace Reviews
CREATE TABLE mcu_reviews (
    id BIGINT PRIMARY KEY,
    package_id BIGINT,
    booking_id BIGINT,
    patient_id BIGINT,
    rating INT, -- 1-5
    review_text TEXT,
    pros TEXT,
    cons TEXT,
    is_verified BOOLEAN DEFAULT FALSE,
    is_published BOOLEAN DEFAULT TRUE,
    helpful_count INT DEFAULT 0,
    created_at TIMESTAMP,
    FOREIGN KEY (package_id) REFERENCES mcu_packages(id),
    FOREIGN KEY (booking_id) REFERENCES mcu_bookings(id),
    FOREIGN KEY (patient_id) REFERENCES patients(id)
);

-- Promo Codes
CREATE TABLE promo_codes (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT,
    code VARCHAR(50) UNIQUE,
    description TEXT,
    discount_type ENUM('percentage', 'fixed'),
    discount_value DECIMAL(10,2),
    min_purchase DECIMAL(10,2),
    max_discount DECIMAL(10,2),
    usage_limit INT,
    usage_count INT DEFAULT 0,
    valid_from TIMESTAMP,
    valid_until TIMESTAMP,
    applicable_packages JSON, -- specific package IDs or null for all
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id)
);
```

### 3.2 MCU Booking Flow

**Frontend (B2C Marketplace):**

```
1. Browse Packages
   └─ Filter by: location, price, type, rating
   └─ Compare packages side-by-side
   └─ View details & reviews

2. Select Package
   └─ Check availability calendar
   └─ Choose date & time slot
   └─ Apply promo code

3. Patient Information
   └─ Login/Register
   └─ Fill medical history form
   └─ Emergency contact
   └─ E-consent

4. Payment
   └─ Payment gateway (Midtrans, Xendit)
   └─ Multiple methods: VA, CC, E-wallet, QRIS
   └─ Instant confirmation

5. Confirmation
   └─ E-ticket with QR code
   └─ WhatsApp confirmation
   └─ Email with preparation instructions
   └─ Add to calendar

6. Reminders
   └─ H-3: Reminder + preparation tips
   └─ H-1: Confirmation + location info
   └─ H-day: Check-in QR code
```

**Backend (Klinik Dashboard):**

```php
// app/Http/Controllers/MCU/BookingController.php
- Calendar view (day/week/month)
- Booking management (approve, reschedule, cancel)
- Capacity management
- No-show tracking
- Waiting list management
```

### 3.3 MCU Workflow Digitalization

```
Pre-MCU:
├─ Patient registration (QR code scan)
├─ Medical history review
├─ Consent signing (digital)
└─ Payment verification

During MCU:
├─ Station 1: Vital signs (BP, HR, temp, BMI)
│   └─ Input via tablet/mobile
├─ Station 2: Lab tests (blood, urine)
│   └─ LIS integration (if available)
├─ Station 3: Radiologi (X-ray, USG)
│   └─ PACS integration
├─ Station 4: EKG
├─ Station 5: Vision & hearing tests
├─ Station 6: Doctor consultation
│   └─ Review all results
│   └─ Provide recommendations
└─ Real-time progress tracking

Post-MCU:
├─ Auto-generate comprehensive PDF report
├─ Doctor review & signature
├─ Auto-delivery via email/WhatsApp
├─ Patient portal access
└─ Follow-up scheduling
```

### 3.4 MCU Report Generation

```php
// app/Services/MCUReportService.php
class MCUReportService
{
    public function generateReport($bookingId)
    {
        // 1. Fetch all results
        // 2. Generate PDF with:
        //    - Cover page
        //    - Patient demographics
        //    - Vital signs table
        //    - Lab results with charts
        //    - Radiologi images
        //    - EKG interpretation
        //    - Doctor summary & recommendations
        //    - Abnormal results highlighted
        //    - QR verification
        // 3. Compress PDF (using our optimization)
        // 4. Store & send
    }
}
```

### 3.5 MCU Marketplace Features

**Landing Page:**
- Hero section with CTA
- Featured packages
- How it works (3 steps)
- Why choose us (benefits)
- Testimonials
- Blog posts (SEO content)

**Search & Filter:**
```php
// app/Http/Controllers/MarketplaceController.php
- Location (city, district)
- Price range slider
- Package type (basic, standard, premium)
- Rating (4+ stars, 3+ stars)
- Availability (this week, this month)
- Clinic facilities (parking, accessibility)
- Sort by: popularity, price (low-high), rating
```

**Package Detail Page:**
- Gallery (images/videos)
- Price & discount
- Detailed exam list
- Clinic information
- Reviews & ratings
- Similar packages
- FAQ
- Book now button

### 3.6 Marketing Automation

```sql
-- Email Campaigns
CREATE TABLE email_campaigns (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT,
    name VARCHAR(255),
    subject VARCHAR(255),
    from_name VARCHAR(255),
    from_email VARCHAR(255),
    template_html TEXT,
    target_audience JSON, -- segmentation criteria
    schedule_type ENUM('immediate', 'scheduled', 'recurring'),
    scheduled_at TIMESTAMP,
    recurrence_pattern VARCHAR(50), -- daily, weekly, monthly
    status ENUM('draft', 'scheduled', 'sending', 'sent', 'paused'),
    total_recipients INT,
    sent_count INT DEFAULT 0,
    opened_count INT DEFAULT 0,
    clicked_count INT DEFAULT 0,
    bounced_count INT DEFAULT 0,
    unsubscribed_count INT DEFAULT 0,
    created_at TIMESTAMP
);

-- Campaign Recipients
CREATE TABLE campaign_recipients (
    id BIGINT PRIMARY KEY,
    campaign_id BIGINT,
    patient_id BIGINT,
    email VARCHAR(255),
    status ENUM('pending', 'sent', 'opened', 'clicked', 'bounced', 'unsubscribed'),
    sent_at TIMESTAMP,
    opened_at TIMESTAMP,
    clicked_at TIMESTAMP,
    FOREIGN KEY (campaign_id) REFERENCES email_campaigns(id)
);
```

**Campaign Types:**
1. **Welcome Series**: New user onboarding
2. **Educational**: Health tips, disease prevention
3. **Promotional**: Discount, flash sale
4. **Abandoned Cart**: Reminder for incomplete booking
5. **Post-MCU**: Follow-up, review request
6. **Re-engagement**: Inactive users (last MCU > 1 year ago)
7. **Seasonal**: Annual check-up reminder, flu season

**WhatsApp Marketing:**
```php
// app/Services/WhatsAppMarketingService.php
- Broadcast to opt-in contacts
- Interactive chat flows
- Appointment reminders
- Feedback collection
- Promo announcements
```

---

## 🏢 Phase 4: MODUL 3 - B2B Portal (Weeks 11-14)

### 4.1 Database Schema

```sql
-- Companies (extended)
CREATE TABLE companies (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT, -- klinik partner
    company_code VARCHAR(50) UNIQUE,
    name VARCHAR(255),
    industry VARCHAR(100),
    size_category ENUM('small', 'medium', 'large', 'enterprise'),
    employee_count INT,
    address TEXT,
    city VARCHAR(100),
    province VARCHAR(100),
    phone VARCHAR(50),
    email VARCHAR(255),
    logo VARCHAR(255),

    -- Partnership
    partnership_type ENUM('basic', 'premium', 'exclusive'),
    contract_start_date DATE,
    contract_end_date DATE,
    credit_limit DECIMAL(12,2),
    credit_used DECIMAL(12,2) DEFAULT 0,
    payment_terms INT, -- 30, 60, 90 days

    -- Settings
    settings JSON,
    is_active BOOLEAN DEFAULT TRUE,

    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id)
);

-- Company Users (HR, Manager, Finance)
CREATE TABLE company_users (
    id BIGINT PRIMARY KEY,
    company_id BIGINT,
    user_id BIGINT,
    role ENUM('admin', 'hr', 'manager', 'finance'),
    department VARCHAR(100),
    permissions JSON,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Company Departments
CREATE TABLE company_departments (
    id BIGINT PRIMARY KEY,
    company_id BIGINT,
    name VARCHAR(255),
    code VARCHAR(50),
    manager_name VARCHAR(255),
    budget_allocation DECIMAL(12,2),
    budget_used DECIMAL(12,2) DEFAULT 0,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

-- Company Employees
CREATE TABLE company_employees (
    id BIGINT PRIMARY KEY,
    company_id BIGINT,
    department_id BIGINT,
    patient_id BIGINT, -- linked to patients table
    employee_number VARCHAR(50),
    full_name VARCHAR(255),
    email VARCHAR(255),
    phone VARCHAR(50),
    position VARCHAR(100),
    level ENUM('staff', 'supervisor', 'manager', 'director', 'executive'),
    join_date DATE,
    status ENUM('active', 'inactive', 'resigned'),
    health_profile JSON, -- medical history, allergies, etc

    -- Annual quotas
    mcu_quota INT DEFAULT 1, -- annual MCU entitlement
    mcu_used INT DEFAULT 0,
    sick_leave_quota INT DEFAULT 12, -- days
    sick_leave_used INT DEFAULT 0,

    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (department_id) REFERENCES company_departments(id),
    FOREIGN KEY (patient_id) REFERENCES patients(id)
);

-- Employee Health Services Requests
CREATE TABLE employee_health_requests (
    id BIGINT PRIMARY KEY,
    request_number VARCHAR(50) UNIQUE,
    company_id BIGINT,
    employee_id BIGINT,
    service_type ENUM('mc_verification', 'skb_application', 'mcu_booking'),

    -- Request details
    description TEXT,
    documents JSON, -- uploaded files

    -- Approval workflow
    status ENUM('pending', 'approved_manager', 'approved_hr', 'approved_clinic', 'completed', 'rejected'),
    requested_by_user_id BIGINT,
    approved_by_manager_id BIGINT,
    approved_by_hr_id BIGINT,
    approved_at TIMESTAMP,
    rejected_by_user_id BIGINT,
    rejection_reason TEXT,

    -- Service delivery
    result_id BIGINT, -- if MC/SKB
    booking_id BIGINT, -- if MCU
    completed_at TIMESTAMP,

    created_at TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (employee_id) REFERENCES company_employees(id)
);

-- Corporate MCU Programs
CREATE TABLE corporate_mcu_programs (
    id BIGINT PRIMARY KEY,
    company_id BIGINT,
    program_name VARCHAR(255),
    program_year INT,

    -- Schedule
    start_date DATE,
    end_date DATE,

    -- Packages per level
    staff_package_id BIGINT,
    manager_package_id BIGINT,
    executive_package_id BIGINT,

    -- Targets
    target_participants INT,
    actual_participants INT DEFAULT 0,
    completion_rate DECIMAL(5,2),

    -- Options
    is_mandatory BOOLEAN DEFAULT FALSE,
    allow_family_members BOOLEAN DEFAULT FALSE,
    onsite_option BOOLEAN DEFAULT FALSE,

    -- Incentives
    incentive_type VARCHAR(50), -- 'bonus', 'points', 'voucher'
    incentive_value DECIMAL(10,2),

    status ENUM('planning', 'active', 'completed', 'cancelled'),
    created_at TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

-- Sick Leave Records
CREATE TABLE sick_leave_records (
    id BIGINT PRIMARY KEY,
    company_id BIGINT,
    employee_id BIGINT,
    result_id BIGINT, -- linked to MC document

    start_date DATE,
    end_date DATE,
    duration_days INT,
    diagnosis VARCHAR(255),
    doctor_name VARCHAR(255),
    clinic_name VARCHAR(255),

    -- Verification
    document_verified BOOLEAN DEFAULT FALSE,
    verified_by_hr_id BIGINT,
    verified_at TIMESTAMP,
    qr_verified BOOLEAN DEFAULT FALSE,

    -- HRIS Integration
    synced_to_hris BOOLEAN DEFAULT FALSE,
    hris_reference VARCHAR(100),
    synced_at TIMESTAMP,

    -- Flags
    is_suspicious BOOLEAN DEFAULT FALSE, -- fraud detection
    suspicious_reason TEXT,

    created_at TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (employee_id) REFERENCES company_employees(id),
    FOREIGN KEY (result_id) REFERENCES results(id)
);

-- Company Invoices
CREATE TABLE company_invoices (
    id BIGINT PRIMARY KEY,
    invoice_number VARCHAR(50) UNIQUE,
    company_id BIGINT,
    tenant_id BIGINT,

    -- Period
    period_start DATE,
    period_end DATE,

    -- Amounts
    subtotal DECIMAL(12,2),
    tax_amount DECIMAL(12,2),
    discount_amount DECIMAL(12,2),
    total_amount DECIMAL(12,2),

    -- Line items
    items JSON, -- [{type, description, qty, unit_price, total}]

    -- Payment
    payment_terms INT, -- days
    due_date DATE,
    paid_amount DECIMAL(12,2) DEFAULT 0,
    payment_status ENUM('unpaid', 'partial', 'paid', 'overdue'),
    paid_at TIMESTAMP,

    -- Documents
    pdf_path VARCHAR(255),
    notes TEXT,

    created_at TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (tenant_id) REFERENCES tenants(id)
);

-- Company Reports (analytics)
CREATE TABLE company_health_reports (
    id BIGINT PRIMARY KEY,
    company_id BIGINT,
    report_type VARCHAR(50), -- 'monthly', 'quarterly', 'annual'
    period_start DATE,
    period_end DATE,

    -- Metrics
    total_employees INT,
    mcu_participation INT,
    mcu_completion_rate DECIMAL(5,2),
    sick_leave_days INT,
    sick_leave_cost DECIMAL(12,2),
    productivity_loss_hours INT,

    -- Health insights
    top_diseases JSON,
    high_risk_employees JSON,
    department_comparison JSON,
    health_risk_distribution JSON,

    -- Recommendations
    recommendations TEXT,

    pdf_path VARCHAR(255),
    created_at TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);
```

### 4.2 Company Portal Features

**Dashboard (HR/Admin View):**
```
┌─────────────────────────────────────────┐
│ Employee Health Overview                │
├─────────────────────────────────────────┤
│ • Total Employees: 1,250               │
│ • MCU Completion: 785 (62.8%)          │
│ • Sick Leave (This Month): 45 days     │
│ • Health Budget Used: Rp 85M / Rp 120M│
└─────────────────────────────────────────┘

┌──────────────┬──────────────┬──────────────┐
│ Pending      │ This Week    │ High Risk    │
│ Approvals    │ MCU Schedule │ Employees    │
│ 12           │ 28 employees │ 15           │
└──────────────┴──────────────┴──────────────┘

┌─────────────────────────────────────────┐
│ Top 5 Diseases (Last 3 Months)         │
│ 1. Upper Respiratory Infection (85)    │
│ 2. Gastritis (42)                      │
│ 3. Hypertension (38)                   │
│ 4. Diabetes Mellitus (25)              │
│ 5. Back Pain (22)                      │
└─────────────────────────────────────────┘
```

**Employee Management:**
- Import employees (Excel/CSV)
- Bulk operations
- Employee health profiles
- Dependent management
- Department assignment

**MCU Program Management:**
- Create annual MCU program
- Set packages per level
- Schedule coordination
- Participation tracking
- Reminder campaigns
- Incentive tracking

**Sick Leave Management:**
- Auto-receive MC notifications
- Digital verification (QR scan)
- Approval workflow
- HRIS integration
- Fraud detection
- Analytics

**Health Analytics:**
- Executive dashboard
- Disease prevalence
- Department comparison
- Cost analysis
- Productivity impact
- Predictive analytics

**Billing:**
- Centralized invoicing
- Departmental breakdown
- Credit limit tracking
- Payment terms
- E-invoice

### 4.3 Employee Self-Service Portal

**Employee Features:**
```
1. Health Profile
   └─ Medical history
   └─ Allergies
   └─ Current medications
   └─ Family health history

2. MCU Booking
   └─ View available packages
   └─ Check quota (company-sponsored)
   └─ Self-book or request approval
   └─ Track status

3. Documents
   └─ MC history
   └─ MCU reports
   └─ Download PDFs

4. Sick Leave
   └─ Submit MC
   └─ View balance
   └─ History

5. Wellness
   └─ Health tips
   └─ Fitness challenges
   └─ Reward points
```

### 4.4 Integration APIs

```php
// HRIS Integration
POST /api/v1/hris/sync-employees
POST /api/v1/hris/sick-leave-sync
GET  /api/v1/hris/employee/{employee_number}

// Payroll Integration
POST /api/v1/payroll/sick-leave-deduction
GET  /api/v1/payroll/health-cost-summary

// LIS (Laboratory Information System)
POST /api/v1/lis/results-import
GET  /api/v1/lis/pending-results

// PACS (Picture Archiving System)
POST /api/v1/pacs/images-import
GET  /api/v1/pacs/study/{study_id}
```

---

## 📊 Phase 5: Platform Features (Weeks 15-16)

### 5.1 Admin Dashboard (Platform Owner)

```
Super Admin Features:
├─ Tenant Management
│   ├─ Create/edit/delete tenants
│   ├─ Subscription management
│   ├─ Usage monitoring
│   └─ Billing & invoicing
│
├─ Platform Analytics
│   ├─ Total revenue
│   ├─ MRR/ARR
│   ├─ Churn rate
│   ├─ Active tenants
│   └─ Usage statistics
│
├─ System Configuration
│   ├─ Payment gateways
│   ├─ Email/SMS providers
│   ├─ Storage management
│   └─ Feature flags
│
└─ Content Management
    ├─ Blog posts
    ├─ Template marketplace
    ├─ Help center
    └─ Announcements
```

### 5.2 API Layer

```php
// app/Http/Controllers/API/V1/

// Public API
- GET  /api/v1/tenants/{tenant}/packages
- POST /api/v1/bookings
- GET  /api/v1/bookings/{id}
- POST /api/v1/bookings/{id}/payment

// Tenant API (requires API key)
- POST /api/v1/results (generate MC/SKB)
- GET  /api/v1/results/{id}
- POST /api/v1/mcu-bookings
- GET  /api/v1/analytics/summary

// Webhooks
- POST /webhooks/payment/{provider}
- POST /webhooks/hris/sync
```

### 5.3 Mobile App (Optional - Phase 6)

**React Native**
- Patient app (B2C)
- Clinic app (for doctors/staff)
- Company app (for HR)

---

## 🔐 Security & Compliance

### Authentication & Authorization
- Multi-factor authentication (2FA)
- Role-based access control (RBAC)
- API rate limiting
- IP whitelisting for API
- Session management

### Data Protection
- Encryption at rest (database)
- Encryption in transit (SSL/TLS)
- HIPAA compliance considerations
- GDPR compliance (if international)
- Regular backups
- Audit trail for sensitive operations

### Testing
- Unit tests (PHPUnit/Pest)
- Feature tests
- Browser tests (Laravel Dusk)
- Load testing (Apache JMeter)
- Security testing (OWASP Top 10)

---

## 📈 Implementation Roadmap

### **Month 1-2: Foundation**
- ✅ Week 1: PDF optimization (DONE)
- Week 2-3: Multi-tenancy setup
  - Install & configure stancl/tenancy
  - Database migration (add tenant_id)
  - Tenant model & seeder
  - Subdomain routing
- Week 4: Subscription system
  - Plans & pricing
  - Billing integration (Midtrans)
  - Usage tracking
- Week 5-6: Modul 1 enhancement
  - Multi-template system
  - Enhanced notifications
  - Analytics dashboard

### **Month 3-4: MCU System**
- Week 7-8: MCU Core
  - Database schema
  - Package management CRUD
  - Booking system
- Week 9-10: MCU Marketplace
  - Landing page
  - Search & filter
  - Payment integration
  - Booking flow
- Week 11-12: MCU Workflow
  - Digital checklist
  - Result input system
  - Report generation
  - Auto-delivery

### **Month 5: B2B Portal**
- Week 13-14: Company Portal
  - Company & employee management
  - MCU program setup
  - Sick leave tracking
- Week 15: Integration & Analytics
  - HRIS API
  - Health analytics
  - Billing system
- Week 16: Testing & Bug Fixes

### **Month 6: Launch & Marketing**
- Week 17: Beta testing (selected tenants)
- Week 18: Marketing website
- Week 19: Documentation & training
- Week 20: Public launch

---

## 💰 Cost Estimation

### Development Costs
| Phase | Duration | Developer | Cost (IDR) |
|-------|----------|-----------|------------|
| Phase 1: Foundation | 3 weeks | 2 devs | Rp 90M |
| Phase 2: Modul 1 | 2 weeks | 1 dev | Rp 20M |
| Phase 3: MCU System | 5 weeks | 2 devs | Rp 150M |
| Phase 4: B2B Portal | 4 weeks | 2 devs | Rp 120M |
| Phase 5: Platform | 2 weeks | 1 dev | Rp 20M |
| Testing & QA | 2 weeks | 1 QA | Rp 20M |
| **TOTAL** | **18 weeks** | | **Rp 420M** |

### Infrastructure Costs (Monthly)
- Cloud hosting (AWS/GCP): Rp 10M
- Database (managed): Rp 5M
- CDN: Rp 2M
- Email service: Rp 1M
- SMS/WhatsApp: Rp 3M
- Monitoring: Rp 1M
- **Total/month**: Rp 22M

### Marketing Budget
- Landing page development: Rp 30M
- SEO & content: Rp 15M/month
- Google Ads: Rp 20M/month
- Social media: Rp 10M/month

---

## 📊 Revenue Projections

### Year 1
| Metric | Month 1 | Month 6 | Month 12 |
|--------|---------|---------|----------|
| Tenants | 5 | 25 | 100 |
| Avg subscription | Rp 1.5M | Rp 1.8M | Rp 2M |
| MRR | Rp 7.5M | Rp 45M | Rp 200M |
| MCU commission (10%) | Rp 5M | Rp 50M | Rp 150M |
| **Total MRR** | **Rp 12.5M** | **Rp 95M** | **Rp 350M** |
| **ARR (Year 1)** | | | **Rp 4.2B** |

### Break-even Analysis
- Fixed costs/month: Rp 22M (infra) + Rp 45M (marketing) = Rp 67M
- Required MRR for break-even: Rp 67M
- **Expected break-even: Month 7**

---

## 🎯 Success Metrics

### Platform Metrics
- Number of active tenants
- Monthly Recurring Revenue (MRR)
- Customer Acquisition Cost (CAC)
- Lifetime Value (LTV)
- Churn rate
- Net Promoter Score (NPS)

### User Metrics
- Documents generated/month
- MCU bookings/month
- B2B companies onboarded
- Employee health requests/month

### Technical Metrics
- System uptime (target: 99.9%)
- Average response time (target: < 200ms)
- PDF generation time (target: < 5s)
- Error rate (target: < 0.1%)

---

## 🚧 Risks & Mitigation

| Risk | Impact | Mitigation |
|------|--------|------------|
| Data breach | Critical | Strong encryption, regular audits, compliance |
| System downtime | High | Redundancy, monitoring, quick rollback |
| Low adoption | High | Free trial, excellent UX, marketing |
| Competition | Medium | Differentiation, continuous innovation |
| Regulatory changes | Medium | Legal advisory, compliance tracking |
| Payment gateway issues | Medium | Multiple providers, backup options |

---

## 📚 Technical Stack Summary

### Backend
- **Framework**: Laravel 12
- **PHP**: 8.2+
- **Database**: MySQL 8.0 (with tenant isolation)
- **Cache**: Redis
- **Queue**: Redis + Supervisor
- **PDF**: DomPDF (optimized)

### Frontend
- **Framework**: Laravel Blade + Alpine.js
- **CSS**: Tailwind CSS
- **Charts**: Chart.js / ApexCharts
- **Tables**: DataTables

### DevOps
- **Hosting**: AWS / Google Cloud
- **CI/CD**: GitHub Actions
- **Monitoring**: Laravel Telescope + New Relic
- **Logging**: Papertrail / CloudWatch

### Third-party Services
- **Payment**: Midtrans, Xendit
- **Email**: AWS SES, SendGrid
- **SMS**: Twilio, Vonage
- **WhatsApp**: WhatsApp Business API
- **Storage**: AWS S3
- **CDN**: CloudFlare

---

## 📞 Next Steps

1. **Review & Approval**: Stakeholder review of this plan
2. **Budget Allocation**: Secure funding (Rp 420M + operational)
3. **Team Formation**: Hire developers, QA, DevOps
4. **Sprint Planning**: Detailed breakdown of each phase
5. **Kickoff Meeting**: Align team on goals & timeline
6. **Start Development**: Begin Phase 1 implementation

---

## 📝 Notes

- This is a comprehensive transformation requiring **4-6 months**
- Budget estimate is conservative, may vary based on team size
- Revenue projections are based on market research and assumptions
- Continuous iteration and user feedback are critical
- Consider MVP approach: launch with core features first

---

**Document Version**: 1.0
**Last Updated**: 2025-11-13
**Status**: ✅ Ready for Review

---

*For questions or clarifications, please contact the development team.*
