<?php

namespace Rahmanramsi\LivewirePageGroup\Facades;

use Illuminate\Support\Facades\Facade;
use Rahmanramsi\LivewirePageGroup\LivewirePageGroupManager;

/**
 * @see LivewirePageGroupManager
 */
class LivewirePageGroup extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'livewire-page-group';
    }
}
