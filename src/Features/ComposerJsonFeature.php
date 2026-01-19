<?php

declare(strict_types=1);

namespace Accelade\Plugins\Features;

/**
 * Generates the composer.json file for the plugin.
 */
class ComposerJsonFeature extends AbstractFeature
{
    public function getName(): string
    {
        return 'composer-json';
    }

    public function shouldGenerate(array $config): bool
    {
        return true; // Always generate composer.json
    }

    public function getPriority(): int
    {
        return 1; // First file to generate
    }

    public function generate(array $config): void
    {
        $this->processor->generateFile(
            $config['base_path'].'/composer.json',
            'composer.json',
            [
                'vendor' => $config['vendor_lower'],
                'package' => $config['kebab_name'],
                'description' => $config['plugin_description'] ?? "{$config['studly_name']} plugin for Accelade",
                'license' => $config['license'] ?? 'MIT',
                'author' => $config['author'] ?? 'Fady Mondy',
                'email' => $config['author_email'] ?? 'info@3x1.io',
                'namespace' => $config['namespace_escaped'],
                'service_provider' => $config['service_provider'],
            ]
        );
    }
}
