<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetRequest extends Model
{
    protected $fillable = [
        'worker_id','category_id','status','requested_amount_bdt','reason',
        'livelihood_justification','verification_id','assessment_id',
        'funding_pool_id','procurement_order_id',
    ];
    protected $casts = [
        'requested_amount_bdt' => 'decimal:2',
    ];
    public function worker() { return $this->belongsTo(User::class, 'worker_id'); }
    public function category() { return $this->belongsTo(AssetCategory::class, 'category_id'); }
    public function verification() { return $this->belongsTo(CommunityVerification::class, 'verification_id'); }
    public function assessment() { return $this->belongsTo(AssessmentRecord::class, 'assessment_id'); }
    public function fundingPool() { return $this->belongsTo(FundingPool::class, 'funding_pool_id'); }
    public function procurementOrder() { return $this->belongsTo(ProcurementOrder::class, 'procurement_order_id'); }
    public function documents() { return $this->hasMany(AssetRequestDocument::class, 'request_id'); }
}
