<?php

declare(strict_types=1);

namespace Accelade\Plugins\Contracts;

use Illuminate\Support\Collection;

/**
 * Interface for the plugin manager.
 */
interface PluginManagerInterface
{
    /**
     * Register a plugin.
     */
    public function register(PluginInterface $plugin): void;

    /**
     * Boot a plugin by ID.
     */
    public function boot(string $id): void;

    /**
     * Boot all registered plugins.
     */
    public function bootAll(): void;

    /**
     * Get a plugin by ID.
     */
    public function get(string $id): PluginInterface;

    /**
     * Check if a plugin is registered.
     */
    public function has(string $id): bool;

    /**
     * Get all registered plugins.
     *
     * @return Collection<string, PluginInterface>
     */
    public function all(): Collection;

    /**
     * Get all enabled plugins.
     *
     * @return Collection<string, PluginInterface>
     */
    public function enabled(): Collection;

    /**
     * Discover and register plugins from configured paths.
     */
    public function discover(): void;
}
