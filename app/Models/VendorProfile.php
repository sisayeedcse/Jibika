<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorProfile extends Model
{
    protected $fillable = ['user_id','company_name','supplier_type','verified'];
    protected $casts = ['verified'=>'boolean'];
    public function user() { return $this->belongsTo(User::class); }
}
