<?php

declare(strict_types=1);

namespace Accelade\Plugins\Commands;

use Accelade\Plugins\Services\PluginGenerator;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\text;

class MakePluginCommand extends Command
{
    protected $signature = 'accelade:plugin
                            {name? : The name of the plugin}
                            {--vendor= : The vendor name}
                            {--path= : The base path where the plugin will be created}
                            {--no-plugin : Skip generating plugin class}';

    protected $description = 'Create a new Accelade plugin package';

    public function __construct(
        protected Filesystem $files,
        protected PluginGenerator $generator
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        // Get plugin name
        $name = $this->getPluginName();
        if (! $name) {
            $this->error('Plugin name is required.');

            return self::FAILURE;
        }

        // Get vendor name
        $vendor = $this->getVendorName();

        // Prepare configuration
        $basePath = $this->option('path') ?? base_path('packages/'.Str::lower($vendor).'/'.Str::kebab($name));

        // Check if directory exists
        if ($this->files->exists($basePath)) {
            $this->error("Plugin directory already exists: {$basePath}");

            return self::FAILURE;
        }

        // Confirm creation
        if (! $this->confirmCreation($name, $basePath)) {
            $this->info('Plugin creation cancelled.');

            return self::SUCCESS;
        }

        $this->info("Creating plugin: {$name}...");

        // Gather configuration options
        $options = $this->gatherConfigurationOptions();

        // Generate the plugin using the service
        $config = $this->generator->prepareConfig(
            $name,
            $vendor,
            $basePath,
            $options['generate_plugin'],
            $options
        );

        $this->generator->createDirectoryStructure($basePath, $config);
        $this->generator->generateAllFiles($config);

        // Display success message
        $this->displaySuccessMessage($basePath);

        // Post-generation tasks
        if (! ! $this->input->isInteractive()) {
            $this->handlePostGeneration($basePath, $config);
        }

        return self::SUCCESS;
    }

    protected function getPluginName(): ?string
    {
        if ($this->argument('name')) {
            return $this->argument('name');
        }

        if (! $this->input->isInteractive()) {
            return null;
        }

        return text(
            label: 'Plugin name',
            placeholder: 'E.g., BlogExtensions',
            required: true
        );
    }

    protected function getVendorName(): string
    {
        if ($this->option('vendor')) {
            return $this->option('vendor');
        }

        if (! $this->input->isInteractive()) {
            return config('accelade-plugins.defaults.vendor', 'accelade');
        }

        return text(
            label: 'Vendor name',
            default: config('accelade-plugins.defaults.vendor', 'accelade'),
            required: true
        );
    }

    /**
     * @return array<string, mixed>
     */
    protected function gatherConfigurationOptions(): array
    {
        if (! $this->input->isInteractive()) {
            return $this->getDefaultOptions();
        }

        $options = [];

        // Get plugin name for auto-filling
        $pluginName = $this->argument('name');
        $humanReadableName = trim(ucwords(str_replace(['-', '_'], ' ', Str::kebab($pluginName))));
        $defaultTitle = $humanReadableName;
        $defaultDescription = trim($humanReadableName.' plugin for Accelade');

        $this->newLine();

        // Ask about features using multiselect
        $selectedFeatures = multiselect(
            label: 'Select the features you want to include:',
            options: [
                'plugin' => 'Accelade plugin class',
                'migrations' => 'Database migrations',
                'views' => 'Blade views',
                'web_routes' => 'Web routes',
                'api_routes' => 'API routes',
                'css' => 'CSS assets (Tailwind v4)',
                'js' => 'JavaScript assets (TypeScript + Vite)',
                'arts' => 'Arts folder',
                'languages' => 'Language files (i18n)',
                'components' => 'Blade component classes',
                'github' => 'GitHub workflows and issue templates',
                'docs' => 'Documentation structure',
                'custom_composer' => 'Custom composer details (author, email, license)',
                'git_init' => 'Initialize Git repository',
                'composer_install' => 'Run composer install after generation',
            ],
            default: ['plugin', 'github', 'docs', 'arts'],
            hint: 'Use space to select/deselect, enter to confirm'
        );

        // Parse selected features
        $options['generate_plugin'] = ! $this->option('no-plugin') && in_array('plugin', $selectedFeatures, true);
        $options['generate_migrations'] = in_array('migrations', $selectedFeatures, true);
        $options['generate_views'] = in_array('views', $selectedFeatures, true);
        $options['generate_web_routes'] = in_array('web_routes', $selectedFeatures, true);
        $options['generate_api_routes'] = in_array('api_routes', $selectedFeatures, true);
        $options['generate_css'] = in_array('css', $selectedFeatures, true);
        $options['generate_js'] = in_array('js', $selectedFeatures, true);
        $options['generate_arts'] = in_array('arts', $selectedFeatures, true);
        $options['generate_components'] = in_array('components', $selectedFeatures, true);
        $options['generate_github_files'] = in_array('github', $selectedFeatures, true);
        $options['generate_docs'] = in_array('docs', $selectedFeatures, true);
        $options['git_init'] = in_array('git_init', $selectedFeatures, true);
        $options['composer_install'] = in_array('composer_install', $selectedFeatures, true);

        $this->newLine();

        // Ask for plugin details with auto-filled defaults
        $options['plugin_title'] = $defaultTitle;
        $options['plugin_description'] = $defaultDescription;

        // Ask about custom composer details (only if selected)
        if (in_array('custom_composer', $selectedFeatures, true)) {
            $options['plugin_description'] = text(
                label: 'Plugin description',
                default: $defaultDescription,
                placeholder: 'e.g., Manage users with advanced features',
                hint: 'Press Enter to use default or type your custom description'
            );

            $options['author'] = text(
                label: 'Author name',
                default: config('accelade-plugins.defaults.author', 'Fady Mondy')
            );

            $options['author_email'] = text(
                label: 'Author email',
                default: config('accelade-plugins.defaults.email', 'info@3x1.io'),
                required: true
            );

            $options['license'] = text(
                label: 'License',
                default: config('accelade-plugins.defaults.license', 'MIT')
            );
        } else {
            $options['author'] = config('accelade-plugins.defaults.author', 'Fady Mondy');
            $options['author_email'] = config('accelade-plugins.defaults.email', 'info@3x1.io');
            $options['license'] = config('accelade-plugins.defaults.license', 'MIT');
        }

        // Ask about Languages (only if selected)
        if (in_array('languages', $selectedFeatures, true)) {
            $languagesInput = text(
                label: 'Which languages do you need? (comma-separated)',
                default: 'en',
                hint: 'e.g., en,ar,fr'
            );
            $options['languages'] = array_map('trim', explode(',', $languagesInput));
        } else {
            $options['languages'] = ['en'];
        }

        // Ask about Sponsor (only if GitHub files are selected)
        if ($options['generate_github_files']) {
            $options['generate_sponsor'] = confirm(
                label: 'Do you have a GitHub Sponsor?',
                default: false
            );

            if ($options['generate_sponsor']) {
                $options['github_sponsor'] = text(
                    label: 'GitHub Sponsor username',
                    required: true
                );
            }
        }

        return $options;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getDefaultOptions(): array
    {
        $pluginName = $this->argument('name');
        $humanReadableName = trim(ucwords(str_replace(['-', '_'], ' ', Str::kebab($pluginName))));

        return [
            'plugin_title' => $humanReadableName,
            'plugin_description' => trim($humanReadableName.' plugin for Accelade'),
            'author' => config('accelade-plugins.defaults.author', 'Fady Mondy'),
            'author_email' => config('accelade-plugins.defaults.email', 'info@3x1.io'),
            'license' => config('accelade-plugins.defaults.license', 'MIT'),
            'generate_plugin' => ! $this->option('no-plugin'),
            'generate_migrations' => false,
            'generate_views' => false,
            'generate_web_routes' => false,
            'generate_api_routes' => false,
            'generate_css' => false,
            'generate_js' => false,
            'generate_arts' => true,
            'generate_components' => false,
            'languages' => ['en'],
            'generate_github_files' => true,
            'generate_docs' => true,
            'generate_sponsor' => true,
            'github_sponsor' => config('accelade-plugins.defaults.github_sponsor', 'fadymondy'),
            'git_init' => false,
            'composer_install' => false,
        ];
    }

    protected function confirmCreation(string $name, string $basePath): bool
    {
        if (! $this->input->isInteractive()) {
            return true;
        }

        return confirm(
            label: "Create plugin '".Str::studly($name)."' in {$basePath}?",
            default: true
        );
    }

    protected function displaySuccessMessage(string $basePath): void
    {
        $this->newLine();
        $this->info('Plugin created successfully!');
        $this->newLine();

        $this->info('Next steps:');
        $this->line('  1. Require the plugin in your composer.json');
        $this->line('  2. Run: composer install');
        $this->line('  3. Run: php artisan [plugin-name]:install');
        $this->newLine();

        $this->info('Plugin location: '.$basePath);
        $this->newLine();
    }

    /**
     * @param  array<string, mixed>  $config
     */
    protected function handlePostGeneration(string $basePath, array $config): void
    {
        $this->newLine();

        // Initialize Git repository (if selected in features)
        if ($config['git_init'] ?? false) {
            $this->initializeGitRepository($basePath);
        }

        // Register in main app's composer.json
        if (confirm(label: 'Register plugin in main app composer.json?', default: true)) {
            $this->registerInComposer($basePath, $config);
        }

        // Run composer install in plugin directory (if selected in features)
        if ($config['composer_install'] ?? false) {
            $this->info('Running composer install...');
            exec("cd {$basePath} && composer install", $output, $returnCode);

            if ($returnCode === 0) {
                $this->info('Composer install completed successfully!');
            } else {
                $this->warn('Composer install failed. Please run it manually.');
            }
        }
    }

    protected function initializeGitRepository(string $basePath): void
    {
        $this->info('Initializing Git repository...');

        exec("cd {$basePath} && git init", $output, $returnCode);

        if ($returnCode !== 0) {
            $this->warn('Failed to initialize Git repository.');

            return;
        }

        $this->info('Git repository initialized!');

        if (confirm(label: 'Create initial commit?', default: true)) {
            exec("cd {$basePath} && git add .", $output, $returnCode);
            exec("cd {$basePath} && git commit -m 'Initial commit: Plugin scaffolding'", $output, $returnCode);

            if ($returnCode === 0) {
                $this->info('Initial commit created!');
            } else {
                $this->warn('Failed to create initial commit.');
            }
        }
    }

    /**
     * @param  array<string, mixed>  $config
     */
    protected function registerInComposer(string $pluginPath, array $config): void
    {
        $appComposerPath = base_path('composer.json');

        if (! file_exists($appComposerPath)) {
            $this->warn('Could not find composer.json in main app');

            return;
        }

        $composerJson = json_decode(file_get_contents($appComposerPath), true);

        // Add to repositories
        $relativePath = str_replace(base_path().'/', '', $pluginPath);
        $composerJson['repositories'][] = [
            'type' => 'path',
            'url' => $relativePath,
        ];

        // Add to require
        $packageName = $config['vendor_lower'].'/'.$config['kebab_name'];
        $composerJson['require'][$packageName] = '@dev';

        file_put_contents(
            $appComposerPath,
            json_encode($composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        $this->info("Plugin registered in composer.json as: {$packageName}");
        $this->comment('Run: composer update to install the plugin');
    }
}
