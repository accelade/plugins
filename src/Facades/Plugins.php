<?php

declare(strict_types=1);

namespace Accelade\Plugins\Facades;

use Accelade\Plugins\Contracts\PluginInterface;
use Accelade\Plugins\Support\PluginManager;
use Accelade\Plugins\Support\PluginManifest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void register(PluginInterface $plugin)
 * @method static void boot(string $id)
 * @method static void bootAll()
 * @method static PluginInterface get(string $id)
 * @method static bool has(string $id)
 * @method static Collection<string, PluginInterface> all()
 * @method static Collection<string, PluginInterface> enabled()
 * @method static Collection<string, PluginInterface> disabled()
 * @method static void discover()
 * @method static PluginManifest getManifest()
 * @method static void enablePlugin(string $id)
 * @method static void disablePlugin(string $id)
 * @method static Collection<string, PluginInterface> sorted()
 *
 * @see PluginManager
 */
class Plugins extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'accelade.plugins';
    }
}
