# 📊 Indexers

> Complete Guide to Indexers in Magento 2

---

## 📑 Table of Contents

1. [Introduction](#1-introduction)
2. [Core Indexers](#2-core-indexers)
3. [Index Modes](#3-index-modes)
4. [Custom Indexer](#4-custom-indexer)
5. [CLI Commands](#5-cli-commands)
6. [Best Practices](#6-best-practices)

---

## 1. Introduction

### What is an Indexer?

Indexer transforms data from complex (EAV) to flat format for better performance.

```mermaid
flowchart LR
    A[EAV Tables] --> B[Indexer Process]
    B --> C[Flat Index Tables]
    C --> D[Fast Queries]
```

---

## 2. Core Indexers

| Indexer | Purpose |
|---------|---------|
| `catalog_product_flat` | Flat products |
| `catalog_category_flat` | Flat categories |
| `catalog_product_price` | Product prices |
| `cataloginventory_stock` | Stock data |
| `catalogsearch_fulltext` | Search index |

---

## 3. Index Modes

### Update on Save (Realtime)

```bash
bin/magento indexer:set-mode realtime
```

### Update by Schedule

```bash
bin/magento indexer:set-mode schedule
```

---

## 4. Custom Indexer

### indexer.xml

```xml
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="urn:magento:framework:Indexer/etc/indexer.xsd">

    <indexer id="vendor_module_custom"
             view_id="vendor_module_custom"
             class="Vendor\Module\Model\Indexer\CustomIndexer">
        <title translate="true">Custom Indexer</title>
        <description translate="true">Indexes custom data</description>
    </indexer>
</config>
```

### mview.xml

```xml
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="urn:magento:framework:Mview/etc/mview.xsd">

    <view id="vendor_module_custom"
          class="Vendor\Module\Model\Indexer\CustomIndexer"
          group="indexer">
        <subscriptions>
            <table name="vendor_module_entity" entity_column="entity_id"/>
        </subscriptions>
    </view>
</config>
```

### Indexer Class

```php
<?php
namespace Vendor\Module\Model\Indexer;

use Magento\Framework\Indexer\ActionInterface as IndexerActionInterface;
use Magento\Framework\Mview\ActionInterface as MviewActionInterface;

class CustomIndexer implements IndexerActionInterface, MviewActionInterface
{
    public function executeFull(): void
    {
        // Full reindex
    }

    public function executeList(array $ids): void
    {
        // Partial reindex
    }

    public function executeRow($id): void
    {
        $this->executeList([$id]);
    }

    public function execute($ids): void
    {
        $this->executeList($ids);
    }
}
```

---

## 5. CLI Commands

```bash
# Show status
bin/magento indexer:status

# Reindex all
bin/magento indexer:reindex

# Reindex specific
bin/magento indexer:reindex vendor_module_custom

# Set mode
bin/magento indexer:set-mode schedule

# Reset
bin/magento indexer:reset
```

---

## 6. Best Practices

### ✅ Use Schedule Mode in Production

### ✅ Batch Processing

```php
public function reindexAll(): void
{
    $batchSize = 1000;
    // Process in batches
}
```

---

## 📌 Summary

| Component | Purpose |
|-----------|---------|
| **indexer.xml** | Define indexer |
| **mview.xml** | Define subscriptions |
| **Indexer Class** | Indexing logic |

---

## ⬅️ [Previous](./17_UI_COMPONENTS.md) | [🏠 Home](../MODULE_STRUCTURE_EN.md) | [Next ➡️](./19_CACHING.md)
