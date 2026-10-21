<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function show() { return view('auth.login'); }
    public function authenticate(Request $r) {
        if (Auth::attempt($r->only('email','password'), $r->boolean('remember'))) {
            $r->session()->regenerate();
            return redirect()->intended('/dashboard');
        }
        return back()->withErrors(['email' => 'Invalid credentials.']);
    }
    public function logout(Request $r) {
        Auth::logout();
        $r->session()->invalidate();
        return redirect('/');
    }
}
