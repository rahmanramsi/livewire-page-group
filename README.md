# Livewire Page Group

[![Latest Version on Packagist](https://img.shields.io/packagist/v/rahmanramsi/livewire-page-group.svg?style=flat-square)](https://packagist.org/packages/rahmanramsi/livewire-page-group)
[![Total Downloads](https://img.shields.io/packagist/dt/rahmanramsi/livewire-page-group.svg?style=flat-square)](https://packagist.org/packages/rahmanramsi/livewire-page-group)

This package groups class-based, full-page Livewire components behind a shared domain, URL prefix, layout, middleware stack, and boot callback.

## Requirements

- PHP `^8.1`
- Laravel 10, 11, 12, or 13
- Livewire `^3.8` or `^4.1`

Livewire is installed as a runtime dependency. Composer selects a compatible release from `^3.8 || ^4.1`, so applications constrained to Livewire 3 can stay on that line while applications such as Filament 5 projects can resolve Livewire 4.

## Installation

Install the package with Composer:

```bash
composer require rahmanramsi/livewire-page-group
```

## Getting Started

Create a service provider for each page group and register it with your application providers:

```php
<?php

namespace App\Providers;

use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Users\ListUsers;
use Illuminate\Support\Facades\Route;
use Rahmanramsi\LivewirePageGroup\PageGroup;
use Rahmanramsi\LivewirePageGroup\PageGroupServiceProvider;

class AdminPageGroupServiceProvider extends PageGroupServiceProvider
{
    public function pageGroup(PageGroup $pageGroup): PageGroup
    {
        return $pageGroup
            ->id('admin')
            ->path('admin')
            ->domain(config('app.admin_domain'))
            ->homePage(Dashboard::class)
            ->layout('layouts.admin')
            ->middleware(['web', 'auth'])
            ->pages([
                ListUsers::class,
            ])
            ->discoverPages(
                app_path('Livewire/Admin'),
                'App\\Livewire\\Admin',
            )
            ->routes(function (PageGroup $pageGroup): void {
                Route::get('/health', fn (): string => "{$pageGroup->getId()} is healthy")
                    ->name('health');
            })
            ->bootUsing(function (PageGroup $pageGroup): void {
                // Run once when this group is selected for the request.
            });
    }
}
```

The example creates these route name prefixes:

```text
livewirePageGroup.admin.pages.*
livewirePageGroup.admin.*
```

Use `domain(null)` or omit `domain()` when the group should respond on every host. The `path()` value is the URL prefix. Group middleware applies to the home page, discovered pages, explicit pages, and custom routes. Pass `true` as the second argument to `middleware()` when those middleware should also be persistent across Livewire requests.

The configured layout must accept the Blade `$slot`. It also receives the mounted component as `$livewire`, which allows a layout to render the page title:

```blade
<title>{{ $livewire->getTitle() }}</title>
{{ $slot }}
```

## Pages

Pages extend `Rahmanramsi\LivewirePageGroup\Pages\Page`:

```php
<?php

namespace App\Livewire\Admin\Users;

use Rahmanramsi\LivewirePageGroup\Pages\Page;

class ListUsers extends Page
{
    protected static ?string $title = 'Users';

    protected static ?string $slug = 'users';

    protected static string $view = 'livewire.admin.users.list-users';
}
```

Explicit pages are registered with `pages([...])`. `discoverPages($directory, $namespace)` discovers non-abstract `Page` subclasses from an autoloadable namespace. Set `protected static bool $isDiscovered = false` on a page to exclude it from discovery.

The package uses `Route::get($uri, ComponentClass::class)` for its class-based full-page components. This form works on both supported Livewire lines. Livewire-specific component registration is isolated behind a compatibility layer so component aliases remain stable across Livewire 3 and 4.

Single-file and multi-file Livewire 4 components are not discovered as page-group pages. Page-group pages remain class-based subclasses of `Rahmanramsi\LivewirePageGroup\Pages\Page`.

## Page Generator

Generate a class and its Blade view for a registered group:

```bash
php artisan make:livewire-page Users/ListUsers --group=admin
```

The command uses the group's first discovered directory and namespace. If more than one is configured, it prompts for the destination. Existing class or view files are not overwritten unless `--force` is supplied.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

-   [Rahman Ramsi](https://github.com/rahmanramsi)
-   [Filament](https://github.com/filamentphp) for inspiration
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
