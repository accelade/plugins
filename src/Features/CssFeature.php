<?php

declare(strict_types=1);

namespace Accelade\Plugins\Features;

/**
 * Generates CSS assets for the plugin.
 */
class CssFeature extends AbstractFeature
{
    public function getName(): string
    {
        return 'css';
    }

    public function shouldGenerate(array $config): bool
    {
        return $config['generate_css'] ?? false;
    }

    public function getPriority(): int
    {
        return 45;
    }

    public function getDirectories(array $config): array
    {
        return ['resources/css', 'dist'];
    }

    public function generate(array $config): void
    {
        $this->processor->generateFile(
            $config['base_path'].'/resources/css/app.css',
            'css/app',
            [
                'name' => $config['studly_name'],
            ]
        );
    }
}
