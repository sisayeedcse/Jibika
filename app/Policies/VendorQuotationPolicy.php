<?php
namespace App\Policies;

use App\Models\User;
use App\Models\VendorQuotation;

class VendorQuotationPolicy
{
    public function view(User $user, VendorQuotation $quotation): bool
    {
        return $user->id === $quotation->vendor_id || $user->isAdmin();
    }
    public function create(User $user): bool
    {
        return $user->isVendor();
    }
}
