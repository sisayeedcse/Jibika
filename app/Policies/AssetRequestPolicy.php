<?php
namespace App\Policies;

use App\Models\User;
use App\Models\AssetRequest;

class AssetRequestPolicy
{
    public function view(User $user, AssetRequest $request): bool
    {
        return $user->id === $request->worker_id || $user->role === 'admin';
    }

    public function create(User $user): bool
    {
        return $user->role === 'worker';
    }

    public function update(User $user, AssetRequest $request): bool
    {
        return $user->id === $request->worker_id && in_array($request->status, ['draft','needs_information','submitted']);
    }
}
