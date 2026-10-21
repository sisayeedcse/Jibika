<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitoringRecord extends Model
{
    protected $fillable = ['request_id','metric_name','metric_value','notes','recorded_at'];
    protected $casts = ['recorded_at'=>'datetime'];
    public function request() { return $this->belongsTo(AssetRequest::class); }
}
