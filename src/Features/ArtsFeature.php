<?php

declare(strict_types=1);

namespace Accelade\Plugins\Features;

/**
 * Generates arts folder with placeholder images.
 */
class ArtsFeature extends AbstractFeature
{
    public function getName(): string
    {
        return 'arts';
    }

    public function shouldGenerate(array $config): bool
    {
        return $config['generate_arts'] ?? true;
    }

    public function getPriority(): int
    {
        return 55;
    }

    public function getDirectories(array $config): array
    {
        return ['arts'];
    }

    public function generate(array $config): void
    {
        // Create a .gitkeep file
        $this->processor->generateRawFile(
            $config['base_path'].'/arts/.gitkeep',
            ''
        );
    }
}
