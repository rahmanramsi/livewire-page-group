<?php

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Rahmanramsi\LivewirePageGroup\Facades\LivewirePageGroup;
use Rahmanramsi\LivewirePageGroup\PageGroup;

function livewirePageCommandTestDirectory(): string
{
    return sys_get_temp_dir().'/livewire-page-group-command-tests';
}

function registerCommandPageGroup(): array
{
    $root = livewirePageCommandTestDirectory();
    $classDirectory = "{$root}/app/PageGroup/Pages";
    $resourceDirectory = resource_path();

    LivewirePageGroup::registerPageGroup(
        PageGroup::make()
            ->id('command')
            ->discoverPages($classDirectory, 'App\\PageGroup\\Pages'),
    );

    return [$classDirectory, $resourceDirectory];
}

beforeEach(function () {
    app(Filesystem::class)->deleteDirectory(livewirePageCommandTestDirectory());
    app(Filesystem::class)->deleteDirectory(resource_path('views/page-group'));
});

afterEach(function () {
    app(Filesystem::class)->deleteDirectory(livewirePageCommandTestDirectory());
    app(Filesystem::class)->deleteDirectory(resource_path('views/page-group'));
});

it('creates a page class and Blade view from the package stubs', function () {
    [$classDirectory, $resourceDirectory] = registerCommandPageGroup();

    $this->artisan('make:livewire-page', [
        'name' => 'Nested/Profile',
        '--group' => 'command',
    ])->assertExitCode(Command::SUCCESS);

    $class = app(Filesystem::class)->get("{$classDirectory}/Nested/Profile.php");
    $view = app(Filesystem::class)->get("{$resourceDirectory}/views/page-group/pages/nested/profile.blade.php");

    expect($class)->toContain('namespace App\\PageGroup\\Pages\\Nested;')
        ->toContain('class Profile extends Page')
        ->toContain("protected static string \$view = 'page-group.pages.nested.profile';")
        ->and($view)->toContain('Nothing in the world is as soft and yielding as water.');
});

it('refuses to overwrite an existing page class', function () {
    [$classDirectory, $resourceDirectory] = registerCommandPageGroup();
    $filesystem = app(Filesystem::class);
    $classPath = "{$classDirectory}/Existing.php";
    $filesystem->ensureDirectoryExists(dirname($classPath));
    $filesystem->put($classPath, 'existing class');

    $this->artisan('make:livewire-page', [
        'name' => 'Existing',
        '--group' => 'command',
    ])->expectsOutputToContain("{$classPath} already exists")
        ->assertExitCode(Command::INVALID);

    expect($filesystem->get($classPath))->toBe('existing class')
        ->and($filesystem->exists("{$resourceDirectory}/views/page-group/pages/existing.blade.php"))->toBeFalse();
});

it('refuses to overwrite an existing Blade view', function () {
    [$classDirectory, $resourceDirectory] = registerCommandPageGroup();
    $filesystem = app(Filesystem::class);
    $viewPath = "{$resourceDirectory}/views/page-group/pages/existing-view.blade.php";
    $filesystem->ensureDirectoryExists(dirname($viewPath));
    $filesystem->put($viewPath, 'existing view');

    $exitCode = Artisan::call('make:livewire-page', [
        'name' => 'ExistingView',
        '--group' => 'command',
    ]);

    expect($exitCode)->toBe(Command::INVALID)
        ->and(Artisan::output())->toContain('resources/views/page-group/pages/existing-view.blade.php already exists')
        ->and($filesystem->get($viewPath))->toBe('existing view')
        ->and($filesystem->exists("{$classDirectory}/ExistingView.php"))->toBeFalse();
});

it('fails clearly when the requested page group does not exist', function () {
    registerCommandPageGroup();

    $this->artisan('make:livewire-page', [
        'name' => 'MissingGroupPage',
        '--group' => 'missing',
    ])->expectsOutputToContain('Page group [missing] not found.')
        ->assertExitCode(Command::FAILURE);
});
