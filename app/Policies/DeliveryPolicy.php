<?php
namespace App\Policies;

use App\Models\Delivery;
use App\Models\User;

class DeliveryPolicy
{
    public function view(User $user, Delivery $delivery): bool
    {
        return $user->id === $delivery->procurementOrder->vendor_id
            || $user->id === $delivery->procurementOrder->request->worker_id
            || $user->isAdmin();
    }
    public function update(User $user, Delivery $delivery): bool
    {
        return $user->id === $delivery->procurementOrder->vendor_id || $user->isAdmin();
    }
}
