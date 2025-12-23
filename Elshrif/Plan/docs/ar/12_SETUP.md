# 🔧 الـ Setup والـ Patches

> الدليل الشامل للـ Installation Scripts والـ Data/Schema Patches

---

## 📑 الفهرس

1. [مقدمة](#1-مقدمة)
2. [هيكل الملفات](#2-هيكل-الملفات)
3. [Schema Patches](#3-schema-patches)
4. [Data Patches](#4-data-patches)
5. [Revertable Patches](#5-revertable-patches)
6. [Patch Dependencies](#6-patch-dependencies)
7. [db_schema.xml](#7-db_schemaxml)
8. [Declarative Schema](#8-declarative-schema)
9. [Best Practices](#9-best-practices)
10. [مستوى متقدم](#10-مستوى-متقدم)

---

## 1. مقدمة

### الطريقة الحديثة (Magento 2.3+)

| النوع | الوظيفة |
|-------|---------|
| **db_schema.xml** | تعريف هيكل الجداول (declarative) |
| **Schema Patch** | تعديلات Schema معقدة |
| **Data Patch** | إضافة/تعديل بيانات |

### الطريقة القديمة (deprecated)

```
Setup/InstallSchema.php   ← لا تستخدمها
Setup/UpgradeSchema.php   ← لا تستخدمها
Setup/InstallData.php     ← لا تستخدمها
Setup/UpgradeData.php     ← لا تستخدمها
```

---

## 2. هيكل الملفات

```
app/code/Vendor/Module/Setup/
└── Patch/
    ├── Data/
    │   ├── AddDefaultData.php
    │   └── UpdateCustomerAttribute.php
    └── Schema/
        ├── CreateCustomTable.php
        └── AddNewColumn.php

etc/
└── db_schema.xml
└── db_schema_whitelist.json
```

---

## 3. Schema Patches

### الكود الكامل

```php
<?php
declare(strict_types=1);

namespace Vendor\Module\Setup\Patch\Schema;

use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class CreateEntityTable implements SchemaPatchInterface
{
    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        private ModuleDataSetupInterface $moduleDataSetup
    ) {}

    /**
     * Apply patch
     *
     * @return void
     */
    public function apply(): void
    {
        $setup = $this->moduleDataSetup;
        $connection = $setup->getConnection();

        // Start setup
        $setup->startSetup();

        // Create table
        if (!$connection->isTableExists($setup->getTable('vendor_module_entity'))) {
            $table = $connection->newTable($setup->getTable('vendor_module_entity'))
                ->addColumn(
                    'entity_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                    ],
                    'Entity ID'
                )
                ->addColumn(
                    'name',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Name'
                )
                ->addColumn(
                    'description',
                    Table::TYPE_TEXT,
                    '64k',
                    ['nullable' => true],
                    'Description'
                )
                ->addColumn(
                    'status',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'default' => 1],
                    'Status'
                )
                ->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                    'Created At'
                )
                ->addColumn(
                    'updated_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                    'Updated At'
                )
                ->addIndex(
                    $setup->getIdxName('vendor_module_entity', ['status']),
                    ['status']
                )
                ->addIndex(
                    $setup->getIdxName('vendor_module_entity', ['name']),
                    ['name']
                )
                ->setComment('Vendor Module Entity Table');

            $connection->createTable($table);
        }

        // End setup
        $setup->endSetup();
    }

    /**
     * Get dependencies
     *
     * @return array
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * Get aliases
     *
     * @return array
     */
    public function getAliases(): array
    {
        return [];
    }
}
```

### Column Types

| Type | PHP Constant | استخدام |
|------|--------------|---------|
| INT | `TYPE_INTEGER` | أرقام صحيحة |
| SMALLINT | `TYPE_SMALLINT` | أرقام صغيرة |
| BIGINT | `TYPE_BIGINT` | أرقام كبيرة |
| DECIMAL | `TYPE_DECIMAL` | أسعار |
| VARCHAR | `TYPE_TEXT` | نص قصير |
| TEXT | `TYPE_TEXT` | نص طويل |
| BLOB | `TYPE_BLOB` | Binary |
| DATE | `TYPE_DATE` | تاريخ |
| DATETIME | `TYPE_DATETIME` | تاريخ ووقت |
| TIMESTAMP | `TYPE_TIMESTAMP` | Timestamp |
| BOOLEAN | `TYPE_BOOLEAN` | true/false |

### Column Options

```php
[
    'identity' => true,      // Auto-increment
    'unsigned' => true,      // Unsigned integer
    'nullable' => false,     // NOT NULL
    'primary' => true,       // Primary key
    'default' => 'value',    // Default value
    'length' => 255,         // For TEXT
    'precision' => 12,       // For DECIMAL
    'scale' => 4,            // For DECIMAL
]
```

---

## 4. Data Patches

### الكود الكامل

```php
<?php
declare(strict_types=1);

namespace Vendor\Module\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory;

class AddCustomerAttribute implements DataPatchInterface
{
    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CustomerSetupFactory $customerSetupFactory
     * @param SetFactory $attributeSetFactory
     */
    public function __construct(
        private ModuleDataSetupInterface $moduleDataSetup,
        private CustomerSetupFactory $customerSetupFactory,
        private SetFactory $attributeSetFactory
    ) {}

    /**
     * Apply patch
     *
     * @return void
     */
    public function apply(): void
    {
        $this->moduleDataSetup->startSetup();

        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);

        // Add customer attribute
        $customerSetup->addAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            'custom_attribute',
            [
                'type' => 'varchar',
                'label' => 'Custom Attribute',
                'input' => 'text',
                'required' => false,
                'visible' => true,
                'user_defined' => true,
                'position' => 999,
                'system' => false,
            ]
        );

        // Add attribute to form
        $attribute = $customerSetup->getEavConfig()
            ->getAttribute(\Magento\Customer\Model\Customer::ENTITY, 'custom_attribute');
        $attribute->setData('used_in_forms', [
            'adminhtml_customer',
            'customer_account_create',
            'customer_account_edit'
        ]);
        $attribute->save();

        $this->moduleDataSetup->endSetup();
    }

    /**
     * Get dependencies
     *
     * @return array
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * Get aliases
     *
     * @return array
     */
    public function getAliases(): array
    {
        return [];
    }
}
```

### Insert Data Patch

```php
<?php
namespace Vendor\Module\Setup\Patch\Data;

class InsertDefaultData implements DataPatchInterface
{
    public function __construct(
        private ModuleDataSetupInterface $moduleDataSetup
    ) {}

    public function apply(): void
    {
        $this->moduleDataSetup->startSetup();

        $connection = $this->moduleDataSetup->getConnection();
        $tableName = $this->moduleDataSetup->getTable('vendor_module_entity');

        // Insert single row
        $connection->insert($tableName, [
            'name' => 'Default Entity',
            'status' => 1
        ]);

        // Insert multiple rows
        $connection->insertMultiple($tableName, [
            ['name' => 'Entity 1', 'status' => 1],
            ['name' => 'Entity 2', 'status' => 1],
            ['name' => 'Entity 3', 'status' => 0],
        ]);

        $this->moduleDataSetup->endSetup();
    }

    public static function getDependencies(): array
    {
        return [CreateEntityTable::class]; // يعتمد على Schema patch
    }

    public function getAliases(): array
    {
        return [];
    }
}
```

---

## 5. Revertable Patches

### PatchRevertableInterface

```php
<?php
namespace Vendor\Module\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;

class RevertableData implements DataPatchInterface, PatchRevertableInterface
{
    public function apply(): void
    {
        // Add data
    }

    public function revert(): void
    {
        // Remove data - called on module:uninstall
        $connection = $this->moduleDataSetup->getConnection();
        $connection->delete(
            $this->moduleDataSetup->getTable('vendor_module_entity'),
            ['name = ?' => 'Default Entity']
        );
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

## 6. Patch Dependencies

### تحديد Dependencies

```php
public static function getDependencies(): array
{
    return [
        \Vendor\Module\Setup\Patch\Schema\CreateEntityTable::class,
        \Vendor\Module\Setup\Patch\Data\InsertDefaultConfig::class,
    ];
}
```

### Aliases (للـ renamed patches)

```php
public function getAliases(): array
{
    return [
        'Vendor\Module\Setup\Patch\Data\OldPatchName'
    ];
}
```

---

## 7. db_schema.xml

### الكود الكامل

```xml
<?xml version="1.0"?>
<schema xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="urn:magento:framework:Setup/Declaration/Schema/etc/schema.xsd">

    <table name="vendor_module_entity" resource="default" engine="innodb"
           comment="Vendor Module Entity Table">

        <!-- Columns -->
        <column xsi:type="int" name="entity_id" unsigned="true" nullable="false"
                identity="true" comment="Entity ID"/>
        <column xsi:type="varchar" name="name" nullable="false" length="255"
                comment="Name"/>
        <column xsi:type="text" name="description" nullable="true"
                comment="Description"/>
        <column xsi:type="smallint" name="status" unsigned="true" nullable="false"
                default="1" comment="Status"/>
        <column xsi:type="int" name="store_id" unsigned="true" nullable="false"
                default="0" comment="Store ID"/>
        <column xsi:type="decimal" name="price" precision="12" scale="4"
                nullable="true" comment="Price"/>
        <column xsi:type="timestamp" name="created_at" nullable="false"
                default="CURRENT_TIMESTAMP" comment="Created At"/>
        <column xsi:type="timestamp" name="updated_at" nullable="false"
                default="CURRENT_TIMESTAMP" on_update="true" comment="Updated At"/>

        <!-- Primary Key -->
        <constraint xsi:type="primary" referenceId="PRIMARY">
            <column name="entity_id"/>
        </constraint>

        <!-- Indexes -->
        <index referenceId="VENDOR_MODULE_ENTITY_STATUS" indexType="btree">
            <column name="status"/>
        </index>

        <index referenceId="VENDOR_MODULE_ENTITY_NAME" indexType="fulltext">
            <column name="name"/>
            <column name="description"/>
        </index>

        <!-- Foreign Key -->
        <constraint xsi:type="foreign" referenceId="VENDOR_MODULE_ENTITY_STORE_ID_STORE_STORE_ID"
                    table="vendor_module_entity" column="store_id"
                    referenceTable="store" referenceColumn="store_id"
                    onDelete="CASCADE"/>

        <!-- Unique -->
        <constraint xsi:type="unique" referenceId="VENDOR_MODULE_ENTITY_NAME_STORE_ID">
            <column name="name"/>
            <column name="store_id"/>
        </constraint>
    </table>
</schema>
```

### Column Types

| Type | Attributes |
|------|------------|
| `int` | unsigned, nullable, identity, default |
| `smallint` | unsigned, nullable, default |
| `bigint` | unsigned, nullable, default |
| `varchar` | length, nullable, default |
| `text` | nullable |
| `decimal` | precision, scale, nullable, default |
| `timestamp` | nullable, default, on_update |
| `boolean` | nullable, default |
| `blob` | nullable |

### Whitelist Generation

```bash
bin/magento setup:db-declaration:generate-whitelist --module-name=Vendor_Module
```

هذا يُنشئ `etc/db_schema_whitelist.json`:

```json
{
    "vendor_module_entity": {
        "column": {
            "entity_id": true,
            "name": true,
            "status": true
        },
        "index": {
            "VENDOR_MODULE_ENTITY_STATUS": true
        },
        "constraint": {
            "PRIMARY": true
        }
    }
}
```

---

## 8. Declarative Schema

### إضافة Column

```xml
<table name="vendor_module_entity">
    <!-- Column جديد -->
    <column xsi:type="varchar" name="new_field" length="100" nullable="true"
            comment="New Field"/>
</table>
```

### حذف Column

1. أزل من `db_schema.xml`
2. أضف للـ whitelist مع `false`

### تغيير Column

```xml
<!-- تغيير length -->
<column xsi:type="varchar" name="name" length="500" nullable="false"/>
```

### Drop Table

1. أزل الـ `<table>` من `db_schema.xml`
2. يُحذف تلقائياً عند `setup:upgrade`

---

## 9. Best Practices

### ✅ استخدم db_schema.xml للجداول الجديدة

```xml
<!-- ✅ Declarative -->
<table name="vendor_module_entity">
    <column .../>
</table>

<!-- ❌ لا تستخدم Schema Patch لإنشاء جداول جديدة -->
```

### ✅ استخدم Data Patches للبيانات

```php
// ✅ Data Patch
class InsertConfig implements DataPatchInterface

// ❌ لا تضع data في Schema
```

### ✅ Dependencies واضحة

```php
public static function getDependencies(): array
{
    return [
        CreateTablePatch::class,  // يجب أن يُنفذ أولاً
    ];
}
```

---

## 10. مستوى متقدم

### Recurring Patch

يُنفذ في كل `setup:upgrade`:

```php
use Magento\Framework\Setup\Patch\PatchVersionInterface;

class RecurringPatch implements DataPatchInterface, PatchVersionInterface
{
    public static function getVersion(): string
    {
        return '1.0.0';
    }

    public function apply(): void
    {
        // يُنفذ كل مرة
    }
}
```

### Check Patch Applied

```bash
bin/magento setup:db:status
```

```sql
SELECT * FROM patch_list WHERE patch_name LIKE '%Vendor%';
```

### Force Re-run Patch

```sql
DELETE FROM patch_list WHERE patch_name = 'Vendor\\Module\\Setup\\Patch\\Data\\MyPatch';
```

ثم:
```bash
bin/magento setup:upgrade
```

---

## 📌 ملخص

| المكون | المسار | الغرض |
|--------|--------|-------|
| **db_schema.xml** | `etc/db_schema.xml` | تعريف الجداول |
| **Schema Patch** | `Setup/Patch/Schema/` | تعديلات Schema معقدة |
| **Data Patch** | `Setup/Patch/Data/` | إضافة بيانات |
| **Whitelist** | `etc/db_schema_whitelist.json` | تتبع التغييرات |

---

## ⬅️ [السابق](./11_API.md) | [🏠 الرئيسية](../MODULE_STRUCTURE.md) | [التالي ➡️](./13_CLI.md)
