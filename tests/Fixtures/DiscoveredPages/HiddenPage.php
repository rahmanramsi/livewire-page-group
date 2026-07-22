<?php

namespace Rahmanramsi\LivewirePageGroup\Tests\Fixtures\DiscoveredPages;

use Rahmanramsi\LivewirePageGroup\Pages\Page;

class HiddenPage extends Page
{
    protected static bool $isDiscovered = false;

    protected static string $view = 'livewire-page-group-tests::pages.hidden';
}
