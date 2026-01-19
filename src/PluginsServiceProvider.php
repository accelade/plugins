<?php

declare(strict_types=1);

namespace Accelade\Plugins;

use Accelade\Plugins\Commands\MakeComponentCommand;
use Accelade\Plugins\Commands\MakePluginCommand;
use Accelade\Plugins\Contracts\PluginManagerInterface;
use Accelade\Plugins\Services\PluginFeatureFactory;
use Accelade\Plugins\Services\PluginGenerator;
use Accelade\Plugins\Services\StubProcessor;
use Accelade\Plugins\Support\PluginManager;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;

class PluginsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/accelade-plugins.php',
            'accelade-plugins'
        );

        // Register Plugin Manager
        $this->app->singleton(PluginManagerInterface::class, function ($app) {
            return new PluginManager($app);
        });

        $this->app->alias(PluginManagerInterface::class, 'accelade.plugins');

        // Register Stub Processor
        $this->app->singleton(StubProcessor::class, function ($app) {
            return new StubProcessor($app->make(Filesystem::class));
        });

        // Register Plugin Feature Factory with features from config
        $this->app->singleton(PluginFeatureFactory::class, function ($app) {
            $factory = new PluginFeatureFactory;
            $stubProcessor = $app->make(StubProcessor::class);

            // Load features from config
            $featureClasses = config('accelade-plugins.features', []);

            foreach ($featureClasses as $featureClass) {
                if (class_exists($featureClass)) {
                    $feature = new $featureClass($stubProcessor);
                    $factory->register($feature);
                }
            }

            return $factory;
        });

        // Register Plugin Generator
        $this->app->singleton(PluginGenerator::class, function ($app) {
            return new PluginGenerator(
                $app->make(Filesystem::class),
                $app->make(PluginFeatureFactory::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakePluginCommand::class,
                MakeComponentCommand::class,
            ]);

            $this->publishes([
                __DIR__.'/../config/accelade-plugins.php' => config_path('accelade-plugins.php'),
            ], 'accelade-plugins-config');
        }

        // Register documentation with Accelade docs system
        $this->registerDocs();

        // Auto-discover and register plugins
        $this->discoverPlugins();
    }

    /**
     * Register documentation.
     */
    protected function registerDocs(): void
    {
        if (! $this->app->bound('accelade.docs')) {
            return;
        }

        $docs = $this->app->make('accelade.docs');

        // Register Plugins package path
        $docs->registerPackage('plugins', __DIR__.'/../docs');

        // Register navigation group
        $docs->registerGroup('plugins', 'Plugins', 'puzzle-piece', 80);

        // Register sections
        $docs->section('plugins-overview')
            ->label('Overview')
            ->markdown('getting-started.md')
            ->inGroup('plugins')
            ->register();

        $docs->section('plugins-creating')
            ->label('Creating Plugins')
            ->markdown('creating-plugins.md')
            ->inGroup('plugins')
            ->register();

        $docs->section('plugins-components')
            ->label('Components')
            ->markdown('components.md')
            ->inGroup('plugins')
            ->register();
    }

    /**
     * Discover and register plugins.
     */
    protected function discoverPlugins(): void
    {
        if (! config('accelade-plugins.discovery.enabled', true)) {
            return;
        }

        $manager = $this->app->make(PluginManagerInterface::class);

        $manager->discover();
        $manager->bootAll();
    }
}
