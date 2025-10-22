# File Orchestrator

Laravel package for safe file generation, editing, and undo via snapshots or manifests.

## Installation

```bash
composer require glugox/file-orchestrator
```

## Usage

### Take snapshot
```bash
php artisan orchestrator:snapshot
```

### Restore snapshot
```bash
php artisan orchestrator:undo 20251022_123456
```

### Inject code after imports in app.ts
```php
$fileManager->injectAfterPattern(
    'resources/js/app.ts',
    '/^import .*;/m',
    "import MyLib from './mylib';",
    'batch1'
);
```

### Undo batch
```bash
php artisan orchestrator:undo:batch batch1
```
