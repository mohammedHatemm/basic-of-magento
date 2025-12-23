# 🔐 ACL & Security

> Complete Guide to Security in Magento 2

---

## 📑 Table of Contents

1. [ACL Resources](#1-acl-resources)
2. [Admin Controllers](#2-admin-controllers)
3. [API Security](#3-api-security)
4. [CSRF Protection](#4-csrf-protection)
5. [XSS Prevention](#5-xss-prevention)
6. [SQL Injection](#6-sql-injection)
7. [Best Practices](#7-best-practices)

---

## 1. ACL Resources

### Define ACL (acl.xml)

```xml
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="urn:magento:framework:Acl/etc/acl.xsd">
    <acl>
        <resources>
            <resource id="Magento_Backend::admin">
                <resource id="Vendor_Module::main" title="Vendor Module">
                    <resource id="Vendor_Module::entity_view" title="View"/>
                    <resource id="Vendor_Module::entity_save" title="Save"/>
                    <resource id="Vendor_Module::entity_delete" title="Delete"/>
                </resource>
            </resource>
        </resources>
    </acl>
</config>
```

### Check Permissions

```php
use Magento\Framework\AuthorizationInterface;

class MyClass
{
    public function __construct(
        private AuthorizationInterface $authorization
    ) {}

    public function canView(): bool
    {
        return $this->authorization->isAllowed('Vendor_Module::entity_view');
    }
}
```

---

## 2. Admin Controllers

```php
<?php
namespace Vendor\Module\Controller\Adminhtml\Entity;

use Magento\Backend\App\Action;

class Index extends Action
{
    public const ADMIN_RESOURCE = 'Vendor_Module::entity_view';

    public function execute()
    {
        $resultPage = $this->pageFactory->create();
        return $resultPage;
    }
}
```

---

## 3. API Security

### webapi.xml

```xml
<!-- Admin access -->
<route url="/V1/entities" method="GET">
    <service class="Vendor\Module\Api\EntityRepositoryInterface" method="getList"/>
    <resources>
        <resource ref="Vendor_Module::entity_view"/>
    </resources>
</route>

<!-- Customer self-service -->
<route url="/V1/my-entities" method="GET">
    <resources>
        <resource ref="self"/>
    </resources>
</route>

<!-- Anonymous -->
<route url="/V1/public" method="GET">
    <resources>
        <resource ref="anonymous"/>
    </resources>
</route>
```

---

## 4. CSRF Protection

### Form Key Validation

```php
if (!$this->_formKeyValidator->validate($this->getRequest())) {
    throw new LocalizedException(__('Invalid form key'));
}
```

### In Templates

```php
<form action="<?= $block->getUrl('*/*/save') ?>" method="post">
    <?= $block->getBlockHtml('formkey') ?>
</form>
```

---

## 5. XSS Prevention

### Escaping Methods

```php
<?= $block->escapeHtml($value) ?>          // HTML content
<?= $block->escapeHtmlAttr($value) ?>      // HTML attributes
<?= $block->escapeUrl($url) ?>              // URLs
<?= $block->escapeJs($value) ?>             // JavaScript
```

### Example

```php
<!-- ✅ Correct -->
<div class="<?= $block->escapeHtmlAttr($className) ?>">
    <?= $block->escapeHtml($content) ?>
</div>

<!-- ❌ Dangerous -->
<div class="<?= $className ?>">
    <?= $content ?>
</div>
```

---

## 6. SQL Injection

### ✅ Safe Way

```php
$connection->select()
    ->from('table')
    ->where('id = ?', $id);

$collection->addFieldToFilter('id', $id);
```

### ❌ Dangerous

```php
// NEVER DO THIS
$connection->query("SELECT * FROM table WHERE id = $id");
```

---

## 7. Best Practices

### ✅ Always Define ACL

### ✅ Use ADMIN_RESOURCE

```php
public const ADMIN_RESOURCE = 'Vendor_Module::entity_view';
```

### ✅ Validate Form Key

### ✅ Escape All Output

### ✅ Use Bindings for SQL

---

## 📌 Summary

| Component | Purpose |
|-----------|---------|
| **ACL** | Define permissions |
| **ADMIN_RESOURCE** | Link Controller to ACL |
| **Form Key** | CSRF protection |
| **Escaping** | XSS prevention |
| **Bindings** | SQL injection prevention |

---

## ⬅️ [Previous](./19_CACHING.md) | [🏠 Home](../MODULE_STRUCTURE_EN.md) | [Next ➡️](./21_TESTING.md)
