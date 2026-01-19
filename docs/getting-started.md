# Plugins Overview

The Accelade Plugins package provides a complete plugin system with:

- **Plugin Generator**: Create new plugin packages with a single command
- **Plugin Management**: Auto-discovery, registration, and lifecycle management
- **Component Generators**: Generate models, controllers, migrations, and more

## Installation

```bash
composer require accelade/plugins
```

## Quick Start

### Creating a New Plugin

```bash
php artisan accelade:plugin MyPlugin
```

This interactive command will guide you through creating a new plugin with:
- Service Provider
- Plugin class
- Configuration file
- Optional: migrations, views, routes, assets, tests, and more

### Generating Components

```bash
php artisan accelade:make model User --plugin=my-plugin
php artisan accelade:make controller User --plugin=my-plugin
php artisan accelade:make migration CreateUsersTable --plugin=my-plugin
```

## Plugin Architecture

Every plugin extends `BasePlugin` and implements the `PluginInterface`:

```php
use Accelade\Plugins\BasePlugin;

class MyPlugin extends BasePlugin
{
    protected static string $id = 'my-plugin';
    protected static string $name = 'My Plugin';
    protected static string $version = '1.0.0';

    public function register(): void
    {
        // Register services
    }

    public function boot(): void
    {
        // Boot plugin
    }
}
```

## Auto-Discovery

Plugins are automatically discovered from:
- `vendor/` directory (via composer.json extra.accelade.plugin)
- `packages/` directory

To disable auto-discovery:

```env
ACCELADE_PLUGINS_DISCOVERY_ENABLED=false
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=accelade-plugins-config
```
