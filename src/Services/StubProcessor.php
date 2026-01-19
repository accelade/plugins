<?php

declare(strict_types=1);

namespace Accelade\Plugins\Services;

use Illuminate\Filesystem\Filesystem;
use RuntimeException;

/**
 * Processes stub files by replacing placeholders with actual values.
 */
class StubProcessor
{
    public function __construct(protected Filesystem $files) {}

    /**
     * Get stub path from stub name.
     */
    protected function getStubPath(string $name): string
    {
        return __DIR__.'/../Stubs/'.$name.'.stub';
    }

    /**
     * Get processed stub content with replacements.
     *
     * @param  string  $stubName  Name of the stub file (without .stub extension)
     * @param  array<string, string>  $replacements  Key-value pairs for placeholder replacement
     * @return string Processed stub content
     *
     * @throws RuntimeException if stub file not found
     */
    public function process(string $stubName, array $replacements): string
    {
        $stubPath = $this->getStubPath($stubName);

        if (! $this->files->exists($stubPath)) {
            throw new RuntimeException("Stub file not found: {$stubPath}");
        }

        $stub = $this->files->get($stubPath);

        foreach ($replacements as $key => $value) {
            $stub = str_replace("{{ {$key} }}", $value, $stub);
        }

        return $stub;
    }

    /**
     * Generate file from stub with replacements.
     *
     * @param  string  $path  Full path where file should be created
     * @param  string  $stubName  Name of the stub file
     * @param  array<string, string>  $replacements  Placeholder replacements
     */
    public function generateFile(string $path, string $stubName, array $replacements): void
    {
        $content = $this->process($stubName, $replacements);
        $this->files->ensureDirectoryExists(dirname($path));
        $this->files->put($path, $content);
    }

    /**
     * Generate file from raw content.
     *
     * @param  string  $path  Full path where file should be created
     * @param  string  $content  File content
     */
    public function generateRawFile(string $path, string $content): void
    {
        $this->files->ensureDirectoryExists(dirname($path));
        $this->files->put($path, $content);
    }

    /**
     * Check if a stub exists.
     */
    public function stubExists(string $stubName): bool
    {
        return $this->files->exists($this->getStubPath($stubName));
    }

    /**
     * Get all available stub names.
     *
     * @return array<string>
     */
    public function getAvailableStubs(): array
    {
        $stubsPath = dirname($this->getStubPath(''));
        $stubs = [];

        foreach ($this->files->allFiles($stubsPath) as $file) {
            if ($file->getExtension() === 'stub') {
                $stubs[] = str_replace('.stub', '', $file->getRelativePathname());
            }
        }

        return $stubs;
    }
}
