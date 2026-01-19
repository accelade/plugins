<?php

declare(strict_types=1);

namespace Accelade\Plugins\Services;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

/**
 * Orchestrates plugin generation using the Factory Pattern.
 *
 * This service uses the PluginFeatureFactory to manage all plugin features.
 * Features are executed in priority order with proper dependency management.
 */
class PluginGenerator
{
    public function __construct(
        protected Filesystem $files,
        protected PluginFeatureFactory $featureFactory
    ) {}

    /**
     * Create the plugin directory structure based on configuration.
     *
     * Uses the PluginFeatureFactory to collect directories from all features.
     *
     * @param  array<string, mixed>  $config
     */
    public function createDirectoryStructure(string $basePath, array $config): void
    {
        $directories = $this->featureFactory->getDirectories($config);

        foreach ($directories as $dir) {
            $this->files->makeDirectory("{$basePath}/{$dir}", 0755, true, true);
        }
    }

    /**
     * Generate all plugin files using the Factory Pattern.
     *
     * Delegates to PluginFeatureFactory which executes features in priority order.
     *
     * @param  array<string, mixed>  $config
     */
    public function generateAllFiles(array $config): void
    {
        $this->featureFactory->generateAll($config);
    }

    /**
     * Prepare configuration array for generators.
     *
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function prepareConfig(
        string $name,
        string $vendor,
        string $basePath,
        bool $generatePlugin = true,
        array $options = []
    ): array {
        $studlyName = Str::studly($name);
        $kebabName = Str::kebab($name);
        $snakeName = Str::snake($name);
        $vendorLower = Str::lower($vendor);
        $vendorStudly = Str::studly($vendorLower);
        $namespace = $vendorStudly.'\\'.$studlyName;

        return array_merge([
            'name' => $name,
            'studly_name' => $studlyName,
            'kebab_name' => $kebabName,
            'snake_name' => $snakeName,
            'vendor' => $vendor,
            'vendor_lower' => $vendorLower,
            'vendor_studly' => $vendorStudly,
            'namespace' => $namespace,
            'namespace_escaped' => str_replace('\\', '\\\\', $namespace),
            'base_path' => $basePath,
            'config_name' => 'accelade-'.$kebabName,
            'env_prefix' => 'ACCELADE_'.strtoupper($snakeName),
            'assets_tag' => 'accelade-'.$kebabName.'-assets',
            'service_provider' => $studlyName.'ServiceProvider',
            'generate_plugin' => $generatePlugin,
            'author' => config('accelade-plugins.defaults.author', 'Fady Mondy'),
            'author_email' => config('accelade-plugins.defaults.email', 'info@3x1.io'),
            'license' => config('accelade-plugins.defaults.license', 'MIT'),
        ], $options);
    }

    /**
     * Get the feature factory instance.
     */
    public function getFeatureFactory(): PluginFeatureFactory
    {
        return $this->featureFactory;
    }
}
