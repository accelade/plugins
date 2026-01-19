<?php

declare(strict_types=1);

namespace Accelade\Plugins\Features;

/**
 * Generates the Plugin class that integrates with Accelade.
 */
class PluginClassFeature extends AbstractFeature
{
    public function getName(): string
    {
        return 'plugin-class';
    }

    public function shouldGenerate(array $config): bool
    {
        return $config['generate_plugin'] ?? true;
    }

    public function getPriority(): int
    {
        return 10;
    }

    public function getDirectories(array $config): array
    {
        return ['src'];
    }

    public function generate(array $config): void
    {
        $this->processor->generateFile(
            $config['base_path'].'/src/'.$config['studly_name'].'Plugin.php',
            'plugin',
            [
                'namespace' => $config['namespace'],
                'class' => $config['studly_name'].'Plugin',
                'id' => $config['kebab_name'],
                'name' => $config['plugin_title'] ?? $config['studly_name'],
                'description' => $config['plugin_description'] ?? "{$config['studly_name']} plugin for Accelade",
                'author' => $config['author'] ?? 'Fady Mondy',
            ]
        );
    }
}
