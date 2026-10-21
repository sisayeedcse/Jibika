<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    protected $fillable = ['procurement_order_id','status','evidence_image_path','delivered_at','confirmed_by'];
    protected $casts = [
        'delivered_at'=>'datetime',
        'confirmed_by'=>'integer',
    ];
    public function procurementOrder() { return $this->belongsTo(ProcurementOrder::class); }
    public function confirmedBy() { return $this->belongsTo(User::class, 'confirmed_by'); }
}
