<?php

declare(strict_types=1);

namespace Accelade\Plugins\Concerns;

use Illuminate\Support\Facades\Blade;

/**
 * Trait for plugins that have Blade components.
 */
trait HasComponents
{
    /**
     * Component namespace prefix.
     */
    protected ?string $componentPrefix = null;

    /**
     * Component namespace.
     */
    protected ?string $componentNamespace = null;

    /**
     * Set the component prefix for anonymous components.
     */
    public function componentPrefix(string $prefix): static
    {
        $this->componentPrefix = $prefix;

        return $this;
    }

    /**
     * Set the component namespace for class-based components.
     */
    public function componentNamespace(string $namespace): static
    {
        $this->componentNamespace = $namespace;

        return $this;
    }

    /**
     * Register Blade components.
     */
    protected function registerPluginComponents(): void
    {
        if ($this->componentPrefix) {
            Blade::anonymousComponentPath(
                $this->getPluginPath('resources/views/components'),
                $this->componentPrefix
            );
        }

        if ($this->componentNamespace) {
            Blade::componentNamespace(
                $this->componentNamespace,
                $this->componentPrefix ?? $this->getId()
            );
        }
    }

    /**
     * Get the plugin base path.
     */
    abstract protected function getPluginPath(string $path = ''): string;
}
