<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContributionOrPledge extends Model
{
    protected $fillable = ['pool_id','contributor_id','amount_bdt','is_confirmed_transaction','is_demo','pledged_at'];
    protected $casts = [
        'amount_bdt'=>'decimal:2',
        'is_confirmed_transaction'=>'boolean',
        'is_demo'=>'boolean',
        'pledged_at'=>'datetime',
    ];
    public function pool() { return $this->belongsTo(FundingPool::class); }
    public function contributor() { return $this->belongsTo(User::class); }
}
