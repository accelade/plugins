<?php

declare(strict_types=1);

namespace Accelade\Plugins\Features;

/**
 * Generates route files for the plugin.
 */
class RoutesFeature extends AbstractFeature
{
    public function getName(): string
    {
        return 'routes';
    }

    public function shouldGenerate(array $config): bool
    {
        return ($config['generate_web_routes'] ?? false) || ($config['generate_api_routes'] ?? false);
    }

    public function getPriority(): int
    {
        return 30;
    }

    public function getDirectories(array $config): array
    {
        return ['routes'];
    }

    public function generate(array $config): void
    {
        if ($config['generate_web_routes'] ?? false) {
            $this->processor->generateFile(
                $config['base_path'].'/routes/web.php',
                'routes/web',
                [
                    'prefix' => $config['kebab_name'],
                    'namespace' => $config['namespace'],
                ]
            );
        }

        if ($config['generate_api_routes'] ?? false) {
            $this->processor->generateFile(
                $config['base_path'].'/routes/api.php',
                'routes/api',
                [
                    'prefix' => $config['kebab_name'],
                    'namespace' => $config['namespace'],
                ]
            );
        }
    }
}
