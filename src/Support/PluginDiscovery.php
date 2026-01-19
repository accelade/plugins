<?php

declare(strict_types=1);

namespace Accelade\Plugins\Support;

use Accelade\Plugins\Contracts\PluginInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

/**
 * Discovers plugins from configured paths.
 */
class PluginDiscovery
{
    protected Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Discover plugins from configured paths.
     *
     * @return Collection<int, PluginInterface>
     */
    public function discover(): Collection
    {
        if (! config('accelade-plugins.discovery.enabled', true)) {
            return collect();
        }

        // Check cache first
        if (config('accelade-plugins.discovery.cache', true)) {
            $cached = $this->getCachedPlugins();
            if ($cached !== null) {
                return $cached;
            }
        }

        $plugins = collect();

        // Discover from composer installed packages
        $composerPlugins = $this->discoverFromComposer();
        $plugins = $plugins->merge($composerPlugins);

        // Discover from configured paths
        $paths = config('accelade-plugins.paths', []);
        foreach ($paths as $path) {
            if (is_dir($path)) {
                $pathPlugins = $this->discoverFromPath($path);
                $plugins = $plugins->merge($pathPlugins);
            }
        }

        // Cache discovered plugins
        if (config('accelade-plugins.discovery.cache', true)) {
            $this->cachePlugins($plugins);
        }

        return $plugins;
    }

    /**
     * Discover plugins from composer installed packages.
     *
     * @return Collection<int, PluginInterface>
     */
    protected function discoverFromComposer(): Collection
    {
        $plugins = collect();
        $installedPath = base_path('vendor/composer/installed.json');

        if (! File::exists($installedPath)) {
            return $plugins;
        }

        $installed = json_decode(File::get($installedPath), true);
        $packages = $installed['packages'] ?? $installed;

        foreach ($packages as $package) {
            $extra = $package['extra'] ?? [];
            $accelade = $extra['accelade'] ?? [];

            if (isset($accelade['plugin'])) {
                $pluginClass = $accelade['plugin'];

                if (class_exists($pluginClass) && is_subclass_of($pluginClass, PluginInterface::class)) {
                    $plugins->push($this->app->make($pluginClass));
                }
            }
        }

        return $plugins;
    }

    /**
     * Discover plugins from a specific path.
     *
     * @return Collection<int, PluginInterface>
     */
    protected function discoverFromPath(string $path): Collection
    {
        $plugins = collect();

        if (! is_dir($path)) {
            return $plugins;
        }

        $directories = File::directories($path);

        foreach ($directories as $directory) {
            $plugin = $this->discoverPluginFromDirectory($directory);

            if ($plugin) {
                $plugins->push($plugin);
            }

            // Check for vendor/package structure
            $subDirectories = File::directories($directory);
            foreach ($subDirectories as $subDirectory) {
                $plugin = $this->discoverPluginFromDirectory($subDirectory);

                if ($plugin) {
                    $plugins->push($plugin);
                }
            }
        }

        return $plugins;
    }

    /**
     * Discover a plugin from a directory.
     */
    protected function discoverPluginFromDirectory(string $directory): ?PluginInterface
    {
        $composerPath = $directory.'/composer.json';

        if (! File::exists($composerPath)) {
            return null;
        }

        $composer = json_decode(File::get($composerPath), true);

        if (! $composer) {
            return null;
        }

        $extra = $composer['extra'] ?? [];
        $accelade = $extra['accelade'] ?? [];

        if (! isset($accelade['plugin'])) {
            return null;
        }

        $pluginClass = $accelade['plugin'];

        // Auto-load the plugin if needed
        $autoload = $composer['autoload']['psr-4'] ?? [];
        foreach ($autoload as $namespace => $path) {
            $fullPath = $directory.'/'.trim($path, '/');
            if (is_dir($fullPath)) {
                spl_autoload_register(function ($class) use ($namespace, $fullPath) {
                    if (str_starts_with($class, $namespace)) {
                        $relativePath = str_replace($namespace, '', $class);
                        $filePath = $fullPath.'/'.str_replace('\\', '/', $relativePath).'.php';

                        if (file_exists($filePath)) {
                            require_once $filePath;
                        }
                    }
                });
            }
        }

        if (class_exists($pluginClass) && is_subclass_of($pluginClass, PluginInterface::class)) {
            return $this->app->make($pluginClass);
        }

        return null;
    }

    /**
     * Get cached plugins.
     *
     * @return Collection<int, PluginInterface>|null
     */
    protected function getCachedPlugins(): ?Collection
    {
        $cachePath = $this->getCachePath();

        if (! File::exists($cachePath)) {
            return null;
        }

        $cached = require $cachePath;

        if (! is_array($cached)) {
            return null;
        }

        $plugins = collect();

        foreach ($cached as $pluginClass) {
            if (class_exists($pluginClass) && is_subclass_of($pluginClass, PluginInterface::class)) {
                $plugins->push($this->app->make($pluginClass));
            }
        }

        return $plugins;
    }

    /**
     * Cache discovered plugins.
     *
     * @param  Collection<int, PluginInterface>  $plugins
     */
    protected function cachePlugins(Collection $plugins): void
    {
        $cachePath = $this->getCachePath();
        $cacheDir = dirname($cachePath);

        if (! is_dir($cacheDir)) {
            File::makeDirectory($cacheDir, 0755, true);
        }

        $classes = $plugins->map(fn (PluginInterface $plugin) => get_class($plugin))->toArray();

        $content = '<?php return '.var_export($classes, true).';';

        File::put($cachePath, $content);
    }

    /**
     * Get the cache file path.
     */
    protected function getCachePath(): string
    {
        return $this->app->bootstrapPath('cache/accelade-plugins.php');
    }

    /**
     * Clear the plugin cache.
     */
    public function clearCache(): void
    {
        $cachePath = $this->getCachePath();

        if (File::exists($cachePath)) {
            File::delete($cachePath);
        }
    }
}
