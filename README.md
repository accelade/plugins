# Accelade Plugins

[![Latest Version on Packagist](https://img.shields.io/packagist/v/accelade/plugins.svg?style=flat-square)](https://packagist.org/packages/accelade/plugins)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/accelade/plugins/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/accelade/plugins/actions?query=workflow%3Atests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/accelade/plugins.svg?style=flat-square)](https://packagist.org/packages/accelade/plugins)

Complete plugin system with generator, management, and auto-discovery for the Accelade ecosystem.

## Features

- **Plugin Generator**: Create new plugin packages with a single command
- **Component Generators**: Generate models, controllers, migrations, and 15+ component types
- **Auto-Discovery**: Automatically discover and register plugins
- **Lifecycle Management**: Enable/disable plugins, manage dependencies
- **Full Suite**: Testing, GitHub workflows, documentation structure

## Installation

\`\`\`bash
composer require accelade/plugins
\`\`\`

## Quick Start

### Create a Plugin

\`\`\`bash
php artisan accelade:plugin MyAwesomePlugin
\`\`\`

Follow the interactive prompts to customize your plugin.

### Generate Components

\`\`\`bash
php artisan accelade:make model User --plugin=my-plugin
php artisan accelade:make controller User --plugin=my-plugin
php artisan accelade:make migration CreateUsersTable --plugin=my-plugin
\`\`\`

## Plugin Structure

\`\`\`
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
\`\`\`

## Configuration

Publish the configuration:

\`\`\`bash
php artisan vendor:publish --tag=accelade-plugins-config
\`\`\`

## Plugin API

\`\`\`php
use Accelade\Plugins\Facades\Plugins;

// Get all plugins
Plugins::all();

// Get enabled plugins
Plugins::enabled();

// Check if plugin exists
Plugins::has('my-plugin');

// Get plugin instance
\$plugin = Plugins::get('my-plugin');

// Enable/disable
Plugins::enablePlugin('my-plugin');
Plugins::disablePlugin('my-plugin');
\`\`\`

## Testing

\`\`\`bash
composer test
\`\`\`

## Credits

- [Fady Mondy](https://github.com/fadymondy)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
