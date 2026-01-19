<?php

declare(strict_types=1);

namespace Accelade\Plugins\Features;

/**
 * Generates the install command for the plugin.
 */
class InstallCommandFeature extends AbstractFeature
{
    public function getName(): string
    {
        return 'install-command';
    }

    public function shouldGenerate(array $config): bool
    {
        return true; // Always generate install command
    }

    public function getPriority(): int
    {
        return 15;
    }

    public function getDirectories(array $config): array
    {
        return ['src/Commands'];
    }

    public function generate(array $config): void
    {
        $this->processor->generateFile(
            $config['base_path'].'/src/Commands/Install'.$config['studly_name'].'Command.php',
            'install-command',
            [
                'namespace' => $config['namespace'],
                'class' => 'Install'.$config['studly_name'].'Command',
                'name' => $config['studly_name'],
                'kebab_name' => $config['kebab_name'],
                'config' => $config['config_name'],
            ]
        );
    }
}
