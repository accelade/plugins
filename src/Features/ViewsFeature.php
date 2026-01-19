<?php

declare(strict_types=1);

namespace Accelade\Plugins\Features;

/**
 * Generates Blade views structure for the plugin.
 */
class ViewsFeature extends AbstractFeature
{
    public function getName(): string
    {
        return 'views';
    }

    public function shouldGenerate(array $config): bool
    {
        return $config['generate_views'] ?? false;
    }

    public function getPriority(): int
    {
        return 35;
    }

    public function getDirectories(array $config): array
    {
        return ['resources/views', 'resources/views/components'];
    }

    public function generate(array $config): void
    {
        // Create a default welcome view
        $this->processor->generateFile(
            $config['base_path'].'/resources/views/welcome.blade.php',
            'view',
            [
                'name' => $config['studly_name'],
            ]
        );

        // Create an example component
        $this->processor->generateRawFile(
            $config['base_path'].'/resources/views/components/.gitkeep',
            ''
        );
    }
}
