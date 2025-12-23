# 🧪 Testing

> الدليل الشامل للاختبارات في Magento 2

---

## 📑 الفهرس

1. [مقدمة](#1-مقدمة)
2. [أنواع الاختبارات](#2-أنواع-الاختبارات)
3. [Unit Tests](#3-unit-tests)
4. [Integration Tests](#4-integration-tests)
5. [Functional Tests](#5-functional-tests)
6. [API Tests](#6-api-tests)
7. [تشغيل الاختبارات](#7-تشغيل-الاختبارات)
8. [Best Practices](#8-best-practices)

---

## 1. مقدمة

### لماذا الاختبارات؟

- ✅ اكتشاف الأخطاء مبكراً
- ✅ ضمان جودة الكود
- ✅ تسهيل الـ Refactoring
- ✅ توثيق السلوك المتوقع

---

## 2. أنواع الاختبارات

| النوع | السرعة | الـ Coverage | الاستخدام |
|-------|--------|-------------|-----------|
| **Unit** | ⚡ سريع جداً | منطق واحد | Functions/Classes |
| **Integration** | 🐢 بطيء | تكامل الأجزاء | Controllers/Models |
| **Functional** | 🐌 بطيء جداً | UI كامل | Browser tests |
| **API** | 🐢 بطيء | REST/GraphQL | API endpoints |

---

## 3. Unit Tests

### هيكل الملفات

```
app/code/Vendor/Module/
└── Test/
    └── Unit/
        └── Model/
            └── EntityTest.php
```

### مثال Unit Test

```php
<?php
declare(strict_types=1);

namespace Vendor\Module\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Vendor\Module\Model\Calculator;

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

    public function testDivision(): void
    {
        $result = $this->calculator->divide(10, 2);
        $this->assertEquals(5, $result);
    }

    public function testDivisionByZeroThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->calculator->divide(10, 0);
    }
}
```

### Mocking Dependencies

```php
<?php
namespace Vendor\Module\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use Vendor\Module\Model\EntityService;
use Vendor\Module\Api\EntityRepositoryInterface;
use Psr\Log\LoggerInterface;

class EntityServiceTest extends TestCase
{
    private EntityService $service;
    private EntityRepositoryInterface|MockObject $repositoryMock;
    private LoggerInterface|MockObject $loggerMock;

    protected function setUp(): void
    {
        // Create mocks
        $this->repositoryMock = $this->createMock(EntityRepositoryInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        // Create service with mocks
        $this->service = new EntityService(
            $this->repositoryMock,
            $this->loggerMock
        );
    }

    public function testGetEntityById(): void
    {
        $entityId = 1;
        $entityMock = $this->createMock(\Vendor\Module\Api\Data\EntityInterface::class);
        $entityMock->method('getId')->willReturn($entityId);

        $this->repositoryMock
            ->expects($this->once())
            ->method('getById')
            ->with($entityId)
            ->willReturn($entityMock);

        $result = $this->service->getEntity($entityId);

        $this->assertEquals($entityId, $result->getId());
    }

    public function testGetEntityLogsError(): void
    {
        $entityId = 999;

        $this->repositoryMock
            ->method('getById')
            ->willThrowException(new \Exception('Not found'));

        $this->loggerMock
            ->expects($this->once())
            ->method('error');

        $this->expectException(\Exception::class);
        $this->service->getEntity($entityId);
    }
}
```

---

## 4. Integration Tests

### هيكل الملفات

```
app/code/Vendor/Module/
└── Test/
    └── Integration/
        └── Model/
            └── EntityRepositoryTest.php
```

### مثال Integration Test

```php
<?php
declare(strict_types=1);

namespace Vendor\Module\Test\Integration\Model;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Vendor\Module\Api\EntityRepositoryInterface;
use Vendor\Module\Api\Data\EntityInterfaceFactory;

class EntityRepositoryTest extends TestCase
{
    private EntityRepositoryInterface $repository;
    private EntityInterfaceFactory $entityFactory;

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->repository = $objectManager->get(EntityRepositoryInterface::class);
        $this->entityFactory = $objectManager->get(EntityInterfaceFactory::class);
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testSaveAndLoad(): void
    {
        // Create entity
        $entity = $this->entityFactory->create();
        $entity->setName('Test Entity');
        $entity->setStatus(1);

        // Save
        $savedEntity = $this->repository->save($entity);
        $this->assertNotNull($savedEntity->getId());

        // Load
        $loadedEntity = $this->repository->getById($savedEntity->getId());
        $this->assertEquals('Test Entity', $loadedEntity->getName());
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testDelete(): void
    {
        // Create and save
        $entity = $this->entityFactory->create();
        $entity->setName('To Delete');
        $savedEntity = $this->repository->save($entity);

        // Delete
        $result = $this->repository->deleteById($savedEntity->getId());
        $this->assertTrue($result);

        // Verify deleted
        $this->expectException(\Magento\Framework\Exception\NoSuchEntityException::class);
        $this->repository->getById($savedEntity->getId());
    }

    /**
     * @magentoDataFixture Vendor_Module::Test/Integration/_files/entities.php
     */
    public function testGetListWithFixture(): void
    {
        $searchCriteria = $this->objectManager
            ->get(\Magento\Framework\Api\SearchCriteriaBuilder::class)
            ->create();

        $result = $this->repository->getList($searchCriteria);

        $this->assertGreaterThan(0, $result->getTotalCount());
    }
}
```

### Data Fixtures

```php
<?php
// Test/Integration/_files/entities.php
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
$entityFactory = $objectManager->get(\Vendor\Module\Api\Data\EntityInterfaceFactory::class);
$repository = $objectManager->get(\Vendor\Module\Api\EntityRepositoryInterface::class);

$entity1 = $entityFactory->create();
$entity1->setName('Fixture Entity 1');
$entity1->setStatus(1);
$repository->save($entity1);

$entity2 = $entityFactory->create();
$entity2->setName('Fixture Entity 2');
$entity2->setStatus(1);
$repository->save($entity2);
```

```php
<?php
// Test/Integration/_files/entities_rollback.php
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
$registry = $objectManager->get(\Magento\Framework\Registry::class);
$registry->register('isSecureArea', true);

// Cleanup code here

$registry->unregister('isSecureArea');
```

---

## 5. Functional Tests

### MFTF (Magento Functional Testing Framework)

```xml
<!-- Test/Mftf/Test/CreateEntityTest.xml -->
<?xml version="1.0" encoding="UTF-8"?>
<tests xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
       xsi:noNamespaceSchemaLocation="urn:magento:mftf:Test/etc/testSchema.xsd">

    <test name="AdminCreateEntityTest">
        <annotations>
            <title value="Admin can create new entity"/>
            <description value="Verify admin can create entity"/>
            <severity value="CRITICAL"/>
            <testCaseId value="VENDOR-001"/>
            <group value="vendor_module"/>
        </annotations>

        <before>
            <actionGroup ref="AdminLoginActionGroup" stepKey="loginAsAdmin"/>
        </before>

        <after>
            <actionGroup ref="AdminLogoutActionGroup" stepKey="logout"/>
        </after>

        <!-- Navigate to grid -->
        <amOnPage url="{{AdminEntityGridPage.url}}" stepKey="goToGrid"/>
        <waitForPageLoad stepKey="waitForGrid"/>

        <!-- Click Add New -->
        <click selector="{{AdminEntityGridSection.addNewButton}}" stepKey="clickAddNew"/>
        <waitForPageLoad stepKey="waitForForm"/>

        <!-- Fill form -->
        <fillField selector="{{AdminEntityFormSection.name}}" userInput="Test Entity" stepKey="fillName"/>
        <selectOption selector="{{AdminEntityFormSection.status}}" userInput="1" stepKey="selectStatus"/>

        <!-- Save -->
        <click selector="{{AdminEntityFormSection.saveButton}}" stepKey="clickSave"/>
        <waitForPageLoad stepKey="waitForSave"/>

        <!-- Verify success -->
        <see selector="{{AdminMessagesSection.success}}" userInput="Entity saved" stepKey="seeSuccess"/>
    </test>
</tests>
```

---

## 6. API Tests

```php
<?php
namespace Vendor\Module\Test\Api;

use Magento\TestFramework\TestCase\WebapiAbstract;

class EntityRepositoryTest extends WebapiAbstract
{
    private const RESOURCE_PATH = '/V1/vendor-module/entities';

    /**
     * @magentoApiDataFixture Vendor_Module::Test/Integration/_files/entities.php
     */
    public function testGetList(): void
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => \Magento\Framework\Webapi\Rest\Request::HTTP_METHOD_GET,
            ],
        ];

        $response = $this->_webApiCall($serviceInfo);

        $this->assertArrayHasKey('items', $response);
        $this->assertArrayHasKey('total_count', $response);
    }

    public function testCreate(): void
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => \Magento\Framework\Webapi\Rest\Request::HTTP_METHOD_POST,
            ],
        ];

        $requestData = [
            'entity' => [
                'name' => 'API Test Entity',
                'status' => 1
            ]
        ];

        $response = $this->_webApiCall($serviceInfo, $requestData);

        $this->assertArrayHasKey('entity_id', $response);
        $this->assertEquals('API Test Entity', $response['name']);
    }
}
```

---

## 7. تشغيل الاختبارات

### Unit Tests

```bash
# كل الـ Unit tests
vendor/bin/phpunit -c dev/tests/unit/phpunit.xml.dist

# موديول معين
vendor/bin/phpunit -c dev/tests/unit/phpunit.xml.dist \
    app/code/Vendor/Module/Test/Unit
```

### Integration Tests

```bash
# إعداد قاعدة البيانات
cd dev/tests/integration
cp phpunit.xml.dist phpunit.xml
# تعديل الـ database credentials

# تشغيل
../../../vendor/bin/phpunit -c phpunit.xml \
    ../../../app/code/Vendor/Module/Test/Integration
```

### MFTF

```bash
# Build tests
vendor/bin/mftf build:project

# Run specific test
vendor/bin/mftf run:test AdminCreateEntityTest
```

---

## 8. Best Practices

### ✅ Test One Thing Per Test

```php
public function testAddition(): void
{
    // Test only one behavior
    $this->assertEquals(5, $this->calculator->add(2, 3));
}
```

### ✅ Use Descriptive Names

```php
public function testDivisionByZeroThrowsInvalidArgumentException(): void
```

### ✅ Use Fixtures for Data

```php
/**
 * @magentoDataFixture Vendor_Module::Test/_files/entities.php
 */
public function testWithFixture(): void
```

### ✅ Isolate Database

```php
/**
 * @magentoDbIsolation enabled
 */
public function testWithIsolation(): void
```

---

## 📌 ملخص

| النوع | المسار | الاستخدام |
|-------|--------|-----------|
| **Unit** | `Test/Unit/` | Logic tests |
| **Integration** | `Test/Integration/` | DB tests |
| **MFTF** | `Test/Mftf/` | UI tests |
| **API** | `Test/Api/` | API tests |

---

## ⬅️ [السابق](./20_ACL_SECURITY.md) | [🏠 الرئيسية](../MODULE_STRUCTURE.md)
