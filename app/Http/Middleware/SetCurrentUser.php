<?php

namespace App\Http\Middleware;

use App\Services\CurrentUserService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetCurrentUser
{
    protected $currentUserService;

    public function __construct(CurrentUserService $currentUserService)
    {
        $this->currentUserService = $currentUserService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the current user from our service
        $currentUser = $this->currentUserService->getCurrentUser();
        
        if ($currentUser) {
            // Set the user for authorization purposes
            Auth::setUser($currentUser);
        }

        return $next($request);
    }
}
