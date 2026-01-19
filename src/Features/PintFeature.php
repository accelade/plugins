<?php

declare(strict_types=1);

namespace Accelade\Plugins\Features;

/**
 * Generates Laravel Pint configuration for the plugin.
 */
class PintFeature extends AbstractFeature
{
    public function getName(): string
    {
        return 'pint';
    }

    public function shouldGenerate(array $config): bool
    {
        return true; // Always generate pint config
    }

    public function getPriority(): int
    {
        return 70;
    }

    public function generate(array $config): void
    {
        $this->processor->generateFile(
            $config['base_path'].'/pint.json',
            'pint.json',
            []
        );
    }
}
