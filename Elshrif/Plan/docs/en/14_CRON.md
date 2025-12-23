# ⏰ Cron Jobs

> Complete Guide to Scheduled Tasks in Magento 2

---

## 📑 Table of Contents

1. [Introduction](#1-introduction)
2. [File Structure](#2-file-structure)
3. [crontab.xml](#3-crontabxml)
4. [Cron Job Class](#4-cron-job-class)
5. [Cron Expression](#5-cron-expression)
6. [Cron Groups](#6-cron-groups)
7. [Running Cron](#7-running-cron)
8. [Debugging](#8-debugging)
9. [Best Practices](#9-best-practices)

---

## 1. Introduction

### What is a Cron Job?

A task that runs **automatically** at scheduled times.

### Use Cases

| Task | Timing |
|------|--------|
| Send pending emails | Every 5 minutes |
| Clean old logs | Daily |
| Update product prices | Hourly |
| Sync with ERP | Every 15 minutes |

---

## 2. File Structure

```
app/code/Vendor/Module/
├── Cron/
│   └── ProcessEntities.php
└── etc/
    └── crontab.xml
```

---

## 3. crontab.xml

```xml
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="urn:magento:module:Magento_Cron:etc/crontab.xsd">

    <group id="default">
        <job name="vendor_module_process_entities"
             instance="Vendor\Module\Cron\ProcessEntities"
             method="execute">
            <schedule>*/5 * * * *</schedule>
        </job>
    </group>
</config>
```

---

## 4. Cron Job Class

```php
<?php
declare(strict_types=1);

namespace Vendor\Module\Cron;

use Psr\Log\LoggerInterface;

class ProcessEntities
{
    public function __construct(
        private LoggerInterface $logger
    ) {}

    public function execute(): void
    {
        $this->logger->info('Cron started');

        try {
            // Processing logic
            $this->logger->info('Cron completed');
        } catch (\Exception $e) {
            $this->logger->error('Cron failed: ' . $e->getMessage());
        }
    }
}
```

---

## 5. Cron Expression

### Syntax

```
┌───────────── Minute (0-59)
│ ┌───────────── Hour (0-23)
│ │ ┌───────────── Day of Month (1-31)
│ │ │ ┌───────────── Month (1-12)
│ │ │ │ ┌───────────── Day of Week (0-7)
│ │ │ │ │
* * * * *
```

### Common Examples

| Expression | Description |
|------------|-------------|
| `* * * * *` | Every minute |
| `*/5 * * * *` | Every 5 minutes |
| `0 * * * *` | Every hour |
| `0 0 * * *` | Daily at midnight |
| `0 2 * * *` | Daily at 2:00 AM |
| `0 0 * * 0` | Weekly on Sunday |
| `0 0 1 * *` | Monthly on 1st |

---

## 6. Cron Groups

```xml
<!-- etc/cron_groups.xml -->
<config>
    <group id="vendor_module">
        <schedule_generate_every>1</schedule_generate_every>
        <schedule_ahead_for>4</schedule_ahead_for>
        <history_success_lifetime>60</history_success_lifetime>
        <history_failure_lifetime>600</history_failure_lifetime>
        <use_separate_process>1</use_separate_process>
    </group>
</config>
```

---

## 7. Running Cron

### Setup System Cron

```bash
crontab -e

# Add:
* * * * * cd /var/www/html && bin/magento cron:run >> var/log/cron.log 2>&1
```

### Manual Run

```bash
bin/magento cron:run
bin/magento cron:run --group=default
```

---

## 8. Debugging

### Check cron_schedule Table

```sql
SELECT * FROM cron_schedule
WHERE job_code LIKE 'vendor_%'
ORDER BY scheduled_at DESC;
```

### Status Values

| Status | Description |
|--------|-------------|
| `pending` | Waiting |
| `running` | Executing |
| `success` | Completed |
| `error` | Failed |
| `missed` | Missed schedule |

---

## 9. Best Practices

### ✅ Check if Enabled

```php
if (!$this->scopeConfig->isSetFlag('vendor/cron/enabled')) {
    return;
}
```

### ✅ Handle Exceptions

```php
try {
    $this->process();
} catch (\Exception $e) {
    $this->logger->error($e->getMessage());
}
```

### ✅ Use Batch Processing

```php
$batches = array_chunk($items, 100);
foreach ($batches as $batch) {
    $this->processBatch($batch);
}
```

---

## 📌 Summary

| Component | Path |
|-----------|------|
| **crontab.xml** | `etc/crontab.xml` |
| **Cron Class** | `Cron/MyJob.php` |
| **Groups** | `etc/cron_groups.xml` |
| **DB Table** | `cron_schedule` |

---

## ⬅️ [Previous](./13_CLI.md) | [🏠 Home](../MODULE_STRUCTURE_EN.md) | 🏁 End
