# 🗄️ Caching

> Complete Guide to Caching in Magento 2

---

## 📑 Table of Contents

1. [Introduction](#1-introduction)
2. [Cache Types](#2-cache-types)
3. [Cache Backends](#3-cache-backends)
4. [Full Page Cache](#4-full-page-cache)
5. [Caching in Code](#5-caching-in-code)
6. [CLI Commands](#6-cli-commands)
7. [Best Practices](#7-best-practices)

---

## 1. Introduction

### Why Caching?

Caching stores computed data to avoid repeated calculations.

---

## 2. Cache Types

| Type | Description |
|------|-------------|
| `config` | Configuration |
| `layout` | Layout files |
| `block_html` | Block output |
| `full_page` | Full Page Cache |
| `collections` | Collections data |
| `eav` | EAV attributes |
| `translate` | Translations |

---

## 3. Cache Backends

### Redis (Recommended)

```php
// env.php
'cache' => [
    'frontend' => [
        'default' => [
            'backend' => 'Magento\Framework\Cache\Backend\Redis',
            'backend_options' => [
                'server' => '127.0.0.1',
                'port' => '6379',
                'database' => '0'
            ]
        ]
    ]
]
```

---

## 4. Full Page Cache

### Non-cacheable Blocks

```xml
<block class="Vendor\Module\Block\Dynamic" cacheable="false"/>
```

### Private Content (sections.xml)

```xml
<config>
    <action name="vendor_module/cart/add">
        <section name="cart"/>
    </action>
</config>
```

---

## 5. Caching in Code

```php
<?php
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Serialize\SerializerInterface;

class DataProvider
{
    private const CACHE_KEY = 'MY_KEY_';
    private const CACHE_LIFETIME = 3600;

    public function __construct(
        private CacheInterface $cache,
        private SerializerInterface $serializer
    ) {}

    public function getData(int $id): array
    {
        $cacheKey = self::CACHE_KEY . $id;
        $cached = $this->cache->load($cacheKey);

        if ($cached) {
            return $this->serializer->unserialize($cached);
        }

        $data = $this->loadFromDatabase($id);

        $this->cache->save(
            $this->serializer->serialize($data),
            $cacheKey,
            ['my_cache_tag'],
            self::CACHE_LIFETIME
        );

        return $data;
    }
}
```

### Block Caching

```php
class MyBlock extends Template implements IdentityInterface
{
    public function getIdentities(): array
    {
        return ['my_entity_' . $this->getId()];
    }

    protected function getCacheLifetime(): int
    {
        return 3600;
    }
}
```

---

## 6. CLI Commands

```bash
# Show status
bin/magento cache:status

# Enable
bin/magento cache:enable

# Disable
bin/magento cache:disable full_page

# Clean
bin/magento cache:clean

# Flush all
bin/magento cache:flush
```

---

## 7. Best Practices

### ✅ Use Redis in Production

### ✅ Use Cache Tags

```php
$this->cache->save($data, $key, ['specific_tag']);
```

### ✅ Implement IdentityInterface

```php
class MyBlock implements IdentityInterface
{
    public function getIdentities(): array
    {
        return ['entity_' . $this->getId()];
    }
}
```

---

## 📌 Summary

| Component | Purpose |
|-----------|---------|
| **Cache Types** | Different data types |
| **Backend** | File/Redis/Varnish |
| **FPC** | Full page caching |
| **Tags** | Cache invalidation |

---

## ⬅️ [Previous](./18_INDEXERS.md) | [🏠 Home](../MODULE_STRUCTURE_EN.md) | [Next ➡️](./20_ACL_SECURITY.md)
