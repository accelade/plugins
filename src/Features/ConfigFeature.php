<?php

declare(strict_types=1);

namespace Accelade\Plugins\Features;

/**
 * Generates the config file for the plugin.
 */
class ConfigFeature extends AbstractFeature
{
    public function getName(): string
    {
        return 'config';
    }

    public function shouldGenerate(array $config): bool
    {
        return true; // Always generate config
    }

    public function getPriority(): int
    {
        return 20;
    }

    public function getDirectories(array $config): array
    {
        return ['config'];
    }

    public function generate(array $config): void
    {
        $this->processor->generateFile(
            $config['base_path'].'/config/'.$config['config_name'].'.php',
            'config',
            [
                'env_prefix' => $config['env_prefix'],
            ]
        );
    }
}
