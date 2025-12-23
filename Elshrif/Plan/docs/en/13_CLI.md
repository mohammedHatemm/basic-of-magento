# ⌨️ CLI Commands

> Complete Guide to Console Commands in Magento 2

---

## 📑 Table of Contents

1. [Introduction](#1-introduction)
2. [File Structure](#2-file-structure)
3. [Creating a Command](#3-creating-a-command)
4. [Registering Commands](#4-registering-commands)
5. [Arguments & Options](#5-arguments--options)
6. [Input/Output](#6-inputoutput)
7. [Progress Bar](#7-progress-bar)
8. [Best Practices](#8-best-practices)

---

## 1. Introduction

### What is a CLI Command?

A command executed from the Terminal:

```bash
bin/magento vendor:module:mycommand --option=value argument
```

---

## 2. File Structure

```
app/code/Vendor/Module/
├── Console/
│   └── Command/
│       └── MyCommand.php
└── etc/
    └── di.xml
```

---

## 3. Creating a Command

```php
<?php
declare(strict_types=1);

namespace Vendor\Module\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessEntitiesCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('vendor:entity:process')
            ->setDescription('Process entities')
            ->addArgument('entity_id', InputArgument::OPTIONAL, 'Entity ID')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force processing');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $entityId = $input->getArgument('entity_id');
        $force = $input->getOption('force');

        $output->writeln('<info>Processing started...</info>');

        // Process logic here

        $output->writeln('<info>Completed!</info>');
        return Command::SUCCESS;
    }
}
```

---

## 4. Registering Commands

```xml
<!-- etc/di.xml -->
<type name="Magento\Framework\Console\CommandListInterface">
    <arguments>
        <argument name="commands" xsi:type="array">
            <item name="vendorEntityProcess" xsi:type="object">
                Vendor\Module\Console\Command\ProcessEntitiesCommand
            </item>
        </argument>
    </arguments>
</type>
```

---

## 5. Arguments & Options

### Arguments

| Mode | Description |
|------|-------------|
| `REQUIRED` | Mandatory |
| `OPTIONAL` | Optional |
| `IS_ARRAY` | Multiple values |

### Options

| Mode | Description |
|------|-------------|
| `VALUE_NONE` | Flag only |
| `VALUE_REQUIRED` | Required value |
| `VALUE_OPTIONAL` | Optional value |

---

## 6. Input/Output

### Output Methods

```php
$output->writeln('Normal text');
$output->writeln('<info>Success message</info>');
$output->writeln('<comment>Warning</comment>');
$output->writeln('<error>Error message</error>');
```

### Interactive Input

```php
use Symfony\Component\Console\Question\ConfirmationQuestion;

$helper = $this->getHelper('question');
$question = new ConfirmationQuestion('Continue? (y/n) ', false);
$continue = $helper->ask($input, $output, $question);
```

---

## 7. Progress Bar

```php
use Symfony\Component\Console\Helper\ProgressBar;

$progressBar = new ProgressBar($output, count($items));
$progressBar->start();

foreach ($items as $item) {
    // Process
    $progressBar->advance();
}

$progressBar->finish();
```

---

## 8. Best Practices

### ✅ Return Proper Exit Codes

```php
return Command::SUCCESS;  // 0
return Command::FAILURE;  // 1
```

### ✅ Handle Exceptions

```php
try {
    $this->process();
    return Command::SUCCESS;
} catch (\Exception $e) {
    $output->writeln('<error>' . $e->getMessage() . '</error>');
    return Command::FAILURE;
}
```

---

## 📌 Summary

| Component | Path |
|-----------|------|
| **Command Class** | `Console/Command/MyCommand.php` |
| **Registration** | `etc/di.xml` |
| **Base Class** | `Symfony\Component\Console\Command\Command` |

---

## ⬅️ [Previous](./12_SETUP.md) | [🏠 Home](../MODULE_STRUCTURE_EN.md) | [Next ➡️](./14_CRON.md)
