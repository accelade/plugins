<?php

declare(strict_types=1);

namespace Accelade\Plugins\Features;

/**
 * Generates Mago linter configuration for the plugin.
 */
class MagoFeature extends AbstractFeature
{
    public function getName(): string
    {
        return 'mago';
    }

    public function shouldGenerate(array $config): bool
    {
        return true; // Always generate mago config
    }

    public function getPriority(): int
    {
        return 71; // Right after pint
    }

    public function generate(array $config): void
    {
        $this->processor->generateFile(
            $config['base_path'].'/mago.toml',
            'mago.toml',
            []
        );
    }
}
