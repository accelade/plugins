<?php

declare(strict_types=1);

namespace Accelade\Plugins\Features;

/**
 * Generates testing structure for the plugin.
 */
class TestingFeature extends AbstractFeature
{
    public function getName(): string
    {
        return 'testing';
    }

    public function shouldGenerate(array $config): bool
    {
        return true; // Always generate testing structure
    }

    public function getPriority(): int
    {
        return 65;
    }

    public function getDirectories(array $config): array
    {
        return ['tests', 'tests/Feature', 'tests/Unit'];
    }

    public function generate(array $config): void
    {
        // Generate TestCase
        $this->processor->generateFile(
            $config['base_path'].'/tests/TestCase.php',
            'TestCase',
            [
                'namespace' => $config['namespace'],
                'service_provider' => $config['service_provider'],
            ]
        );

        // Generate Pest.php
        $this->processor->generateFile(
            $config['base_path'].'/tests/Pest.php',
            'Pest.php',
            [
                'namespace' => $config['namespace'],
            ]
        );

        // Generate example test
        $this->processor->generateFile(
            $config['base_path'].'/tests/Feature/ExampleTest.php',
            'ExampleTest',
            [
                'namespace' => $config['namespace'],
            ]
        );

        // Generate phpunit.xml
        $this->processor->generateFile(
            $config['base_path'].'/phpunit.xml',
            'phpunit.xml',
            [
                'name' => $config['studly_name'],
            ]
        );
    }
}
