<?php

namespace App\Http\Middleware;

use App\Models\Membership;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class ExpireEndedMemberships
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Schema::hasTable('memberships')) {
            Membership::expireEndedMemberships();
        }

        return $next($request);
    }
}
