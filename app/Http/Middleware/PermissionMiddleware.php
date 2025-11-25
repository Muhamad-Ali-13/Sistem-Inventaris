<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, $permission)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user has permission
        if (!$this->checkPermission($user, $permission)) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }

    /**
     * Check if user has permission through roles
     */
    private function checkPermission($user, $permission)
    {
        // If user has super admin role, allow all
        if ($user->hasRole('super admin')) {
            return true;
        }

        // Check if user has the specific permission
        try {
            // Try using hasPermissionTo method
            if (method_exists($user, 'hasPermissionTo') && $user->hasPermissionTo($permission)) {
                return true;
            }
        } catch (\Exception $e) {
            // Fallback to manual check
        }

        // Manual permission check through roles
        foreach ($user->roles as $role) {
            foreach ($role->permissions as $rolePermission) {
                if ($rolePermission->name === $permission) {
                    return true;
                }
            }
        }

        return false;
    }
}