# 🌐 الـ API والـ WebAPI

> الدليل الشامل لـ REST API و GraphQL في Magento 2

---

## 📑 الفهرس

1. [مقدمة](#1-مقدمة)
2. [Service Contracts](#2-service-contracts)
3. [webapi.xml](#3-webapixml)
4. [Authentication](#4-authentication)
5. [REST API](#5-rest-api)
6. [GraphQL](#6-graphql)
7. [ACL Resources](#7-acl-resources)
8. [Error Handling](#8-error-handling)
9. [Best Practices](#9-best-practices)
10. [مستوى متقدم](#10-مستوى-متقدم)

---

## 1. مقدمة

### أنواع الـ API

| النوع | Protocol | استخدام |
|-------|----------|---------|
| **REST** | HTTP/JSON | CRUD operations |
| **GraphQL** | HTTP/JSON | Flexible queries |
| **SOAP** | HTTP/XML | Legacy systems |

### الهيكل

```
app/code/Vendor/Module/
├── Api/
│   ├── Data/
│   │   └── EntityInterface.php        # Data interface
│   └── EntityRepositoryInterface.php  # Service interface
├── Model/
│   └── EntityRepository.php           # Implementation
└── etc/
    ├── webapi.xml                      # REST/SOAP routes
    └── schema.graphqls                 # GraphQL schema
```

---

## 2. Service Contracts

### Repository Interface

```php
<?php
declare(strict_types=1);

namespace Vendor\Module\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Vendor\Module\Api\Data\EntityInterface;
use Vendor\Module\Api\Data\EntitySearchResultsInterface;

/**
 * Entity Repository Interface
 * @api
 */
interface EntityRepositoryInterface
{
    /**
     * Save entity
     *
     * @param \Vendor\Module\Api\Data\EntityInterface $entity
     * @return \Vendor\Module\Api\Data\EntityInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(EntityInterface $entity): EntityInterface;

    /**
     * Get entity by ID
     *
     * @param int $entityId
     * @return \Vendor\Module\Api\Data\EntityInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $entityId): EntityInterface;

    /**
     * Delete entity
     *
     * @param \Vendor\Module\Api\Data\EntityInterface $entity
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(EntityInterface $entity): bool;

    /**
     * Get list
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Vendor\Module\Api\Data\EntitySearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): EntitySearchResultsInterface;
}
```

### Data Interface

```php
<?php
declare(strict_types=1);

namespace Vendor\Module\Api\Data;

/**
 * Entity Data Interface
 * @api
 */
interface EntityInterface
{
    public const ENTITY_ID = 'entity_id';
    public const NAME = 'name';
    public const STATUS = 'status';

    /**
     * Get entity ID
     *
     * @return int|null
     */
    public function getEntityId(): ?int;

    /**
     * Set entity ID
     *
     * @param int $entityId
     * @return $this
     */
    public function setEntityId(int $entityId): self;

    /**
     * Get name
     *
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self;
}
```

> 📝 **مهم:** DocBlocks ضرورية للـ API لأن Magento يستخدمها لتوليد الـ schema.

---

## 3. webapi.xml

### الكود الكامل

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

    <!-- GET list -->
    <route url="/V1/vendor-module/entities" method="GET">
        <service class="Vendor\Module\Api\EntityRepositoryInterface" method="getList"/>
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

    <!-- PUT update -->
    <route url="/V1/vendor-module/entities/:entityId" method="PUT">
        <service class="Vendor\Module\Api\EntityRepositoryInterface" method="save"/>
        <resources>
            <resource ref="Vendor_Module::entity_save"/>
        </resources>
    </route>

    <!-- DELETE -->
    <route url="/V1/vendor-module/entities/:entityId" method="DELETE">
        <service class="Vendor\Module\Api\EntityRepositoryInterface" method="deleteById"/>
        <resources>
            <resource ref="Vendor_Module::entity_delete"/>
        </resources>
    </route>

    <!-- Anonymous access -->
    <route url="/V1/vendor-module/public-data" method="GET">
        <service class="Vendor\Module\Api\PublicDataInterface" method="getData"/>
        <resources>
            <resource ref="anonymous"/>
        </resources>
    </route>

    <!-- Customer self-service -->
    <route url="/V1/vendor-module/customer/orders" method="GET">
        <service class="Vendor\Module\Api\CustomerOrderInterface" method="getMyOrders"/>
        <resources>
            <resource ref="self"/>
        </resources>
    </route>
</routes>
```

### Route Attributes

| Attribute | الوظيفة |
|-----------|---------|
| `url` | الـ endpoint path |
| `method` | HTTP method |
| `service.class` | الـ Interface |
| `service.method` | الـ method name |
| `resource.ref` | ACL resource |

### URL Parameters

```xml
<!-- :entityId يتطابق مع $entityId في method -->
<route url="/V1/entities/:entityId" method="GET">
    <service class="..." method="getById"/>
    <!-- getById(int $entityId) -->
</route>

<!-- Multiple parameters -->
<route url="/V1/stores/:storeId/entities/:entityId" method="GET">
    <service class="..." method="getByIdAndStore"/>
    <!-- getByIdAndStore(int $entityId, int $storeId) -->
</route>
```

---

## 4. Authentication

### أنواع الـ Authentication

| Resource | الوصف | استخدام |
|----------|-------|---------|
| `anonymous` | بدون authentication | Public API |
| `self` | Customer token | Customer self-service |
| `Vendor_Module::resource` | Admin token | Admin API |

### Admin Token

```bash
# الحصول على token
curl -X POST "https://store.com/rest/V1/integration/admin/token" \
     -H "Content-Type: application/json" \
     -d '{"username":"admin","password":"admin123"}'

# استخدام الـ token
curl -X GET "https://store.com/rest/V1/vendor-module/entities/1" \
     -H "Authorization: Bearer {token}"
```

### Customer Token

```bash
# الحصول على token
curl -X POST "https://store.com/rest/V1/integration/customer/token" \
     -H "Content-Type: application/json" \
     -d '{"username":"customer@example.com","password":"password"}'
```

### Integration Token

```
System → Integrations → Add New Integration
→ API permissions
→ Activate
→ Access Token
```

---

## 5. REST API

### GET Request

```bash
# Get single entity
curl -X GET "https://store.com/rest/V1/vendor-module/entities/1" \
     -H "Authorization: Bearer {token}"

# Response
{
    "entity_id": 1,
    "name": "Entity Name",
    "status": 1
}
```

### GET List with SearchCriteria

```bash
# Get list with filters
curl -X GET "https://store.com/rest/V1/vendor-module/entities?\
searchCriteria[filterGroups][0][filters][0][field]=status&\
searchCriteria[filterGroups][0][filters][0][value]=1&\
searchCriteria[filterGroups][0][filters][0][conditionType]=eq&\
searchCriteria[pageSize]=10&\
searchCriteria[currentPage]=1" \
     -H "Authorization: Bearer {token}"
```

### POST Create

```bash
curl -X POST "https://store.com/rest/V1/vendor-module/entities" \
     -H "Authorization: Bearer {token}" \
     -H "Content-Type: application/json" \
     -d '{
         "entity": {
             "name": "New Entity",
             "status": 1
         }
     }'
```

### PUT Update

```bash
curl -X PUT "https://store.com/rest/V1/vendor-module/entities/1" \
     -H "Authorization: Bearer {token}" \
     -H "Content-Type: application/json" \
     -d '{
         "entity": {
             "entity_id": 1,
             "name": "Updated Name"
         }
     }'
```

### DELETE

```bash
curl -X DELETE "https://store.com/rest/V1/vendor-module/entities/1" \
     -H "Authorization: Bearer {token}"
```

---

## 6. GraphQL

### Schema Definition

```graphql
# etc/schema.graphqls

type Query {
    vendorEntity(id: Int! @doc(description: "Entity ID")): VendorEntity
        @resolver(class: "Vendor\\Module\\Model\\Resolver\\Entity")
        @doc(description: "Get entity by ID")

    vendorEntities(
        pageSize: Int = 20 @doc(description: "Page size")
        currentPage: Int = 1 @doc(description: "Current page")
    ): VendorEntities
        @resolver(class: "Vendor\\Module\\Model\\Resolver\\Entities")
}

type Mutation {
    createVendorEntity(input: VendorEntityInput!): VendorEntity
        @resolver(class: "Vendor\\Module\\Model\\Resolver\\CreateEntity")
}

type VendorEntity {
    entity_id: Int @doc(description: "Entity ID")
    name: String @doc(description: "Entity name")
    status: Int @doc(description: "Status")
    created_at: String @doc(description: "Created date")
}

type VendorEntities {
    items: [VendorEntity] @doc(description: "Entity list")
    total_count: Int @doc(description: "Total count")
}

input VendorEntityInput {
    name: String! @doc(description: "Entity name")
    status: Int @doc(description: "Status")
}
```

### Resolver

```php
<?php
declare(strict_types=1);

namespace Vendor\Module\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Vendor\Module\Api\EntityRepositoryInterface;

class Entity implements ResolverInterface
{
    public function __construct(
        private EntityRepositoryInterface $entityRepository
    ) {}

    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ): array {
        $entityId = $args['id'];
        $entity = $this->entityRepository->getById($entityId);

        return [
            'entity_id' => $entity->getEntityId(),
            'name' => $entity->getName(),
            'status' => $entity->getStatus(),
            'created_at' => $entity->getCreatedAt()
        ];
    }
}
```

### GraphQL Query

```graphql
query {
    vendorEntity(id: 1) {
        entity_id
        name
        status
    }
}
```

### GraphQL Mutation

```graphql
mutation {
    createVendorEntity(input: {
        name: "New Entity"
        status: 1
    }) {
        entity_id
        name
    }
}
```

---

## 7. ACL Resources

### acl.xml

```xml
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="urn:magento:framework:Acl/etc/acl.xsd">
    <acl>
        <resources>
            <resource id="Magento_Backend::admin">
                <resource id="Vendor_Module::top_level" title="My Module">
                    <resource id="Vendor_Module::entity" title="Entities">
                        <resource id="Vendor_Module::entity_view" title="View"/>
                        <resource id="Vendor_Module::entity_save" title="Save"/>
                        <resource id="Vendor_Module::entity_delete" title="Delete"/>
                    </resource>
                </resource>
            </resource>
        </resources>
    </acl>
</config>
```

---

## 8. Error Handling

### Exception Types

| Exception | HTTP Code | استخدام |
|-----------|-----------|---------|
| `NoSuchEntityException` | 404 | Entity not found |
| `CouldNotSaveException` | 500 | Save failed |
| `CouldNotDeleteException` | 500 | Delete failed |
| `InputException` | 400 | Invalid input |
| `AuthorizationException` | 401 | Not authorized |
| `LocalizedException` | 500 | General error |

### Implementation

```php
public function getById(int $entityId): EntityInterface
{
    $entity = $this->entityFactory->create();
    $this->resourceModel->load($entity, $entityId);

    if (!$entity->getId()) {
        throw new NoSuchEntityException(
            __('Entity with ID "%1" does not exist.', $entityId)
        );
    }

    return $entity;
}
```

---

## 9. Best Practices

### ✅ استخدم Interfaces

```php
// في webapi.xml استخدم Interface
<service class="Vendor\Module\Api\EntityRepositoryInterface" .../>
```

### ✅ Versioned URLs

```xml
<!-- Version في URL -->
<route url="/V1/vendor-module/entities" .../>
<route url="/V2/vendor-module/entities" .../> <!-- New version -->
```

### ✅ DocBlocks للـ Types

```php
/**
 * @param \Vendor\Module\Api\Data\EntityInterface $entity
 * @return \Vendor\Module\Api\Data\EntityInterface
 */
public function save(EntityInterface $entity): EntityInterface;
```

---

## 10. مستوى متقدم

### Custom API Endpoint (Without Repository)

```php
<?php
namespace Vendor\Module\Api;

interface CustomApiInterface
{
    /**
     * Custom action
     *
     * @param string $param
     * @return string[]
     */
    public function customAction(string $param): array;
}
```

```xml
<route url="/V1/vendor-module/custom/:param" method="GET">
    <service class="Vendor\Module\Api\CustomApiInterface" method="customAction"/>
    <resources>
        <resource ref="anonymous"/>
    </resources>
</route>
```

### Rate Limiting

في `env.php`:
```php
'webapi' => [
    'rate_limiting' => [
        'guest' => [
            'limit' => 100,
            'period' => 3600
        ]
    ]
]
```

---

## 📌 ملخص

| المكون | المسار |
|--------|--------|
| **Service Interface** | `Api/EntityRepositoryInterface.php` |
| **Data Interface** | `Api/Data/EntityInterface.php` |
| **webapi.xml** | `etc/webapi.xml` |
| **GraphQL Schema** | `etc/schema.graphqls` |
| **ACL** | `etc/acl.xml` |

---

## ⬅️ [السابق](./10_PLUGINS.md) | [🏠 الرئيسية](../MODULE_STRUCTURE.md) | [التالي ➡️](./12_SETUP.md)
