# CLAUDE.md

This file provides guidance to Claude Code when working with the Accelade Plugins package.

## Package Overview

**Accelade Plugins** is a complete plugin system for the Accelade ecosystem providing:
- Plugin generation with scaffolding
- Component generators (models, controllers, migrations, etc.)
- Auto-discovery and lifecycle management
- Testing and documentation structure

## Directory Structure

```
src/
├── BasePlugin.php              # Base class for plugins
├── PluginsServiceProvider.php  # Package service provider
├── Commands/                   # Artisan commands
│   ├── MakePluginCommand.php   # Plugin generator
│   └── MakeComponentCommand.php # Component generator
├── Concerns/                   # Plugin traits
├── Contracts/                  # Interfaces
├── Features/                   # Generation features
├── Services/                   # Core services
│   ├── StubProcessor.php       # Stub file processor
│   ├── PluginFeatureFactory.php
│   └── PluginGenerator.php
├── Stubs/                      # Stub templates
└── Support/                    # Support classes
    ├── PluginManager.php
    ├── PluginDiscovery.php
    └── PluginManifest.php
config/
├── accelade-plugins.php        # Configuration
docs/
├── getting-started.md
├── creating-plugins.md
└── components.md
tests/
├── Feature/
└── Unit/
```

## Key Commands

```bash
# Create a new plugin
php artisan accelade:plugin PluginName

# Generate a component
php artisan accelade:make {type} {name} --plugin={plugin}
```

## Architecture Patterns

### Feature Pattern

Each generation feature implements `PluginFeatureInterface`:

```php
interface PluginFeatureInterface
{
    public function getName(): string;
    public function shouldGenerate(array $config): bool;
    public function generate(array $config): void;
    public function getDirectories(array $config): array;
    public function getPriority(): int;
}
```

Priority ranges:
- 0-20: Core files (composer.json, service provider)
- 21-40: Structure files (migrations, routes)
- 41-60: Asset files (CSS, JS)
- 61-80: Testing files
- 81-100: Documentation files

### Plugin Discovery

Plugins are discovered from:
1. `vendor/composer/installed.json` - via `extra.accelade.plugin`
2. `packages/` directory - scanning composer.json files

### Stub Processing

Stubs use `{{ placeholder }}` syntax. The `StubProcessor` replaces placeholders with config values.

## Testing

```bash
composer test          # Run tests
composer format        # Format with Pint
```

## Extending

### Adding New Features

1. Create a feature class extending `AbstractFeature`
2. Add to config `accelade-plugins.features` array
3. Create stub file in `src/Stubs/`

### Adding Component Types

Add to config `accelade-plugins.component_types`:

```php
'widget' => [
    'description' => 'Dashboard widget',
    'path' => 'src/Widgets',
],
```

Then implement in `MakeComponentCommand::generateComponent()`.
