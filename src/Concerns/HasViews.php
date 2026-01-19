<?php

declare(strict_types=1);

namespace Accelade\Plugins\Concerns;

/**
 * Trait for plugins that have Blade views.
 */
trait HasViews
{
    /**
     * Whether to load views.
     */
    protected bool $loadViews = true;

    /**
     * Enable or disable view loading.
     */
    public function withViews(bool $load = true): static
    {
        $this->loadViews = $load;

        return $this;
    }

    /**
     * Load plugin views.
     */
    protected function loadPluginViews(): void
    {
        if ($this->loadViews) {
            $viewsPath = $this->getPluginPath('resources/views');

            if (is_dir($viewsPath)) {
                $this->loadViewsFrom($viewsPath, $this->getId());
            }
        }
    }

    /**
     * Publish views.
     */
    protected function publishViews(string $tag): void
    {
        if ($this->app->runningInConsole()) {
            $viewsPath = $this->getPluginPath('resources/views');

            if (is_dir($viewsPath)) {
                $this->publishes([
                    $viewsPath => resource_path('views/vendor/'.$this->getId()),
                ], $tag);
            }
        }
    }

    /**
     * Get the plugin base path.
     */
    abstract protected function getPluginPath(string $path = ''): string;
}
