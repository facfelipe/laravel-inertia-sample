<?php

namespace App\Http\Controllers;

use App\Services\CurrentUserService;
use Illuminate\Http\Request;

class UserSwitchController extends Controller
{
    protected $currentUserService;

    public function __construct(CurrentUserService $currentUserService)
    {
        $this->currentUserService = $currentUserService;
    }

    /**
     * Switch to a specific user
     */
    public function switch(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = \App\Models\User::find($request->user_id);
        
        if ($user) {
            $this->currentUserService->setCurrentUser($user);
        }

        return redirect()->back()->with('success', "Switched to {$user->name} ({$user->role})");
    }

    /**
     * Get current user data for the frontend
     */
    public function current()
    {
        $currentUser = $this->currentUserService->getCurrentUser();
        $availableUsers = $this->currentUserService->getAvailableUsers();

        return response()->json([
            'current_user' => $currentUser,
            'available_users' => $availableUsers
        ]);
    }
}
