<?php

declare(strict_types=1);

namespace Accelade\Plugins\Contracts;

/**
 * Interface for plugin features that can be generated.
 *
 * Each feature (migrations, routes, assets, etc.) implements this interface
 * to determine if it should be generated and how to generate it.
 */
interface PluginFeatureInterface
{
    /**
     * Get the feature name/identifier.
     */
    public function getName(): string;

    /**
     * Determine if this feature should be generated based on config.
     *
     * @param  array<string, mixed>  $config
     */
    public function shouldGenerate(array $config): bool;

    /**
     * Generate the feature files.
     *
     * @param  array<string, mixed>  $config
     */
    public function generate(array $config): void;

    /**
     * Get the directories this feature needs.
     *
     * @param  array<string, mixed>  $config
     * @return array<string>
     */
    public function getDirectories(array $config): array;

    /**
     * Get the priority/order for generation (lower = earlier).
     *
     * Priority Guide:
     * - 0-20: Core files (composer, service provider, etc.)
     * - 21-40: Structure files (migrations, routes, etc.)
     * - 41-60: Asset files (CSS, JS, etc.)
     * - 61-80: Testing files
     * - 81-100: Documentation files
     */
    public function getPriority(): int;
}
