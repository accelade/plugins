<?php

declare(strict_types=1);

use Accelade\Plugins\Services\StubProcessor;
use Illuminate\Filesystem\Filesystem;

it('can get available stubs', function () {
    $processor = new StubProcessor(new Filesystem);
    $stubs = $processor->getAvailableStubs();

    expect($stubs)->toBeArray();
    expect($stubs)->toContain('composer.json');
    expect($stubs)->toContain('service-provider');
    expect($stubs)->toContain('plugin');
});

it('throws exception for non-existent stub', function () {
    $processor = new StubProcessor(new Filesystem);

    expect(fn () => $processor->process('non-existent-stub', []))
        ->toThrow(RuntimeException::class);
});
