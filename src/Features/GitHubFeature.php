<?php

declare(strict_types=1);

namespace Accelade\Plugins\Features;

/**
 * Generates GitHub-specific files for the plugin.
 */
class GitHubFeature extends AbstractFeature
{
    public function getName(): string
    {
        return 'github';
    }

    public function shouldGenerate(array $config): bool
    {
        return $config['generate_github_files'] ?? true;
    }

    public function getPriority(): int
    {
        return 90;
    }

    public function getDirectories(array $config): array
    {
        return [
            '.github',
            '.github/workflows',
            '.github/ISSUE_TEMPLATE',
        ];
    }

    public function generate(array $config): void
    {
        // Workflows
        $this->processor->generateFile(
            $config['base_path'].'/.github/workflows/tests.yml',
            'github/workflows/tests.yml',
            [
                'name' => $config['studly_name'],
            ]
        );

        $this->processor->generateFile(
            $config['base_path'].'/.github/workflows/fix-php-code-styling.yml',
            'github/workflows/fix-php-code-styling.yml',
            []
        );

        $this->processor->generateFile(
            $config['base_path'].'/.github/workflows/dependabot-auto-merge.yml',
            'github/workflows/dependabot-auto-merge.yml',
            []
        );

        // Issue templates
        $this->processor->generateFile(
            $config['base_path'].'/.github/ISSUE_TEMPLATE/bug.yml',
            'github/ISSUE_TEMPLATE/bug.yml',
            []
        );

        $this->processor->generateFile(
            $config['base_path'].'/.github/ISSUE_TEMPLATE/config.yml',
            'github/ISSUE_TEMPLATE/config.yml',
            []
        );

        // Dependabot
        $this->processor->generateFile(
            $config['base_path'].'/.github/dependabot.yml',
            'github/dependabot.yml',
            []
        );

        // Security
        $this->processor->generateFile(
            $config['base_path'].'/.github/SECURITY.md',
            'github/SECURITY.md',
            [
                'vendor' => $config['vendor_lower'],
                'package' => $config['kebab_name'],
            ]
        );

        // Contributing
        $this->processor->generateFile(
            $config['base_path'].'/.github/CONTRIBUTING.md',
            'github/CONTRIBUTING.md',
            [
                'name' => $config['studly_name'],
            ]
        );

        // Funding (if sponsor is configured)
        if ($config['generate_sponsor'] ?? false) {
            $this->processor->generateFile(
                $config['base_path'].'/.github/FUNDING.yml',
                'github/FUNDING.yml',
                [
                    'github_sponsor' => $config['github_sponsor'] ?? 'fadymondy',
                ]
            );
        }
    }
}
