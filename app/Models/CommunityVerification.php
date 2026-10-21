<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityVerification extends Model
{
    protected $fillable = ['request_id','community_member_id','status','evidence_notes','decision_notes','verified_at'];
    protected $casts = ['verified_at'=>'datetime'];
    public function request() { return $this->belongsTo(AssetRequest::class); }
    public function communityMember() { return $this->belongsTo(User::class, 'community_member_id'); }
}
