<?php

declare(strict_types=1);

namespace Accelade\Plugins\Concerns;

/**
 * Trait for plugins that have assets (CSS, JS).
 */
trait HasAssets
{
    /**
     * CSS files to include.
     *
     * @var array<string>
     */
    protected array $styles = [];

    /**
     * JavaScript files to include.
     *
     * @var array<string>
     */
    protected array $scripts = [];

    /**
     * Register styles for the plugin.
     *
     * @param  array<string>  $styles
     */
    public function registerStyles(array $styles): static
    {
        $this->styles = array_merge($this->styles, $styles);

        return $this;
    }

    /**
     * Register scripts for the plugin.
     *
     * @param  array<string>  $scripts
     */
    public function registerScripts(array $scripts): static
    {
        $this->scripts = array_merge($this->scripts, $scripts);

        return $this;
    }

    /**
     * Get registered styles.
     *
     * @return array<string>
     */
    public function getStyles(): array
    {
        return $this->styles;
    }

    /**
     * Get registered scripts.
     *
     * @return array<string>
     */
    public function getScripts(): array
    {
        return $this->scripts;
    }

    /**
     * Publish assets to public directory.
     */
    protected function publishAssets(string $source, string $tag): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                $source => public_path('vendor/'.$this->getId()),
            ], $tag);
        }
    }
}
