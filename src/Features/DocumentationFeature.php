<?php

declare(strict_types=1);

namespace Accelade\Plugins\Features;

/**
 * Generates documentation structure for the plugin.
 */
class DocumentationFeature extends AbstractFeature
{
    public function getName(): string
    {
        return 'documentation';
    }

    public function shouldGenerate(array $config): bool
    {
        return $config['generate_docs'] ?? true;
    }

    public function getPriority(): int
    {
        return 95;
    }

    public function getDirectories(array $config): array
    {
        return ['docs'];
    }

    public function generate(array $config): void
    {
        // Generate getting-started.md
        $this->processor->generateFile(
            $config['base_path'].'/docs/getting-started.md',
            'docs/getting-started.md',
            [
                'name' => $config['studly_name'],
                'vendor' => $config['vendor_lower'],
                'package' => $config['kebab_name'],
                'description' => $config['plugin_description'] ?? "{$config['studly_name']} plugin for Accelade",
            ]
        );

        // Generate CLAUDE.md
        $this->processor->generateFile(
            $config['base_path'].'/CLAUDE.md',
            'CLAUDE.md',
            [
                'name' => $config['studly_name'],
                'vendor' => $config['vendor_lower'],
                'package' => $config['kebab_name'],
                'namespace' => $config['namespace'],
            ]
        );
    }
}
