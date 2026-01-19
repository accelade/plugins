# Accelade Plugins

[![Latest Version on Packagist](https://img.shields.io/packagist/v/accelade/plugins.svg?style=flat-square)](https://packagist.org/packages/accelade/plugins)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/accelade/plugins/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/accelade/plugins/actions?query=workflow%3Atests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/accelade/plugins.svg?style=flat-square)](https://packagist.org/packages/accelade/plugins)

Complete plugin system with generator, management, and auto-discovery for the Accelade ecosystem.

## Features

- **Plugin Generator**: Create new plugin packages with a single command
- **Component Generators**: Generate models, controllers, migrations, and 15+ component types
- **Auto-Discovery**: Automatically discover and register plugins from vendor/ and packages/
- **Lifecycle Management**: Enable/disable plugins, manage dependencies
- **Full Suite**: Testing, GitHub workflows, documentation structure
- **Docs Integration**: Automatic documentation registration with Accelade docs system

## Installation

```bash
composer require accelade/plugins
```

## Quick Start

### Create a Plugin

```bash
php artisan accelade:plugin MyAwesomePlugin
```

Follow the interactive prompts to customize your plugin with:
- Plugin class (Accelade integration)
- Database migrations
- Blade views and components
- Web and API routes
- CSS (Tailwind v4) and JS (TypeScript + Vite) assets
- Language files (i18n)
- GitHub workflows and issue templates
- Documentation structure

### Generate Components

```bash
php artisan accelade:make model User --plugin=my-plugin
php artisan accelade:make controller User --plugin=my-plugin
php artisan accelade:make migration CreateUsersTable --plugin=my-plugin
```

## Available Component Types

| Type | Description |
|------|-------------|
| `model` | Eloquent model |
| `controller` | HTTP controller with CRUD methods |
| `migration` | Database migration |
| `command` | Artisan console command |
| `job` | Queueable job |
| `event` | Event class |
| `listener` | Event listener |
| `notification` | Notification class |
| `request` | Form request validation |
| `resource` | API resource |
| `middleware` | HTTP middleware |
| `policy` | Authorization policy |
| `rule` | Validation rule |
| `component` | Blade component |
| `test` | Pest test |
| `factory` | Model factory |
| `seeder` | Database seeder |
| `view` | Blade view |

## Plugin Structure

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
├── docs/
└── composer.json
```

## Configuration

Publish the configuration:

```bash
php artisan vendor:publish --tag=accelade-plugins-config
```

### Configuration Options

```php
// config/accelade-plugins.php
return [
    'discovery' => [
        'enabled' => true,  // Enable auto-discovery
        'cache' => true,    // Cache discovered plugins
    ],
    'paths' => [
        base_path('packages'),  // Paths to scan for plugins
    ],
    'defaults' => [
        'vendor' => 'accelade',
        'author' => 'Your Name',
        'email' => 'your@email.com',
        'license' => 'MIT',
    ],
];
```

## Plugin API

```php
use Accelade\Plugins\Facades\Plugins;

// Get all plugins
Plugins::all();

// Get enabled plugins
Plugins::enabled();

// Check if plugin exists
Plugins::has('my-plugin');

// Get plugin instance
$plugin = Plugins::get('my-plugin');

// Enable/disable
Plugins::enablePlugin('my-plugin');
Plugins::disablePlugin('my-plugin');

// Discover plugins manually
Plugins::discover();
```

## Documentation

The package includes comprehensive documentation:

- [Getting Started](docs/getting-started.md) - Installation and basic usage
- [Creating Plugins](docs/creating-plugins.md) - Detailed guide on creating plugins
- [Component Generators](docs/components.md) - All available component generators

### Docs Integration

Plugins automatically register their documentation with the Accelade docs system via the service provider:

```php
// In your plugin's ServiceProvider
protected function registerDocs(): void
{
    if (! $this->app->bound('accelade.docs')) {
        return;
    }

    $docs = $this->app->make('accelade.docs');

    // Register package path
    $docs->registerPackage('my-plugin', __DIR__.'/../docs');

    // Register navigation group
    $docs->registerGroup('my-plugin', 'My Plugin', 'icon-name', 50);

    // Register sections
    $docs->section('my-plugin-overview')
        ->label('Overview')
        ->markdown('overview.md')
        ->inGroup('my-plugin')
        ->register();
}
```

## Testing

```bash
# Run tests
composer test

# Run tests with coverage
composer test:coverage

# Run code formatter
composer format

# Run mago linter
composer mago

# Run static analysis
composer analyse
```

## Credits

- [Fady Mondy](https://github.com/fadymondy)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
