<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WorkerProfile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(User $user)
    {
        $this->authorize('view', $user);
        $user->load(['workerProfile','communityProfile','vendorProfile']);
        return view('pages.profile.show', compact('user'));
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);
        $user->load('workerProfile');
        return view('pages.profile.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);
        $validated = $request->validate([
            'name' => ['required','string','max:255'],
            'occupation' => ['nullable','string','max:255'],
            'experience_years' => ['nullable','integer','min:0','max:60'],
            'location' => ['nullable','string','max:255'],
            'bio' => ['nullable','string','max:2000'],
        ]);
        $user->update(['name' => $validated['name']]);
        if ($user->workerProfile) {
            $user->workerProfile->update([
                'occupation' => $validated['occupation'] ?? '',
                'experience_years' => $validated['experience_years'] ?? 0,
                'location' => $validated['location'] ?? '',
                'bio' => $validated['bio'] ?? '',
            ]);
        } else {
            WorkerProfile::create(array_merge(['user_id' => $user->id], array_filter([
                'occupation' => $validated['occupation'] ?? null,
                'experience_years' => $validated['experience_years'] ?? null,
                'location' => $validated['location'] ?? null,
                'bio' => $validated['bio'] ?? null,
            ])));
        }
        return redirect()->route('profile.show', $user)->with('success', 'Profile updated.');
    }
}
