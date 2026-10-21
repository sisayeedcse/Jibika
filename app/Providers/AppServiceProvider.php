<?php
namespace App\Providers;

use App\Models\AssetRequest;
use App\Models\AssessmentRecord;
use App\Models\CommunityVerification;
use App\Models\Delivery;
use App\Models\FundingPool;
use App\Models\Notification;
use App\Models\ProcurementOrder;
use App\Models\User;
use App\Models\VendorQuotation;
use App\Policies\AssetRequestPolicy;
use App\Policies\AssessmentRecordPolicy;
use App\Policies\CommunityVerificationPolicy;
use App\Policies\DeliveryPolicy;
use App\Policies\FundingPoolPolicy;
use App\Policies\NotificationPolicy;
use App\Policies\ProcurementOrderPolicy;
use App\Policies\UserPolicy;
use App\Policies\VendorQuotationPolicy;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void { }

    public function boot(): void
    {
        $policies = [
            User::class => UserPolicy::class,
            AssetRequest::class => AssetRequestPolicy::class,
            CommunityVerification::class => CommunityVerificationPolicy::class,
            AssessmentRecord::class => AssessmentRecordPolicy::class,
            FundingPool::class => FundingPoolPolicy::class,
            VendorQuotation::class => VendorQuotationPolicy::class,
            Delivery::class => DeliveryPolicy::class,
            ProcurementOrder::class => ProcurementOrderPolicy::class,
            Notification::class => NotificationPolicy::class,
        ];
        foreach ($policies as $model => $policy) {
            $this->app->make(\Illuminate\Contracts\Auth\Access\Gate::class)->policy($model, $policy);
        }
    }
}
