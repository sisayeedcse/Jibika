<?php
namespace App\Policies;

use App\Models\AssetRequest;
use App\Models\User;

class AssetRequestPolicy
{
    public function view(User $user, AssetRequest $request): bool
    {
        return $user->id === $request->worker_id || $user->isAdmin() || $user->isCommunity();
    }
    public function create(User $user): bool
    {
        return $user->isWorker();
    }
    public function update(User $user, AssetRequest $request): bool
    {
        return $user->id === $request->worker_id && $request->status === 'draft';
    }
    public function delete(User $user, AssetRequest $request): bool
    {
        return $user->id === $request->worker_id && $request->status === 'draft';
    }

    public function assess(User $user, AssetRequest $request): bool
    {
        return $user->isAdmin() || ($user->id === $request->worker_id && $request->status === 'verified');
    }
}
