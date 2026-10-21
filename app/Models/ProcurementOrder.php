<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcurementOrder extends Model
{
    protected $fillable = ['request_id','vendor_id','quotation_id','status','cost_bdt'];
    protected $casts = ['cost_bdt'=>'decimal:2'];
    public function request() { return $this->belongsTo(AssetRequest::class); }
    public function vendor() { return $this->belongsTo(User::class, 'vendor_id'); }
    public function quotation() { return $this->belongsTo(VendorQuotation::class); }
    public function delivery() { return $this->hasOne(Delivery::class, 'procurement_order_id'); }
}
