# Creating Plugins

## Using the Generator

The quickest way to create a new plugin is with the generator command:

```bash
php artisan accelade:plugin MyAwesomePlugin
```

### Options

- `--vendor`: Specify the vendor name (default: from config)
- `--path`: Custom base path for the plugin
- `--no-plugin`: Skip generating the plugin class
- `--no-interaction`: Run without prompts (uses defaults)

### Interactive Features

When running interactively, you can select:
- **Plugin class**: Accelade plugin integration
- **Migrations**: Database migrations structure
- **Views**: Blade views and components
- **Routes**: Web and/or API routes
- **Assets**: CSS (Tailwind v4) and JS (TypeScript + Vite)
- **Languages**: i18n translation files
- **GitHub**: Workflows, issue templates, funding
- **Documentation**: Getting started docs and CLAUDE.md

## Manual Plugin Creation

### 1. Create the Directory Structure

```
my-plugin/
├── src/
│   ├── MyPluginPlugin.php
│   ├── MyPluginServiceProvider.php
│   └── Commands/
├── config/
├── resources/
│   ├── views/
│   └── lang/
├── routes/
├── tests/
└── composer.json
```

### 2. Create the Service Provider

```php
<?php

namespace Accelade\MyPlugin;

use Illuminate\Support\ServiceProvider;

class MyPluginServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/my-plugin.php', 'my-plugin');
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'my-plugin');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'my-plugin');
    }
}
```

### 3. Create the Plugin Class

```php
<?php

namespace Accelade\MyPlugin;

use Accelade\Plugins\BasePlugin;

class MyPluginPlugin extends BasePlugin
{
    protected static string $id = 'my-plugin';
    protected static string $name = 'My Plugin';
    protected static string $version = '1.0.0';
    protected static string $description = 'My awesome plugin';

    public function register(): void
    {
        // Register services, bindings
    }

    public function boot(): void
    {
        $this->loadPluginViews();
        $this->loadPluginTranslations();
    }

    protected function getPluginPath(string $path = ''): string
    {
        return dirname(__DIR__) . ($path ? "/{$path}" : '');
    }
}
```

### 4. Register in composer.json

```json
{
    "extra": {
        "laravel": {
            "providers": [
                "Accelade\\MyPlugin\\MyPluginServiceProvider"
            ]
        },
        "accelade": {
            "plugin": "Accelade\\MyPlugin\\MyPluginPlugin"
        }
    }
}
```

## Plugin Dependencies

Define dependencies on other plugins:

```php
protected static array $dependencies = [
    'accelade-forms',
    'accelade-tables',
];
```

The plugin manager will ensure dependencies are loaded first.

## Enabling/Disabling Plugins

```php
use Accelade\Plugins\Facades\Plugins;

// Disable a plugin
Plugins::disablePlugin('my-plugin');

// Enable a plugin
Plugins::enablePlugin('my-plugin');

// Check status
Plugins::get('my-plugin')->isEnabled();
```
