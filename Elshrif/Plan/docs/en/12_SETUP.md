# 🔧 Setup and Patches

> Complete Guide to Installation Scripts and Patches

---

## 📑 Table of Contents

1. [Introduction](#1-introduction)
2. [File Structure](#2-file-structure)
3. [Schema Patches](#3-schema-patches)
4. [Data Patches](#4-data-patches)
5. [db_schema.xml](#5-db_schemaxml)
6. [Best Practices](#6-best-practices)

---

## 1. Introduction

### Modern Approach (Magento 2.3+)

| Type | Purpose |
|------|---------|
| **db_schema.xml** | Declarative table structure |
| **Schema Patch** | Complex schema changes |
| **Data Patch** | Add/modify data |

---

## 2. File Structure

```
app/code/Vendor/Module/Setup/
└── Patch/
    ├── Data/
    │   └── AddDefaultData.php
    └── Schema/
        └── CreateTable.php

etc/
└── db_schema.xml
└── db_schema_whitelist.json
```

---

## 3. Schema Patches

```php
<?php
declare(strict_types=1);

namespace Vendor\Module\Setup\Patch\Schema;

use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class CreateEntityTable implements SchemaPatchInterface
{
    public function __construct(
        private ModuleDataSetupInterface $moduleDataSetup
    ) {}

    public function apply(): void
    {
        $setup = $this->moduleDataSetup;
        $setup->startSetup();

        $table = $setup->getConnection()
            ->newTable($setup->getTable('vendor_module_entity'))
            ->addColumn('entity_id', Table::TYPE_INTEGER, null, [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true
            ])
            ->addColumn('name', Table::TYPE_TEXT, 255, ['nullable' => false])
            ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, [
                'nullable' => false,
                'default' => Table::TIMESTAMP_INIT
            ]);

        $setup->getConnection()->createTable($table);
        $setup->endSetup();
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }
}
```

---

## 4. Data Patches

```php
<?php
declare(strict_types=1);

namespace Vendor\Module\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InsertDefaultData implements DataPatchInterface
{
    public function __construct(
        private ModuleDataSetupInterface $moduleDataSetup
    ) {}

    public function apply(): void
    {
        $this->moduleDataSetup->startSetup();

        $this->moduleDataSetup->getConnection()->insert(
            $this->moduleDataSetup->getTable('vendor_module_entity'),
            ['name' => 'Default Entity']
        );

        $this->moduleDataSetup->endSetup();
    }

    public static function getDependencies(): array
    {
        return [CreateEntityTable::class];
    }

    public function getAliases(): array
    {
        return [];
    }
}
```

---

## 5. db_schema.xml

```xml
<?xml version="1.0"?>
<schema xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="urn:magento:framework:Setup/Declaration/Schema/etc/schema.xsd">

    <table name="vendor_module_entity" resource="default" engine="innodb">
        <column xsi:type="int" name="entity_id" unsigned="true" nullable="false"
                identity="true"/>
        <column xsi:type="varchar" name="name" nullable="false" length="255"/>
        <column xsi:type="timestamp" name="created_at" nullable="false"
                default="CURRENT_TIMESTAMP"/>

        <constraint xsi:type="primary" referenceId="PRIMARY">
            <column name="entity_id"/>
        </constraint>

        <index referenceId="VENDOR_MODULE_ENTITY_NAME" indexType="btree">
            <column name="name"/>
        </index>
    </table>
</schema>
```

### Generate Whitelist

```bash
bin/magento setup:db-declaration:generate-whitelist --module-name=Vendor_Module
```

---

## 6. Best Practices

### ✅ Use db_schema.xml for New Tables

### ✅ Use Data Patches for Data

### ✅ Define Dependencies

```php
public static function getDependencies(): array
{
    return [CreateTablePatch::class];
}
```

---

## 📌 Summary

| Component | Path | Purpose |
|-----------|------|---------|
| **db_schema.xml** | `etc/db_schema.xml` | Table definitions |
| **Schema Patch** | `Setup/Patch/Schema/` | Complex schema changes |
| **Data Patch** | `Setup/Patch/Data/` | Add data |
| **Whitelist** | `etc/db_schema_whitelist.json` | Track changes |

---

## ⬅️ [Previous](./11_API.md) | [🏠 Home](../MODULE_STRUCTURE_EN.md) | [Next ➡️](./13_CLI.md)
