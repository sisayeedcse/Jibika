<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorQuotation extends Model
{
    protected $fillable = ['procurement_order_id','vendor_id','quoted_amount_bdt','notes','status'];
    protected $casts = ['quoted_amount_bdt'=>'decimal:2'];
    public function procurementOrder() { return $this->belongsTo(ProcurementOrder::class); }
    public function vendor() { return $this->belongsTo(User::class); }
}
