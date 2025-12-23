# 🔷 GraphQL

> Complete Guide to GraphQL in Magento 2

---

## 📑 Table of Contents

1. [Introduction](#1-introduction)
2. [Schema Definition](#2-schema-definition)
3. [Queries](#3-queries)
4. [Mutations](#4-mutations)
5. [Resolvers](#5-resolvers)
6. [Best Practices](#6-best-practices)

---

## 1. Introduction

### What is GraphQL?

GraphQL is a query language for APIs that allows clients to request exactly the data they need.

### GraphQL vs REST

| Feature | REST | GraphQL |
|---------|------|---------|
| **Endpoints** | Multiple | Single `/graphql` |
| **Data** | Fixed response | Flexible |
| **Versioning** | v1, v2, v3 | Not needed |

---

## 2. Schema Definition

### schema.graphqls

```graphql
# etc/schema.graphqls

type Query {
    vendorEntity(id: Int!): VendorEntity
        @resolver(class: "Vendor\\Module\\Model\\Resolver\\Entity")

    vendorEntities(pageSize: Int = 20, currentPage: Int = 1): VendorEntities
        @resolver(class: "Vendor\\Module\\Model\\Resolver\\Entities")
}

type Mutation {
    createVendorEntity(input: VendorEntityInput!): VendorEntity
        @resolver(class: "Vendor\\Module\\Model\\Resolver\\CreateEntity")

    deleteVendorEntity(id: Int!): Boolean
        @resolver(class: "Vendor\\Module\\Model\\Resolver\\DeleteEntity")
}

type VendorEntity {
    entity_id: Int
    name: String
    status: Int
}

type VendorEntities {
    items: [VendorEntity]
    total_count: Int
}

input VendorEntityInput {
    name: String!
    status: Int
}
```

---

## 3. Queries

```graphql
# Get single entity
query {
    vendorEntity(id: 1) {
        entity_id
        name
        status
    }
}

# Get list
query {
    vendorEntities(pageSize: 10) {
        items {
            entity_id
            name
        }
        total_count
    }
}
```

---

## 4. Mutations

```graphql
# Create
mutation {
    createVendorEntity(input: { name: "New Entity", status: 1 }) {
        entity_id
        name
    }
}

# Delete
mutation {
    deleteVendorEntity(id: 1)
}
```

---

## 5. Resolvers

```php
<?php
namespace Vendor\Module\Model\Resolver;

use Magento\Framework\GraphQl\Query\ResolverInterface;

class Entity implements ResolverInterface
{
    public function resolve($field, $context, $info, $value = null, $args = null): array
    {
        $entity = $this->repository->getById($args['id']);

        return [
            'entity_id' => $entity->getId(),
            'name' => $entity->getName(),
            'status' => $entity->getStatus()
        ];
    }
}
```

---

## 6. Best Practices

### ✅ Cache Responses

```graphql
type Query {
    cachedQuery: Type @cache(cacheIdentity: "Vendor\\Module\\Model\\Resolver\\Identity")
}
```

### ✅ Error Handling

```php
throw new GraphQlInputException(__('Invalid input'));
throw new GraphQlNoSuchEntityException(__('Not found'));
```

---

## 📌 Summary

| Component | Purpose |
|-----------|---------|
| **schema.graphqls** | Define types |
| **Query** | Read data |
| **Mutation** | Write data |
| **Resolver** | Execute logic |

---

## ⬅️ [Previous](./21_TESTING.md) | [🏠 Home](../MODULE_STRUCTURE_EN.md) | [Next ➡️](./23_MESSAGE_QUEUES.md)
