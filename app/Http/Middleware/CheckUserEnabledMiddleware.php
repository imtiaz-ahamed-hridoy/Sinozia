<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class CheckUserEnabledMiddleware
{
    use ApiResponse;

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Use Gate to check if user is enabled
            if (! Gate::allows('is_active', $user)) {
                $request->user()->currentAccessToken()?->delete();

                return $this->error([], 'Your account is disabled.', 403);
            }
        }

        return $next($request);
    }
}
