<?php

declare(strict_types=1);

namespace Accelade\Plugins\Features;

/**
 * Generates language files for the plugin.
 */
class LanguageFeature extends AbstractFeature
{
    public function getName(): string
    {
        return 'language';
    }

    public function shouldGenerate(array $config): bool
    {
        return ! empty($config['languages'] ?? []);
    }

    public function getPriority(): int
    {
        return 38;
    }

    public function getDirectories(array $config): array
    {
        $dirs = ['resources/lang'];

        foreach ($config['languages'] ?? ['en'] as $lang) {
            $dirs[] = "resources/lang/{$lang}";
        }

        return $dirs;
    }

    public function generate(array $config): void
    {
        $languages = $config['languages'] ?? ['en'];

        foreach ($languages as $lang) {
            $this->processor->generateFile(
                $config['base_path']."/resources/lang/{$lang}/messages.php",
                'messages.php',
                [
                    'name' => $config['studly_name'],
                ]
            );
        }
    }
}
