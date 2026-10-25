<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DemoLoginController extends Controller
{
    public const DEMO_USERS = [
        'worker'      => 'worker@demo.local',
        'community'   => 'community@demo.local',
        'contributor' => 'contributor@demo.local',
        'vendor'      => 'vendor@demo.local',
        'admin'       => 'admin@demo.local',
    ];

    public function login(Request $request, string $role)
    {
        abort_unless(config('app.demo_mode'), 404);
        abort_unless(isset(self::DEMO_USERS[$role]), 404);

        $user = User::where('email', self::DEMO_USERS[$role])
            ->where('status', 'active')
            ->firstOrFail();

        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/dashboard');
    }
}
