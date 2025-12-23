# 🧪 Testing

> Complete Guide to Testing in Magento 2

---

## 📑 Table of Contents

1. [Test Types](#1-test-types)
2. [Unit Tests](#2-unit-tests)
3. [Integration Tests](#3-integration-tests)
4. [Running Tests](#4-running-tests)
5. [Best Practices](#5-best-practices)

---

## 1. Test Types

| Type | Speed | Usage |
|------|-------|-------|
| **Unit** | ⚡ Fast | Functions/Classes |
| **Integration** | 🐢 Slow | DB/Controllers |
| **MFTF** | 🐌 Very Slow | Browser UI |
| **API** | 🐢 Slow | REST/GraphQL |

---

## 2. Unit Tests

### File Structure

```
Test/Unit/Model/EntityTest.php
```

### Example

```php
<?php
namespace Vendor\Module\Test\Unit\Model;

use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{
    private Calculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new Calculator();
    }

    public function testAddition(): void
    {
        $result = $this->calculator->add(2, 3);
        $this->assertEquals(5, $result);
    }

    public function testDivisionByZeroThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->calculator->divide(10, 0);
    }
}
```

### Mocking

```php
public function testWithMock(): void
{
    $repositoryMock = $this->createMock(EntityRepositoryInterface::class);
    $repositoryMock->method('getById')->willReturn($entityMock);

    $service = new EntityService($repositoryMock);
    $result = $service->getEntity(1);
}
```

---

## 3. Integration Tests

### Example

```php
<?php
namespace Vendor\Module\Test\Integration\Model;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class EntityRepositoryTest extends TestCase
{
    /**
     * @magentoDbIsolation enabled
     */
    public function testSaveAndLoad(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $repository = $objectManager->get(EntityRepositoryInterface::class);

        $entity = $this->entityFactory->create();
        $entity->setName('Test');

        $saved = $repository->save($entity);
        $loaded = $repository->getById($saved->getId());

        $this->assertEquals('Test', $loaded->getName());
    }

    /**
     * @magentoDataFixture Vendor_Module::Test/_files/entities.php
     */
    public function testWithFixture(): void
    {
        // Test with pre-loaded data
    }
}
```

---

## 4. Running Tests

```bash
# Unit Tests
vendor/bin/phpunit -c dev/tests/unit/phpunit.xml.dist \
    app/code/Vendor/Module/Test/Unit

# Integration Tests
cd dev/tests/integration
../../../vendor/bin/phpunit -c phpunit.xml \
    ../../../app/code/Vendor/Module/Test/Integration

# MFTF
vendor/bin/mftf run:test AdminCreateEntityTest
```

---

## 5. Best Practices

### ✅ Test One Thing Per Test

```php
public function testAddition(): void
{
    $this->assertEquals(5, $this->calc->add(2, 3));
}
```

### ✅ Use Descriptive Names

```php
public function testDivisionByZeroThrowsException(): void
```

### ✅ Use Database Isolation

```php
/**
 * @magentoDbIsolation enabled
 */
```

### ✅ Use Fixtures

```php
/**
 * @magentoDataFixture Vendor_Module::Test/_files/data.php
 */
```

---

## 📌 Summary

| Type | Path | Purpose |
|------|------|---------|
| **Unit** | `Test/Unit/` | Logic tests |
| **Integration** | `Test/Integration/` | DB tests |
| **MFTF** | `Test/Mftf/` | UI tests |
| **API** | `Test/Api/` | API tests |

---

## ⬅️ [Previous](./20_ACL_SECURITY.md) | [🏠 Home](../MODULE_STRUCTURE_EN.md)
