<?php

declare(strict_types=1);

namespace Accelade\Plugins\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class MakeComponentCommand extends Command
{
    protected $signature = 'accelade:make
                            {type? : The type of component to generate}
                            {name? : The name of the component}
                            {--plugin= : The plugin to generate the component in}
                            {--path= : Custom path for the component}
                            {--no-interaction : Run without user interaction}';

    protected $description = 'Generate a component for an Accelade plugin';

    public function __construct(protected Filesystem $files)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $type = $this->getComponentType();
        $name = $this->getComponentName();
        $pluginPath = $this->getPluginPath();

        if (! $type || ! $name || ! $pluginPath) {
            $this->error('Type, name, and plugin path are required.');

            return self::FAILURE;
        }

        $componentTypes = config('accelade-plugins.component_types', []);

        if (! isset($componentTypes[$type])) {
            $this->error("Invalid component type: {$type}");
            $this->line('Available types: '.implode(', ', array_keys($componentTypes)));

            return self::FAILURE;
        }

        $componentConfig = $componentTypes[$type];
        $targetPath = $pluginPath.'/'.$componentConfig['path'];

        $this->generateComponent($type, $name, $targetPath, $pluginPath);

        $this->info("Component {$type}:{$name} created successfully!");

        return self::SUCCESS;
    }

    protected function getComponentType(): ?string
    {
        if ($this->argument('type')) {
            return $this->argument('type');
        }

        if ($this->option('no-interaction')) {
            return null;
        }

        $componentTypes = config('accelade-plugins.component_types', []);
        $options = [];

        foreach ($componentTypes as $key => $config) {
            $options[$key] = "{$key} - {$config['description']}";
        }

        return select(
            label: 'What type of component do you want to create?',
            options: $options,
            required: true
        );
    }

    protected function getComponentName(): ?string
    {
        if ($this->argument('name')) {
            return $this->argument('name');
        }

        if ($this->option('no-interaction')) {
            return null;
        }

        return text(
            label: 'Component name',
            placeholder: 'E.g., UserController, PostModel',
            required: true
        );
    }

    protected function getPluginPath(): ?string
    {
        if ($this->option('path')) {
            return $this->option('path');
        }

        if ($this->option('plugin')) {
            $plugin = $this->option('plugin');

            // Check packages directory
            $packagesPath = base_path('packages');
            $dirs = $this->files->directories($packagesPath);

            foreach ($dirs as $vendorDir) {
                $pluginDir = $vendorDir.'/'.Str::kebab($plugin);
                if ($this->files->exists($pluginDir)) {
                    return $pluginDir;
                }

                // Check subdirectories
                $subDirs = $this->files->directories($vendorDir);
                foreach ($subDirs as $subDir) {
                    if (basename($subDir) === Str::kebab($plugin)) {
                        return $subDir;
                    }
                }
            }
        }

        if ($this->option('no-interaction')) {
            return null;
        }

        return text(
            label: 'Plugin path',
            placeholder: 'E.g., packages/accelade/my-plugin',
            required: true
        );
    }

    protected function generateComponent(string $type, string $name, string $targetPath, string $pluginPath): void
    {
        $this->files->ensureDirectoryExists($targetPath);

        $studlyName = Str::studly($name);
        $namespace = $this->getNamespaceFromPluginPath($pluginPath);

        switch ($type) {
            case 'model':
                $this->generateModel($studlyName, $targetPath, $namespace);
                break;
            case 'controller':
                $this->generateController($studlyName, $targetPath, $namespace);
                break;
            case 'command':
                $this->generateCommand($studlyName, $targetPath, $namespace);
                break;
            case 'migration':
                $this->generateMigration($name, $targetPath);
                break;
            case 'test':
                $this->generateTest($studlyName, $targetPath, $namespace);
                break;
            case 'view':
                $this->generateView($name, $targetPath);
                break;
            case 'component':
                $this->generateBladeComponent($studlyName, $targetPath, $namespace);
                break;
            case 'middleware':
                $this->generateMiddleware($studlyName, $targetPath, $namespace);
                break;
            case 'request':
                $this->generateRequest($studlyName, $targetPath, $namespace);
                break;
            case 'resource':
                $this->generateResource($studlyName, $targetPath, $namespace);
                break;
            case 'job':
                $this->generateJob($studlyName, $targetPath, $namespace);
                break;
            case 'event':
                $this->generateEvent($studlyName, $targetPath, $namespace);
                break;
            case 'listener':
                $this->generateListener($studlyName, $targetPath, $namespace);
                break;
            case 'notification':
                $this->generateNotification($studlyName, $targetPath, $namespace);
                break;
            case 'policy':
                $this->generatePolicy($studlyName, $targetPath, $namespace);
                break;
            case 'rule':
                $this->generateRule($studlyName, $targetPath, $namespace);
                break;
            case 'factory':
                $this->generateFactory($studlyName, $targetPath, $namespace);
                break;
            case 'seeder':
                $this->generateSeeder($studlyName, $targetPath, $namespace);
                break;
            default:
                $this->warn("No generator implemented for type: {$type}");
        }
    }

    protected function getNamespaceFromPluginPath(string $pluginPath): string
    {
        $composerPath = $pluginPath.'/composer.json';

        if ($this->files->exists($composerPath)) {
            $composer = json_decode($this->files->get($composerPath), true);
            $autoload = $composer['autoload']['psr-4'] ?? [];

            if (! empty($autoload)) {
                return rtrim(array_keys($autoload)[0], '\\');
            }
        }

        // Fallback to guessing from path
        $parts = explode('/', $pluginPath);
        $vendor = Str::studly($parts[count($parts) - 2] ?? 'Accelade');
        $package = Str::studly($parts[count($parts) - 1] ?? 'Plugin');

        return "{$vendor}\\{$package}";
    }

    protected function generateModel(string $name, string $path, string $namespace): void
    {
        $content = <<<PHP
<?php

declare(strict_types=1);

namespace {$namespace}\\Models;

use Illuminate\Database\Eloquent\Model;

class {$name} extends Model
{
    protected \$fillable = [
        //
    ];

    protected function casts(): array
    {
        return [
            //
        ];
    }
}
PHP;

        $this->files->put("{$path}/{$name}.php", $content);
    }

    protected function generateController(string $name, string $path, string $namespace): void
    {
        $content = <<<PHP
<?php

declare(strict_types=1);

namespace {$namespace}\\Http\\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class {$name}Controller extends Controller
{
    public function index()
    {
        //
    }

    public function store(Request \$request)
    {
        //
    }

    public function show(string \$id)
    {
        //
    }

    public function update(Request \$request, string \$id)
    {
        //
    }

    public function destroy(string \$id)
    {
        //
    }
}
PHP;

        $this->files->put("{$path}/{$name}Controller.php", $content);
    }

    protected function generateCommand(string $name, string $path, string $namespace): void
    {
        $kebabName = Str::kebab($name);
        $content = <<<PHP
<?php

declare(strict_types=1);

namespace {$namespace}\\Commands;

use Illuminate\Console\Command;

class {$name}Command extends Command
{
    protected \$signature = '{$kebabName}';

    protected \$description = 'Command description';

    public function handle(): int
    {
        \$this->info('Command executed successfully!');

        return self::SUCCESS;
    }
}
PHP;

        $this->files->put("{$path}/{$name}Command.php", $content);
    }

    protected function generateMigration(string $name, string $path): void
    {
        $tableName = Str::snake(Str::plural($name));
        $timestamp = date('Y_m_d_His');
        $className = 'Create'.Str::studly($tableName).'Table';

        $content = <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('{$tableName}', function (Blueprint \$table) {
            \$table->id();
            \$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('{$tableName}');
    }
};
PHP;

        $this->files->put("{$path}/{$timestamp}_create_{$tableName}_table.php", $content);
    }

    protected function generateTest(string $name, string $path, string $namespace): void
    {
        $content = <<<PHP
<?php

declare(strict_types=1);

it('can test {$name}', function () {
    expect(true)->toBeTrue();
});
PHP;

        $this->files->put("{$path}/{$name}Test.php", $content);
    }

    protected function generateView(string $name, string $path): void
    {
        $content = <<<BLADE
<div>
    {{-- {$name} view --}}
</div>
BLADE;

        $this->files->put("{$path}/{$name}.blade.php", $content);
    }

    protected function generateBladeComponent(string $name, string $path, string $namespace): void
    {
        $content = <<<PHP
<?php

declare(strict_types=1);

namespace {$namespace}\\Components;

use Illuminate\View\Component;

class {$name} extends Component
{
    public function __construct()
    {
        //
    }

    public function render()
    {
        return view('components.{$name}');
    }
}
PHP;

        $this->files->put("{$path}/{$name}.php", $content);
    }

    protected function generateMiddleware(string $name, string $path, string $namespace): void
    {
        $content = <<<PHP
<?php

declare(strict_types=1);

namespace {$namespace}\\Http\\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class {$name}
{
    public function handle(Request \$request, Closure \$next): Response
    {
        return \$next(\$request);
    }
}
PHP;

        $this->files->put("{$path}/{$name}.php", $content);
    }

    protected function generateRequest(string $name, string $path, string $namespace): void
    {
        $content = <<<PHP
<?php

declare(strict_types=1);

namespace {$namespace}\\Http\\Requests;

use Illuminate\Foundation\Http\FormRequest;

class {$name}Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            //
        ];
    }
}
PHP;

        $this->files->put("{$path}/{$name}Request.php", $content);
    }

    protected function generateResource(string $name, string $path, string $namespace): void
    {
        $content = <<<PHP
<?php

declare(strict_types=1);

namespace {$namespace}\\Http\\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class {$name}Resource extends JsonResource
{
    public function toArray(Request \$request): array
    {
        return parent::toArray(\$request);
    }
}
PHP;

        $this->files->put("{$path}/{$name}Resource.php", $content);
    }

    protected function generateJob(string $name, string $path, string $namespace): void
    {
        $content = <<<PHP
<?php

declare(strict_types=1);

namespace {$namespace}\\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class {$name}Job implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle(): void
    {
        //
    }
}
PHP;

        $this->files->put("{$path}/{$name}Job.php", $content);
    }

    protected function generateEvent(string $name, string $path, string $namespace): void
    {
        $content = <<<PHP
<?php

declare(strict_types=1);

namespace {$namespace}\\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class {$name}
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct()
    {
        //
    }
}
PHP;

        $this->files->put("{$path}/{$name}.php", $content);
    }

    protected function generateListener(string $name, string $path, string $namespace): void
    {
        $content = <<<PHP
<?php

declare(strict_types=1);

namespace {$namespace}\\Listeners;

class {$name}Listener
{
    public function __construct()
    {
        //
    }

    public function handle(object \$event): void
    {
        //
    }
}
PHP;

        $this->files->put("{$path}/{$name}Listener.php", $content);
    }

    protected function generateNotification(string $name, string $path, string $namespace): void
    {
        $content = <<<PHP
<?php

declare(strict_types=1);

namespace {$namespace}\\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class {$name}Notification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function via(object \$notifiable): array
    {
        return ['mail'];
    }
}
PHP;

        $this->files->put("{$path}/{$name}Notification.php", $content);
    }

    protected function generatePolicy(string $name, string $path, string $namespace): void
    {
        $content = <<<PHP
<?php

declare(strict_types=1);

namespace {$namespace}\\Policies;

use App\\Models\\User;

class {$name}Policy
{
    public function viewAny(User \$user): bool
    {
        return true;
    }

    public function view(User \$user, mixed \$model): bool
    {
        return true;
    }

    public function create(User \$user): bool
    {
        return true;
    }

    public function update(User \$user, mixed \$model): bool
    {
        return true;
    }

    public function delete(User \$user, mixed \$model): bool
    {
        return true;
    }
}
PHP;

        $this->files->put("{$path}/{$name}Policy.php", $content);
    }

    protected function generateRule(string $name, string $path, string $namespace): void
    {
        $content = <<<PHP
<?php

declare(strict_types=1);

namespace {$namespace}\\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class {$name} implements ValidationRule
{
    public function validate(string \$attribute, mixed \$value, Closure \$fail): void
    {
        //
    }
}
PHP;

        $this->files->put("{$path}/{$name}.php", $content);
    }

    protected function generateFactory(string $name, string $path, string $namespace): void
    {
        $content = <<<PHP
<?php

declare(strict_types=1);

namespace {$namespace}\\Database\\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class {$name}Factory extends Factory
{
    public function definition(): array
    {
        return [
            //
        ];
    }
}
PHP;

        $this->files->put("{$path}/{$name}Factory.php", $content);
    }

    protected function generateSeeder(string $name, string $path, string $namespace): void
    {
        $content = <<<PHP
<?php

declare(strict_types=1);

namespace {$namespace}\\Database\\Seeders;

use Illuminate\Database\Seeder;

class {$name}Seeder extends Seeder
{
    public function run(): void
    {
        //
    }
}
PHP;

        $this->files->put("{$path}/{$name}Seeder.php", $content);
    }
}
