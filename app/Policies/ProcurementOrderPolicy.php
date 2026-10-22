<?php
namespace App\Policies;

use App\Models\ProcurementOrder;
use App\Models\User;

class ProcurementOrderPolicy
{
    public function view(User $user, ProcurementOrder $order): bool
    {
        return $user->id === $order->vendor_id
            || $user->id === $order->request->worker_id
            || $user->isAdmin();
    }

    public function update(User $user, ProcurementOrder $order): bool
    {
        return $user->id === $order->vendor_id
            || $user->id === $order->request->worker_id
            || $user->isAdmin();
    }
}
