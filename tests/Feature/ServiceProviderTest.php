<?php

declare(strict_types=1);

use Accelade\Plugins\Contracts\PluginManagerInterface;
use Accelade\Plugins\Services\PluginFeatureFactory;
use Accelade\Plugins\Services\PluginGenerator;
use Accelade\Plugins\Services\StubProcessor;

it('registers the plugin manager', function () {
    expect(app()->bound(PluginManagerInterface::class))->toBeTrue();
    expect(app()->bound('accelade.plugins'))->toBeTrue();
});

it('registers the stub processor', function () {
    expect(app()->bound(StubProcessor::class))->toBeTrue();
});

it('registers the plugin feature factory', function () {
    expect(app()->bound(PluginFeatureFactory::class))->toBeTrue();
});

it('registers the plugin generator', function () {
    expect(app()->bound(PluginGenerator::class))->toBeTrue();
});

it('loads the config file', function () {
    expect(config('accelade-plugins'))->toBeArray();
    expect(config('accelade-plugins.discovery.enabled'))->toBeBool();
});
