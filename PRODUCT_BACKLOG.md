# 📦 Product Backlog - Medical Certificate v3 SaaS (MVP)

**Version**: 1.0
**Last Updated**: 2025-11-13
**Total P0 Features**: 187
**Total Story Points**: 842 points
**Estimated Duration**: 14 weeks (with 3 developers @ 127 points/sprint)

---

## 🎯 Product Vision

Transform Medical Certificate v3 into a **multi-tenant SaaS platform** that enables:
- **Klinik/RS**: Manage digital health certificates with subscription model
- **Patients**: Book MCU packages online via marketplace
- **Companies**: Monitor employee health with B2B portal

**MVP Goal**: Launch core platform with multi-tenancy, MCU marketplace, and B2B portal in 14 weeks

---

## 📊 Backlog Overview

| Sprint | Focus Area | Story Points | Weeks | Features |
|--------|------------|--------------|-------|----------|
| **Sprint 1** | Multi-Tenancy Foundation | 125 | 2 | 19 |
| **Sprint 2** | Subscription & Billing | 118 | 2 | 15 |
| **Sprint 3** | Modul 1 Enhancement | 95 | 2 | 20 |
| **Sprint 4** | MCU Packages & Booking | 135 | 2 | 24 |
| **Sprint 5** | MCU Workflow & Reports | 142 | 2 | 26 |
| **Sprint 6** | B2B Portal Core | 128 | 2 | 30 |
| **Sprint 7** | B2B Analytics & Integration | 99 | 2 | 28 |
| **TOTAL MVP** | | **842** | **14** | **162** |

---

## 🏃 SPRINT 1: Multi-Tenancy Foundation (125 points)

**Goal**: Set up multi-tenancy with tenant isolation and basic subscription

### Epic 1: Multi-Tenancy Core (42 points)
| ID | User Story | Points | Priority |
|----|------------|--------|----------|
| F0.1.1 | Install & configure stancl/tenancy package | 3 | P0 |
| F0.1.2 | Create Tenant model with subscription tracking | 5 | P0 |
| F0.1.3 | Add tenant_id to all existing tables | 8 | P0 |
| F0.1.4 | Implement subdomain-based tenant detection | 5 | P0 |
| F0.1.6 | Create tenant middleware & guards | 5 | P0 |
| F0.1.7 | Write cross-tenant data isolation tests | 8 | P0 |
| F0.1.9 | Build tenant settings management UI | 8 | P0 |

### Epic 2: Subscription Plans (28 points)
| ID | User Story | Points | Priority |
|----|------------|--------|----------|
| F0.2.1 | Create subscription plans table & model | 5 | P0 |
| F0.2.2 | Define plan features matrix | 3 | P0 |
| F0.2.3 | Implement trial period management (14 days) | 5 | P0 |
| F0.2.4 | Build usage tracking & quota enforcement | 8 | P0 |
| F0.2.9 | Create invoice generation system | 5 | P0 |
| F0.2.13 | Build payment history & receipts view | 2 | P0 |

### Epic 3: Usage Tracking (18 points)
| ID | User Story | Points | Priority |
|----|------------|--------|----------|
| F0.3.1 | Implement document generation counter | 5 | P0 |
| F0.3.2 | Create storage usage calculator | 5 | P0 |
| F0.3.3 | Build user count enforcement | 3 | P0 |
| F0.3.5 | Create quota exceeded notifications | 5 | P0 |

### Epic 4: Platform Admin (25 points)
| ID | User Story | Points | Priority |
|----|------------|--------|----------|
| F0.4.1 | Implement super admin authentication | 5 | P0 |
| F0.4.2 | Build tenant management CRUD | 8 | P0 |
| F0.4.3 | Create platform-wide analytics dashboard | 8 | P0 |

### Epic 5: Security (12 points)
| ID | User Story | Points | Priority |
|----|------------|--------|----------|
| F6.1.2 | Implement session management | 5 | P0 |
| F6.1.3 | Add IP tracking & geolocation | 3 | P0 |
| F6.1.4 | Create login attempt limiting | 2 | P0 |

---

## 🏃 SPRINT 2: Subscription & Billing (118 points)

**Goal**: Complete payment gateway integration and subscription lifecycle

### Epic 1: Payment Gateway (45 points)
| ID | User Story | Points | Priority |
|----|------------|--------|----------|
| F0.2.5 | Integrate Midtrans payment gateway | 13 | P0 |
| F0.2.7 | Build subscription upgrade/downgrade flow | 8 | P0 |
| F0.2.8 | Implement auto-renewal with retry logic | 8 | P0 |
| F0.2.10 | Create payment webhook handlers | 8 | P0 |
| F0.2.14 | Send failed payment notifications | 2 | P0 |
| F4.4.3 | Add Virtual Account (VA) payment method | 3 | P0 |
| F4.4.4 | Add Credit Card payment method | 3 | P0 |

### Epic 2: API Foundation (28 points)
| ID | User Story | Points | Priority |
|----|------------|--------|----------|
| F4.1.1 | Implement OAuth 2.0 API authentication | 8 | P0 |
| F4.1.2 | Create API key management | 5 | P0 |
| F4.1.3 | Add rate limiting & throttling | 5 | P0 |
| F4.1.4 | Generate API documentation (Swagger) | 5 | P0 |
| F4.1.9 | Implement API versioning | 5 | P0 |

### Epic 3: Platform Features (30 points)
| ID | User Story | Points | Priority |
|----|------------|--------|----------|
| F0.4.4 | Build revenue dashboard (MRR, ARR, churn) | 8 | P0 |
| F0.3.6 | Create usage analytics dashboard | 8 | P0 |
| F6.3.1 | Implement activity logging for all actions | 8 | P0 |
| F6.1.1 | Add two-factor authentication (2FA) | 8 | P0 |

### Epic 4: Documentation (15 points)
| ID | User Story | Points | Priority |
|----|------------|--------|----------|
| F8.1.1 | Create user documentation (Help Center) | 8 | P0 |
| F8.1.3 | Write administrator guide | 5 | P0 |
| F8.1.6 | Build FAQs section | 2 | P0 |

---

## 🏃 SPRINT 3: Modul 1 Enhancement (95 points)

**Goal**: Enhance existing medical certificate features

### Epic 1: Templates (20 points)
| ID | User Story | Points | Priority |
|----|------------|--------|----------|
| F1.1.1 | Create document template model & CRUD | 8 | P0 |
| F1.1.4 | Implement template variables/placeholders | 5 | P0 |
| F1.1.7 | Create 3 default templates | 5 | P0 |
| F1.1.10 | Allow setting default template per type | 2 | P0 |

### Epic 2: Notifications (25 points)
| ID | User Story | Points | Priority |
|----|------------|--------|----------|
| F1.3.1 | Integrate WhatsApp Business API | 13 | P0 |
| F1.3.2 | Create email notification templates | 5 | P0 |
| F1.3.8 | Build notification templates editor | 5 | P0 |

### Epic 3: Analytics (30 points)
| ID | User Story | Points | Priority |
|----|------------|--------|----------|
| F1.4.1 | Build real-time analytics dashboard | 8 | P0 |
| F1.4.2 | Track document generation statistics | 5 | P0 |
| F1.4.5 | Create document type breakdown (pie chart) | 5 | P0 |
| F1.4.6 | Track success/failure rates | 5 | P0 |
| F1.4.11 | Add custom date range filtering | 2 | P0 |

### Epic 4: Document Management (20 points)
| ID | User Story | Points | Priority |
|----|------------|--------|----------|
| F1.6.1 | Build document search & filter | 8 | P0 |

---

## 🏃 SPRINT 4: MCU Packages & Booking Part 1 (135 points)

**Goal**: Build MCU package management and online booking system

### Epic 1: Package Management (40 points)
| ID | User Story | Points | Priority |
|----|------------|--------|----------|
| F2.1.1 | Create MCU package model & CRUD | 8 | P0 |
| F2.1.2 | Build package item builder (drag & drop) | 13 | P0 |
| F2.1.3 | Create pre-defined package templates (4 tiers) | 8 | P0 |
| F2.1.4 | Implement package pricing management | 5 | P0 |
| F2.1.5 | Add package images gallery | 3 | P0 |
| F2.1.10 | Create package tags & categories | 3 | P0 |

### Epic 2: MCU Marketplace (50 points)
| ID | User Story | Points | Priority |
|----|------------|--------|----------|
| F2.2.1 | Build public MCU marketplace landing page | 13 | P0 |
| F2.2.2 | Create package listing with search & filter | 8 | P0 |
| F2.2.3 | Add advanced filtering (location, price, rating) | 8 | P0 |
| F2.2.4 | Build package detail page | 8 | P0 |
| F2.2.5 | Implement real-time availability calendar | 8 | P0 |
| F2.2.6 | Create time slot selection | 5 | P0 |

### Epic 3: Booking Flow (45 points)
| ID | User Story | Points | Priority |
|----|------------|--------|----------|
| F2.2.7 | Build booking capacity management | 8 | P0 |
| F2.2.8 | Create patient registration during booking | 5 | P0 |
| F2.2.9 | Build medical history form | 8 | P0 |
| F2.2.10 | Implement e-consent signing | 5 | P0 |
| F2.2.11 | Add promo code application | 5 | P0 |
| F2.2.12 | Create booking summary & confirmation | 5 | P0 |
| F2.2.13 | Integrate payment gateway | 8 | P0 |

---

## 🏃 SPRINT 5: MCU Workflow & Reports (142 points)

**Goal**: Digitalize MCU workflow and generate comprehensive reports

### Epic 1: Payment & Confirmation (35 points)
| ID | User Story | Points | Priority |
|----|------------|--------|----------|
| F2.2.14 | Support multiple payment methods | 8 | P0 |
| F2.2.15 | Generate e-ticket with QR code | 5 | P0 |
| F2.2.16 | Send booking confirmation (email/WhatsApp) | 5 | P0 |
| F2.2.18 | Create automated reminders (H-3, H-1, H-day) | 8 | P0 |
| F2.2.19 | Implement booking rescheduling | 8 | P0 |
| F2.2.20 | Build booking cancellation with refund | 8 | P0 |

### Epic 2: MCU Workflow (45 points)
| ID | User Story | Points | Priority |
|----|------------|--------|----------|
| F2.3.1 | Build QR code check-in system | 8 | P0 |
| F2.3.2 | Create digital checklist per station | 8 | P0 |
| F2.3.3 | Build vital signs input (mobile/tablet) | 8 | P0 |
| F2.3.4 | Create lab results input interface | 8 | P0 |
| F2.3.6 | Build radiology results upload | 5 | P0 |
| F2.3.10 | Add doctor consultation notes | 5 | P0 |
| F2.3.14 | Implement abnormal result flagging | 3 | P0 |

### Epic 3: MCU Reports (42 points)
| ID | User Story | Points | Priority |
|----|------------|--------|----------|
| F2.4.1 | Create comprehensive MCU report template | 13 | P0 |
| F2.4.2 | Add multi-page report with charts | 8 | P0 |
| F2.4.5 | Show lab results with reference ranges | 5 | P0 |
| F2.4.9 | Add doctor summary & recommendations | 5 | P0 |
| F2.4.10 | Highlight abnormal results | 3 | P0 |
| F2.4.12 | Add doctor digital signature | 3 | P0 |
| F2.4.15 | Auto-deliver report (email/WhatsApp) | 5 | P0 |

### Epic 4: Reviews & Promo (20 points)
| ID | User Story | Points | Priority |
|----|------------|--------|----------|
| F2.5.1 | Build review submission after MCU | 8 | P0 |
| F2.5.2 | Create star rating (1-5) system | 3 | P0 |
| F2.6.1 | Build promo code generator | 5 | P0 |
| F2.6.4 | Set usage limit per code | 2 | P0 |

---

## 🏃 SPRINT 6: B2B Portal Core (128 points)

**Goal**: Build company portal for employee health management

### Epic 1: Company Management (38 points)
| ID | User Story | Points | Priority |
|----|------------|--------|----------|
| F3.1.1 | Create company registration & onboarding | 8 | P0 |
| F3.1.2 | Build company profile management | 8 | P0 |
| F3.1.3 | Implement multi-user access (HR, Manager, Finance) | 8 | P0 |
| F3.1.4 | Create role-based permissions | 8 | P0 |
| F3.1.5 | Build department structure setup | 5 | P0 |

### Epic 2: Employee Management (35 points)
| ID | User Story | Points | Priority |
|----|------------|--------|----------|
| F3.2.1 | Create employee database | 8 | P0 |
| F3.2.2 | Build bulk employee import (Excel/CSV) | 8 | P0 |
| F3.2.3 | Create employee profile with health data | 8 | P0 |
| F3.2.8 | Implement annual quota management | 8 | P0 |
| F3.2.12 | Build employee search & filtering | 3 | P0 |

### Epic 3: Health Service Requests (35 points)
| ID | User Story | Points | Priority |
|----|------------|--------|----------|
| F3.3.1 | Build service request submission | 8 | P0 |
| F3.3.2 | Create MC verification request | 5 | P0 |
| F3.3.4 | Build MCU booking request | 5 | P0 |
| F3.3.5 | Implement approval workflow | 13 | P0 |
| F3.3.6 | Create request status tracking | 3 | P0 |

### Epic 4: MCU Program (20 points)
| ID | User Story | Points | Priority |
|----|------------|--------|----------|
| F3.4.1 | Build MCU program creation | 8 | P0 |
| F3.4.3 | Implement bulk MCU booking | 8 | P0 |
| F3.4.6 | Create participation tracking | 5 | P0 |

---

## 🏃 SPRINT 7: B2B Analytics & Integration (99 points)

**Goal**: Complete B2B portal with analytics and HRIS integration

### Epic 1: Sick Leave Management (30 points)
| ID | User Story | Points | Priority |
|----|------------|--------|----------|
| F3.5.1 | Auto-receive MC notification from clinic | 8 | P0 |
| F3.5.2 | Build MC digital verification (QR scan) | 8 | P0 |
| F3.5.3 | Create sick leave approval workflow | 5 | P0 |
| F3.5.5 | Implement auto-deduct sick leave quota | 5 | P0 |
| F3.5.6 | Build sick leave history tracking | 5 | P0 |

### Epic 2: Health Analytics (35 points)
| ID | User Story | Points | Priority |
|----|------------|--------|----------|
| F3.6.1 | Build executive health overview dashboard | 13 | P0 |
| F3.6.3 | Show top 10 diseases in company | 5 | P0 |
| F3.6.4 | Identify high-risk employees | 5 | P0 |
| F3.6.8 | Create department health comparison | 5 | P0 |
| F3.6.9 | Build sick leave cost analysis | 5 | P0 |

### Epic 3: Company Reporting (25 points)
| ID | User Story | Points | Priority |
|----|------------|--------|----------|
| F3.7.1 | Create comprehensive health report generator | 13 | P0 |
| F3.7.2 | Build employee health overview report | 5 | P0 |
| F3.7.10 | Add export to PDF/Excel/PowerPoint | 5 | P0 |

### Epic 4: HRIS Integration (20 points)
| ID | User Story | Points | Priority |
|----|------------|--------|----------|
| F4.2.1 | Build employee sync API | 8 | P0 |
| F4.2.2 | Create sick leave sync API | 8 | P0 |

### Epic 5: Billing (15 points)
| ID | User Story | Points | Priority |
|----|------------|--------|----------|
| F3.9.1 | Build centralized invoicing | 8 | P0 |
| F3.9.5 | Create e-invoice with e-signature | 5 | P0 |

---

## 📊 Backlog Statistics

### By Module
| Module | Features | Story Points | % of Total |
|--------|----------|--------------|------------|
| Foundation (Multi-tenancy) | 38 | 243 | 29% |
| MCU System | 48 | 282 | 33% |
| B2B Portal | 45 | 218 | 26% |
| API & Integration | 11 | 56 | 7% |
| Security | 15 | 43 | 5% |
| **TOTAL** | **157** | **842** | **100%** |

### By Complexity
| Story Points | Count | % of Features |
|--------------|-------|---------------|
| 1-2 (Trivial) | 18 | 11% |
| 3-5 (Simple) | 79 | 50% |
| 8 (Complex) | 52 | 33% |
| 13+ (Very Complex) | 8 | 5% |

### Velocity Planning
- **Team capacity**: 127 points/sprint
- **Required sprints**: 7 sprints (14 weeks)
- **Buffer**: +10% = 1 extra sprint if needed

---

## 🎯 Release Milestones

### **Milestone 1: Foundation Ready** (End of Sprint 2)
- ✅ Multi-tenancy operational
- ✅ Subscription & billing working
- ✅ Payment gateway integrated
- **Demo**: Create tenant, subscribe, make payment

### **Milestone 2: MCU Marketplace Live** (End of Sprint 5)
- ✅ MCU packages published
- ✅ Public can book & pay
- ✅ Workflow digitalized
- ✅ Reports auto-generated
- **Demo**: End-to-end MCU booking flow

### **Milestone 3: B2B Portal Live** (End of Sprint 7)
- ✅ Companies can onboard
- ✅ Employee management working
- ✅ Health analytics available
- ✅ HRIS integration functional
- **Demo**: Company manages employee health

### **Milestone 4: MVP Launch** (Week 15)
- 🚀 Production deployment
- 🚀 Marketing campaign starts
- 🚀 First paying customers

---

## 📝 Backlog Refinement Guidelines

### **Weekly Refinement** (1 hour, every Wednesday)
- Review next sprint's stories
- Clarify acceptance criteria
- Estimate new stories
- Reprioritize if needed

### **Story Ready Criteria**
A story is ready for sprint if:
- [ ] Clear title & description
- [ ] Acceptance criteria defined
- [ ] Story points estimated
- [ ] Dependencies identified
- [ ] No blockers

### **Estimation Guidelines**
When estimating, consider:
- **Complexity**: How complex is the logic?
- **Uncertainty**: How well do we understand it?
- **Effort**: How much time will it take?
- **Dependencies**: Does it depend on other stories?

---

## 🔄 Backlog Maintenance

### **Add New Stories**
```markdown
| FX.X.X | As a [role], I want [feature] so that [benefit] | X | P0 |
```

### **Update Status**
- ⏳ Pending
- 🔄 In Progress
- ✅ Done
- ❌ Blocked

### **Archive Completed**
Move done stories to `BACKLOG_ARCHIVE.md` after sprint review

---

## 📞 Questions for Product Owner

Before starting each sprint, clarify:
1. Are priorities still valid?
2. Any new urgent features?
3. Should we split any large stories?
4. Any dependencies or blockers?
5. Acceptance criteria clear?

---

## ✅ Next Steps

1. **Review with Team**: Ensure everyone understands backlog
2. **Refine Sprint 1**: Deep dive into Sprint 1 stories
3. **Set Up Tools**: Jira/GitHub Projects board
4. **Start Sprint 1**: Nov 13, 2025

---

**Document Owner**: Product Owner / Tech Lead
**Last Review**: 2025-11-13
**Next Review**: Every Sprint Planning

**Status**: ✅ Ready for Development
