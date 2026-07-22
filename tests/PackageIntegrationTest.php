<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Rahmanramsi\LivewirePageGroup\Facades\LivewirePageGroup;
use Rahmanramsi\LivewirePageGroup\Http\Middleware\SetUpPageGroup;
use Rahmanramsi\LivewirePageGroup\LivewirePageGroupManager;
use Rahmanramsi\LivewirePageGroup\PageGroup;
use Rahmanramsi\LivewirePageGroup\Pages\HomePage;
use Rahmanramsi\LivewirePageGroup\Support\LivewireCompatibility;
use Rahmanramsi\LivewirePageGroup\Tests\Fixtures\DiscoveredPages\HiddenPage;
use Rahmanramsi\LivewirePageGroup\Tests\Fixtures\DiscoveredPages\ReportsPage;
use Rahmanramsi\LivewirePageGroup\Tests\Fixtures\Pages\ProfilePage;
use Rahmanramsi\LivewirePageGroup\Tests\Fixtures\ProfileRouteMiddleware;
use Rahmanramsi\LivewirePageGroup\Tests\Fixtures\RecordPageGroupRequest;
use Rahmanramsi\LivewirePageGroup\Tests\Fixtures\TestPageGroupServiceProvider;

it('boots the package service provider and exposes its manager', function () {
    expect($this->app->bound('livewire-page-group'))->toBeTrue()
        ->and($this->app->make('livewire-page-group'))->toBeInstanceOf(LivewirePageGroupManager::class)
        ->and(LivewirePageGroup::getFacadeRoot())->toBe($this->app->make('livewire-page-group'))
        ->and($this->app->make(Router::class)->getMiddleware()['pagegroup'])->toBe(SetUpPageGroup::class);
});

it('registers and finds a page group by id', function () {
    $pageGroup = LivewirePageGroup::getPageGroup('test');

    expect($pageGroup)->toBeInstanceOf(PageGroup::class)
        ->and($pageGroup->getId())->toBe('test')
        ->and(LivewirePageGroup::getPageGroups())->toHaveKey('test', $pageGroup)
        ->and(LivewirePageGroup::getPageGroup('missing'))->toBeNull();
});

it('registers home, custom page, discovered page, and custom routes', function () {
    $home = Route::getRoutes()->getByName('livewirePageGroup.test.pages.home');
    $profile = Route::getRoutes()->getByName('livewirePageGroup.test.pages.settings.profile');
    $reports = Route::getRoutes()->getByName('livewirePageGroup.test.pages.reports-page');
    $health = Route::getRoutes()->getByName('livewirePageGroup.test.health');

    expect($home)->not->toBeNull()
        ->and($home->uri())->toBe('portal')
        ->and($home->getDomain())->toBe('page-group.test')
        ->and($home->getActionName())->toContain(HomePage::class)
        ->and($home->gatherMiddleware())->toContain('pagegroup:test', RecordPageGroupRequest::class)
        ->and($profile)->not->toBeNull()
        ->and($profile->uri())->toBe('portal/settings/profile')
        ->and($profile->gatherMiddleware())->toContain(ProfileRouteMiddleware::class)
        ->and($reports)->not->toBeNull()
        ->and($reports->uri())->toBe('portal/reports-page')
        ->and($health)->not->toBeNull()
        ->and($health->uri())->toBe('portal/health');
});

it('discovers eligible pages from a directory and namespace', function () {
    $pageGroup = LivewirePageGroup::getPageGroup('test');

    expect($pageGroup->getPageDirectories())->toContain(__DIR__.'/Fixtures/DiscoveredPages')
        ->and($pageGroup->getPageNamespaces())->toContain('Rahmanramsi\\LivewirePageGroup\\Tests\\Fixtures\\DiscoveredPages')
        ->and($pageGroup->getPages())->toContain(ProfilePage::class, ReportsPage::class)
        ->and($pageGroup->getPages())->not->toContain(HiddenPage::class);
});

it('sets up and boots the current page group once per group', function () {
    $response = $this->get('http://page-group.test/portal/health');

    $response->assertOk()
        ->assertSee('test is healthy')
        ->assertHeader('X-Page-Group-Middleware', 'applied');

    expect(LivewirePageGroup::getCurrentPageGroup())->toBe(LivewirePageGroup::getPageGroup('test'))
        ->and(TestPageGroupServiceProvider::$bootCount)->toBe(1);

    LivewirePageGroup::bootCurrentPageGroup();

    expect(TestPageGroupServiceProvider::$bootCount)->toBe(1);
});

it('renders the home page with the configured layout and title', function () {
    $this->get('http://page-group.test/portal')
        ->assertOk()
        ->assertSee('Eiusmod et exercitation quis commodo aute non quis.')
        ->assertSee('Livewire Page Group test layout')
        ->assertSee('<title>Home Page</title>', false);
});

it('renders custom and discovered Livewire pages', function () {
    $this->get('http://page-group.test/portal/settings/profile')
        ->assertOk()
        ->assertSee('Profile page rendered.')
        ->assertSee('Livewire Page Group test layout')
        ->assertSee('<title>Profile Settings</title>', false)
        ->assertHeader('X-Profile-Middleware', 'applied');

    $this->get('http://page-group.test/portal/reports-page')
        ->assertOk()
        ->assertSee('Reports page rendered.')
        ->assertSee('<title>Reports Page</title>', false);
});

it('keeps component aliases stable and processes Livewire updates', function () {
    $compatibility = app(LivewireCompatibility::class);
    $componentName = $compatibility->componentName(ProfilePage::class);

    expect($componentName)
        ->toBe('rahmanramsi.livewire-page-group.tests.fixtures.pages.profile-page');

    $component = Livewire::test($componentName)
        ->assertSet('count', 0)
        ->assertSee('Count: 0')
        ->call('increment')
        ->assertSet('count', 1)
        ->assertSee('Count: 1');

    expect($component->instance()->getName())->toBe($componentName);
});

it('returns a clear not found response for an unknown middleware group', function () {
    Route::get('/missing-page-group', fn (): string => 'unreachable')
        ->middleware('pagegroup:missing');

    $this->get('/missing-page-group')->assertNotFound();
});

it('resets boot state when the current page group changes', function () {
    $manager = new LivewirePageGroupManager;
    $firstBoots = 0;
    $secondBoots = 0;
    $first = PageGroup::make()->id('first')->bootUsing(function () use (&$firstBoots): void {
        $firstBoots++;
    });
    $second = PageGroup::make()->id('second')->bootUsing(function () use (&$secondBoots): void {
        $secondBoots++;
    });

    $manager->bootCurrentPageGroup();
    $manager->setCurrentPageGroup($first);
    $manager->bootCurrentPageGroup();
    $manager->bootCurrentPageGroup();
    $manager->setCurrentPageGroup($second);
    $manager->bootCurrentPageGroup();

    expect($firstBoots)->toBe(1)
        ->and($secondBoots)->toBe(1);
});
