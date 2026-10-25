# JIBIKA — Directory Structure & Naming Conventions

## 1. Directory Tree Organization

```text
app/
├── Actions/                              # Single-purpose domain business operations
│   ├── AssetRequests/
│   │   └── SubmitAssetRequestAction.php
│   ├── Assessments/
│   │   └── AssessAssetRequestAction.php
│   ├── Funding/
│   │   ├── ContributePledgeAction.php
│   │   └── OpenFundingPoolAction.php
│   ├── Monitoring/
│   │   └── AddMonitoringRecordAction.php
│   ├── Procurement/
│   │   ├── ConfirmDeliveryAction.php
│   │   ├── RejectQuotationAction.php
│   │   ├── ResolveDeliveryDisputeAction.php
│   │   ├── SelectQuotationAction.php
│   │   └── SubmitVendorQuotationAction.php
│   └── Verification/
│       └── DecideVerificationAction.php
├── Http/
│   ├── Controllers/
│   │   ├── AdminController.php           # Administrative oversight & contract award
│   │   ├── Api/
│   │   │   └── V1/                       # Version 1 JSON API Controllers
│   │   │       ├── AssetCategoryApiController.php
│   │   │       ├── AssetRequestApiController.php
│   │   │       └── FundingPoolApiController.php
│   │   ├── AssessmentController.php      # Assessment presentation & triggers
│   │   ├── AssetRequestController.php    # Worker asset requests & wizard
│   │   ├── Auth/                         # Authentication & registration
│   │   │   ├── LoginController.php
│   │   │   └── RegisterController.php
│   │   ├── FundingController.php         # Funding pool catalog & simulated pledges
│   │   ├── MonitoringController.php      # Post-delivery impact monitoring
│   │   ├── NotificationController.php    # Scoped user notifications
│   │   ├── ProcurementController.php     # Delivery tracking & dispute resolution
│   │   ├── ProfileController.php         # User profile management
│   │   ├── RoleDashboardController.php   # 5 dedicated role dashboards
│   │   ├── VendorController.php          # Vendor opportunity discovery & quoting
│   │   └── VerificationController.php    # Community verification queue
│   ├── Middleware/
│   │   └── Active.php                    # Account suspension enforcement
│   ├── Requests/                         # Form Requests (validation & authorization)
│   │   ├── Admin/
│   │   │   └── SetUserStatusRequest.php
│   │   ├── AssetRequest/
│   │   │   ├── StoreAssetRequestRequest.php
│   │   │   └── UpdateAssetRequestRequest.php
│   │   ├── Funding/
│   │   │   └── ContributePledgeRequest.php
│   │   ├── Monitoring/
│   │   │   └── StoreMonitoringRecordRequest.php
│   │   ├── Procurement/
│   │   │   ├── ConfirmDeliveryRequest.php
│   │   │   ├── ResolveDisputeRequest.php
│   │   │   └── SubmitVendorQuotationRequest.php
│   │   ├── Profile/
│   │   │   └── UpdateProfileRequest.php
│   │   └── Verification/
│   │       └── DecideVerificationRequest.php
│   └── Resources/                        # API Resources for JSON serialization
│       ├── AssetCategoryResource.php
│       ├── AssetRequestResource.php
│       ├── AssessmentRecordResource.php
│       ├── CommunityVerificationResource.php
│       └── FundingPoolResource.php
├── Models/                               # Eloquent models, casts, relations, immutability
│   ├── AssessmentRecord.php
│   ├── AssetCategory.php
│   ├── AssetRequest.php
│   ├── AuditLog.php                      # Append-only model with credential redaction
│   ├── CommunityProfile.php
│   ├── CommunityVerification.php
│   ├── ContributionOrPledge.php
│   ├── Delivery.php
│   ├── FundingPool.php
│   ├── MonitoringRecord.php
│   ├── Notification.php
│   ├── ProcurementOrder.php
│   ├── User.php
│   ├── VendorProfile.php
│   ├── VendorQuotation.php
│   └── WorkerProfile.php
├── Policies/                             # Fine-grained authorization policies
├── Services/                             # Reusable external domain capabilities
│   └── AIService.php                     # Transparent rule-based assessment engine
└── View/                                 # Blade component classes
    └── Components/
        └── Layout.php

resources/
├── views/
│   ├── auth/                             # Login & registration views
│   ├── components/                       # Reusable Blade UI components
│   │   ├── bdt-format.blade.php          # Currency formatter
│   │   ├── empty-state.blade.php         # Empty list / queue indicator
│   │   ├── provenance-badge.blade.php    # Non-LLM rule provenance pill
│   │   ├── simulated-banner.blade.php    # Simulated funding disclaimer
│   │   └── status-badge.blade.php        # Lifecycle status pill
│   ├── errors/                           # Branded production error views
│   │   ├── 403.blade.php
│   │   ├── 404.blade.php
│   │   ├── 500.blade.php
│   │   └── account-suspended.blade.php
│   ├── layouts/
│   │   └── app.blade.php                 # Global responsive authenticated layout
│   └── pages/                            # Role dashboards & functional domain pages
│       ├── admin/
│       ├── assessment/
│       ├── dashboards/                   # Dedicated dashboards for 5 roles
│       ├── funding/
│       ├── monitoring/
│       ├── notifications/
│       ├── procurement/
│       ├── profile/
│       ├── requests/
│       ├── vendor/
│       └── verification/

routes/
├── api.php                               # Version 1 JSON API endpoints
├── console.php                           # Artisan commands
└── web.php                               # Primary application routes
```

---

## 2. Naming Conventions

| Component | Convention | Example |
| :--- | :--- | :--- |
| **Classes & Enums** | `PascalCase` | `SelectQuotationAction`, `AssetRequestResource` |
| **Methods & Variables** | `camelCase` | `execute()`, `$requestedAmountBdt` |
| **Form Requests** | `[Verb][Entity]Request` | `StoreAssetRequestRequest`, `ConfirmDeliveryRequest` |
| **Policies** | `[Model]Policy` | `AssetRequestPolicy`, `VendorQuotationPolicy` |
| **Actions** | `[Verb][Entity]Action` | `SubmitAssetRequestAction`, `OpenFundingPoolAction` |
| **API Resources** | `[Model]Resource` | `AssetRequestResource`, `FundingPoolResource` |
| **Blade Views** | `kebab-case.blade.php` | `how-it-works.blade.php`, `status-badge.blade.php` |
| **Database Tables** | `snake_case` plural | `asset_requests`, `vendor_quotations` |
| **Database Columns** | `snake_case` | `requested_amount_bdt`, `current_amount_bdt` |
| **Route Names** | Resource-oriented dot notation | `requests.show`, `api.v1.funding-pools.index` |
| **Environment Vars** | `SCREAMING_SNAKE_CASE` | `SESSION_SECURE_COOKIE`, `AI_PROVIDER` |
