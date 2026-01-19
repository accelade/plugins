<?php

declare(strict_types=1);

namespace Accelade\Plugins\Contracts;

/**
 * Interface for Accelade plugins.
 *
 * All plugins must implement this interface to be discovered
 * and managed by the plugin system.
 */
interface PluginInterface
{
    /**
     * Get the unique plugin ID.
     */
    public function getId(): string;

    /**
     * Get the plugin name.
     */
    public function getName(): string;

    /**
     * Get the plugin version.
     */
    public function getVersion(): string;

    /**
     * Get the plugin description.
     */
    public function getDescription(): string;

    /**
     * Get the plugin author.
     */
    public function getAuthor(): string;

    /**
     * Get plugin dependencies.
     *
     * @return array<string>
     */
    public function getDependencies(): array;

    /**
     * Check if dependencies are satisfied.
     */
    public function dependenciesSatisfied(): bool;

    /**
     * Check if the plugin is enabled.
     */
    public function isEnabled(): bool;

    /**
     * Enable the plugin.
     */
    public function enable(): static;

    /**
     * Disable the plugin.
     */
    public function disable(): static;

    /**
     * Register the plugin.
     *
     * This method is called during the registration phase.
     */
    public function register(): void;

    /**
     * Boot the plugin.
     *
     * This method is called after all plugins are registered.
     */
    public function boot(): void;
}
