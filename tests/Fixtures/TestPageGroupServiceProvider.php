<?php

namespace Rahmanramsi\LivewirePageGroup\Tests\Fixtures;

use Illuminate\Support\Facades\Route;
use Rahmanramsi\LivewirePageGroup\PageGroup;
use Rahmanramsi\LivewirePageGroup\PageGroupServiceProvider;
use Rahmanramsi\LivewirePageGroup\Tests\Fixtures\Pages\ProfilePage;

class TestPageGroupServiceProvider extends PageGroupServiceProvider
{
    public static int $bootCount = 0;

    public function pageGroup(PageGroup $pageGroup): PageGroup
    {
        return $pageGroup
            ->id('test')
            ->path('portal')
            ->domain('page-group.test')
            ->layout('livewire-page-group-tests::layouts.app')
            ->middleware([RecordPageGroupRequest::class])
            ->pages([ProfilePage::class])
            ->discoverPages(__DIR__.'/DiscoveredPages', __NAMESPACE__.'\\DiscoveredPages')
            ->routes(function (PageGroup $pageGroup): void {
                Route::get('/health', fn (): string => "{$pageGroup->getId()} is healthy")
                    ->name('health');
            })
            ->bootUsing(function (): void {
                static::$bootCount++;
            });
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/views', 'livewire-page-group-tests');
    }
}
