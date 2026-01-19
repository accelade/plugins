<?php

declare(strict_types=1);

namespace Accelade\Plugins\Concerns;

/**
 * Trait for plugins that have database migrations.
 */
trait HasMigrations
{
    /**
     * Whether to load migrations.
     */
    protected bool $loadMigrations = true;

    /**
     * Enable or disable migration loading.
     */
    public function withMigrations(bool $load = true): static
    {
        $this->loadMigrations = $load;

        return $this;
    }

    /**
     * Load plugin migrations.
     */
    protected function loadPluginMigrations(): void
    {
        if ($this->loadMigrations) {
            $migrationsPath = $this->getPluginPath('database/migrations');

            if (is_dir($migrationsPath)) {
                $this->loadMigrationsFrom($migrationsPath);
            }
        }
    }

    /**
     * Publish migrations.
     */
    protected function publishMigrations(string $tag): void
    {
        if ($this->app->runningInConsole()) {
            $migrationsPath = $this->getPluginPath('database/migrations');

            if (is_dir($migrationsPath)) {
                $this->publishes([
                    $migrationsPath => database_path('migrations'),
                ], $tag);
            }
        }
    }

    /**
     * Get the plugin base path.
     */
    abstract protected function getPluginPath(string $path = ''): string;
}
