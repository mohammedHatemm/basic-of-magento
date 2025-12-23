# 🎨 Theme Development

> Complete Guide to Theme Development in Magento 2

---

## 📑 Table of Contents

1. [Theme Structure](#1-theme-structure)
2. [Configuration](#2-configuration)
3. [LESS & CSS](#3-less--css)
4. [Template Overrides](#4-template-overrides)
5. [Layout Overrides](#5-layout-overrides)
6. [Deployment](#6-deployment)

---

## 1. Theme Structure

```
app/design/frontend/Vendor/theme-name/
├── registration.php
├── theme.xml
├── etc/
│   └── view.xml
├── web/
│   ├── css/source/
│   │   ├── _theme.less
│   │   └── _variables.less
│   ├── images/
│   └── js/
├── Magento_Theme/
│   ├── layout/
│   └── templates/
└── Magento_Catalog/
    ├── layout/
    └── templates/
```

---

## 2. Configuration

### registration.php

```php
<?php
use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::THEME,
    'frontend/Vendor/theme-name',
    __DIR__
);
```

### theme.xml

```xml
<?xml version="1.0"?>
<theme xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
       xsi:noNamespaceSchemaLocation="urn:magento:framework:Config/etc/theme.xsd">
    <title>Vendor Custom Theme</title>
    <parent>Magento/luma</parent>
    <media>
        <preview_image>media/preview.jpg</preview_image>
    </media>
</theme>
```

---

## 3. LESS & CSS

### _variables.less

```less
@brand__primary: #ff6b35;
@brand__secondary: #2c3e50;
@font-family__base: 'Open Sans', sans-serif;
@button-primary__background: @brand__primary;
```

### _theme.less

```less
@import '_variables.less';

body {
    font-family: @font-family__base;
}

.action.primary {
    background-color: @button-primary__background;
    &:hover {
        background-color: darken(@brand__primary, 10%);
    }
}
```

---

## 4. Template Overrides

```
Original: vendor/magento/module-catalog/view/frontend/templates/product/view.phtml
Override: app/design/frontend/Vendor/theme-name/Magento_Catalog/templates/product/view.phtml
```

---

## 5. Layout Overrides

### Remove Block

```xml
<referenceBlock name="catalog.compare.sidebar" remove="true"/>
```

### Move Block

```xml
<move element="product.info.stock" destination="product.info.main" before="-"/>
```

### Add CSS/JS

```xml
<page>
    <head>
        <css src="css/custom.css"/>
        <script src="js/custom.js"/>
    </head>
</page>
```

---

## 6. Deployment

```bash
# Development
bin/magento setup:static-content:deploy -f
bin/magento cache:clean

# Production
bin/magento setup:di:compile
bin/magento setup:static-content:deploy en_US -f
bin/magento deploy:mode:set production
```

---

## 📌 Summary

| Component | Purpose |
|-----------|---------|
| **theme.xml** | Theme config |
| **view.xml** | Image settings |
| **_theme.less** | Main styles |
| **_variables.less** | Variables |
| **Layout XML** | Page structure |

---

## ⬅️ [Previous](./25_CHECKOUT.md) | [🏠 Home](../MODULE_STRUCTURE_EN.md)
