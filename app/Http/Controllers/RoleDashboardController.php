<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoleDashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        return match ($user->role) {
            'worker' => redirect()->route('worker.dashboard'),
            'community' => redirect()->route('community.dashboard'),
            'contributor' => redirect()->route('contributor.dashboard'),
            'vendor' => redirect()->route('vendor.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
            default => redirect('/'),
        };
    }
}
