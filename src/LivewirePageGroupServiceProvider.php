<?php

namespace Rahmanramsi\LivewirePageGroup;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Rahmanramsi\LivewirePageGroup\Commands\LivewirePageGroupCommand;

class LivewirePageGroupServiceProvider extends PackageServiceProvider
{
    /*
    * This class is a Package Service Provider
    *
    * More info: https://github.com/spatie/laravel-package-tools
    */
    public function configurePackage(Package $package): void
    {
        $package
            ->name('livewire-page-group')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_livewire-page-group_table')
            ->hasCommands($this->getCommands());
    }

    public function packageRegistered(): void
    {
        $this->app->scoped('livewire-page-group', function (): LivewirePageGroupManager {
            return new LivewirePageGroupManager();
        });
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [];
    }
}
