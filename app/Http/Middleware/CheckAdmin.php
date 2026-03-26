<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponses;
use Closure;
use Illuminate\Http\Request;

class CheckAdmin
{
    use ApiResponses;

    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) return $this->error('Unauthenticated', 401);
        if (!auth()->user()->is_admin) return $this->error('Permission Denied', 403);
        return $next($request);
    }
}
