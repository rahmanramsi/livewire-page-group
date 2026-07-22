<?php

namespace Rahmanramsi\LivewirePageGroup\Tests\Fixtures;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RecordPageGroupRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $response->headers->set('X-Page-Group-Middleware', 'applied');

        return $response;
    }
}
