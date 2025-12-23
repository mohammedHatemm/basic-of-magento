# ⌨️ الـ CLI Commands

> الدليل الشامل لأوامر الـ Console في Magento 2

---

## 📑 الفهرس

1. [مقدمة](#1-مقدمة)
2. [هيكل الملفات](#2-هيكل-الملفات)
3. [إنشاء Command](#3-إنشاء-command)
4. [تسجيل Command](#4-تسجيل-command)
5. [Arguments](#5-arguments)
6. [Options](#6-options)
7. [Input/Output](#7-inputoutput)
8. [Progress Bar](#8-progress-bar)
9. [Tables & Formatting](#9-tables--formatting)
10. [Best Practices](#10-best-practices)
11. [مستوى متقدم](#11-مستوى-متقدم)

---

## 1. مقدمة

### ما هو CLI Command؟

أمر يُنفذ من الـ Terminal:

```bash
bin/magento vendor:module:mycommand --option=value argument
              ↑                       ↑              ↑
           Command Name           Option        Argument
```

### أوامر Magento الأساسية

```bash
bin/magento list                    # عرض كل الأوامر
bin/magento help command:name       # مساعدة لأمر معين
bin/magento cache:flush             # مسح الكاش
bin/magento setup:upgrade           # تحديث
bin/magento setup:di:compile        # Compile DI
bin/magento indexer:reindex         # إعادة الفهرسة
```

---

## 2. هيكل الملفات

```
app/code/Vendor/Module/
├── Console/
│   └── Command/
│       ├── MyCommand.php
│       └── AnotherCommand.php
└── etc/
    └── di.xml
```

---

## 3. إنشاء Command

### الكود الكامل

```php
<?php
declare(strict_types=1);

namespace Vendor\Module\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Vendor\Module\Api\EntityRepositoryInterface;
use Magento\Framework\App\State;

class ProcessEntitiesCommand extends Command
{
    private const ARGUMENT_ID = 'entity_id';
    private const OPTION_FORCE = 'force';
    private const OPTION_DRY_RUN = 'dry-run';

    /**
     * @param EntityRepositoryInterface $entityRepository
     * @param State $appState
     * @param string|null $name
     */
    public function __construct(
        private EntityRepositoryInterface $entityRepository,
        private State $appState,
        ?string $name = null
    ) {
        parent::__construct($name);
    }

    /**
     * Configure command
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('vendor:entity:process')
            ->setDescription('Process entities')
            ->setHelp('This command processes entities based on given criteria.')
            ->addArgument(
                self::ARGUMENT_ID,
                InputArgument::OPTIONAL,
                'Entity ID to process (optional, processes all if not provided)'
            )
            ->addOption(
                self::OPTION_FORCE,
                'f',
                InputOption::VALUE_NONE,
                'Force processing even if already processed'
            )
            ->addOption(
                self::OPTION_DRY_RUN,
                null,
                InputOption::VALUE_NONE,
                'Dry run mode - no actual changes'
            );

        parent::configure();
    }

    /**
     * Execute command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            // Set area code
            $this->appState->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
        } catch (\Exception $e) {
            // Area already set
        }

        $entityId = $input->getArgument(self::ARGUMENT_ID);
        $force = $input->getOption(self::OPTION_FORCE);
        $dryRun = $input->getOption(self::OPTION_DRY_RUN);

        if ($dryRun) {
            $output->writeln('<comment>DRY RUN MODE - No changes will be made</comment>');
        }

        try {
            if ($entityId) {
                $this->processEntity($entityId, $output, $force, $dryRun);
            } else {
                $this->processAllEntities($output, $force, $dryRun);
            }

            $output->writeln('<info>Processing completed successfully.</info>');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $output->writeln('<error>Error: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }

    /**
     * Process single entity
     */
    private function processEntity(int $id, OutputInterface $output, bool $force, bool $dryRun): void
    {
        $entity = $this->entityRepository->getById($id);
        $output->writeln(sprintf('Processing entity #%d: %s', $id, $entity->getName()));

        if (!$dryRun) {
            // Actual processing
        }
    }

    /**
     * Process all entities
     */
    private function processAllEntities(OutputInterface $output, bool $force, bool $dryRun): void
    {
        // Get all entities and process
        $output->writeln('Processing all entities...');
    }
}
```

---

## 4. تسجيل Command

### di.xml

```xml
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="urn:magento:framework:ObjectManager/etc/config.xsd">

    <type name="Magento\Framework\Console\CommandListInterface">
        <arguments>
            <argument name="commands" xsi:type="array">
                <item name="vendorEntityProcess"
                      xsi:type="object">Vendor\Module\Console\Command\ProcessEntitiesCommand</item>
                <item name="vendorEntityExport"
                      xsi:type="object">Vendor\Module\Console\Command\ExportCommand</item>
            </argument>
        </arguments>
    </type>
</config>
```

---

## 5. Arguments

### أنواع Arguments

| Mode | الوصف | مثال |
|------|-------|------|
| `REQUIRED` | إلزامي | `vendor:cmd <arg>` (يجب) |
| `OPTIONAL` | اختياري | `vendor:cmd [arg]` |
| `IS_ARRAY` | متعدد | `vendor:cmd arg1 arg2 arg3` |

### تعريف Arguments

```php
protected function configure(): void
{
    $this->addArgument(
        'entity_id',           // Name
        InputArgument::REQUIRED,   // Mode
        'Entity ID to process'     // Description
    );

    // Multiple arguments
    $this->addArgument(
        'ids',
        InputArgument::IS_ARRAY | InputArgument::REQUIRED,
        'Entity IDs (space-separated)'
    );
}

protected function execute(InputInterface $input, OutputInterface $output): int
{
    // Get single argument
    $id = $input->getArgument('entity_id');

    // Get array argument
    $ids = $input->getArgument('ids'); // ['1', '2', '3']
}
```

---

## 6. Options

### أنواع Options

| Mode | الوصف | مثال |
|------|-------|------|
| `VALUE_NONE` | Flag فقط | `--verbose` |
| `VALUE_REQUIRED` | قيمة إلزامية | `--format=json` |
| `VALUE_OPTIONAL` | قيمة اختيارية | `--output[=file]` |
| `VALUE_IS_ARRAY` | قيم متعددة | `--exclude=a --exclude=b` |

### تعريف Options

```php
protected function configure(): void
{
    $this->addOption(
        'format',              // Name
        'f',                   // Shortcut (optional)
        InputOption::VALUE_REQUIRED, // Mode
        'Output format (json|csv|table)', // Description
        'table'                // Default value
    );

    // Flag option
    $this->addOption(
        'verbose',
        'v',
        InputOption::VALUE_NONE,
        'Verbose output'
    );

    // Array option
    $this->addOption(
        'exclude',
        null,
        InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
        'IDs to exclude'
    );
}

protected function execute(InputInterface $input, OutputInterface $output): int
{
    $format = $input->getOption('format');      // 'json' or 'table'
    $verbose = $input->getOption('verbose');    // true or false
    $exclude = $input->getOption('exclude');    // ['1', '2']
}
```

---

## 7. Input/Output

### Output Methods

```php
// Basic output
$output->writeln('Normal text');

// Styled output
$output->writeln('<info>Success message</info>');
$output->writeln('<comment>Warning/notice</comment>');
$output->writeln('<question>Question text</question>');
$output->writeln('<error>Error message</error>');

// Custom formatting
$output->writeln('<fg=green;bg=black>Custom colors</>');
$output->writeln('<fg=white;bg=red;options=bold>Bold white on red</>');

// Multiple lines
$output->writeln([
    'Line 1',
    'Line 2',
    'Line 3'
]);

// Write without newline
$output->write('Processing... ');
$output->writeln('Done!');
```

### Input Questions

```php
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Helper\QuestionHelper;

/** @var QuestionHelper $helper */
$helper = $this->getHelper('question');

// Confirmation (yes/no)
$question = new ConfirmationQuestion('Continue? (y/n) ', false);
$continue = $helper->ask($input, $output, $question);

// Text input
$question = new Question('Enter name: ', 'default');
$name = $helper->ask($input, $output, $question);

// Password (hidden)
$question = new Question('Enter password: ');
$question->setHidden(true);
$password = $helper->ask($input, $output, $question);

// Choice
$question = new ChoiceQuestion(
    'Select format:',
    ['json', 'csv', 'table'],
    0  // Default index
);
$format = $helper->ask($input, $output, $question);
```

---

## 8. Progress Bar

```php
use Symfony\Component\Console\Helper\ProgressBar;

$items = $this->getItems();
$progressBar = new ProgressBar($output, count($items));

$progressBar->setFormat(
    ' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%'
);

$progressBar->start();

foreach ($items as $item) {
    // Process item
    $this->processItem($item);

    $progressBar->advance();
}

$progressBar->finish();
$output->writeln(''); // New line after progress bar
```

### Custom Progress Bar

```php
$progressBar->setBarCharacter('<fg=green>=</>');
$progressBar->setEmptyBarCharacter('-');
$progressBar->setProgressCharacter('>');
$progressBar->setBarWidth(50);
```

---

## 9. Tables & Formatting

### Table Output

```php
use Symfony\Component\Console\Helper\Table;

$table = new Table($output);
$table->setHeaders(['ID', 'Name', 'Status', 'Created']);

foreach ($entities as $entity) {
    $table->addRow([
        $entity->getId(),
        $entity->getName(),
        $entity->getStatus() ? '<info>Active</info>' : '<error>Inactive</error>',
        $entity->getCreatedAt()
    ]);
}

$table->render();
```

### Table Styles

```php
$table->setStyle('box');        // Box style
$table->setStyle('borderless'); // No borders
$table->setStyle('compact');    // Compact
```

### Definition List

```php
use Symfony\Component\Console\Helper\FormatterHelper;

/** @var FormatterHelper $formatter */
$formatter = $this->getHelper('formatter');

// Block of text
$block = $formatter->formatBlock(
    ['Error!', 'Something went wrong.'],
    'error',
    true
);
$output->writeln($block);

// Section
$section = $formatter->formatSection('Module', 'Processing started');
$output->writeln($section);
```

---

## 10. Best Practices

### ✅ Set Area Code

```php
protected function execute(InputInterface $input, OutputInterface $output): int
{
    try {
        $this->appState->setAreaCode(Area::AREA_ADMINHTML);
    } catch (\Exception $e) {
        // Already set
    }
}
```

### ✅ Return Proper Exit Codes

```php
return Command::SUCCESS;  // 0
return Command::FAILURE;  // 1
return Command::INVALID;  // 2
```

### ✅ Use Dependency Injection

```php
// ✅ Inject via constructor
public function __construct(
    private EntityRepository $repository,
    ?string $name = null
) {
    parent::__construct($name);
}

// ❌ Don't use ObjectManager
$objectManager = ObjectManager::getInstance();
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

## 11. مستوى متقدم

### Interactive Command

```php
protected function interact(InputInterface $input, OutputInterface $output): void
{
    if (!$input->getArgument('entity_id')) {
        $helper = $this->getHelper('question');
        $question = new Question('Enter entity ID: ');
        $id = $helper->ask($input, $output, $question);
        $input->setArgument('entity_id', $id);
    }
}
```

### Verbosity Levels

```php
// عادي
$output->writeln('Always visible');

// Verbose (-v)
$output->writeln('Verbose info', OutputInterface::VERBOSITY_VERBOSE);

// Very verbose (-vv)
$output->writeln('Debug info', OutputInterface::VERBOSITY_VERY_VERBOSE);

// Debug (-vvv)
$output->writeln('Trace info', OutputInterface::VERBOSITY_DEBUG);
```

### Command Chaining

```php
use Symfony\Component\Console\Input\ArrayInput;

protected function execute(InputInterface $input, OutputInterface $output): int
{
    // Run another command
    $command = $this->getApplication()->find('cache:flush');
    $arguments = new ArrayInput([]);
    $command->run($arguments, $output);

    return Command::SUCCESS;
}
```

### Parallel Processing

```php
use Symfony\Component\Process\Process;

$processes = [];
foreach ($batches as $batch) {
    $process = new Process(['bin/magento', 'vendor:process', '--batch=' . $batch]);
    $process->start();
    $processes[] = $process;
}

// Wait for all
foreach ($processes as $process) {
    $process->wait();
}
```

---

## 📌 ملخص

| المكون | المسار |
|--------|--------|
| **Command Class** | `Console/Command/MyCommand.php` |
| **Registration** | `etc/di.xml` |
| **Base Class** | `Symfony\Component\Console\Command\Command` |
| **Execute** | `execute(InputInterface, OutputInterface)` |

---

## ⬅️ [السابق](./12_SETUP.md) | [🏠 الرئيسية](../MODULE_STRUCTURE.md) | [التالي ➡️](./14_CRON.md)
