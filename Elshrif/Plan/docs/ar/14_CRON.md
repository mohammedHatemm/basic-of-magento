# ⏰ الـ Cron Jobs

> الدليل الشامل للمهام المجدولة في Magento 2

---

## 📑 الفهرس

1. [مقدمة](#1-مقدمة)
2. [هيكل الملفات](#2-هيكل-الملفات)
3. [crontab.xml](#3-crontabxml)
4. [Cron Job Class](#4-cron-job-class)
5. [Cron Expression](#5-cron-expression)
6. [Cron Groups](#6-cron-groups)
7. [تشغيل Cron](#7-تشغيل-cron)
8. [Debugging](#8-debugging)
9. [Best Practices](#9-best-practices)
10. [مستوى متقدم](#10-مستوى-متقدم)

---

## 1. مقدمة

### ما هو Cron Job؟

مهمة تُنفذ **تلقائياً** في أوقات محددة.

### أمثلة الاستخدام

| المهمة | التوقيت |
|--------|---------|
| إرسال emails معلقة | كل 5 دقائق |
| تنظيف logs قديمة | يومياً |
| تحديث أسعار المنتجات | كل ساعة |
| Sync مع ERP | كل 15 دقيقة |
| إنشاء Reports | أسبوعياً |

---

## 2. هيكل الملفات

```
app/code/Vendor/Module/
├── Cron/
│   ├── ProcessEntities.php
│   └── CleanupOldData.php
└── etc/
    └── crontab.xml
```

---

## 3. crontab.xml

### الكود الكامل

```xml
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="urn:magento:module:Magento_Cron:etc/crontab.xsd">

    <!-- Default group -->
    <group id="default">
        <job name="vendor_module_process_entities"
             instance="Vendor\Module\Cron\ProcessEntities"
             method="execute">
            <schedule>*/5 * * * *</schedule>
        </job>

        <job name="vendor_module_cleanup"
             instance="Vendor\Module\Cron\CleanupOldData"
             method="execute">
            <schedule>0 2 * * *</schedule>
        </job>
    </group>

    <!-- Custom group -->
    <group id="vendor_module">
        <job name="vendor_module_sync"
             instance="Vendor\Module\Cron\SyncData"
             method="execute">
            <schedule>*/15 * * * *</schedule>
        </job>
    </group>

    <!-- Config-based schedule -->
    <group id="default">
        <job name="vendor_module_configurable"
             instance="Vendor\Module\Cron\ConfigurableCron"
             method="execute">
            <config_path>vendor_module/cron/schedule</config_path>
        </job>
    </group>
</config>
```

### Job Attributes

| Attribute | الوصف | مطلوب |
|-----------|-------|-------|
| `name` | معرف فريد | ✅ |
| `instance` | الـ Class | ✅ |
| `method` | الـ Method | ✅ |

### Schedule Methods

| Element | الوصف |
|---------|-------|
| `<schedule>` | Cron expression مباشر |
| `<config_path>` | مسار config للـ schedule |

---

## 4. Cron Job Class

### الكود الكامل

```php
<?php
declare(strict_types=1);

namespace Vendor\Module\Cron;

use Psr\Log\LoggerInterface;
use Vendor\Module\Api\EntityRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;

class ProcessEntities
{
    private const CONFIG_ENABLED = 'vendor_module/cron/enabled';
    private const CONFIG_BATCH_SIZE = 'vendor_module/cron/batch_size';

    /**
     * @param LoggerInterface $logger
     * @param EntityRepositoryInterface $entityRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        private LoggerInterface $logger,
        private EntityRepositoryInterface $entityRepository,
        private SearchCriteriaBuilder $searchCriteriaBuilder,
        private ScopeConfigInterface $scopeConfig
    ) {}

    /**
     * Execute cron job
     *
     * @return void
     */
    public function execute(): void
    {
        // Check if enabled
        if (!$this->scopeConfig->isSetFlag(self::CONFIG_ENABLED)) {
            $this->logger->info('Vendor Module cron is disabled');
            return;
        }

        $this->logger->info('Starting entity processing cron');

        try {
            $batchSize = (int) $this->scopeConfig->getValue(self::CONFIG_BATCH_SIZE) ?: 100;

            // Get pending entities
            $this->searchCriteriaBuilder->addFilter('status', 'pending');
            $this->searchCriteriaBuilder->setPageSize($batchSize);
            $searchCriteria = $this->searchCriteriaBuilder->create();

            $result = $this->entityRepository->getList($searchCriteria);

            $processed = 0;
            foreach ($result->getItems() as $entity) {
                try {
                    $this->processEntity($entity);
                    $processed++;
                } catch (\Exception $e) {
                    $this->logger->error('Failed to process entity', [
                        'entity_id' => $entity->getId(),
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $this->logger->info('Entity processing completed', [
                'processed' => $processed,
                'total' => $result->getTotalCount()
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Entity processing cron failed', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Process single entity
     *
     * @param EntityInterface $entity
     * @return void
     */
    private function processEntity($entity): void
    {
        // Processing logic
        $entity->setStatus('processed');
        $this->entityRepository->save($entity);
    }
}
```

---

## 5. Cron Expression

### الصيغة

```
┌───────────── Minute (0-59)
│ ┌───────────── Hour (0-23)
│ │ ┌───────────── Day of Month (1-31)
│ │ │ ┌───────────── Month (1-12)
│ │ │ │ ┌───────────── Day of Week (0-7, 0 and 7 = Sunday)
│ │ │ │ │
* * * * *
```

### أمثلة شائعة

| Expression | الوصف |
|------------|-------|
| `* * * * *` | كل دقيقة |
| `*/5 * * * *` | كل 5 دقائق |
| `*/15 * * * *` | كل 15 دقيقة |
| `0 * * * *` | بداية كل ساعة |
| `0 */2 * * *` | كل ساعتين |
| `0 0 * * *` | منتصف الليل يومياً |
| `0 2 * * *` | الساعة 2 صباحاً يومياً |
| `0 0 * * 0` | منتصف الليل كل أحد |
| `0 0 1 * *` | أول يوم من كل شهر |
| `0 0 1 1 *` | 1 يناير (سنوياً) |
| `30 4 1,15 * *` | 4:30 صباحاً في 1 و 15 من كل شهر |

### رموز خاصة

| الرمز | المعنى |
|-------|--------|
| `*` | كل قيمة |
| `*/n` | كل n |
| `n-m` | من n إلى m |
| `n,m,o` | القيم n و m و o |

---

## 6. Cron Groups

### تعريف Group

```xml
<!-- etc/cron_groups.xml -->
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="urn:magento:module:Magento_Cron:etc/cron_groups.xsd">

    <group id="vendor_module">
        <schedule_generate_every>1</schedule_generate_every>
        <schedule_ahead_for>4</schedule_ahead_for>
        <schedule_lifetime>2</schedule_lifetime>
        <history_cleanup_every>10</history_cleanup_every>
        <history_success_lifetime>60</history_success_lifetime>
        <history_failure_lifetime>600</history_failure_lifetime>
        <use_separate_process>1</use_separate_process>
    </group>
</config>
```

### Group Settings

| Setting | الوصف | Default |
|---------|-------|---------|
| `schedule_generate_every` | توليد الجدول كل X دقيقة | 1 |
| `schedule_ahead_for` | جدولة لـ X دقيقة قادمة | 4 |
| `schedule_lifetime` | عمر الجدولة بالدقائق | 2 |
| `history_cleanup_every` | تنظيف التاريخ كل X دقيقة | 10 |
| `history_success_lifetime` | الاحتفاظ بالنجاح X دقيقة | 60 |
| `history_failure_lifetime` | الاحتفاظ بالفشل X دقيقة | 600 |
| `use_separate_process` | عملية منفصلة | 0 |

### Groups Magento الأساسية

| Group | الاستخدام |
|-------|-----------|
| `default` | معظم المهام |
| `index` | Re-indexing |
| `consumers` | Message queue |

---

## 7. تشغيل Cron

### إعداد System Cron

```bash
# فتح crontab
crontab -e

# إضافة السطر التالي
* * * * * cd /var/www/html && bin/magento cron:run >> var/log/cron.log 2>&1
```

### تشغيل يدوي

```bash
# تشغيل كل الـ cron jobs
bin/magento cron:run

# تشغيل group معين
bin/magento cron:run --group=default
bin/magento cron:run --group=vendor_module

# Bootstrap فقط (testing)
bin/magento cron:run --bootstrap=standaloneProcessStarted=1
```

### حذف Jobs معلقة

```bash
bin/magento cron:remove
```

### تثبيت Cron

```bash
bin/magento cron:install
bin/magento cron:install --force
```

---

## 8. Debugging

### جدول cron_schedule

```sql
-- عرض الجدول
SELECT * FROM cron_schedule
WHERE job_code LIKE 'vendor_%'
ORDER BY scheduled_at DESC
LIMIT 20;

-- عرض الفشل
SELECT * FROM cron_schedule
WHERE status = 'error'
ORDER BY executed_at DESC;

-- تنظيف
DELETE FROM cron_schedule WHERE status = 'error';
```

### Status Values

| Status | الوصف |
|--------|-------|
| `pending` | في الانتظار |
| `running` | قيد التنفيذ |
| `success` | نجح |
| `error` | فشل |
| `missed` | فات الموعد |

### Logging

```php
class MyCron
{
    public function __construct(
        private LoggerInterface $logger
    ) {}

    public function execute(): void
    {
        $this->logger->info('Cron started', ['job' => 'vendor_module_sync']);

        try {
            // ...
            $this->logger->info('Cron completed successfully');
        } catch (\Exception $e) {
            $this->logger->error('Cron failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
```

---

## 9. Best Practices

### ✅ Check if Enabled

```php
public function execute(): void
{
    if (!$this->scopeConfig->isSetFlag('vendor/cron/enabled')) {
        return;
    }
}
```

### ✅ Handle Exceptions

```php
public function execute(): void
{
    try {
        $this->process();
    } catch (\Exception $e) {
        $this->logger->error($e->getMessage());
        // Don't re-throw - prevents marking as 'error'
    }
}
```

### ✅ استخدم Batch Processing

```php
public function execute(): void
{
    $items = $this->getItems();
    $batches = array_chunk($items, 100);

    foreach ($batches as $batch) {
        $this->processBatch($batch);
    }
}
```

### ✅ Logging مفصل

```php
$this->logger->info('Processing', [
    'batch' => $batchNumber,
    'items' => count($batch),
    'memory' => memory_get_usage(true)
]);
```

---

## 10. مستوى متقدم

### Configurable Schedule

```xml
<!-- system.xml -->
<field id="schedule" translate="label" type="text" sortOrder="10" showInDefault="1">
    <label>Cron Schedule</label>
    <comment>Cron expression (e.g., */15 * * * *)</comment>
</field>
```

```xml
<!-- crontab.xml -->
<job name="vendor_configurable_cron" instance="..." method="execute">
    <config_path>vendor_module/cron/schedule</config_path>
</job>
```

### Lock Mechanism

```php
use Magento\Framework\Lock\LockManagerInterface;

class MyCron
{
    private const LOCK_NAME = 'vendor_module_cron_lock';
    private const LOCK_TIMEOUT = 300; // 5 minutes

    public function __construct(
        private LockManagerInterface $lockManager
    ) {}

    public function execute(): void
    {
        if (!$this->lockManager->lock(self::LOCK_NAME, self::LOCK_TIMEOUT)) {
            $this->logger->info('Cron already running, skipping');
            return;
        }

        try {
            $this->process();
        } finally {
            $this->lockManager->unlock(self::LOCK_NAME);
        }
    }
}
```

### Async Processing

```php
use Magento\Framework\MessageQueue\PublisherInterface;

class MyCron
{
    public function execute(): void
    {
        $items = $this->getItemsToProcess();

        foreach ($items as $item) {
            $this->publisher->publish('vendor.module.process', $item);
        }
    }
}
```

### Monitor Running Time

```php
public function execute(): void
{
    $startTime = microtime(true);
    $maxRunTime = 300; // 5 minutes

    foreach ($items as $item) {
        if ((microtime(true) - $startTime) > $maxRunTime) {
            $this->logger->warning('Max runtime exceeded, stopping');
            break;
        }

        $this->processItem($item);
    }
}
```

---

## 📌 ملخص

| المكون | المسار |
|--------|--------|
| **crontab.xml** | `etc/crontab.xml` |
| **Cron Class** | `Cron/MyJob.php` |
| **Groups** | `etc/cron_groups.xml` |
| **DB Table** | `cron_schedule` |

---

## ⬅️ [السابق](./13_CLI.md) | [🏠 الرئيسية](../MODULE_STRUCTURE.md) | 🏁 النهاية
