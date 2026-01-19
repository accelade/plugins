<?php

declare(strict_types=1);

namespace Accelade\Plugins\Features;

/**
 * Generates README.md for the plugin.
 */
class ReadmeFeature extends AbstractFeature
{
    public function getName(): string
    {
        return 'readme';
    }

    public function shouldGenerate(array $config): bool
    {
        return true; // Always generate README
    }

    public function getPriority(): int
    {
        return 85;
    }

    public function generate(array $config): void
    {
        $this->processor->generateFile(
            $config['base_path'].'/README.md',
            'README.md',
            [
                'name' => $config['studly_name'],
                'vendor' => $config['vendor_lower'],
                'package' => $config['kebab_name'],
                'description' => $config['plugin_description'] ?? "{$config['studly_name']} plugin for Accelade",
                'author' => $config['author'] ?? 'Fady Mondy',
            ]
        );

        // Generate CHANGELOG.md
        $this->processor->generateFile(
            $config['base_path'].'/CHANGELOG.md',
            'CHANGELOG.md',
            []
        );

        // Generate LICENSE.md
        $this->processor->generateFile(
            $config['base_path'].'/LICENSE.md',
            'LICENSE.md',
            [
                'year' => date('Y'),
                'author' => $config['author'] ?? 'Fady Mondy',
            ]
        );
    }
}
