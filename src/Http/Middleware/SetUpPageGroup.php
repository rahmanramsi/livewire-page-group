<?php

namespace Rahmanramsi\LivewirePageGroup\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Rahmanramsi\LivewirePageGroup\Facades\LivewirePageGroup;

class SetUpPageGroup
{
    public function handle(Request $request, Closure $next, string $pageGroup): mixed
    {
        $pageGroupId = $pageGroup;
        $pageGroup = LivewirePageGroup::getPageGroup($pageGroupId);

        if (! $pageGroup) {
            abort(404, "Page group [{$pageGroupId}] not found.");
        }

        LivewirePageGroup::setCurrentPageGroup($pageGroup);

        LivewirePageGroup::bootCurrentPageGroup();

        return $next($request);
    }
}
