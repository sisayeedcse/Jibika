<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WorkerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function show() { return view('auth.register'); }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required','string','max:255'],
            'email'    => ['required','string','email','max:255','unique:users'],
            'password' => ['required','string','min:8','confirmed'],
            'role'     => ['sometimes','string','in:worker,contributor,vendor'],
        ]);

        // Only worker, contributor, vendor allowed on public registration.
        // admin and community are granted by administrators only.
        $role = in_array($validated['role'] ?? 'worker', ['worker','contributor','vendor'])
            ? ($validated['role'] ?? 'worker')
            : 'worker';

        // Use direct attribute assignment (role is not in $fillable to prevent mass-assignment)
        $user = new User();
        $user->name     = $validated['name'];
        $user->email    = $validated['email'];
        $user->password = Hash::make($validated['password']);
        $user->role     = $role;
        $user->status   = 'active';
        $user->save();

        if ($role === 'worker') {
            WorkerProfile::create([
                'user_id'          => $user->id,
                'occupation'       => '',
                'experience_years' => 0,
                'location'         => '',
                'bio'              => '',
                'verified'         => false,
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Account created successfully.');
    }
}
