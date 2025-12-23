# 📚 Magento 2 Module Structure Guide

> Complete guide to Magento 2 module structure with links to detailed documentation

---

## 📁 Complete Module Structure

```
app/code/Vendor/ModuleName/
│
├── 📄 registration.php              # [1] Module Registration
│
├── 📂 etc/                          # [2] Configuration Files
│   ├── module.xml                   # Module Definition
│   ├── di.xml                       # Dependency Injection
│   ├── events.xml                   # Event Registration
│   ├── crontab.xml                  # Cron Jobs Schedule
│   ├── webapi.xml                   # API Endpoints
│   ├── acl.xml                      # Access Control List
│   ├── config.xml                   # Default Config Values
│   │
│   ├── 📂 adminhtml/                # Admin Configuration
│   │   ├── routes.xml
│   │   ├── system.xml               # Admin Panel Settings
│   │   ├── menu.xml                 # Admin Menus
│   │   └── di.xml
│   │
│   └── 📂 frontend/                 # Frontend Configuration
│       ├── routes.xml
│       ├── sections.xml             # Customer Sections
│       └── di.xml
│
├── 📂 Api/                          # [3] Service Contracts
│   ├── 📂 Data/                     # Data Interfaces
│   │   └── EntityInterface.php
│   └── EntityRepositoryInterface.php
│
├── 📂 Block/                        # [4] View Blocks
│   ├── 📂 Adminhtml/                # Admin Blocks
│   └── SomeBlock.php
│
├── 📂 Controller/                   # [5] Controllers
│   ├── 📂 Adminhtml/                # Admin Controllers
│   │   └── Entity/
│   │       ├── Index.php
│   │       ├── Edit.php
│   │       ├── Save.php
│   │       └── Delete.php
│   └── 📂 Index/                    # Frontend Controllers
│       └── Index.php
│
├── 📂 Model/                        # [6] Models
│   ├── Entity.php                   # Data Model
│   ├── EntityRepository.php         # Repository
│   ├── 📂 ResourceModel/            # Database Layer
│   │   ├── Entity.php               # Resource Model
│   │   └── Entity/
│   │       └── Collection.php       # Collection
│   └── 📂 Config/
│       └── Source/                  # Dropdowns
│
├── 📂 Observer/                     # [7] Event Observers
│   └── SomeObserver.php
│
├── 📂 Plugin/                       # [8] Plugins (Interceptors)
│   └── SomePlugin.php
│
├── 📂 Helper/                       # [9] Helper Classes
│   └── Data.php
│
├── 📂 Setup/                        # [10] Installation
│   └── 📂 Patch/
│       ├── 📂 Data/                 # Data Patches
│       │   └── AddSampleData.php
│       └── 📂 Schema/               # Schema Patches
│           └── CreateTable.php
│
├── 📂 Cron/                         # [11] Cron Jobs
│   └── SomeJob.php
│
├── 📂 Console/                      # [12] CLI Commands
│   └── Command/
│       └── SomeCommand.php
│
├── 📂 view/                         # [13] View Layer
│   ├── 📂 adminhtml/
│   │   ├── 📂 layout/               # Admin Layouts
│   │   ├── 📂 templates/            # Admin Templates
│   │   ├── 📂 ui_component/         # UI Components
│   │   └── 📂 web/
│   │       ├── css/
│   │       └── js/
│   │
│   ├── 📂 frontend/
│   │   ├── 📂 layout/               # Frontend Layouts
│   │   ├── 📂 templates/            # PHTML Files
│   │   ├── 📂 requirejs-config.js   # RequireJS Config
│   │   └── 📂 web/
│   │       ├── css/
│   │       ├── js/
│   │       └── images/
│   │
│   └── 📂 base/                     # Shared (Admin + Frontend)
│       └── 📂 web/
│
├── 📂 i18n/                         # [14] Translations
│   ├── en_US.csv
│   └── ar_SA.csv
│
├── 📂 Test/                         # Tests
│   ├── 📂 Unit/
│   └── 📂 Integration/
│
└── 📄 composer.json                 # Composer Package Info (optional)
```

---

## 🔄 Basic Flow

```mermaid
flowchart TD
    subgraph Bootstrap["🚀 Bootstrap"]
        A[HTTP Request] --> B[registration.php]
        B --> C[module.xml]
    end

    subgraph Routing["🛣️ Routing"]
        C --> D[routes.xml]
        D --> E[Controller/Action]
    end

    subgraph Business["💼 Business Logic"]
        E --> F[Model]
        F --> G[ResourceModel]
        G --> H[(Database)]
    end

    subgraph Presentation["🎨 Presentation"]
        E --> I[Block]
        I --> J[Layout XML]
        J --> K[Template PHTML]
    end

    K --> L[HTTP Response]

    style Bootstrap fill:#e1f5fe
    style Routing fill:#fff3e0
    style Business fill:#f3e5f5
    style Presentation fill:#e8f5e9
```

---

## 📖 Detailed Documentation

> Each module component has its own detailed documentation file

| # | Component | File | Status |
|---|-----------|------|--------|
| 1 | Registration | [docs/en/01_REGISTRATION.md](./docs/en/01_REGISTRATION.md) | ✅ |
| 2 | Module XML | [docs/en/02_MODULE_XML.md](./docs/en/02_MODULE_XML.md) | ✅ |
| 3 | Routes | [docs/en/03_ROUTES.md](./docs/en/03_ROUTES.md) | ✅ |
| 4 | Controllers | [docs/en/04_CONTROLLERS.md](./docs/en/04_CONTROLLERS.md) | ✅ |
| 5 | Models | [docs/en/05_MODELS.md](./docs/en/05_MODELS.md) | ✅ |
| 6 | Blocks | [docs/en/06_BLOCKS.md](./docs/en/06_BLOCKS.md) | ✅ |
| 7 | Views & Layouts | [docs/en/07_VIEWS.md](./docs/en/07_VIEWS.md) | ✅ |
| 8 | Dependency Injection | [docs/en/08_DI.md](./docs/en/08_DI.md) | ✅ |
| 9 | Observers | [docs/en/09_OBSERVERS.md](./docs/en/09_OBSERVERS.md) | ✅ |
| 10 | Plugins | [docs/en/10_PLUGINS.md](./docs/en/10_PLUGINS.md) | ✅ |
| 11 | API & WebAPI | [docs/en/11_API.md](./docs/en/11_API.md) | ✅ |
| 12 | Setup & Patches | [docs/en/12_SETUP.md](./docs/en/12_SETUP.md) | ✅ |
| 13 | CLI Commands | [docs/en/13_CLI.md](./docs/en/13_CLI.md) | ✅ |
| 14 | Cron Jobs | [docs/en/14_CRON.md](./docs/en/14_CRON.md) | ✅ |

### 🔥 Advanced Topics

| # | Component | File | Status |
|---|-----------|------|--------|
| 15 | EAV System | [docs/en/15_EAV.md](./docs/en/15_EAV.md) | ✅ |
| 16 | XML Configuration | [docs/en/16_XML_CONFIGURATION.md](./docs/en/16_XML_CONFIGURATION.md) | ✅ |
| 17 | UI Components | [docs/en/17_UI_COMPONENTS.md](./docs/en/17_UI_COMPONENTS.md) | ✅ |
| 18 | Indexers | [docs/en/18_INDEXERS.md](./docs/en/18_INDEXERS.md) | ✅ |
| 19 | Caching | [docs/en/19_CACHING.md](./docs/en/19_CACHING.md) | ✅ |
| 20 | ACL & Security | [docs/en/20_ACL_SECURITY.md](./docs/en/20_ACL_SECURITY.md) | ✅ |
| 21 | Testing | [docs/en/21_TESTING.md](./docs/en/21_TESTING.md) | ✅ |
| 22 | GraphQL | [docs/en/22_GRAPHQL.md](./docs/en/22_GRAPHQL.md) | ✅ |
| 23 | Message Queues | [docs/en/23_MESSAGE_QUEUES.md](./docs/en/23_MESSAGE_QUEUES.md) | ✅ |
| 24 | Payment Methods | [docs/en/24_PAYMENT.md](./docs/en/24_PAYMENT.md) | ✅ |
| 25 | Checkout | [docs/en/25_CHECKOUT.md](./docs/en/25_CHECKOUT.md) | ✅ |
| 26 | Themes | [docs/en/26_THEMES.md](./docs/en/26_THEMES.md) | ✅ |

---

## 🎓 Certification Roadmap

> **Want to get Adobe Commerce Developer Certification?**

📚 [**6-Month Certification Plan**](./certification-roadmap/README.md)

> **📚 Arabic Version:** [MODULE_STRUCTURE.md](./MODULE_STRUCTURE.md)

> **Legend:** ✅ Complete | 📝 In Progress | ⏳ Not Started

---

## 🔴 Minimum Required Files

To create a working module, you only need:

```
Vendor/ModuleName/
├── registration.php    # Required ❗
└── etc/
    └── module.xml      # Required ❗
```

### 1️⃣ registration.php
```php
<?php
use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'Vendor_ModuleName',
    __DIR__
);
```

### 2️⃣ etc/module.xml
```xml
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="urn:magento:framework:Module/etc/module.xsd">
    <module name="Vendor_ModuleName" setup_version="1.0.0">
        <sequence>
            <!-- Dependencies here -->
        </sequence>
    </module>
</config>
```

---

## 🚀 Important Commands

```bash
# Enable module
bin/magento module:enable Vendor_ModuleName

# Update
bin/magento setup:upgrade

# Compile DI
bin/magento setup:di:compile

# Clear cache
bin/magento cache:flush

# Check module status
bin/magento module:status Vendor_ModuleName
```

---

## 📌 Quick Reference

| Element | Path | Purpose |
|---------|------|---------|
| Registration | `registration.php` | Register module in Magento |
| Definition | `etc/module.xml` | Module name + dependencies |
| Routes | `etc/*/routes.xml` | Link URLs to Controllers |
| DI | `etc/di.xml` | Dependency Injection |
| Events | `etc/events.xml` | Register Observers |
| Cron | `etc/crontab.xml` | Schedule Tasks |
| ACL | `etc/acl.xml` | Access Permissions |
| API | `etc/webapi.xml` | REST/GraphQL Endpoints |

---

## 🔗 Useful Links

- [Magento DevDocs - Module Development](https://developer.adobe.com/commerce/php/development/)
- [Magento Coding Standards](https://developer.adobe.com/commerce/php/coding-standards/)
- [Magento Architecture](https://developer.adobe.com/commerce/php/architecture/)

---

> [!TIP]
> **Start with the basics!** Understand `registration.php` and `module.xml` first, then move on to Controllers and Models.
