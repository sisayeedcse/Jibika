<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FundingPool extends Model
{
    protected $fillable = ['request_id','target_amount_bdt','current_amount_bdt','status','is_demo','is_simulated_mode'];
    protected $casts = [
        'target_amount_bdt'=>'decimal:2',
        'current_amount_bdt'=>'decimal:2',
        'is_demo'=>'boolean',
        'is_simulated_mode'=>'boolean',
    ];
    public function request() { return $this->belongsTo(AssetRequest::class); }
    public function contributions() { return $this->hasMany(ContributionOrPledge::class, 'pool_id'); }
}
