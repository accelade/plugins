<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Plugin Discovery
    |--------------------------------------------------------------------------
    |
    | Enable or disable automatic plugin discovery. When enabled, Accelade
    | will automatically discover and register plugins from installed packages.
    |
    */
    'discovery' => [
        'enabled' => env('ACCELADE_PLUGINS_DISCOVERY_ENABLED', true),
        'cache' => env('ACCELADE_PLUGINS_CACHE_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugin Paths
    |--------------------------------------------------------------------------
    |
    | Define custom paths where Accelade should look for plugins.
    |
    */
    'paths' => [
        base_path('vendor'),
        base_path('packages'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Plugin Configuration
    |--------------------------------------------------------------------------
    |
    | Default configuration for generated plugins.
    |
    */
    'defaults' => [
        'vendor' => env('ACCELADE_PLUGINS_DEFAULT_VENDOR', 'accelade'),
        'author' => env('ACCELADE_PLUGINS_DEFAULT_AUTHOR', 'Fady Mondy'),
        'email' => env('ACCELADE_PLUGINS_DEFAULT_EMAIL', 'info@3x1.io'),
        'license' => env('ACCELADE_PLUGINS_DEFAULT_LICENSE', 'MIT'),
        'github_sponsor' => env('ACCELADE_PLUGINS_DEFAULT_GITHUB_SPONSOR', 'fadymondy'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugin Features
    |--------------------------------------------------------------------------
    |
    | This array defines all available features for plugin generation.
    | Features are loaded in priority order (lower priority = earlier).
    |
    | You can add custom features by adding them to this array.
    |
    | Priority Guide:
    | - 0-20: Core files (composer, service provider, etc.)
    | - 21-40: Structure files (migrations, routes, etc.)
    | - 41-60: Asset files (CSS, JS, etc.)
    | - 61-80: Testing files
    | - 81-100: Documentation files
    |
    */
    'features' => [
        // Core Files (0-20)
        \Accelade\Plugins\Features\ComposerJsonFeature::class,
        \Accelade\Plugins\Features\GitignoreFeature::class,
        \Accelade\Plugins\Features\ServiceProviderFeature::class,
        \Accelade\Plugins\Features\PluginClassFeature::class,
        \Accelade\Plugins\Features\InstallCommandFeature::class,
        \Accelade\Plugins\Features\ConfigFeature::class,

        // Structure Files (21-40)
        \Accelade\Plugins\Features\MigrationsFeature::class,
        \Accelade\Plugins\Features\RoutesFeature::class,
        \Accelade\Plugins\Features\ViewsFeature::class,
        \Accelade\Plugins\Features\LanguageFeature::class,
        \Accelade\Plugins\Features\ComponentsFeature::class,

        // Asset Files (41-60)
        \Accelade\Plugins\Features\CssFeature::class,
        \Accelade\Plugins\Features\JsFeature::class,
        \Accelade\Plugins\Features\ArtsFeature::class,

        // Testing Files (61-80)
        \Accelade\Plugins\Features\TestingFeature::class,
        \Accelade\Plugins\Features\PintFeature::class,

        // Documentation Files (81-100)
        \Accelade\Plugins\Features\ReadmeFeature::class,
        \Accelade\Plugins\Features\GitHubFeature::class,
        \Accelade\Plugins\Features\DocumentationFeature::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Component Types
    |--------------------------------------------------------------------------
    |
    | Available component types that can be generated for plugins.
    |
    */
    'component_types' => [
        'migration' => [
            'description' => 'Database migration file',
            'path' => 'database/migrations',
        ],
        'model' => [
            'description' => 'Eloquent model class',
            'path' => 'src/Models',
        ],
        'controller' => [
            'description' => 'HTTP controller class',
            'path' => 'src/Http/Controllers',
        ],
        'command' => [
            'description' => 'Artisan console command',
            'path' => 'src/Commands',
        ],
        'job' => [
            'description' => 'Queueable job class',
            'path' => 'src/Jobs',
        ],
        'event' => [
            'description' => 'Event class',
            'path' => 'src/Events',
        ],
        'listener' => [
            'description' => 'Event listener class',
            'path' => 'src/Listeners',
        ],
        'notification' => [
            'description' => 'Notification class',
            'path' => 'src/Notifications',
        ],
        'seeder' => [
            'description' => 'Database seeder class',
            'path' => 'database/seeders',
        ],
        'factory' => [
            'description' => 'Model factory class',
            'path' => 'database/factories',
        ],
        'test' => [
            'description' => 'Pest test file',
            'path' => 'tests/Feature',
        ],
        'lang' => [
            'description' => 'Language file',
            'path' => 'resources/lang',
        ],
        'route' => [
            'description' => 'Route file',
            'path' => 'routes',
        ],
        'view' => [
            'description' => 'Blade view file',
            'path' => 'resources/views',
        ],
        'component' => [
            'description' => 'Blade component class',
            'path' => 'src/Components',
        ],
        'middleware' => [
            'description' => 'HTTP middleware class',
            'path' => 'src/Http/Middleware',
        ],
        'request' => [
            'description' => 'Form request class',
            'path' => 'src/Http/Requests',
        ],
        'resource' => [
            'description' => 'API resource class',
            'path' => 'src/Http/Resources',
        ],
        'policy' => [
            'description' => 'Authorization policy class',
            'path' => 'src/Policies',
        ],
        'rule' => [
            'description' => 'Validation rule class',
            'path' => 'src/Rules',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Demo & Docs
    |--------------------------------------------------------------------------
    |
    | Enable or disable demo routes and documentation.
    |
    */
    'demo' => [
        'enabled' => env('ACCELADE_PLUGINS_DEMO_ENABLED', false),
    ],

    'docs' => [
        'enabled' => env('ACCELADE_PLUGINS_DOCS_ENABLED', true),
    ],
];
