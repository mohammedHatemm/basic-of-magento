# 📨 Message Queues & RabbitMQ

> Complete Guide to Message Queues in Magento 2

---

## 📑 Table of Contents

1. [Introduction](#1-introduction)
2. [Configuration Files](#2-configuration-files)
3. [Publisher](#3-publisher)
4. [Consumer](#4-consumer)
5. [CLI Commands](#5-cli-commands)

---

## 1. Introduction

### What are Message Queues?

Async processing system for background tasks.

```mermaid
flowchart LR
    A[Producer] -->|Publish| B[Queue]
    B -->|Consume| C[Consumer]
```

### Use Cases

- Sending bulk emails
- Processing large imports
- Background calculations
- External integrations

---

## 2. Configuration Files

### communication.xml

```xml
<?xml version="1.0"?>
<config>
    <topic name="vendor.module.entity.process" request="Vendor\Module\Api\Data\EntityInterface">
        <handler name="entityHandler" type="Vendor\Module\Model\Queue\Handler" method="process"/>
    </topic>
</config>
```

### queue_topology.xml

```xml
<?xml version="1.0"?>
<config>
    <exchange name="vendor.module.exchange" type="topic" connection="amqp">
        <binding id="entityBinding" topic="vendor.module.entity.process"
                 destination="vendor.module.entity.queue" destinationType="queue"/>
    </exchange>
</config>
```

### queue_consumer.xml

```xml
<?xml version="1.0"?>
<config>
    <consumer name="vendor.module.entity.consumer"
              queue="vendor.module.entity.queue"
              handler="Vendor\Module\Model\Queue\Handler::process"
              connection="amqp" maxMessages="100"/>
</config>
```

---

## 3. Publisher

```php
<?php
namespace Vendor\Module\Model\Queue;

use Magento\Framework\MessageQueue\PublisherInterface;

class Publisher
{
    private const TOPIC = 'vendor.module.entity.process';

    public function __construct(
        private PublisherInterface $publisher
    ) {}

    public function publish(EntityInterface $entity): void
    {
        $this->publisher->publish(self::TOPIC, $entity);
    }
}
```

---

## 4. Consumer

```php
<?php
namespace Vendor\Module\Model\Queue;

class Handler
{
    public function process(EntityInterface $entity): void
    {
        // Process the entity
        $this->logger->info('Processing entity', ['id' => $entity->getId()]);

        // Business logic here
        $this->processor->process($entity);
    }
}
```

---

## 5. CLI Commands

```bash
# Start consumer
bin/magento queue:consumers:start vendor.module.entity.consumer

# With max messages
bin/magento queue:consumers:start vendor.module.entity.consumer --max-messages=100

# List consumers
bin/magento queue:consumers:list
```

---

## 📌 Summary

| File | Purpose |
|------|---------|
| **communication.xml** | Define topics |
| **queue_topology.xml** | Exchange & bindings |
| **queue_consumer.xml** | Consumer config |
| **Publisher** | Send messages |
| **Handler** | Process messages |

---

## ⬅️ [Previous](./22_GRAPHQL.md) | [🏠 Home](../MODULE_STRUCTURE_EN.md) | [Next ➡️](./24_PAYMENT.md)
