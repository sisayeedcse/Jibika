<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Models\WorkerProfile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $user->load(['workerProfile', 'communityProfile', 'vendorProfile']);
        return view('pages.profile.show', compact('user'));
    }

    public function edit(Request $request)
    {
        $user = $request->user();
        $user->load(['workerProfile', 'communityProfile', 'vendorProfile']);
        return view('pages.profile.edit', compact('user'));
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = $request->user();
        $validated = $request->validated();

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
