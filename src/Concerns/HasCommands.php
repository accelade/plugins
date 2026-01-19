<?php

declare(strict_types=1);

namespace Accelade\Plugins\Concerns;

/**
 * Trait for plugins that have artisan commands.
 */
trait HasCommands
{
    /**
     * Console commands to register.
     *
     * @var array<class-string>
     */
    protected array $pluginCommands = [];

    /**
     * Register commands for the plugin.
     *
     * @param  array<class-string>  $commands
     */
    public function registerPluginCommands(array $commands): static
    {
        $this->pluginCommands = array_merge($this->pluginCommands, $commands);

        return $this;
    }

    /**
     * Get registered commands.
     *
     * @return array<class-string>
     */
    public function getPluginCommands(): array
    {
        return $this->pluginCommands;
    }

    /**
     * Load commands in the boot method.
     */
    protected function loadPluginCommands(): void
    {
        if ($this->app->runningInConsole() && ! empty($this->pluginCommands)) {
            $this->commands($this->pluginCommands);
        }
    }
}
