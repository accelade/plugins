<?php

declare(strict_types=1);

use Accelade\Plugins\Contracts\PluginManagerInterface;
use Accelade\Plugins\Support\PluginManager;

it('can get plugin manager instance', function () {
    $manager = app(PluginManagerInterface::class);

    expect($manager)->toBeInstanceOf(PluginManager::class);
});

it('returns empty collection when no plugins registered', function () {
    $manager = app(PluginManagerInterface::class);

    expect($manager->all())->toBeEmpty();
});

it('can check if plugin exists', function () {
    $manager = app(PluginManagerInterface::class);

    expect($manager->has('non-existent'))->toBeFalse();
});
