<?php
namespace App\Policies;

use App\Models\CommunityVerification;
use App\Models\User;

class CommunityVerificationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isCommunity() || $user->isAdmin();
    }
    public function update(User $user, CommunityVerification $verification): bool
    {
        return ($user->isCommunity() && $user->id === $verification->community_member_id) || $user->isAdmin();
    }
}
