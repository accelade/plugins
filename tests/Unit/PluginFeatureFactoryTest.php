<?php

declare(strict_types=1);

use Accelade\Plugins\Contracts\PluginFeatureInterface;
use Accelade\Plugins\Services\PluginFeatureFactory;

it('can register features', function () {
    $factory = new PluginFeatureFactory;
    $feature = mock(PluginFeatureInterface::class);
    $feature->shouldReceive('getName')->andReturn('test-feature');

    $factory->register($feature);

    expect($factory->hasFeature('test-feature'))->toBeTrue();
});

it('returns features sorted by priority', function () {
    $factory = new PluginFeatureFactory;

    $feature1 = mock(PluginFeatureInterface::class);
    $feature1->shouldReceive('getName')->andReturn('feature1');
    $feature1->shouldReceive('getPriority')->andReturn(50);

    $feature2 = mock(PluginFeatureInterface::class);
    $feature2->shouldReceive('getName')->andReturn('feature2');
    $feature2->shouldReceive('getPriority')->andReturn(10);

    $factory->register($feature1);
    $factory->register($feature2);

    $features = $factory->getFeatures();

    expect($features[0]->getName())->toBe('feature2');
    expect($features[1]->getName())->toBe('feature1');
});
