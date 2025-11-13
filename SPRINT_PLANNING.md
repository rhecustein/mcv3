# 🏃 Sprint Planning - Medical Certificate v3 SaaS Platform

**Version**: 1.0
**Last Updated**: 2025-11-13
**Sprint Duration**: 2 weeks
**Team Size**: 3 developers + 1 QA

---

## 📊 Story Point Scale

| Points | Time | Complexity | Description |
|--------|------|------------|-------------|
| **1** | 1-2 hours | Trivial | Simple config, copy-paste |
| **2** | 3-5 hours | Simple | Basic CRUD, simple logic |
| **3** | 1 day | Medium | Standard feature with tests |
| **5** | 2-3 days | Complex | Multiple components, integration |
| **8** | 3-5 days | Very Complex | Major feature, multiple dependencies |
| **13** | 1-2 weeks | Extremely Complex | Epic-level feature |

**Team Velocity Target**: 40-50 points per sprint (per developer)
**Total Team Capacity**: 120-150 points per sprint (3 developers)

---

## 👥 Team Structure

### **Team Composition**

**Developer 1 (Backend Lead)**
- Focus: Multi-tenancy, Database, API
- Skills: Laravel, MySQL, Architecture
- Velocity: 45 points/sprint

**Developer 2 (Full Stack)**
- Focus: CRUD, Business Logic, UI
- Skills: Laravel, Blade, Alpine.js
- Velocity: 42 points/sprint

**Developer 3 (Frontend/Integration)**
- Focus: UI/UX, Integrations, PDF
- Skills: Tailwind, JavaScript, Third-party APIs
- Velocity: 40 points/sprint

**QA Engineer**
- Focus: Testing, Bug tracking
- Skills: Manual testing, PHPUnit, Pest
- Works alongside developers

**Total Team Velocity**: 127 points/sprint

---

## 📅 Release Plan (MVP - 12 Weeks)

```
Sprint 1  (Week 1-2):  Foundation - Multi-tenancy Core
Sprint 2  (Week 3-4):  Subscription & Billing System
Sprint 3  (Week 5-6):  Modul 1 Enhancement
Sprint 4  (Week 7-8):  MCU Package & Booking (Part 1)
Sprint 5  (Week 9-10): MCU Workflow & Reports
Sprint 6  (Week 11-12): B2B Portal Core
```

---

## 🎯 SPRINT 1: Foundation - Multi-Tenancy Core

**Sprint Goal**: Set up multi-tenancy foundation with tenant isolation and basic CRUD

**Duration**: 2 weeks (Nov 13 - Nov 26, 2025)
**Total Story Points**: 125 points
**Team Capacity**: 127 points ✅

---

### 📋 Sprint 1 Backlog

#### **EPIC 1: Multi-Tenancy Setup (42 points)**

| ID | User Story | Story Points | Assignee | Priority |
|----|------------|--------------|----------|----------|
| **F0.1.1** | As a developer, I want to install stancl/tenancy package | 3 | Dev 1 | P0 |
| **F0.1.2** | As a developer, I want Tenant model with subscription tracking | 5 | Dev 1 | P0 |
| **F0.1.3** | As a developer, I want to add tenant_id to all existing tables | 8 | Dev 1 | P0 |
| **F0.1.4** | As a developer, I want subdomain-based tenant detection | 5 | Dev 1 | P0 |
| **F0.1.6** | As a developer, I want tenant middleware & guards for isolation | 5 | Dev 1 | P0 |
| **F0.1.7** | As a QA, I want cross-tenant data isolation tests | 8 | QA + Dev 1 | P0 |
| **F0.1.9** | As a tenant admin, I want to manage my tenant settings | 8 | Dev 2 | P0 |

**Subtotal**: 42 points

---

#### **EPIC 2: Subscription Plans Foundation (28 points)**

| ID | User Story | Story Points | Assignee | Priority |
|----|------------|--------------|----------|----------|
| **F0.2.1** | As a developer, I want subscription plans table & model | 5 | Dev 2 | P0 |
| **F0.2.2** | As a platform admin, I want to define plan features (Starter/Pro/Enterprise) | 3 | Dev 2 | P0 |
| **F0.2.3** | As a tenant, I want 14-day trial period management | 5 | Dev 2 | P0 |
| **F0.2.4** | As a developer, I want usage tracking & quota enforcement system | 8 | Dev 1 | P0 |
| **F0.2.9** | As a tenant, I want invoice generation for my subscription | 5 | Dev 2 | P0 |
| **F0.2.13** | As a tenant, I want to view payment history & receipts | 2 | Dev 2 | P0 |

**Subtotal**: 28 points

---

#### **EPIC 3: Usage Tracking (18 points)**

| ID | User Story | Story Points | Assignee | Priority |
|----|------------|--------------|----------|----------|
| **F0.3.1** | As a system, I want to track document generation count per tenant | 5 | Dev 1 | P0 |
| **F0.3.2** | As a system, I want to calculate storage usage per tenant | 5 | Dev 1 | P0 |
| **F0.3.3** | As a system, I want to enforce user count limits per plan | 3 | Dev 1 | P0 |
| **F0.3.5** | As a tenant, I want notifications when quota is exceeded | 5 | Dev 3 | P0 |

**Subtotal**: 18 points

---

#### **EPIC 4: Platform Admin Dashboard (25 points)**

| ID | User Story | Story Points | Assignee | Priority |
|----|------------|--------------|----------|----------|
| **F0.4.1** | As a platform owner, I want super admin authentication | 5 | Dev 2 | P0 |
| **F0.4.2** | As a platform admin, I want to manage tenants (CRUD) | 8 | Dev 2 | P0 |
| **F0.4.3** | As a platform admin, I want platform-wide analytics dashboard | 8 | Dev 3 | P0 |
| **F0.4.4** | As a platform admin, I want revenue dashboard (MRR, ARR, churn) | 8 | Dev 3 | P0 |

**Subtotal**: 29 points (adjusted to 25 by deprioritizing F0.4.4 to Sprint 2)

---

#### **EPIC 5: Security Foundation (12 points)**

| ID | User Story | Story Points | Assignee | Priority |
|----|------------|--------------|----------|----------|
| **F6.1.2** | As a developer, I want robust session management | 5 | Dev 1 | P0 |
| **F6.1.3** | As a system, I want IP tracking & geolocation for security | 3 | Dev 3 | P0 |
| **F6.1.4** | As a system, I want login attempt limiting to prevent brute force | 2 | Dev 3 | P0 |
| **F6.3.1** | As a platform admin, I want activity logging for all actions | 5 | Dev 1 | P0 |

**Subtotal**: 15 points (adjusted to 12 by deprioritizing F6.3.1 to Sprint 2)

---

### 📊 Sprint 1 Summary

| Epic | Story Points | % of Sprint |
|------|--------------|-------------|
| Multi-Tenancy Setup | 42 | 34% |
| Subscription Plans | 28 | 22% |
| Usage Tracking | 18 | 14% |
| Platform Admin | 25 | 20% |
| Security Foundation | 12 | 10% |
| **TOTAL** | **125** | **100%** |

---

## 🗓️ Sprint 1 Daily Breakdown

### **Week 1: Days 1-5**

#### **Day 1 (Nov 13) - Setup & Planning**
- Morning: Sprint planning meeting (2h)
- Dev 1: Install stancl/tenancy (F0.1.1) ✅
- Dev 2: Create subscription plans table (F0.2.1) 🔄
- Dev 3: Setup development environment
- QA: Prepare test plan

**End of Day**: 8 points completed

#### **Day 2 (Nov 14) - Core Setup**
- Dev 1: Tenant model with subscription tracking (F0.1.2) 🔄
- Dev 2: Define plan features matrix (F0.2.2) ✅
- Dev 3: IP tracking implementation (F6.1.3) 🔄
- QA: Review code, prepare test cases

**End of Day**: 16 points completed

#### **Day 3 (Nov 15) - Database Migration**
- Dev 1: Add tenant_id to all tables (F0.1.3) 🔄
- Dev 2: Trial period management (F0.2.3) 🔄
- Dev 3: Login attempt limiting (F6.1.4) ✅
- QA: Testing

**End of Day**: 26 points completed

#### **Day 4 (Nov 16) - Tenant Detection**
- Dev 1: Subdomain-based detection (F0.1.4) 🔄
- Dev 2: Invoice generation (F0.2.9) 🔄
- Dev 3: Quota exceeded notifications (F0.3.5) 🔄
- QA: Integration testing

**End of Day**: 41 points completed

#### **Day 5 (Nov 17) - Middleware & Guards**
- Dev 1: Tenant middleware & guards (F0.1.6) 🔄
- Dev 2: Payment history view (F0.2.13) ✅
- Dev 3: Continue notifications
- QA: Tenant isolation tests prep
- **End of Week 1**: 55 points completed

---

### **Week 2: Days 6-10**

#### **Day 6 (Nov 20) - Isolation Testing**
- Dev 1: Cross-tenant isolation tests (F0.1.7) 🔄
- Dev 2: Tenant settings management (F0.1.9) 🔄
- Dev 3: Platform-wide analytics (F0.4.3) 🔄
- QA: Testing isolation

**End of Day**: 68 points completed

#### **Day 7 (Nov 21) - Usage Tracking**
- Dev 1: Document generation counter (F0.3.1) 🔄
- Dev 2: Continue tenant settings
- Dev 3: Continue analytics dashboard
- QA: Functional testing

**End of Day**: 82 points completed

#### **Day 8 (Nov 22) - Admin Dashboard**
- Dev 1: Storage usage calculator (F0.3.2) 🔄
- Dev 2: Super admin auth (F0.4.1) 🔄
- Dev 3: Analytics dashboard finalization
- QA: Admin features testing

**End of Day**: 98 points completed

#### **Day 9 (Nov 23) - CRUD & Limits**
- Dev 1: User count enforcement (F0.3.3) ✅
- Dev 2: Tenant management CRUD (F0.4.2) 🔄
- Dev 3: Session management (F6.1.2) 🔄
- QA: Full regression testing

**End of Day**: 115 points completed

#### **Day 10 (Nov 24) - Polish & Review**
- Dev 1: Usage tracking finalization (F0.2.4) ✅
- Dev 2: Tenant CRUD finalization
- Dev 3: Bug fixes & polish
- QA: Final testing & bug reporting
- **End of Sprint 1**: 125 points completed ✅

**Sprint Review**: Nov 25 (Monday)
**Sprint Retrospective**: Nov 25 (Monday)
**Sprint 2 Planning**: Nov 26 (Tuesday)

---

## 📝 Definition of Done (DoD)

For a story to be considered "Done":

✅ **Code Complete**
- Feature implemented according to acceptance criteria
- Code follows Laravel best practices & PSR-12
- No hardcoded values (use config/env)

✅ **Tested**
- Unit tests written (minimum 80% coverage)
- Feature tests passing
- Manual testing by QA completed
- No critical/high severity bugs

✅ **Documented**
- Code comments for complex logic
- PHPDoc blocks for all methods
- README updated if needed

✅ **Reviewed**
- Code review by at least 1 other developer
- PR approved and merged to dev branch

✅ **Deployed**
- Deployed to development environment
- Database migrations run successfully
- No breaking changes to existing features

---

## 🎯 Sprint 1 Acceptance Criteria

### **Multi-Tenancy**
- [ ] Can create multiple tenants via seeder
- [ ] Each tenant has isolated database context
- [ ] Subdomain routing works (tenant1.mcv3.local, tenant2.mcv3.local)
- [ ] Cross-tenant data queries return empty (isolation verified)
- [ ] Tenant middleware blocks unauthorized access

### **Subscription Plans**
- [ ] 3 plans defined: Starter, Professional, Enterprise
- [ ] Trial period (14 days) automatically set on tenant creation
- [ ] Plan features stored and retrievable
- [ ] Basic invoice generation works

### **Usage Tracking**
- [ ] Document generation increments counter
- [ ] Storage usage calculated on file upload
- [ ] User count compared against plan limit
- [ ] Quota exceeded notification sent

### **Platform Admin**
- [ ] Super admin can log in
- [ ] Admin can view all tenants in dashboard
- [ ] Admin can create/edit/delete tenants
- [ ] Basic analytics show: total tenants, active subscriptions

### **Security**
- [ ] Login attempts limited (5 per 15 minutes)
- [ ] IP addresses logged on login
- [ ] Sessions expire after 2 hours inactivity
- [ ] Activity log captures critical actions

---

## 🧪 Testing Plan

### **Unit Tests** (Developer responsibility)
```php
// Example tests to write

// TenantTest.php
- test_tenant_can_be_created()
- test_tenant_has_subscription()
- test_tenant_trial_period_calculation()

// SubscriptionTest.php
- test_subscription_plan_features()
- test_trial_period_expiry()
- test_usage_quota_enforcement()

// UsageTrackerTest.php
- test_document_generation_increments_counter()
- test_storage_usage_calculation()
- test_quota_exceeded_detection()
```

### **Feature Tests** (Developer + QA)
```php
// Example feature tests

// TenantIsolationTest.php
- test_tenant_cannot_access_other_tenant_data()
- test_subdomain_routes_to_correct_tenant()

// SubscriptionFlowTest.php
- test_new_tenant_starts_trial()
- test_invoice_generation()

// AdminDashboardTest.php
- test_super_admin_can_manage_tenants()
- test_analytics_show_correct_metrics()
```

### **Manual Testing Checklist** (QA)
- [ ] Create tenant via admin panel
- [ ] Login as tenant
- [ ] Switch between tenants (different subdomains)
- [ ] Try to access another tenant's data (should fail)
- [ ] Generate document, check counter increments
- [ ] Upload file, check storage usage updates
- [ ] Exceed quota, verify notification sent
- [ ] View invoice from admin panel

---

## 🚧 Risks & Mitigation

| Risk | Probability | Impact | Mitigation |
|------|-------------|--------|------------|
| **stancl/tenancy learning curve** | High | High | - Allocate 1 day for R&D<br>- Pair programming<br>- Documentation reading |
| **Database migration complexity** | Medium | High | - Test migrations on copy of prod DB<br>- Have rollback plan<br>- Incremental migrations |
| **Tenant isolation bugs** | Medium | Critical | - Extensive testing<br>- Peer code review<br>- Automated tests |
| **Performance with many tenants** | Low | Medium | - Monitor query performance<br>- Use eager loading<br>- Database indexing |
| **Developer availability** | Low | High | - Daily standups<br>- Clear documentation<br>- Knowledge sharing |

---

## 📈 Sprint Metrics to Track

### **Velocity**
- [ ] Planned: 125 points
- [ ] Completed: ___ points (update at sprint end)
- [ ] Velocity %: ___ %

### **Quality**
- [ ] Bugs found: ___ (target < 10)
- [ ] Critical bugs: ___ (target 0)
- [ ] Code coverage: ___ % (target > 80%)

### **Delivery**
- [ ] Stories completed: ___ / 19
- [ ] Stories carried over: ___ (target 0)
- [ ] Blocked stories: ___ (target 0)

### **Team Health**
- [ ] Daily standup attendance: ___ %
- [ ] PR review time: ___ hours (target < 4h)
- [ ] Team satisfaction: ___ / 5

---

## 🔄 Sprint Ceremonies

### **Daily Standup** (15 min, 9:00 AM)
Each developer answers:
1. What did I complete yesterday?
2. What will I work on today?
3. Any blockers?

**Format**: Slack/Discord or video call

### **Sprint Review** (2h, Nov 25 - 2:00 PM)
- Demo completed features
- Gather feedback from stakeholders
- Accept/reject stories based on DoD

**Attendees**: Dev team, QA, Product Owner, Stakeholders

### **Sprint Retrospective** (1.5h, Nov 25 - 4:00 PM)
- What went well?
- What didn't go well?
- Action items for next sprint

**Format**: Start/Stop/Continue or Mad/Sad/Glad

### **Sprint Planning** (3h, Nov 26 - 9:00 AM)
- Review backlog
- Estimate story points
- Commit to Sprint 2 scope

---

## 📚 Technical Setup Required

### **Before Sprint Start** (Pre-work)
```bash
# 1. Development environment
- Laravel 12 installed
- PHP 8.2+
- MySQL 8.0
- Redis installed
- Node.js & NPM

# 2. Tools
- Git configured
- IDE setup (VS Code / PHPStorm)
- Laravel Debugbar enabled
- Telescope installed

# 3. Packages to install Sprint 1
composer require stancl/tenancy
composer require --dev pestphp/pest
```

### **Development Workflow**
```bash
# Branch naming
feature/F0.1.1-install-tenancy-package
feature/F0.2.1-subscription-plans-table

# Commit message format
feat(tenancy): install stancl/tenancy package [F0.1.1]
fix(tenancy): resolve tenant isolation bug [F0.1.7]
test(subscription): add trial period tests [F0.2.3]

# PR template
Title: [F0.1.1] Install stancl/tenancy package
Description:
- Installed stancl/tenancy
- Configured central domains
- Created basic tenant structure

Closes #F0.1.1
```

---

## 🎓 Learning Resources

### **Multi-Tenancy**
- [stancl/tenancy Documentation](https://tenancyforlaravel.com/docs/v3/)
- [Laravel Multi-Tenancy Guide](https://laravel.com/docs/12.x/multi-tenancy)
- Video: "Multi-Tenancy in Laravel" by Laracasts

### **Testing**
- [Pest PHP Documentation](https://pestphp.com)
- [Laravel Testing Guide](https://laravel.com/docs/12.x/testing)

### **Team Resources**
- Slack channel: #mcv3-dev
- Wiki: Company Confluence
- Code repo: https://github.com/rhecustein/mcv3

---

## ✅ Sprint 1 Checklist

### **Before Sprint Start**
- [ ] All developers have dev environment set up
- [ ] Access to repositories granted
- [ ] Slack/Discord channels created
- [ ] Sprint planning meeting scheduled
- [ ] Backlog items refined and estimated

### **During Sprint**
- [ ] Daily standups held
- [ ] Stories moved through board (To Do → In Progress → Review → Done)
- [ ] Code reviews completed within 4 hours
- [ ] Tests written for all features
- [ ] Documentation updated

### **End of Sprint**
- [ ] All stories meet Definition of Done
- [ ] Demo prepared for Sprint Review
- [ ] Retrospective action items documented
- [ ] Sprint 2 backlog ready

---

## 📊 Sprint 1 Kanban Board Setup

### **Columns**
1. **Backlog** - All Sprint 1 stories
2. **To Do** - Ready to start (refined, estimated)
3. **In Progress** - Currently being worked on (max 3 per developer)
4. **Code Review** - PR submitted, awaiting review
5. **QA Testing** - Feature tested by QA
6. **Done** - Meets DoD, merged to dev

### **Labels**
- `P0` - Critical
- `backend` - Backend work
- `frontend` - Frontend work
- `bug` - Bug fix
- `blocked` - Blocked by dependency
- `ready-for-review` - PR submitted

---

## 🚀 Success Criteria for Sprint 1

✅ **Must Have (Critical)**
- Multi-tenancy package installed and configured
- Tenant isolation verified (no data leaks)
- Subdomain routing functional
- 3 subscription plans defined
- Basic admin dashboard operational
- All P0 features completed

✅ **Should Have (Important)**
- 80%+ test coverage
- All code reviewed
- < 5 bugs found in QA
- Documentation complete

✅ **Could Have (Nice to Have)**
- Performance benchmarks documented
- CI/CD pipeline started
- Deployment to staging environment

---

## 📝 Notes & Tips

### **For Developers**
- Focus on quality over quantity
- Write tests BEFORE implementing features (TDD)
- Ask for help if blocked > 2 hours
- Commit frequently, push daily
- Document complex logic

### **For QA**
- Test as features are completed (don't wait till end)
- Report bugs immediately in Jira/GitHub
- Provide steps to reproduce
- Verify fixes before closing tickets

### **For Team**
- Communicate openly about blockers
- Help each other (pair programming encouraged)
- Celebrate small wins
- Be honest in retrospectives

---

## 🎯 Ready for Sprint 1?

**Pre-Sprint Checklist:**
- [ ] Team has read this document
- [ ] Questions answered in planning meeting
- [ ] Development environment ready
- [ ] Tools & access granted
- [ ] Backlog items understood
- [ ] Everyone committed to sprint goal

**LET'S BUILD! 🚀**

---

**Next Sprint Planning**: Sprint 2 - Subscription & Billing (Payment Gateway Integration)

**Document Version**: 1.0
**Status**: ✅ Ready for Team Review
