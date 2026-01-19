# Component Generators

The `accelade:make` command generates various components for your plugin.

## Usage

```bash
php artisan accelade:make {type} {name} --plugin={plugin-name}
```

## Available Component Types

### Models

```bash
php artisan accelade:make model Post --plugin=blog
```

Generates `src/Models/Post.php` with Eloquent model boilerplate.

### Controllers

```bash
php artisan accelade:make controller Post --plugin=blog
```

Generates `src/Http/Controllers/PostController.php` with CRUD methods.

### Migrations

```bash
php artisan accelade:make migration CreatePostsTable --plugin=blog
```

Generates timestamped migration file in `database/migrations/`.

### Commands

```bash
php artisan accelade:make command SyncPosts --plugin=blog
```

Generates `src/Commands/SyncPostsCommand.php`.

### Jobs

```bash
php artisan accelade:make job ProcessPost --plugin=blog
```

Generates `src/Jobs/ProcessPostJob.php` implementing `ShouldQueue`.

### Events & Listeners

```bash
php artisan accelade:make event PostCreated --plugin=blog
php artisan accelade:make listener SendPostNotification --plugin=blog
```

### Notifications

```bash
php artisan accelade:make notification PostPublished --plugin=blog
```

### Form Requests

```bash
php artisan accelade:make request StorePost --plugin=blog
```

### API Resources

```bash
php artisan accelade:make resource Post --plugin=blog
```

### Middleware

```bash
php artisan accelade:make middleware CheckPostAccess --plugin=blog
```

### Policies

```bash
php artisan accelade:make policy Post --plugin=blog
```

### Validation Rules

```bash
php artisan accelade:make rule ValidSlug --plugin=blog
```

### Blade Components

```bash
php artisan accelade:make component PostCard --plugin=blog
```

### Tests

```bash
php artisan accelade:make test PostCreation --plugin=blog
```

### Factories & Seeders

```bash
php artisan accelade:make factory Post --plugin=blog
php artisan accelade:make seeder Post --plugin=blog
```

### Views

```bash
php artisan accelade:make view posts/index --plugin=blog
```

## Custom Path

Override the default path:

```bash
php artisan accelade:make model Post --path=/custom/path/to/plugin
```
