<?php

declare(strict_types=1);

namespace Accelade\Plugins\Concerns;

/**
 * Trait for plugins that have translations.
 */
trait HasTranslations
{
    /**
     * Whether to load translations.
     */
    protected bool $loadTranslations = true;

    /**
     * Enable or disable translation loading.
     */
    public function withTranslations(bool $load = true): static
    {
        $this->loadTranslations = $load;

        return $this;
    }

    /**
     * Load plugin translations.
     */
    protected function loadPluginTranslations(): void
    {
        if ($this->loadTranslations) {
            $langPath = $this->getPluginPath('resources/lang');

            if (is_dir($langPath)) {
                $this->loadTranslationsFrom($langPath, $this->getId());
                $this->loadJsonTranslationsFrom($langPath);
            }
        }
    }

    /**
     * Publish translations.
     */
    protected function publishTranslations(string $tag): void
    {
        if ($this->app->runningInConsole()) {
            $langPath = $this->getPluginPath('resources/lang');

            if (is_dir($langPath)) {
                $this->publishes([
                    $langPath => lang_path('vendor/'.$this->getId()),
                ], $tag);
            }
        }
    }

    /**
     * Get the plugin base path.
     */
    abstract protected function getPluginPath(string $path = ''): string;
}
