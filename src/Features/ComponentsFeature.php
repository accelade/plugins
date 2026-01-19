<?php

declare(strict_types=1);

namespace Accelade\Plugins\Features;

/**
 * Generates Blade component classes for the plugin.
 */
class ComponentsFeature extends AbstractFeature
{
    public function getName(): string
    {
        return 'components';
    }

    public function shouldGenerate(array $config): bool
    {
        return $config['generate_components'] ?? false;
    }

    public function getPriority(): int
    {
        return 40;
    }

    public function getDirectories(array $config): array
    {
        return ['src/Components'];
    }

    public function generate(array $config): void
    {
        // Create a .gitkeep file to ensure directory is tracked
        $this->processor->generateRawFile(
            $config['base_path'].'/src/Components/.gitkeep',
            ''
        );
    }
}
