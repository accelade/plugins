<?php

declare(strict_types=1);

namespace Accelade\Plugins\Support;

use Accelade\Plugins\Contracts\PluginInterface;
use Accelade\Plugins\Contracts\PluginManagerInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Collection;
use RuntimeException;

/**
 * Manages plugin registration, discovery, and lifecycle.
 */
class PluginManager implements PluginManagerInterface
{
    protected Application $app;

    /**
     * Registered plugins.
     *
     * @var Collection<string, PluginInterface>
     */
    protected Collection $plugins;

    /**
     * Booted plugins.
     *
     * @var Collection<string>
     */
    protected Collection $booted;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->plugins = collect();
        $this->booted = collect();
    }

    /**
     * Register a plugin.
     */
    public function register(PluginInterface $plugin): void
    {
        $id = $plugin->getId();

        if ($this->plugins->has($id)) {
            throw new RuntimeException("Plugin '{$id}' is already registered.");
        }

        // Check dependencies
        if (! $plugin->dependenciesSatisfied()) {
            $dependencies = implode(', ', $plugin->getDependencies());

            throw new RuntimeException(
                "Plugin '{$id}' dependencies not satisfied: {$dependencies}"
            );
        }

        $this->plugins->put($id, $plugin);

        // Call the plugin's register method
        $plugin->register();
    }

    /**
     * Boot a plugin by ID.
     */
    public function boot(string $id): void
    {
        if ($this->booted->contains($id)) {
            return;
        }

        $plugin = $this->get($id);

        if (! $plugin->isEnabled()) {
            return;
        }

        // Boot dependencies first
        foreach ($plugin->getDependencies() as $dependency) {
            $this->boot($dependency);
        }

        // Call the plugin's boot method
        $plugin->boot();

        $this->booted->push($id);
    }

    /**
     * Boot all registered plugins.
     */
    public function bootAll(): void
    {
        foreach ($this->plugins->keys() as $id) {
            $this->boot($id);
        }
    }

    /**
     * Get a plugin by ID.
     */
    public function get(string $id): PluginInterface
    {
        if (! $this->has($id)) {
            throw new RuntimeException("Plugin '{$id}' is not registered.");
        }

        return $this->plugins->get($id);
    }

    /**
     * Check if a plugin is registered.
     */
    public function has(string $id): bool
    {
        return $this->plugins->has($id);
    }

    /**
     * Get all registered plugins.
     *
     * @return Collection<string, PluginInterface>
     */
    public function all(): Collection
    {
        return $this->plugins;
    }

    /**
     * Get all enabled plugins.
     *
     * @return Collection<string, PluginInterface>
     */
    public function enabled(): Collection
    {
        return $this->plugins->filter(fn (PluginInterface $plugin) => $plugin->isEnabled());
    }

    /**
     * Get all disabled plugins.
     *
     * @return Collection<string, PluginInterface>
     */
    public function disabled(): Collection
    {
        return $this->plugins->filter(fn (PluginInterface $plugin) => ! $plugin->isEnabled());
    }

    /**
     * Discover and register plugins from configured paths.
     */
    public function discover(): void
    {
        $discovery = new PluginDiscovery($this->app);
        $plugins = $discovery->discover();

        foreach ($plugins as $plugin) {
            try {
                $this->register($plugin);
            } catch (RuntimeException $e) {
                // Log the error but continue with other plugins
                if ($this->app->bound('log')) {
                    $this->app->make('log')->warning($e->getMessage());
                }
            }
        }
    }

    /**
     * Get plugin manifest.
     */
    public function getManifest(): PluginManifest
    {
        return new PluginManifest($this->plugins);
    }

    /**
     * Enable a plugin by ID.
     */
    public function enablePlugin(string $id): void
    {
        $this->get($id)->enable();
    }

    /**
     * Disable a plugin by ID.
     */
    public function disablePlugin(string $id): void
    {
        $this->get($id)->disable();
    }

    /**
     * Get plugins sorted by priority (based on dependencies).
     *
     * @return Collection<string, PluginInterface>
     */
    public function sorted(): Collection
    {
        $sorted = collect();
        $visited = collect();

        $visit = function (PluginInterface $plugin) use (&$sorted, &$visited, &$visit) {
            $id = $plugin->getId();

            if ($visited->contains($id)) {
                return;
            }

            $visited->push($id);

            foreach ($plugin->getDependencies() as $dependency) {
                if ($this->has($dependency)) {
                    $visit($this->get($dependency));
                }
            }

            $sorted->put($id, $plugin);
        };

        foreach ($this->plugins as $plugin) {
            $visit($plugin);
        }

        return $sorted;
    }
}
