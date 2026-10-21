<?php
namespace App\Policies;

use App\Models\FundingPool;
use App\Models\User;

class FundingPoolPolicy
{
    public function view(User $user, FundingPool $pool): bool
    {
        return $user->id === $pool->request->worker_id || $user->isAdmin() || $user->isContributor();
    }
    public function contribute(User $user, FundingPool $pool): bool
    {
        return $user->isContributor() && $pool->status === 'open';
    }
}
