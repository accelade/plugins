<?php

declare(strict_types=1);

namespace Accelade\Plugins\Features;

/**
 * Generates database migrations structure for the plugin.
 */
class MigrationsFeature extends AbstractFeature
{
    public function getName(): string
    {
        return 'migrations';
    }

    public function shouldGenerate(array $config): bool
    {
        return $config['generate_migrations'] ?? false;
    }

    public function getPriority(): int
    {
        return 25;
    }

    public function getDirectories(array $config): array
    {
        return ['database/migrations', 'database/factories', 'database/seeders'];
    }

    public function generate(array $config): void
    {
        // Create a .gitkeep file to ensure directory is tracked
        $this->processor->generateRawFile(
            $config['base_path'].'/database/migrations/.gitkeep',
            ''
        );

        $this->processor->generateRawFile(
            $config['base_path'].'/database/factories/.gitkeep',
            ''
        );

        $this->processor->generateRawFile(
            $config['base_path'].'/database/seeders/.gitkeep',
            ''
        );
    }
}
