# 🌐 API and WebAPI

> Complete Guide to REST API and GraphQL in Magento 2

---

## 📑 Table of Contents

1. [Introduction](#1-introduction)
2. [Service Contracts](#2-service-contracts)
3. [webapi.xml](#3-webapixml)
4. [Authentication](#4-authentication)
5. [REST API](#5-rest-api)
6. [GraphQL](#6-graphql)
7. [ACL Resources](#7-acl-resources)
8. [Best Practices](#8-best-practices)

---

## 1. Introduction

### API Types

| Type | Protocol | Usage |
|------|----------|-------|
| **REST** | HTTP/JSON | CRUD operations |
| **GraphQL** | HTTP/JSON | Flexible queries |
| **SOAP** | HTTP/XML | Legacy systems |

---

## 2. Service Contracts

### Repository Interface

```php
<?php
namespace Vendor\Module\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * @api
 */
interface EntityRepositoryInterface
{
    /**
     * @param \Vendor\Module\Api\Data\EntityInterface $entity
     * @return \Vendor\Module\Api\Data\EntityInterface
     */
    public function save(\Vendor\Module\Api\Data\EntityInterface $entity);

    /**
     * @param int $entityId
     * @return \Vendor\Module\Api\Data\EntityInterface
     */
    public function getById(int $entityId);

    /**
     * @param int $entityId
     * @return bool
     */
    public function deleteById(int $entityId): bool;
}
```

> 📝 **Important:** DocBlocks are required for API - Magento uses them to generate schema.

---

## 3. webapi.xml

```xml
<?xml version="1.0"?>
<routes xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="urn:magento:module:Magento_Webapi:etc/webapi.xsd">

    <!-- GET single entity -->
    <route url="/V1/vendor-module/entities/:entityId" method="GET">
        <service class="Vendor\Module\Api\EntityRepositoryInterface" method="getById"/>
        <resources>
            <resource ref="Vendor_Module::entity_view"/>
        </resources>
    </route>

    <!-- POST create -->
    <route url="/V1/vendor-module/entities" method="POST">
        <service class="Vendor\Module\Api\EntityRepositoryInterface" method="save"/>
        <resources>
            <resource ref="Vendor_Module::entity_save"/>
        </resources>
    </route>

    <!-- Anonymous access -->
    <route url="/V1/vendor-module/public-data" method="GET">
        <service class="Vendor\Module\Api\PublicDataInterface" method="getData"/>
        <resources>
            <resource ref="anonymous"/>
        </resources>
    </route>
</routes>
```

---

## 4. Authentication

### Resource Types

| Resource | Description | Usage |
|----------|-------------|-------|
| `anonymous` | No authentication | Public API |
| `self` | Customer token | Customer self-service |
| `Vendor::resource` | Admin token | Admin API |

### Get Admin Token

```bash
curl -X POST "https://store.com/rest/V1/integration/admin/token" \
     -H "Content-Type: application/json" \
     -d '{"username":"admin","password":"admin123"}'
```

### Use Token

```bash
curl -X GET "https://store.com/rest/V1/vendor-module/entities/1" \
     -H "Authorization: Bearer {token}"
```

---

## 5. REST API

### GET Request

```bash
curl -X GET "https://store.com/rest/V1/vendor-module/entities/1" \
     -H "Authorization: Bearer {token}"
```

### POST Create

```bash
curl -X POST "https://store.com/rest/V1/vendor-module/entities" \
     -H "Authorization: Bearer {token}" \
     -H "Content-Type: application/json" \
     -d '{"entity": {"name": "New Entity"}}'
```

---

## 6. GraphQL

### Schema

```graphql
# etc/schema.graphqls

type Query {
    vendorEntity(id: Int!): VendorEntity
        @resolver(class: "Vendor\\Module\\Model\\Resolver\\Entity")
}

type VendorEntity {
    entity_id: Int
    name: String
}
```

### Resolver

```php
class Entity implements ResolverInterface
{
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null): array
    {
        $entity = $this->repository->getById($args['id']);
        return [
            'entity_id' => $entity->getId(),
            'name' => $entity->getName()
        ];
    }
}
```

---

## 7. ACL Resources

```xml
<!-- etc/acl.xml -->
<config>
    <acl>
        <resources>
            <resource id="Magento_Backend::admin">
                <resource id="Vendor_Module::entity" title="Entities">
                    <resource id="Vendor_Module::entity_view" title="View"/>
                    <resource id="Vendor_Module::entity_save" title="Save"/>
                </resource>
            </resource>
        </resources>
    </acl>
</config>
```

---

## 8. Best Practices

### ✅ Use Interfaces

### ✅ Versioned URLs

```xml
<route url="/V1/vendor-module/entities" .../>
```

### ✅ Complete DocBlocks

```php
/**
 * @param \Vendor\Module\Api\Data\EntityInterface $entity
 * @return \Vendor\Module\Api\Data\EntityInterface
 */
```

---

## 📌 Summary

| Component | Path |
|-----------|------|
| **Service Interface** | `Api/EntityRepositoryInterface.php` |
| **Data Interface** | `Api/Data/EntityInterface.php` |
| **webapi.xml** | `etc/webapi.xml` |
| **GraphQL Schema** | `etc/schema.graphqls` |

---

## ⬅️ [Previous](./10_PLUGINS.md) | [🏠 Home](../MODULE_STRUCTURE_EN.md) | [Next ➡️](./12_SETUP.md)
