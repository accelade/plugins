<?php

declare(strict_types=1);

namespace Accelade\Plugins;

use Accelade\Plugins\Concerns\HasAssets;
use Accelade\Plugins\Concerns\HasCommands;
use Accelade\Plugins\Concerns\HasComponents;
use Accelade\Plugins\Concerns\HasMigrations;
use Accelade\Plugins\Concerns\HasTranslations;
use Accelade\Plugins\Concerns\HasViews;
use Accelade\Plugins\Contracts\PluginInterface;
use Illuminate\Support\ServiceProvider;

/**
 * Base class for Accelade plugins.
 *
 * Extend this class to create a new plugin that integrates
 * with the Accelade ecosystem.
 */
abstract class BasePlugin extends ServiceProvider implements PluginInterface
{
    use HasAssets;
    use HasCommands;
    use HasComponents;
    use HasMigrations;
    use HasTranslations;
    use HasViews;

    /**
     * The plugin ID (must be unique).
     */
    protected static string $id;

    /**
     * The plugin name.
     */
    protected static string $name;

    /**
     * The plugin version.
     */
    protected static string $version = '1.0.0';

    /**
     * The plugin description.
     */
    protected static string $description = '';

    /**
     * The plugin author.
     */
    protected static string $author = '';

    /**
     * Plugin dependencies (other plugin IDs).
     *
     * @var array<string>
     */
    protected static array $dependencies = [];

    /**
     * Whether the plugin is enabled.
     */
    protected bool $enabled = true;

    /**
     * Get the plugin ID.
     */
    public function getId(): string
    {
        return static::$id ?? static::$name;
    }

    /**
     * Get the plugin name.
     */
    public function getName(): string
    {
        return static::$name;
    }

    /**
     * Get the plugin version.
     */
    public function getVersion(): string
    {
        return static::$version;
    }

    /**
     * Get the plugin description.
     */
    public function getDescription(): string
    {
        return static::$description;
    }

    /**
     * Get the plugin author.
     */
    public function getAuthor(): string
    {
        return static::$author;
    }

    /**
     * Get plugin dependencies.
     *
     * @return array<string>
     */
    public function getDependencies(): array
    {
        return static::$dependencies;
    }

    /**
     * Check if dependencies are satisfied.
     */
    public function dependenciesSatisfied(): bool
    {
        if (! $this->app->bound('accelade.plugins')) {
            return true;
        }

        $manager = $this->app->make('accelade.plugins');

        foreach ($this->getDependencies() as $dependency) {
            if (! $manager->has($dependency)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if the plugin is enabled.
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Enable the plugin.
     */
    public function enable(): static
    {
        $this->enabled = true;

        return $this;
    }

    /**
     * Disable the plugin.
     */
    public function disable(): static
    {
        $this->enabled = false;

        return $this;
    }

    /**
     * Create a new instance of the plugin.
     */
    public static function make(): static
    {
        return app(static::class);
    }

    /**
     * Get the default instance of the plugin.
     */
    public static function get(): static
    {
        return app('accelade.plugins')->get(static::$id ?? static::$name);
    }

    /**
     * Get plugin metadata as an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'version' => $this->getVersion(),
            'description' => $this->getDescription(),
            'author' => $this->getAuthor(),
            'dependencies' => $this->getDependencies(),
            'enabled' => $this->isEnabled(),
        ];
    }
}
