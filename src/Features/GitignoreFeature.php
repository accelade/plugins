<?php

declare(strict_types=1);

namespace Accelade\Plugins\Features;

/**
 * Generates the .gitignore file for the plugin.
 */
class GitignoreFeature extends AbstractFeature
{
    public function getName(): string
    {
        return 'gitignore';
    }

    public function shouldGenerate(array $config): bool
    {
        return true; // Always generate .gitignore
    }

    public function getPriority(): int
    {
        return 2;
    }

    public function generate(array $config): void
    {
        $this->processor->generateFile(
            $config['base_path'].'/.gitignore',
            '.gitignore',
            []
        );
    }
}
