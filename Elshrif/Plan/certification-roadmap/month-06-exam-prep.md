# 📅 الشهر السادس: التحضير للامتحان

> **الهدف:** المراجعة الشاملة، Practice Tests، واجتياز الامتحان

---

## 🎯 أهداف الشهر

- [ ] مراجعة جميع المواضيع
- [ ] حل Practice Tests
- [ ] دراسة الأسئلة الشائعة
- [ ] تسجيل واجتياز الامتحان

---

## 📋 Exam Topics Checklist

### 1. Magento Architecture (18%)

- [ ] Module structure
- [ ] Request flow
- [ ] Dependency injection
- [ ] Plugin system
- [ ] Event/Observer pattern
- [ ] Areas (frontend, adminhtml, webapi_rest, graphql)

### 2. Request Flow Processing (12%)

- [ ] URL routing
- [ ] Controllers
- [ ] Result types
- [ ] Action interfaces

### 3. Customizing the Magento UI (11%)

- [ ] Layout XML
- [ ] Blocks
- [ ] Templates
- [ ] UI Components
- [ ] JavaScript/RequireJS

### 4. Working with Databases (8%)

- [ ] Models
- [ ] ResourceModels
- [ ] Collections
- [ ] db_schema.xml
- [ ] Data/Schema Patches

### 5. EAV (10%)

- [ ] EAV structure
- [ ] Entity types
- [ ] Attributes
- [ ] Attribute sets
- [ ] Creating custom attributes

### 6. Service Contracts & APIs (13%)

- [ ] Repository pattern
- [ ] Data interfaces
- [ ] webapi.xml
- [ ] REST/GraphQL
- [ ] Authentication

### 7. Catalog (10%)

- [ ] Products
- [ ] Categories
- [ ] Prices
- [ ] Inventory

### 8. Checkout (8%)

- [ ] Quote
- [ ] Shipping methods
- [ ] Payment methods
- [ ] Order processing

### 9. Security (10%)

- [ ] ACL
- [ ] CSRF protection
- [ ] XSS prevention
- [ ] Form key validation

---

## 📆 الأسبوع الأول: مراجعة Architecture

### اليوم 1-2: Module Development

**أسئلة للمراجعة:**

1. ما هي الملفات الإلزامية لإنشاء موديول؟
2. ما الفرق بين `etc/di.xml` و `etc/frontend/di.xml`؟
3. كيف تحدد dependencies بين الموديولات؟
4. ما هو الـ `sequence` في module.xml؟

**تمارين:**
- [ ] إنشاء موديول من الصفر بدون نسخ
- [ ] شرح كل ملف ووظيفته

### اليوم 3-4: Dependency Injection

**أسئلة:**
1. ما الفرق بين Preference و Plugin؟
2. متى نستخدم Virtual Types؟
3. ما الفرق بين Shared و Non-Shared objects؟
4. كيف تعمل الـ Factories؟

### اليوم 5-7: Plugins & Events

**أسئلة:**
1. ما هي أنواع الـ Plugins؟
2. ما الفرق بين Before و After و Around؟
3. كيف يتم ترتيب الـ Plugins (sortOrder)؟
4. ما القيود على الـ Plugins؟
5. متى نستخدم Observer بدلاً من Plugin؟

---

## 📆 الأسبوع الثاني: مراجعة Database & EAV

### اليوم 8-10: Models & Collections

**تمارين:**
- [ ] إنشاء Entity كامل (Model, ResourceModel, Collection, Repository)
- [ ] استخدام SearchCriteria مع Filters مختلفة
- [ ] إنشاء Data Patch لإضافة بيانات

### اليوم 11-14: EAV System

**أسئلة مهمة:**
1. ما هي الـ Entity Types الموجودة في Magento؟
2. ما الفرق بين backend_type و frontend_input؟
3. كيف تنشئ Product Attribute برمجياً؟
4. ما هي الـ Attribute Scopes (Global, Website, Store View)؟

**تمارين:**
- [ ] إنشاء Custom Customer Attribute
- [ ] إنشاء Custom Product Attribute with Options
- [ ] فهم جداول EAV في قاعدة البيانات

---

## 📆 الأسبوع الثالث: مراجعة Frontend & APIs

### اليوم 15-17: Layout & JavaScript

**أسئلة:**
1. ما الفرق بين Container و Block؟
2. كيف تضيف JavaScript لصفحة معينة؟
3. ما هي الـ Mixins ومتى نستخدمها؟
4. كيف تنشئ UI Component؟

### اليوم 18-21: REST & GraphQL

**تمارين:**
- [ ] إنشاء REST API endpoint كامل
- [ ] إنشاء GraphQL Query و Mutation
- [ ] فهم Authentication methods

---

## 📆 الأسبوع الرابع: Practice Tests & الامتحان

### اليوم 22-24: Practice Tests

**موارد:**
- [SwiftOtter Practice Questions](https://swiftotter.com/technical/certifications/magento-2-certified-professional-developer/practice-test)
- [Magento 2 Certification Sample Questions](https://learning.adobe.com/certification.html)

### اليوم 25-26: مراجعة نقاط الضعف

بعد الـ Practice Tests، ركز على المواضيع التي أخطأت فيها.

### اليوم 27-28: التسجيل للامتحان

1. اذهب إلى [Adobe Credential Management System](https://www.certmetrics.com/adobe/)
2. إنشاء حساب أو تسجيل الدخول
3. اختر الامتحان (AD0-E717 للـ Professional)
4. اختر موعد ومكان (Online أو Testing Center)
5. ادفع رسوم الامتحان (~$225)

---

## 📝 نصائح للامتحان

### قبل الامتحان

- [ ] نوم جيد الليلة السابقة
- [ ] تأكد من اتصال الإنترنت (للامتحان Online)
- [ ] جهز بيئة هادئة
- [ ] راجع الـ Cheat Sheet

### أثناء الامتحان

- [ ] اقرأ السؤال كاملاً قبل الإجابة
- [ ] انتبه للكلمات المفتاحية (NOT, EXCEPT, BEST)
- [ ] إذا لم تعرف الإجابة، علّم السؤال وارجع له لاحقاً
- [ ] لا تقضي وقتاً طويلاً على سؤال واحد
- [ ] راجع إجاباتك قبل التسليم

### درجة النجاح

- **AD0-E717 Professional:** 50 سؤال، 100 دقيقة، 69% للنجاح
- **AD0-E716 Expert:** 60 سؤال، 120 دقيقة، 64% للنجاح

---

## 📚 Quick Reference Card

### Files Structure
```
registration.php      → Module registration
etc/module.xml        → Module declaration
etc/di.xml           → Dependency injection
etc/events.xml       → Event observers
etc/routes.xml       → URL routing
etc/webapi.xml       → REST endpoints
etc/schema.graphqls  → GraphQL schema
etc/db_schema.xml    → Database tables
```

### DI Configuration
```xml
<preference for="Interface" type="Implementation"/>
<type name="Class">
    <arguments>
        <argument name="x" xsi:type="object">Class</argument>
    </arguments>
</type>
<virtualType name="VT" type="Base"/>
<plugin name="x" type="Plugin" sortOrder="10"/>
```

### Plugin Methods
```php
beforeMethodName($subject, $arg) → return [$arg];
afterMethodName($subject, $result) → return $result;
aroundMethodName($subject, $proceed, $arg) → return $proceed($arg);
```

### Observer
```xml
<event name="event_name">
    <observer name="unique_name" instance="Observer\Class"/>
</event>
```

### Layout Actions
```xml
<referenceContainer name="content">
    <block class="Block" template="template.phtml"/>
</referenceContainer>
<referenceBlock name="block" remove="true"/>
<move element="x" destination="y" before="-"/>
```

---

## 🎉 بعد النجاح

1. **شارك إنجازك** على LinkedIn
2. **أضف الشهادة** لـ CV
3. **ابدأ التحضير** للـ Expert Certification
4. **ساعد الآخرين** في رحلتهم

---

## 🏆 مبروك!

لقد أكملت خطة الـ 6 شهور. الآن أنت مستعد للامتحان!

> **تذكر:** الممارسة العملية أهم من الحفظ. اعمل على مشاريع حقيقية!

---

## ⬅️ [الشهر السابق](./month-05-advanced.md) | [🏠 الرئيسية](./README.md)
