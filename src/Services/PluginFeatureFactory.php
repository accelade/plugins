<?php

declare(strict_types=1);

namespace Accelade\Plugins\Services;

use Accelade\Plugins\Contracts\PluginFeatureInterface;
use InvalidArgumentException;

/**
 * Factory for managing and executing plugin features.
 *
 * This class follows the Factory Pattern to allow dynamic registration
 * and execution of plugin features without modifying core code.
 */
class PluginFeatureFactory
{
    /**
     * @var array<string, PluginFeatureInterface>
     */
    protected array $features = [];

    /**
     * Register a feature for generation.
     */
    public function register(PluginFeatureInterface $feature): void
    {
        $this->features[$feature->getName()] = $feature;
    }

    /**
     * Register multiple features at once.
     *
     * @param  array<PluginFeatureInterface>  $features
     */
    public function registerMany(array $features): void
    {
        foreach ($features as $feature) {
            $this->register($feature);
        }
    }

    /**
     * Get all registered features sorted by priority.
     *
     * @return array<PluginFeatureInterface>
     */
    public function getFeatures(): array
    {
        $features = $this->features;

        usort($features, fn ($a, $b) => $a->getPriority() <=> $b->getPriority());

        return $features;
    }

    /**
     * Get a feature by name.
     */
    public function getFeature(string $name): ?PluginFeatureInterface
    {
        return $this->features[$name] ?? null;
    }

    /**
     * Check if a feature is registered.
     */
    public function hasFeature(string $name): bool
    {
        return isset($this->features[$name]);
    }

    /**
     * Get directories needed by all features that should be generated.
     *
     * @param  array<string, mixed>  $config
     * @return array<string>
     */
    public function getDirectories(array $config): array
    {
        $directories = [];

        foreach ($this->getFeatures() as $feature) {
            if ($feature->shouldGenerate($config)) {
                $directories = array_merge($directories, $feature->getDirectories($config));
            }
        }

        return array_unique($directories);
    }

    /**
     * Generate all features that should be generated.
     *
     * @param  array<string, mixed>  $config
     */
    public function generateAll(array $config): void
    {
        foreach ($this->getFeatures() as $feature) {
            if ($feature->shouldGenerate($config)) {
                $feature->generate($config);
            }
        }
    }

    /**
     * Generate a specific feature by name.
     *
     * @param  array<string, mixed>  $config
     */
    public function generate(string $featureName, array $config): void
    {
        if (! isset($this->features[$featureName])) {
            throw new InvalidArgumentException("Feature '{$featureName}' not registered");
        }

        $feature = $this->features[$featureName];

        if ($feature->shouldGenerate($config)) {
            $feature->generate($config);
        }
    }

    /**
     * Get feature names.
     *
     * @return array<string>
     */
    public function getFeatureNames(): array
    {
        return array_keys($this->features);
    }

    /**
     * Get feature count.
     */
    public function count(): int
    {
        return count($this->features);
    }
}
