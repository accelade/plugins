<?php

declare(strict_types=1);

namespace Accelade\Plugins\Support;

use Accelade\Plugins\Contracts\PluginInterface;
use Illuminate\Support\Collection;

/**
 * Plugin manifest containing metadata about all registered plugins.
 */
class PluginManifest
{
    /**
     * @param  Collection<string, PluginInterface>  $plugins
     */
    public function __construct(protected Collection $plugins) {}

    /**
     * Get the manifest as an array.
     *
     * @return array<string, array<string, mixed>>
     */
    public function toArray(): array
    {
        return $this->plugins->mapWithKeys(function (PluginInterface $plugin) {
            return [
                $plugin->getId() => $plugin->toArray(),
            ];
        })->toArray();
    }

    /**
     * Get the manifest as JSON.
     */
    public function toJson(int $options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Get plugin count.
     */
    public function count(): int
    {
        return $this->plugins->count();
    }

    /**
     * Get enabled plugin count.
     */
    public function enabledCount(): int
    {
        return $this->plugins->filter(fn (PluginInterface $plugin) => $plugin->isEnabled())->count();
    }

    /**
     * Get disabled plugin count.
     */
    public function disabledCount(): int
    {
        return $this->plugins->filter(fn (PluginInterface $plugin) => ! $plugin->isEnabled())->count();
    }

    /**
     * Get plugin IDs.
     *
     * @return array<string>
     */
    public function getIds(): array
    {
        return $this->plugins->keys()->toArray();
    }

    /**
     * Get plugin versions.
     *
     * @return array<string, string>
     */
    public function getVersions(): array
    {
        return $this->plugins->mapWithKeys(function (PluginInterface $plugin) {
            return [$plugin->getId() => $plugin->getVersion()];
        })->toArray();
    }
}
