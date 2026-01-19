<?php

declare(strict_types=1);

namespace Accelade\Plugins\Features;

/**
 * Generates JavaScript assets for the plugin.
 */
class JsFeature extends AbstractFeature
{
    public function getName(): string
    {
        return 'js';
    }

    public function shouldGenerate(array $config): bool
    {
        return $config['generate_js'] ?? false;
    }

    public function getPriority(): int
    {
        return 50;
    }

    public function getDirectories(array $config): array
    {
        return ['resources/js', 'dist'];
    }

    public function generate(array $config): void
    {
        // Generate main JS file
        $this->processor->generateFile(
            $config['base_path'].'/resources/js/app.ts',
            'js/app',
            [
                'name' => $config['studly_name'],
                'kebab_name' => $config['kebab_name'],
            ]
        );

        // Generate package.json
        $this->processor->generateFile(
            $config['base_path'].'/package.json',
            'package.json',
            [
                'vendor' => $config['vendor_lower'],
                'package' => $config['kebab_name'],
                'description' => $config['plugin_description'] ?? "{$config['studly_name']} plugin for Accelade",
            ]
        );

        // Generate vite.config.ts
        $this->processor->generateFile(
            $config['base_path'].'/vite.config.ts',
            'vite.config',
            [
                'name' => $config['studly_name'],
                'kebab_name' => $config['kebab_name'],
            ]
        );

        // Generate tsconfig.json
        $this->processor->generateFile(
            $config['base_path'].'/tsconfig.json',
            'tsconfig.json',
            []
        );
    }
}
