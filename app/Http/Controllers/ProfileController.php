<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WorkerProfile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $user->load(['workerProfile','communityProfile','vendorProfile']);
        return view('pages.profile.show', compact('user'));
    }

    public function edit(Request $request)
    {
        $user = $request->user();
        $user->load(['workerProfile','communityProfile','vendorProfile']);
        return view('pages.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'name'             => ['required','string','max:255'],
            'occupation'       => ['nullable','string','max:255'],
            'experience_years' => ['nullable','integer','min:0','max:60'],
            'location'         => ['nullable','string','max:255'],
            'bio'              => ['nullable','string','max:2000'],
        ]);

        $user->update(['name' => $validated['name']]);

        if ($user->workerProfile) {
            $user->workerProfile->update([
                'occupation'       => $validated['occupation'] ?? '',
                'experience_years' => $validated['experience_years'] ?? 0,
                'location'         => $validated['location'] ?? '',
                'bio'              => $validated['bio'] ?? '',
            ]);
        } elseif ($user->isWorker()) {
            WorkerProfile::create([
                'user_id'          => $user->id,
                'occupation'       => $validated['occupation'] ?? '',
                'experience_years' => $validated['experience_years'] ?? 0,
                'location'         => $validated['location'] ?? '',
                'bio'              => $validated['bio'] ?? '',
                'verified'         => false,
            ]);
        }

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully.');
    }
}
