<?php
namespace App\Policies;

use App\Models\AssessmentRecord;
use App\Models\User;

class AssessmentRecordPolicy
{
    public function view(User $user, AssessmentRecord $assessment): bool
    {
        return $user->id === $assessment->request->worker_id || $user->isAdmin() || $user->isCommunity() || $user->isContributor();
    }
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }
}
